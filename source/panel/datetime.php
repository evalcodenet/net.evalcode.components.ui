<?php


namespace Components;


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

      if(!$value=$this->getValue())
        $this->setValue(Date::now());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * (non-PHPdoc)
     * @see \Components\Ui_Panel::onRetrieveValue()
     */
    protected function onRetrieveValue()
    {
      $params=$this->scriptlet->request->getParams();

      if($params->containsKey("{$this->getId()}-date"))
        $date=$params->get("{$this->getId()}-date");
      else
        $date=Date::now()->formatLocalized('common/date/pattern/short');

      if($params->containsKey("{$this->getId()}-time"))
        $time=$params->get("{$this->getId()}-time");
      else
        $time=Date::now()->formatLocalized('common/time/pattern/short');

      $this->setValue(Date::parse("$date $time", Timezone::systemDefault()));
    }
    //--------------------------------------------------------------------------
  }
?>
