<?php


namespace Components;


  /**
   * Ui_Scriptlet_Test
   *
   * @package net.evalcode.components.ui
   * @subpackage scriptlet
   *
   * @author evalcode.net
   */
  class Ui_Scriptlet_Test extends Ui_Scriptlet
  {
    // IMPLEMENTATION
    protected function init()
    {
      parent::init();

      $this->panel->add(new Ui_Scriptlet_Test_Panel('foo'));
    }
    //--------------------------------------------------------------------------
  }


  /**
   * Ui_Scriptlet_Test_Panel
   *
   * @package net.evalcode.components.ui
   * @subpackage scriptlet
   *
   * @author evalcode.net
   */
  class Ui_Scriptlet_Test_Panel extends Ui_Panel
  {
    // INITIALIZATION
    protected function init()
    {
      parent::init();

      // FIXME (CSH) Re-implement fallback / root panel submission / find a better solution ...
//       $this->form('POST', null, Io_Mimetype::APPLICATION_FORM_URLENCODED(), 'ISO-8859-15');
     $this->form();
//       $this->ajaxEnabled=false;

      $this->add(new Ui_Panel_Label('labela', null, 'Google.com.hk 使用下列语言： 中文（繁體） English'));
      $this->add(new Ui_Panel_Label('labelb', null, I18n_Script::Hans()->transformToLatn('Google.com.hk 使用下列语言： 中文（繁體） English')));
      $this->add(new Ui_Panel_Label('labelc', null, I18n_Script::Hans()->transformToAscii('Google.com.hk 使用下列语言： 中文（繁體） English')));
      $this->add(new Ui_Panel_Label('labeld', null, String::toLowercaseUrlIdentifier(I18n_Script::Hans()->transformToAscii('Google.com.hk 使用下列语言： 中文（繁體） English'))));

      $this->add(new Ui_Panel_Tabs('tabs'));

      $this->tabs->add(new Ui_Panel_Disclosure('disclosure', null, 'Disclosure'));

      $this->tabs->disclosure->add(new Ui_Panel_Datetime('date'));

      $this->tabs->add(new Ui_Panel_Editor_Text('text', null, 'Text'));

      $this->tabs->add(new Ui_Panel_Image('image', Io_Image::valueOf(__DIR__.'/test.jpg'), 'Image'));
      $this->tabs->image->attribute('width', 64);
      $this->tabs->image->embedded=true;

      $this->tabs->add(new Ui_Panel_Upload_File('file', null, 'File'));
      $this->tabs->add(new Ui_Panel_Select('list', null, 'List', ['A', 'B', 'C']));
      $this->tabs->add(new Ui_Panel_Editor_Html('html', null, 'HTML'));

      $button=new Ui_Panel_Button_Submit('submit', null, 'Submit');
      $button->callback=[$this, 'onSubmit'];

      $this->add($button);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /*private*/ function onSubmit()
    {
      /* @var $value \Components\Date */
      $value=$this->tabs->disclosure->date->value();
      $this->tabs->disclosure->date->value($value->after(Time::forMinutes(100)));

      $this->tabs->disclosure->open=!$this->tabs->disclosure->open;

      $this->tabs->redraw();
    }
    //--------------------------------------------------------------------------
  }
?>
