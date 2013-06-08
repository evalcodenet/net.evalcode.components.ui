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
    public static $embedded=false;
    public static $transferSessionId=false;

    public $title;
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

          $response->setException(null);

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
        throw Http_Exception::notFound('ui/scriptlet');

      Ui_Panel::setSubmittedPanelId(
        $submittedPanelId=$params->get('ui-panel-submitted')
      );

      // TODO Store & verify possibly correct session ids per useragent+host to prevent stealing ..
      if(self::$transferSessionId && $params->containsKey('ui-panel-sid'))
        session_id($params->get('ui-panel-sid'));

      session_start();

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

      if(false===$params->containsKey('ui-panel-path') || !($path=$params->get('ui-panel-path')))
        throw Http_Exception::notFound('ui/scriptlet');

      $redraw=null;
      $panels=array();
      $submittedPanel=null;

      $types=array();
      foreach(explode(',', $path) as $type)
        $types[substr($type, 0, strpos($type, ':'))]=substr($type, strpos($type, ':')+1);

      $names=array_keys($types);
      for($i=0, $count=count($names); $i<$count; $i++)
      {
        $type=String::pathToType($types[$names[$i]]);

        $panels[$i]=new $type($names[$i]);
        if($panels[$i] instanceof Ui_Panel_Root)
          $panels[$i]->scriptlet=$this;
        else if(false===isset($panels[$i-1]->{$names[$i]}))
          $panels[$i-1]->add($panels[$i]);

        if($submittedPanelId===$panels[$i]->getId())
          $submittedPanel=$panels[$i];

        if(null===$redraw && $panels[$i]->redraw())
          $redraw=$panels[$i];
      }

      if(null===$submittedPanel)
        $submittedPanel=$panels[count($panels)-1]->getPanelForId($submittedPanelId);

      if(null!==$redraw)
      {
        $this->response->addParameter('redraw', $redraw->getContainerId());

        return $redraw->render();
      }

      // FIXME (CSH) If we allow callbacks to selectivly redraw sub-panels, we need to support multiple redraw panels.
      foreach(end($panels)->getPanels() as $panel)
      {
        if($panel->redraw())
        {
          $this->response->addParameter('redraw', $panel->getContainerId());

          return $panel->render();
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
  }
?>
