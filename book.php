<!DOCTYPE html>
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
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
<script src="tpl/vendors/modernizr-2.6.2-respond-1.1.0.min.js"></script>
<script type="text/javascript" src="tpl/vendors/jquery-1.9.1.min.js"></script>
<script src="tpl/bootstrap/js/bootstrap.min.js"></script>
<script src="common.js"></script>

<title>book</title>
<style type="text/css">
#wrapper {
  width: 100%;height:100%;
}
#list_tb1 {  width: 100%;}
	 td {padding:5px;}
	 .tb1 {border:2px solid;border-spacing:0px; }
	 .tb1 th {margin:3px;padding:5px;border:1px gray solid;}
	 .tb1 td { border:1px gray solid; margin:2px;padding:3px; border-collapse : collapse;}
	 #span_tag { margin-left:50px;margin-right:50px;}
</style>
<script type='text/javascript'>
	
	var page_id = 1;

function save_book(){	
	var book_id = $('#hid_book_id').val();
	var book_name = $('#book_name').val();
	var author = $('#author').val();
	var page_num = $('#page_num').val();
	var publisher = $('#publisher').val();
	var add_date = $('#add_date').val();
	var url = 'book_action.php?action=add_book';
	if(book_id==''){
		
	} else {
		url = 'book_action.php?action=edit_book';
	}
	
	$.post(url,
		{r:Math.random(),book_id:book_id,book_name:book_name,page_num:page_num,
		author:author,add_date:add_date,publisher:publisher},
		function(data){
			if(data=='success'){
				go_page(page_id);
				$('#add_div').addClass('hide');
				$('#list_div').removeClass('hide');
				active_id('id1');
			} else {
				alert(data);
			}
		});
}


function ret(){
	go_page(page_id);
	$('#add_div').addClass('hide');
	$('#list_div').removeClass('hide');
	active_id('id1');
}

function active_id(id){
	$('#id1').removeClass('active');
	$('#id2').removeClass('active');
	$('#'+id).addClass('active');
}

function list_init(){
	active_id('id1');
	$('#add_div').addClass('hide');
	$('#list_div').removeClass('hide');
	var filter = $('#filter_book_name').val();
        var from = $('#filter_add_from').val();
        var to = $('#filter_add_to').val();
	var page_size = 10;
	$.get('book_action.php?action=list_book',
	{page_id:1,page_size:page_size,filter1:filter,from:from,to:to,r:Math.random()},
		function(data){
			$('#content_div').html(data);
		});
}

function go_page(page_id1){
	var filter = $('#filter_book_name').val();
        var from = $('#filter_add_from').val();
        var to = $('#filter_add_to').val();
	var page_size = 10;
	page_num = page_id1;
	$.get('book_action.php?action=list_book',
		{page_id:page_num,page_size:page_size,filter1:filter,from:from,to:to,r:Math.random()},
		function(data){
			$('#content_div').html(data);
	});
}

function del1(book_id){
	if(!confirm('确定要删除吗？')){
		return;
	}
	$.post('book_action.php?action=del_book',
	{book_id:book_id,r:Math.random()},
		function(data){
			if(data=='success'){
				go_page(page_id);
				$('#add_div').addClass('hide');
				$('#list_div').removeClass('hide');
				active_id('id1');
			}
		});
}

function edit1(book_id){
	active_id('id2');
	$.get('book_action.php?action=get_book',
	{book_id:book_id,r:Math.random()},
		function(data){
		$('#hid_book_id').val(data.BOOK_ID);
		$('#book_name').val(data.BOOK_NAME);
		$('#author').val(data.AUTHOR);
		$('#page_num').val(data.PAGE_NUM);
		$('#publisher').val(data.PUBLISHER);
		$('#add_date').val(data.ADD_DATE);
		},'JSON');
		$('#add_div').removeClass('hide');
		$('#list_div').addClass('hide');
}

function copy1(book_id){
	active_id('id2');
	$.get('book_action.php?action=get_book',
	{book_id:book_id,r:Math.random()},
		function(data){
		$('#hid_book_id').val('');
		$('#book_name').val(data.BOOK_NAME);
		$('#author').val(data.AUTHOR);
		$('#page_num').val(data.PAGE_NUM);
		$('#publisher').val(data.PUBLISHER);
		$('#add_date').val(data.ADD_DATE);
		},'JSON');
		$('#add_div').removeClass('hide');
		$('#list_div').addClass('hide');
}

function add_init(){
	active_id('id2');
	$('#add_div').removeClass('hide');
	$('#list_div').addClass('hide');
	$('#hid_book_id').val('');
	$('#book_name').val('');
	$('#author').val('');
	$('#page_num').val('');
	$('#publisher').val('');
	$('#add_date').val('<?php  echo date("Y-m-d");  ?>');
}

function export1(){
    
        var filter = $('#filter_book_name').val();
        var from = $('#filter_add_from').val();
        var to = $('#filter_add_to').val();
        
        var form = $("<form>");
        form.attr('style', 'display:none');
        form.attr('target', '_blank');
        form.attr('method', 'get');
        form.attr('action', 'book_action.php');

        var input1 = $('<input>');
        input1.attr('type', 'hidden');
        input1.attr('name', 'action');
        input1.attr('value', 'export_csv');
        
        var input2 = $('<input>');
        input2.attr('type', 'hidden');
        input2.attr('name', 'filter1');
        input2.attr('value', filter);
        
        var input3 = $('<input>');
        input3.attr('type', 'hidden');
        input3.attr('name', 'from');
        input3.attr('value', from);
        
        var input4 = $('<input>');
        input4.attr('type', 'hidden');
        input4.attr('name', 'to');
        input4.attr('value', to);
        

        $('body').append(form);
        form.append(input1);
        form.append(input2);
        form.append(input3);
        form.append(input4);
        
        form.submit();
        form.remove();   
}

$(document).ready(function(data){
	list_init();
});

</script>
</head>
<body>
	<div id='wrapper'>
	
	<div class="navbar navbar-fixed-top" id='top_div'>
            <div class="navbar-inner">
                <div class="container-fluid" id="top_div1">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href='javascript:void(0)' onclick='list_init()'>Admin Panel</a>
                 <ul class="nav">
                            <li class="active" id='id1'>
        				 <a href='javascript:void(0)' onclick='list_init()'>书籍列表</a>	
                            </li>
        			<li id='id2'>
        			 <a href='javascript:void(0)' onclick='add_init()'>添加书籍</a>
        			</li>
                        </ul>
                    </div><?php #top_div1 ?>
          </div><?php #navbar-inner ?>
        </div><?php #top_div ?>
        
	<div id='add_div' style='padding-left:0px;padding-top:5px;margin-left:50px;margin-right:50px;'>
		<input type='hidden' id='mode1' />
			  <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">添加书籍</div>
                            </div>
                            <div class="block-content collapse in">
			  <form class="form-horizontal">
			
			
			   <div class="control-group">
                                          <label class="control-label">书籍名称</label>
                                          <div class="controls">
                                          <input type='text' id='book_name' />
                                          </div>
                                        </div>
                   
                     <div class="control-group">
                                          <label class="control-label" >作者</label>
                                          <div class="controls">
                                          <input type='text' id='author' /> 
                                          </div>
                                        </div>
                   
                     <div class="control-group">
                                          <label class="control-label" >页数</label>
                                          <div class="controls">
                                         <input type='text' id='page_num' />
                                          </div>
                                        </div>
			
			 <div class="control-group">
                                          <label class="control-label" >出版社</label>
                                          <div class="controls">
                                         <input type='text' id='publisher' />
                                          </div>
                                        </div>
                 
                  <div class="control-group">
                                          <label class="control-label" >添加日期</label>
                                          <div class="controls">
                                        <input type='text' id='add_date' class='Wdate' onClick='WdatePicker()' />
                                          </div>
                                        </div>
                 
                         <div class="form-actions">
							<input type='button' value='保存' onclick='save_book()' class="btn btn-primary"/>
							<input type='button' value='返回' onclick='ret()' class="btn btn-primary"/>
                                        
                                        </div>

		<input type='hidden' id='hid_book_id' />
			
			</form>
				</div><?php #block-content ?>
			</div><?php #block ?>
			</div><?php #add_div ?>
			

		<div id='list_div'>
				  <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">书籍列表</div>
                            </div>
                            <div class="block-content collapse in">
                                <div id='search_content_div'>
                                     	名称：<input type='text' id='filter_book_name' style="width:120px;" />
                                        <script type="text/javascript">
                                            if(IsPC()){
                                                
                                            } else {
                                                document.write("<br />");
                                            }
                                            </script>
                                        日期：<input type='text' id='filter_add_from' class='Wdate' onClick='WdatePicker()' style="width:100px;" />到
                                        <input type='text' id='filter_add_to' class='Wdate' onClick='WdatePicker()' style="width:100px;"  />
                                        <input type='button' value="搜索" onclick="go_page(1)" />
                                        <input type='button' value="导出" onclick="export1()" />
                                </div>
				<div id='content_div' class="table-responsive">
	    				
	    			</div><?php #content_div ?>
	    				</div><?php #block-content ?>
	    				</div><?php #block ?>
			</div><?php #list_div ?>
		


 </div>  <?php #wrapper ?>    
</body>
</html>