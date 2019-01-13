<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";
    require "./extends/Image.class.php";
    require "./extends/Upload.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept'); 
    // 新闻添加、删除、修改
    $newsListModel = new Model('m_newslist');
    $newsImageModel = new Model('news_imgs');

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
                $temObj['title'] = $_POST['title'];
                $temObj['text'] = $_POST['text'];
                $temObj['content'] = $_POST['content'];
                $temObj['addtime'] = date("Y-m-d H:i:s");
                $addedNewsItem = $newsListModel->add($temObj);
                if ($addedNewsItem > 0) {
                    $result['code'] = 1;
                    $result['message'] = '添加新闻成功';
                    $result['id'] = json_encode($addedNewsItem);
                } else {
                    $result['code'] = 0;
                    $result['message'] = '添加新闻失败';
                }
            } else {
                $result['code'] = 2;
                $result['message'] = '添加新闻失败';
            }
            break;
        case 'edit': 
            if ($_POST) {
                $temObj['title'] = $_POST['title'];
                $temObj['content'] = $_POST['content'];
                $temObj['addtime'] = date("Y-m-d H:i:s");
                if ($newsListModel->save($tmpObj) > 0) {
                    $result['code'] = 1;
                    $result['message'] = '修改新闻成功';
                } else {
                    $result['code'] = 0;
                    $result['message'] = '修改新闻失败';
                }
            } else {
                $result['code'] = 1;
                $result['message'] = '修改新闻失败';
            }
            
            break;
        case 'del': 
            $newsImgArr = $newsImageModel->where("news_id={$_POST['id']}")->select();
            $newsImageModel->delete($newsImgArr[0]['id']);
            if ($newsListModel->delete($_POST['id']) > 0) {
                $result['code'] = 1;
                $result['message'] = '删除新闻成功';
            } else {
                $result['code'] = 2;
                $result['message'] = '删除新闻失败';
            }
            break;
        case 'imgadd':
            $upload = new Upload();
            $upload->maxSize   =     0;
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
            $upload->rootPath  =     './images/'; // 待合并为一个文件夹
            $upload->savePath  =     'news/';
            // 上传文件 
            $info = $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $result['code'] = 2;
                $result['message'] = $upload->getError();
            }else{// 上传成功
                $imgObj['path'] = $info['file']['savepath'];
                $imgObj['url'] = $info['file']['savename'];
                $imgObj['news_id'] = $_POST['targetId'];
                // 保存图片相对路径
                if($newsImageModel->add($imgObj) > 0) {
                    $result['code'] = 1;
                    $result['message'] = json_encode($_POST); 
                }else{
                    $result['code'] = 3;
                    $result['message'] = '新闻图片上传失败';
                }
            }
            break;
    }

    echo json_encode($result);

?>