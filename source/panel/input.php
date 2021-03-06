<?php


namespace Components;


  /**
   * Ui_Panel_Input
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
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

      $this->tag=null;
      $this->template=__DIR__.'/input.tpl';

      $this->addClass('ui_panel_input');
    }
    //--------------------------------------------------------------------------
  }


  /**
   * Ui_Panel_Input_Type
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   *
   * @method \Components\Ui_Panel_Input_Type HIDDEN
   * @method \Components\Ui_Panel_Input_Type TEXT
   */
  class Ui_Panel_Input_Type extends Enumeration
  {
    // PREDEFINED PROPERTIES
    const HIDDEN='hidden';
    const IMAGE='image';
    const TEXT='text';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @see Components\Enumeration::values() Components\Enumeration::values()
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
      $panel=new Ui_Panel_Input($name_, $value_, $title_);
      $panel->type=$this;

      return $panel;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_types=[
      'HIDDEN',
      'IMAGE',
      'TEXT'
    ];
    //--------------------------------------------------------------------------
  }
?>
