<?php

namespace DSchoenbauer\DotNotation;

/**
 * Faster way to deal with PHP arrays
 * - does key checking
 * - default values
 *
 * @author David
 */
class ArrayDotNotation {

    private $_data = [];

    public function __construct(array $data) {
        $this->setData($data);
    }

    public function getData() {
        return $this->_data;
    }

    public function setData(array $data) {
        $this->_data = $data;
        return $this;
    }

    public function get($dotNotation, $defaultValue = null) {
        return $this->recursiveGet($this->getData(), explode('.', $dotNotation), $defaultValue);
    }

    protected function recursiveGet($data, $keys, $defaultValue) {
        $key = array_shift($keys);
        if (is_array($data) && $key && count($keys) == 0) { //Last Key
            return array_key_exists($key, $data) ? $data[$key] : $defaultValue;
        } elseif (is_array($data) && array_key_exists($key, $data)) {
            return $this->recursiveGet($data[$key], $keys, $defaultValue);
        }
        return $defaultValue;
    }

    public function set($dotNotation, $value) {
        $this->recursiveSet($this->_data, explode('.', $dotNotation), $value);
        return $this;
    }

    protected function recursiveSet(&$data, $keys, $value) {
        $key = array_shift($keys);
        if (is_array($data) && $key && count($keys) == 0) { //Last Key
            $data[$key] = $value;
        }else{
            if(!array_key_exists($key, $data)){
                $data[$key] = [];
            }
            $this->recursiveSet($data[$key], $keys, $value);
        }
    }

}
