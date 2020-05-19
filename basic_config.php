<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"] = "basic_config.php";
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

        <title>基本设置</title>
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
                get_info1();
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


         

            function get_info1() {

                console.log('test1');
                $.get('basic_config_action.php?action=get_info',
                    {r: Math.random()},
                    function (data) {
                        console.log('test2');
                        
                        $('#company_name1').val(data.company_name1);
                        $('#company_name2').val(data.company_name2);
                        $('#company_name3').val(data.company_name3);
                        $('#company_addr').val(data.company_addr);
                        $('#company_phone').val(data.company_phone);                            


                    },'JSON');
                    
            }
          
            function save_info() {
                
                $.post('basic_config_action.php?action=save_info',
                        {   
                            company_name1: $('#company_name1').val(),
                            company_name2: $('#company_name2').val(),
                            company_name3: $('#company_name3').val(),
                            company_addr: $('#company_addr').val(),
                            company_phone: $('#company_phone').val(),
                            r: Math.random()},
                        function (data) {
                            console.log(data);
                            if (data != 'success') {
                                $('#info').html(data);
                            } else {
                                $('#info').html('');
                            }
                          get_info1();
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
                      
                        <div style="position:inline; float: right;">
                            <ul class="nav">  
                             
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class='container'>
                <div id='add_div' style='padding-left:0px;padding-top:5px;margin-left:50px;margin-right:50px;'>
                    
                    <div class="block">
                        <div class="navbar navbar-inner block-header">
                            <div class="muted pull-left">基本信息编辑</div>
                        </div>
                        <div class="block-content collapse in">
                            <form class="form-horizontal">


                                <div class="control-group">
                                    <label class="control-label">销售单单位抬头1</label>
                                    <div class="controls">
                                        <input type='text' id='company_name1' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >销售单单位抬头2</label>
                                    <div class="controls">
                                        <input type='text' id='company_name2' /> 
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >销售单单位抬头3</label>
                                    <div class="controls">
                                        <input type='text' id='company_name3' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >销售单地址</label>
                                    <div class="controls">
                                        <input type='text' id='company_addr' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >销售单联系电话</label>
                                    <div class="controls">
                                        <input type='text' id='company_phone' />
                                    </div>
                                </div>
                            

                                <div class="form-actions">
                                    <input type='button' value='保存' onclick='save_info()' class="btn btn-primary"/>

                                </div>


                            </form>
                        </div>
                    </div>
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


