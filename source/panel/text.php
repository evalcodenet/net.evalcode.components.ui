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

      $this->tag=null;
      $this->setTemplate(__DIR__.'/text.tpl');
    }
    //--------------------------------------------------------------------------
  }
?>
