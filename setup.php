<?php
    

                                                     	 
                                                     	 
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
<script src="common.js"></script>

<title>数据库配置</title>
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

    function save_db_conf(){
    	var db_addr = $('#txt_db_addr').val();
    	var db_name = $('#txt_dbname').val();
    	var user_id = $('#txt_userid').val();
    	var passwd = $('#txt_pwd').val();
    	var prefix = $('#txt_prefix').val();
    	var admin_user_id = $('#txt_admin_userid').val();
    	var admin_passwd = $('#txt_admin_pwd').val();
        if(db_addr==''){
        	$('#e_db_addr').html("请输入数据库地址");
        	$('#c_db_addr').addClass('error');
    		return false;
    	} else {
    		$('#e_db_addr').html("");
    		 $('#c_db_addr').removeClass('error');
    	}
    	if(db_name==''){
    		$('#e_dbname').html("请输入数据库名");
    		$('#c_dbname').addClass('error');
    		return false;
    	} else {
    		$('#e_dbname').html("");
    		$('#c_dbname').removeClass('error');
    	}
    	if(user_id==''){
    		$('#e_userid').html("请输入用户名");
    		$('#c_userid').addClass('error');
    		return false;
    	} else {
    		$('#e_userid').html("");
    		$('#c_userid').removeClass('error');
    	}
    	if(passwd==''){
    		$('#e_pwd').html("请输入密码");
    		$('#c_pwd').addClass('error');
    		return false;
    	} else {
    		$('#e_pwd').html("");
    		$('#c_pwd').removeClass('error');
    	}
    	if(prefix==''){
    		$('#e_prefix').html("请输入表前缀");
    		$('#c_prefix').addClass('error');
    		return false;
    	} else {
    		$('#e_prefix').html("");
    		$('#c_prefix').removeClass('error');
    	}
    	if(admin_user_id==''){
    		$('#e_admin_userid').html("请输入管理用户名");
    		$('#c_admin_userid').addClass('error');
    		return false;
    	} else {
    		$('#e_admin_userid').html("");
    		$('#c_admin_userid').removeClass('error');
    	}
    	if(admin_passwd==''){
    		$('#e_admin_pwd').html("请输入管理密码");
    		$('#c_admin_pwd').addClass('error');
    		return false;
    	} else {
    		$('#e_admin_pwd').html("");
    		$('#c_admin_pwd').removeClass('error');
    	}
    	
    	$.post('setup_action.php?action=save_db_conf',
    	{db_addr:db_addr,db_name:db_name,user_id:user_id,passwd:passwd,
    		prefix:prefix,
    		admin_userid:admin_user_id,admin_passwd:admin_passwd,
	    		r:Math.random()},
    		function(data){
    			if(data=='success'){
    				$('#div1').hide();
    				create_table();
    				
    			} else if(data=='no_db'){
    			 	$('#div1').hide();
    			 	$('#div2').show();
    			}else {
    				alert(data);
    			}
    		});
    }
    
    function create_table(){
    	var url = 'setup_action.php?action=create_table';
    	var para = {r:Math.random()};
    	$.get(url,para,function(data){
    		if(data=='success'){
				create_admin_user();
    			
    		} else {
    			$('#div3').html(data);
    			$('#div3').show();
    		}
    	
    	
    	});
    }
    
    function create_admin_user(){
    	 var admin_user_id = $('#txt_admin_userid').val();
    	 var admin_passwd = $('#txt_admin_pwd').val();
    	 var url = 'login_action.php?action=create_admin_user';
    	 var para  = {r:Math.random(),user_id:admin_user_id,passwd:admin_passwd};
    	 $.post(url,para,function(data){
    	 	 if(data=='success'){
				$('#div3').show();
    	 	 } else {
    	 	 	alert(data);
    	 	 }
    	 });
    }
    
    function create_db(){
    	 	var db_addr = $('#txt_db_addr').val();
	    	var db_name = $('#txt_dbname').val();
	    	var user_id = $('#txt_userid').val();
	    	var passwd = $('#txt_pwd').val();
	    	var url = 'setup_action.php?action=create_db';
	    	var para = {db_name:db_name,
	    	db_addr:db_addr,
	    	user_id:user_id,
	    	passwd:passwd,
	    	r:Math.random()};
	    	$.post(url,para,function(data){
	    		if(data=='success'){
	    			$('#div2').hide();
	    			create_table();
	    			create_admin_user();
	    		}else {
	    			alert(data);
	    		}
	    	});
    }
    
    function cancel_create_db(){
    		$('#div1').show();
    		$('#div2').hide();
    }

	$(document).ready(function(){
		

	});

</script>
</head>
<body>
	<div id='wrapper'>


	<div class="navbar navbar-fixed-top" id='top_div'>
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href='javascript:void(0)'>安装向导</a>
                 <ul class="nav">
                           
                        </ul>
                    </div>
	
	
	
	      	  
	      	  	</div>
	      	  		
	      	  		    <div class="container-fluid">
                    <div class="row-fluid">
                    		
                    		
									<div style='display:none;'>		
									系统检查：  <br />
									操作系统：<?php
									    
									echo PHP_OS;
									                                                     	 
									                                                     	 
									?><br />
									应用服务器：<?php  echo $_SERVER['SERVER_SOFTWARE']; ?><br />
									php版本：<?php  echo PHP_VERSION; ?><br />
									支持mysql：<?php  echo  extension_loaded('mysql') ? "是" : "否"; ?>
									<br />
									开启Socket：<?php echo function_exists('fsockopen') ? "是" : "否";  ?>
									<br />
									时区：<?php echo  function_exists("date_default_timezone_get") ? date_default_timezone_get() : "未找到默认时区"; ?>
									<br />
									</div>
	
	<div id='div1' class='block'>
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left"> &nbsp;&nbsp;数据库配置</div>
                            </div>
                            <div class="block-content collapse in">        
         <form class="form-horizontal">
        
           <div class="control-group" id="c_db_addr">
                                          <label class="control-label" for="focusedInput">数据库地址</label>
                                          <div class="controls">
                                            <input class="input-xlarge focused" id="txt_db_addr" type="text" value="">
                 <span class="help-inline" id="e_db_addr"></span>
                                          </div>
                                        </div>
        
                   <div class="control-group" id="c_dbname">
                                          <label class="control-label" for="focusedInput">数据库名</label>
                                          <div class="controls">
                                            <input class="input-xlarge focused" id="txt_dbname" type="text" value="">
                       <span class="help-inline" id="e_dbname"></span>
                                          </div>
                                        </div>

		<div class="control-group" id="c_userid">
                                          <label class="control-label" for="focusedInput">用户名</label>
                                          <div class="controls">
                                            <input class="input-xlarge focused" id="txt_userid" type="text" value="">
            <span class="help-inline" id="e_userid"></span>
                                          </div>
                                        </div>

			<div class="control-group" id="c_pwd">
                                          <label class="control-label" for="focusedInput">密码</label>
                                          <div class="controls">
                                            <input class="input-xlarge focused" id="txt_pwd" type="password" value="">
                <span class="help-inline" id="e_pwd"></span>
                                          </div>
                                        </div>
               
               	<div class="control-group" id="c_prefix">
                                          <label class="control-label" for="focusedInput">表前缀</label>
                                          <div class="controls">
                                            <input class="input-xlarge focused" id="txt_prefix" type="text" value="ying_">
                    <span class="help-inline" id="e_prefix"></span>
                                          </div>
                                        </div> 
		</form>
	</div>	
	   <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">&nbsp;&nbsp;管理配置</div>
                            </div>
		    <div class="block-content collapse in">  
		    		  <form class="form-horizontal">
		    		
		    			<div class="control-group" id="c_admin_userid">
                                          <label class="control-label" for="focusedInput">用户名</label>
                                          <div class="controls">
                                            <input class="input-xlarge focused" id="txt_admin_userid" type="text" value="">
                            <span class="help-inline" id="e_admin_userid"></span>
                                          </div>
                                        </div>
                            
				<div class="control-group" id="c_admin_pwd">
                                          <label class="control-label" for="focusedInput">密码</label>
                                          <div class="controls">
                                            <input class="input-xlarge focused" id="txt_admin_pwd" type="password" value="">
                    <span class="help-inline" id="e_admin_pwd"></span>
                                          </div>
                                        </div>
                    
                     <div class="form-actions">
                                     	<input type='button' value='保存配置并继续' onclick='save_db_conf()' class="btn btn-primary" />
                                        </div>
					
		
	

		 </form>
	     </div>
	     	 </div>
	
		<div id='div2' style='display:none'>
			
			 <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">&nbsp;&nbsp;</div>
                            </div>
		    <div class="block-content collapse in">  
		    	 	  <form class="form-horizontal">
		    	 
		    	 	<div class="control-group">
                                      
                                          <div class="controls">
                                        	<span id='span2'>数据库不存在，是否创建数据库？</span>
                                          </div>
                                        </div>
                                        	  
                                        	     <div class="form-actions">
                                    	<input type='button' value='是' onclick='create_db()' class="btn btn-primary" />
			<input type='button' value='否' onclick='cancel_create_db()' class="btn btn-primary" />
                                        </div>
		    	  </form>
		  </div>
			
			
		</div>
		<div id='div3'  style='display:none'>
		 		
		 		<div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">&nbsp;&nbsp;</div>
                            </div>
		    <div class="block-content collapse in">  
		    	 	  <form class="form-horizontal">
		    	 
		    	 	<div class="control-group">
                                          <div class="controls">
                                       安装成功！
                                          </div>
                                        </div>
                                        	  
                                        	  	<div class="control-group">
                                          <div class="controls">
                                       	<a href='login.php'>跳转到后台登陆页面。</a>
                                       				
                                          </div>
                                        </div>
		    	  </form>
		 		
		 		
		</div>
	</div>
		
		</div>

	</div>
</body>
</html>

	
