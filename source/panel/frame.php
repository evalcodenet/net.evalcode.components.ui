<?php


namespace Components;


  /**
   * Ui_Panel_Frame
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Frame extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->template=__DIR__.'/frame.tpl';

      $this->addClass('ui_panel_frame');
    }
    //--------------------------------------------------------------------------
  }
?>
