<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"] = "tj1.php";
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
        <script src="common.js"></script>

        <title>统计</title>
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
            .content1 {margin-left:50px;margin-right:50px;}
        </style>	
        <script type='text/javascript'>

            $(document).ready(function () {

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
            
            function list_tj(){
                var start_time = $('#start_time').val();
                var end_time = $('#end_time').val();     
                var tj_type = $('#tj_type').val();
                $.get('tj1_action.php?action=list_tj1',{
                    tj_type:tj_type,start_time:start_time,end_time:end_time,r:Math.random(),page_name:'tj1.php'
                },function(data){
                    $('#content1').html(data);
                });
                
            }
            
            function changeTjType(){
                
                
                
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
            
               <div style='padding-left:0px;padding-top:0px;margin-left:50px;margin-right:50px;' id='list_search_div'>
                        <select id='tj_type' name='tj_type' onchange="changeTjType()" style="width:120px;">
                            <option value='day' selected="selected">按日</option>
                            <option value='month'>按月</option>
                            <option value='year'>按年</option>   
                            <option value='tag'>按标签</option>   
                   			<option value='detail_and_profit'>明细及利润</option>   
                        </select>
                     开始时间：<input id='start_time' name='start_time' style='width:100px;' class='Wdate' onClick='WdatePicker()'  />
		结束时间：<input id='end_time' name='end_time' style='width:100px;' class='Wdate' onClick='WdatePicker()'  />
		<input type='button' value='统计' onclick='list_tj()' />
               </div>        
                        
            
            <div class='container'>
                <div id="content1" class="content1">
                    
                </div>
                    <?php
                    
                    
                    
                    ?>

            </div>
<?php
require_once ( 'change_password.php');
?>
        </div>

    </body>
</html>
