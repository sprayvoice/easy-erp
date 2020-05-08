<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"]="product.php";
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
        <script src="common.js?r=20180626"></script>

        <title>商品列表</title>
        <style type="text/css">
            #wrapper {
                width: 100%;height:100%;
            }
            .container {width: 100%;height:100%;}
            td {padding:5px;}
            .tb1 {border:2px solid;border-spacing:0px; }
            .tb1 th {margin:3px;padding:5px;border:1px gray solid;}
            .tb1 td { border:1px gray solid; margin:2px;padding:3px; border-collapse : collapse;}
            #span_tag { margin-left:50px;margin-right:50px;}
            
            
            .table-hover > tbody > tr:hover > td,
            .table-hover > tbody > tr:hover > th {
              background-color: yellowgreen;
            }
            
        </style>	
        <script type='text/javascript'>



            $(document).ready(function () {
                list_pro();
                list_tags();
                $('#price_edit_div').hide();
                $('#filter').bind('focus',filter_time);
                $('#price_div').toggle();
                $('#price_div2').toggle();



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
            
            filter_time = function(){
                //$(this).val('');/*清除数据*/
                var time = setInterval(list_pro, 1000);/*每1秒执行一次人员筛选，time是停止本方法的参数*/
                $(this).bind('blur',function(){
                    clearInterval(time); /*停止setInterval*/
                });
            };
            
            function show_stock(product_id){
                var url = 'product_action.php?action=get_stock';
                $.get(url,
                    {page_name:'product.php',pro_id:product_id,r:Math.random()},
                    function(data){
                        $('#stock_'+product_id).text(data);
                });
                
            }

            function changeSort() {
                list_pro();
            }
            function add_init() {
                $('#mode1').val('add_pro');
                $('#pro').val('');
                $('#tag').val('');
                $('#model').val('');
                $('#made').val('');
                $('#remark').val('');
                $('#pym').val('');                
                $('#add_div').show();
                $('#list_div').hide();
                $('#is_stock').prop('checked', 'true');
            }

            function fill_tag(tag) {
                $('#filter').val(tag);
                $('#editor_product_id').val('');
                list_pro();
            }



            function copy1(product_id) {
                $.getJSON('product_action.php?action=get_pro',
                        {pro_id: product_id, r: Math.random(),page_name:'product.php'},
                        function (data) {
                            $('#mode1').val('add_pro');
                            $('#pro').val(data.product_name);
                            $('#tag').val(data.product_tags);
                            $('#model').val(data.product_model);
                            $('#made').val(data.product_made);
                            $('#pym').val(data.pym);                            
                            $('#remark').val(data.remark);
                            $('#hid_pro_id').val('');
                            $('#add_div').show();
                            $('#list_div').hide();
                            if (data.is_stock == 1) {
                                $('#is_stock').prop('checked', 'true');
                            } else {
                                $('#is_stock').removeAttr('checked');
                            }
                       
                             if (data.is_not_used == 1) {
                                $('#is_not_used').prop('checked', 'true');
                            } else {
                                $('#is_not_used').removeAttr('checked');
                            }
                        });
            }



          

            function show_price(product_id) {
                $.get('product_action.php?action=get_price_by_pid',
                        {pro_id: product_id, r: Math.random(),page_name:'product.php'},
                        function (data) {
                            $('#hid_p_pro_id').val(product_id);
                            $('#price_div').html(data);
                            $('#price_edit_div').hide();
                            $('#price_div').show();
                            $('#location_div').hide();
                            $('#editor_product_id').val(product_id);

                            $.getJSON('stock_action.php',
                                    {r: Math.random(), action: 'get_stock', product_id: product_id,page_name:'product.php'},
                                    function (stock_data) {

                                        $('#price_div2').html(
                                                '库存单价：' + stock_data.stock_price + '<br />'
                                                + '单位：' + stock_data.stock_unit + '<br />'
                                                + '库存数量：' + stock_data.stock_quantity + '<br />'
                                                + '盘点日期：' + stock_data.last_upd_date + '<br />'
                                                );
                                        $('#price_div2').show();
                                    });

                            list_pro();



                        });

            }
           

            function add_price(product_id) {
                $.getJSON('product_action.php?action=get_pro',
                        {pro_id: product_id, r: Math.random(),page_name:'product.php'},
                        function (data) {
                            $('#hid_product_price_id').val('');
                            $('#hid_p_pro_id').val(product_id);
                            $('#span_pro2').html(data.product_name);
                            $('#span_model2').html(data.product_model);
                            $('#span_made2').html(data.product_made);
                            $('#price_edit_div').show();
                            
                        });

            }

         

            function save_product_price() {
                var pro_id = $('#hid_p_pro_id').val();
                var price_id = $('#hid_product_price_id').val();
                var price_name = $('#price_name').val();
                var price = $('#price').val();
                var unit = $('#unit').val();
                var is_hide = $('#is_hide').val();
                $.post('product_action.php?action=save_product_price',
                        {id: price_id, pro_id: pro_id, price_name: price_name, price: price, unit: unit, is_hide: is_hide,page_name:'product.php', r: Math.random()},
                        function (data) {
                            if (data == 'success') {
                                show_price(pro_id);
                                $('#info').html('');
                            } else {
                                $('#info').html(data);
                            }
                        });

            }

         

            function del_price(id) {
                var pro_id = $('#hid_p_pro_id').val();
                $.get('product_action.php?action=del_price_by_id',
                        {id: id, r: Math.random(),page_name:'product.php'},
                        function (data) {
                            if (data == 'success') {
                                show_price(pro_id);
                                $('#info').html('');
                            }
                        });
            }

            function list_tags() {
                $.get('product_action.php?action=list_tags',
                        {r: Math.random(),page_name:'product.php'},
                        function (data) {                            
                            $('#span_tag').html(data);  			
                        });
            }

        

            function start_change_price() {

                $('#add_div').hide();
                $('#list_div').show();
                var sort1 = $('#sort1').val();
                var filter1 = $('#filter').val();
                var price_name1 = $('#price_name1').val();
                var id = $('#editor_product_id').val();
                $.get('product_action.php?action=list_pro_for_price',
                        {r: Math.random(), sort1: sort1, filter1: filter1, id: id, price_name: price_name1,page_name:'product.php'},
                        function (data) {
                            $('#content_div').html(data);
                            $('#tag_div').html('');

                        });

            }

            function start_sort() {
                $('#add_div').hide();
                $('#list_div').show();
                var sort1 = $('#sort1').val();
                var filter1 = $('#filter').val();
                var id = $('#editor_product_id').val();
                var filter_type = $('#filter_type').val();
                $.get('product_action.php?action=list_pro_for_sort',
                        {r: Math.random(), sort1: sort1, filter1: filter1, id: id,filter_type:filter_type,page_name:'product.php'},
                        function (data) {
                            $('#content_div').html(data);
                            $('#tag_div').html('');


                        });
            }

            function change_sort(product_id) {
                var product_sort = $('#sort_' + product_id).val();
                $.get('product_action.php?action=update_product_sort',
                        {r: Math.random(), product_id: product_id, product_sort: product_sort,page_name:'product.php'},
                        function (data) {
                            if (data == 'success') {

                            } else {
                                alert(data);
                            }
                        });
            }
            
            function change_is_include_component(){
                   if ($('#is_include_component').is(":checked")) {
                    $('#is_include_component_div').show()
                } else {
                    $('#is_include_component_div').hide();
                }
            }

            function change_price(product_id) {
                var product_price = $('#price_' + product_id).val();
                var price_name = $('#price_name1').val();
                var unit1 = $('#unit1').val();
                var is_hide = 0;
                if (price_name == '进价') {
                    is_hide = 1;
                }
                $.post('product_action.php?action=update_product_price',
                        {r: Math.random(), pro_id: product_id, 
                            price_name: price_name, price: product_price, 
                            unit: unit1, is_hide: is_hide,page_name:'product.php'},
                        function (data) {
                            if (data == 'success') {

                            } else {
                                alert(data);
                            }
                        });
            }

            function edit_price(id) {
                $.getJSON('product_action.php?action=get_price_by_id',
                        {id: id, r: Math.random(),page_name:'product.php'},
                        function (data) {
                            $('#hid_product_price_id').val(id);
                            $('#price_name').val(data.price_name);
                            $('#price').val(data.product_price);
                            $('#unit').val(data.unit);
                            $('#price_edit_div').show();
                            $('#is_hide').val(data.is_hide);

                            $.getJSON('product_action.php?action=get_pro',
                                    {pro_id: data.product_id, page_name:'product.php',r: Math.random()},
                                    function (data) {
                                        $('#span_pro2').html(data.product_name);
                                        $('#span_model2').html(data.product_model);
                                        $('#span_made2').html(data.product_made);
                                        $('#hid_p_pro_id').val(data.product_id);

                                    });

                        });


            }

            function edit1(product_id) {
                $.getJSON('product_action.php?action=get_pro',
                        {pro_id: product_id, r: Math.random(),page_name:'product.php'},
                        function (data) {
            
                            $('#mode1').val('edit_pro');
                            $('#pro').val(data.product_name);
                            $('#tag').val(data.product_tags);
                            $('#model').val(data.product_model);
                            $('#made').val(data.product_made);
                            $('#pym').val(data.pym);                            
                            $('#remark').val(data.product_remark);
                            $('#hid_pro_id').val(product_id);
                            if (data.is_stock == 1) {
                                $('#is_stock').prop('checked', 'true');
                            } else {
                                $('#is_stock').removeAttr('checked');
                            }
                            if(data.is_not_used == 1){
                            	$('#is_not_used').prop('checked', 'true');
                            } else {
                            	$('#is_not_used').removeAttr('checked');
                            }
                            if(data.is_include_component==1){
                                $('#is_include_component').prop('checked', 'true');
                                $.getJSON('product_action.php?action=get_pro_component',
                                {pro_id:product_id,r:Math.random(),page_name:'product.php'},function(data1){
                                    $('#is_include_component_div').show();
                                    var str = '';
                                    clear_comp_tbl();
                                    for(var i=0;i<data1.length;i++){
                                        var product_name = data1[i].product_name;
                                        var product_model = data1[i].product_model;
                                        var product_made = data1[i].product_made;
                                        var product_quantity = data1[i].component_product_quantity;
                                        var str = '<tr>';
                                        str += '<td>'+product_name+'</td>';
                                        str += '<td>'+product_model+'</td>';
                                        str += '<td>'+product_made+'</td>';                            
                                        str += '<td>'+'<input type="hidden" value="'+data1[i].product_id+'" name="comp_id"  />'
                                                +'<input type="text" value="'+product_quantity+'" name="comp_quantity" style="width:80px;" />'+'</td>';
                                        str += '<td>'+'<input type="button" value="删除" name="btn_del" onclick="del_this_row(this)" />'+'</td>';
                                        str += '</tr>';
                                        var index1 = $('#incude_component_tbl').find('tr').length-2;
                                        $('#incude_component_tbl').find('tr').eq(index1).after(str);
                                    }
                                });
                            } else {
                                $('#is_include_component').removeAttr('checked');
                                $('#is_include_component_div').hide();
                            }
                            $('#add_div').show();
                            $('#list_div').hide();
                        });
                        $.getJSON('product_action.php?action=get_pro_unit',
                        {pro_id:product_id,r:Math.random(),page_name:'product.php'},
                        function(data){
                           $('#unit_name1').val(data.unit1);
                           $('#unit_quantity1').val(data.q1);
                           $('#unit_name2').val(data.unit2);
                           $('#unit_quantity2').val(data.q2);
                           $('#unit_name3').val(data.unit3);
                           $('#unit_quantity3').val(data.q3);
                        });
            }
            
            function del1(product_id) {
                if (!confirm('确定要删除吗？')) {
                    return;
                }
                $.post('product_action.php?action=del_pro',
                        {pro_id: product_id, r: Math.random(),page_name:'product.php'},
                        function (data) {
                            if (data == 'success') {
                                list_pro();
                                $('#info').html('');
                            } else {
                                $('#info').html(data);
                            }
                        });

            }
            function save_pro(num) {
                var mode1 = $('#mode1').val();
                var is_stock = '';                
                if ($('#is_stock').is(":checked")) {
                    is_stock = '1';
                } else {
                    is_stock = '0';
                }
                var is_include_component = '0';
                if ($('#is_include_component').is(":checked")) {
                    is_include_component = '1';
                } else {
                    is_include_component = '0';
                }
                var is_not_used = '0';
                  if ($('#is_not_used').is(":checked")) {
                    is_not_used = '1';
                } else {
                    is_not_used = '0';
                }
                var comp_id_ar = $('input[name=comp_id]');
                var comp_quantity_ar = $('input[name=comp_quantity]');
                var comp_id_str = '';
                var comp_quantity_str = '';
                if(is_include_component=='1'){
                    if(comp_id_ar && comp_id_ar.length>0){
                        for(var i=0;i<comp_id_ar.length;i++){
                            comp_id_str = comp_id_str+','+$(comp_id_ar[i]).val();
                            comp_quantity_str = comp_quantity_str+','+$(comp_quantity_ar[i]).val();
                        }
                    }
                }     
                
                $.post('product_action.php?action=' + mode1,
                        {product_id: $('#hid_pro_id').val(),
                            pro: $('#pro').val(),
                            tag: $('#tag').val(),
                            made: $('#made').val(),
                            model: $('#model').val(),
                            pym:$('#pym').val(),                            
                            is_stock: is_stock,
                            is_not_used:is_not_used,
                            unit1:$('#unit_name1').val(),
                            quantity1:$('#unit_quantity1').val(),
                            unit2:$('#unit_name2').val(),
                            quantity2:$('#unit_quantity2').val(),
                            unit3:$('#unit_name3').val(),
                            quantity3:$('#unit_quantity3').val(),
                            remark:$('#remark').val(),
                            is_include_component:is_include_component,
                            comp_id:comp_id_str,
                            comp_quantity:comp_quantity_str,
                            r: Math.random(),
                            page_name:'product.php'},
                        function (data) {
                            if (data != 'success') {
                                $('#info').html(data);
                            } else {
                                $('#info').html('');
                                  if (num == 1) {
	                                $('#pro').val('');
	                                $('#tag').val('');
	                                $('#model').val('');
	                                $('#made').val('');
	                                $('#pym').val('');                                
	                                $('#remark').val('');
	                                $('#is_stock').prop('checked', 'true');
	                                $('#is_include_component').removeAttr('checked');
	                                $('#add_div').show();
	                                $('#list_div').hide();
	                            } else if (num == 2) {
	                                list_pro();
	                            }
                            }
                          
                            
                        });
            }
            function ret() {
                list_pro();
            }
            
            function changeFilter(){
                list_pro();
            }
            
            function print_in_price(){
              var filter1 = $('#filter').val();
              var price_name = $('#price_name1').val();
           	window.open('product_print.php?filter1='+filter1+'&price_name='+price_name+'&r='+Math.random());
           	
            }
            
            function list_pro() {
                $('#add_div').hide();
                $('#list_div').show();
                var sort1 = $('#sort1').val();
                var filter1 = $('#filter').val();
                var filter_type = $('#filter_type').val();
                var id = $('#editor_product_id').val();
                $.get('product_action.php?action=list_pro',
                        {r: Math.random(), page_name:'product.php',sort1: sort1, filter1: filter1, id: id,filter_type:filter_type},
                        function (data) {
                            $('#content_div').html(data);
                            $('#tag_div').html('');
                            $('#info').html('');
                        });
            }

            function clearId() {
                $('#editor_product_id').val('');
                list_pro();
                $('#price_div').hide();
                $('#price_div2').html('');

            }

            function show_product_price() {
                $('#add_div').hide();
                $('#list_div').show();
                var sort1 = $('#sort1').val();
                var filter1 = $('#filter').val();
                var price_name1 = $('#price_name1').val();
                var id = $('#editor_product_id').val();
                $.get('product_action.php?action=list_pro_for_price_show',
                        {r: Math.random(), sort1: sort1, filter1: filter1, id: id, price_name: price_name1,page_name:'product.php'},
                        function (data) {
                            $('#content_div').html(data);
                            $('#tag_div').html('');

                        });
            }

            function show_ext() {
                $('#ext_div').show();
            }

            function hide_ext() {
                $('#ext_div').hide();
            }

            function merge_pro() {
                var to_merge_pro = $('#to_merge_pro').val();
                var merge_to_pro = $('#merge_to_pro').val();
                if (to_merge_pro != '' && merge_to_pro != '') {

                    $.post('product_action.php?action=merge_pro',
                            {to_merge_pro: to_merge_pro, merge_to_pro: merge_to_pro, r: Math.random(),page_name:'product.php'}, function (data) {
                        if (data == 'success') {
                            list_pro();
                        } else {
                            alert(data);
                        }
                    });
                }
            }
            
            function merge_tag(){
                var to_merge_tag = $('#to_merge_tag').val();
                var merge_to_tag = $('#merge_to_tag').val();
                if (to_merge_tag != '' && merge_to_tag != '') {
                    $.post('product_action.php?action=merge_tag',
                            {to_merge_tag: to_merge_tag, merge_to_tag: merge_to_tag, r: Math.random(),page_name:'product.php'}, function (data) {
                        if (data == 'success') {
                            list_pro();
                            list_tags();
                        } else {
                            alert(data);
                        }
                    });
                }
            }
            
            function compute_pym(){
                var pro = $('#pro').val();
                var model = $('#model').val();
                var made = $('#made').val();
                var tag = $('#tag').val();
                $.post('product_action.php?action=get_pym',
                {r:Math.random(),pro:pro,model:model,made:made,tag:tag,page_name:'product.php'},function(data){
                    $('#pym').val(data);
                });
            }
            
               function hideme() {
                $('#oLayer').hide();
            }

            function add_new_component(){
                $('#oLayer_content').html('');
                var top = $('#add_new_component_btn').offset().top;
                var left = $('#add_new_component_btn').offset().left;
                $('#oLayer').css('left', left+80);
                $('#oLayer').css('top', top );
                $('#oLayer').show();
               list_component_filter();                     
            }
            
            function list_component_filter(){
                 var key = $('#filter2').val();
                $.get('product_action.php',
                                {action: 'list_pro_for_instock', r: Math.random(), filter1: key,page_name:'product.php'},
                                function (data) {
                                    if (data == '') {
                                        $('#oLayer').hide();
                                    } else {
                                        $('#oLayer_content').html(data);
                                    }

                                });
            }
            
            function del_this_row(ele){
                $(ele).parent().parent().remove();
            }
            
            function clear_comp_tbl(){
                var c1 = $('#incude_component_tbl').find('tr').length;
                while(c1>2){
                    var index1 = $('#incude_component_tbl').find('tr').length-2;
                    $('#incude_component_tbl').find('tr').eq(index1).remove();
                    c1 = $('#incude_component_tbl').find('tr').length;
                }
            }
            
            function selectone(product_id){
                $.getJSON('product_action.php',
                        {r: Math.random(), action: 'get_pro', pro_id: product_id,page_name:'product.php'},
                        function (data) {
                            var product_name = data.product_name;
                            var product_model = data.product_model;
                            var product_made = data.product_made;
                            var str = '<tr>';
                            str += '<td>'+product_name+'</td>';
                            str += '<td>'+product_model+'</td>';
                            str += '<td>'+product_made+'</td>';                            
                            str += '<td>'+'<input type="hidden" value="'+product_id+'" name="comp_id"  />'
                                    +'<input type="text" value="1" name="comp_quantity" style="width:80px;" />'+'</td>';
                            str += '<td>'+'<input type="button" value="删除" name="btn_del" onclick="del_this_row(this)" />'+'</td>';
                            str += '</tr>';
                            var index1 = $('#incude_component_tbl').find('tr').length-2;
                            $('#incude_component_tbl').find('tr').eq(index1).after(str);
                            hideme();
                        });
            }
            
            
        </script>
    </head>
    <body>
        <div id='wrapper' style="position:absolute;left:0px;top:0px;">
            <div class="navbar" id='top_div'>
                <div class="navbar-inner">
                  <?php include("top_div1.php") ?>


                    <div id='span_tag'></div>

                    <div style='padding-left:0px;padding-top:0px;margin-left:50px;margin-right:50px;' id='list_search_div'>
                        <select id='filter_type' name='filter_type' onchange="changeFilter()" style="width:120px;">
                            <option value='all' selected="selected">搜索全部</option>
                            <option value='name'>搜索名称</option>
                            <option value='tag'>搜索标签</option>
                            <option value='model'>搜索规格</option>
                            <option value='made'>搜索品牌产地</option>
                        </select>
                        <input type='text' id='filter' />
                        <input type='button' value='x' onclick='fill_tag("")' />
                        <select id='sort1' name='sort1' onchange="changeSort()">
                            <option value='1'>按序号顺序排序</option>
                            <option value='2'>按序号倒叙排序</option>
                            <option value='3'>按名称,规格,品牌排序</option>
                            <option value='4'>按名称,品牌,规格排序</option>
                            <option value='5' selected>按名称,品牌,排序字段,规格排序</option>
                        </select>
                        <input type="button" value="排序" onclick="start_sort()" />
                        <input type="button" value="扩镇功能" onclick="show_ext()" />


                        <input type='hidden' id='editor_product_id' value='' />    

                        <div style="position:inline; float: right;">
                            <ul class="nav">  
                                <li>
                                    <a href='javascript:void(0)' onclick='add_init()'>添加商品</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='list_pro()'>商品列表</a>
                                </li>
                            </ul>
                        </div>

                        <div id="ext_div" style="display:none;">
                            <input type="button" value="价格修改" onclick="start_change_price()" /> 
                            <select id="price_name1" name="price_name1" onchange="show_product_price()">
                                <option value='零售价' >零售价</option>
                                <option value='进价' >进价</option>
                                <option value='批发价' >批发价</option>
                            </select>
                            <input type='text' id='unit1' name='unit1' style='width:80px;'/>
                            <input type='button' value='打印价格' onclick='print_in_price()' />
                            <br />
                            <input type="button" value="合并商品" onclick="merge_pro()" />
                            <input type="text" value="" id="to_merge_pro" style="width:100px;" />
                            合并到 
                            <input type="text" value="" id="merge_to_pro" style="width:100px;" />
                            <br />
                            <input type="button" value="合并标签" onclick="merge_tag()" />
                            <input type="text" value="" id="to_merge_tag" style="width:100px;" />
                            合并到 
                            <input type="text" value="" id="merge_to_tag" style="width:100px;" />    
                            <br />
                            <input type="button" value="隐藏" onclick="hide_ext()" />
                        </div>

                    </div>

                </div>
            </div>
            <div class='container'>
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
                                
                                <div class="control-group">
                                    <label class="control-label" >备注</label>
                                    <div class="controls">
                                        <input type='text' id='remark' />
                                    </div>
                                </div>
                                
                                <div class="control-group">
                                    <label class="control-label" >拼音码</label>
                                    <div class="controls">
                                        <input type='text' id='pym' /><input type="button" value="获取" onclick="compute_pym()" />
                                    </div>
                                </div>
                                	
                               <div class="control-group">
                                    <label class="control-label" >弃用</label>
                                    <div class="controls">
                                        <input type="checkbox" value="is_not_used" id='is_not_used'> 
                                    </div>
                                </div>
                              
                                <div class="control-group">
                                    <label class="control-label" >计入库存</label>
                                    <div class="controls">
                                        <input type="checkbox" value="is_stock" id='is_stock'> 
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" >  <input type='text' id='unit_quantity1' style='width:50px; text-align:right;' /></label>
                                    <div class="controls">
                                       <input type='text' id='unit_name1' />
                                    </div>
                                </div>
                              
                                 <div class="control-group">
                                    <label class="control-label" >  <input type='text' id='unit_quantity2'  style='width:50px; text-align:right;' /></label>
                                    <div class="controls">
                                       <input type='text' id='unit_name2' />
                                    </div>
                                </div>
                         
                                <div class="control-group">
                                    <label class="control-label" >  <input type='text' id='unit_quantity3'  style='width:50px; text-align:right;' /></label>
                                    <div class="controls">
                                       <input type='text' id='unit_name3' />
                                    </div>
                                </div>
                             
                                 <div class="control-group">
                                    <label class="control-label" >包含部件</label>
                                    <div class="controls">
                                        <input type="checkbox" value="is_include_component" id='is_include_component' onclick="change_is_include_component()" /> 
                                    </div>
                                </div>
                                <div class="control-group" id="is_include_component_div" style="display: none;">
                                   
                                    <div class="controls">
                                        <table id="incude_component_tbl">
                                            <tr>
                                                <td>
                                                    商品名称
                                                </td>
                                                <td>
                                                    规格
                                                </td>
                                                <td>
                                                    产地/品牌
                                                </td>                                              
                                                <td>
                                                    数量
                                                </td>
                                                <td>
                                                    操作
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" style="text-align:right;">
                                                    <input id="add_new_component_btn" type="button" value="添加" onclick="add_new_component()" class="btn btn-primary" />
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>


                                <div class="form-actions">
                                    <input type='button' value='保存并继续添加' onclick='save_pro(1)' class="btn btn-primary"/>
                                    <input type='button' value='保存并跳转至列表' onclick='save_pro(2)' class="btn btn-primary"/>
                                    <input type='button' value='返回' onclick='ret()' class="btn btn-primary"/>

                                </div>

                                <input type='hidden' id='hid_pro_id' />

                            </form>
                        </div>
                    </div>
                </div>
                <div id='list_div'>	    

                    <div id='content_div' style='margin-left:50px;margin-right:50px;'>

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
                
                 <div id="oLayer" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
             width: 800px; display:none;">
                     过滤： <input type="text" value="" id="filter2" />
                     <input type="button" value="搜索" onclick="list_component_filter()" class="btn btn-primary" />
            <div id='oLayer_content'>

            </div>

            <div style='float:right;'>
                <input type='button' value='x' onclick='hideme()' />
            </div>
        </div>
                
            </div>
<?php
require_once ( 'change_password.php');
?>
        </div>
    </body>
</html>


