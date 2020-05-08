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

        <title>库存分组</title>
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
                         
            });
            function save_stock_qty(group_id,pro_id, ele) {
                var value = $(ele).parent().parent().find('input[name=stock_qty]').eq(0).val();
                var unit = $(ele).parent().parent().find('input[name=stock_dw]').eq(0).val();
                console.log(value);
                console.log(unit);
                console.log(pro_id);
                 if (value != '') {
                     $.get('product_stock_group_action.php',
                             {r: Math.random(), action: 'pandian', id: pro_id, qty: value,unit:unit,page_name:'stock_group.php'},
                             function (data) {
                                 if (data == 'success') {
                                    show_stock_group_f(group_id);
                                 }
                             });
                 }

               
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
                         
                $.get('product_stock_group_action.php?action=list_stock_group',
                        {r: Math.random()},
                        function (data) {
                            $('#content_div').html(data);
                            $('#tag_div').html('');
                        });
            }

            function clearId() {
                list_pro();


            }

            function add_init() {
                $('#mode1').val('add_stock_group');
                $('#hid_product_group').val('');
                $('#product_group_name').val('');   
                $('#product_html').html('');                             
                $('#add_div').show();
                $('#list_div').hide();                

            }

           
            function list_detail(product_group_id){
                $.get('product_stock_group_action.php',
                    {r:Math.random(),action:'get_stock_group_detail',id:product_group_id},
                    function(html){
                        $('#product_html').html(html);
                    }
                )
            }

           
            function edit_stock_group(product_group_id,ele) {
                add_init();
                $.getJSON('product_stock_group_action.php',
                        {r: Math.random(), action: 'get_stock_group', id: product_group_id},
                        function (data) {
                            if(data.result=='success'){
                                $('#hid_product_group').val(product_group_id);
                                $('#stock_group_name').val(data.group_name);

                                list_detail(product_group_id);

                                
                            }                                                                                                      
                        });
            }

          

            function hideme() {
                $('#oLayer').hide();
            }

            function save_stock_group(){
                var product_group_id = $('#hid_product_group').val();
                if(product_group_id==''){
                    return;
                }
                var stock_group_name = $('#stock_group_name').val();                
                
                $.post('product_stock_group_action.php?action=save_stock_group',
                {
                    stock_group_id:product_group_id,
                    stock_group_name:stock_group_name,
                    r:Math.random()
                },function(data){
                    if(data=='success'){                        
                        list_pro();
                    } else {
                        alert(data);
                    }
                });
            }
            
            function del1(id){
            
                if(!confirm('确定要删除吗?')){
                    return;
                }
                $.get('product_stock_group_action.php?action=del_group',
                {r:Math.random(),id:id},function(data){
                    if(data=='success'){
                        var product_group_id = $('#hid_product_group').val();
                        $.get('product_stock_group_action.php',
                            {r:Math.random(),action:'get_stock_group_detail',id:product_group_id},
                            function(html){
                                $('#product_html').html(html);
                            }
                        )
                    }
                });                
               
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
            


        
            
            function add_new_line(){
                
                var html = '<tr><td><input type="text" name="new_product" value="" style="width:80px;" /></td>'+''
                +'<td></td><td></td><td></td><td>  <a href="javascript:void(0)" onclick="add_ok(this)">确定</a> '
                +' <a href="javascript:void(0)" onclick="add_cancel(this)">取消</a> </td>'+'</tr>';
                console.log(html);
                $('#list_tb2').find('tr:last').after(html);
                
            }

            function add_ok(ele){
                var val = $(ele).parent().parent().find('td').find('input').val();
                var group_id = $('#hid_product_group').val();
                $.post('product_stock_group_action.php?action=add_product_to_group',
                    {r:Math.random(),stock_group_id:group_id,product_id:val},function(data){
                        if(data=='success'){
                            list_detail(group_id);
                        }
                    }
                    );
            }
            function add_cancel(ele){
                var group_id = $('#hid_product_group').val();
                list_detail(group_id);
            }

            function move_up1(id,ele){
                var group_id = $('#hid_product_group').val();
                $.get('product_stock_group_action.php?action=move_up',
                {r:Math.random(),group_id:group_id,id:id},function(data){
                    console.log(data);
                    if(data=='success'){
                        list_detail(group_id);
                    }
                });
            }
            function move_down1(id,ele){
                var group_id = $('#hid_product_group').val();
                $.get('product_stock_group_action.php?action=move_down',
                {r:Math.random(),group_id:group_id,id:id},function(data){
                    console.log(data);
                    if(data=='success'){
                        list_detail(group_id);
                    }
                });
            }
            function move_head1(id,ele){
                var group_id = $('#hid_product_group').val();
                $.get('product_stock_group_action.php?action=move_head',
                {r:Math.random(),group_id:group_id,id:id},function(data){
                    console.log(data);
                    if(data=='success'){
                        list_detail(group_id);
                    }
                });
            }

            function show_stock_group_f(id){
                $.get('product_stock_group_action.php?action=show_stock_group_detail',
                {r:Math.random(),id:id},function(data){                                        
                    $('#content_div').html(data);                    
                });
            }

            function show_stock_group(id,ele){
                show_stock_group_f(id);
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
                     

                        <div style="position:inline; float: right;">
                            <ul class="nav">  
                                <li>
                                    <a href='javascript:void(0)' onclick='add_init()'>添加库存分组</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='list_pro()'>库存分组列表</a>
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
                            <div class="muted pull-left">添加/编辑库存分组</div>
                        </div>
                        <div class="block-content collapse in">
                            <form class="form-horizontal">


                                <div class="control-group">
                                    <label class="control-label">库存分组名称</label>
                                    <div class="controls">
                                        <input type='text' id='stock_group_name' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">商品</label>
                                    <div class="controls" id='product_html'>

                                    </div>
                                </div>



                                <div class="form-actions">
                                    <input type='button' value='添加一行' onclick='add_new_line()' class="btn btn-primary"/>
                                    <input type='button' value='保存' onclick='save_stock_group()' class="btn btn-primary"/>
                                    <input type='button' value='删除' onclick='del_stock_group()' class="btn btn-primary"/>
                                    <input type='button' value='返回' onclick='ret()' class="btn btn-primary"/>

                                </div>

                                <input type='hidden' id='hid_product_group' />

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


