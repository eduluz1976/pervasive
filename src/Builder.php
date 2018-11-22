<?php

namespace eduluz1976\pervasive;

/**
 *
 * Class Builder
 * @package eduluz1976\pervasive
 */
class Builder
{
    protected static $lsClasses = [];

    /**
     * Create and wrap an instance of ``$className`` class.
     *
     * @param string $className
     * @param array $props
     * @return Mock
     */
    public static function design($className, $props = [])
    {

        if (isset($props['constructor'])) {
            $obj = self::instantiate($className, $props['constructor']);
        } else {
            $obj = new $className;
        }

        self::$lsClasses[$className] = new Mock($obj);

        return self::$lsClasses[$className];
    }

    /**
     * Return the wrapped object
     *
     * @param string $className
     * @param array $props
     * @return Mock
     */
    public static function build($className, $props = [])
    {
        $obj = clone self::$lsClasses[$className];

        if ($props) {
            self::redesign($obj, $props);
        }

        return $obj;
    }

    /**
     * Create an instance of ``$className``
     *
     * @param string $className
     * @param mixed $constructor
     * @return mixed
     */
    protected static function instantiate($className, $constructor)
    {
        if (is_array($constructor)) {
            $obj = new $className(...$constructor);
        } else {
            $obj = new $className($constructor);
        }

        return $obj;
    }


    /**
     * Apply some functions defined on ``design`` or ``build`` operation.
     *
     * @param object $obj
     * @param array $props
     */
    protected static function redesign(&$obj, $props = [])
    {
        if (isset($props['apply']) && (is_array($props['apply']))) {
            foreach ($props['apply'] as $methodName => $params) {
                if (method_exists($obj->getObj(), $methodName)) {
                    call_user_func_array([$obj->getObj(), $methodName], $params);
                }
            }
        }
    }
}
