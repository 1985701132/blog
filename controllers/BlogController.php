<?php
namespace controllers;
use PDO;
use models\Blog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        public function makeExcel()
        {
            // 获取当前标签页
            $spreadsheet = new Spreadsheet();
            // 获取当前工作
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', '标题');
            $sheet->setCellValue('B1', '内容');
            $sheet->setCellValue('C1', '发表时间');
            $sheet->setCellValue('D1', '是否公开');

            $model = new Blog;
            $blogs = $model->getNew();

            $i=2; //第几行
            foreach($blogs as $v)
            {
                $sheet->setCellValue('A'.$i, $v['title']);
                $sheet->setCellValue('B'.$i, $v['content']);
                $sheet->setCellValue('C'.$i, $v['created_at']);
                $sheet->setCellValue('D'.$i, $v['is_show']);
                $i++;
            }

            $date = date('Ymd');

            // 生成 excel 文件
            $writer = new Xlsx($spreadsheet);
            $writer->save(ROOT . 'excel/'.$date.'.xlsx');


            //下载文件路径
            $file = ROOT . 'excel/'.$date.'.xlsx' ;
            //下载时文件名
            $fileName = $date.'最新的10篇日志.xlsx';

            // 告诉浏览器这是一个二进程文件流    
            Header("Content-Type:application/octet-stream");
            // 请求范围的度量单位  
            Header("Accept-Ranges:bytes:bytes");
            // 告诉浏览器文件尺寸    
            Header("Accept-Length:".filesize($file));
            // 开始下载，下载时的文件名
            Header("Content-Disposition:attachment;filename=".$fileName);
            // 读取服务器上的一个文件并以文件流的形式输出给浏览器
            readfile($file);

        }

    }
?>