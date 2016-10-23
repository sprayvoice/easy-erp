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

<title>新增入库</title>
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

function accDiv(arg1,arg2){
    var t1 = 0,t2 = 0,r1,r2;
    try{t1 = arg1.toString().split('.')[1].length}catch(e){}
    try{t2 = arg2.toString().split('.')[1].length}catch(e){}
    with(Math){
        r1 = Number(arg1.toString().replace('.',''));
        r2 = Number(arg2.toString().replace('.',''));
        return (r1 / r2) * pow(10,t2 - t1);
    }
}
function accMul(arg1,arg2)
{
    var m = 0,s1 = arg1.toString(),s2 = arg2.toString();
    try{m += s1.split('.')[1].length}catch(e){}
    try{m += s2.split('.')[1].length}catch(e){}
    return Number(s1.replace('.',''))*Number(s2.replace('.','')) / Math.pow(10,m);
}
function accAdd(arg1,arg2){
    var r1,r2,m;
    try{r1 = arg1.toString().split('.')[1].length}catch(e){r1 = 0}
    try{r2 = arg2.toString().split('.')[1].length}catch(e){r2 = 0}
    m = Math.pow(10,Math.max(r1,r2));
    return (arg1 * m + arg2 * m) / m;
}
function accSub(arg1,arg2){
    var r1,r2,m,n;
    try{r1 = arg1.toString().split('.')[1].length}catch(e){r1 = 0}
    try{r2 = arg2.toString().split('.')[1].length}catch(e){r2 = 0}
    m = Math.pow(10,Math.max(r1,r2));
    //动态控制精度长度
    n = (r1 >= r2) ? r1 : r2;
    return ((arg2 * m - arg1 * m) / m).toFixed(n);
}


function unbind_name_gg(){
	$('input[name=name_gg]').unbind('keyup');
}
function bind_name_gg(){
		$('#oLayer_content').html('');
		$('input[name=name_gg]').keyup(function(){
			var key  =$(this).val();
			if(key!=''){
				active_index = $('input[name=name_gg]').index(this);
				var top = $(this).offset().top;
				var left = $(this).offset().left;
				$('#oLayer').css('left',left+40);
				$('#oLayer').css('top',top+40);
				$('#oLayer').show();
				$.get('product_action.php',
				{action:'list_pro_for_instock',r:Math.random(),filter1:key},
					function(data){
						 $('#oLayer_content').html(data);
					});
				
			} else {
					$('#oLayer').hide();
			}
		});
}

function hideme(){
	$('#oLayer').hide();
}

 function unbind_change(){
 	$('input[name=quantity]').unbind('change');
 	$('input[name=price]').unbind('change');
 	$('input[name=money]').unbind('change');
}

function bind_change(){
	$('input[name=quantity]').change(function(){
		computePrice($(this).parent().parent());
		computeMoney($(this).parent().parent());
	});
	$('input[name=price]').change(function(){
		 computeMoney($(this).parent().parent());
	});
	$('input[name=money]').change(function(){
		 computePrice($(this).parent().parent());
	});
}

	$(document).ready(function(){
		bind_change();
		bind_name_gg();
		add_init();

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
	
	function computeMoney(row){
		var quantity = $(row).find('input[name=quantity]').val();
		 var price = $(row).find('input[name=price]').val();
		 if(quantity!='' && price!=''){
		 	 var sum = accMul(quantity, price);
			  if(isNaN(sum)){
					$(row).find('input[name=money]').val('');
			  } else {
		 	 		$(row).find('input[name=money]').val(sum);
			  }
		 } 
		 computeHj();
	}
	
	function get_instock(batch_id){
		$.getJSON('instock_action.php',{action:'get_instock',batch_id:batch_id,r:Math.random()},
			function(data){			
				$('#edit_batch_id').val(data.in_batch_id);
				$('#money_hj').val(data.total_money);
				var sales_day = data.add_date;
				var yymmdd = sales_day.split('-');
				if(yymmdd.length==3){
					$('#yy').val(yymmdd[0]);
					$('#mm').val(yymmdd[1]);
					$('#dd').val(yymmdd[2]);
				}				
				$('#company_name').val(data.in_company);			
				$('#remark_t').val(data.remark);
				$.getJSON('instock_action.php',{action:'get_instock_detail',batch_id:batch_id,r:Math.random()},
						function(data){	
							var pro_id_list = $('input[name=hid_pro_id]');
							var name_gg_list = $('input[name=name_gg]');
							var p_model_list = $('input[name=p_model]');
							var p_made_list = $('input[name=p_made]');
							var unit_list = $('input[name=unit]');
							var quantity_list = $('input[name=quantity]');
							var price_list = $('input[name=price]');
							var money_list = $('input[name=money]');
							var remark_list = $('input[name=remark]');
							var row_length = $('input[name=hid_pro_id]').length;
							while(data.length>row_length){
								add_line();
								row_length = $('input[name=hid_pro_id]').length;
							}
							for(var i=0;i<data.length;i++){
								var row = data[i];
								$(pro_id_list[i]).val(row.product_id);
								$(name_gg_list[i]).val(row.product_name);
								$(p_model_list[i]).val(row.product_model);
								$(p_made_list[i]).val(row.product_made);
								$(unit_list[i]).val(row.unit);
								$(quantity_list[i]).val(row.in_quantity);
								$(price_list[i]).val(row.in_price);
								$(money_list[i]).val(row.in_money);
								$(remark_list[i]).val(row.remark);
							}
						});
			});
		$('#add_div').show();
		$('#list_div').hide();
			
	}
	
	function computePrice(row){
		 var quantity = $(row).find('input[name=quantity]').val();
		 var price = $(row).find('input[name=price]').val();
		 var sum = $(row).find('input[name=money]').val();
		 if(sum!=''){
			 if(quantity=='' && price==''){
				$(row).find('input[name=quantity]').val('1');
				$(row).find('input[name=price]').val(sum); 
			 } else if(quantity!=''){
				 price = accDiv(sum,quantity);
				 price = price.toFixed(2);
				 $(row).find('input[name=price]').val(price);
			 }
		 }
		 computeHj();

	}
	
	function add_line(){
		var len = $('#list_tb1').find('tbody').find('tr').length;
		var html = $('#list_tb1').find('tbody').find('tr').html();
		$('#list_tb1').find('tbody').find('tr').eq(len-2).after('<tr>'+html+'</tr>');
		unbind_change();
		unbind_name_gg();
		bind_change();
		bind_name_gg();
	}

	function save_instock(){
		var company_name = $('#company_name').val();
		var date = $('#yy').val()+'-'+$('#mm').val()+'-'+$('#dd').val();
		var pro_id_list =  $('input[name=hid_pro_id]');
		var pro_id_s = '';
		var name_gg_list = $('input[name=name_gg]');
		var name_gg_s = '';
		var p_model_list =  $('input[name=p_model]');
		var p_model_s = '';
		var p_made_list =  $('input[name=p_made]');
		var p_made_s = '';
		var unit_list = $('input[name=unit]');
		var unit_s = '';
		var quantity_list = $('input[name=quantity]');
		var quantity_s = '';
		var price_list = $('input[name=price]');
		var price_s = '';
		var money_list = $('input[name=money]');
		var money_s = '';
		var remark_list = $('input[name=remark]');
		var remark_s = '';
		var hj = $('#money_hj').val();
		var remark_t = $('#remark_t').val();
		for(var i=0;i<name_gg_list.length;i++){
			var name_gg = $(name_gg_list[i]).val();
			if(name_gg!=''){							
				var unit = $(unit_list[i]).val();
				var quantity = $(quantity_list[i]).val();
				var price = $(price_list[i]).val();
				var remark = $(remark_list[i]).val();
				var money = $(money_list[i]).val();
				var pro_id = $(pro_id_list[i]).val();
				var p_model = $(p_model_list[i]).val();
				var p_made = $(p_made_list[i]).val();
				name_gg_s += name_gg+',';
				unit_s += unit+',';
				quantity_s += quantity + ',';
				price_s += price + ',';
				money_s += money + ',';
				remark_s += remark + ',';
				pro_id_s += pro_id+',';
				p_model_s += p_model+',';
				p_made_s += p_made+',';					
			}
		}


		$.post('instock_action.php?action=save_instock',{
			batch_id:$('#edit_batch_id').val(),
			company_name:company_name,
			date:date,
			pro_id:pro_id_s,				
			name_gg:name_gg_s,
			p_model:p_model_s,
			p_made:p_made_s,
			unit:unit_s,
			quantity:quantity_s,
			price:price_s,
			money:money_s,
			remark:remark_s,
			hj:hj,
			remark_t:remark_t,
			r:Math.random()
		},function(data){
			if(data=='success'){				
				$('#list_div').show();
				$('#add_div').hide();
	 			list(last_page_id);	 	
			} else {
				alert(data);
			}
		})
	}

	function computeHj(){
		var money_list = $('input[name=money]');
		var total = 0;
		for(var i=0;i<money_list.length;i++){
			var money = $(money_list[i]).val();
			if(money!=''){
				total = accAdd(total,money);
			}
		}
		$('#money_hj').val(total);
	}
	
	function add_init(){
		$('#add_div').show();
		$('#list_div').hide();
		clear_info();
	}
	
	function ret(){
		$('#add_div').hide();
		$('#list_div').show();
		clear_info();
	}
	
	function selectone(product_id){
		$.getJSON('product_action.php',
		{r:Math.random(),action:'get_pro',pro_id:product_id} ,
			function(data){
			var str = data.product_name;
			if(data.product_model!=''){
				str += ' '+data.product_model;
			}
			if(data.product_made!=''){
				str +=  ' ' +data.product_made;
			}
			$('input[name=name_gg]').eq(active_index).val(str);
			$('input[name=hid_pro_id]').eq(active_index).val(product_id);
			 $('input[name=p_model]').eq(active_index).val(data.product_model);
			 $('input[name=p_made]').eq(active_index).val(data.product_made);
			 $.getJSON('stock_action.php',
			 {r:Math.random(),action:'get_stock',product_id:product_id},
			 	 function(stock_data){
			 	 	 $('input[name=unit]').eq(active_index).val(stock_data.stock_unit);
			 	 });
			hideme();
		});
	}
	
	function clear_info(){
		$('#edit_batch_id').val('0');
		$('#company_name').val('');
		var name_gg_list = $('input[name=name_gg]');
		var unit_list = $('input[name=unit]');
		var quantity_list = $('input[name=quantity]');
		var price_list = $('input[name=price]');
		var money_list = $('input[name=money]');
		var remark_list = $('input[name=remark]');
	
		for(var i=0;i<name_gg_list.length;i++){
			$(name_gg_list[i]).val('');
			$(unit_list[i]).val('');
			$(quantity_list[i]).val('');
			$(price_list[i]).val('');
			$(money_list[i]).val('');
			$(remark_list[i]).val('');
		}
		$('#money_hj').val('');
		$('#remark_t').val('');
	}

	function list(page_id){
		 var client_name = $('#client_name').val();
		 var filter = $('#filter').val();
		 var start_time = $('#start_time').val();
		 var end_time = $('#end_time').val();
		 last_page_id=page_id;
		 $.get('instock_action.php',
			{r:Math.random(),
			action:'list_instock',
			page_id:page_id,
			client_name:client_name,
			filter:filter,
			start_time:start_time,
			end_time:end_time		 
			},
			function(data){				
				$('#list_div_tbl').html(data);
		 });
	 }

	var last_page_id = 1;

	function go_page(page_id){
		list(page_id);	
	}

	function show_detail(batch_id,ele){
		if($('#detail_'+batch_id).length>0){

		} else {
	$.get('instock_action.php?action=show_detail',{r:Math.random(),batch_id:batch_id},function(data){
			var new_data = '<tr><td colspan="7">'+data+'</td></tr>';
			$(new_data).insertAfter($(ele).parent().parent());
		});
		}
	

		
	}

	function hide_this(ele){
		$(ele).parent().parent().parent().parent().parent().remove();
	}
	


	function list_sales(){
	 	$('#list_div').show();
		$('#add_div').hide();
	 	list(1);	
	 }

	 function del_sales(batch_id){
		 if(confirm('确认要删除吗？')){
 			$.get('instock_action.php?action=del_instock',
		 	{r:Math.random(),batch_id:batch_id},
		 	function(data){
				 if(data=='success'){
					go_page(last_page_id);
				 }
			 }
		 	);
		 }
		
	 }
	
</script>
</head>
<body>
	<div id='wrapper'>
	<div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href='javascript:void(0)' onclick='fill_tag("")'>Admin Panel</a>
                 <ul class="nav">
                        
                    	<li class="active" id='id1'>
        			 <a href='javascript:void(0)' onclick='add_init()'>新增入库</a>
        			</li>
        			<li id='id2'>
        			 <a href='javascript:void(0)' onclick='list_sales()'>入库列表</a>
        			</li>
        			<li id='id3'>
        				 <a href='product.php' target='_blank'>商品列表</a>	
                            </li>
                            	<li id='id4'>
        				 <a href='sales.php' target='_blank'>新增销售</a>	
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
	  
		</div>
		</div>
	<div id='add_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
	
	<input type='hidden' id='edit_batch_id' value='0' />
	<table id='list_tb1'  class='table table-bordered table-striped table-hover'>
	<thead>
		<tr>
			<td colspan='3' style='text-align:left;'>
					单位名称：<input type='text' name='company_name' id='company_name'  style='width:350px'   />
			</td>
			<td colspan='3' style='text-align:right;'>
					日期：<input type='text' name='yy' id='yy' style='width:40px' value='<?php echo date("Y")?>'  /> 年 
					 <input type='text' name='mm' id='mm' style='width:30px' value='<?php echo date("m")?>'   /> 月 
					 <input type='text' name='dd' id='dd' style='width:30px' value='<?php echo date("d")?>'   /> 日 
			</td>
		</tr>
		 <tr>
		<th>品名及规格</th><th>单位</th>
		<th>数量</th><th>单价</th><th>金额</th><th>备注</th>
		</tr>
		</thead>	
		 <tbody>
		<tr>
		 	<td>
			<input type='text' name='name_gg'  style='width:150px'   /> <input type='hidden' name='hid_pro_id' />
		 <input type='hidden' name='p_model' />
		 <input type='hidden' name='p_made' />
			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			</td>
		</tr>
			<tr>
		 	<td>
			<input type='text' name='name_gg'  style='width:150px'   /><input type='hidden' name='hid_pro_id' />
			 <input type='hidden' name='p_model' />
		 <input type='hidden' name='p_made' />
			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			</td>
		</tr>
			<tr>
		 	<td>
			<input type='text' name='name_gg'  style='width:150px'   /><input type='hidden' name='hid_pro_id' />
			 <input type='hidden' name='p_model' />
		 <input type='hidden' name='p_made' />
			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			</td>
		</tr>
			<tr>
		 	<td>
			<input type='text' name='name_gg'  style='width:150px'   /><input type='hidden' name='hid_pro_id' />
			 <input type='hidden' name='p_model' />
		 <input type='hidden' name='p_made' />
			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			</td>
		</tr>
			<tr>
		 	<td>
			<input type='text' name='name_gg'  style='width:150px'   /><input type='hidden' name='hid_pro_id' />
			 <input type='hidden' name='p_model' />
		 <input type='hidden' name='p_made' />
			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			</td>
		</tr>
		<tr>
				<td colspan='4'>
				备注：<input type='text' name='remark_t' id='remark_t'  style='width:250px'   />
				</td>
			
					<td style='text-align:right;' >
						合计：<input type='text' name='money_hj' id='money_hj'  style='width:100px'   />
				</td>
				<td>
						<input type='button' value='增行' onclick='add_line()' />
						</td>
		</tr>
		</tbody>
		</table>
	<br />

	
	<input type='button' value='保存并跳转至列表' onclick='save_instock()'/>
		<input type='button' value='返回' onclick='ret()'/>


	                </div>

	    <div id='list_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
		
<table id='search_tbl'  class='table table-bordered table-striped table-hover'>
	<tr><td>

		单位名称：<input id='client_name' name='client_name' style='width:250px;' />
		筛选：<input id='filter' name='filter' style='width:150px;' />
		开始时间：<input id='start_time' name='start_time' style='width:80px;' />
		结束时间：<input id='end_time' name='end_time' style='width:80px;' />
		<input type='button' value='搜索' onclick='list_sales(1)' />

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

	
