<?php
/**
 * Created by PhpStorm.
 * User: eduardoluz
 * Date: 2018-11-21
 * Time: 11:53 PM
 */

namespace unit;

use eduluz1976\pervasive\Builder;
use eduluz1976\pervasive\Mock;
use tests\eduluz1976\ExternalServiceProviderAPI;
use tests\eduluz1976\Request;
use tests\eduluz1976\UserController;

class BuilderTest extends \PHPUnit\Framework\TestCase
{

    public function testAddPreSuccess()
    {

        Builder::design(UserController::class)
            ->_()
            ->addPreFunction(
                'addNewUser',
                function (Mock $container = null) {

                    $email = Request::get('email');

                    $newEmail = ExternalServiceProviderAPI::checkEmail($email);

                    if ($newEmail) {
                        Request::set('email', $newEmail);
                    }

                }
            );

        $testEmail = 'my_user@domain.com';

        Request::set('email',$testEmail);

        $controller = Builder::build(UserController::class);

        $controller->addNewUser();

        $this->assertEquals('a'.$testEmail.'.br', Request::get('email'));

    }

}
