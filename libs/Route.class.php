<?php

namespace libs;

class Route
{
    private $controller;
    private $action;
    
    /**
     * @content routeParse方法
     */
    public function routeParse()
    {
        $a = $_GET['a'] ?? 'index';
        $c = $_GET['c'] ?? 'index';

        $this -> controller = $c;
        $this -> action = $a;

        //返回控制器和方法
        $this -> controller = ucfirst($this -> controller).'Controller';
        $this -> action = 'action'.ucfirst($this -> action);

        return [$this -> controller,$this -> action];
    }
}