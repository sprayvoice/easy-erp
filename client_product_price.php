<?php
if(isset($_COOKIE["login_true"])==false){
        session_start();
        $_SESSION["last_url"]="client_product_price.php";
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
<script src="common.js?r=20180626"></script>

<title>客户商品价格表</title>
<style type="text/css">
#wrapper {
  width: 100%;height:100%;
}
.price {
	width:80px;
}
#list_tb1 th {text-align:center;}
#table_b th {text-align:center;}
	 td {padding:5px;}
	 .tb1 {border:2px solid;border-spacing:0px; }
	 .tb1 th {margin:3px;padding:5px;border:1px gray solid;}
	 .tb1 td { border:1px gray solid; margin:2px;padding:3px; border-collapse : collapse;}
	 #span_tag { margin-left:50px;margin-right:50px;}
</style>	
<script type='text/javascript'>

function filter_client(){
	if($('#client_name').val().indexOf("'")>=0){
		return;
	}
	$.get('client_product_price_action.php?action=list_company',
		{r:Math.random(),filter1:$('#client_name').val(),page_name:'client_product_price.php'},
		function(data){
			$('#oLayer_content').html(data);
			$('#oLayer').show();
		}
	);


}

function select_client(client_no){
	$.getJSON('client_product_price_action.php?action=get_company',
	{r:Math.random(),client_no:client_no,page_name:'client_product_price.php'},
	function(data){
		hideme();
		$('#hid_client_no').val(data.client_no);
		$('#client_name').val(data.client_company);
		
	}
	);
}

function gen_data(){
	var client_no = $('#hid_client_no').val();
	console.log('client_no:'+client_no);
	if(client_no!=''){
		$.get('client_product_price_action.php?action=insert_by_client_no',
			{r:Math.random(),client_no:client_no,page_name:'client_product_price.php'},
			function(data){
				console.log(data);
				if(data=='success'){
					list_price();
					refresh_div_head();
				}
			}
		);
	}
}

function find_a(client_no){
	$.getJSON('client_product_price_action.php?action=get_company',
	{r:Math.random(),client_no:client_no,page_name:'client_product_price.php'},
	function(data){
		hideme();
		$('#hid_client_no').val(data.client_no);
		$('#client_name').val(data.client_company);
		list_price();
	}
	);
}

function list_price(){
	var client_no = $('#hid_client_no').val();
	var filter1 = $('#filter').val();
	var show_del = $('#show_del').val();
	$.get('client_product_price_action.php?action=list_price',
		{r:Math.random(),client_no:client_no,page_name:'client_product_price.php',filter1:filter1,show_del:show_del},
		function(data){
				$('#list_div_tbl').html(data);
			}		
	);


}

function del_price(id){
	$.get('client_product_price_action.php?action=del_price',
		{r:Math.random(),id:id,page_name:'client_product_price.php'},
		function(data){
			if(data=='success'){
				list_price();
			} else {
				alert(data);
			}
		}		
	);
}

function save_price(id,element){
	var price = $(element).parent().parent().find('input[name=price]').val();
	console.log(price);
	var fake_price = $(element).parent().parent().find('input[name=fake_price]').val();
	console.log(fake_price);
	var tax_price = $(element).parent().parent().find('input[name=tax_price]').val();
	console.log(tax_price);
	var fake_tax_price = $(element).parent().parent().find('input[name=fake_tax_price]').val();
	console.log(fake_tax_price);
	$.post('client_product_price_action.php?action=save_price',
	{r:Math.random(),id:id,price:price,fake_price:fake_price,
	tax_price:tax_price,fake_tax_price:fake_tax_price,page_name:'client_product_price.php'},
		function(data){
			if(data=='success'){
				$.getJSON('client_product_price_action.php?action=get_by_id',
				{id:id,r:Math.random(),page_name:'client_product_price.php'},
					function(data2){
						console.log(data2);
						$(element).parent().parent().find('input[name=price]').val(data2.price);
						$(element).parent().parent().find('input[name=fake_price]').val(data2.fake_price);
						$(element).parent().parent().find('input[name=tax_price]').val(data2.tax_price);
						$(element).parent().parent().find('input[name=fake_tax_price]').val(data2.fake_tax_price);

					}
				);
			}
		}	
	);
	
}

function hideme(){
	$('#oLayer').hide();
}

$(document).ready(function(){
	console.log('document.ready');
	refresh_div_head();

})

function refresh_div_head(){
	$.get('client_product_price_action.php?action=list_all_clients',
		{r:Math.random(),page_name:'client_product_price.php'}
		,function(data){
			$('#list_div_head').html(data);
		}
	);
}
	
</script>
</head>
<body>
	 <div id='wrapper' style="position:absolute;left:0px;top:0px;">
            <div class="navbar" id='top_div'>
                <div class="navbar-inner">
                  <?php include("top_div1.php") ?>                                             
	  
		</div>
		</div>

		<div id='list_div_head' style='padding-left:0px;padding-top:5px;margin-left:50px;margin-right:50px;'>

		</div>

	    <div id='list_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
		
<table id='search_tbl'  class='table table-bordered table-striped table-hover'>
	<tr><td>
		<input type='hidden' id='hid_client_no' value='' />
		客户名称：<input id='client_name' name='client_name' style='width:250px;' onkeyup="filter_client()" />
		筛选：<input id='filter' name='filter' style='width:150px;' />
		显示：<select id='show_del'>
				<option value='0'>最新数据</option>
				<option value='2'>包含纸面数据</option>
				<option value='1'>包含删除数据</option>
			</select>
		
		<input type='button' value='搜索' onclick='list_price()' />

		<input type='button' value='生成' onclick='gen_data()' />

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
    
    
					
						<?php
	require_once ( 'change_password.php');
		
		?>
</body>
</html>

	
