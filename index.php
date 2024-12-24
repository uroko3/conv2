<?php

require_once('MyReflection.class.php');

class A {
    protected $x;
    
    public function __construct(int $a) {
        echo "create A\n";
        $this->x = $a;
    }
    
    public function f() {
        return $this->x;
    }
}

class B extends A {
    public function __construct(C $c, C $cc, A $a) {
        echo "create B\n";
    }
}


class CC extends CCC {
    protected $a;
    function __construct(A $a) {
        echo "create CC\n";
        $this->a = $a;
    }
    
    function getx() {
        return $this->a->f();
    }
    
}


class CCC {
    protected $aaa;
    function __construct(A $a, A $aa, A $aaa) {
        echo "create CCC\n";
        $this->aaa = $aaa;
    }
    
    function getxx() {
        return $this->aaa->f();
        //echo $this->aaa;
    }
    
}

class C extends CC {
    //public function __construct() {
    //    echo "create C\n";
    //}
    
    function get() {
        echo "call C::get()\n";
    }
}

class XX {
    function __construct(int $a) {
        echo "create XX\n";
    }
}

$box = new Box();
$box->when(A::class)
->needs('a')
->give(9999);

$ref = new MyReflection($box);
$obj = $ref->make(C::class);
print_r($obj->getxx());

