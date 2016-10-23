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

<title>商品列表</title>
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
		$('#location_edit_div').hide();
		$('#price_edit_div').hide();
		$('#filter').keyup(function(){
			 list_pro();
		});
		$('#location_div').toggle();
		$('#price_div').toggle();
		$('#price_div2').toggle();

	

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
	function add_init(){
			$('#mode1').val('add_pro');
			$('#pro').val('');
		 	$('#tag').val('');
		 	$('#model').val('');
		 	$('#made').val('');
		 	$('#add_div').show();
			$('#list_div').hide();		
	}
	
	function fill_tag(tag){
		$('#filter').val(tag);
		$('#editor_product_id').val('');
		list_pro();	
	}

	
	
	function copy1(product_id){	
			 $.getJSON('product_action.php?action=get_pro',
		 {pro_id:product_id,r:Math.random()},
		 function(data){
		 	$('#mode1').val('add_pro');
		 	 $('#pro').val(data.product_name);
		 	$('#tag').val(data.product_tags);
		 	$('#model').val(data.product_model);
		 	$('#made').val(data.product_made);
		 	$('#hid_pro_id').val('');
			$('#add_div').show();
			$('#list_div').hide();
			});		
	}
	
	
	
	function show_location(product_id){	
		$.get('product_action.php?action=get_location_by_pid',
			{pro_id:product_id,r:Math.random()},
			function(data){
			$('#hid_l_pro_id').val(product_id);
			 $('#location_div').html(data);
			 $('#location_edit_div').hide();
			 $('#location_div').show();
			 $('#price_div').hide();
			 $('#price_div2').html('');
			 $('#price_div2').hide();
			 $('#editor_product_id').val(product_id);
			 list_pro();
		});
	
	}
	
	function show_price(product_id){
		 $.get('product_action.php?action=get_price_by_pid',
			{pro_id:product_id,r:Math.random()},
			function(data){
			$('#hid_p_pro_id').val(product_id);
			 $('#price_div').html(data);
			 $('#price_edit_div').hide();
			 $('#price_div').show();
			 $('#location_div').hide();
			 $('#editor_product_id').val(product_id);
			 
			 	 $.getJSON('stock_action.php',
			 {r:Math.random(),action:'get_stock',product_id:product_id},
			 	 function(stock_data){
			 
			 	 	 $('#price_div2').html(
			 	 	 '库存单价：'+stock_data.stock_price+'<br />'
			 	 	 	 +'单位：'+stock_data.stock_unit+'<br />'
			 	 	 +'库存数量：'+stock_data.stock_quantity +'<br />'
			 	 	 	 +'盘点日期：'+stock_data.last_upd_date +'<br />'
			 	 	 );
			 	 	 $('#price_div2').show();
			 	 });
			 
			 list_pro();
			 
		
			 
		});
		
	}
 	function add_location(product_id){
 			 $.getJSON('product_action.php?action=get_pro',
		 {pro_id:product_id,r:Math.random()},
		 function(data){
		 		$('#hid_product_location_id').val('');
		 		$('#hid_l_pro_id').val(product_id);
		 	 	$('#span_pro').html(data.product_name);
		 		$('#span_model').html(data.product_model);
		 		$('#span_made').html(data.product_made);
		 		$('#location_edit_div').show();
				$('#price_edit_div').hide();
			});	
 		
 	}
 	
 	function add_price(product_id){
 			 $.getJSON('product_action.php?action=get_pro',
		 {pro_id:product_id,r:Math.random()},
		 function(data){
		 		$('#hid_product_price_id').val('');
		 		$('#hid_p_pro_id').val(product_id);
		 	 	$('#span_pro2').html(data.product_name);
		 		$('#span_model2').html(data.product_model);
		 		$('#span_made2').html(data.product_made);
		 		$('#price_edit_div').show();
				$('#location_edit_div').hide();
			});	
 		
 	}
 	
 	function save_product_location(){
 	       var pro_id = $('#hid_l_pro_id').val();
 	       var location_id =  $('#hid_product_location_id').val();
 		var location1 = $('#location').val();
		var quantity = $('#quantity').val();
		$.post('product_action.php?action=save_product_location',
			{id:location_id,pro_id:pro_id,location1:location1,quantity:quantity,r:Math.random()},
			function(data){
				if(data=='success'){
					show_location(pro_id);					
				} else {
					$('#info').html(data);
				}
			}); 		
 	}
 	
 	function save_product_price(){
 		var pro_id = $('#hid_p_pro_id').val();
 		var price_id = $('#hid_product_price_id').val();
 		var price_name = $('#price_name').val();
 		var price = $('#price').val();
 		var unit = $('#unit').val();
 		var is_hide = $('#is_hide').val();
 		$.post('product_action.php?action=save_product_price',
			{id:price_id,pro_id:pro_id,price_name:price_name,price:price,unit:unit,is_hide:is_hide,r:Math.random()},
			function(data){
				if(data=='success'){
					show_price(pro_id);
					$('#info').html('');					
				} else {
					$('#info').html(data);
				}
			}); 
 		
 	}
 	
 	function del_location(id){
 		var pro_id = $('#hid_l_pro_id').val();
 		$.get('product_action.php?action=del_location_by_id',
 		{id:id,r:Math.random()},
 			function(data){
 				if(data=='success'){
 					show_location(pro_id);
 					$('#info').html('');
 				}
 			});
 	}
 	
 	function del_price(id){
 		var pro_id = $('#hid_p_pro_id').val();
 		$.get('product_action.php?action=del_price_by_id',
 		{id:id,r:Math.random()},
 			function(data){
 				if(data=='success'){
 					show_price(pro_id);
 					$('#info').html('');
 				}
 			});
 	}
 	
 	function list_tags(){
 		$.get('product_action.php?action=list_tags',
 		{r:Math.random()},
 			function(data){
 				$('#span_tag').html(data);
 				
 					var top_div_height = $('#top_div').height()-30;	
                    		
					$('#add_div').css('padding-top',top_div_height+'px');
					$('#list_div').css('padding-top',top_div_height+'px');
		
 			});
 	}
	 	
  	function edit_location(id){
  	
		$.getJSON('product_action.php?action=get_location_by_id',
				{id:id,r:Math.random()},
			function(data){

				 $('#hid_product_location_id').val(id);
				 $('#location').val(data.product_location);
				 $('#quantity').val(data.product_quantity);				 
			 	 $('#location_edit_div').show();
			 	 
			 	 $.getJSON('product_action.php?action=get_pro',
		 {pro_id:data.product_id,r:Math.random()},
		 function(data){		 		
		 	 	$('#span_pro').html(data.product_name);
		 		$('#span_model').html(data.product_model);
		 		$('#span_made').html(data.product_made);
		 		$('#hid_l_pro_id').val(data.product_id);
		 		
			});
			 	 
		});
		
			
	}
	
		function edit_price(id){
		$.getJSON('product_action.php?action=get_price_by_id',
				{id:id,r:Math.random()},
			function(data){
				 $('#hid_product_price_id').val(id);
				 $('#price_name').val(data.price_name);
				 $('#price').val(data.product_price);
				 $('#unit').val(data.unit);				 
			 	 $('#price_edit_div').show();
			 	 $('#is_hide').val(data.is_hide);
			 	 
			 	 $.getJSON('product_action.php?action=get_pro',
		 {pro_id:data.product_id,r:Math.random()},
		 function(data){		 		
		 	 	$('#span_pro2').html(data.product_name);
		 		$('#span_model2').html(data.product_model);
		 		$('#span_made2').html(data.product_made);
		 		$('#hid_p_pro_id').val(data.product_id);
		 		
			});
			 	 
		});
		
			
	}
	
	function edit1(product_id){	
			 $.getJSON('product_action.php?action=get_pro',
		 {pro_id:product_id,r:Math.random()},
		 function(data){
	
		 	$('#mode1').val('edit_pro');
		 	 $('#pro').val(data.product_name);
		 	$('#tag').val(data.product_tags);
		 	$('#model').val(data.product_model);
		 	$('#made').val(data.product_made);
		 	$('#hid_pro_id').val(product_id);
			$('#add_div').show();
			$('#list_div').hide();
			});		
	}
	function del1(product_id){
	if(!confirm('确定要删除吗？')){
		return;
	}
	 $.post('product_action.php?action=del_pro',
		 {pro_id:product_id,r:Math.random()},
		 function(data){
			if(data=='success'){
				list_pro();
				$('#info').html('');
			} else {
				$('#info').html(data);
			}
			});
	
	}
	function save_pro(num){
		var mode1 = $('#mode1').val();
		 $.post('product_action.php?action='+mode1,
		 {   product_id:$('#hid_pro_id').val(),
		 	 pro:$('#pro').val(),
		 	 tag:$('#tag').val(),
		 	 	 made:$('#made').val(),
		 	 	 model:$('#model').val(),
		 	 	 r:Math.random()},
		 	 function(data){
		 	 if(data!='success'){
		 	 	$('#info').html(data);
		 	 } else {
		 	 	$('#info').html(''); 
		 	 }
		 	 	  if(num==1){
		 	 	  	  $('#pro').val('');
		 	 	  	  $('#tag').val('');
		 	 	  	  $('#model').val('');
		 	 	  	  $('#made').val('');
		 	 	  	  	$('#add_div').show();
						$('#list_div').hide();
		 	 	  } else if(num==2){
		 	 	  	  list_pro();
		 	 	  }
		 	 });
	}
	function ret(){
		list_pro();
	}
	function list_pro(){
		$('#add_div').hide();
		$('#list_div').show();
		var sort1 = $('#sort1').val();
		var filter1 = $('#filter').val();
		var id = $('#editor_product_id').val();
		$.get('product_action.php?action=list_pro',
		 {r:Math.random(),sort1:sort1,filter1:filter1,id:id},
		 	 function(data){
		 	 	$('#content_div').html(data);			
				   $('#tag_div').html('');
				   
				   	$('#id1').addClass('active');
					$('#id2').removeClass('active');
		 	 });
	}

	function clearId(){
		$('#editor_product_id').val('');
		list_pro();
		$('#price_div').hide();
		$('#price_div2').html('');
		$('#location_div').hide();

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
        				 <a href='javascript:void(0)' onclick='fill_tag("")'>商品列表</a>	
                            </li>
        			<li id='id2'>
        			 <a href='javascript:void(0)' onclick='add_init()'>添加商品</a>
        			</li>
						<li id='id3'>
        			 <a href='sales.php' target='_blank'>新增销售</a>
        			</li>
    	<li id='id4'>
        			 <a href='instock.php' target='_blank'>新增入库</a>
        			</li>
     	<li id='id5'>
        			 <a href='stock.php' target='_blank'>库存列表</a>
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
		
		<input type='hidden' id='editor_product_id' value='' />    
			</div>

		</div>
		</div>
	<div id='add_div' style='padding-left:0px;padding-top:5px;margin-left:50px;margin-right:50px;'>
		<input type='hidden' id='mode1' />
			  <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">添加商品</div>
                            </div>
                            <div class="block-content collapse in">
			  <form class="form-horizontal">
			
			
			   <div class="control-group">
                                          <label class="control-label">商品名称</label>
                                          <div class="controls">
                                          <input type='text' id='pro' />
                                          </div>
                                        </div>
                   
                     <div class="control-group">
                                          <label class="control-label" >规格</label>
                                          <div class="controls">
                                          <input type='text' id='model' /> 
                                          </div>
                                        </div>
                   
                     <div class="control-group">
                                          <label class="control-label" >品牌/产地</label>
                                          <div class="controls">
                                         <input type='text' id='made' />
                                          </div>
                                        </div>
			
			 <div class="control-group">
                                          <label class="control-label" >标签</label>
                                          <div class="controls">
                                         <input type='text' id='tag' />
                                          </div>
                                        </div>
                 
                         <div class="form-actions">
                         	 	<input type='button' value='保存并继续添加' onclick='save_pro(1)' class="btn btn-primary"/>
	<input type='button' value='保存并跳转至列表' onclick='save_pro(2)' class="btn btn-primary"/>
							  	<input type='button' value='返回' onclick='ret()' class="btn"/>
                                        
                                        </div>

		<input type='hidden' id='hid_pro_id' />
			
			</form>
				</div>
				</div>
	                </div>
	    <div id='list_div'>	    
		
	    			<div id='content_div' style='margin-left:50px;margin-right:50px;'>
	    				
	    			</div>
	    				<div id='location_div' style='margin-left:50px;margin-right:50px;'>
	    				
	    				</div>	  
	    					
	    				<div id='location_edit_div' style='margin-left:50px;margin-right:50px;'>
	    					<input type='hidden' id='hid_product_location_id' />
	    						<input type='hidden' id='hid_l_pro_id' />
	    					
	    					 <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">存放</div>
                            </div>
                            <div class="block-content collapse in">
			  <form class="form-horizontal">
			  					 
			  					  <div class="control-group">
                                          <label class="control-label" >商品名称</label>
                                          <div class="controls">
                                        <span id='span_pro'></span>
                                          </div>
                                        </div>
                                      
                                      <div class="control-group">
                                          <label class="control-label" >规格</label>
                                          <div class="controls">
                                        <span id='span_model'></span>
                                          </div>
                                        </div>
                                          
                                              <div class="control-group">
                                          <label class="control-label" >品牌/产地</label>
                                          <div class="controls">
                                        <span id='span_made'></span>
                                          </div>
                                        </div>
                                        		  
                                        		     <div class="control-group">
                                          <label class="control-label" >存放位置</label>
                                          <div class="controls">
                                       <input type='text' id='location' />
                                          </div>
                                        </div>
                                        				 
                                        				     <div class="control-group">
                                          <label class="control-label" >数量</label>
                                          <div class="controls">
                                      <input type='text' id='quantity' />
                                          </div>
                                        </div>
                                      
	    					   <div class="form-actions">
	    					   	   
	    					   	   	<input type='button' value='保存' onclick='save_product_location()' class="btn btn-primary"/>
                         	 
                                        
                                        </div>
	    					</form>
				</div>
				</div>
	    				
	    						
	    						
	    				</div>  
	    					<div id='price_div' style='margin-left:50px;margin-right:50px;'>
	    					
	    					</div>
	    					<div id='price_div2' style='margin-left:50px;margin-right:50px;'>
	    						
	    					</div>
	    						<div id='price_edit_div' style='margin-left:50px;margin-right:50px;'>
	    								<input type='hidden' id='hid_product_price_id' />
	    								<input type='hidden' id='hid_p_pro_id' />
	    						
	    							 <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">价格</div>
                            </div>
                            <div class="block-content collapse in">
			  <form class="form-horizontal">
			  							 
			  							 
			  							    <div class="control-group">
                                          <label class="control-label" >商品名称</label>
                                          <div class="controls">
                                        <span id='span_pro2'></span>
                                          </div>
                                        </div>
                                        		
                                        			    <div class="control-group">
                                          <label class="control-label" >规格</label>
                                          <div class="controls">
                                        <span id='span_model2'></span>
                                          </div>
                                        </div>
                                        					
                                        					  <div class="control-group">
                                          <label class="control-label" >品牌/产地</label>
                                          <div class="controls">
                                        <span id='span_made2'></span>
                                          </div>
                                        </div>
	    						
							 	  <div class="control-group">
                                          <label class="control-label" >价格名</label>
                                          <div class="controls">
                                      <input type='text' id='price_name' value='零售价' />
                                          </div>
                                        </div>
							
								  <div class="control-group">
                                          <label class="control-label" >价格</label>
                                          <div class="controls">
                                  <input type='text' id='price'  />
                                          </div>
                                        </div>
							
								  <div class="control-group">
                                          <label class="control-label" >单位</label>
                                          <div class="controls">
                                 <input type='text' id='unit'  />
                                          </div>
                                        </div>
							
								  <div class="control-group">
                                          <label class="control-label" >是否隐藏</label>
                                          <div class="controls">
                               <select id='is_hide' name='is_hide'>
									<option value='0'>否</option>
								  <option value='1'>是</option>
								</select>
                                          </div>
                                        </div>
							
							  <div class="form-actions">
	    					   	   
	    					   	   	<input type='button' value='保存' onclick='save_product_price()' class="btn btn-primary"/>
                         	 
                                        
                                        </div>
								
										
										
										
											</form>
				</div>
				</div>
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

	
