<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"] = "company.php";
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

        <title>单位列表</title>
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
                list_company();
                $('#filter').keyup(function () {
                    list_company();
                });
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


            function add_init() {
                $('#mode1').val('add_company');
                $('#client_no').val('');
                $('#client_company').val('');
                $('#client_addr').val('');
                $('#tax_no').val('');
                $('#bank_name').val('');
                $('#client_phone').val('');
                $('#remark').val('');
                $('#add_div').show();
                $('#list_div').hide();
            }

            function edit1(client_no) {
                $.getJSON('client_action.php?action=get_company',
                        {client_no: client_no, r: Math.random(),page_name:'company.php'},
                        function (data) {
                            $('#mode1').val('edit_company');
                            $('#client_no').val(data.client_no);
                            $('#client_company').val(data.client_company);
                            $('#client_addr').val(data.client_addr);
                            $('#tax_no').val(data.tax_no);
                            $('#bank_name').val(data.bank_name);
                            $('#client_phone').val(data.client_phone);
                            $('#remark').val(data.remark);

                            $('#add_div').show();
                            $('#list_div').hide();

                        });
            }
            function del1(client_no) {
                if (!confirm('确定要删除吗？')) {
                    return;
                }
                $.post('client_action.php?action=del_company',
                        {client_no: client_no, r: Math.random(),page_name:'company.php'},
                        function (data) {
                            if (data == 'success') {
                                list_company();
                                $('#info').html('');
                            } else {
                                $('#info').show();
                                alert(data);
                            }
                        });

            }
            function save_company(num) {
                var mode1 = $('#mode1').val();

                $.post('client_action.php?action=' + mode1,
                        {client_no: $('#client_no').val(),
                            client_company: $('#client_company').val(),
                            client_addr: $('#client_addr').val(),
                            tax_no: $('#tax_no').val(),
                            bank_name: $('#bank_name').val(),
                            client_phone: $('#client_phone').val(),
                            remark: $('#remark').val(),
                            page_name:'company.php',
                            r: Math.random()},
                        function (data) {
                            if (data != 'success') {
                                $('#info').html(data);
                            } else {
                                $('#info').html('');
                            }
                            if (num == 1) {
                                add_init();
                            } else if (num == 2) {
                                list_company();
                            }
                        });
            }
            function ret() {
                list_company();
            }
            function list_company() {
                $('#add_div').hide();
                $('#list_div').show();
                var filter1 = $('#filter').val();
                $.get('client_action.php?action=list_company',
                        {r: Math.random(), filter1: filter1,page_name:'company.php'},
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


                    <div id='span_tag'></div>

                    <div style='padding-left:0px;padding-top:0px;margin-left:50px;margin-right:50px;' id='list_search_div'>
                        <input type='text' id='filter' />	    	
                        <div style="position:inline; float: right;">
                            <ul class="nav">  
                                <li>
                                    <a href='javascript:void(0)' onclick='add_init()'>添加单位</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='list_company()'>单位列表</a>
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
                            <div class="muted pull-left">添加单位</div>
                        </div>
                        <div class="block-content collapse in">
                            <form class="form-horizontal">


                                <div class="control-group">
                                    <label class="control-label">单位名称</label>
                                    <div class="controls">
                                        <input type='hidden' id='client_no' value='' />
                                        <input type='text' id='client_company' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >地址</label>
                                    <div class="controls">
                                        <input type='text' id='client_addr' /> 
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >税号</label>
                                    <div class="controls">
                                        <input type='text' id='tax_no' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >开户银行</label>
                                    <div class="controls">
                                        <input type='text' id='bank_name' />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" >联系电话</label>
                                    <div class="controls">
                                        <input type='text' id='client_phone' />
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" >备注</label>
                                    <div class="controls">
                                        <input type='text' id='remark' />
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <input type='button' value='保存并继续添加' onclick='save_company(1)' class="btn btn-primary"/>
                                    <input type='button' value='保存并跳转至列表' onclick='save_company(2)' class="btn btn-primary"/>
                                    <input type='button' value='返回' onclick='ret()' class="btn btn-primary"/>

                                </div>


                            </form>
                        </div>
                    </div>
                </div>
                <div id='list_div'>	    

                    <div id='content_div' style='margin-left:50px;margin-right:50px;'>

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


