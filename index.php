<?php

$data = include './data.php';
include './calc.php';
include './global.php';

$calc = new Calc($data);

GlobalGlass::set('calc', $calc);

function match($c1, $c2)
{
//    GLOBAL $calc;
    $calc = GlobalGlass::get('calc');

    return $calc->match($c1, $c2);
}
