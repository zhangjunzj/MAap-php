<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');
    // 分页查询项目列表接口
    $itemListModel = new Model('m_itemlist');
    
    if (!empty($_POST)) {
        $keyfile = fopen("key.txt", "r") or die("Unable to open file!");
        $key = fread($keyfile, 100);
        if (empty($_POST['key']) || $_POST['key'] != $key) {
            $result['code'] = -1;
            $result['message'] = '未登录';
            echo json_encode($result);
            return;
        }

        $list = $itemListModel->select();
        if ($list) {
            foreach ($list as $key => $value) {
                $temp['id'] = $value['i_id'];
                $temp['title'] = $value['i_title'];
                $temp['introduce'] = $value['i_introduce'];
                $temp['uid'] = $value['uid'];
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
        
    } else {
        $result['code'] = 0;
        $result['message'] = '查询失败';
    }

    echo json_encode($result);

?>