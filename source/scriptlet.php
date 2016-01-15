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
    public static $submittedPanelId;
    /**
     * @var string
     */
    public static $transactionId;

    /**
     * @var string
     */
    public $title;
    /**
     * @var \Components\Ui_Panel
     */
    public $panel;
    /**
     * @var string
     */
    public $template;
    /**
     * @var string[]
     */
    public $scripts=[];
    /**
     * @var string[]
     */
    public $styles=[];
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @see \Components\Http_Scriptlet::dispatch() dispatch
     */
    public static function dispatch(Http_Scriptlet_Context $context_, Uri $uri_)
    {
      $response=$context_->getResponse();

      $content=null;

      try
      {
        // FIXME Temporary fix - create a explicit route for ui/scriptlet/embedded.
        if(Environment::isEmbedded())
        {
          $scriptlet=new Ui_Scriptlet_Embedded();
          $scriptlet->request=$context_->getRequest();
          $scriptlet->response=$context_->getResponse();

          $method=$scriptlet->request->getMethod();

          if(false===method_exists($scriptlet, strtolower($method)))
            throw new Http_Exception('ui/scriptlet', null, Http_Exception::NOT_FOUND);

          $content=$scriptlet->$method();
        }
        else
        {
          $content=parent::dispatch($context_, $uri_);
        }
      }
      catch(\Exception $e)
      {
        Runtime::addException($e);

        if($e instanceof Http_Exception)
          $e->sendHeader();
      }

      echo $content;
    }

    /**
     * @return string
     */
    public static function transactionId()
    {
      if(!self::$transactionId)
        self::$transactionId=\math\random_sha1_weak();

      return self::$transactionId;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return string
     */
    public function post()
    {
      return $this->dispatchImpl();
    }

    /**
     * @return string
     */
    public function get()
    {
      return $this->dispatchImpl();
    }

    /**
     * @param string $name_
     * @param boolean $async_
     *
     * @return boolean
     */
    public function script($name_, $async_=true, $defer_=false)
    {
      if(0===strpos($name_, '/'))
        return false;

      $chunks=explode('/', $name_);
      $ns=array_shift($chunks);

      $uri=Environment::uriComponentsResource($ns.'/js/'.implode('/', $chunks).'.js');

      $options=[];
      $options['src']=$uri;

      if($defer_)
        $options['defer']='defer';
      if($async_)
        $options['async']='async';

      $this->scripts[$uri]=$options;

      return true;
    }

    /**
     * @param string $name_
     * @param string $media_
     *
     * @return boolean
     */
    public function style($name_, $media_='all')
    {
      if(0===strpos($name_, '/'))
        return false;

      $chunks=explode('/', $name_);
      $ns=array_shift($chunks);

      $uri=Environment::uriComponentsResource($ns.'/css/'.implode('/', $chunks).'.css');

      $options=[];
      $options['href']=$uri;
      $options['media']=$media_;

      $this->styles[$uri]=$options;

      return true;
    }

    /**
     * @return string
     */
    public function fetch()
    {
      $engine=new Ui_Template();
      $engine->self=$this;

      return $engine->render($this->template);
    }

    /**
     * @return string
     */
    public function display()
    {
      $engine=new Ui_Template();
      $engine->self=$this;

      $engine->display($this->template);
    }

    /**
     * @return void
     */
    public function printReferences()
    {
      $print=function($pattern_, $attributes_)
      {
        $attributes='';
        foreach($attributes_ as $name=>$value)
          $attributes.=" $name=\"$value\"";

        printf($pattern_, $attributes);
      };

      foreach($this->scripts as $script=>$attributes)
        $print("<script type=\"text/javascript\"%s></script>", $attributes);
      foreach($this->styles as $stylesheet=>$attributes)
        $print("<link type=\"text/css\" rel=\"stylesheet\"%s/>", $attributes);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected function init()
    {
      $this->template=__DIR__.'/scriptlet.tpl';

      // Override for router-free ui/panel dispatch.
      $this->script('ui/jquery/jquery-1.11.2.min', false, false);
      $this->script('runtime/libstd');

      $this->script('ui/jquery/mobile/jquery.mobile.touch.min');

      $this->script('ui/common');
      $this->style('ui/common');

      $this->panel=new Ui_Panel('ui-panel');
      $this->panel->scriptlet=$this;
    }


    /**
     * @return \Components\Ui_Panel[]
     */
    // TODO [CSH] Implement ui/router for panel access.
    protected function dispatchImpl()
    {
      $isHtml=$this->response->getMimetype()->isHtml();

      $params=$this->request->getParams();

      if(!self::$transactionId=$params->get('ui-panel-tx'))
        self::$transactionId=\math\random_sha1_weak();

      self::$submittedPanelId=$params->get('ui-panel-submitted');

      if(__CLASS__!==get_class($this))
      {
        $this->init();

        if($isHtml)
          return $this->fetch();

        $panels=[];

        foreach($this->panel->redrawPanels() as $panel)
          $panels[$panel->id()]=$panel->fetch();

        return json_encode(['p'=>$panels]);
      }


      // static callback
      if($params->containsKey('ui-panel-callback'))
      {
        $callback=$params->get('ui-panel-callback');

        if(false!==($pos=strpos($callback, '::')))
        {
          $type=substr($callback, 0, $pos);
          $method=substr($callback, $pos+2);

          // TODO [CSH] Runtime_Classloader::lookupClass(class/name).
          if(class_exists($type) && method_exists($type, $method))
            return $type::$method();
        }
      }
    }
    //--------------------------------------------------------------------------
  }
?>
