<?php


namespace Components;


  /**
   * Ui_Panel_Pager
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  // TODO Implement paging of sub-panels and/or pageable by reference.
  class Ui_Panel_Pager extends Ui_Panel
  {
    // PROPERTIES
    /**
     * @var integer
     */
    public $page=0;
    /**
     * @var \Components\Ui_Panel
     */
    public $pageable;
    /**
     * @var boolean
     */
    public $swipe=true;
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->tag=null;
      $this->template=__DIR__.'/pager.tpl';

      $this->panelType='ui/panel/pager';
      $this->panelPropertiesToggle=['page', 'pageable', 'swipe'];

      $this->addClass('ui_panel_pager');
    }
    //--------------------------------------------------------------------------
  }
?>
