<?php
    namespace controllers;
    use PDO;
    class MockController
    {
        function blog()
        {
            $pdo = new PDO('mysql:localhost=127.0.0.1;dbname=blog','root','');
            $pdo->exec('set names utf8');
            // 清空表，并且重置 ID
            $pdo->exec('TRUNCATE blogs');

            for($i=0;$i<100;$i++)
            {
                $title = $this->getChar( rand(20,100) ) ;
                $content = $this->getChar( rand(100,600) );
                $short = $this->getChar( rand(30,100) );
                $display = rand(10,500);
                $is_show = rand(0,1);
                $date = rand(1233333399,1535592288);
                $date = date('Y-m-d H:i:s', $date);
                $pdo->exec("INSERT INTO blogs (title,content,display,is_show,created_at,short_content) VALUES('$title','$content',$display,$is_show,'$date','$short')");
                // print_r($pdo->errorInfo());
            }

        }
        private function getChar($num)  // $num为生成汉字的数量
        {
            $b = '';
            for ($i=0; $i<$num; $i++) {
                // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
                $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
                // 转码
                $b .= iconv('GB2312', 'UTF-8', $a);
            }
            return $b;
        }
    }
?>