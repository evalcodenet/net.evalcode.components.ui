<?php


namespace Components;


  /**
   * Ui_Scriptlet
   *
   * @package net.evalcode.components
   * @subpackage ui
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
      }
      catch(\Exception $e)
      {
        if(!$e instanceof Http_Exception)
          $e=new Http_Exception_Wrapper($e);

        $exception=$e;
      }

      if(null===$exception)
        $exception=$response->getException();

      if(Io_Mimetype::APPLICATION_JSON()->equals($response->getMimetype()))
      {
        $parameters=$response->getParameters();
        $parameters['content']=$content;

        if(null!==$exception)
        {
          $exception->log();
          $exception->sendHeader();

          $response->unsetException();

          if(Debug::active() && Runtime::isManagementAccess())
            $parameters['exception']=$exception->toJson();
        }

        echo json_encode(array($parameters));
      }
      else
      {
        if(null!==$exception)
          throw $exception;

        echo $content;
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

      // TODO Store & verify possibly correct session ids per useragent+host to prevent stealing ..
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

      $form=json_decode($form);

      $i=0;
      foreach($form as $name=>$type)
      {
        $type=String::pathToType($type);

        $panels[$i]=$this->panel=new $type($name);
        if($panels[$i] instanceof Ui_Panel_Root)
          $panels[$i]->scriptlet=$this;
        else if(false===isset($panels[$i-1]->$name))
          $panels[$i-1]->add($panels[$i]);

        if(null===$redraw && $panels[$i]->redraw())
          $redraw=$panels[$i];

        $i++;
      }

      if(null!==$redraw)
      {
        $this->response->addParameter('redraw', $redraw->getId());

        return $redraw->fetch();
      }

      // FIXME (CSH) If we allow callbacks to selectivly redraw sub-panels, we need to support multiple redraw panels.
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
    * (non-PHPdoc)
    * @see Components\Object::equals()
    */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
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
