<?php
if(isset($_COOKIE["login_true"])==false){
        session_start();
        $_SESSION["last_url"]="drug.php";
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

<title>易过期产品</title>
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


function hideme(){
	$('#oLayer').hide();
}



	$(document).ready(function(){
		
		list_drug();

		$.each($('.nav li'),function(name,value){
			$(this).click(function(){
				var len = $('.nav li').length;
				for(var i=0;i<len;i++){
					$('.nav li').eq(i).removeClass('active');
				}
				$(this).addClass('active');
			});
		});
		
		
		get_going_expire_drug();
		
	});		
	
	function get_drug(d_id){
                clear_info();
		$.getJSON('drug_action.php',{action:'get_drug',d_id:d_id,r:Math.random(),page_name:'drug.php'},
			function(data){	
                            
				$('#edit_d_id').val(data.d_id);				      
                                $('#d_name').val(data.d_name);
                                $('#d_model_made').val(data.d_model_made);
                                $('#d_remark').val(data.d_remark);
                                $('#in_date').val(data.in_date);
                                $('#expire_date').val(data.expire_date);
                                $('#del_flag').val(data.del_flag);
                                $('#add_date').val(data.add_date);
                            });
		$('#add_div').show();
		$('#list_div').hide();
            
			
	}
	
	function save_drug(){
                
                  var d_id =  $('#edit_d_id').val();
                var d_name =  $('#d_name').val();
                var d_model_made =  $('#d_model_made').val();
                var d_remark =  $('#d_remark').val();
                var in_date =  $('#in_date').val();
                var expire_date =  $('#expire_date').val();
                var del_flag =  $('#del_flag').val();
                var add_date =  $('#add_date').val();
                

		$.post('drug_action.php?action=save_drug',{
			d_id : d_id,
                        d_name : d_name,
                        d_model_made : d_model_made,
                        d_remark : d_remark,
                        in_date : in_date,
                        expire_date : expire_date,
                        del_flag : del_flag,
                        add_date : add_date,
                        page_name:'drug.php',
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
            
              $('#edit_d_id').val('');
    	          $('#d_name').val('');
    	          $('#d_model_made').val('');
    	          $('#d_remark').val('');
    	          $('#in_date').val('');
    	          $('#expire_date').val('');
    	          $('#del_flag').val('');
    	          $('#add_date').val('');
		
	}

	function list(page_id){
		 
		 var filter = $('#filter').val();
                 var time_type = $('#time_type').val();
		 var start_time = $('#start_time').val();
		 var end_time = $('#end_time').val();
                 var show_del = $('#show_del').val();
                 
		 last_page_id=page_id;
		 $.get('drug_action.php',
			{r:Math.random(),
			action:'list_drug',
			page_id:page_id,
			filter:filter,
                        time_type:time_type,
			start_time:start_time,
			end_time:end_time,	
                        show_del:show_del,
                        page_name:'drug.php'	 
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
	


	function list_drug(){
	 	$('#list_div').show();
		$('#add_div').hide();
	 	list(1);	
	 }

	 function del_drug(d_id){
		 if(confirm('确认要删除吗？')){
 			$.get('drug_action.php?action=del_drug',
		 	{r:Math.random(),d_id:d_id,page_name:'drug.php'},
		 	function(data){
				 if(data=='success'){
					go_page(last_page_id);
				 }
			 }
		 	);
		 }
		
	 }
         
      
      	function get_going_expire_drug(){
		$.get('drug_action.php',{action:'get_going_expire_drug',r:Math.random(),page_name:'drug.php'},
			function(data){	
			
                    $('#global_warning_info').html(data);        			            			
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
                                    <a href='javascript:void(0)' onclick='add_init()'>新增易过期产品</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='list_drug()'>易过期产品列表</a>
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
                            <input type='hidden' id='edit_d_id' value='0' />
                            <input type="text" id="d_name" />
                        </td>
                </tr>
                <tr>
			<td style='text-align:right;'>
			规格产地
                        </td>
                        <td style='text-align:left;'>
                            <input type="text" id="d_model_made" />
                        </td>
                </tr>
                  <tr>
			<td style='text-align:right;'>
			备注
                        </td>
                        <td style='text-align:left;'>
                            <input type="text" id="d_remark" />
                        </td>
                </tr>
                  <tr>
			<td style='text-align:right;'>
			入库日期
                        </td>
                        <td style='text-align:left;'>
                            <input type="text" id="in_date" class='Wdate' onClick='WdatePicker()'/>
                        </td>
                </tr>
                  <tr>
			<td style='text-align:right;'>
			失效期
                        </td>
                        <td style='text-align:left;'>
                            <input type="text" id="expire_date" class='Wdate' onClick='WdatePicker()'/>
                        </td>
                </tr>
                 <tr>
			<td style='text-align:right;'>
			删除标识
                        </td>
                        <td style='text-align:left;'>
                            <select id="del_flag">
                                <option value="0">否</option>
                                <option value="1">是</option>                                
                            </select>
                        </td>
                </tr>
                  <tr>
			<td style='text-align:right;'>
			添加日期
                        </td>
                        <td style='text-align:left;'>
                             <input type="text" id="add_date"   class='Wdate' onClick='WdatePicker()'/>
                        </td>
                </tr>                       
                          
		</tbody>
		</table>
	<br />

	
	<input type='button' value='保存并跳转至列表' onclick='save_drug()'/>
		<input type='button' value='返回' onclick='ret()'/>


	                </div>

	    <div id='list_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
		
<table id='search_tbl'  class='table table-bordered table-striped table-hover'>
	<tr><td>


		筛选：<input id='filter' name='filter' style='width:150px;' />
                &nbsp;
                时间类型：<select id="time_type" style="width:150px;">
                    <option value="1">入库时间</option>
                    <option value="2">失效时间</option>
                    <option value="3">录入时间</option>
                </select>
                &nbsp;
		开始时间：<input id='start_time' name='start_time' style='width:100px;' class='Wdate' onClick='WdatePicker()' />
                &nbsp;
		结束时间：<input id='end_time' name='end_time' style='width:100px;' class='Wdate' onClick='WdatePicker()' />
                &nbsp;
                显示删除记录：<select id="show_del" style="width:150px;">
                    <option value="1">不显示</option>
                    <option value="0">显示</option>                    
                </select>
		<input type='button' value='搜索' onclick='list_drug()' />

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

	
