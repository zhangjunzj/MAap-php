<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');


    function str_rand($length = 32, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
         if(!is_int($length) || $length < 0) {
            return false;
        }
         $string = '';
        for($i = $length; $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }
         return $string;
    }

    if (!empty($_POST)) {
        $userModel = new Model('m_user');
        $username = $_POST['username'];
        $pwd = $_POST['pwd'];
        $user_data = $userModel->where("user_name='{$username}'")->select();
        if (empty($user_data)) {
            $result['code'] = 2;
            $result['message'] = '用户名或密码错误';
        } else if ($pwd != $user_data[0]['user_pwd']) {
            $result['code'] = 3;
            $result['message'] = '用户名或密码错误';
        } else {
            $keyfile = fopen("key.txt", "w+") or die("Unable to open file!");
            $str = str_rand();
            $key = md5($str);
            fwrite($keyfile, $key);
            fclose($keyfile);
            $result['code'] = 1;
            $result['message'] = '登录成功';
            $result['data'] = $key;
        }
    } else {
        $result['code'] = 0;
        $result['message'] = 'post不对';
    }

    echo json_encode($result);

?>