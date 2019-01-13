<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";
    require "./extends/Image.class.php";
    require "./extends/Upload.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept'); 
    // 项目添加、删除、修改
    $itemListModel = new Model('m_itemlist');
    $imageModel = new Model('item_imgs');

    $keyfile = fopen("key.txt", "r") or die("Unable to open file!");
    $key = fread($keyfile, 100);
    if (empty($_POST['key']) || $_POST['key'] != $key) {
        $result['code'] = -1;
        $result['message'] = '未登录';
        echo json_encode($result);
        return;
    }
    
    switch ($_GET['action']) {
        case 'add':
            if ($_POST) {
                $temObj['i_title'] = $_POST['title'];
                $temObj['i_introduce'] = $_POST['introduce'];
                $addedItemObj = $itemListModel->add($temObj);
                if ($addedItemObj > 0) {
                    $result['code'] = 1;
                    $result['id'] = json_encode($addedItemObj);
                    $result['message'] = '添加成功';
                } else {
                    $result['code'] = 0;
                    $result['message'] = '添加失败';
                }
            } else {
                $result['code'] = 3;
                $result['message'] = '添加失败';
            }
            
            break;
        case 'edit': 
            if ($_POST) {
                $tmpObj['i_id'] = $_POST['id'];
                $tmpObj['i_title'] = $_POST['title'];
                $tmpObj['i_introduce'] = $_POST['introduce'];
                if ($itemListModel->save($tmpObj) > 0) {
                    $result['code'] = 1;
                    $result['message'] = '修改成功';
                } else {
                    $result['code'] = 2;
                    $result['message'] = '修改失败';
                }
            } else {
                $result['code'] = 3;
                $result['message'] = '修改失败';
            }
            
            break;
        case 'del': 
            $itemImgArr = $imageModel->where("item_id={$_POST['id']}")->select();
            foreach($itemImgArr as $imgVal) {
               $imageModel->delete($imgVal['id']);
            };
            if ($itemListModel->delete($_POST['id']) > 0) {
                $result['code'] = 1;
                $result['message'] = '删除成功';
            } else {
                $result['code'] = 2;
                $result['message'] = '删除失败';
            }
            break;
        case 'queryitemimg':
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
        case 'delimg':
            if ($imageModel->delete($_POST['id'])) {
                $result['code'] = 1;
                $result['message'] = '图片删除成功';
            } else {
                $result['code'] = 0;
                $result['message'] = '图片删除失败';
            }
            break;

        case 'imgadd':
            $upload = new Upload();
            $upload->maxSize   =     0;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './images/'; // 设置附件上传根目录
            $upload->savePath  =     'item/'; // 设置附件上传（子）目录
            // 上传文件 
            $info = $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $result['code'] = 2;
                $result['message'] = $upload->getError();
                $result['data'] = json_encode($_FILES);
            }else{// 上传成功
                $imgObj['path'] = $info['file']['savepath'];
                $imgObj['url'] = $info['file']['savename'];
                $imgObj['item_id'] = $_POST['targetId'];
                // 保存图片相对路径
                if($imageModel->add($imgObj) > 0) {
                    $result['code'] = 1;
                    $result['message'] = json_encode($_POST); 
                }else{
                    $result['code'] = 3;
                    $result['message'] = 'image upload error';
                }
            }
            break;
        case 'queryitem':
            $item = $itemListModel->find($_POST['id']);
            $itemimg = $imageModel->where("item_id={$_POST['id']}")->select();
            if ($item) {
                foreach($itemimg as $imgval) {
                    $imgTmpObj['url'] = './images/'.$imgval['path'].$imgval['url'];
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
        
    }

    echo json_encode($result);

?>