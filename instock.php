<?php
if(isset($_COOKIE["login_true"])==false){
        session_start();
        $_SESSION["last_url"]="instock.php";
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

<title>新增入库</title>
<style type="text/css">
#wrapper {
  width: 100%;height:100%;
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

var  active_index=0;





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
				{action:'list_pro_for_instock_1',r:Math.random(),filter1:key,page_name:'instock.php'},
					function(data){
						 $('#oLayer_content').html(data);
						 hidemeC();
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
                clear_info();
		$.getJSON('instock_action.php',{action:'get_instock',batch_id:batch_id,r:Math.random(),page_name:'instock.php'},
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
				$.getJSON('instock_action.php',{action:'get_instock_detail',batch_id:batch_id,r:Math.random(),page_name:'instock.php'},
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
							pro_id_list = $('input[name=hid_pro_id]');
							name_gg_list = $('input[name=name_gg]');
							p_model_list = $('input[name=p_model]');
							p_made_list = $('input[name=p_made]');
							unit_list = $('input[name=unit]');
							quantity_list = $('input[name=quantity]');
							price_list = $('input[name=price]');
							money_list = $('input[name=money]');
							remark_list = $('input[name=remark]');
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
		$('#btn_add_big_stock').show();
            
			
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
                        page_name:'instock.php',
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
		$('#btn_add_big_stock').hide();
	}
	
	function ret(){                
        clear_info();	
        $('#add_div').hide();
		$('#list_div').show();
		
	}
	
	function selectone(product_id){
		$.getJSON('product_action.php',
		{r:Math.random(),action:'get_pro',pro_id:product_id,page_name:'instock.php'} ,
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
		/*	 $.getJSON('stock_action.php',
			 {r:Math.random(),action:'get_stock',product_id:product_id,page_name:'instock.php'},
			 	 function(stock_data){
			 	 	 $('input[name=unit]').eq(active_index).val(stock_data.stock_unit);
			 	 });*/
			 	    $.getJSON('instock_action.php',
                                    {r: Math.random(),product_id: product_id,action:'get_last_price_by_product_id',page_name:'instock.php'},
                                    function (stock_data) {
                                        $('input[name=unit]').eq(active_index).val(stock_data.unit);
                                        $('input[name=price]').eq(active_index).val(stock_data.in_price);
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
		var span_big_list =  $('span[name=hid_big]');
	
		for(var i=0;i<name_gg_list.length;i++){
			$(name_gg_list[i]).val('');
			$(unit_list[i]).val('');
			$(quantity_list[i]).val('');
			$(price_list[i]).val('');
			$(money_list[i]).val('');
			$(remark_list[i]).val('');
			$(span_big_list[i]).html('');
		}
		$('#money_hj').val('');
		$('#remark_t').val('');
	}

	function list(page_id){
	      hidemeC();
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
			end_time:end_time,	
                        page_name:'instock.php'	 
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
	$.get('instock_action.php?action=show_detail',{r:Math.random(),batch_id:batch_id,page_name:'instock.php'},function(data){
			var new_data = '<tr><td colspan="7">'+data+'</td></tr>';
			$(new_data).insertAfter($(ele).parent().parent());
		});
		}
	

		
	}

	function hide_this(ele){
		$(ele).parent().parent().parent().parent().parent().remove();
	}
	


	function list_instock(){
	 	$('#list_div').show();
		$('#add_div').hide();
	 	list(1);	
	 }

	 function del_sales(batch_id){
		 if(confirm('确认要删除吗？')){
 			$.get('instock_action.php?action=del_instock',
		 	{r:Math.random(),batch_id:batch_id,page_name:'instock.php'},
		 	function(data){
				 if(data=='success'){
					go_page(last_page_id);
				 }
			 }
		 	);
		 }
		
	 }
         
         function selectCompany(company_name){
            $('#company_name').val(company_name);
            $('#oLayerC').hide();
        }
        
        function hidemeC(){
        	  $('#oLayerC').hide(); 
        }
		function hidemeB(){
			$('#oLayerB').hide(); 
		}
        var last_company = '';
        function show_company(){
    		var company = $('#company_name').val().trim();
    		if(company==last_company){
    			return;
    		}
    		last_company = company;
    		if(company==''){
        		$('#oLayerC').hide();        
    		} else {
        		var top = $('#company_name').offset().top;
        		var left = $('#company_name').offset().left;
        		$('#oLayerC').css('left',left+40);
        		$('#oLayerC').css('top',top+40);
        		$('#oLayerC').show();
        		$.get('instock_action.php',
                	{action:'list_company',r:Math.random(),filter1:company,page_name:'instock.php'},
                        function(data){
                            if(data==''){
                                $('#oLayerC').hide();
                            } else {
                                $('#oLayer_contentC').html(data);
                            }
                        });
        
    		}    
		}

		

function add_big_stock(){
	var name_gg_list = $('input[name=name_gg]');
	var pro_id_list =  $('input[name=hid_pro_id]');
	var span_big_list =  $('span[name=hid_big]');
	for(var i=0;i<name_gg_list.length;i++){
		var name_gg = $(name_gg_list[i]).val();
		if(name_gg!=''){							
			let batch_id = $('#edit_batch_id').val();
			let pro_id = $(pro_id_list[i]).val();
			let index = i;
			$.get('instock_action.php',
				{action:'count_by_batch_id_and_product_id',
				r:Math.random(),
				product_id:pro_id,
				batch_id:batch_id,
				page_name:'instock.php'},
				function(data){
					
					if(data=='0'){
						let html = '<input type="button" value="添加" onclick="show_big_layer('+
						pro_id+',1)"/>';
						$(span_big_list[index]).html(html);
						
					} else if(data!='0'){
						let html = '<input type="button" value="编辑" onclick="show_big_layer('+
						pro_id+',2)"/>';
						$(span_big_list[index]).html(html);
						
					}
				}			
			);
		}

	}
}



function show_big_layer(product_id,big_stock_mode){
	
	clear_flag();
	if(big_stock_mode =='1'){
		b_del_all_line_except_firt_line();
		$.getJSON('product_action.php?action=get_pro',
		{r:Math.random(),page_name:'instock.php',pro_id:product_id},
			function(data){
				console.log(data);
				var top = 50;
				var left = 0;
				$('#oLayerB').css('left',left+40);
				$('#oLayerB').css('top',top+40);
				$('#oLayerB').show();

				$('input[name=hid_b_pro_id]').eq(0).val(product_id);
				var str = data.product_name;
				if(data.product_model!=''){
					str += ' '+data.product_model;
				}
				if(data.product_made!=''){
					str +=  ' ' +data.product_made;
				}
				$('input[name=b_name_gg]').eq(0).val(str);
				$('span[name=b_instock_batch_no]').eq(0).html($('#edit_batch_id').val());			
				

			}
		);
	} else if(big_stock_mode=='2'){
		 $.getJSON('instock_action.php?action=get_big_stock_full',
		 {
		 	r:Math.random(),
		 	product_id:product_id,
		  instock_batch_id: $('#edit_batch_id').val(),
		  page_name:'instock.php',
		 },function(data){
				console.log(data);
				console.log(data.length);
				remove_to_leave_one_row();
				for(let index = 1;index<data.length;index++){
					b_add_line(null);
				}
				var str = data[0].product_name;
				if(data[0].product_model!=''){
					str += ' '+data[0].product_model;
				}
				if(data[0].product_made!=''){
					str +=  ' ' +data[0].product_made;
				}
				for(var index = 0;index<data.length;index++){
					$('input[name=b_id]').eq(index).val(data[index].id);
					console.log(data[index].id);
					$('input[name=b_name_gg]').eq(index).val(str);
					console.log(str);
					$('input[name=hid_b_pro_id]').eq(index).val(data[index].product_id);
					console.log(data[index].product_id);
					$('input[name=b_quantity]').eq(index).val(data[index].quantity);
					console.log(data[index].quantity);
					$('input[name=b_unit]').eq(index).val(data[index].unit);
					console.log(data[index].unit);
					$('input[name=b_save_location]').eq(index).val(data[index].stock_position);
					console.log(data[index].stock_position);
					$('input[name=b_state]').eq(index).val(data[index].product_state);
					console.log(data[index].product_state);					
					$('span[name=b_instock_batch_no]').eq(index).html(data[index].instock_batch_id);
					console.log(data[index].instock_batch_id);		
					$('span[name=b_add_date]').eq(index).html(data[index].add_date_str);
					console.log(data[index].add_date_str);
					$('span[name=b_update_date]').eq(index).html(data[index].update_date_str);	
					console.log(data[index].update_date_str);
					$('span[name=b_no]').eq(index).html(data[index].b_no);	


				}
				var top = 50;
				var left = 0;
				$('#oLayerB').css('left',left+40);
				$('#oLayerB').css('top',top+40);
				$('#oLayerB').show();


		 });


	}
	
	
	

}

function remove_to_leave_one_row(){
	while($('#table_b').find('tbody').find('tr').length>2){
		$('#table_b').find('tbody').find('tr').eq(1).remove();
	}
}


function b_add_line(ele){
	//let index = $('input[name=btn_b_add_line]').index(ele);
	
	var len = $('#table_b').find('tbody').find('tr').length;
	var html = $('#table_b').find('tbody').find('tr').html();
	$('#table_b').find('tbody').find('tr').eq(len-2).after('<tr>'+html+'</tr>');
	
	var product_id = $('input[name=hid_b_pro_id]').eq(0).val();
	var b_name_gg = $('input[name=b_name_gg]').eq(0).val();
	var b_instock_batch_no = $('span[name=b_instock_batch_no]').eq(0).html();

	$('#table_b').find('tbody').find('tr').eq(len-1).find('input[name=hid_b_pro_id]').eq(0).val(product_id);
	console.log('product_id:'+product_id);

	$('#table_b').find('tbody').find('tr').eq(len-1).find('input[name=b_name_gg]').eq(0).val(b_name_gg);
	console.log('b_name_gg:'+b_name_gg);
	$('#table_b').find('tbody').find('tr').eq(len-1).find('span[name=b_instock_batch_no]').eq(0).html(b_instock_batch_no);
	console.log('b_instock_batch_no:'+b_instock_batch_no);

}

function b_del_all_line_except_firt_line(){
	var len = $('#table_b').find('tbody').find('tr').length;
	while(len>2){
		$('#table_b').find('tbody').find('tr').eq(1).remove();
		len = $('#table_b').find('tbody').find('tr').length;
	}
}

function b_del_line(ele){
	let index = $('input[name=btn_b_del_line]').index(ele);
	if(index>0){
		$('#table_b').find('tbody').find('tr').eq(index).remove();
	}
}

function b_save(){
	var array_b_id = $('input[name=b_id]');
	var array_b_name_gg = $('input[name=b_name_gg]');
	var array_hid_b_pro_id = $('input[name=hid_b_pro_id]');
	var array_b_quantity = $('input[name=b_quantity]');
	var array_b_unit = $('input[name=b_unit]');
	var array_b_save_location = $('select[name=b_save_location]');
	var array_b_state = $('select[name=b_state]');
	var array_b_instock_batch_no = $('span[name=b_instock_batch_no]');
	for(let i=0;i<array_b_id.length;i++){
		let b_id = $(array_b_id[i]).val();
		let pro_id = $(array_hid_b_pro_id[i]).val();
		let b_quantity = $(array_b_quantity[i]).val();
		let b_unit = $(array_b_unit[i]).val();
		let b_save_location = $(array_b_save_location[i]).val();
		let b_state = $(array_b_state[i]).val();
		console.log("b_id:"+b_id);
		console.log("pro_id:"+pro_id);
		console.log("b_quantity:"+b_quantity);
		console.log("b_unit:"+b_unit);
		console.log("b_save_location:"+b_save_location);
		console.log("b_state:"+b_state);
		$.post('instock_action.php?action=save_big',{
			b_id:b_id,
			pro_id:pro_id,
			b_quantity:b_quantity,
			b_unit:b_unit,
			b_save_location:b_save_location,
			b_state:b_state,
			r:Math.random(),
			page_name:'instock.php',
			batch_id:$('#edit_batch_id').val()
		},function(data){
			$('span[name=op_state]').eq(i).html(data);
		}		
		);


	}

}

function clear_flag(){
	var array_b_quantity = $('span[name=op_state]');
	for(let i=0;i<array_b_quantity.length;i++){
		$(array_b_quantity[i]).html('');
	}
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
                                    <a href='javascript:void(0)' onclick='add_init()'>新增入库</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='list_instock()'>入库列表</a>
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
					供应商：<input type='text' name='company_name' id='company_name'  style='width:350px' onkeyup="show_company()"  />
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
			<input type='text' name='name_gg'  style='width:150px'   /> 
			<input type='hidden' name='hid_pro_id' />
		 	<input type='hidden' name='p_model' />
		 	<input type='hidden' name='p_made' />

			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			<span name='hid_big'></span>
			</td>
		</tr>
			<tr>
		 	<td>
			<input type='text' name='name_gg'  style='width:150px'   />
			<input type='hidden' name='hid_pro_id' />
			 <input type='hidden' name='p_model' />
		 <input type='hidden' name='p_made' />
			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			<span name='hid_big'></span>
			</td>
		</tr>
			<tr>
		 	<td>
			<input type='text' name='name_gg'  style='width:150px'   />
			<input type='hidden' name='hid_pro_id' />
			 <input type='hidden' name='p_model' />
		 <input type='hidden' name='p_made' />
			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			<span name='hid_big'></span>
			</td>
		</tr>
			<tr>
		 	<td>
			<input type='text' name='name_gg'  style='width:150px'   />
			<input type='hidden' name='hid_pro_id' />
			 <input type='hidden' name='p_model' />
		 <input type='hidden' name='p_made' />
			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			<span name='hid_big'></span>
			</td>
		</tr>
			<tr>
		 	<td>
			<input type='text' name='name_gg'  style='width:150px'   />
			<input type='hidden' name='hid_pro_id' />
			 <input type='hidden' name='p_model' />
		 <input type='hidden' name='p_made' />
			</td>
			<td>	<input type='text' name='unit'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
			<td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
			<td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
			<td>
			<input type='text' name='remark'  style='width:150px'   />
			<span name='hid_big'></span>
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

		&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;
		&nbsp;&nbsp;
			<input type='button' value='添加大件库存' onclick='add_big_stock()' id='btn_add_big_stock' style='display:none;'/>


	                </div>

	    <div id='list_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
		
<table id='search_tbl'  class='table table-bordered table-striped table-hover'>
	<tr><td>

		供应商：<input id='client_name' name='client_name' style='width:250px;' />
		筛选：<input id='filter' name='filter' style='width:150px;' />
		开始时间：<input id='start_time' name='start_time' style='width:100px;' class='Wdate' onClick='WdatePicker()' />
		结束时间：<input id='end_time' name='end_time' style='width:100px;' class='Wdate' onClick='WdatePicker()' />
		<input type='button' value='搜索' onclick='list_instock()' />

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

			<div id="oLayerB" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
            width: 1100px; display:none;">
                <div id='oLayer_contentB'>
					<table id='table_b' class='table table-bordered table-striped table-hover'>
						<thead>
							<tr>
							<th>编号</th>
							<th>名称及规格</th>							
							<th>数量</th>
							<th>单位</th>
							<th>存放位置</th>
							<th>状态</th>
							<th>入库编号</th>
							<th>库存编号</th>
							<th>添加日期</th>
							<th>更新日期</th>
							<th>操作</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td>
							<input type='text' name='b_id' style='width:60px;' />
							</td>
							<td>
							
							<input type='text' name='b_name_gg'  style='width:150px'   />
							<input type='hidden' name='hid_b_pro_id' />
							</td>							
							<td>
							<input type='text' name='b_quantity'  style='width:60px'   />							  
							</td>
							<td>
							<input type='text' name='b_unit'  style='width:60px'   />
							</td>
							<td>
							<select name='b_save_location' style='width:80px' >
								<option value='1'>店内</option>
								<option value='2'>包家仓库</option>
								<option value='3'>舜北仓库</option>
							</select>
							</td>
							<td>
							<select name='b_state' style='width:80px' >
								<option value='1'>在库</option>
								<option value='2'>部分售出</option>
								<option value='3'>售出</option>
							</select>
							</td>
							<td>							
							<span name='b_instock_batch_no'></span>
							</td>
							<td>							
							<span name='b_no'></span>
							</td>
							<td>
							<span name='b_add_date'></span>
							</td>
							<td>
							<span name='b_update_date'></span>
							</td>
							<td>
							<input type='button' value='+' name='btn_b_add_line' onclick='b_add_line(this)' />
							<input type='button' value='-' name='btn_b_del_line' onclick='b_del_line(this)' />
							<span name='op_state'>

							</span>
							</td>
							</tr>
							
							<tr>
							<td colspan='9'>
							<input type='button' value='保存' name='btn_b_save' onclick='b_save()' />
							</td>
							<td></td>
							</tr>
							
						</tbody>						
					</table>
                </div>
                
                <div style='float:right;'>
                	       <input type='button' value='x' onclick='hidemeB()' />
                	</div>
			</div>
					
						<?php
	require_once ( 'change_password.php');
		
		?>
</body>
</html>

	
