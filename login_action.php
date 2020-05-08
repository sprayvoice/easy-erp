<?php
ini_set("display_errors", "On");

require_once(dirname(__FILE__).'/data/config.php');
require_once (dirname(__FILE__). '/db/db_login.class.php');
require_once(dirname(__FILE__).'/db/db_admin_login.class.php');
require_once ( dirname(__FILE__).'/db/mysqli.class.php');

require_once ( 'filter.php');

function GetIP() {
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
        $cip = $_SERVER["REMOTE_ADDR"];
    } else {
        $cip = "无法获取！";
    }
    return $cip;
}

$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_login = new db_login($db);
$cls_admin_login = new db_admin_login($db);

$action = $_GET['action'];
if ($action == 'login') {
    $uid = $_POST['uid'];
    $pwd = $_POST['upwd'];
    $rememberme = $_POST['rememberme'];
    $ip = GetIP();

    $result_admin_login = $cls_admin_login->insert_or_update($ip);
    if ($result_admin_login != "success") {
        echo $result_admin_login;
        return;
    }

    $result = $cls_login->login($uid, $pwd);
    if ($result) {

        $result = $cls_login->update_login_time_and_ip($uid, $ip);
        if ($result == false) {
            echo "更新登陆时间和ip失败";
        }
        $expire = time() + 86400 * 7 * 30;
        setcookie("user_id", $uid, $expire);
        if ($rememberme == '1') {
            setcookie("passwd", $pwd, $expire);
            setcookie("rememberme", "1", $expire);
        } else {
            setcookie("passwd", false);
            setcookie("rememberme", false);
        }
        setcookie("login_true", 'true', $expire);
        echo "success";
    } else {
        echo "用户名或密码错误";
    }
} else if ($action == 'logout') {
    setcookie("login_true", false);
    echo "logout";
} else if ($action == 'update_user_and_pwd') {
    if (!isset($_COOKIE["login_true"])) {
        echo "用户登陆超时";
    }
    $uid = $_COOKIE["user_id"];
    $old_pass = $_POST['old_passwd'];
    $new_pass = $_POST['new_passwd'];
    $result = $cls_login->update_user_and_pwd($uid, $old_pass, $new_pass);
    if ($result)
        echo "success";
    else
        echo "更新错误(" . $result . ")";
} else if ($action == 'create_admin_user') {
    $uid = $_POST["user_id"];
    $pass = $_POST['passwd'];
    $result = $cls_login->create_user_and_pwd($uid, $pass);
    if ($result)
        echo "success";
    else
        echo "插入错误(" . $result . ")";
}
?>