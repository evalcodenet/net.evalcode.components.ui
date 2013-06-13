<?php


namespace Components;


  /**
   * Ui_Panel_Label
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Label extends Ui_Panel
  {
    // OVERRIDES/IMPLEMENTS
    public function render()
    {
      if(0<count($panels=$this->getPanels()))
      {
        $panel=reset($panels);

        if(!$id=$this->getValue())
          $id=$panel->getId();
        if(!$title=$this->getTitle())
          $title=$panel->getTitle();

        return sprintf('<label for="%1$s">%2$s</label>%3$s', $id, $title, $panel->fetch());
      }

      if($id=$this->getValue())
        return sprintf('<label for="%1$s">%2$s</label>', $id, $this->getTitle());

      return sprintf('<label>%1$s</label>', $this->getTitle());
    }
    //--------------------------------------------------------------------------
  }
?>
