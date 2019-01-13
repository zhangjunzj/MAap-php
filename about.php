<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";
    require "./extends/Image.class.php";
    require "./extends/Upload.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers:Origin, X-Requested-With, Content-Type, Accept'); 
    // 关于我们
    $aboutModel = new Model('m_about');

    $keyfile = fopen("key.txt", "r") or die("Unable to open file!");
    $key = fread($keyfile, 100);
    if (empty($_POST['key']) || $_POST['key'] != $key) {
        $result['code'] = -1;
        $result['message'] = '未登录';
        echo json_encode($result);
        return;
    }
    
    switch ($_GET['action']) {
        case 'query':
            $item = $aboutModel->limit(0, 1)->select();
            if ($item) {
                $result['code'] = 1;
                $result['data'] = $item;
                $result['message'] = '查询成功';
            } else {
                $result['code'] = 0;
                $result['message'] = '查询失败';
            }
            break;
        case 'edit': 
            if ($_POST) {
                $tmpObj['id'] = $_POST['id'];
                $tmpObj['introduce'] = $_POST['introduce'];
                $tmpObj['address'] = $_POST['address'];
                $tmpObj['phone'] = $_POST['phone'];
                $tmpObj['email'] = $_POST['email'];
                $tmpObj['keyword'] = $_POST['keyword'];
                $tmpObj['description'] = $_POST['description'];
                $tmpObj['time'] = $_POST['time'];
                if ($aboutModel->save($tmpObj) > 0) {
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
        case 'imgupdate':
            $upload = new Upload();
            $upload->maxSize   =     0;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './images/'; // 设置附件上传根目录
            $upload->savePath  =     'about/'; // 设置附件上传（子）目录
            // 上传文件 
            $info = $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $result['code'] = 2;
                $result['message'] = $upload->getError();
                $result['data'] = json_encode($_FILES);
            }else{// 上传成功
                $oldItem = $aboutModel->limit(0, 1)->select();
                $imgObj = $oldItem[0];
                $imgObj['imgurl'] = 'images/'.$info['file']['savepath'].$info['file']['savename'];
                // 保存图片相对路径
                if($aboutModel->save($imgObj) > 0) {
                    $result['code'] = 1;
                    $result['message'] = json_encode($_POST); 
                }else{
                    $result['code'] = 3;
                    $result['message'] = 'image upload error';
                }
            }
            break;
    }

    echo json_encode($result);

?>