<?php
if(isset($_COOKIE["login_true"])==false){
        session_start();
        $_SESSION["last_url"]="art_category.php";
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
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
<script src="common.js"></script>
<script src="ajaxfileupload.js" type="text/javascript"></script>
<title>记事分类</title>
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

var  active_index=0;

var last_selected_sel_category = '';
var last_selected_sel_category2 = '';
var pic_width = 300;

function hideme(){
	$('#oLayer').hide();
}


function get_category(c_id){
                clear_info();
                
           		$.getJSON('art_cat_action.php',
                    {action:'get_category',c_id:c_id,r:Math.random(),page_name:'art_category.php'},                    
					function(data){	                        
                  $('#c_id').val(data.cat_id);
                  $('#c_sort').val(data.cat_sort);
                  $('#c_name').val(data.cat_name);
                  $('#c_show_front').val(data.cat_show_front);
                  
                });     
                
               
           
		$('#add_div').show();
		$('#list_div').hide();
       
                
		
        
			
	}


	$(document).ready(function(){
		
		list_category();
              

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
	
      
	
	
	function save_category(){
                
            var c_id =  $('#c_id').val();
            var c_sort =  $('#c_sort').val();
            var c_name =  $('#c_name').val();
            var c_show_front = $('#c_show_front').val();

		$.post('art_cat_action.php?action=save_category',{
			         c_id : c_id,
             c_sort : c_sort,
             c_name : c_name,
             c_show_front:c_show_front,
                        page_name:'art_cat.php',
			r:Math.random()
		},function(data){
			if(data=='success'){				
				$('#list_div').show();
				$('#add_div').hide();
	 			list(last_page_id);	 
			} else {
				alert(data);
			}
		});
	}

	
	
	function add_init(){
		$('#add_div').show();
		$('#list_div').hide();
		clear_info();
	}
	
	function ret(){                
                clear_info();	
                $('#add_div').hide();
		$('#list_div').show();
		
	}
	
	
	
	function clear_info(){             
          $('#c_id').val('');
          $('#c_sort').val('');
          $('#c_name').val('');
          $('#c_show_front').val('1');
	}

	function list(page_id){
		 
		 var filter = $('#filter').val();     
		 last_page_id=page_id;
		 $.get('art_cat_action.php',
			{ r:Math.random(),
			  action:'list_category',
			  page_id:page_id,                        
			  filter:filter,  
              page_name:'art_cat.php'	 
			},
			function(data){				
				$('#list_div_tbl').html(data);
		 });
	 }

	var last_page_id = 1;

	function go_page(page_id){
		list(page_id);	
	}


	function hide_this(ele){
		$(ele).parent().parent().parent().parent().parent().remove();
	}
	


	function list_category(){
	 	$('#list_div').show();
		$('#add_div').hide();
	 	list(1);	
	 }

	 function del_category(d_id){
		 if(confirm('确认要删除吗？')){
 			$.get('art_cat_action.php?action=del_category',
		 	{r:Math.random(),d_id:d_id,page_name:'art_cat.php'},
		 	function(data){
				 if(data=='success'){
					go_page(last_page_id);
				 }
			 }
		 	);
		 }
		
	 }
	 
	 function move_up(cat_id){
	 	 $.get('art_cat_action.php?action=reorder_category',
	 	 {page_name:'art_cat.php',cat_id:cat_id,move_action:'up',r:Math.random()},
	 	 	 function(data){
	 	 	 	 if(data=='success'){
	 	 	 	 	 	go_page(last_page_id);
	 	 	 	 } else {
	 	 	 	 	alert(data); 
	 	 	 	 }
	 	 	 });
	 }
	 
	 function move_down(cat_id){
	 	  $.get('art_cat_action.php?action=reorder_category',
	 	 {page_name:'art_cat.php',cat_id:cat_id,move_action:'down',r:Math.random()},
	 	 	 function(data){
	 	 	 	 if(data=='success'){
	 	 	 	 	 	go_page(last_page_id);
	 	 	 	 }else {
	 	 	 	 	alert(data); 
	 	 	 	 }
	 	 	 });
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
                                    <a href='javascript:void(0)' onclick='add_init()'>新增记事分类</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='list_category()'>记事分类列表</a>
                                </li>
                            </ul>
                        </div>	 
	  
		</div>
		</div>
	<div id='add_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
	
	
	<table id='list_tb1'  class='table table-bordered table-striped table-hover'>
	<thead>
		<tr>
			<td style='text-align:right;'>
			名称
                        </td>
                        <td style='text-align:left;'>
                            <input type='hidden' id='c_id' value='0' />
                            <input type="text" id="c_name" />
                        </td>
                                                 
                </tr>
                <tr>
	
                  <tr>
			<td style='text-align:right;'>
			排序
                        </td>
                        <td style='text-align:left;'>
                            <input type="text" id="c_sort" />
                        </td>
                </tr>
                <tr>
                 	<td style='text-align:right;'>
							是否前台可见
                        </td>
                        <td style='text-align:left;'>
                           <select id='c_show_front'>
                      		<option value='1'>是</option>
                      		<option value='0'>否</option>
                        </td>
                </tr>
                 
                
            
                               
                          
		</tbody>
		</table>
	<br />

	
	<input type='button' value='保存并跳转至列表' onclick='save_category()'/>
		<input type='button' value='返回' onclick='ret()'/>


	                </div>

	    <div id='list_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
		
<table id='search_tbl'  class='table table-bordered table-striped table-hover'>
	<tr><td>


		筛选：<input id='filter' name='filter' style='width:150px;' />                                
                &nbsp;
                       
           
		<input type='button' value='搜索' onclick='list_category()' />

</td></tr></table>

				<div id='list_div_tbl'>

				</div>
		
	    		
	    					
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

	
