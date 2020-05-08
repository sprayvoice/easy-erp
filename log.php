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
        <script src="common.js"></script>
        <script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>

        <title>日志列表</title>
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
        </style>	
        <script type='text/javascript'>



            $(document).ready(function () {
                list_log();
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
                var time = setInterval(list_log, 1000);/*每1秒执行一次人员筛选，time是停止本方法的参数*/
                $(this).bind('blur',function(){
                    clearInterval(time); /*停止setInterval*/
                });
            };
            
            function del_batch(){
                  if (!confirm('确定要删除吗？')) {
                    return;
                }
                var page_name = $('#page_name').val();
                var action_name = $('#action_name').val();
                var sql_type = $('#sql_type').val();
                var execute_result = $('#execute_result').val();
                var log_batch_id = $('#log_batch_id').val();
                var filter1 = $('#filter').val();
                var start_day = $('#start_time').val();
                var end_day = $('#end_time').val();
                
                $.get('log_action.php?action=del_batch',
                        {   r: Math.random(), 
                            page_name:page_name,
                            action_name:action_name,
                            sql_type:sql_type,
                            execute_result:execute_result,
                            log_batch_id:log_batch_id,
                            filter1: filter1,
                            start_day:start_day,
                            end_day:end_day
                        },
                        function (data) {
                            if (data == 'success') {
                                list_log();
                                $('#info').html('');
                            } else {
                                $('#info').html(data);
                            }
                        });
            }
            
            function del1(log_id) {
                if (!confirm('确定要删除吗？')) {
                    return;
                }
                $.post('log_action.php?action=del_log',
                        {log_id: log_id, r: Math.random(),page_name:'log.php'},
                        function (data) {
                            if (data == 'success') {
                                list_log();
                                $('#info').html('');
                            } else {
                                $('#info').html(data);
                            }
                        });

            }
           
            function ret() {
                list_log();
            }
            
            function changeFilter(){
                list_log();
            }
            
            var page_id = 1;
            
            function go_page(p_id) {
                page_id = p_id;
                list_log();
            }
            
            function list_log1(){
                page_id = 1;
                list_log();
            }
            
            
            function list_log() {      
                var page_name = $('#page_name').val();
                var action_name = $('#action_name').val();
                var sql_type = $('#sql_type').val();
                var execute_result = $('#execute_result').val();
                var log_batch_id = $('#log_batch_id').val();
                var filter1 = $('#filter').val();
                var start_day = $('#start_time').val();
                var end_day = $('#end_time').val();
                $.get('log_action.php?action=list_log',
                        {   r: Math.random(), 
                            page_id:page_id,
                            page_name:page_name,
                            action_name:action_name,
                            sql_type:sql_type,
                            execute_result:execute_result,
                            log_batch_id:log_batch_id,
                            filter1: filter1,
                            start_day:start_day,
                            end_day:end_day
                        },
                        function (data) {
                            $('#content_div').html(data);

                        });
            }
            
            function filter_by_batch_id(batch_id){
                $('#log_batch_id').val(batch_id);
                list_log1();
            }
            function filter_by_page_name(page_name){
                $('#page_name').val(page_name);
                list_log1();
            }
            function filter_by_action_name(action_name){
                $('#action_name').val(action_name);
                 list_log1();
            }

        </script>
    </head>
    <body>
        <div id='wrapper' style="position:absolute;left:0px;top:0px;">
            <div class="navbar" id='top_div'>
                <div class="navbar-inner">
                  <?php include("top_div1.php") ?>


                  
                    <div style='padding-left:0px;padding-top:0px;margin-left:50px;margin-right:50px;' id='list_search_div'>
                        page_name:<input type="text" id="page_name" name="page_name" style="width:80px;" />
                        action_name:<input type="text" id="action_name" name="action_name" style="width:80px;" />
                        sql_type:<input type="text" id="sql_type" name="sql_type" style="width:80px;" />
                        execute_result:<input type="text" id="execute_result" name="execute_result" style="width:80px;" />
                        log_batch_id:<input type="text" id="log_batch_id" name="log_batch_id" style="width:80px;" />
                        sql:<input type='text' id='filter' /> 
                        <br />
                             开始时间：<input id='start_time' name='start_time' style='width:100px;' class='Wdate' onClick='WdatePicker()'  />
                            结束时间：<input id='end_time' name='end_time' style='width:100px;' class='Wdate' onClick='WdatePicker()'  />
                            <input type='button' value='搜索' onclick='list_log()' />
                            <input type="button" value="删除" onclick="del_batch()" />
                        <div id="ext_div" style="display:none;">
                           
                            <br />
                            <input type="button" value="隐藏" onclick="hide_ext()" />
                        </div>

                    </div>

                </div>
            </div>
            <div class='container'>
        
                <div id='list_div'>	    

                    <div id='content_div' style='margin-left:50px;margin-right:50px;'>

                    </div>                 

                </div>


                <div id='info' style='margin-left:50px;margin-right:50px;'>

                </div>
                
              
                
            </div>
<?php
require_once ( 'change_password.php');
?>
        </div>
    </body>
</html>


