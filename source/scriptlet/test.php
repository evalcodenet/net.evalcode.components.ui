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
  class Ui_Scriptlet_Test extends Http_Scriptlet
  {
    // OVERRIDES/IMPLEMENTS
    public function get()
    {
      $root=new Ui_Panel_Root('ui-panel');
      $root->scriptlet=$this;

      $root->add(new Ui_Panel_Datetime('date'));

      $engine=new Ui_Template();
      $engine->panel=$root;

      echo $engine->render(dirname(__DIR__).'/scriptlet.tpl');
    }

    public function post()
    {
      return $this->get();
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
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
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
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
  }
?>
