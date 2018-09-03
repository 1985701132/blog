<?php
    define('ROOT',dirname(__FILE__).'/../');
    function autoLoadClass($class){
        require_once ROOT . str_replace('\\','/',$class) . '.php';
    }
    spl_autoload_register('autoLoadClass');
    function view($file,$data=[]){
        if($data){
            extract($data);
        }
        require_once ROOT . 'views/' . str_replace('.','/',$file) . '.html';
    }

?>