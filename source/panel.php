<?php


namespace Components;


  /**
   * Ui_Panel
   *
   * @api
   * @package net.evalcode.components.ui
   *
   * @author evalcode.net
   */
  class Ui_Panel implements Object
  {
    // PREDEFINED PROPERTIES
    const MODE_VIEW=1;
    const MODE_EDIT=2;
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var integer
     */
    public $mode=self::MODE_EDIT;

    /**
     * @var string
     */
    public $tag='div';
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $title;
    /**
     * @var \Components\Ui_Panel
     */
    public $parent;
    /**
     * @var \Components\Ui_Panel
     */
    public $root;
    /**
     * @var \Components\Ui_Scriptlet
     */
    public $scriptlet;

    /**
     * @var string
     */
    public $template;
    /**
     * @var \Closure|callable
     */
    public $callback;

    /**
     * @var mixed
     */
    public $value;
    /**
     * @var mixed
     */
    public $valueDefault;
    /**
     * @var string [typename \Components\Value]
     */
    public $valueType;

    /**
     * @var string
     */
    public $panelType;
    /**
     * @var string[]
     */
    public $panelProperties=[];
    /**
     * @var string[]
     */
    public $panelPropertiesToggle=[];

    /**
     * @var boolean
     */
    public $ajaxEnabled=true;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, $value_=null, $title_=null)
    {
      $this->root=$this;

      $this->name=$name_;
      $this->title=$title_;
      $this->value=$value_;
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      // Override ...
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param \Components\Ui_Panel $panel_
     */
    public function add(Ui_Panel $panel_, $category_=null)
    {
      $this->m_children[$panel_->name]=$panel_;

      if(null!==$category_)
        $this->m_categories[$category_][$panel_->name]=$panel_;

      if(null!==$panel_->parent)
        $panel_->clearId();

      $panel_->parent=$this;
      $panel_->root=$this->root;
      $panel_->scriptlet=$this->scriptlet;
      $panel_->ajaxEnabled=$this->ajaxEnabled;

      $panel_->m_form=$this->m_form;

      $panel_->init();
      $panel_->onRetrieveValue();

      // TODO Trigger validators.

      if($panel_->m_form && ($panel_ instanceof Ui_Panel_Button_Submit)
        && $panel_->hasBeenSubmitted() && $panel_->callback)
        call_user_func_array($panel_->callback, [$panel_]);
      else if(null!==$panel_->callback && Ui_Scriptlet::$submittedPanelId===$panel_->id())
        call_user_func_array($panel_->callback, [$panel_]);
    }

    /**
     * @param \Components\Ui_Panel $panel_
     */
    public function remove(Ui_Panel $panel_)
    {
      $panel_->parent=null;
      $panel_->clearId();

      foreach($this->m_categories as $category=>$panels)
      {
        if(isset($this->m_categories[$category][$panel_->name]))
          unset($this->m_categories[$category][$panel_->name]);
      }

      unset($this->m_children[$panel_->name]);
    }

    /**
     * @return string
     */
    public function id()
    {
      if(null===$this->m_id)
      {
        if(null===$this->parent)
          return $this->name;

        $this->m_id=$this->parent->id().'-'.$this->name;
      }

      return $this->m_id;
    }

    /**
     * @return \Components\Http_Session
     */
    public function session()
    {
      if(null===$this->m_session)
        $this->m_session=Http_Session::forNamespace($this->id());

      return $this->m_session;
    }

    /**
     * @return \Components\Ui_Panel[]
     */
    public function panels($category_=null)
    {
      if(null!==$category_ && isset($this->m_categories[$category_]))
        return $this->m_categories[$category_];

      return $this->m_children;
    }

    /**
     * @param mixed $value_
     *
     * @return mixed
     */
    public function value($value_=null)
    {
      if(null!==$value_)
        $this->value=$value_;

      if(null===$this->value && false===$this->hasBeenSubmitted())
        return $this->valueDefault;

      return $this->value;
    }

    /**
     * @param string $panel_
     * @param mixed $value_
     *
     * @return mixed
     */
    public function panelValue($panel_, $value_=null)
    {
      if(false===isset($this->m_children[$panel_]))
        return null;

      if(null!==$value_)
        $this->m_children[$panel_]->value=$value_;

      return $this->m_children[$panel_]->value;
    }

    /**
     * @param string $name_
     * @param scalar $value_
     *
     * @return scalar
     */
    public function attribute($name_, $value_=null)
    {
      if(null!==$value_)
        $this->m_attributes[$name_]=$value_;
      else if(false===isset($this->m_attributes[$name_]))
        return null;

      return $this->m_attributes[$name_];
    }

    /**
     * @return string
     */
    public function attributes()
    {
      $attributes=[];

      if(count($this->m_classes))
        $attributes[]='class="'.\html\escape(implode(' ', $this->m_classes)).'"';

      if(null!==$this->panelType)
        $attributes[]="ui-panel=\"$this->panelType\"";

      $properties=array_merge($this->panelProperties, $this->panelPropertiesToggle);

      if(0<count($properties))
      {
        $attributes[]='ui-panel-properties="'.\html\escape(json_encode($properties)).'"';

        foreach($properties as $property)
        {
          if($this->$property || false===in_array($property, $this->panelPropertiesToggle))
          {
            if(is_scalar($this->$property))
              $attributes[]="$property=\"".\html\escape($this->$property).'"';
            else if($this->$property instanceof self)
              $attributes[]="$property=\"#".$this->$property->id().'"';
            else
              $attributes[]="$property=\"".\html\escape(json_encode($this->$property)).'"';
          }
        }
      }

      foreach($this->m_attributes as $key=>$value)
      {
        if(null===$value)
          $attributes[]=$key;
        else if(is_array($value))
          $attributes[]=\html\escape($key).'="'.\html\escape(implode(' ', $value)).'"';
        else
          $attributes[]=\html\escape($key).'="'.\html\escape($value).'"';
      }

      if(0===count($attributes))
        return '';

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

    public function hasBeenSubmitted()
    {
      $id=$this->id();

      if(isset(self::$m_submittedPanelIds[$id]))
        return self::$m_submittedPanelIds[$id];

      if(Ui_Scriptlet::$submittedPanelId===$id)
        return self::$m_submittedPanelIds[$id]=true;

      $panel=$this;

      $panelIds=[];

      while($panel=$panel->parent)
      {
        $panelId=$panel->id();
        $panelIds[]=$panelId;

        if(Ui_Scriptlet::$submittedPanelId===$panelId)
        {
          foreach($panelIds as $panelId)
            self::$m_submittedPanelIds[$panelId]=true;

          return self::$m_submittedPanelIds[$id]=true;
        }
      }

      return self::$m_submittedPanelIds[$id]=false;
    }

    /**
     * @param string $method_
     * @param string $action_
     * @param \Components\Io_Mimetype $encType_
     * @param string $acceptCharset_
     */
    public function form($method_='POST', $action_=null, Io_Mimetype $encType_=null, $acceptCharset_=null)
    {
      if(null===$acceptCharset_)
        $acceptCharset_=\env\charset();
      if(null===$encType_)
        $encType_=Io_Mimetype::APPLICATION_FORM_URLENCODED();

      $this->m_formProperties=[
        'method'=>$method_,
        'accept-charset'=>$acceptCharset_,
        'enctype'=>$encType_->name()
      ];

      if(null!==$action_)
        $this->m_formProperties['action']=$action_;

      $this->m_form=$this;
    }

    /**
     * Renders and returns markup for this panel or sub-panel.
     *
     * @param string $panel_ Name of sub-panel to render.
     *
     * @return string
     */
    public function fetch($panel_=null)
    {
      ob_start();
      $this->display($panel_);

      return ob_get_clean();
    }

    /**
     * Displays this panel or sub-panel.
     *
     * @param string $panel_ Name of sub-panel to display
     *
     * @throws Ui_Panel_Exception
     */
    public function display($panel_=null)
    {
      if(null!==$panel_)
      {
        if(isset($this->m_children[$panel_]))
          return $this->m_children[$panel_]->display();

        return;
      }

      if(null===$this->m_form)
      {
        if(Environment::isEmbedded() && null!==$this->callback)
          throw new Ui_Panel_Exception('ui/panel', 'Panels with callbacks require a form in embedded mode.');
      }
      else if($this->m_form->id()===$this->id())
      {
        $this->m_attributes['ui-panel-form']=null;

        $this->m_classes['ui_panel_form']='ui_panel_form';

        if($this->ajaxEnabled)
        {
          if(Environment::isEmbedded())
          {
            $path=[$this->name=>\str\typeToPath(get_class($this))];

            $panel=$this;
            while($panel=$panel->parent)
              $path[$panel->name]=\str\typeToPath(get_class($panel));

            $this->m_attributes['ui-panel-path']=json_encode(array_reverse($path));
          }
        }
        else
        {
          $this->tag='form';
        }

        if(isset($this->m_attributes['action']))
          $this->m_attributes['action']=$this->m_formProperties['action'];

        $this->m_attributes['accept-charset']=$this->m_formProperties['accept-charset'];
        $this->m_attributes['enctype']=$this->m_formProperties['enctype'];
        $this->m_attributes['method']=$this->m_formProperties['method'];
      }

      if(null!==$this->tag)
      {
        printf('<%s id="%s" %s>', $this->tag, $this->id(), $this->attributes());

        if('form'===$this->tag)
          printf('<input type="hidden" name="ui-panel-submitted" value="%s">', $this->id());
      }

      echo $this->render();

      if(null!==$this->tag)
        printf('</%s>', $this->tag);
    }

    /**
     * Returns rendered contents of this panel or sub-panel.
     *
     * Auto-generated markup like a form or container
     * element & attributes are omitted.
     *
     * @param string $panel_
     *
     * @return string
     */
    public function render($panel_=null)
    {
      if(null!==$panel_)
      {
        if(isset($this->m_children[$panel_]))
          return $this->m_children[$panel_]->render();

        return;
      }

      if(null===$this->template)
      {
        $rendered='';
        foreach($this->m_children as $panel)
          $rendered.=$panel->fetch();

        return $rendered;
      }

      return $this->templateEngine()->render($this->template);
    }

    /**
     * @param string $uri_
     * @param integer $header_
     */
    public function redirect($uri_, $header_=302)
    {
      header("Location: $uri_", true, $header_);
    }

    /**
     * @return \Components\Ui_Panel[]
     */
    public function redrawPanels()
    {
      return $this->m_redraw;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function __isset($name_)
    {
      return isset($this->m_children[$name_]);
    }

    /**
     * @param string $name_
     *
     * @return \Components\Ui_Panel
     *
     * @throws \Components\Exception_NullPointer
     */
    public function __get($name_)
    {
      if(isset($this->m_children[$name_]))
        return $this->m_children[$name_];


      $trace=debug_backtrace(false);
      $caller=$trace[0];

      throw new Exception_NullPointer('ui/panel', sprintf(
          'Undefined property: %s::$%s in %s on line %s',
        get_class($this),
        $name_,
        $caller['file'],
        $caller['line']
      ));
    }

    /**
     * @see Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s{id: %s, name: %s, value: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->id(),
        $this->name,
        $this->value
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string[]
     */
    private static $m_submittedPanelIds=[];

    /**
     * @var scalar[][]
     */
    private $m_attributes=[];
    /**
     * @var string[]
     */
    private $m_classes=[];
    /**
     * @var \Components\Ui_Panel[]
     */
    private $m_children=[];
    /**
     * @var \Components\Ui_Panel[][]
     */
    private $m_categories=[];
    /**
     * @var string[][]
     */
    private $m_errors=[];

    /**
     * @var string
     */
    private $m_id;
    /**
     * @var \Components\Ui_Panel
     */
    private $m_form;
    /**
     * @var scalar[]
     */
    private $m_formProperties=[];
    /**
     * @var \Components\Http_Session
     */
    private $m_session;
    /**
     * @var \Components\Ui_Template
     */
    private $m_templateEngine;
    /**
     * @var boolean
     */
    private $m_ajaxEnabled=true;
    /**
     * @var \Components\Ui_Panel[]
     */
    private $m_redraw=[];
    //-----


    /**
     * Redraw this panel.
     */
    protected function redraw()
    {
      $this->root->m_redraw[]=$this;
    }

    /**
     * @param string $name_
     *
     * @return scalar
     */
    protected function requestParam($name_)
    {
      if(false===array_key_exists("{$this->id()}-$name_", $_REQUEST))
        return null;

      return $_REQUEST["{$this->id()}-$name_"];
    }

    /**
     * @return void
     */
    protected function onRetrieveValue()
    {
      $params=$this->scriptlet->request->getParams();

      $id=$this->id();

      $value=null;
      $hasBeenSubmitted=false;

      if($value=$params->containsKey($id))
      {
        $value=$params->get($id);
        $hasBeenSubmitted=true;
      }
      else if($value=$params->containsKey("$id-value"))
      {
        $value=$params->get("$id-value");
        $hasBeenSubmitted=true;
      }

      if($hasBeenSubmitted)
      {
        if('null'===$value || null===$value)
        {
          $this->value=null;
        }
        else
        {
          if(null===$this->valueType)
          {
            $this->value=$value;
          }
          else
          {
            $valueType=$this->valueType;

            if($value instanceof $valueType)
              $this->value=$value;
            else
              $this->value=$valueType::valueOf($value);
          }
        }
      }

      foreach($this->panelProperties as $property)
      {
        if($params->containsKey("$id-$property"))
        {
          if(($value=$params->get("$id-$property")) && 'null'!==$value)
          {
            if(is_scalar($this->$property))
              $this->$property=Primitive::castTo(gettype($this->$property), $value);
            else
              $this->$property=json_decode($value);
          }
          else
          {
            $this->$property=null;
          }
        }
      }

      foreach($this->panelPropertiesToggle as $property)
      {
        if($params->containsKey("$id-$property"))
        {
          $value=$params->get("$id-$property");

          if(in_array($value, ['1', 'true', $property]))
            $this->$property=true;
          else
            $this->$property=false;
        }
      }
    }

    /**
     * @return \Components\Ui_Template
     */
    protected function templateEngine()
    {
      if(null===$this->m_templateEngine)
      {
        $this->m_templateEngine=new Ui_Template();
        $this->m_templateEngine->self=$this;
      }

      return $this->m_templateEngine;
    }

    /**
     * @param string $message_
     * @param mixed... $arg0_
     */
    protected function addError($message_, $arg0_=null/*, $arg1_, ..*/)
    {
      $args=func_get_args();
      $message=array_shift($args);

      array_push($this->m_errors, ['message'=>$message_, 'args'=>$args]);
    }

    /**
     * @return void
     */
    private function clearId()
    {
      foreach($this->m_children as $panel)
        $panel->reset();

      $this->m_id=null;
    }


    // [TEMPLATE] HELPERS
    /*private*/ function callback(array $params_=[], $callback_=null)
    {
      if(null===$callback_)
      {
        if(false===$this->ajaxEnabled && ($this instanceof Ui_Panel_Button_Submit))
          return '';

        $callback_=$this->callback;
      }

      $callback='null';
      if(isset($callback_[0]) && is_string($callback_[0]))
        $callback=json_encode([$callback_]);

      return sprintf('ui.submit(\'#%s\', %s, %s);',
        $this->id(),
        strtr($callback, '"', "'"),
        count($params_)?strtr(json_encode($params_), '"', "'"):'null'
      );
    }

    /*private*/ function errors($includeSubPanels_=true, array &$errors_=[])
    {
      if(0<count($this->m_errors))
        $errors_[$this->id()]=['panel'=>$this, 'errors'=>$this->m_errors];

      if($includeSubPanels_)
      {
        foreach($this->m_children as $panel)
          $panel->errors(true, $errors_);
      }

      return $errors_;
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
      $errors=$this->errors($includeSubPanels_);

      if(1>($count=$this->countErrors($includeSubPanels_)))
        return;

      // TODO Localize ...
      printf('
        <div class="ui_panel_errors">
          <div class="ui_panel_disclosure_header">
            <h2 class="title">%2$s (%3$s)</h2>
            <a href="javascript:void(0);" rel="%1$s-errors" class="ui_panel_disclosure_toggle%4$s">collapse</a>
          </div>',
          $this->id(),
          \html\escape(I18n::translate('ui/panel/errors/title')),
          $count,
          $count>$expandThreshold_?'':' expanded'
      );

      printf('<ul id="%1$s-errors" class="ui_panel_errors">', $this->id());

      foreach($errors as $panelId=>$value)
      {
        if($includeSubPanels_)
        {
          if(!$title=$value['panel']->title)
            $title=$value['panel']->name;

          printf('<li class="ui_panel_error_category"><label for="%1$s">%2$s</label><ul>',
            $value['panel']->id(),
            \html\escape($title)
          );
        }

        foreach($value['errors'] as $error)
        {
          printf('<li class="ui_panel_error"><label for="%1$s">%2$s</label></li>',
            $value['panel']->id(),
            \html\escape($error['message'])
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
