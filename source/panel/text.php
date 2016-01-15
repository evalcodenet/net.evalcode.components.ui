<?php


namespace Components;


  /**
   * Ui_Panel_Text
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Text extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->tag='p';
      $this->template=__DIR__.'/text.tpl';

      $this->addClass('ui_panel_text');
    }
    //--------------------------------------------------------------------------
  }
?>
