<?php
	
	function warning_handler($errno, $errstr) { 
// do something
}

/**
 * 加载sql文件为分号分割的数组
 * 支持存储过程和函数提取，自动过滤注释
 * @param string $path 文件路径
 * @return boolean|array
 * @since 1.0 <2015-5-27> SoChishun Added.
 */
function load_sql_file($path, $fn_splitor = ';;') {
    if (!file_exists($path)) {
        return false;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $arr = false;
    $str = '';
    $skip = false;
    $fn = false;
    foreach ($lines as $line) {
        $line = trim($line);
        // 过滤注释
        if (!$line || 0 === strpos($line, '--') || 0 === strpos($line,'*') || 0 === strpos($line, '/*') || (false !== strpos($line, '*/') && strlen($line) == (strpos($line, '*/') + 2))) {
            if (!$skip && 0 === strpos($line, '/*')) {
                $skip = true;
            }
            if ($skip && false !== strpos($line, '*/') && strlen($line) == (strpos($line, '*/') + 2)) {
                $skip = false;
            }
            continue;
        }
        if ($skip) {
            continue;
        }
        // 提取存储过程和函数
        if (0 === strpos($line, 'DELIMITER ' . $fn_splitor)) {
            $fn = true;
            continue;
        }
        if (0 === strpos($line, 'DELIMITER ;')) {
            $fn = false;
            $arr[] = $str;
            $str = '';
            continue;
        }
        if ($fn) {
            $str.=$line . ' ';
            continue;
        }
        // 提取普通语句
        $str.=$line;
        if (false !== strpos($line, ';') && strlen($line) == (strpos($line, ';') + 1)) {
            $arr[] = $str;
            $str = '';
        }
    }
    return $arr;
}

    require_once ( 'mysql.class.php');
    $action = $_GET['action'];
      if($action=='save_db_conf'){
 	 $db_addr = $_POST['db_addr'];
 	 $db_name = $_POST['db_name'];	
 	 $user_id = $_POST['user_id'];
 	 $passwd = $_POST['passwd'];
 	 $prefix = $_POST['prefix'];
 	 $admin_userid = $_POST['admin_userid'];
 	 $admin_passwd = $_POST['admin_passwd'];
 	  $content = file_get_contents('data/config.sample.php');
 	  $content =str_replace('$$dbhost',$db_addr,$content);
 	  $content =str_replace('$$dbname',$db_name,$content);
 	 $content =str_replace('$$dbuser',$user_id,$content);
 	 $content =str_replace('$$dbpass',$passwd,$content);
 	 $content = str_replace('$$prefix',$prefix,$content);
 	 $content = iconv("ASCII","UTF-8",$content);
 	 
 	 if(file_exists('data/config.php')){
 	 	 $new_file_name="data/config_".date("YmdHis").".php";
 	 	 copy('data/config.php',$new_file_name);
 	 }
 	 file_put_contents('data/config.php',$content);
 	 
 	 set_error_handler("warning_handler", E_WARNING);
 	 
 	 $link1 = mysql_pconnect($db_addr, $user_id, $passwd);
 	 
 	 if($link1){
 	 	if (!mysql_select_db($db_name, $link1)) {
 	 		echo "no_db";
        	}  else {
        	 	echo "success";
        	}     
        } else {
        	echo "连接数据库失败";
        }
 	 restore_error_handler();
 	 
    }  
       else if($action=='get_db_conf'){
         
         } else if($action=='create_db'){
         	$db_addr = $_POST['db_addr'];
 	 	$db_name = $_POST['db_name'];	
 	 	$user_id = $_POST['user_id'];
 	 	$passwd = $_POST['passwd'];
 	 	$sql = "create database `".$db_name."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
 	 	$link1 = mysql_pconnect($db_addr, $user_id, $passwd);
 	 	if($link1){
 	 		$result = mysql_query($sql, $link1);
 	 		if($result){
 	 			echo "success";
 	 		} else {
 	 			echo "创建数据库失败";
 	 		}
 	 	}
         } else if($action=='create_table'){
         	 require_once ( 'data/config.php');
         	 $arr = load_sql_file('data/create_table.sql');
         	 $db = new mysql($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
         	 if($db ){
         	 	 $isError = false;
         	    foreach ($arr as $line) {
         	    	  $content =str_replace('$prefix_',constant("TABLE_PREFIX"),$line);
         	    		$result = $db->query($content);
         	    		if($result){
	         	 		
	         	 	} else {
	         	 		echo "执行出错（“.$content.”）";
	         	 		$isError = true;
	         	 	}
         	    }
              if($isError==false)
              	  echo "success";
         
         	 
 		
         	 
         	 } else {
         	 	echo "数据库连接出错";
         	}
         	 
         	 
         }
      
?>