<?php

namespace TodoPago\Core\Customer;

use TodoPago\Core\AbstractClass\AbstractModel as AbstractModel;
use TodoPago\Core\Customer\CustomerDTO as CustomerDTO;
use TodoPago\Core\Utils\CustomValidator;

class CustomerModel extends AbstractModel
{
    protected $id;
    protected $username;
    protected $user_pass;
    protected $first_name;
    protected $last_name;
    protected $user_email;
    protected $user_registered;
    protected $ip_address;
    protected $customValidator;

    public function __construct(CustomerDTO $customerDTO = NULL)
    {
        $this->setCustomValidator(new CustomValidator('CUSTOMER'));
        if ($customerDTO)
            foreach ($customerDTO as $parametro => $valore) {
                $this->{"set$parametro"}($valore);
            }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserName()
    {
        return $this->username;
    }

    public function getUserPass()
    {
        return $this->user_pass;
    }

    public function getUserEmail()
    {
        return $this->user_email;
    }

    public function getUserRegistered()
    {
        return $this->user_registered;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function setFirstName($first_name)
    {
        $first_name = $this->getCustomValidator()->validateString($first_name, 'FIRST NAME');
        $this->first_name = $first_name;
    }

    public function setLastName($last_name)
    {
        $last_name = $this->getCustomValidator()->validateString($last_name, 'LAST NAME');
        $this->last_name = $last_name;
    }

    public function setId($id)
    {
        $id = $this->getCustomValidator()->validateString($id, 'Customer ID');
        $this->id = $id;
    }

    public function setUserName($username)
    {
        $username = $this->getCustomValidator()->validateString($username, 'USER NAME');
        $this->username = $username;
    }

    public function setUserPass($user_pass)
    {
        $user_pass = $this->getCustomValidator()->validateString($user_pass, 'USER PASS');
        $this->user_pass = $user_pass;
    }

    public function setUserEmail($user_email)
    {
        $user_email = $this->getCustomValidator()->validateString($user_email, 'USER EMAIL');
        $this->user_email = $user_email;
    }

    public function setUserRegistered($user_registered)
    {
        $user_registered = $this->getCustomValidator()->validateString($user_registered, 'USER REGISTERED');
        $this->user_registered = $user_registered;
    }

    public function getIpAddress()
    {
        return $this->ip_address;
    }

    public function setIpAddress($ip_address)
    {
        $ip_address = $this->getCustomValidator()->validateString($ip_address, 'IP ADDRESS');
        $this->ip_address = $ip_address;
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