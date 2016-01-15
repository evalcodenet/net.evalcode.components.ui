<?php


namespace Components;


  /**
   * Ui_Panel_Image
   *
   * @api
   * @package net.evalcode.components.ui
   * @subpackage panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Image extends Ui_Panel
  {
    // PREDEFINED PROPERTIES
    const VALUE_TYPE='\Components\Io_Image';
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var boolean
     */
    public $embedded=false;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, Io_Image $value_=null, $title_=null)
    {
      parent::__construct($name_, $value_, $title_);

      $this->valueType=self::VALUE_TYPE;
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->tag=null;
      $this->template=__DIR__.'/image.tpl';

      $this->addClass('ui_panel_image');
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function render($panel_=null)
    {
      /* @var $value \Components\Io_Image */
      if(($value=$this->value()) && $value->exists())
      {
        $image=$value;

        $height=(int)$this->attribute('height');
        $width=(int)$this->attribute('width');

        if($width || $height)
        {
          $dimensions=$value->getDimensions();

          if(!$width)
            $width=round($dimensions->x/($dimensions->y/$height));
          else if(!$height)
            $height=round($dimensions->y/($dimensions->x/$width));

          if($this->embedded)
          {
            $image=Io_Image::valueOf(Environment::pathResource('ui', 'image', 'tmp', $width, $height, $value->getName()));

            if(false===$image->exists())
            {
              $value->scale(Point::of($width, $height));
              $value->saveAs($image);
            }
          }
        }

        $this->attribute('width', $width);
        $this->attribute('height', $height);

        if($this->embedded)
        {
          $this->attribute('src', sprintf('data:%s;base64,%s',
            $image->getMimetype(), $image->getBase64()
          ));
        }
        else
        {
          $this->attribute('src', (string)Media_Scriptlet_Engine::imageUri(
            'thumbnail', $image->getPath(), $width, $height
          ));
        }
      }
      else if(null!==$value && Debug::active())
      {
        Log::debug('components/ui/panel/image', 'Image for given location does not exist [%s].', $value);
      }

      return parent::render();
    }
    //--------------------------------------------------------------------------
  }
?>
