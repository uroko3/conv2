<?php

require_once('MyReflection.class.php');

class A {
    protected $x;
    
    public function __construct(array $a) {
        echo "create A\n";
        $this->x = $a[0];
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
    /**
    function __construct(A $a) {
        echo "create CC\n";
        $this->a = $a;
    }
    */
    
    function getx() {
        return $this->a->f();
    }
    
}


class CCC {
    protected $a, $aa, $aaa;
    function __construct(A $a, A $aa, A $aaa) {
        echo "create CCC\n";
        $this->a = $a;
        $this->aa = $aa;
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
        return $this->getxx();
    }
}

class XX {
    function __construct(array $a) {
        echo "create XX\n";
    }
}

class Y {
    function __construct(int $b, int $c) {
        
    }
}

$box = new Box();
$box->when([A::class,XX::class])
->needs('a')
->give([11,22,33])
->when(Y::class)
->needs('b')
->give(8888)
->needs('c')
->give(7777);

var_dump($box);

$ref = new MyReflection($box);
$obj = $ref->make(C::class);
print_r($obj->get());

