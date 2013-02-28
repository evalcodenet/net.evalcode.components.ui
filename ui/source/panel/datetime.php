<?php


  /**
   * Ui_Panel_Datetime
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Datetime extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->addScript('ui/date', null, 'ui_panel_datetime_init(\''.$this->getId().'\');');

      $this->setTemplate(__DIR__.'/datetime.tpl');

      if(!$value=$this->getValue());
        $this->setValue(new DateTime());
    }
    //--------------------------------------------------------------------------
  }
?>
