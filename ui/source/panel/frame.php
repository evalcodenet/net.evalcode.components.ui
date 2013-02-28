<?php


  /**
   * Ui_Panel_Frame
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Frame extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->setTemplate(__DIR__.'/frame.tpl');
    }
    //--------------------------------------------------------------------------
  }
?>
