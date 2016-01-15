<?php


namespace Components;


  /**
   * Ui_Scriptlet_Embedded
   *
   * @api
   * @package net.evalcode.components.ui
   *
   * @author evalcode.net
   */
  class Ui_Scriptlet_Embedded extends Ui_Scriptlet
  {
    // CONSTRUCTION
    public function __construct()
    {
      Environment::isEmbedded(true);

      $this->template=__DIR__.'/embedded.tpl';

      $this->script('ui/jquery/jquery-1.11.2.min', false, false);
      $this->script('runtime/libstd');

      $this->script('ui/jquery/mobile/jquery.mobile.touch.min');

      $this->script('ui/common');
      $this->style('ui/common');

      $this->panel=new Ui_Panel('ui-panel');
      $this->panel->scriptlet=$this;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
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

      $path=Environment::pathComponentsResource($ns, 'resource', 'js', implode('/', $chunks).'.js');
      $uri=Environment::uriComponentsResource($ns.'/js/'.implode('/', $chunks).'.js');

      $options=[];
      $options['src']=$uri;

      if($defer_)
        $options['defer']='defer';
      if($async_)
        $options['async']='async';

      $this->scripts[$path]=$options;

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

      $path=Environment::pathComponentsResource($ns, 'resource', 'css', implode('/', $chunks).'.css');
      $uri=Environment::uriComponentsResource($ns.'/css/'.implode('/', $chunks).'.css');

      $options=[];
      $options['href']=$uri;
      $options['media']=$media_;

      $this->styles[$path]=$options;

      return true;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected function init()
    {
      // Do nothing.
    }

    /**
     * @return \Components\Ui_Panel[]
     */
    protected function dispatchImpl()
    {
      $isHtml=$this->response->getMimetype()->isHtml();

      $params=$this->request->getParams();
      $path=$params->get('ui-panel-path');

      if($scope=$params->get('ui-panel-scope'))
        session_name($scope);

      if(!self::$transactionId=$params->get('ui-panel-tx'))
        self::$transactionId=\math\random_sha1_weak();

      self::$submittedPanelId=$params->get('ui-panel-submitted');


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
            return $type::$method($this->request, $this->response);
        }
      }


      if(null===$path)
        throw new Http_Exception('ui/scriptlet', null, Http_Exception::NOT_FOUND);


      // embedded mode
      $path=json_decode($path);

      $i=0;
      $panels=[];

      foreach($path as $name=>$type)
      {
        $type=\str\pathToType($type);

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

        $i++;
      }

      $panels=[];

      foreach($this->panel->redrawPanels() as $panel)
        $panels[$panel->id()]=$panel->fetch();

      return json_encode(['p'=>$panels]);
    }
    //--------------------------------------------------------------------------
  }
?>
