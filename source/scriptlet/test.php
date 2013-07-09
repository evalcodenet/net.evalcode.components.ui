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

            I18n::push(I18n_Locale::en());
      foreach(I18n_Country::CN()->childNames() as $regionName)
      {
        $region=I18n_Country::CN()->$regionName;

        foreach($region->childNames() as $cityName)
        {
          $city=$region->$cityName;

          foreach($city->childNames() as $districtName)
          {
            I18n::push(I18n_Locale::zh());
            var_dump((string)$city->$districtName->title());
            I18n::pop();
            var_dump((string)$city->$districtName->title());
          }
        }
      }

      return;

      // FIXME (CSH) Re-implement fallback / root panel submission / find a better solution ...
      $this->form='test';

      $this->add(new Ui_Panel_Datetime('date'));
      $this->add(new Ui_Panel_Label('labela', null, I18n_Script::Hans()->transformToLatn('Google.com.hk 使用下列语言： 中文（繁體） English')));
      $this->add(new Ui_Panel_Label('labelb', null, I18n_Script::Hans()->transformToAscii('Google.com.hk 使用下列语言： 中文（繁體） English')));
      $this->add(new Ui_Panel_Label('labelc', null, I18n_Script::Hans()->transformToLatn('Google.com.hk 使用下列语言： 中文（繁體） English')));
      $this->add(new Ui_Panel_Label('labeld', null, I18n_Script::Hans()->transformToAscii('Google.com.hk 使用下列语言： 中文（繁體） English')));
      $this->add(new Ui_Panel_Label('labele', null, I18n_Script::Hans()->transformToLatn('Google.com.hk 使用下列语言： 中文（繁體） English')));
      $this->add(new Ui_Panel_Label('labelf', null, I18n_Script::Hans()->transformToAscii('Google.com.hk 使用下列语言： 中文（繁體） English')));

      $this->add(new Ui_Panel_Tabs('tabs'));

      $this->tabs->add(new Ui_Panel_Text('text', null, 'Text'));
      $this->tabs->add(new Ui_Panel_Image('image', Io_Image::valueOf(Environment::pathApplication().'/favicon.ico'), 'Image'));
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
