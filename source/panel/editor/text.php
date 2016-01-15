<?php


namespace Components;


  /**
   * Ui_Panel_Editor_Text
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel.editor
   *
   * @author evalcode.net
   */
  class Ui_Panel_Editor_Text extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->tag=null;
      $this->template=__DIR__.'/text.tpl';

      $this->addClass('ui_panel_editor_text');
    }
    //--------------------------------------------------------------------------
  }
?>
