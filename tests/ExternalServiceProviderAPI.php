<?php

namespace tests\eduluz1976;

class ExternalServiceProviderAPI
{
    public static function checkEmail($email)
    {
        return 'a' . $email . '.br';
    }
}
