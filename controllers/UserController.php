<?php
    namespace controllers;
    use models\User;

    class UserController{
        public function register(){
            view('user.add');
        }
        public function store()
        {
            $email = $_POST['email'];
            $password = md5($_POST['password']);  

            $user = new User;
            $ret = $user->add($email,$password);
            if(!$ret)
            {
                die('注册失败');
            }

            //发邮件
            $mail = new \libs\Mail;
            $content = "注册成功啦，哈哈哈";
            $name = explode('@',$email);
            //收件人地址
            $from = [$email,$name[0]];
            //发邮件
            $mail->send('注册成功',$content,$from);
            echo 'ok';
        }
    }
?>