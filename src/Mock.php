<?php

namespace eduluz1976\pervasive;

/**
 * Class Mock
 * @package eduluz1976\pervasive
 */
class Mock
{
    protected $obj;
    protected $builder;
    protected $methods = [];

    /**
     * @return mixed
     */
    public function getObj()
    {
        return $this->obj;
    }

    /**
     * @param mixed $obj
     * @return Mock
     */
    public function setObj($obj)
    {
        $this->obj = $obj;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param mixed $builder
     * @return Mock
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     * @return Mock
     */
    public function setMethods(array $methods): Mock
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * Mock constructor.
     * @param object $obj
     */
    public function __construct($obj = null)
    {
        if ($obj) {
            $this->setObj($obj);
        }
    }

    /**
     * Capture the calls to methods to run on $obj context.
     *
     * @param string $name
     * @param mixed $arguments
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        $ret = null;

        assert(is_object($this->getObj()), "Invalid type on 'object' context: type found= " . gettype($this->getObj()));
        assert(method_exists($this->getObj(), $name), "Method $name is not implemented");

        $this->_testAndRunPreMethod($name);

        $ret = call_user_func_array([$this->getObj(), $name], $arguments);

        $this->_testAndRunPosMethod($name);

        return $ret;
    }

    /**
     * @param $methodName
     */
    protected function _testAndRunPreMethod($methodName)
    {
        if (isset($this->methods['pre-' . $methodName])) {
            $method = $this->methods['pre-' . $methodName];
            $return = $method->call($this->getObj());
            assert(is_object($return), new RuntimeException("Invalid return from method $methodName (pre): " . gettype($return)));
            $this->setObj($return);
        }
    }

    /**
     * @param $methodName
     */
    protected function _testAndRunPosMethod($methodName)
    {
        if (isset($this->methods['pos-' . $methodName])) {
            $method = $this->methods['pos-' . $methodName];
            $return = $method->call($this->getObj());
            assert(is_object($return), new RuntimeException("Invalid return from method $methodName (pos): " . gettype($return)));
            $this->setObj($return);
        }
    }

    /**
     * @return MockBuilder
     */
    public function _()
    {
        return MockBuilder::getInstance($this);
    }

    /**
     * @param string $k
     * @param mixed $v
     * @param mixed $index
     */
    public function _set($k, $v, $index = false)
    {
        if (is_array($this->$k) && $index) {
            $this->$k[$index] = $v;
        } else {
            $this->$k = $v;
        }
    }

    /**
     *
     * @param string $k
     * @return mixed
     */
    public function _get($k)
    {
        return $this->$k;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->obj->$name = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->obj->$name;
    }

    /**
     * Just allow to clone the object
     */
    public function __clone()
    {
        $this->obj = clone $this->obj;
    }
}
