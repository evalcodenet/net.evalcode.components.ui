<?php


namespace Components;


  /**
   * Ui_Panel_Root
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  // FIXME Remove this panel / merge into ui/scriptlet
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

      $this->tag=null;

      $this->setTemplate(__DIR__.'/root.tpl');

      $this->addStylesheet('ui/common');

      $this->addScript('ui/common');
      $this->addScript('ui/jquery/jquery-1.9.1', '\'undefined\'==typeof(jQuery)');
    }
    //--------------------------------------------------------------------------
  }
?>
