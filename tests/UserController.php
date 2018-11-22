<?php
namespace tests\eduluz1976;

class UserController {

    protected $email;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }




    public function addNewUser() {

        $this->setEmail( Request::get('email'));


    }


}
