<?php

namespace eduluz1976\pervasive;

class MockBuilder
{
    protected static $context;
    protected static $instance;

    public static function getInstance($context = false)
    {
        if (!self::$instance) {
            self::$instance = new MockBuilder();
        }
        self::$context = $context;
        return self::$instance;
    }


    /**
     * @param $methodName
     * @param $closure
     * @return $this
     */
    public function addPreFunction($methodName, $closure)
    {
        self::$context->_set('methods', $closure, 'pre-' . $methodName);

        return $this;
    }
}
