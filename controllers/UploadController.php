<?php
namespace controllers;

class UploadController
{
    public function upload()
    {
        view('text');
    }

    public function doupload()
    {
        //根目录
        $uploadDir = ROOT.'public/uploads';
        $date = date('Y-m-d');

        if(!is_dir($uploadDir.'/'.$date))
        {
            mkdir($uploadDir.'/'.$date);
        }

        //获取文件扩展名
        $ext = strrchr($_FILES['image']['name'] , '.');  
        //strrchr() 函数查找字符串在另一个字符串中最后一次出现的位置，并返回从该位置到字符串结尾的所有字符。

        //生成唯一文件名
        $name = md5(time() . rand(1,9999));

        //完整文件名
        $fullName = $uploadDir . '/' .$date . '/' . $name . $ext;

        //保存文件到指定目录
        move_uploaded_file($_FILES['image']['tmp_name'],$fullName);
        echo '图片上传成功';

    }

    public function uploadmore()
    {
        //根目录
        $uploadDir = ROOT.'public/uploads';
        $date = date('Y-m-d');

        if(!is_dir($uploadDir.'/'.$date))
        {
            mkdir($uploadDir.'/'.$date);
        }

        //循环五张图片的name
        foreach($_FILES['images']['name'] as $k =>$v)
        {
            $name = md5(time() . rand(1,9999));
            $ext = strrchr($v, '.');

            //根据 name 的下标找到对应的临时文件并移动
            move_uploaded_file($_FILES['images']['tmp_name'][$k],$uploadDir. '/' .$date . '/' . $name . $ext);

            echo '图片上传成功 <br>';
            // echo $uploadDir.$date . '/' . $name . $ext . '<hr>';
        }
    }

    public function uploadbig()
    {
        // echo "<pre>";
        // var_dump($_POST,$_FILES);die;
        $count = $_POST['count'];  // 总量
        $i = $_POST['i'];        // 第几块
        $size = $_POST['size'];   // 每块大小
        $name = 'big_img_'.$_POST['img_name'];  // 所有分块的名字
        $img = $_FILES['img'];    // 图片

        move_uploaded_file( $img['tmp_name'] , ROOT.'tmp/'.$i);

        $redis = \libs\Redis::getInstance();

        // 每上传一张就加1
        $uploadedCount = $redis->incr($name);

        if($uploadedCount == $count)
        {
            $fp = fopen(ROOT.'public/uploads/big/'.$name.'.png', 'a');
            // var_dump($fp);die;
            for($i=0;$i<$count;$i++)
            {
                fwrite($fp,file_get_contents(ROOT.'tmp/'.$i));
                var_dump(ROOT.'tmp/'.$i);
                unlink(ROOT.'tmp/'.$i);
                

            }
            fclose($fp);
               
                $redis->del($name);
        }
    }

    
    
}