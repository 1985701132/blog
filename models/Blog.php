<?php
namespace models;

use PDO;

class Blog
{
    public $pdo;
    public function __construct()
    {
        $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=blog', 'root', '');
        $this->pdo->exec('set names utf8');
    }
    public function search()
    {
        $where = 1;
        /******************* 搜索 ****************/
        if (isset($_GET['keywords']) && $_GET['keywords']) {
            $where .= " AND (title like '%{$_GET['keywords']}%' OR content like '%{$_GET['keywords']}%')";
        }
        if (isset($_GET['start_date']) && $_GET['start_date']) {
            $where .= " AND created_at >=  '{$_GET['start_date']}'";
        }
        if (isset($_GET['end_date']) && $_GET['end_date']) {
            $where .= " AND created_at <=  '{$_GET['end_date']}'";
        }
        if (isset($_GET['is_show']) && $_GET['is_show'] != "") {
            $where .= " AND is_show = {$_GET['is_show']}";
        }

        /******************* 排序 ****************/

        //默认排序条件
        $orderBy = 'created_at';
        $orderWay = 'desc';
        if (isset($_GET['order_by']) && $_GET['order_by'] == 'display') {
            $orderBy = 'display';
        }
        if (isset($_GET['order_way']) && $_GET['order_way'] == 'asc') {
            $orderWay = 'asc';
        }

        $where .=" ORDER BY {$orderBy} {$orderWay}";



        /******************* 分页 ****************/

        // 每页显示条数
        $prepage = 20;
        //获取当前页码
        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        //起始值
        $offset = ($page-1)*5;
        $where .= " LIMIT {$offset} , {$prepage}";
        
        //总记录数
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM blogs");
        $count = $stmt->fetch(PDO::FETCH_COLUMN);
        //总页数
        $pages = ceil($count/$prepage);
        $pageBtn = '';
        for($i=1;$i<=$pages;$i++)
        {
            $class = $page == $i ? 'page_active' : '';
            $pageBtn .="<a class='{$class}' href='?page={$i}'>{$i}</a>";
        }
      
        



        $stmt = $this->pdo->query("SELECT * FROM blogs WHERE {$where}");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [
            'data'=>$data,
            'pageBtn'=>$pageBtn,
        ];
    }

    public function getDisplay($id)
    {
        // 使用日志ID拼出键名
        $key = "blog-{$id}";

        // 连接 Redis
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);

        // 判断 hash 中是否有这个键，如果有就操作内存，如果没有就从数据库中取
        if($redis->hexists('blog_displays', $key))
        {
            // 累加 并且 返回添加完之后的值
            $newNum = $redis->hincrby('blog_displays', $key, 1);
            return $newNum;
        }
        else
        {
            // 从数据库中取出浏览量
            $stmt = $this->pdo->prepare('SELECT display FROM blogs WHERE id=?');
            $stmt->execute([$id]);
            $display = $stmt->fetch( PDO::FETCH_COLUMN );
            $display++;
            // 保存到 redis
            $redis->hset('blog_displays', $key, $display);
            return $display;
        }
    }

    public function getDisplayToDb()
    {
        // 连接 Redis
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
        $data = $redis->hgetall('blog_displays');
        echo "<pre>";
        var_dump($data);
        foreach($data as $k => $v){
            $id = str_replace('blog-','',$k);
            $sql = "UPDATE blogs SET display={$v} WHERE id = {$id}";
            echo "<pre>";
            var_dump($sql);
            $this->pdo->exec($sql);
        }
    }

    public function create($title,$content,$is_show)
    {
        $stmt = $this->pdo->prepare("INSERT INTO blogs SET title = ? , content = ? , is_show = ? , user_id = ?");
        return $stmt->execute([
            $title,
            $content,
            $is_show,
            $_SESSION['id'],
        ]);

    }

    


}

?>