<?php


namespace Components;


  /**
   * Ui_Panel_Reference_Js
   *
   * @api
   * @package net.evalcode.components.ui
   *
   * @author evalcode.net
   */
  class Ui_Panel_Reference_Js implements Object, Serializable_Json
  {
    // PREDEFINED PROPERTIES
    const REF_PLAIN=1;
    const REF_CALLBACK=2;
    const REF_TRIGGER_ON_SUBMIT=4;
    const REF_TRIGGER_ON_RESPONSE=8;
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var integer
     */
    public $type;
    /**
     * @var string
     */
    public $method;
    /**
     * @var scalar[]
     */
    public $params=[];
    //--------------------------------------------------------------------------


    // CONTRUCTION
    public function __construct($type_, $method_, array $params_=[])
    {
      $this->type=$type_;
      $this->method=$method_;
      $this->params=$params_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $sourceJs_
     *
     * @return \Components\Ui_Panel_Reference_Js
     */
    public static function plain($sourceJs_)
    {
      return new static(self::REF_PLAIN, $sourceJs_);
    }

    /**
     * @param string $method_
     * @param scalar[] $params_
     *
     * @return \Components\Ui_Panel_Reference_Js
     */
    public static function callback($method_, array $params_=[])
    {
      return new static(self::REF_CALLBACK, $method_, $params_);
    }

    /**
     * @param string $method_
     * @param scalar[] $params_
     *
     * @return \Components\Ui_Panel_Reference_Js
     */
    public static function onSubmit($method_, array $params_=[])
    {
      return new static(self::REF_TRIGGER_ON_SUBMIT, $method_, $params_);
    }

    /**
     * @param string $method_
     * @param scalar[] $params_
     *
     * @return \Components\Ui_Panel_Reference_Js
     */
    public static function onResponse($method_, array $params_=[])
    {
      return new static(self::REF_TRIGGER_ON_RESPONSE, $method_, $params_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Serializable_Json::serializeJson() serializeJson
     */
    public function serializeJson()
    {
      return json_encode($this, JSON_FORCE_OBJECT);
    }

    /**
     * @see \Components\Serializable_Json::unserializeJson() unserializeJson
     */
    public function unserializeJson($json_)
    {
      $object=json_decode($json_);

      return new static($object->type, $object->method, $object->params);
    }

    /**
     * @see \Components\Serializable::serialVersionUid() serialVersionUid
     */
    public function serialVersionUid()
    {
      return 1;
    }

    /**
     * @see Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s{type: %s, method: %s, params: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->type,
        $this->method,
        Arrays::toString($this->params)
      );
    }
    //--------------------------------------------------------------------------
  }
?>
