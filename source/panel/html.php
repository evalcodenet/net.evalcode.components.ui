<?php


namespace Components;


  /**
   * Ui_Panel_Html
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Html extends Ui_Panel_Text
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->addStylesheet('ui/html');
      $this->addScript('ui/html', null, 'ui_panel_html_init("'.$this->getId().'");');
    }
    //--------------------------------------------------------------------------
  }
?>
