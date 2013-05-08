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

      if(Io_MimeType::APPLICATION_JSON()->equals($response->getMimeType()))
      {
        $parameters=$response->getParameters();
        $parameters['content']=$content;

        if(null!==$exception)
        {
          $exception->log();
          $exception->sendHeader();

          $response->setException(null);

          if(Debug::enabled() && Runtime::isManagementAccess())
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
        throw new Http_Exception('components/ui/scriptlet', Http_Exception::NOT_FOUND);

      Ui_Panel::setSubmittedPanelId(
        $submittedPanelId=$params->get('ui-panel-submitted')
      );

      $callbackPanelId=null;
      $callbackPanelMethod=null;

      if($params->containsKey('ui-panel-callback'))
      {
        $callback=$params->get('ui-panel-callback');
        $type=substr($callback, 0, strpos($callback, '::'));
        $method=substr($callback, strpos($callback, '::')+2);

        if(class_exists($type) && method_exists($type, $method))
          return $type::$method();

        $callbackPanelId=$type;
        $callbackPanelMethod=$method;
      }

      if(false===$params->containsKey('ui-panel-path') || !($path=$params->get('ui-panel-path')))
        throw new Http_Exception('components/ui/scriptlet', Http_Exception::NOT_FOUND);

      // ui/panel callback
      session_start();

      // TODO Lazy..
      Config::get('i18n');

      $types=array();
      foreach(explode(',', $path) as $type)
        $types[substr($type, 0, strpos($type, ':'))]=substr($type, strpos($type, ':')+1);

      $root=new Ui_Panel_Root('ui-panel');
      $root->scriptlet=$this;

      $callbackPanel=null;
      $submittedPanel=null;

      $redraw=null;
      $panels=array(-1=>$root);
      $names=array_keys($types);

      for($i=0, $count=count($names); $i<$count; $i++)
      {
        $type=$types[$names[$i]];

        if(isset($panels[$i-1]->{$names[$i]}))
        {
          if(null===$redraw && $panels[$i-1]->{$names[$i]}->redraw())
            $redraw=$panels[$i-1]->{$names[$i]};
        }
        else
        {
          $panels[$i-1]->add($panels[$i]=new $type($names[$i]));
          if(null===$redraw && $panels[$i]->redraw())
            $redraw=$panels[$i];
        }

        if(null!==$callbackPanelId && $callbackPanelId===$panels[$i]->getId())
          $callbackPanel=$panels[$i];
        if($submittedPanelId===$panels[$i]->getId())
          $submittedPanel=$panels[$i];
      }

      if(null!==$callbackPanelId && null===$callbackPanel)
        $callbackPanel=$panels[count($panels)-1]->getPanelForId($callbackPanelId);

      if(null!==$callbackPanel)
      {
        if(null===$submittedPanel)
          $submittedPanel=$panels[count($panels)-1]->getPanelForId($submittedPanelId);

        $callbackPanel->$callbackPanelMethod($submittedPanel);
      }

      if(null!==$redraw)
      {
        $this->response->addParameter('redraw', $redraw->getContainerId());

        return $redraw->render();
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
    * @see Components.Object::equals()
    */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------
  }
?>
