<?php


namespace Components;


  /**
   * Ui_Panel_Datetime
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Datetime extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->template=__DIR__.'/datetime.tpl';

      $this->addClass('ui_panel_datetime');

      if(!$value=$this->value())
        $this->value(Date::now());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @see \Components\Ui_Panel::onRetrieveValue() onRetrieveValue
     */
    protected function onRetrieveValue()
    {
      $params=$this->scriptlet->request->getParams();

      $id=$this->id();

      if($params->containsKey("$id-date"))
        $date=$params->get("$id-date");
      else
        $date=Date::now()->formatLocalized('common/date/pattern/short');

      if($params->containsKey("$id-time"))
        $time=$params->get("$id-time");
      else
        $time=Date::now()->formatLocalized('common/time/pattern/short');

      $this->value(Date::parse("$date $time", Timezone::systemDefault()));
    }
    //--------------------------------------------------------------------------
  }
?>
