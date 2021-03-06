<?php
namespace controllers;

    use libs\Mail;
    class MailController
    {
        public function send()
        {
             // 连接 Redis
             $redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);

            $mailer = new Mail;

            // 设置 PHP 永不超时
            ini_set('default_socket_timeout', -1);
            echo "发邮件队列启动成功..\r\n";

            while(true)
            {
                //从email中取消息
                $data = $redis->brpop('email',0);
                $message = json_decode($data[1],TRUE);

                //发邮件
                $mailer->send($message['title'],$message['content'],$message['from']);

                echo "发送邮件成功！继续等待下一个。\r\n";


            }
        }

    }


?>