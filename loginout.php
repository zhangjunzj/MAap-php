<?php
    require "./extends/config.php";
    require "./extends/Model.class.php";

    header("Content-Type: text/html;charset=utf-8");
    header('Access-Control-Allow-Origin: *');

    if (!empty($_POST)) {
        $keyfile = fopen("key.txt", "r") or die("Unable to open file!");
        $key = fread($keyfile, 100);
        if (empty($_POST['key']) || $_POST['key'] != $key) {
            $result['code'] = 0;
            $result['message'] = '退出失败';
        } else {
            fwrite($keyfile, '');
            $result['code'] = 1;
            $result['message'] = '退出成功';
        }
    } else {
        $result['code'] = 0;
        $result['message'] = 'post不对';
    }

    echo json_encode($result);

?>