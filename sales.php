<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"] = "sales.php";
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

        <title>新增销售</title>
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
        	
        	var yy = '<?php echo date("Y") ?>';
        	var mm = '<?php echo date("m") ?>';
        	var dd = '<?php echo date("d") ?>';
        

           


            function unbind_name_gg() {
                $('input[name=name_gg]').unbind('keyup');
            }
            function bind_name_gg() {
                $('#oLayer_content').html('');
                //$('input[name=name_gg]').keyup(function () {
                $('input[name=name_gg]').bind('keyup',function () {
                    
                    var key = $(this).val();
                    if (key != '') {
                        $('#oLayerC').hide();
                        active_index = $('input[name=name_gg]').index(this);
                        var top = $(this).offset().top;
                        var left = $(this).offset().left;
                        $('#oLayer').css('left', left + 40);
                        $('#oLayer').css('top', top + 40);
                        $('#oLayer').show();
                        $.get('product_action.php',
                                {action: 'list_pro_for_instock', r: Math.random(), filter1: key,page_name:'sales.php'},
                                function (data) {
                                    if (data == '') {
                                        $('#oLayer').hide();
                                    } else {
                                        $('#oLayer_content').html(data);
                                    }

                                });

                    } else {
                        $('#oLayer').hide();
                    }
                });
            }



            function show_company() {
                var company = $('#company_name').val();
                if (company == '') {
                    $('#oLayerC').hide();
                } else {
                    var top = $('#company_name').offset().top;
                    var left = $('#company_name').offset().left;
                    $('#oLayerC').css('left', left + 40);
                    $('#oLayerC').css('top', top + 40);
                    $('#oLayerC').show();
                    $.get('sales_action.php',
                            {action: 'list_company', r: Math.random(), filter1: company,page_name:'sales.php'},
                            function (data) {
                                if (data == '') {
                                    $('#oLayerC').hide();
                                } else {
                                    $('#oLayer_contentC').html(data);
                                }
                            });

                }
            }
            
            function hidemeC(){
                $('#oLayerC').hide();
            }

            function hidemeP(){
                $('#oLayerP').hide();
            }

            function hideme() {
                $('#oLayer').hide();
            }

            function selectCompany(company_name) {
                $('#company_name').val(company_name);
                $('#oLayerC').hide();
            }

            function unbind_change() {
                $('input[name=quantity]').unbind('change');
                $('input[name=price]').unbind('change');
                $('input[name=money]').unbind('change');
                $('input[name=money_real]').unbind('change');
            }

            function bind_change() {
                $('input[name=quantity]').change(function () {
                    computePrice($(this).parent().parent());
                    computeMoney($(this).parent().parent());
                });
                $('input[name=price]').change(function () {
                    computeMoney($(this).parent().parent());
                });
                $('input[name=money]').change(function () {
                    computePrice($(this).parent().parent());
                });
                $('input[name=money_real').change(function () {
                    computeHjReal();
                });
            }

            $(document).ready(function () {
                bind_change();
                bind_name_gg();
                add_init();

                $.each($('.nav li'), function (name, value) {
                    $(this).click(function () {
                        var len = $('.nav li').length;
                        for (var i = 0; i < len; i++) {
                            $('.nav li').eq(i).removeClass('active');
                        }
                        $(this).addClass('active');
                    });
                });
                
                	get_going_expire_drug();

            });

            function computeMoney(row) {
                var quantity = $(row).find('input[name=quantity]').val();
                var price = $(row).find('input[name=price]').val();
                if (quantity != '' && price != '') {
                    var sum = accMul(quantity, price);
                    if (isNaN(sum)) {
                        $(row).find('input[name=money]').val('');
                        $(row).find('input[name=money_real]').val('');
                    } else {
                        $(row).find('input[name=money]').val(sum);
                        $(row).find('input[name=money_real]').val(Math.floor(sum));
                    }
                }
                computeHj();
            }

            function get_sales(batch_id) {
              clear_info();
                $.getJSON('sales_action.php', {action: 'get_sales', batch_id: batch_id, r: Math.random(),page_name:'sales.php'},
                        function (data) {
                       
                            $('#edit_batch_id').val(data.batch_id);
                            $('#money_hj').val(data.sales_money);
                            $('#money_hj_real').val(data.sales_money_real);
                            var sales_day = data.sales_day;
                            var yymmdd = sales_day.split('-');
                            if (yymmdd.length == 3) {
                                $('#yy').val(yymmdd[0]);
                                $('#mm').val(yymmdd[1]);
                                $('#dd').val(yymmdd[2]);
                            }
                            $('#company_name').val(data.client_company);
                            $('#remark_t').val(data.remark);
                            $.getJSON('sales_action.php', {action: 'get_sales_detail', batch_id: batch_id, r: Math.random(),page_name:'sales.php'},
                                    function (data) {
                                              
                                        var pro_id_list = $('input[name=hid_pro_id]');
                                        var name_gg_list = $('input[name=name_gg]');
                                        var p_model_list = $('input[name=p_model]');
                                        var p_made_list = $('input[name=p_made]');
                                        var unit_list = $('input[name=unit]');
                                        var quantity_list = $('input[name=quantity]');
                                        var price_list = $('input[name=price]');
                                        var money_list = $('input[name=money]');
                                        var money_real_list = $('input[name=money_real]');
                                        var remark_list = $('input[name=remark]');
                                        var row_length = $('input[name=hid_pro_id]').length;
                                        while (data.length > row_length) {
                                            add_line();
                                            row_length = $('input[name=hid_pro_id]').length;
                                        }
                                        for (var i = 0; i < data.length; i++) {
                                            var row = data[i];
                                            $(pro_id_list[i]).val(row.product_id);
                                            $(name_gg_list[i]).val(row.product_name);
                                            $(p_model_list[i]).val(row.product_model);
                                            $(p_made_list[i]).val(row.product_made);
                                            $(unit_list[i]).val(row.unit);
                                            $(quantity_list[i]).val(row.sales_ammount);
                                            $(price_list[i]).val(row.sales_price);
                                            $(money_list[i]).val(row.sales_money);
                                            $(money_real_list[i]).val(row.sales_money_real);
                                            $(remark_list[i]).val(row.remark);
                                        }


                                    });
                        });
                $('#add_div').show();
                $('#list_div').hide();
                $('#id1').addClass('active');
                $('#id2').removeClass('active');

            }

            function selectone(product_id) {
                $.getJSON('product_action.php',
                        {r: Math.random(), action: 'get_pro', pro_id: product_id,page_name:'sales.php'},
                        function (data) {
                            var str = data.product_name;
                            if (data.product_model != '') {
                                str += ' ' + data.product_model;
                            }
                            if (data.product_made != '') {
                                str += ' ' + data.product_made;
                            }
                            $('input[name=name_gg]').eq(active_index).val(str);
                            $('input[name=hid_pro_id]').eq(active_index).val(product_id);
                            $('input[name=p_model]').eq(active_index).val(data.product_model);
                            $('input[name=p_made]').eq(active_index).val(data.product_made);                      
                            var company = $('#company_name').val();                           
                            
                            $.getJSON('sales_action.php',
                            {r: Math.random(), product_id: product_id,price_name:'零售价',company:company,
                                action:'get_price_by_product_id_and_price_name',page_name:'sales.php'},
                            function (stock_data) {
                                $('input[name=unit]').eq(active_index).val(stock_data.unit);
                                $('input[name=price]').eq(active_index).val(stock_data.product_price);
                                if(stock_data.unit==''){
                                    if(stock_data.tag_unit!='' && stock_data.tag_unit.indexOf('|')==-1){
                                        $('input[name=unit]').eq(active_index).val(stock_data.tag_unit);
                                    }
                                }                                
                                $('span[name=unit_hint]').eq(active_index).html(add_a(stock_data.tag_unit));
                                
				$('input[name=quantity]').eq(active_index).focus();

				add_a(stock_data.tag_unit);
                                
                            });
                            hideme();
                        });
	    }

	    function add_a(str){
		var out_str = '';
		if(str!=''){
			var list = str.split('|');
			for(var i=0;i<list.length;i++){
				var item = list[i];
				if(i>0){
					out_str += '|';
				}
				out_str += '<a href="javascript:void(0)" onclick="return fill_unit(this,\''+item+'\')">'+item+'</a>';
			}

		}
		return out_str;

	    }

	    function fill_unit(element,str){
		$(element).parent().parent().find('[name=unit]').eq(0).val(str);
		//alert(str);
		//ttt
	    }

            function computePrice(row) {
                var quantity = $(row).find('input[name=quantity]').val();
                var price = $(row).find('input[name=price]').val();
                var sum = $(row).find('input[name=money]').val();
                if (sum != '') {
                    if (quantity == '' && price == '') {
                        $(row).find('input[name=quantity]').val('1');
                        $(row).find('input[name=price]').val(sum);
                    } else if (quantity != '') {
                        price = accDiv(sum, quantity);
                        price = price.toFixed(2);
                        $(row).find('input[name=price]').val(price);
                    }
                }
                computeHj();

            }

            function add_line() {
                var len = $('#list_tb1').find('tbody').find('tr').length;
                var html = $('#list_tb1').find('tbody').find('tr').html();
                $('#list_tb1').find('tbody').find('tr').eq(len - 2).after('<tr>' + html + '</tr>');
                $('span[name=unit_hint]:last').html('');
                unbind_change();
                unbind_name_gg();
                bind_change();
                bind_name_gg();
            }

            function save_sales(go_where) {
                var company_name = $('#company_name').val();
                var date = $('#yy').val() + '-' + $('#mm').val() + '-' + $('#dd').val();
                var pro_id_list = $('input[name=hid_pro_id]');
                var pro_id_s = '';
                var name_gg_list = $('input[name=name_gg]');
                var name_gg_s = '';
                var p_model_list = $('input[name=p_model]');
                var p_model_s = '';
                var p_made_list = $('input[name=p_made]');
                var p_made_s = '';
                var unit_list = $('input[name=unit]');
                var unit_s = '';
                var quantity_list = $('input[name=quantity]');
                var quantity_s = '';
                var price_list = $('input[name=price]');
                var price_s = '';
                var money_list = $('input[name=money]');
                var money_s = '';
                var money_real_list = $('input[name=money_real]');
                var money_real_s = '';
                var remark_list = $('input[name=remark]');
                var remark_s = '';
                var hj = $('#money_hj').val();
                var hj_real = $('#money_hj_real').val();
                var remark_t = $('#remark_t').val();
                for (var i = 0; i < name_gg_list.length; i++) {
                    var name_gg = $(name_gg_list[i]).val();
                    if (name_gg != '') {
                        var unit = $(unit_list[i]).val();
                        var quantity = $(quantity_list[i]).val();
                        var price = $(price_list[i]).val();
                        var remark = $(remark_list[i]).val();
                        var money = $(money_list[i]).val();
                        var money_real = $(money_real_list[i]).val();
                        var pro_id = $(pro_id_list[i]).val();
                        var p_model = $(p_model_list[i]).val();
                        var p_made = $(p_made_list[i]).val();
                        name_gg_s += name_gg + ',';
                        unit_s += unit + ',';
                        quantity_s += quantity + ',';
                        price_s += price + ',';
                        money_s += money + ',';
                        money_real_s += money_real + ',';
                        remark_s += remark + ',';
                        pro_id_s += pro_id + ',';
                        p_model_s += p_model + ',';
                        p_made_s += p_made + ',';

                    }
                }
                $.post('sales_action.php?action=save_sales', {
                    batch_id: $('#edit_batch_id').val(),
                    company_name: company_name,
                    date: date,
                    pro_id: pro_id_s,
                    name_gg: name_gg_s,
                    p_model: p_model_s,
                    p_made: p_made_s,
                    unit: unit_s,
                    quantity: quantity_s,
                    price: price_s,
                    money: money_s,
                    money_real: money_real_s,
                    remark: remark_s,
                    hj: hj,
                    hj_real: hj_real,
                    remark_t: remark_t,
                    r: Math.random(),
                    page_name:'sales.php'
                }, function (data) {
                    if (data.indexOf('success') != -1) {  
                        if(go_where=='print'){
                            var batch_id_get = data.substr(8);
                            print_sales_pre(batch_id_get);
                            //window.open('sales_print.php?batch_id='+batch_id_get);
                        } else {
                            $('#list_div').show();
                            $('#add_div').hide();
                            $('#id2').addClass('active');
                            $('#id1').removeClass('active');
                            list(last_page_id);
                            hidemeC();
                        }                        
                    } else {
                        alert(data);
                    }
                })
            }

            function computeHj() {
                var money_list = $('input[name=money]');
                var total = 0;
                for (var i = 0; i < money_list.length; i++) {
                    var money = $(money_list[i]).val();
                    if (money != '') {
                        total = accAdd(total, money);
                    }
                }
                $('#money_hj').val(total);
                money_list = $('input[name=money_real]');
                total = 0;
                for (var i = 0; i < money_list.length; i++) {
                    var money = $(money_list[i]).val();
                    if (money != '') {
                        total = accAdd(total, money);
                    }
                }
                $('#money_hj_real').val(total);
            }

            function computeHjReal() {
                var money_list = $('input[name=money_real]');
                var total = 0;
                for (var i = 0; i < money_list.length; i++) {
                    var money = $(money_list[i]).val();
                    if (money != '') {
                        total = accAdd(total, money);
                    }
                }
                $('#money_hj_real').val(total);
            }

            function print_sales_pre(id){
                $('#hid_batch_id0').val(id);
                $('#oLayerP').css('left', 200);
                $('#oLayerP').css('top', 100);
                $('#oLayerP').show();
            }

            function print_sales(){
                var c = $('#print_company').val();
                var other_c = $('#txt_other_company').val();
                var id = $('#hid_batch_id0').val();
                $('#oLayerP').hide();
                window.open('sales_print.php?batch_id='+id+'&c='+c+'&o='+other_c);
            }

            function change_print_company(){
                if($('#print_company').val()=='4'){
                    $('#txt_other_company').show();
                } else {
                    $('#txt_other_company').hide();
                }
            }

            function add_init() {
                $('#add_div').show();
                $('#list_div').hide();
                clear_info();
            }

            function ret() {
                $('#add_div').hide();
                $('#list_div').show();
                clear_info();
                list(last_page_id);
            }

            function clear_info() {
                $('#edit_batch_id').val('0');
                $('#company_name').val('');
                var name_gg_list = $('input[name=name_gg]');
                var p_model_list = $('input[name=p_model]');
                var p_made_list = $('input[name=p_made]');
                var unit_list = $('input[name=unit]');
                var quantity_list = $('input[name=quantity]');
                var price_list = $('input[name=price]');
                var money_list = $('input[name=money]');
                var money_real_list = $('input[name=money_real]');
                var remark_list = $('input[name=remark]');
                var pro_id_list = $('input[name=hid_pro_id]');
                var unit_hint_list = $('span[name=unit_hint]');

                for (var i = 0; i < name_gg_list.length; i++) {
                    $(name_gg_list[i]).val('');
                    $(p_model_list[i]).val('');
                    $(p_made_list[i]).val('');
                    $(unit_list[i]).val('');
                    $(quantity_list[i]).val('');
                    $(price_list[i]).val('');
                    $(money_list[i]).val('');
                    $(money_real_list[i]).val('');
                    $(remark_list[i]).val('');
                    $(pro_id_list[i]).val('');
                    $(unit_hint_list[i]).html('');
                }
                $('#money_hj').val('');
                $('#remark_t').val('');
            }

            function list(page_id) {
                var client_name = $('#client_name').val();
                var filter = $('#filter').val();
                var start_time = $('#start_time').val();
                var end_time = $('#end_time').val();
                var min_money = $('#min_money').val();
                var max_money = $('#max_money').val();
                last_page_id = page_id;
                $.get('sales_action.php',
                        {r: Math.random(),
                            action: 'list_sales',
                            page_id: page_id,
                            client_name: client_name,
                            filter: filter,
                            start_time: start_time,
                            end_time: end_time,
                            min_money:min_money,
                            max_money:max_money,
                            page_name:'sales.php'
                        },
                        function (data) {
                            $('#list_div_tbl').html(data);
                        });
            }

            var last_page_id = 1;

            function go_page(page_id) {
                list(page_id);
            }

            function show_detail(batch_id, ele) {
                if ($('#detail_' + batch_id).length > 0) {

                } else {
                    $.get('sales_action.php?action=show_detail', {r: Math.random(), batch_id: batch_id,page_name:'sales.php'}, function (data) {
                        var new_data = '<tr><td colspan="7">' + data + '</td></tr>';
                        $(new_data).insertAfter($(ele).parent().parent());
                    });
                }



            }

            function hide_this(ele) {
                $(ele).parent().parent().parent().parent().parent().remove();
            }



            function list_sales() {
                $('#list_div').show();
                $('#add_div').hide();
                list(1);
            }

            function del_sales(batch_id) {
                if (confirm('确认要删除吗？')) {
                    $.get('sales_action.php?action=del_sales',
                            {r: Math.random(), batch_id: batch_id,page_name:'sales.php'},
                            function (data) {
                                if (data == 'success') {
                                    go_page(last_page_id);
                                }
                            }
                    );
                }

            }

			function set_rq_today(){
				$('#yy').val(yy);
				$('#mm').val(mm);
				$('#dd').val(dd);
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
                                <a href='javascript:void(0)' onclick='add_init()'>新增销售</a>
                            </li>
                            <li>
                                <a href='javascript:void(0)' onclick='list_sales()'>销售列表</a>
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
                                单位名称：
                                <input type='text' name='company_name' id='company_name'  style='width:350px' onkeyup="show_company()"   />
                            </td>
                            <td colspan='3' style='text-align:right;'>
                                日期：<input type='text' name='yy' id='yy' style='width:40px' value='<?php echo date("Y") ?>'  /> 年 
                                <input type='text' name='mm' id='mm' style='width:30px' value='<?php echo date("m") ?>'   /> 月 
                                <input type='text' name='dd' id='dd' style='width:30px' value='<?php echo date("d") ?>'   /> 日 
								&nbsp;&nbsp;<input type='button' value='今日' onclick='set_rq_today()' />

                            </td>
                        </tr>
                        <tr>
                            <th>品名及规格</th><th>单位</th>
                            <th>数量</th><th>单价</th><th>金额</th><th>实收金额</th><th>备注</th>
                        </tr>
                    </thead>	
                    <tbody>
                        <tr>
                            <td>
                                <input type='text' name='name_gg'  style='width:150px'   /><input type='hidden' name='hid_pro_id' />
                                <input type='hidden' name='p_model' />
                                <input type='hidden' name='p_made' />
                            </td>
                            <td>	<input type='text' name='unit'  style='width:60px'   /><span name="unit_hint"></span></td>
                            <td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money_real'  style='width:100px'   /></td>
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
                            <td>	<input type='text' name='unit'  style='width:60px'   /><span name="unit_hint"></span></td>
                            <td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money_real'  style='width:100px'   /></td>
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
                            <td>	<input type='text' name='unit'  style='width:60px'   /> <span name="unit_hint"></span>   </td>
                            <td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money_real'  style='width:100px'   /></td>
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
                            <td>	<input type='text' name='unit'  style='width:60px'   /><span name="unit_hint"></span></td>
                            <td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money_real'  style='width:100px'   /></td>
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
                            <td>	<input type='text' name='unit'  style='width:60px'   /><span name="unit_hint"></span></td>
                            <td style='text-align:right'>	<input type='text' name='quantity'  style='width:60px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='price'  style='width:80px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money'  style='width:100px'   /></td>
                            <td style='text-align:right'>	<input type='text' name='money_real'  style='width:100px'   /></td>
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
                            <td style='text-align:right;' >
                                合计：<input type='text' name='money_hj_real' id='money_hj_real'  style='width:100px'   />
                            </td>
                            <td>	<input type='button' value='增行' onclick='add_line()' /></td>
                        </tr>
                    </tbody>
                </table>
                <br />


                <input type='button' value='保存并跳转至列表' onclick='save_sales("")'/>
                <input type='button' value='保存并打印' onclick='save_sales("print")'/>
                <input type='button' value='返回' onclick='ret()'/>


            </div>

            <div id='list_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>

                <table id='search_tbl'  class='table table-bordered table-striped table-hover'>
                    <tr><td>

                            单位名称：<input id='client_name' name='client_name' style='width:250px;' />
                            筛选：<input id='filter' name='filter' style='width:150px;' />
                            开始时间：<input id='start_time' name='start_time' style='width:100px;' class='Wdate' onClick='WdatePicker()' value='<?php echo date("Y-m-d") ?>'  />
                            结束时间：<input id='end_time' name='end_time' style='width:100px;' class='Wdate' onClick='WdatePicker()'  />
                            金额：<input id="min_money" name="min_money" style="width:100px" />
                            -<input id="max_money" name="max_money" style="width:100px" />
                            <input type='button' value='搜索' onclick='list_sales(1)' />

                        </td></tr></table>

                <div id='list_div_tbl'>

                </div>



            </div>
            <div id='info' style='margin-left:50px;margin-right:50px;'>

            </div>
        </div>

        <div id="oLayer" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
             width: 800px; display:none;">
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

        <div id="oLayerP" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
             width: 800px; display:none;">
            <div id='oLayer_contentP' style='padding-left:50px;padding-top:50px;'>
            选择单位： 
                <select id='print_company' onchange='change_print_company()'>
                    <option value='1'>渔舜五金</option>
                    <option value='2'>万红五金</option>
                    <option value='3'>朗歌五金</option>
                    <option value='4'>其他</option>
                </select>
                <input type='text' id='txt_other_company' style='display:none;' />
                <input type='hidden' id='hid_batch_id0' value='' />
                
                <input type='button' value='打印' onclick='print_sales()' />
            </div>

            <div style='float:right;'>
                <input type='button' value='x' onclick='hidemeP()' />
            </div>
        </div>


        <?php
        require_once ( 'change_password.php');
        ?>
    </body>
</html>


