<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');

    // 分页查询新闻列表接口
    $newsListModel = new Model('m_newslist');
    
    $keyfile = fopen("key.txt", "r") or die("Unable to open file!");
    $key = fread($keyfile, 100);
    if (empty($_POST['key']) || $_POST['key'] != $key) {
        $result['code'] = -1;
        $result['message'] = '未登录';
        echo json_encode($result);
        return;
    }
    
    $list = $newsListModel->select();
    if ($list) {
        foreach ($list as $key => $value) {
            $temp['id'] = $value['id'];
            $temp['title'] = $value['title'];
            $temp['text'] = $value['text'];
            $temp['icon'] = $value['icon'];
            $temp['addtime'] = $value['addtime'];
            $temp['content'] = $value['content'];
            $tempArray[] = $temp;
        }
        $result['code'] = 1;
        $result['message'] = '查询成功';
        $result['data'] = $tempArray;
    } else {
        $result['code'] = 1;
        $result['message'] = '查询成功';
        $result['data'] = array();
    }

    echo json_encode($result);

?>