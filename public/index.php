<?php
    //设置时区
    date_default_timezone_set('PRC');

    // 动态的修改 php.ini 配置文件
    ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
    ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=3');  // 设置 redis 服务器的地址、端口、使用的数据库    
    ini_set('session.gc_maxlifetime', 600);   // 设置 SESSION 10分钟过期

    // 开启SESSION
    session_start();

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

    if(php_sapi_name() == 'cli')
    {
        $controller = ucfirst($argv[1]) . 'Controller';
        $action = $argv[2];
    }
    else
    {
        if(isset($_SERVER['PATH_INFO']))
        {
            $pathInfo = explode('/',$_SERVER['PATH_INFO']);//explode() 函数把字符串打散为数组。
            $controller = ucfirst($pathInfo[1]).'Controller';
            $action = $pathInfo[2];
        }else{
            $controller = 'IndexController';
            $action = 'index';
        }
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