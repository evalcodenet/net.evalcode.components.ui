<?php


namespace Components;


  /**
   * Ui_Panel_Checkboxes
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Checkboxes extends Ui_Panel
  {
    // PROPERTIES
    public $options=[];
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, $value_=null, $title_=null, array $options_=[])
    {
      parent::__construct($name_, $value_, $title_);

      $this->options=$options_;
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    /**
     * @see \Components\Ui_Panel::init() init
     */
    protected function init()
    {
      parent::init();

      $this->template=__DIR__.'/checkboxes.tpl';

      $this->addClass('ui_panel_checkboxes');
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Ui_Panel::value() value
     */
    public function value($value_=null)
    {
      return (array)parent::value($value_);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @see \Components\Ui_Panel::onRetrieveValue() onRetrieveValue
     */
    protected function onRetrieveValue()
    {
      if(false===$this->hasBeenSubmitted())
        return;

      $params=$this->scriptlet->request->getParams();

      $value=[];
      if($params->containsKey($this->id()))
        $value=$params->get($this->id());

      $this->value((array)$value);
    }
    //--------------------------------------------------------------------------
  }
?>
