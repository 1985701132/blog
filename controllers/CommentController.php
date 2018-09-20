<?php
namespace controllers;

class CommentController
{
    //发表评论
    public function comments()
    {
        //接受原始数据
        $data = file_get_contents('php://input');

        //转成数组
        $_POST = json_decode($data,TRUE);
        //判断是否登录
        if(!isset($_SESSION['id']))
        {
            echo json_encode([
                'status_code'=>'401',
                'message'=>'登陆以后才可以评论哦~',
            ]);
            exit;
        }

        //接受表单中数据
        $content = $_POST['content'];
        $blog_id = $_POST['blog_id'];

        //插入表
        $model = new \models\Comment;
        $model->add($content,$blog_id);

        //返回新发表的评论（过滤数据）
        echo json_encode([
            'status_code'=>'200',
            'message'=>'发表成功',
            'data'=>[
                'content'=>$content,
                'avatar'=>$_SESSION['avatar'],
                'email'=>$_SESSION['email'],
                'created_at'=>date('Y-m-d H:i:s')
            ]
        ]);
        exit;
    }

    public function comment_list()
    {
        //接受日志id
        $blogId = $_GET['id'];

        //获取日志评论
        $model = new \models\Comment;
        $data = $model->getComments($blogId);
        
        //转成json
        echo json_encode([
            'status_code'=>200,
            'data'=>$data,
        ]);


    }
}