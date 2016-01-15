<?php


namespace Components;


  /**
   * Ui_Panel_Slider
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Slider extends Ui_Panel
  {
    // PREDEFINED PROPERTIES
    const ORIENTATION_HORIZONTAL=1;
    const ORIENTATION_VERTICAL=2;
    //--------------------------------------------------------------------------


    // PROPERTIES
    // TODO Use ui/panel#callback.
    /**
     * Loads items lazily via defined static ajax callback when they become visible.
     *
     * @var callable
     */
    public $callbackLazy;
    /**
     * @var integer
     */
    public $orientation=self::ORIENTATION_HORIZONTAL;
    /**
     * @var integer
     */
    public $page=0;
    /**
     * Equalize dimensions of elements matching this list of jQuery selectors.
     *
     * @var string[]
     */
    public $selectorsEqualize=[];
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->template=__DIR__.'/slider.tpl';

      $this->panelType='ui/panel/slider';
      $this->panelProperties=['callbackLazy', 'orientation', 'page', 'selectorsEqualize'];

      $this->addClass('ui_panel_slider');
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Ui_Panel::add() add
     */
    public function add(Ui_Panel $panel_, $category_=null)
    {
      parent::add($panel_, $category_);

      $panel_->addClass('ui_panel_slider_item');
    }
    //--------------------------------------------------------------------------
  }
?>
