<?php


namespace Components;


  /**
   * Ui_Panel_Checkboxes
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
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
    protected function init()
    {
      parent::init();

      $this->setTemplate(__DIR__.'/checkboxes.tpl');

      $this->params->options=$this->m_options;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function getValue()
    {
      if(is_array($value=parent::getValue()))
        return $value;

      return array();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_options=array();
    //--------------------------------------------------------------------------
  }
?>
