<?php

require_once('MyReflection.class.php');

class A {
    protected $x;
    
    public function __construct() {
        echo "create A\n";
    }
    
    public function f() {
        
    }
}

class B extends A {
    public function __construct(C $c, C $cc, A $a) {
        echo "create B\n";
    }
}


class CC extends CCC {
    function __constructx(A $a) {
        echo "create CC\n";
    }
    
}


class CCC {
    function __construct(A $a, A $aa, A $aaa) {
        echo "create CCC\n";
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




$ref = new MyReflection();
$ref->make(C::class);


