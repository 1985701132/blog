<?php
namespace libs;

class Uploader
{
    private function __construct(){}
    private function __clone(){}
    private static $_obj = null;

    public static function make()
    {
        if(self::$_obj === null)
        {
            self::$_obj = new self;
        }
        return self::$_obj;
    } 

    /****************** 定义属性 *******************/
    private $_root = ROOT . 'public/uploads'; //保存图片一级目录
    private $_ext =['image/jpeg','image/jpg','image/ejpeg','image/png','image/gif','image/bmp'];// 允许上传的扩展名
    private $_maxSize = 1024*1024*1.8;  // 最大允许上传的尺寸，1.8M

    private $_file;  // 保存用户上传的图片信息
    private $_subDir;


    /************** 定义公开方法 ****************/
    // 上传图片
    // 参数一、表单中文件名
    // 参数二、保存到的二级目录
    public function upload($name,$subdir)
    {
        $this->_file = $_FILES[$name];
        $this->_subDir = $subdir;
        
    }
    



}


?>