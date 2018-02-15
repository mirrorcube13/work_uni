<?php

namespace PTS\Models;

class Model implements \ArrayAccess
{
    protected const PRIMARY_KEY_NOT_SET = 'Primary key for model [{class}] not set';

    protected const PRIMARY_KEY_NOT_UNQ = 'Primary key [{key}] for table [{table}] is not unique';

    protected $table;

    protected $primary_key;

    protected $attributes = [];

    protected $hasDefault = [];

    public function __get($name)
    {
        return $this->attributes[$name];
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset)
    {

        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset) {}
}
