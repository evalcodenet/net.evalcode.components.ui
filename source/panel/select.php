<?php


namespace Components;


  /**
   * Ui_Panel_Select
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Select extends Ui_Panel
  {
    // PROPERTIES
    /**
     * @var scalar[]
     */
    public $options=[];
    /**
     * @var string
     */
    public $emptyOptionTitle;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, $value_=null, $title_=null, array $options_=[])
    {
      parent::__construct($name_, $value_, $title_);

      $this->options=$options_;
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->tag=null;
      $this->template=__DIR__.'/select.tpl';

      $this->addClass('ui_panel_select');
    }
    //--------------------------------------------------------------------------
  }
?>
