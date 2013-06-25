<?php


namespace Components;


  /**
   * Ui_Panel_Select
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Select extends Ui_Panel
  {
    // PROPERTIES
    public $emptyOptionTitle=null;
    //--------------------------------------------------------------------------


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

      $this->tag=null;
      $this->setTemplate(__DIR__.'/select.tpl');

      $this->params->options=$this->m_options;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_options=array();
    //--------------------------------------------------------------------------
  }
?>
