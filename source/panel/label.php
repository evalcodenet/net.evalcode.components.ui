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
      if($attributes=$this->getAttributesAsString())
        $attributes=String::pad($attributes, 1, ' ', String::PAD_LEFT);

      if(0<count($panels=$this->getPanels()))
      {
        $panel=reset($panels);

        if(!$id=$this->getValue())
          $id=$panel->getId();
        if(!$title=$this->getTitle())
          $title=$panel->getTitle();

        return sprintf('<label for="%1$s"%3$s>%2$s</label>%4$s', $id, $title, $attributes, $panel->fetch());
      }

      if($id=$this->getValue())
        return sprintf('<label for="%1$s"%3$s>%2$s</label>', $id, $this->getTitle(), $attributes);

      return sprintf('<label%2$s>%1$s</label>', $this->getTitle(), $attributes);
    }
    //--------------------------------------------------------------------------
  }
?>
