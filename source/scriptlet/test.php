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
  class Ui_Scriptlet_Test extends Ui_Scriptlet
  {
    // IMPLEMENTATION
    protected function init()
    {
      parent::init();

      $this->panel->add(new Ui_Scriptlet_Test_Panel('test'));
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
      $this->tabs->add(new Ui_Panel_Image('image', Io_Image::valueOf('/tmp/image.png'), 'Image'));
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
