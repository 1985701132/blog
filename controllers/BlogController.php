<?php
namespace controllers;
use PDO;
use models\Blog;
    class BlogController{
        public function create()
        {
            view('blog.create');
        }
        public function store()
        {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $is_show = $_POST['is_show'];

            $blog = new Blog;
            $res = $blog->create($title,$content,$is_show);
            if($res)
            {
                header('Location:/blog/index');
            }
            else
            {
                header('Location:/blog/create');
            }

        }
        public function index(){
            $blog = new Blog;
            $data=$blog->search();
            view('blog.index',$data);
        }
        public function content_to_html(){
            $blog = new Blog;
            $stmt=$blog->pdo->query("SELECT * FROM blogs");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            //开启缓冲区
            ob_start();
                        
            //生成静态页
            foreach($data as $v){
                view('blog.content',[
                    'blog'=>$v,
                ]);
                //取出缓冲区内容
                $str = ob_get_contents();
                //生成静态页
                file_put_contents(ROOT.'public/contents/'.$v['id'].'.html',$str);
                //清空缓冲区
                ob_clean();
            }

        }

        public function display()
        {
            // 接收日志ID
            $id = (int)$_GET['id'];

            $blog = new Blog;

            echo $blog->getDisplay($id);

        }

        public function DisplayToDb()
        {
            $blog = new Blog;
            $blog->getDisplayToDb();
        }

    }
?>