<?php


  /**
   * Ui_Panel_Button
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Button extends Ui_Panel
  {
    // PREDEFINED PROPERTIES
    const TYPE_PLAIN='button';
    const TYPE_SUBMIT='submit';
    const TYPE_RESET='reset';
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->hasContainer(false);

      $this->setTemplate(__DIR__.'/button.tpl');

      $this->setAttribute('type', self::TYPE_PLAIN);
      $this->setAttribute('value', $this->getTitle());
    }
    //--------------------------------------------------------------------------
  }


  /**
   * Ui_Panel_Button_Submit
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Button_Submit extends Ui_Panel_Button
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->setAttribute('type', self::TYPE_SUBMIT);
    }
    //--------------------------------------------------------------------------
  }
?>
