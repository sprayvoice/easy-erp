<?php
if(isset($_COOKIE["login_true"])==false){
        session_start();
        $_SESSION["last_url"]="product_category.php";
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
 <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="tpl/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
<link href="tpl/assets/styles.css" rel="stylesheet" media="screen">
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
<script src="tpl/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
<script type="text/javascript" src="tpl/vendors/jquery-1.9.1.min.js"></script>
<script src="tpl/bootstrap/js/bootstrap.min.js"></script>
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
<script src="common.js"></script>

<title>分类</title>
<style type="text/css">
#wrapper {
  width: 100%;height:100%;
}
#list_tb1 th {text-align:center;}
	 td {padding:5px;}
	 .tb1 {border:2px solid;border-spacing:0px; }
	 .tb1 th {margin:3px;padding:5px;border:1px gray solid;}
	 .tb1 td { border:1px gray solid; margin:2px;padding:3px; border-collapse : collapse;}
	 #span_tag { margin-left:50px;margin-right:50px;}
</style>	
<script type='text/javascript'>


function hideme(){
	$('#oLayer').hide();
}



	$(document).ready(function(){
		
		list_pro();
                
                list_category_sel();
                
                give_category_to();

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
	
	
	
	
	
	
	

	function list(page_id){
		 
		 var filter = $('#filter').val();   
                 var category = $('#category_sel').val();
                 var display = $('#display_condition_sel').val();
		 last_page_id=page_id;
		 $.get('product_category_action.php',
			{r:Math.random(),
			action:'list_pro',
			page_id:page_id,
			filter:filter,
                        category:category,
                        display:display,
                        page_name:'product_category.php'	 
			},
			function(data){				
				$('#list_div_tbl').html(data);
		 });
	 }

	var last_page_id = 1;

	function go_page(page_id){
		list(page_id);	
	}

	function list_pro(){	 	
	 	list(1);	
	 }	
         
         function check_all(element){
             if($(element).is(":checked")){
                 $('input[name=chk]').prop('checked',true);
             } else {
                 $('input[name=chk]').prop('checked',false);
             }
         }
         
         
    function list_category_sel(){
        $.get('product_category_action.php?action=list_category_sel',
            {r:Math.random(),page_name:'product_category.php'},
                function(data){
                    $('#category_sel').html(data);         
                });
    }

    function give_category_to(){
        $.get('product_category_action.php?action=give_category_to',
            {r:Math.random(),page_name:'product_category.php'},
                function(data){
                 $('#give_category_to').html(data);             
            });        
    }
    
    function refresh_give_category_to(){
    	give_category_to();
    }
    
    function get_selected_ids(){
        var pro_id_str = '';
        var list = $('input[name=chk]');
        for(var i=0;i<list.length;i++){
            if($(list[i]).is(':checked')){
                pro_id_str += $(list[i]).val()+',';
            }
        }        
        return pro_id_str;
    }
    
    function exec_give_category(){
        var pro_id_str = get_selected_ids();
        var get_to_category = $('#give_category_to').val();
        $.post('product_category_action.php?action=change_category_to',
        {page_name:'product_category.php',pro_id_strs:pro_id_str,category_id:get_to_category,r:Math.random()},
        function(data1){
          if(data1=='success'){
              list(last_page_id);
              
          }  else {
              list(last_page_id);
              alert(data1);
          }
        }﻿
        );        
    }
    
    function exec_cancel_category(){
        var pro_id_str = get_selected_ids();
        $.post('product_category_action.php?action=cancel_category',
        {page_name:'product_category.php',pro_id_strs:pro_id_str,r:Math.random()},
        function(data1){
          if(data1=='success'){
              list(last_page_id);              
          }  else {
              list(last_page_id);
              alert(data1);
          }
        }﻿
        );
        
        
    }

</script>
</head>
<body>
	 <div id='wrapper' style="position:absolute;left:0px;top:0px;">
            <div class="navbar" id='top_div'>
                <div class="navbar-inner">
                  <?php include("top_div1.php") ?>                             
                
                  <div style="position:inline; float: right;">
                            <ul class="nav">                                  
                                <li>
                                    <a href='javascript:void(0)' onclick='list_pro()'>分类列表</a>
                                </li>
                            </ul>
                        </div>	 
	  
		</div>
		</div>
	

	    <div id='list_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
		
<table id='search_tbl'  class='table table-bordered table-striped table-hover'>
	<tr><td>


		筛选：<input id='filter' name='filter' style='width:150px;' />
                &nbsp;
                分类：<select id="category_sel">
                    
                </select>    
                
                &nbsp;
                显示
                <select id="display_condition_sel">
                    <option value="1">显示已分类商品</option>
                    <option value="2">显示全部商品</option>
                    <option value="3">显示未分类商品</option>
                </select>
                
                
                
		<input type='button' value='搜索' onclick='list_pro()' />

</td></tr></table>

				<div id='list_div_tbl'>

				</div>
                <div
                    <span>
                        &nbsp;
                    &nbsp;全选   &nbsp; 
                    </span>
                        <input type='checkbox' id='checkAll2' onclick='check_all(this)' />
                    &nbsp;&nbsp;
	    		归类为：
                        <select id="give_category_to">
                            
                        </select>
                        	 <a href="javascript:void(0)" onclick='refresh_give_category_to()'>
          <span class="glyphicon glyphicon-refresh"></span>
        </a>
                        &nbsp;&nbsp;
                        <input type="button" value='执行' onclick="exec_give_category()"/>
                        &nbsp;&nbsp;
                        <input type='button' value='删除所选归类' onclick="exec_cancel_category()" />
                        
	     	</div>
	<div id='info' style='margin-left:50px;margin-right:50px;'>
		
		</div>
	</div>
			
			
			<div id="oLayer" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
            width: 500px; display:none;">
                <div id='oLayer_content'>
                
                </div>
                
                <div style='float:right;'>
                	       <input type='button' value='x' onclick='hideme()' />
                	</div>
			</div>
    
      <div id="oLayerC" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
            width: 800px; display:none;">
                <div id='oLayer_contentC'>
                
                </div>
                
                <div style='float:right;'>
                	       <input type='button' value='x' onclick='hidemeC()' />
                	</div>
			</div>
					
						<?php
	require_once ( 'change_password.php');
		
		?>
</body>
</html>

	
