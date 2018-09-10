<?php
    // 定义常量 根目录
    define('ROOT',dirname(__FILE__).'/../');

    require(ROOT.'vendor/autoload.php');
    
    function autoLoadClass($class){
        require_once ROOT . str_replace('\\','/',$class) . '.php';
    }
    spl_autoload_register('autoLoadClass');

    function view($file,$data=[]){
        if($data){
            extract($data);
        }
        require ROOT . 'views/' . str_replace('.','/',$file) . '.html';
    }

        if(isset($_SERVER['PATH_INFO']))
        {
            $pathInfo = explode('/',$_SERVER['PATH_INFO']);//explode() 函数把字符串打散为数组。
            $controller = ucfirst($pathInfo[1]).'Controller';
            $action = $pathInfo[2];
        }else{
            $controller = 'IndexController';
            $action = 'index';
        }

    $controller = "controllers\\{$controller}";
    $_C = new $controller;
    $_C->$action();
    
    function config($name)
    {
        static $config = null;
        if($config === null)
        {
            // 引入配置文件 
            $config = require(ROOT.'config.php');
        }
        return $config[$name];
    }
    




?>