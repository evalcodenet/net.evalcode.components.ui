<?php


namespace Components;


  /**
   * Ui_Panel_Tabs
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Tabs extends Ui_Panel
  {
    // PREDEFINED PROPERTIES
    const HORIZONTAL=1;
    const VERTICAL=2;

    const NONE=1;
    const FIRST=2;
    const LAST=4;
    //--------------------------------------------------------------------------


    // PROPERTIES
    public $align=self::HORIZONTAL;
    public $default=self::FIRST;
    public $alwaysShowTabBar=false;
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->addStylesheet('ui/tabs');
      $this->addScript('ui/tabs', null, 'ui_panel_tabs_init("'.$this->getId().'");');

      $this->setTemplate(__DIR__.'/tabs.tpl');
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function getTabIndex(Ui_Panel $tab_)
    {
      $index=array_flip($this->m_index);

      if(false===isset($index[$tab_->getName()]))
        return null;

      return $index[$tab_->getName()];
    }

    public function isActiveTab(Ui_Panel $tab_)
    {
      if(null!==($value=$this->getValue()))
        return (int)$value===$this->getTabIndex($tab_);

      if(0<($this->default&self::FIRST) && $this->isFirstTab($tab_))
        return true;

      if(0<($this->default&self::LAST) && $this->isLastTab($tab_))
        return true;

      return false;
    }

    public function isActiveTabByIndex($index_)
    {
      if(null!==($value=$this->getValue()))
        return (int)$value===(int)$index_;

      if(0<($this->default&self::FIRST) && $this->m_index[(int)$index_]===reset($this->m_index))
        return true;

      if(0<($this->default&self::LAST) && $this->m_index[(int)$index_]===end($this->m_index))
        return true;

      return false;
    }

    public function isFirstTab(Ui_Panel $tab_)
    {
      return reset($this->m_index)===$tab_->getName();
    }

    public function getFirstTab()
    {
      return $this->{reset($this->m_index)};
    }

    public function isLastTab(Ui_Panel $tab_)
    {
      return end($this->m_index)===$tab_->getName();
    }

    public function getLastTab()
    {
      return $this->{end($this->m_index)};
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function add(UI_Panel $panel_)
    {
      parent::add($panel_);

      array_push($this->m_index, $panel_->getName());
    }

    public function remove(UI_Panel $panel_)
    {
      parent::remove($panel_);

      $index=array();
      while($next=array_shift($this->m_index))
      {
        if($next===$panel_->getName())
          continue;

        array_push($index, $next);
      }

      $this->m_index=$index;
    }

    public function display()
    {
      if(0<($this->align&self::HORIZONTAL))
        $this->addClass('align_horizontal');
      else if(0<($this->align&self::VERTICAL))
        $this->addClass('align_vertical');

      parent::display();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_index=array();
    //-----


    protected function initTemplateEngine(Ui_Template $engine_)
    {
      parent::initTemplateEngine($engine_);

      $engine_->tabCount=count($this->m_index);
      $engine_->tabIndex=array($this, 'getTabIndex');

      $engine_->isActiveTab=array($this, 'isActiveTab');
    }
    //--------------------------------------------------------------------------
  }
?>
