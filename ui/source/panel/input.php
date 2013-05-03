<?php


namespace Components;


/**
   * Ui_Panel_Input
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Input extends Ui_Panel
  {
    // PROPERTIES
    /**
     * @var Ui_Panel_Input_Type
     */
    public $type;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param Ui_Panel_Input_Type $type_
     *
     * @param string $name_
     * @param string $value_
     * @param string $title_
     *
     * @return Ui_Panel_Input
     */
    public static function forType(Ui_Panel_Input_Type $type_, $name_, $value_=null, $title_=null)
    {
      $panel=new self($name_, $value_, $title_);
      $panel->type=$type_->name();

      return $panel;
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->hasContainer(false);

      $this->setTemplate(__DIR__.'/input.tpl');
    }
    //--------------------------------------------------------------------------
  }


  /**
   * Ui_Panel_Input_Type
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   *
   * @method Ui_Panel_Input_Type HIDDEN
   * @method Ui_Panel_Input_Type TEXT
   */
  class Ui_Panel_Input_Type extends Enumeration
  {
    // PREDEFINED PROPERTIES
    const HIDDEN='hidden';
    const TEXT='text';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @see Components.Enumeration::values()
     */
    public static function values()
    {
      return self::$m_types;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @param string $name_
     * @param string $value_
     * @param string $title_
     *
     * @return \Components\Ui_Panel_Input
     */
    public function create($name_, $value_=null, $title_=null)
    {
      return Ui_Panel_Input::forType($this, $name_, $value_, $title_);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_types=array(
      'HIDDEN',
      'TEXT',
    );
    //--------------------------------------------------------------------------
  }
?>
