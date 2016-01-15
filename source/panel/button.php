<?php


namespace Components;


  /**
   * Ui_Panel_Button
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
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

      $this->tag=null;
      $this->template=__DIR__.'/button.tpl';

      $this->attribute('type', self::TYPE_PLAIN);
      $this->attribute('value', $this->title);

      $this->addClass('ui_panel_button');
    }
    //--------------------------------------------------------------------------
  }


  /**
   * Ui_Panel_Button_Submit
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Button_Submit extends Ui_Panel_Button
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->attribute('type', self::TYPE_SUBMIT);

      $this->addClass('ui_panel_button_submit');
    }
    //--------------------------------------------------------------------------
  }
?>
