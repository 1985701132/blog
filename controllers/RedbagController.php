<?php
namespace controllers;

    class RedbagController{
        public function rob_view()
        {
            view('redbag.rob');
        }

        public function rob()
        {
            //判断登录
            if(!isset($_SESSION['id']))
            {
                echo json_encode([
                    'status_code'=>'401',
                    'message'=>'未登录'
                ]);
                exit;
            }

            //判断时间9~10之间
            if(date ('H')<9 || date ('H')>20)
            {
                echo json_encode([
                    'status_code'=>'403',
                    'message'=>'时间段不允许'
                ]);
                exit;
            }

            //判断今天是否已经抢过
            $key = 'redbag_'.date('Ymd');
            $redis = \libs\Redis::getInstance();
            $exists = $redis->sismember($key,$_SESSION['id']);
            if($exists)
            {
                echo json_encode([
                    'status_code'=>'403',
                    'message'=>'今天已经抢过了'
                ]);
                exit;
            }

            //减少库存量（-1），并返回 减完之后的值
            $stock = $redis->decr('redbag_stock');
            if($stock<0)
            {
                echo json_encode([
                    'status_code'=>'403',
                    'message'=>'今天红包已经抢完了，明天再来哦'
                ]);
                exit;
            }

            //下单(放到队列)
            $redis->lpush('redbag_orders',$_SESSION['id']);

            //把ID放到集合中（代表已经抢过了）
            $redis->sadd($key,$_SESSION['id']);

            echo json_encode([
                'status_code'=>'200',
                'message'=>'红包已到手'
            ]);


            
        }

        public function init()
        {
            $redis = \libs\Redis::getInstance();
            //初始化库存量
            $redis->set('redbag_stock',20);
            //初始化空的集合
            $key = 'redbag_'.date('Ymd');
            $redis->sadd($key,-1);
            //设置过期
            $redis->expire($key,3900);
        }

        //监听消息队列
        public function makeOrder()
        {
            $redis = \libs\Redis::getInstance();
            $model = new \models\Redbag;
            // 设置 socket 永不超时
            ini_set('default_socket_timeout', -1); 
            echo "开始监听红包队列... \r\n";

            while(true)
            {
                $data = $redis->brpop('redbag_orders',0);
                $userId = $data[1];

                //下订单
                $model->create($userId);
                echo "有人抢了红包！ \r\n";
            }

        }
    }