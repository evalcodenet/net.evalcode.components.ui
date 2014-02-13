<?php


namespace Components;


  /**
   * Ui_Scriptlet_Image
   *
   * @package net.evalcode.components.ui
   * @subpackage scriptlet
   *
   * @author evalcode.net
   */
  class Ui_Scriptlet_Image extends Http_Scriptlet
  {
    // ACCESSORS/MUTATORS
    /**
     * @param \Components\Io_Image $image_
     *
     * @return \Components\Uri
     */
    public static function uri(Io_Image $image_)
    {
      $path=$image_->getPathAsString();
      $extension=$image_->getMimetype()->fileExtension();
      $key=md5($path);

      Cache::set($key, $path);

      return Uri::valueOf(Environment::uriComponents('ui', 'image', "$key.$extension"));
    }

    /**
     * @param string $image_
     *
     * @return \Components\Uri
     */
    public static function uriForPath($image_)
    {
      $extension=Io::fileExtension($image_);
      $key=md5($image_);

      Cache::set($key, $image_);

      return Environment::uriComponents('ui', 'image', "$key.$extension");
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @param \Components\Http_Scriptlet_Context $context_
     * @param \Components\Uri $uri_
     */
    public static function dispatch(Http_Scriptlet_Context $context_, Uri $uri_)
    {
      $key=$uri_->getFilename();

      if(!$path=Cache::get($key))
        throw new Http_Exception('ui/scriptlet/image', null, Http_Exception::NOT_FOUND);

      // TODO Cache headers.
      readfile($path);
    }
    //--------------------------------------------------------------------------
  }
?>
