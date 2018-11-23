<?php
/**
 * Created by PhpStorm.
 * User: eduardoluz
 * Date: 2018-11-21
 * Time: 11:53 PM
 */

namespace unit;

// Important set this value to 1
ini_set('assert.exception', 1);

use eduluz1976\pervasive\Builder;
use eduluz1976\pervasive\ClassNotExistsException;
use eduluz1976\pervasive\Mock;
use eduluz1976\pervasive\RuntimeException;
use tests\eduluz1976\ExternalServiceProviderAPI;
use tests\eduluz1976\Request;
use tests\eduluz1976\UserController;

class BuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests add and run a 'pre' function, that not use '$this' as context;
     *
     * @throws \Exception
     */
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
                    return $this;
                }
            );

        $testEmail = 'my_user@domain.com';

        Request::set('email', $testEmail);

        $controller = Builder::build(UserController::class);

        $controller->addNewUser();

        $this->assertEquals('a' . $testEmail . '.br', Request::get('email'));
    }

    /**
     * Tests add a function that uses the '$this' context
     *
     * @throws \Exception
     */
    public function testAddPreWithContext()
    {
        Builder::design(UserController::class)
            ->_()
            ->addPreFunction(
                'addNewUser',
                function () {
                    $email = $this->getEmail();

                    $this->setEmail('a' . $email . '.br');

                    return $this;
                }
            );

        $testEmail = 'my_user@domain.com';

        $controller = Builder::build(UserController::class);

        $controller->setEmail($testEmail);
        $controller->addNewUser();

        $this->assertEquals('a' . $testEmail . '.br', $controller->getEmail());
    }

    /**
     * Tests if adding 'pre' and 'pos' works
     * @throws \Exception
     */
    public function testAddPrePosWithContext()
    {
        Builder::design(UserController::class)
            ->_()
            ->addPreFunction(
                'addNewUser',
                function () {
                    $this->setEmail('mytest@domain.com');

                    return $this;
                }
            )->addPosFunction(
                'addNewUser',
                function () {
                    $email = $this->getEmail();

                    $this->setEmail($email . '.br');

                    return $this;
                }
            );

        $testEmail = 'my_user@domain.com';

        $controller = Builder::build(UserController::class);

        $controller->setEmail($testEmail);
        $controller->addNewUser();

        $this->assertEquals('mytest@domain.com.br', $controller->getEmail());
    }

    /**
     * Tests a error-prone situation: the 'pos' function does not return the '$obj'
     */
    public function testAddPosWithoutReturn()
    {
        Builder::design(UserController::class)
            ->_()
            ->addPreFunction(
                'addNewUser',
                function () {
                    $this->setEmail('mytest@domain.com');

                    return $this;
                }
            )->addPosFunction(
                'addNewUser',
                function () {
                    $email = $this->getEmail();

                    $this->setEmail($email . '.br');
                }
            );

        $testEmail = 'my_user@domain.com';

        $controller = Builder::build(UserController::class);

        $controller->setEmail($testEmail);

        $this->expectException(RuntimeException::class);
        $controller->addNewUser();
    }

    /**
     * Testing adding an invalid class
     */
    public function testInvalidClass()
    {
        $this->expectException(ClassNotExistsException::class);
        Builder::design('Invalid');

        $controller = Builder::build('Invalid');

        $controller->execute();
    }

    /**
     * Tests the 'pre' function without return the '$obj'
     */
    public function testMethodWithNoReturn()
    {
        Builder::design(UserController::class)
            ->_()
            ->addPreFunction(
                'addNewUser',
                function () {
                    $email = $this->getEmail();

                    $this->setEmail('a' . $email . '.br');
                }
            );

        $testEmail = 'my_user@domain.com';

        $controller = Builder::build(UserController::class);

        $controller->setEmail($testEmail);

        $this->expectException(RuntimeException::class);
        $controller->addNewUser();
    }

    /**
     * Tests if an exception occurs inside the 'pre' function.
     */
    public function testMethodWithException()
    {
        Builder::design(UserController::class)
            ->_()
            ->addPreFunction(
                'addNewUser',
                function () {
                    throw new \Exception('Something wrong', 1);
                }
            );

        $testEmail = 'my_user@domain.com';

        $controller = Builder::build(UserController::class);

        $controller->setEmail($testEmail);

        $this->expectException(\Exception::class);
        $controller->addNewUser();
    }

    /**
     * Tests if a constructor
     * @throws \Exception
     */
    public function testClassWithConstructorSuccess()
    {
        $myClass = new class(0) {
            public $x = 0;
            public $y = 0;
            public $z = 0;

            public function __construct($z)
            {
                $this->z = $z;
            }

            public function add($a, $b)
            {
                $this->x = $a;
                $this->y = $b;
                return $a + $b + $this->z;
            }
        };

        $className = get_class($myClass);

        Builder::design($className, ['constructor' => [5]]);

        $controller = Builder::build($className);

        $v = $controller->add(10, 20);

        $this->assertEquals(35, $v);
    }

    /**
     * Tests if a constructor
     * @throws \Exception
     * @expectedException ArgumentCountError
     */
    public function testClassWithConstructorError()
    {
        $myClass = new class {
            public $x = 0;
            public $y = 0;
            public $z = 0;

            public function __construct($x)
            {
                $this->x = $x;
            }

            public function add($a, $b)
            {
                $this->x = $a;
                $this->y = $b;
                return $a + $b + $this->z;
            }
        };

        $className = get_class($myClass);

        Builder::design($className);
    }

    /**
     * Tests an
     * @throws \Exception
     */
    public function testDynamicClass()
    {
        $myClass = new class {
            public $x = 0;
            public $y = 0;
            public $z = 0;

            public function add($a, $b)
            {
                $this->x = $a;
                $this->y = $b;
                return $a + $b + $this->z;
            }
        };

        $className = get_class($myClass);

        Builder::design($className);

        $controller = Builder::build($className);

        $v = $controller->add(10, 20);

        $this->assertEquals(30, $v);
    }

    /**
     * Tests the 'apply' modifier
     * @throws \Exception
     */
    public function testApplyModifier()
    {
        $myClass = new class {
            public $x = 0;
            public $y = 0;
            public $z = 0;

            public function add($a, $b)
            {
                $this->x = $a;
                $this->y = $b;
                return $a + $b + $this->z;
            }

            public function setZ($z)
            {
                $this->z = $z;
            }
        };

        $className = get_class($myClass);

        Builder::design($className);

        $controller = Builder::build($className, [
            'apply' => [
                'setZ' => [50]
            ]
        ]);

        $v = $controller->add(10, 20);

        $this->assertEquals(80, $v);
    }

    /**
     * Tests an case where the 'apply' parameter refers a method that does not exists on this class
     * @throws \Exception
     */
    public function testApplyModifierUndefinedMethod()
    {
        $myClass = new class {
            public $x = 0;
            public $y = 0;
            public $z = 0;

            public function add($a, $b)
            {
                $this->x = $a;
                $this->y = $b;
                return $a + $b + $this->z;
            }
        };

        $className = get_class($myClass);

        Builder::design($className);

        $this->expectException(RuntimeException::class);
        $controller = Builder::build($className, [
            'apply' => [
                'setZ' => [50]
            ]
        ]);

        $v = $controller->add(10, 20);

        $this->assertEquals(80, $v);
    }
}
