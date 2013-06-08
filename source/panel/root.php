<?php


namespace Components;


  /**
   * Ui_Panel_Root
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Root extends Ui_Panel
  {
    // PROPERTIES
    /**
     * @var \Components\Ui_Scriptlet
     */
    public $scriptlet;
    //--------------------------------------------------------------------------


    // INITIALIZATION
    public function __construct($name_)
    {
      parent::__construct($name_, null, null);

      $this->hasContainer(false);

      $this->setTemplate(__DIR__.'/root.tpl');

      $this->addStylesheet('ui/common');

      $this->addScript('ui/common');
      $this->addScript('ui/jquery/jquery-1.9.1', '\'undefined\'==typeof(jQuery)');
    }
    //--------------------------------------------------------------------------
  }
?>
