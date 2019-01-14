<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";
    require "./extends/Image.class.php";
    require "./extends/Upload.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept'); 
    // 查询方法
    $itemListModel = new Model('m_itemlist');
    $imageModel = new Model('item_imgs');
    $newsListModel = new Model('m_newslist');

    switch ($_GET['action']) {
        case 'queryitemimg':
            //$imgdata = $image->where("pro_id={$_GET['id']}")->select();
            $imgarr = $imageModel->where("item_id={$_POST['itemId']}")->select();
            if ($imgarr) {
                foreach($imgarr as $imgval) {
                    $imgTmpObj['name'] = $imgval['id'];
                    $imgTmpObj['url'] = 'images/'.$imgval['path'].$imgval['url'];
                    $imgTmpArr[] = $imgTmpObj;

                }
                $result['code'] = 1;
                $result['data'] = $imgTmpArr;
                $result['message'] = '图片查询成功';
            } else {
                $result['code'] = 1;
                $result['message'] = '图片查询成功';
                $result['data'] = array();
            }
            break;
        case 'queryitem':
            $item = $itemListModel->find($_POST['id']);
            $itemimg = $imageModel->where("item_id='{$_POST['uid']}'")->select();
            if ($item) {
                foreach($itemimg as $imgval) {
                    $imgTmpObj['url'] = 'images/'.$imgval['path'].$imgval['url'];
                    $imgTmpArr[] = $imgTmpObj;
                }
                $item['images'] = $imgTmpArr;
                $result['code'] = 1;
                $result['data'] = $item;
                $result['message'] = '查询成功';
            } else {
                $result['code'] = 0;
                $result['message'] = '查询失败';
            }
            break;
        case 'querynews': 
            $news = $newsListModel->find($_POST['id']);
            if ($news) {
                $result['code'] = 1;
                $result['data'] = $news;
                $result['message'] = '查询成功';
            } else {
                $result['code'] = 0;
                $result['message'] = '查询失败';
            }
            break;
    }

    echo json_encode($result);

?>