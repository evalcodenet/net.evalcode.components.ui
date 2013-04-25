<?php


namespace Components;


  /**
   * Ui_Panel_Root
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   *
   * TODO Replace with 'Http_Scriptlet'
   */
  class Ui_Panel_Root extends Ui_Panel
  {
    // PROPERTIES
    public static $ajaxEnabled=true;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($standalone_=true, $title_=null)
    {
      parent::__construct('ui-panel', null, $title_);

      $this->m_standalone=$standalone_;
      $this->hasContainer(!$standalone_);
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      if($this->m_standalone)
        $this->setTemplate(__DIR__.'/root/standalone.tpl');
      else
        $this->setTemplate(__DIR__.'/root/embedded.tpl');

      $this->addStylesheet('ui/common');

      // TODO Handle in ui_panel_script_add
      if(!$this->m_standalone)
        $this->addScript('ui/jquery/jquery-1.9.1', '\'undefined\'==typeof(jQuery)');

      $this->addScript('ui/common');
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_standalone=true;
    //-----


    protected function initializeTemplateEngine(Ui_Template $engine_)
    {
      parent::initializeTemplateEngine($engine_);

      $engine_->printReferences=array($this, 'printReferences');
    }

    /*private*/ function printReferences()
    {
      foreach($this->getReferences() as $reference)
      {
        echo "  <$reference[0] ";

        $attributes=array();
        foreach($reference[2] as $attribute=>$value)
          $attributes[]="$attribute=\"$value\"";

        echo implode(' ', $attributes);

        if(null===$reference[1])
          echo "/>\n";
        else
          echo ">$reference[1]</$reference[0]>\n";
      }
    }
    //--------------------------------------------------------------------------
  }
?>
