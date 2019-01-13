<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');

    $keyfile = fopen("key.txt", "r") or die("Unable to open file!");
    $key = fread($keyfile, 100);
    if (empty($_POST['key']) || $_POST['key'] != $key) {
        $result['code'] = -1;
        $result['message'] = '未登录';
        echo json_encode($result);
        return;
    }

    if (!empty($_POST)) {
        $userModel = new Model('m_user');
        $user = $userModel->limit(0, 1)->select();
        if ($user[0]['user_pwd'] !== $_POST['pwd']) {
            $result['code'] = 3;
            $result['message'] = '请输入正确的原始密码!';
            echo json_encode($result);
            return;
        }
        $temObj['user_id'] = $user[0]['user_id'];
        $temObj['user_name'] = $user[0]['user_name'];
        $temObj['user_pwd'] = $_POST['newpwd'];
        if ($userModel->save($temObj)>0) {
            $keyfile = fopen("key.txt", "r") or die("Unable to open file!");
            fwrite($keyfile, '');
            $result['code'] = 1;
            $result['message'] = '密码修改成功';
        } else {
            $result['code'] = 2;
            $result['message'] = '密码修改失败';
        }
    } else {
        $result['code'] = 0;
        $result['message'] = 'post不对';
    }

    echo json_encode($result);

?>