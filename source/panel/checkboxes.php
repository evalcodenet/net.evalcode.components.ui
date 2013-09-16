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
    // CONSTRUCTION
    public function __construct($name_, $value_=null, $title_=null, array $options_=array())
    {
      parent::__construct($name_, $value_, $title_);

      $this->m_options=$options_;
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    /**
     * @see \Components\Ui_Panel::init() \Components\Ui_Panel::init()
     */
    protected function init()
    {
      parent::init();

      $this->setTemplate(__DIR__.'/checkboxes.tpl');

      $this->params->options=$this->m_options;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Ui_Panel::getValue() \Components\Ui_Panel::getValue()
     */
    public function getValue()
    {
      return (array)parent::getValue();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|mixed
     */
    protected $m_options=array();
    //-----


    /**
     * @see \Components\Ui_Panel::onRetrieveValue() \Components\Ui_Panel::onRetrieveValue()
     */
    protected function onRetrieveValue()
    {
      if(false===$this->hasBeenSubmitted())
        return;

      $params=$this->scriptlet->request->getParams();

      $value=array();
      if($params->containsKey($this->getId()))
        $value=$params->get($this->getId());

      $this->setValue((array)$value);
    }
    //--------------------------------------------------------------------------
  }
?>
