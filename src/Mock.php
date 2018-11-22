<?php

namespace eduluz1976\pervasive;

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



    public function __construct($obj)
    {
        $this->obj = $obj;
    }

    public function __call($name, $arguments)
    {
        $ret = null;
        if (method_exists($this->obj, $name)) {
            if (isset($this->methods['pre-' . $name])) {
                call_user_func($this->methods['pre-' . $name], $this);
            }

            $ret = call_user_func_array([$this->obj, $name], $arguments);
        }
        return $ret;
    }

    /**
     * @return MockBuilder
     */
    public function _()
    {
        return MockBuilder::getInstance($this);
    }

    public function _set($k, $v, $index = false)
    {
        if (is_array($this->$k) && $index) {
            $this->$k[$index] = $v;
        } else {
            $this->$k = $v;
        }
    }

    public function _get($k)
    {
        return $this->$k;
    }

    public function __set($name, $value)
    {
        $this->obj->$name = $value;
    }

    public function __get($name)
    {
        return $this->obj->$name;
    }

    public function __clone()
    {
        $this->obj = clone $this->obj;
    }
}
