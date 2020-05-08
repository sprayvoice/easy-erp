<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"] = "stock.php";
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
            .container {width: 100%;height:100%;}
            td {padding:5px;}
            .tb1 {border:2px solid;border-spacing:0px; }
            .tb1 th {margin:3px;padding:5px;border:1px gray solid;}
            .tb1 td { border:1px gray solid; margin:2px;padding:3px; border-collapse : collapse;}
            #span_tag { margin-left:50px;margin-right:50px;}
            .red {background-color:#EED5B7;}
        </style>	
        <script type='text/javascript'>



            $(document).ready(function () {
                list_pro();
                list_tags();
                $('#filter').bind('focus',filter_time);                

                $.each($('.nav li'), function (name, value) {
                    $(this).click(function () {
                        var len = $('.nav li').length;
                        for (var i = 0; i < len; i++) {
                            $('.nav li').eq(i).removeClass('active');
                        }
                        $(this).addClass('active');
                    });
                });

            });
            
            filter_time = function(){
                //$(this).val('');/*清除数据*/
                var time = setInterval(list_pro, 1000);/*每1秒执行一次人员筛选，time是停止本方法的参数*/
                $(this).bind('blur',function(){
                    clearInterval(time); /*停止setInterval*/
                });
            };

            function changeSort() {
                list_pro();
            }


            function fill_tag(tag) {
                $('#filter').val(tag);
                list_pro();
            }

            function list_tags() {
                $.get('stock_action.php?action=list_tags',
                        {r: Math.random(),page_name:'stock.php'},
                        function (data) {
                            $('#span_tag').html(data);
                            if (IsPC()) {
                                var top_div_height = $('#sort1').offset().top + 30;
                                $('body').css('padding-top', top_div_height + 'px');
                            }
                        });
            }

            function pandian1(pro_id, ele) {
                var html = $(ele).parent().prev().prev().prev().html();
                html = html + '<input type="text" id="pandian_value" value="" style="width:50px;background-color:yellow;" /> ';
                html = html + '<input type="button" id="pandian_save_btn" value="保存" onclick="save_pandian1(' + pro_id + ',this)" /> <input type="button" value="取消" onclick="cancel_pandian1(this)" id="pandian_cancel_btn" />';
                $(ele).parent().prev().prev().prev().html(html);
                $('#pandian_value').focus();
            }

            function save_pandian1(pro_id, ele) {
                var value = $('#pandian_value').val();
                if (value != '') {
                    $.get('stock_action.php',
                            {r: Math.random(), action: 'save_stock', product_id: pro_id, stock_quantity: value,page_name:'stock.php'},
                            function (data) {
                                if (data != 'success') {
                                    alert(data);
                                }
                            });
                }

                $('#pandian_cancel_btn').remove();
                $('#pandian_save_btn').remove();
                $('#pandian_value').remove();
                list_pro();
            }

            function cancel_pandian1(ele) {
                $('#pandian_cancel_btn').remove();
                $('#pandian_save_btn').remove();
                $('#pandian_value').remove();
            }

            function ret() {
                list_pro();

            }                       
            
            function list_pro() {

                $('#list_div').show();
                $('#add_div').hide();
                
                var sort1 = $('#sort1').val();
                var filter1 = $('#filter').val();
                var show_low = '0';
                 if ($('#btn_show_low').is(":checked")) {
                    show_low = '1';
                } else {
                    show_low = '0';
                }
                var show_recent = '0';
                if($('#btn_show_recent').is(':checked')){
                    show_recent = '1';
                } else {
                    show_recent = '0';    
                }                
                $.get('stock_action.php?action=list_pro',
                        {r: Math.random(), sort1: sort1, filter1: filter1,page_name:'stock.php',show_low:show_low,show_recent:show_recent},
                        function (data) {
                            $('#content_div').html(data);
                            $('#tag_div').html('');
                        });
            }

            function clearId() {
                list_pro();


            }

            function add_init() {
                $('#mode1').val('add_stock');
                $('#hid_pro_id').val('');
                $('#pro').val('');
                $('#model').val('');
                $('#made').val('');
                $('#stock_quantity').val('');
                $('#stock_unit').val('');
                $('#stock_price').val('');
                $('#low_quantity').val('');
                $('#remark').val('');
                $('#add_div').show();
                $('#list_div').hide();
                

            }

            function sel_pro() {
                $('#oLayer_content').html('');
                var sel_btn = $('#sel_btn');
                var top = sel_btn.offset().top;
                var left = sel_btn.offset().left;
                $('#oLayer').css('left', left + 40);
                $('#oLayer').css('top', top + 40);
                $('#oLayer').show();
                list_serach_pro();
            }

            function list_serach_pro() {
                var key = $('#filter2').val();
                $.get('product_action.php',
                        {action: 'list_pro_for_instock', r: Math.random(), filter1: key,page_name:'stock.php'},
                        function (data) {
                            if (data == '') {
                                $('#oLayer_content').html('');
                            } else {
                                $('#oLayer_content').html(data);
                            }
                        });
            }

            function filter_search() {
                list_serach_pro();
            }

            function edit_stock1(product_id) {
                add_init();

                $.getJSON('product_action.php',
                        {r: Math.random(), action: 'get_pro', pro_id: product_id,page_name:'stock.php'},
                        function (data) {
                            $('#hid_pro_id').val(product_id);
                            $('#pro').val(data.product_name);
                            $('#model').val(data.product_model);
                            $('#made').val(data.product_made);
                            $.getJSON('stock_action.php',
                                    {r: Math.random(), action: 'get_stock', product_id: product_id,page_name:'stock.php'},
                                    function (stock_data) {
                                        $('#stock_unit').val(stock_data.stock_unit);
                                        $('#stock_quantity').val(stock_data.stock_quantity);
                                        $('#stock_price').val(stock_data.stock_price);
                                        $('#low_quantity').val(stock_data.low_quantity);
                                        $('#remark').val(stock_data.remark);
                                    });

                        });



            }

            function selectone(product_id) {

                $('#hid_pro_id').val('');
                $('#pro').val('');
                $('#model').val('');
                $('#made').val('');
                $('#stock_quantity').val('');
                $('#stock_unit').val('');
                $('#stock_price').val('');
                $('#low_quantity').val('');
                $('#remark').val('');

                $.getJSON('product_action.php',
                        {r: Math.random(), action: 'get_pro', pro_id: product_id,page_name:'stock.php'},
                        function (data) {
                            $('#hid_pro_id').val(product_id);
                            $('#pro').val(data.product_name);
                            $('#model').val(data.product_model);
                            $('#made').val(data.product_made);
                            $.getJSON('stock_action.php',
                                    {r: Math.random(), action: 'get_stock', product_id: product_id,page_name:'stock.php'},
                                    function (stock_data) {
                                        $('#stock_unit').val(stock_data.stock_unit);
                                        $('#stock_quantity').val(stock_data.stock_quantity);
                                        $('#stock_price').val(stock_data.stock_price);
                                        $('#low_quantity').val(stock_data.low_quantity);
                                    });
                            hideme();

                        });
            }

            function hideme() {
                $('#oLayer').hide();
            }

            function save_stock1(){
                var product_id = $('#hid_pro_id').val();
                if(product_id==''){
                    return;
                }
                var product_name = $('#pro').val();
                var product_model = $('#model').val();
                var product_made = $('#made').val();
                var stock_price = $('#stock_price').val();
                var stock_quantity = $('#stock_quantity').val();
                var stock_unit = $('#stock_unit').val();
                var low_quantity = $('#low_quantity').val();
                var remark = $('#remark').val();
                
                $.post('stock_action.php?action=save_stock_full',
                {
                    product_id:product_id,product_name:product_name,
                    product_model:product_model,product_made:product_made,
                    stock_price:stock_price,stock_quantity:stock_quantity,
                    stock_unit:stock_unit,r:Math.random(),page_name:'stock.php',
                    low_quantity:low_quantity,remark:remark
                },function(data){
                    if(data=='success'){
                        list_pro();
                    } else {
                        alert(data);
                    }
                });
            }
            
            function del_stock1(){
            
                var product_id = $('#hid_pro_id').val();
                if(product_id==''){
                    return;
                }
                del_by_product_id_callback(product_id,list_pro);
                
                /*$.get('stock_action.php?action=del_stock',{
                     product_id:product_id,r:Math.random(),page_name:'stock.php'
                },function(data){
                    if(data!='success'){
                        alert(data);
                    } else {
                         list_pro();
                    }
                });*/
            }
            
            function del_selected(){
            	var eles = $('input[name=hid_id]');
            	for(var i=0;i<eles.length;i++){
            		if($(eles[i]).prop('checked')){
            			var product_id = $(eles[i]).val();
            			del_by_product_id_callback(product_id,list_pro);
            		}
            	}
            }

            function add_to_group_selected(){
                var filter1 = $('#filter').val();
                var eles = $('input[name=hid_id]');
                var ids_str = '';
            	for(var i=0;i<eles.length;i++){
            		if($(eles[i]).prop('checked')){
            			var product_id = $(eles[i]).val();
                        ids_str += product_id+',';
            		}
            	}
                console.log('todo');
                console.log(ids_str);
                $.post('product_stock_group_action.php?action=add_group&r='+Math.random(),
                {ids:ids_str,name:filter1,page_name:'stock.php'},function(data){
                    if(data=='success'){
                        alert('添加成功');
                    } else {
                        alert(data);
                    }
                });
            }
            
            function del_by_product_id_callback(product_id,callback){
            	 $.get('stock_action.php?action=del_stock',{
                     product_id:product_id,r:Math.random(),page_name:'stock.php'
                },function(data){
                    if(data!='success'){
                        alert(data);
                    } else {
                         //list_pro();
                         if (typeof callback === "function") {
                         	callback();
                         }
                    }
                });
            }
            
            function check_it_all(ele){
            	var chk = $(ele).prop('checked');
            	var eles = $('input[name=hid_id]');
        		for(var i=0;i<eles.length;i++){
        			$(eles[i]).prop('checked',chk);
        		}
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
                        <input type='text' id='filter' />
                        <input type='button' value='x' onclick='fill_tag("")' />
                        <select id='sort1' name='sort1' onchange="changeSort()">
                            <option value='1'>按序号顺序排序</option>
                            <option value='2'>按序号倒叙排序</option>
                            <option value='3'>按名称,规格,品牌排序</option>
                            <option value='4' selected>按名称,品牌,规格排序</option>
                        </select>
                        <input type="checkbox" id="btn_show_low" value="1" onclick="list_pro()" />库存预警
                        <input type="checkbox" id="btn_show_recent" value="1" onclick="list_pro()" />当天数据
                        <input type="button" value="删除所选" onclick="del_selected()" />
                        <input type="button" value="加入库存分组" onclick="add_to_group_selected()" />
                        

                        <div style="position:inline; float: right;">
                            <ul class="nav">  
                                <li>
                                    <a href='stock_group.php'>库存分组</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='add_init()'>添加库存</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='list_pro()'>库存列表</a>
                                </li>
                            </ul>
                        </div>

                    </div>



                </div>
            </div>
            <div class='container'>
                <div id='list_div'>	    

                    <div id='content_div' style='margin-left:50px;margin-right:50px;'>

                    </div>

                </div>

                <div id='add_div' style='padding-left:0px;padding-top:5px;margin-left:50px;margin-right:50px;display:none;' >
                    <input type='hidden' id='mode1' />
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">添加/编辑库存</div>
                        </div>
                        <div class="block-content collapse in">
                            <form class="form-horizontal">


                                <div class="control-group">
                                    <label class="control-label">商品名称</label>
                                    <div class="controls">
                                        <input type='text' id='pro' />
                                        <input type="button" value="选择" id="sel_btn" onclick="sel_pro()" />
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
                                    <label class="control-label" >库存数量</label>
                                    <div class="controls">
                                        <input type='text' id='stock_quantity' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >库存单位</label>
                                    <div class="controls">
                                        <input type='text' id='stock_unit' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >库存单价</label>
                                    <div class="controls">
                                        <input type='text' id='stock_price' />
                                    </div>
                                </div>
                                
                                   <div class="control-group">
                                    <label class="control-label" >预警数量</label>
                                    <div class="controls">
                                        <input type='text' id='low_quantity' />
                                    </div>
                                </div>
                                	        <div class="control-group">
                                    <label class="control-label" >备注</label>
                                    <div class="controls">
                                        <input type='text' id='remark' />
                                    </div>
                                </div>
                                	   
                                
                               

                                <div class="form-actions">
                                    <input type='button' value='保存' onclick='save_stock1()' class="btn btn-primary"/>
                                    <input type='button' value='删除' onclick='del_stock1()' class="btn btn-primary"/>
                                    <input type='button' value='返回' onclick='ret()' class="btn btn-primary"/>

                                </div>

                                <input type='hidden' id='hid_pro_id' />

                            </form>
                        </div>
                    </div>
                </div>


                <div id='info' style='margin-left:50px;margin-right:50px;'>

                </div>
            </div>


            <div id="oLayer" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
                 width: 800px; display:none;">
                <div id="oLayer_filter" style="margin-left:30px ">
                    <input type='text' id='filter2' onkeyup="filter_search()" />
                </div>
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


