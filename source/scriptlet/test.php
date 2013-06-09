<?php


namespace Components;


  /**
   * Ui_Scriptlet_Test
   *
   * @package net.evalcode.components
   * @subpackage ui.scriptlet
   *
   * @author evalcode.net
   */
  class Ui_Scriptlet_Test extends Http_Scriptlet
  {
    // OVERRIDES/IMPLEMENTS
    public function get()
    {
      $root=new Ui_Panel_Root('root');
      $root->scriptlet=$this;

      $root->add(new Ui_Scriptlet_Test_Panel('test'));

      $root->display();
    }

    public function post()
    {
      return $this->get();
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
    * (non-PHPdoc)
    * @see Components\Object::equals()
    */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------
  }


  /**
   * Ui_Scriptlet_Test_Panel
   *
   * @package net.evalcode.components
   * @subpackage ui.scriptlet
   *
   * @author evalcode.net
   */
  class Ui_Scriptlet_Test_Panel extends Ui_Panel
  {
    // INITIALIZATION
    public function init()
    {
      parent::init();

      // FIXME (CSH) Re-implement fallback / root panel submission / find a better solution ...
      $this->form='test';

      $this->add(new Ui_Panel_Datetime('date'));

      $this->add(new Ui_Panel_Tabs('tabs'));

      $this->tabs->add(new Ui_Panel_Text('text', null, 'Text'));
      $this->tabs->add(new Ui_Panel_Upload_File('file', null, 'File'));
      $this->tabs->add(new Ui_Panel_Select('list', null, 'List', array('A', 'B', 'C')));
      $this->tabs->add(new Ui_Panel_Html('html', null, 'HTML'));

      $button=new Ui_Panel_Button('submit', null, 'Submit');
      $button->setCallback(array($this, 'onSubmit'));

      $this->add($button);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /*private*/ function onSubmit()
    {
      $this->date->setValue(Date::now());
      $this->redraw(true);
    }
    //--------------------------------------------------------------------------
  }
?>
