<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"]="big_product_stock.php";
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

        <title>大件商品库存列表</title>
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
                list_big_product_stock();
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
                var time = setInterval(list_big_product_stock, 1000);/*每1秒执行一次人员筛选，time是停止本方法的参数*/
                $(this).bind('blur',function(){
                    clearInterval(time); /*停止setInterval*/
                });
            };            

            function fill_tag(tag) {
                $('#filter').val(tag);
                $('#editor_product_id').val('');
                list_big_product_stock();
            }

            function list_tags() {
                $.get('product_action.php?action=list_tags',
                        {r: Math.random(),page_name:'product.php'},
                        function (data) {                            
                            $('#span_tag').html(data);  			
                        });
            }    

            function add_init(){
                $('#hid_id').val(0);
                $('#mode1').val('add_big_pro_stock');
                $('#pro').html(''); 
                $('#model').html('');
                $('#made').html('');

                $('#stock_state').val('');
                $('#stock_save_location').val('');

                $('#quantity').val('');
                $('#unit').val('');

                $('#add_div').show();
                $('#list_div').hide();
            }

            function edit1(id) {
                $.getJSON('big_product_stock_action.php?action=get_by_id',
                        {id: id, r: Math.random(),page_name:'big_product_stock.php'},
                        function (data) {
                            
                            $('#hid_id').val(id);
                            $('#mode1').val('edit_big_pro_stock');
                            $('#hid_product_id').val(data.product_id);
                            //console.log('hid_product_id:'+$('#hid_product_id').val());
                            $('#pro').html(data.product_name);                            
                            $('#model').html(data.product_model);
                            $('#made').html(data.product_made);

                            $('#stock_state').val(data.product_state);
                            $('#stock_save_location').val(data.stock_position);

                            $('#quantity').val(data.quantity);
                            $('#unit').val(data.unit);
  
                            $('#add_div').show();
                            $('#list_div').hide();

                            $.getJSON('instock_action.php',{action:'get_instock',batch_id:data.instock_batch_id,r:Math.random(),page_name:'big_product_stock.php'},
                                function(data){							
                                                    
                                    $('#company_name').html(data.in_company);
                                    $('#in_add_date').html(data.add_date);	
                                
                                });

                        });
                   
            }
            
            function del1(id) {
                if (!confirm('确定要删除吗？')) {
                    return;
                }
                $.post('big_product_stock_action.php?action=del_pro',
                        {id: id, r: Math.random(),page_name:'big_product_stock.php'},
                        function (data) {
                            if (data == 'success') {
                                list_big_product_stock();
                                $('#info').html('');
                            } else {
                                $('#info').html(data);
                            }
                        });

            }
            function save_pro(num) {

                var mode1 = $('#mode1').val();
                
                $.post('big_product_stock_action.php?action=' + mode1,
                        {id: $('#hid_id').val(),
                        product_id:$('#hid_product_id').val(),
                            stock_state:$('#stock_state').val(),
                            stock_position: $('#stock_save_location').val(),
                            unit1: $('#unit').val(),
                            quantity1:$('#quantity').val(),
                            r: Math.random(),
                            page_name:'big_product_stock.php'},
                        function (data) {
                            if (data != 'success') {
                                $('#info').html(data);
                            } else {
                                $('#info').html('');
                                list_big_product_stock();
                            }                          
                            
                        });

                        
            }
            function ret() {
                list_big_product_stock();
            }
            
            function changeFilter(){
                list_big_product_stock();
            }
            
            
            function list_big_product_stock() {
                $('#add_div').hide();
                $('#list_div').show();       
                var page_id =  $('#hid_page_id').val();     
                var filter1 = $('#filter').val();       
                var p_state = $('#sel_stock_state').val();        
                $.get('big_product_stock_action.php?action=list_big_pro_stock',
                        {r: Math.random(), page_name:'big_product_stock.php', filter1: filter1,
                            stock_state:p_state,page_id:page_id,page_size:50},
                        function (data) {
                            $('#content_div').html(data);
                            $('#tag_div').html('');
                            $('#info').html('');
                        });
            }

           

            function go_page(page_id){
                $('#hid_page_id').val(page_id);
                list_big_product_stock();
            }
            
            
            function hideme() {
                $('#oLayer').hide();
            }

            
            
            function del_this_row(ele){
                $(ele).parent().parent().remove();
            }

            function choose_product(){
                $.get('big_product_stock_action.php?action=list_pro',{
                    r:Math.random(),filter:$('#filter2').val(),page_name:'big_product_stock.php'},function(data){
                        $('#oLayer_content').html(data);
                    }          
                );
                $('#oLayer').show();
            }

            function select_it(product_id){
               // console.log(product_id+' selected');
                $.get('product_action.php?action=get_pro',
                {pro_id:product_id,r:Math.random(),page_name:'big_product_stock.php'},function(data){
                 //   console.log(data);
                    $('#hid_id').val('');
                 //   console.log('hidId:'+$('#hid_id').val());
                    $('#hid_product_id').val(product_id);
                    $('#mode1').val('add_big_pro_stock');
                    $('#pro').html(data.product_name);                            
                    $('#model').html(data.product_model);
                    $('#made').html(data.product_made);
                    $('#oLayer').hide();
                },'JSON');
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
                       产品名称/规格/品牌(产地): <input type='text' id='filter' />
                        <input type='button' value='x' onclick='fill_tag("")' />     
                        状态：
                            <select id='sel_stock_state' style='width:80px' onchange='list_big_product_stock()'>
                                    <option value='0'>不限</option>
                                    <option value='1'>在库</option>
                                    <option value='2'>部分售出</option>
                                    <option value='3'>售出</option>
                            </select>
                            <input type='button' value='搜索' onclick='list_big_product_stock()' />
                                 
                        
                        <div style="position:inline; float: right;">
                            <ul class="nav">  
                                <li>
                                    <a href='javascript:void(0)' onclick='add_init()'>添加</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='list_big_product_stock()'>列表</a>
                                </li>
                            </ul>
                        </div>

                     

                    </div>

                </div>
            </div>
            <div class='container'>
                <div id='add_div' style='padding-left:0px;padding-top:5px;margin-left:50px;margin-right:50px;'>
                    <input type='hidden' id='mode1' />
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">编辑大件商品库存</div>
                        </div>
                        <div class="block-content collapse in">
                            <form class="form-horizontal">


                                <div class="control-group">
                                    <label class="control-label">商品名称</label>
                                    <div class="controls">
                                        <span id='pro'></span>
                                        <input type='button' value='选择'  onclick='choose_product()' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >规格</label>
                                    <div class="controls">
                                        <span id='model'></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >品牌/产地</label>
                                    <div class="controls">
                                        <span id='made'></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >供应商</label>
                                    <div class="controls">
                                        <span id='company_name'></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >入库日期</label>
                                    <div class="controls">
                                        <span id='in_add_date'></span>
                                    </div>
                                </div>


                                <div class="control-group">
                                    <label class="control-label" >状态</label>
                                    <div class="controls">
                                        <select id='stock_state' style='width:80px'>
                                        <option value='1'>在库</option>
                                        <option value='2'>部分售出</option>
                                        <option value='3'>售出</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="control-group">
                                    <label class="control-label" >位置</label>
                                    <div class="controls">
                                        <select id='stock_save_location' style='width:80px'>
                                            <option value='1'>店内</option>
                                            <option value='2'>包家仓库</option>
                                            <option value='3'>舜北仓库</option>
                                        </select>
                                    </div>
                                </div>
                                
                           
                                <div class="control-group">
                                    <label class="control-label" >  <input type='text' id='quantity' style='width:50px; text-align:right;' /></label>
                                    <div class="controls">
                                       <input type='text' id='unit' />
                                    </div>
                                </div>
                              
                                <div class="form-actions">
                                    <input type='button' value='保存' onclick='save_pro(2)' class="btn btn-primary"/>
                                    <input type='button' value='返回' onclick='ret()' class="btn btn-primary"/>

                                </div>

                                <input type='hidden' id='hid_id' />
                                <input type='hidden' id='hid_product_id' />

                            </form>
                        </div>
                    </div>
                </div>
                <div id='list_div'>	    

                    <div id='content_div' style='margin-left:50px;margin-right:50px;'>

                    </div>
                
                
                   	

                </div>


                <div id='info' style='margin-left:50px;margin-right:50px;'>

                </div>


                <div id="oLayer" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
                     width: 800px; display:none;">
                     <div id='oLayer_title'>
                        <input id='filter2' type='text' value='' onchange='choose_product()' />
                        <input type='button' value='确定' onclick='choose_product()'  />
                     </div>
                <div id='oLayer_content'>


                </div>

                <div style='float:right;'>
                    <input type='button' value='x' onclick='hideme()' />
                </div>
            </div>

                <input type='hidden' id='hid_page_id' value='1' />
                
              
                
            </div>
<?php
require_once ( 'change_password.php');
?>
        </div>
    </body>
</html>


