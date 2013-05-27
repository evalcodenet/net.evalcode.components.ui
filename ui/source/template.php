<?php


namespace Components;


  /**
   * Ui_Template
   *
   * @package net.evalcode.components
   * @subpackage ui
   *
   * @author evalcode.net
   */
  class Ui_Template implements Object
  {
    // ACCESSORS
    /**
     * Renders template for given path and returns rendered contents.
     *
     * @param string $templatePath_
     *
     * @return string
     */
    public function render($templatePath_)
    {
      ob_start();

      extract($this->m_members);
      include $templatePath_;

      return ob_get_clean();
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function __call($name_, array $params_=array())
    {
      if(array_key_exists($name_, $this->m_members))
      {
        if(is_callable($this->m_members[$name_]))
          return call_user_func_array($this->m_members[$name_], $params_);

        return $this->m_members[$name_];
      }
    }

    public function __get($name_)
    {
      if(array_key_exists($name_, $this->m_members))
        return $this->m_members[$name_];

      return null;
    }

    public function __set($name_, $value_)
    {
      $this->m_members[$name_]=$value_;
    }

    public function __isset($name_)
    {
      return array_key_exists($name_, $this->m_members);
    }

    public function __unset($name_)
    {
      if(array_key_exists($name_, $this->m_members))
      {
        unset($this->m_members[$name_]);

        return true;
      }

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
     * @see Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_members=array();
    //--------------------------------------------------------------------------
  }
?>
