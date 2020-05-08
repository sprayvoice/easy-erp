<?php

$url = "product.php";
session_start();
if(isset($_SESSION["last_url"])){
    $url = $_SESSION["last_url"];
}	

 if(isset($_COOKIE["login_true"])==true){     
    echo "<script language='javascript' type='text/javascript'>";
    echo "window.location.href='$url'";
    echo "</script>";
    return;
} else {
    echo "<script language='javascript' type='text/javascript'>";
    echo "var go_url='$url'";
    echo "</script>";
}

?><!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="keyword" content="">	
<link rel="shortcut icon" href="favicon.ico">
<link href="tpl/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="tpl/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
<link href="tpl/assets/styles.css" rel="stylesheet" media="screen">
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
<script src="tpl/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
<script type="text/javascript" src="tpl/vendors/jquery-1.9.1.min.js"></script>
<script src="tpl/bootstrap/js/bootstrap.min.js"></script>

<title>登陆</title>
<style type="text/css">
#wrapper {
  width: 100%;height:100%;
}
	 td {padding:5px;}
	 .tb1 {border:2px solid;border-spacing:0px; }
	 .tb1 th {margin:3px;padding:5px;border:1px gray solid;}
	 .tb1 td { border:1px gray solid; margin:2px;padding:3px; border-collapse : collapse;}
	 #span_tag { margin-left:50px;margin-right:50px;}
</style>	
<script type='text/javascript'>



	$(document).ready(function(){
			$('#btn_login').click(function(){
				return login();
				
			});
	



	});
	

	function login(){
		var id = $('#uid').val();
		var pwd = $('#upwd').val();
		rememberme = '1';
		if($('#urememberme').is(":checked")){
			rememberme = '1';
		} else {
			rememberme = '0';
		}
		$.post('login_action.php?action=login',
		{r:Math.random(),uid:id,upwd:pwd,rememberme:rememberme},
			function(data){
				if(data=='success'){                                    
					location.href=go_url;
				} else {
					alert(data);
				}
			});
		return false;
		
	}



	
</script>
</head>
  <body id="login">

  <div class="container">

      <form class="form-signin">
        <h2 class="form-signin-heading">请登录</h2>
        <input type="text" class="input-block-level" placeholder="用户名" id='uid'>
        <input type="password" class="input-block-level" placeholder="密码" id='upwd'>
        <label class="checkbox">
          <input type="checkbox" value="remember-me" id='urememberme'> 记住我
        </label>
        <button class="btn btn-large btn-primary" type="submit" id='btn_login'> 登 陆 </button>
      </form>

    </div> <!-- /container -->
    <script src="tpl/vendors/jquery-1.9.1.min.js"></script>
    <script src="tpl/bootstrap/js/bootstrap.min.js"></script>
<?php 
	if(isset($_COOKIE["rememberme"])==true){
		if($_COOKIE["rememberme"]=="1"){
			$uid=$_COOKIE['user_id'];
			$pwd = $_COOKIE['passwd'];
			echo "<script language='javascript' type='text/javascript'>";
			echo "$('#uid').val('".$uid."');";
			echo "$('#upwd').val('".$pwd."');";
			echo "$('#urememberme').attr('checked','true');";
			echo "</script>";
			
		} else {
			echo "<script language='javascript' type='text/javascript'>";
			echo "</script>";
		}
	} else {
			echo "<script language='javascript' type='text/javascript'>";
			echo "</script>";
	}
?>
</body>
</html>

	
