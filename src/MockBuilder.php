<?php

namespace eduluz1976\pervasive;

/**
 * Class MockBuilder
 * @package eduluz1976\pervasive
 */
class MockBuilder
{
    protected static $context;
    protected static $instance;

    /**
     * @param object $context
     * @return MockBuilder
     */
    public static function getInstance($context = false)
    {
        if (!self::$instance) {
            self::$instance = new MockBuilder();
        }
        self::$context = $context;
        return self::$instance;
    }

    /**
     * @param string $methodName
     * @param Closure $closure
     * @return $this
     */
    public function addPreFunction($methodName, $closure)
    {
        self::$context->_set('methods', $closure, 'pre-' . $methodName);

        return $this;
    }

    /**
     * @param string $methodName
     * @param Closure $closure
     * @return $this
     */
    public function addPosFunction($methodName, $closure)
    {
        self::$context->_set('methods', $closure, 'pos-' . $methodName);

        return $this;
    }
}
