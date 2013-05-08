<?php


namespace Components;


  /**
   * Ui_Panel_Root
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   *
   * TODO Replace with 'Http_Scriptlet'
   */
  class Ui_Panel_Root extends Ui_Panel
  {
    public $scriptlet;
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->setTemplate(__DIR__.'/root.tpl');

      $this->addStylesheet('ui/common');

      $this->addScript('ui/jquery/jquery-1.9.1', '\'undefined\'==typeof(jQuery)');
      $this->addScript('ui/common');
    }
    //--------------------------------------------------------------------------
  }
?>
