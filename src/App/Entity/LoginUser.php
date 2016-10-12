<?php

namespace App\Entity;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ArraySerializable")
 * @Annotation\Name("LoginUser")
 */
class LoginUser implements AuthUserInterface
{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Validator({"name":"NotEmpty"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Username:"})
     *
     * @var int
     */
    private $username;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Validator({"name":"NotEmpty"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Options({"label":"Password:"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     *
     * @var string
     */
    private $password;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit"})
     *
     * @var string
     */
    private $submit;

    /**
     * @param array $data
     */
    public function populate($data)
    {
        $this->username = $data['username'];
        $this->password = $data['password'];
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}