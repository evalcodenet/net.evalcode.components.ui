<?php


  /**
   * Ui_Panel_Text
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Text extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->setTemplate(__DIR__.'/text.tpl');
    }
    //--------------------------------------------------------------------------
  }
?>