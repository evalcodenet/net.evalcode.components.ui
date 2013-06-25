<?php


namespace Components;


  /**
   * Ui_Panel_Session
   *
   * @package net.evalcode.components
   * @subpackage ui.panel
   *
   * @author evalcode.net
   */
  class Ui_Panel_Session extends Properties implements Value_String
  {
    // CONSTRUCTION
    public function __construct($namespace_)
    {
      parent::__construct();

      $this->m_namespace=$namespace_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return Ui_Panel_Session
     */
    public static function forNamespace($namespace_)
    {
      if(false===isset($_SESSION[$namespace_]))
        $_SESSION[$namespace_]=new static($namespace_);

      return $_SESSION[$namespace_];
    }

    public static function has($namespace_, $key_)
    {
      return isset(static::forNamespace($namespace_)->$key_);
    }

    public static function get($namespace_, $key_)
    {
      return static::forNamespace($namespace_)->$key_;
    }

    public static function set($namespace_, $key_, $value_)
    {
      return static::forNamespace($namespace_)->$key_=$value_;
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Value_String::valueOf()
     */
    public static function valueOf($value_)
    {
      return static::forNamespace($value_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see \Components\Value_String::value()
     */
    public function value()
    {
      return $this->m_namespace;
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Properties::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{namespace: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_namespace
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_namespace;
    //--------------------------------------------------------------------------
  }
?>
