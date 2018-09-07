<?php
namespace controllers;
use PDO;
use models\Blog;
    class BlogController{
        public function index(){
            $blog = new Blog;
            $data=$blog->search();
            $blog->search();
            view('blog.index',$data);
        }
        public function content_to_html(){
            $blog = new Blog;
            $data=$blog->search();
            
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

    }
?>