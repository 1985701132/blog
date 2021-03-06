<?php
    namespace controllers;
    use Intervention\Image\ImageManagerStatic as Image;
    use models\User;

    class UserController{

        public function dologin()
        {
            $email = $_POST['email'];
            $password = md5($_POST['password']);

            $user = new User;
            $res = $user->login($email,$password);
            if($res)
            {   
                $_SESSION['email'] = $res['email'];
                $_SESSION['id'] = $res['id'];
                $_SESSION['avatar'] = $res['avatar'];

                header('Location:/blog/index');
                die('登录成功');
            }
            else
            {
                die('用户名或密码错误');
            }

        }

        public function register(){
            view('user.add');
        }

        public function store()
        {   
            //接收表单
            $email = $_POST['email'];
            $password = md5($_POST['password']);  

            //生成激活码(32位随机字符串)
            $code = md5(rand(1,9999));

            //保存到redis
            $redis = \libs\Redis::getInstance();

            //序列化(数组转成JSON字符串)
            $value = json_encode([
                'email'=>$email,
                'password'=>$password,
            ]);

            //键名
            $key = "temp_user:{$code}";
            $redis->setex($key,300,$value);



            //把激活码发送到用户的账邮箱中       
            //从邮箱地址中取出姓名 
            $name = explode('@',$email);
            //收件人地址
            $from = [$email,$name[0]];

            //信息数组
            $message = [
                'title' => '智聊系统-账号激活',
                'content' => "点击以下链接进行激活：<br> <a href='http://localhost:8888/user/active_user?code={$code}'>http://localhost:8888/user/active_user?code={$code}</a><p>如果按钮不能点击，请复制上面链接地址，在浏览器中访问来激活账号</p>",
                'from' => $from,
            ];

            // 把消息转成字符串(JSON ==> 序列化)
            $message = json_encode($message);

            // 放到队列中
            $redis->lpush('email',$message);

            echo 'ok';
        }
        public function active_user()
        {
            //接受激活码
            $code = $_GET['code'];

            //到redis中取出账号
            $redis = \libs\Redis::getInstance();

            //拼名字
            $key = 'temp_user:'.$code;
            //取出数据
            $data = $redis->get($key);

            if($data)
            {
                //删除redis中的激活码
                $redis->del($key);
                //转回数组
                $data = json_decode($data,true);
                //插入数据库
                $user = new User;
                $user->add($data['email'],$data['password']);
                //跳转到登录页面
                header('Location:/user/login');
            }
            else{
                die('激活码无效');
            }
        }

        public function login()
        {
            view('user.login');
        }
        public function logout()
        {
            $_SESSION =[];
            die('退出成功');
        }

        public function avatar()
        {
            view('user.avatar');
        }

        public function setavatar()
        {
            //上传新头像
            $upload = \libs\Uploader::make();
            $path = $upload->upload('avatar','avatar');

            //裁切图片
            $image = Image::make(ROOT . 'public/uploads/'.$path);

            // 注意：Crop 参数必须是整数，所以需要转成整数：(int)
            $image->crop((int)$_POST['w'],(int)$_POST['h'],(int)$_POST['x'],(int)$_POST['y']);

            //保存时覆盖原图
            $image->save(ROOT . 'public/uploads/'.$path);

            //保存到user表中
            $model = new \models\User;
            $model->setAvatar('/uploads/'.$path);

            //删除原头像
            @unlink(ROOT . 'public'.$_SESSION['avatar']);
            //设置新头像
            $_SESSION['avatar']='/uploads/'.$path;

        }

        public function setActiveUsers()
        {
            $user = new User;
            $user->computeActiveUsers();
        }

    }
?>