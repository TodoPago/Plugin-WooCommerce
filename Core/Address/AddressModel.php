<?php

namespace TodoPago\Core\Address;

use TodoPago\Core\AbstractClass\AbstractModel as AbstractModel;
use TodoPago\Core\Address\AddressDTO as AddressDTO;
use TodoPago\Core\Utils\CustomValidator as CustomValidator;

class AddressModel extends AbstractModel
{
    protected $city;
    protected $country;
    protected $postalcode;
    protected $phonenumber;
    protected $state;
    protected $street;
    protected $customValidator;

    public function __construct(AddressDTO $addressDTO = NULL)
    {
        $this->setCustomValidator(new CustomValidator('Address'));
        $this->setCity($addressDTO->getCity());
        $this->setCountry($addressDTO->getCountry());
        $this->setPhoneNumber($addressDTO->getPhoneNumber());
        $this->setPostalCode($addressDTO->getPostalCode());
        $this->setState($addressDTO->getState());
        $this->setStreet($addressDTO->getStreet());
    }

    public function validarDatos()
    {

    }

    public function getCity()
    {
        return $this->city;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getPostalCode()
    {
        return $this->postalcode;
    }

    public function getPhoneNumber()
    {
        return $this->phonenumber;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function setCity($city)
    {
        $city = $this->getCustomValidator()->validateString($city, 'CITY/CIUDAD');
        $this->city = $city;
    }

    public function setCountry($country)
    {
        $country = $this->getCustomValidator()->validateString($country, 'COUNTRY/PAIS');
        $this->country = $country;
    }

    public function setPostalCode($postalCode)
    {
        $postalCode = $this->getCustomValidator()->validateString($postalCode, 'POSTAL CODE');
        $this->postalcode = $postalCode;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $phoneNumber = $this->getCustomValidator()->validateString($phoneNumber, 'PHONE NUMBER');
        $this->phonenumber = $phoneNumber;
    }

    public function setState($state)
    {
        $state = $this->getCustomValidator()->validateString($state, 'STATE/PROVINCIA');
        $this->state = $state;
    }

    public function setStreet($street)
    {
        $street = $this->getCustomValidator()->validateString($street, 'STREET/CALLE');
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getCustomValidator()
    {
        return $this->customValidator;
    }

    /**
     * @param CustomValidator $customValidator
     */
    public function setCustomValidator(CustomValidator $customValidator)
    {
        $this->customValidator = $customValidator;
    }



}


?>