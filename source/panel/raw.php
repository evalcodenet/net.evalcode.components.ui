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
    // OVERRIDES/IMPLEMENTS
    /**     * @see \Components\Ui_Panel::render() \Components\Ui_Panel::render()
     */
    public function render()
    {
      return $this->getValue();
    }
    //--------------------------------------------------------------------------
  }
?>
