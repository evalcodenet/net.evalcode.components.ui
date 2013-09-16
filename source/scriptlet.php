<?php


namespace Components;


  /**
   * Ui_Scriptlet
   *
   * @api
   * @package net.evalcode.components.ui
   *
   * @author evalcode.net
   */
  class Ui_Scriptlet extends Http_Scriptlet
  {
    // PROPERTIES
    /**
     * @var string
     */
    public $title;
    /**
     * @var \Components\Ui_Panel
     */
    public $panel;
    /**
     * @var boolean
     */
    public static $embedded;
    /**
     * @var string
     */
    public static $transferSessionId;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    public static function dispatch(Http_Scriptlet_Context $context_, Uri $uri_)
    {
      $response=$context_->getResponse();

      $content=null;
      $exception=null;

      try
      {
        $content=parent::dispatch($context_, $uri_);

        $exception=$response->getException();
      }
      catch(\Exception $e)
      {
        $exception=$e;
      }

      if($response->getMimetype()->isApplicationJson())
      {
        $parameters=$response->getParameters();
        $parameters['content']=$content;

        if(null!==$exception)
        {
          // Only log and indicate an exception for AJAX requests ..
          exception_log($exception);

          Http_Exception::sendHeaderInternalError();

          // .. yet admin/development access should see more details
          if(Runtime::isManagementAccess())
            $parameters['exception']=exception_as_json($exception);

          $response->unsetException();
        }

        echo json_encode(array($parameters));
      }
      else
      {
        echo $content;

        if(null!==$exception)
          throw $exception;
      }
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    // TODO Implement ui/router for panel access.
    public function get()
    {
      $params=$this->request->getParams();

      // TODO Not a submitted form or ajax request - Implement regular routing ...
      if(false===$params->containsKey('ui-panel-submitted'))
      {
        if(__CLASS__!==get_class($this))
        {
          // FIXME Session may still not be necessarily required / make lazy?
          if(false===isset($_SESSION))
            session_start();

          $this->init();

          $engine=new Ui_Template();
          $engine->self=$this;

          return $engine->render(__DIR__.'/scriptlet.tpl');
        }

        throw Http_Exception::notFound('ui/scriptlet');
      }

      Ui_Panel::setSubmittedPanelId(
        $submittedPanelId=$params->get('ui-panel-submitted')
      );

      /**
       * TODO Check & close possibilities to exploit this for
       * i.e. session stealing hijacking.
       */
      if(!session_id() && $params->containsKey('ui-panel-sid'))
        session_id($params->get('ui-panel-sid'));

      if($params->containsKey('ui-panel-callback'))
      {
        $callback=$params->get('ui-panel-callback');

        if(false!==($pos=strpos($callback, '::')))
        {
          $type=substr($callback, 0, $pos);
          $method=substr($callback, $pos+2);

          // TODO Runtime_Classloader::lookupClass(class/name)
          if(class_exists($type) && method_exists($type, $method))
            return $type::$method();
        }
      }

      if(false===$params->containsKey('ui-panel-form') || !($form=$params->get('ui-panel-form')))
        throw Http_Exception::notFound('ui/scriptlet');

      if(false===isset($_SESSION))
        session_start();

      $redraw=null;
      $panels=array();

      /**
       * TODO Either obsolete/remove with regular routing or implement
       * a solution to selectively initialize requested/required panel(s) and
       * dependencies instead of whole panel tree.
       */
      $form=json_decode($form);

      $i=0;
      foreach($form as $name=>$type)
      {
        $type=String::pathToType($type);

        if(0===$i)
        {
          $this->panel=$panels[$i]=new $type($name);
          $this->panel->scriptlet=$this;
        }
        else
        {
          if(isset($panels[$i-1]->$name))
            $panels[$i]=$panels[$i-1]->$name;
          else
            $panels[$i-1]->add($panels[$i]=new $type($name));
        }

        if(null===$redraw && $panels[$i]->redraw())
          $redraw=$panels[$i];

        $i++;
      }

      if(null!==$redraw)
      {
        $this->response->addParameter('redraw', $redraw->getId());

        return $redraw->fetch();
      }

      /**
       * FIXME (CSH) We need to support multiple redraw panels
       * per request/response if we allow callbacks to selectively
       * request (sub-)panels.
       */
      foreach(end($panels)->getPanels() as $panel)
      {
        if($panel->redraw())
        {
          $this->response->addParameter('redraw', $panel->getId());

          return $panel->fetch();
        }
      }
    }

    public function post()
    {
      return $this->get();
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected function init()
    {
      $this->panel=new Ui_Panel_Root('ui-panel');
      $this->panel->scriptlet=$this;

      // Override for router-free ui/panel dispatch.
    }
    //--------------------------------------------------------------------------
  }
?>
