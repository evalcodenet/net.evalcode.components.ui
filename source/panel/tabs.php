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
    const ORIENTATION_HORIZONTAL=1;
    const ORIENTATION_VERTICAL=2;
    //--------------------------------------------------------------------------


    // PROPERTIES
    public $orientation=self::ORIENTATION_HORIZONTAL;
    public $alwaysShowTabBar=false;
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->template=__DIR__.'/tabs.tpl';

      $this->panelType='ui/panel/tabs';
      $this->panelProperties=['orientation'];

      $this->valueDefault=0;

      $this->addClass('ui_panel_tabs');
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @param \Components\Ui_Panel $tab_
     *
     * @return integer
     */
    public function index(Ui_Panel $tab_)
    {
      $index=array_flip($this->m_index);

      if(false===isset($index[$tab_->name]))
        return -1;

      return $index[$tab_->name];
    }

    /**
     * @param \Components\Ui_Panel $tab_
     *
     * @return boolean
     */
    public function isActive(Ui_Panel $tab_)
    {
      return $this->isActiveByIndex($this->index($tab_));
    }

    /**
     * @param integer $index_
     *
     * @return boolean
     */
    public function isActiveByIndex($index_)
    {
      return (int)$this->value()===(int)$index_;
    }

    /**
     * @param \Components\Ui_Panel $tab_
     *
     * @return boolean
     */
    public function isFirst(Ui_Panel $tab_)
    {
      if(false===isset($this->m_index[0]))
        return false;

      return $this->m_index[0]===$tab_->name;
    }

    /**
     * @param \Components\Ui_Panel $tab_
     *
     * @return boolean
     */
    public function isLast(Ui_Panel $tab_)
    {
      if(false===isset($this->m_index[$this->m_count-1]))
        return false;

      return $this->m_index[$this->m_count-1]===$tab_->name;
    }

    /**
     * @return \Components\Ui_Panel
     */
    public function first()
    {
      if(false===isset($this->m_index[0]))
        return null;

      return $this->{$this->m_index[0]};
    }

    /**
     * @return \Components\Ui_Panel
     */
    public function last()
    {
      if(false===isset($this->m_index[$this->m_count-1]))
        return null;

      return $this->{$this->m_index[$this->m_count-1]};
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Ui_Panel::add() add
     */
    public function add(Ui_Panel $panel_, $category_=null)
    {
      parent::add($panel_, $category_);

      array_push($this->m_index, $panel_->name);

      $this->m_count++;
    }

    /**
     * @see \Components\Ui_Panel::remove() remove
     */
    public function remove(UI_Panel $panel_)
    {
      parent::remove($panel_);

      $index=[];

      while($next=array_shift($this->m_index))
      {
        if($next===$panel_->name)
          continue;

        array_push($index, $next);
      }

      $this->m_index=$index;

      $this->m_count--;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string[]
     */
    private $m_index=[];
    /**
     * @var integer
     */
    private $m_count=0;
    //--------------------------------------------------------------------------
  }
?>
