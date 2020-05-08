<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"] = "stock_detail.php";
    $url = "login.php";
    echo "<script language='javascript' type='text/javascript'>";
    echo "window.location.href='$url'";
    echo "</script>";
    return;
}
$product_id = $_GET["product_id"];
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

        <title>库存明细列表</title>
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

            var product_id = '<?php echo $product_id; ?>';

            $(document).ready(function () {
                list_detail();                
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
                var time = setInterval(list_detail, 1000);/*每1秒执行一次人员筛选，time是停止本方法的参数*/
                $(this).bind('blur',function(){
                    clearInterval(time); /*停止setInterval*/
                });
            };

            var g_page_id = 1;

           function go_page(page_id){
               g_page_id = page_id;
               list_detail();
           }                     
            
            function list_detail() {
                $('#list_div').show();                
                var start_time = $('#start_time').val();
                var end_time = $('#end_time').val();                                
                var filter1 = $('#filter').val();                                                
                $.get('stock_detail_action.php?action=list_detail&product_id='+product_id,
                        {r: Math.random(),page_id:g_page_id, start_time:start_time,end_time:end_time, filter1: filter1,page_name:'stock_detail.php'},
                        function (data) {
                            $('#content_div').html(data);
                        });
            }

           
          
            
        </script>
    </head>
    <body>
        <div id='wrapper' style="position:absolute;left:0px;top:0px;">
            <div class="navbar" id='top_div'>
                <div class="navbar-inner">
                    <?php include("top_div1.php") ?>

                    <div style='padding-left:0px;padding-top:0px;margin-left:50px;margin-right:50px;' id='list_search_div'>
                        <input type='text' id='filter' />
                          开始时间：<input id='start_time' name='start_time' style='width:100px;' class='Wdate' onClick='WdatePicker()'  />
                            结束时间：<input id='end_time' name='end_time' style='width:100px;' class='Wdate' onClick='WdatePicker()'  />                      
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
    </body>
</html>


