<?php


namespace Components;


  /**
   * Ui_Panel_Label
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Label extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->addClass('ui_panel_label');
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Ui_Panel::render() render
     */
    public function render($panel_=null)
    {
      if(0<count($panels=$this->panels()))
      {
        $panel=reset($panels);

        if(!$id=$this->value())
          $id=$panel->id();
        if(!$this->title)
          $title=$panel->title;

        return sprintf('<label for="%1$s" %3$s>%2$s</label>%4$s', $id, $title, $this->attributes(), $panel->fetch());
      }

      if($id=$this->value())
        return sprintf('<label for="%1$s" %3$s>%2$s</label>', $id, $this->title, $this->attributes());

      return sprintf('<label %2$s>%1$s</label>', $this->title, $this->attributes());
    }
    //--------------------------------------------------------------------------
  }
?>
