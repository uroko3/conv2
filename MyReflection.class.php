<?php
class Box {
    protected $when;
    protected $when_last_keys;
    
    function __construct() {
        $this->when = [];
        $this->when_last_keys;
    }
    
    function getWhen() {
        return $this->when;
    }
    
    function when($abstruct) {
        $this->when_last_keys = [];
        if(is_array($abstruct)) {
            foreach($abstruct as $v) {
                $this->when_last_keys[] = $v;
            }
        }
        else {
            $this->when_last_keys[] = $abstruct;
        }
        foreach($this->when_last_keys as $key) {
            $this->when[$key] = ['key'=>[],'value'=>[]];
        }
        
        return $this;
    }
    
    function needs(string $key) {
        foreach($this->when_last_keys as $when_key) {
            $this->when[$when_key]['key'][] = $key;
        }
        return $this;
    }
    
    function give($value) {
        if(is_callable($value)) {
            $value = $value(new MyReflection());
        }
        foreach($this->when_last_keys as $key) {
            $this->when[$key]['value'][] = $value;
        }
        return $this;
    }
}

class MyReflection {
    protected $box;
    protected $when;
    
    function __construct(Box $box) {
        $this->box = $box;
        $this->when = [];
    }
    
    public function make(string $className, $param=[]) {
        $reflectionClass = new \ReflectionClass($className);
        $constructor = $reflectionClass->getConstructor();
        if(empty($constructor)) {
            return new $className;
        }
        $parameters = $constructor->getParameters();
        if(empty($parameters)) {
            return new $className;
        }
        $parameterInstance = [];
        foreach($parameters as $parameter) {
            $parameterClassName = $parameter->getType();
            if(!$parameterClassName->isBuiltin()) {
                if(array_key_exists($parameter->getName(), $param)) {
                    $parameterInstance[] = array_shift($param);
                }
                elseif($this->isWhen($className)) {
                    $parameterInstance = array_merge($parameterInstance, $this->when($className, $parameter));
                }
                else {
                    $parameterInstance[] = $this->make($parameterClassName);
                }
            }
            else if(!empty($param)){
                if(array_key_exists($parameter->getName(), $param)) {
                    $parameterInstance[] = array_shift($param);
                }
                else if($parameter->isOptional()) {
                    $parameterInstance[] = $parameter->getDefaultValue();
                }
                else {
                    $parameterInstance[] = array_shift($param);
                }
            }
            elseif($this->isWhen($className)) {
                $parameterInstance = array_merge($parameterInstance, $this->when($className, $parameter));
            }
        }
        return new $className(...$parameterInstance);
    }
    
    protected function isWhen($className) {
        return array_key_exists($className, $this->box->getWhen());
    }
    
    protected function when($className, $parameter) {
        $ret = [];
        $this->when = $this->box->getWhen()[$className];
        if(($index = array_search($parameter->getName(), $this->when['key'])) !== false) {
            if($parameter->isVariadic()) {
                foreach($this->when['value'][$index] as $val) {
                    $ret[] = $val;
                }
            }
            else {
                $ret[] = $this->when['value'][$index];
            }
        }
        return $ret;
    }
    
    public function depen($instance, string $method, $param=[]) {
        $m = new \ReflectionMethod($instance, $method);
        $parameters = $m->getParameters();
        $parameterInstance = [];
        foreach($parameters as $parameter) {
            $parameterClassName = $parameter->getType();
            if(!$parameterClassName->isBuiltin()) {
                if(array_key_exists($parameter->getName(), $param)) {
                    $parameterInstance[] = array_shift($param);
                }
                else {
                    $parameterInstance[] = $this->make($parameterClassName);
                }
            }
            else if(!empty($param)){
                if(array_key_exists($parameter->getName(), $param)) {
                    $parameterInstance[] = array_shift($param);
                }
                else if($parameter->isOptional()) {
                    $parameterInstance[] = $parameter->getDefaultValue();
                }
                else {
                    $parameterInstance[] = array_shift($param);
                }
            }
        }
        return new $parameterInstance;
    }
}