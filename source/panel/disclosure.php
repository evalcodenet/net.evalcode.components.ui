<?php


namespace Components;


  /**
   * Ui_Panel_Disclosure
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Disclosure extends Ui_Panel
  {
    // PROPERTIES
    public $open=false;
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->tag=null;
      $this->template=__DIR__.'/disclosure.tpl';

      $this->panelType='ui/panel/disclosure';
      $this->panelPropertiesToggle=['open'];

      $this->addClass('ui_panel_disclosure');
    }
    //--------------------------------------------------------------------------
  }
?>
