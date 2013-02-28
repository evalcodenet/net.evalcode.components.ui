<?php


  /**
   * Ui_Panel_Session
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Session extends Properties
  {
    // STATIC ACCESSORS
    /**
     * @return Ui_Panel_Session
     */
    public static function forNamespace($namespace_)
    {
      if(false===isset($_SESSION[$namespace_]))
        $_SESSION[$namespace_]=new self();

      return $_SESSION[$namespace_];
    }

    public static function has($namespace_, $key_)
    {
      return isset(self::forNamespace($namespace_)->$key_);
    }

    public static function get($namespace_, $key_)
    {
      return self::forNamespace($namespace_)->$key_;
    }

    public static function set($namespace_, $key_, $value_)
    {
      return self::forNamespace($namespace_)->$key_=$value_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    public function clear()
    {
      $this->m_params=array();
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------
  }
?>
