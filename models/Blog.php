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


}

?>