<?php


namespace Components;


  /**
   * Ui_Template
   *
   * @api
   * @package net.evalcode.components.ui
   *
   * @author evalcode.net
   */
  class Ui_Template implements Object
  {
    // ACCESSORS/MUTATORS
    /**
     * Displays template for given path.
     *
     * @param string $templatePath_
     */
    public function display($templatePath_)
    {
      extract($this->m_members);

      if(isset($this->m_members['self']))
        $self=$this->m_members['self'];

      include $templatePath_;
    }

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

      if(isset($this->m_members['self']))
        $self=$this->m_members['self'];

      include $templatePath_;

      return ob_get_clean();
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function __call($name_, array $params_=[])
    {
      if(false===isset($this->m_members[$name_]))
        return null;

      if(is_callable($this->m_members[$name_]))
        return call_user_func_array($this->m_members[$name_], $params_);

      return $this->m_members[$name_];
    }

    public function __get($name_)
    {
      if(false===isset($this->m_members[$name_]))
        return null;

      return $this->m_members[$name_];
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
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_members=[];
    //--------------------------------------------------------------------------
  }
?>
