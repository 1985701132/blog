<?php
    namespace models;
    use PDO;
    class User{
        public $pdo;
        public function __construct()
        {
            $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','');
            $this->pdo->exec('SET NAMES utf8');
        }

        public function add($email,$password)
        {
            $stmt = $this->pdo->prepare("INSERT INTO users (email,password) VALUES(?,?)");
            return $stmt->execute([
                $email,
                $password,
            ]);
        }

        public function login($email,$password)
        {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email=? AND password=?");
            $stmt->execute([
                $email,
                $password,
            ]);
            $res = $stmt->fetch();
            return $res;
        }

        public function setAvatar($path)
        {
            $stmt = $this->pdo->prepare('UPDATE users SET avatar=? WHERE id=?');
            $stmt->execute([
                $path,
                $_SESSION['id']
            ]);
        }

        public function computeActiveUsers()
        {
            echo "<pre>";
            //取日志的分值
            $stmt = $this->pdo->query('SELECT user_id,COUNT(*)*5 fz FROM blogs WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) GROUP BY user_id');
            $data1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //取评论的分值
            $stmt = $this->pdo->query('SELECT user_id,COUNT(*)*3 fz FROM comments WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) GROUP BY user_id');
            $data2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //合并数组
            $arr = [];

            foreach($data1 as $v)
            {
                $arr[$v['user_id']] = $v['fz'];
            }

            foreach($data2 as $v)
            {
                $arr[$v['user_id']] = $v['fz'];
            }

            //排序(倒序)
            arsort($arr);

            //取前20并保存键（第四个参数保留键）
            $data = array_slice($arr,0,20,TRUE);
            // var_dump($data);
            // 取出前20用户的ID
            // 从数组中取出所有的键
            $userIds = array_keys($data);
            //数组转字符中 = [1,2,3,4,5,6,8];   =>  '1,2,3,4,5,6,7'
            $userIds = implode(',',$userIds);
            
            //取出用户的 头像 和 email
            $sql = "SELECT id,email,avatar FROM users WHERE id IN($userIds)";

            $stmt = $this->pdo->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 把计算的结果保存到 Redis，因为 Redis 中只能保存字符串，所以我们需要把数组转成JSON字符串
            $redis = \libs\Redis::getInstance();
            $redis->set('active_users', json_encode($data));
        }

        public function getActiveUsers()
        {
            $redis = \libs\Redis::getInstance();
            $data = $redis->get('active_users');
            // 转回数组（第二个参数 true:数组    false：对象）
            return json_decode($data, true);
        }
    }
?>