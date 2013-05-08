<?php


namespace Components;


  /**
   * Ui_Panel
   *
   * @package net.evalcode.components
   * @subpackage ui
   *
   * @author evalcode.net
   *
   * @property Ui_Panel_Root $root
   * @property Ui_Scriptlet $scriptlet
   */
  class Ui_Panel implements Object
  {
    // PREDEFINED PROPERTIES
    const JS_TRIGGER_ON_CALLBACK_SUBMIT=1;
    const JS_TRIGGER_ON_CALLBACK_RESPONSE=2;

    const ELEMENT_BLOCK='div';
    const ELEMENT_PREFORMATTED='pre';
    const ELEMENT_LIST='ul';
    const ELEMENT_DEFINITION_LIST='dl';
    const ELEMENT_DEFAULT=self::ELEMENT_BLOCK;
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var Properties
     */
    public $params;
    /**
     * @var Ui_Panel_Session
     */
    public $session;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, $value_=null, $title_=null)
    {
      $this->m_name=$this->m_id=$name_;

      $this->m_value=$value_;
      $this->m_title=$title_;

      $this->params=new Properties();

      $this->m_template=__DIR__.'/panel.tpl';
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      // Override ...
    }

    protected function afterInit()
    {
      // Override ...
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function add(Ui_Panel $panel_)
    {
      $this->m_children[$panel_->m_name]=$panel_;

      $panel_->setParent($this);
    }

    public function remove(Ui_Panel $panel_)
    {
      $this->m_children[$panel_->m_name]=null;
    }

    public function hasParent()
    {
      return null!==$this->m_parent;
    }

    public function getParent()
    {
      return $this->m_parent;
    }

    public function setParent(Ui_Panel $parent_)
    {
      $this->m_parent=$parent_;

      foreach($this->m_children as $panel)
        $panel->setParent($this);

      $this->initialize();
    }

    public function getPath()
    {
      return $this->m_path;
    }

    /**
     * @param string $panelId_
     *
     * @return Ui_Panel
     */
    public function getPanelForId($panelId_)
    {
      if(0===strpos($panelId_, $this->m_id))
      {
        if($this->m_id===$panelId_)
          return $this;

        $path=substr($panelId_, 0, strlen($this->m_id));

        // in sub path
        $panel=$this;
        $names=explode('-', $panelId_);
        while($next=array_shift($names))
        {
          if(isset($panel->m_children[$next]))
            $panel=$panel->m_children[$next];
        }

        return $panel;
      }

      if(0===strpos($this->m_id, $panelId_))
      {
        // in parent path ..
        $panel=$this;
        while($panel=$panel->m_parent)
        {
          if($panelId_===$panel->m_id)
            return $panel;
        }
      }

      if(null!==$this->m_parent)
        return $this->m_parent->getPanelForId($panelId_);
    }

    public function getId()
    {
      return $this->m_id;
    }

    public function getName()
    {
      return $this->m_name;
    }

    public function getValue()
    {
      return $this->m_value;
    }

    public function setValue($value_)
    {
      $this->m_value=$value_;
    }

    public function getTitle()
    {
      return $this->m_title;
    }

    public function setTitle($title_)
    {
      $this->m_title=$title_;
    }

    public function getContainerId()
    {
      return $this->m_containerId;
    }

    public function getContainerTag()
    {
      return $this->m_containerTag;
    }

    public function setContainerTag($htmlTag_)
    {
      $this->m_containerTag=$htmlTag_;
    }

    public function getAttribute($name_)
    {
      if(false===isset($this->m_attributes[$name_]))
        return null;

      return $this->m_attributes[$name_];
    }

    public function setAttribute($name_, $value_)
    {
      $this->m_attributes[$name_]=$value_;
    }

    public function getAttributesAsString()
    {
      $attributes=array(
        'class="'.String::escapeHtml(implode(' ', $this->m_classes)).'"'
      );

      foreach($this->m_attributes as $key=>$value)
      {
        if(is_array($value))
          array_push($attributes, $key.'="'.String::escapeHtml(implode(' ', $value)).'"');
        else
          array_push($attributes, "$key=\"".String::escapeHtml($value).'"');
      }

      return implode(' ', $attributes);
    }

    public function addClass($name_)
    {
      $this->m_classes[$name_]=$name_;
    }

    public function removeClass($name_)
    {
      if(isset($this->m_classes[$name_]))
        unset($this->m_classes[$name_]);
    }

    public function getTemplate()
    {
      return $this->m_template;
    }

    public function setTemplate($template_)
    {
      $this->m_template=$template_;
    }

    public function isForm()
    {
      foreach($this->m_children as $panel)
      {
        if($panel->hasCallback())
          return true;
      }

      return false;
    }

    public function isActiveForm()
    {
      if($this->isForm())
      {
        $panel=$this;
        while($panel=$panel->m_parent)
        {
          if($panel->isForm())
            return false;
        }

        return true;
      }

      return false;
    }

    public function hasCallback()
    {
      return null!==$this->m_callback;
    }

    public function getCallback()
    {
      return $this->m_callback;
    }

    public function setCallback(array $callback_)
    {
      $this->m_callback=$callback_;
    }

    public function hasCallbackJs()
    {
      return null!==$this->m_callbackJs;
    }

    public function getCallbackJs()
    {
      return $this->m_callbackJs;
    }

    public function setCallbackJs($functionJs_, array $params_=array())
    {
      $this->m_callbackJs=array($functionJs_, $params_);
    }

    public function hasTriggerJs()
    {
      return 0<count($this->m_triggerJs);
    }

    public function getTriggerJs()
    {
      return $this->m_triggerJs;
    }

    public function addTriggerJs($type_, $js_, $param0_=null/*, $param1_, ...*/)
    {
      $args=func_get_args();
      $type=array_shift($args);
      $method=array_shift($args);

      $this->m_triggerJs[$type]=array('method'=>$method, 'args'=>$args);
    }

    public function display()
    {
      if($this instanceof Ui_Panel_Root)
        $this->initialize();

      $type=new \ReflectionObject($this);

      $this->addClass(strtr(strtolower($type->getShortName()), '-', '_'));
      while($type=$type->getParentClass())
        $this->addClass(strtr(strtolower($type->getShortName()), '-', '_'));

      if($this->isActiveForm())
        $this->addClass('ui_panel_form');

      if($this->m_hasContainer)
        printf('<%2$s id="%1$s"%3$s>', $this->m_containerId, $this->m_containerTag, $this->getAttributesAsString());

      if($this->isActiveForm())
      {
        $panel=$this;
        $tree=array($this->m_name.':'.get_class($this));
        while($panel=$panel->m_parent)
        {
          if($panel instanceof Ui_Panel_Root)
            break;

          $tree[]=$panel->m_name.':'.get_class($panel);
        }

        $tree=array_reverse($tree);

        printf(
          '<input type="hidden" name="ui-panel-ie" value="1"/>%2$s'.
          '<input type="hidden" name="ui-panel-path" value="%1$s"/>%2$s',
            implode(',', $tree),
            "\n"
        );
      }

      $engine=new Ui_Template();
      $this->initTemplateEngine($engine);
      $engine->display($this->getTemplate());

      if(count($this->m_scripts) || count($this->m_stylesheets))
      {
        echo '<script type="text/javascript">';

        foreach($this->m_scripts as $name=>$options)
        {
          $condition=isset($options['condition'])?'"'.addcslashes($options['condition'], "\"\r\n").'"':'null';
          $callback=isset($options['callback'])?'"'.addcslashes($options['callback'], "\"\r\n").'"':'null';

          printf('ui_panel_script_add("%1$s", true, %2$s, %3$s, %4$s);', $name, $condition, $callback, Runtime::getTimestampLastUpdate());
        }

        foreach($this->m_stylesheets as $name=>$media)
          printf('ui_panel_stylesheet_add("%1$s", true, "%2$s", %3$s);', $name, $media, Runtime::getTimestampLastUpdate());

        echo '</script>';
      }

      if($this->m_hasContainer)
        printf('</%1$s>', $this->m_containerTag);
    }

    public function render()
    {
      ob_start();
      $this->display();

      return ob_get_clean();
    }

    public function redraw($redraw_=null)
    {
      if(null===$redraw_)
        return $this->m_redraw;

      return $this->m_redraw=$redraw_?true:false;
    }

    public function redirect($uri_)
    {
      header("Location: $uri_", true, 302);
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    public static function getSubmittedPanelId()
    {
      return self::$m_submittedPanelId;
    }

    public static function setSubmittedPanelId($panelId_)
    {
      self::$m_submittedPanelId=$panelId_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function __isset($name_)
    {
      return isset($this->m_children[$name_]);
    }

    public function __get($name_)
    {
      if('scriptlet'===$name_)
        return $this->root->scriptlet;

      if('root'===$name_)
      {
        $panel=$this;
        while($panel->m_parent)
          $panel=$panel->m_parent;

        return $panel;
      }

      if(isset($this->m_children[$name_]))
        return $this->m_children[$name_];

      $trace=debug_backtrace(false);
      $caller=$trace[0];

      throw new Ui_Panel_Exception('ui/panel', sprintf('Undefined property: %1$s::$%2$s in %3$s on line %4$s',
        get_class($this),
        $name_,
        $caller['file'],
        $caller['line']
      ));
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
     * @see Components.Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{id: %s, name: %s, value: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_id,
        $this->m_name,
        $this->m_value
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_submittedPanelId;

    private $m_path=array();
    private $m_attributes=array();
    private $m_children=array();
    private $m_errors=array();
    private $m_triggerJs=array();
    private $m_references=array();
    private $m_stylesheets=array();
    private $m_scripts=array();
    private $m_classes=array();

    /**
     * @var Ui_Panel
     */
    private $m_parent;
    private $m_id;
    private $m_name;
    private $m_value;
    private $m_title;
    private $m_template;
    private $m_callback;
    private $m_callbackJs;
    private $m_hasContainer=true;
    private $m_containerId=true;
    private $m_containerTag=self::ELEMENT_DEFAULT;
    private $m_redraw=false;
    //-----


    protected function hasContainer($toggle_=null)
    {
      if(null===$toggle_)
        return $this->m_hasContainer;

      return $this->m_hasContainer=$toggle_?true:false;
    }

    protected function hasRequestParam($name_)
    {
      return array_key_exists("{$this->m_id}-$name_", $_REQUEST);
    }

    protected function getRequestParam($name_)
    {
      if(false===array_key_exists("{$this->m_id}-$name_", $_REQUEST))
        return null;

      return $_REQUEST["{$this->m_id}-$name_"];
    }

    protected function addError($message_, $arg0_=null/*, $arg1_, ..*/)
    {
      $args=func_get_args();
      $message=array_shift($args);

      array_push($this->m_errors, array('message'=>$message_, 'args'=>$args));
    }

    protected function addScript($name_, $conditionJs_=null, $callbackJsOnLoad_=null)
    {
      if(0!==strpos($name_, '/'))
      {
        $chunks=explode('/', $name_);
        $ns=array_shift($chunks);

        $name_='/'.$ns.'/resource/js/'.implode('/', $chunks).'.js';
      }

      $this->m_scripts[$name_]=array('condition'=>$conditionJs_, 'callback'=>$callbackJsOnLoad_);
    }

    protected function addStylesheet($name_, $media_='all')
    {
      if(0!==strpos($name_, '/'))
      {
        $chunks=explode('/', $name_);
        $ns=array_shift($chunks);

        $name_='/'.$ns.'/resource/css/'.implode('/', $chunks).'.css';
      }

      $this->m_stylesheets[$name_]=$media_;
    }

    protected function getReferences()
    {
      return $this->m_references;
    }

    protected function addReference($name_, $tag_, $value_, array $attributes_=array())
    {
      $this->m_references[$name_]=array($tag_, $value_, $attributes_);
    }

    protected function initTemplateEngine(Ui_Template $engine_)
    {
      $engine_->self=$this;

      $engine_->id=$this->m_id;
      $engine_->title=$this->m_title;
      $engine_->value=$this->m_value;
      $engine_->params=$this->params;
      $engine_->panels=$this->m_children;

      $engine_->panel=array($this, 'displayPanel');
      $engine_->attributes=array($this, 'getAttributesAsString');
      $engine_->hasErrors=array($this, 'hasErrors');
      $engine_->getErrors=array($this, 'getErrors');
      $engine_->printErrors=array($this, 'printErrors');
      $engine_->callbackJs=array($this, 'callbackJs');
      $engine_->callbackAjax=array($this, 'callbackAjax');
      $engine_->hasCallback=array($this, 'hasCallback');
      $engine_->hasCallbackJs=array($this, 'hasCallbackJs');
      $engine_->hasCallbackAjax=array($this, 'hasCallbackAjax');
    }

    protected function onRetrieveValue()
    {
      if(isset($_REQUEST[$this->m_id]))
      {
        if('null'==($value=$_REQUEST[$this->m_id]))
          $this->m_value=null;
        else
          $this->m_value=$value;
      }
    }


    private function initialize()
    {
      $path=array();
      $panel=$this;

      do
      {
        array_unshift($path, $panel->m_name);
      }
      while($panel=$panel->m_parent);

      $this->m_path=$path;
      $this->m_id=implode('-', $path);
      $this->m_containerId="{$this->m_id}-container";

      $this->session=Ui_Panel_Session::forNamespace($this->m_id);

      $this->init();
      $this->afterInit();

      $this->onRetrieveValue();

      if(null!==$this->m_callback && self::$m_submittedPanelId===$this->m_id)
        call_user_func_array($this->m_callback, array($this));
    }


    // TEMPLATE METHODS
    /*private*/ function displayPanel($name_)
    {
      if(isset($this->m_children[$name_]))
        $this->m_children[$name_]->display();
    }

    /*private*/ function hasCallbackAjax()
    {
      return $this->hasCallback();
    }

    /*private*/ function callbackAjax(array $params_=array(), $callback_=null)
    {
      if(null===$callback_)
        $callback_=$this->m_callback;

      $callback='null';
      if(isset($callback_[0]) && is_string($callback_[0]))
        $callback=json_encode(array($callback_));

      return sprintf('ui_panel_submit(\'%1$s\', %2$s, %3$s, %4$s);',
        $this->m_id,
        strtr($callback, '"', "'"),
        count($params_)?strtr(json_encode($params_), '"', "'"):'null',
        count($this->m_triggerJs)?strtr(json_encode($this->m_triggerJs), '"', "'"):'null'
      );
    }

    /*private*/ function callbackJs(array $params_=array())
    {
      if(null===$this->m_callbackJs)
        return null;

      $functionJs=$this->m_callbackJs[0];
      $params=array_merge($this->m_callbackJs[1], $params_);

      return sprintf('%3$s(\'%1$s\', %2$s);',
        $this->m_id,
        count($params)?strtr(json_encode($params), '"', "'"):'null',
        $functionJs
      );
    }

    /*private*/ function hasErrors($includeSubPanels_=true)
    {
      if($includeSubPanels_)
      {
        foreach($this->m_children as $panel)
        {
          if($panel->hasErrors(true))
            return true;
        }
      }

      return 0<count($this->m_errors);
    }

    /*private*/ function getErrors($includeSubPanels_=true, array &$errors_=array())
    {
      if(0<count($this->m_errors))
        $errors_[$this->m_id]=array('panel'=>$this, 'errors'=>$this->m_errors);

      if($includeSubPanels_)
      {
        foreach($this->m_children as $panel)
          $panel->getErrors(true, $errors_);
      }

      return $errors_;
    }

    /*private*/ function countErrors($includeSubPanels_=true)
    {
      $count=count($this->m_errors);
      if($includeSubPanels_)
      {
        foreach($this->m_children as $panel)
          $count+=$panel->countErrors($includeSubPanels_);
      }

      return $count;
    }

    /*private*/ function printErrors($includeSubPanels_=true, $expandThreshold_=4)
    {
      $errors=$this->getErrors($includeSubPanels_);

      if(1>($count=$this->countErrors($includeSubPanels_)))
        return;

      printf('
        <div class="ui_panel_errors">
          <div class=" ui_panel_disclosure_header">
            <h2 class="title">%2$s (%3$s)</h2>
            <a href="javascript:void(0);" rel="%1$s-errors" class="ui_panel_disclosure_toggle%4$s">collapse</a>
          </div>',
          $this->m_id,
          // TODO Localize ...
          String::escapeHtml(translate('ui/panel/errors/title')),
          $count,
          $count>$expandThreshold_?'':' expanded'
      );

      printf('<ul id="%1$s-errors" class="ui_panel_errors">', $this->m_id);

      foreach($errors as $panelId=>$value)
      {
        if($includeSubPanels_)
        {
          if(!$title=$value['panel']->m_title)
            $title=$value['panel']->m_name;

          printf('<li class="ui_panel_error_category"><label for="%1$s">%2$s</label><ul>',
            $value['panel']->m_id,
            String::escapeHtml($title)
          );
        }

        foreach($value['errors'] as $error)
        {
          printf('<li class="ui_panel_error"><label for="%1$s">%2$s</label></li>',
            $value['panel']->m_id,
            String::escapeHtml($error['message'])
          );
        }

        if($includeSubPanels_)
          echo '</ul></li>';
      }

      echo '</ul></div>';
    }
    //--------------------------------------------------------------------------
  }
?>
