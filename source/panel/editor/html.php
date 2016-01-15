<?php


namespace Components;


  /**
   * Ui_Panel_Editor_Html
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel.editor
   *
   * @author evalcode.net
   */
  class Ui_Panel_Editor_Html extends Ui_Panel_Editor_Text
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->panelType='ui/panel/editor/html';

      $this->addClass('ui_panel_editor_html');
    }
    //--------------------------------------------------------------------------
  }
?>
