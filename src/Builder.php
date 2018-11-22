<?php

namespace eduluz1976\pervasive;

class Builder
{
    protected static $lsClasses = [];


    protected static function instantiate($className, $constructor) {

        if (is_array($constructor)) {
            $obj = new $className(...$constructor);
        } else {
            $obj = new $className($constructor);
        }

        return $obj;
    }



    /**
     * @param $className
     * @param array $props
     * @return Mock
     */
    public static function design($className, $props = [])
    {
        if (isset($props['constructor']) ) {
            $obj = self::instantiate($className, $props['constructor']);


        } else {
            $obj = new $className;
        }

        // TODO if constructor...

        self::$lsClasses[$className] = new Mock($obj);

        return self::$lsClasses[$className];
    }


    public static function build($className, $props=[])
    {
        $obj = clone self::$lsClasses[$className];

        if ($props) {
            self::redesign($obj, $props);
        }

        return $obj;
    }



    protected static function redesign(&$obj, $props=[]) {

        if (isset($props['apply']) && (is_array($props['apply']))) {

            foreach ($props['apply'] as $methodName => $params) {




                if (method_exists($obj->getObj(), $methodName)) {


                    call_user_func_array([$obj->getObj(), $methodName], $params);
                }


            }



        }


    }

}
