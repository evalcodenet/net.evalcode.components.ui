<?php


namespace Components;


  /**
   * Ui_Panel_Raw
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Raw extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->setTemplate(__DIR__.'/raw.tpl');
    }
    //--------------------------------------------------------------------------
  }
?>
