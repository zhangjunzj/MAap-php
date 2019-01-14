<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";
    require "./extends/Image.class.php";
    require "./extends/Upload.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept'); 
    // 项目排序
    $itemListModel = new Model('m_itemlist');

    $keyfile = fopen("key.txt", "r") or die("Unable to open file!");
    $key = fread($keyfile, 100);
    if (empty($_POST['key']) || $_POST['key'] != $key) {
        $result['code'] = -1;
        $result['message'] = '未登录';
        echo json_encode($result);
        return;
    }
    
    switch ($_GET['action']) {
        case 'sort': 
            if ($_POST) {
                $item = $itemListModel->find($_POST['curid']);
                $nextItem = $itemListModel->find($_POST['nextid']);

                $tempObj['i_title'] =  $item['i_title'];
                $tempObj['i_introduce'] =  $item['i_introduce'];
                $tempObj['uid'] =  $item['uid'];

                $item['i_title'] = $nextItem['i_title'];
                $item['i_introduce'] =  $nextItem['i_introduce'];
                $item['uid'] =  $nextItem['uid'];

                $nextItem['i_title'] =  $tempObj['i_title'];
                $nextItem['i_introduce'] =  $tempObj['i_introduce'];
                $nextItem['uid'] =  $tempObj['uid'];

             


                if ($itemListModel->save($item) > 0 && $itemListModel->save($nextItem) > 0)  {
                    $result['code'] = 1;
                    $result['message'] = '排序成功';
                } else {
                    $result['code'] = 2;
                    $result['message'] = '排序失败';
                }
            } else {
                $result['code'] = 3;
                $result['message'] = '排序失败';
            }
            
            break;
        
    }

    echo json_encode($result);

?>