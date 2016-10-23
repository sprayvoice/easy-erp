<?php
if(isset($_COOKIE["user_id"])==false){
	$url = "login.php";
	echo "<script language='javascript' type='text/javascript'>";
	echo "window.location.href='$url'";
	echo "</script>";
	return;
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
<script src="common.js"></script>

<title>库存列表</title>
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
		list_pro();
		list_tags();
		$('#filter').keyup(function(){
			 list_pro();
		});
	
		$.each($('.nav li'),function(name,value){
			$(this).click(function(){
				var len = $('.nav li').length;
				for(var i=0;i<len;i++){
					$('.nav li').eq(i).removeClass('active');
				}
				$(this).addClass('active');
			});
		});

	});
	
	function changeSort(){
		list_pro();
	}

	
	function fill_tag(tag){
		$('#filter').val(tag);
		list_pro();	
	}
	
 	function list_tags(){
 		$.get('stock_action.php?action=list_tags',
 		{r:Math.random()},
 			function(data){
 				$('#span_tag').html(data);
 				
 				var top_div_height = $('#top_div').height()-30;	
                    		
				$('#list_div').css('padding-top',top_div_height+'px');
 			});
 	}
	 	
  	function pandian1(pro_id,ele){
  		var html = $(ele).parent().prev().prev().html();
  		html = html+'<input type="text" id="pandian_value" value="" style="width:50px;background-color:yellow;" /> ';
  		html = html + '<input type="button" id="pandian_save_btn" value="保存" onclick="save_pandian1('+pro_id+',this)" /> <input type="button" value="取消" onclick="cancel_pandian1(this)" id="pandian_cancel_btn" />';
  		$(ele).parent().prev().prev().html(html);
  		$('#pandian_value').focus();
  	}

	function save_pandian1(pro_id,ele){
		var value =  $('#pandian_value').val();
		if(value!=''){
			$.get('stock_action.php',
			{r:Math.random(),action:'save_stock',product_id:pro_id,stock_quantity:value},
				function(data){
					if(data!='success'){
						alert(data);
					}
				});
		}
		
		$('#pandian_cancel_btn').remove();
		$('#pandian_save_btn').remove();
		$('#pandian_value').remove();
		list_pro();
	}
	
	function cancel_pandian1(ele){
		$('#pandian_cancel_btn').remove();
		$('#pandian_save_btn').remove();
		$('#pandian_value').remove();
	}

	function ret(){
		list_pro();
	}
	function list_pro(){
	
		$('#list_div').show();
		var sort1 = $('#sort1').val();
		var filter1 = $('#filter').val();
		$.get('stock_action.php?action=list_pro',
		 {r:Math.random(),sort1:sort1,filter1:filter1},
		 	 function(data){
		 	 	$('#content_div').html(data);			
				   $('#tag_div').html('');
		 	 });
	}

	function clearId(){
		list_pro();
	

	}
	
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
                    <a class="brand" href='javascript:void(0)' onclick='fill_tag("")'>Admin Panel</a>
                 <ul class="nav">
                            <li class="active" id='id1'>
        				 <a href='javascript:void(0)' onclick='fill_tag("")'>库存列表</a>	
                            </li>
        			<li id='id2'>
        			 <a href='product.php' target='_blank'>商品列表</a>
        			</li>
						<li id='id3'>
        			 <a href='sales.php' target='_blank'>新增销售</a>
        			</li>
    	<li id='id4'>
        			 <a href='instock.php' target='_blank'>新增入库</a>
        			</li>
						<li id='id7'>
        			 <a href='javascript:void(0)' onclick="changepswd()">修改密码</a>
        			</li>

						<li id='id6'>
        			 <a href='javascript:void(0)' onclick="logout()">注销</a>
        			</li>
     
                        </ul>
                    </div>
	
	
		 
	      <div id='span_tag'></div>

	<div style='padding-left:0px;padding-top:0px;margin-left:50px;margin-right:50px;' id='list_search_div'>
	    	<input type='text' id='filter' />
	    	<input type='button' value='x' onclick='fill_tag("")' />
	    				<select id='sort1' name='sort1' onchange="changeSort()">
			<option value='1'>按序号顺序排序</option>
			<option value='2'>按序号倒叙排序</option>
			<option value='3'>按名称,规格,品牌排序</option>
			<option value='4' selected>按名称,品牌,规格排序</option>
		</select>
		
	
			</div>

		</div>
		</div>

	    <div id='list_div'>	    
		
	    			<div id='content_div' style='margin-left:50px;margin-right:50px;'>
	    				
	    			</div>
	    			
	    </div>
	    					
	    
	<div id='info' style='margin-left:50px;margin-right:50px;'>
		
		</div>
	</div>
			
				<div id='change_pswd_div' 
	style='z-index:99;background-color:white;position:fixed;width:250px;height:250px;left:200px;top:200px;
	padding-left:50px;padding-right:50px;padding-top:15px;padding-bottom:15px;display:none;'>
		
		<div style='text-align:right;'>
		<input type='button' value='x' onclick='close_change_pswd_div()' />
		</div>

		原密码：<input type='password' id='ori_passwd' /><br />
		
		新密码：<input type='password' id='new_passwd' /><br />
		
		确认新密码：<input type='password' id='re_new_passwd' /><br />

		<input type='button' value='确认' onclick='change_passwd_click()' />
        <br />
        	<span id='change_password_success_span' style='color:green;'></span>

	</div>
</body>
</html>

	
