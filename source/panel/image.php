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
    const TYPE_VALUE='\Components\Io_Image';
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

      $this->typeValue=self::TYPE_VALUE;
    }
    //--------------------------------------------------------------------------


    // INITIALIZATION
    protected function init()
    {
      parent::init();

      $this->tag=null;

      $this->setTemplate(__DIR__.'/image.tpl');
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function render()
    {
      /* @var $value \Components\Io_Image */
      if(($value=$this->getValue()) && $value->exists())
      {
        $height=(int)$this->getAttribute('height');
        $width=(int)$this->getAttribute('width');

        if($width || $height)
        {
          $dimensions=$value->getDimensions();

          if(!$width)
            $width=round($dimensions->x/($dimensions->y/$height));
          else if(!$height)
            $height=round($dimensions->y/($dimensions->x/$width));

          $this->setAttribute('width', $width);
          $this->setAttribute('height', $height);

          $image=Io_Image::valueOf(Environment::pathResource('ui', 'image', 'tmp', $width, $height, $value->getName()));

          if(false===$image->exists())
          {
            $value->scale(Point::of($width, $height));
            $value->saveAs($image);
          }
        }
        else
        {
          $image=$value;
        }

        if($this->embedded)
        {
          $this->setAttribute('src', sprintf('data:%s;base64,%s',
            $image->getMimetype(),
            $image->getBase64()
          ));
        }
        else
        {
          $pathResource=Io_Path::valueOf(Environment::pathResource());

          if($pathResource->isParentOf($image->getPath()))
            $this->setAttribute('src', Environment::uriResource($pathResource->getRelativePath($image->getPath())));
          else
            $this->setAttribute('src', (string)Ui_Scriptlet_Image::uri($image));
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
