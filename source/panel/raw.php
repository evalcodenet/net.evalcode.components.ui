<?php


namespace Components;


  /**
   * Ui_Panel_Raw
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Raw extends Ui_Panel
  {
    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Ui_Panel::render() render
     */
    public function render()
    {
      return $this->value();
    }
    //--------------------------------------------------------------------------
  }
?>
