<?php

class MyReflection {
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
        return new $className(...$parameterInstance);
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