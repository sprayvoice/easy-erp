<?php
if(isset($_COOKIE["login_true"])==false){
        session_start();
        $_SESSION["last_url"]="art.php";
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
<script src="ajaxfileupload.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8" src="ueditor18/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="ueditor18/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8" src="ueditor18/lang/zh-cn/zh-cn.js"></script>

<title>记事</title>
<style type="text/css">
#wrapper {
  width: 100%;height:100%;
}
#list_tb1 th {text-align:center;}
	 td {padding:5px;}
	 .tb1 {border:2px solid;border-spacing:0px; }
	 .tb1 th {margin:3px;padding:5px;border:1px gray solid;}
	 .tb1 td { border:1px gray solid; margin:2px;padding:3px; border-collapse : collapse;}
	 #span_tag { margin-left:50px;margin-right:50px;}
</style>
<script type='text/javascript'>

$(document).keyup(function(event){
	 switch(event.keyCode) {
	 case 27:
		 hideme();
	 case 96:
		 hideme();
	 }

	});
	
function hideme(){
	$('#oLayer').hide();
}

function show_cat(){
	$.get('art_cat_action.php',
	{action:'list_category_sel',r:Math.random(),page_name:'art.php'},   
		function(data){
			$('#cat_id').html(data);
			$('#sel_cat_id').html(data);
		});
}


function show_art(art_id,ele){
	$.getJSON('art_action.php',
            {action:'get_art',art_id:art_id,r:Math.random(),page_name:'art.php'},                    
			function(data){	  
            	$('#oLayer_content').html(data.art_content);
				var top = $(ele).parent().parent().position().top;
				var left = $(ele).parent().parent().position().left;
				
				$('#oLayer').css({'position':'absolute','top':top+'px','left':left+'px'});
            	$('#oLayer').show();
            	
            });
}

function get_art(art_id){
                clear_info();
                
           		$.getJSON('art_action.php',
                    {action:'get_art',art_id:art_id,r:Math.random(),page_name:'art.php'},                    
					function(data){	                        
                  $('#art_id').val(data.art_id);
                  $('#cat_id').val(data.cat_id);
                  $('#art_title').val(data.art_title);
                  UE.getEditor('art_content').setContent(data.art_content, false);
                  $('#add_date').val(data.add_date);
                  $('#sort_order').val(data.sort_order);
                  $('#summary').val(data.summary);
                  
                });     
                
               
           
		$('#add_div').show();
		$('#list_div').hide();
       
                
		
        
			
	}


	$(document).ready(function(){
		
		list_art();
        show_cat();

		$.each($('.nav li'),function(name,value){
			$(this).click(function(){
				var len = $('.nav li').length;
				for(var i=0;i<len;i++){
					$('.nav li').eq(i).removeClass('active');
				}
				$(this).addClass('active');
			});
		});
	
		
		
		
	});		
	
      
	
	
	function save_art(){
                
            var art_id =  $('#art_id').val();
            var cat_id =  $('#cat_id').val();
            var art_title =  $('#art_title').val();
            var art_content = UE.getEditor('art_content').getContent();
            var add_date =  $('#add_date').val();
            var sort_order = $('#sort_order').val();
            var summary = $('#summary').val();

		$.post('art_action.php?action=save_art',{
			 art_id : art_id,
             cat_id : cat_id,
             art_title : art_title,
             art_content : art_content,
             add_date : add_date,
             sort_order : sort_order,
             summary : summary,
             page_name:'art.php',
			r:Math.random()
		},function(data){
			if(data=='success'){				
				$('#list_div').show();
				$('#add_div').hide();
	 			list(last_page_id);	 
			} else {
				alert(data);
			}
		});
	}

	
	
	function add_init(){
		$('#add_div').show();
		$('#list_div').hide();
		clear_info();
	}
	
	function ret(){                
                clear_info();	
                $('#add_div').hide();
		$('#list_div').show();
		
	}
	
	
	
	function clear_info(){             
          $('#art_id').val('');
          $('#cat_id').val('');
          $('#art_title').val('');
          UE.getEditor('art_content').setContent('', false);
          $('#add_date').val('');
          $('#sort_order').val('');
          $('#summary').val('');
	}

	function list(page_id){		 
		 var filter = $('#filter').val();
		 var cat_id = $('#sel_cat_id').val();     
		 var sort_method = $('#sel_order').val();
		 var start_time = $('#start_time').val();
		 var end_time = $('#end_time').val();
		 last_page_id=page_id;
		 $.get('art_action.php',
			{ r:Math.random(),
			  action:'list_art',
			  page_id:page_id,                        
			  filter:filter,  
			  cat_id:cat_id,
			  sort_method:sort_method,
			  start_time:start_time,
			  end_time:end_time,
              page_name:'art.php'	 
			},
			function(data){				
				$('#list_div_tbl').html(data);
		 });
	 }

	var last_page_id = 1;

	function go_page(page_id){
		list(page_id);	
	}


	function hide_this(ele){
		$(ele).parent().parent().parent().parent().parent().remove();
	}
	


	function list_art(){
	 	$('#list_div').show();
		$('#add_div').hide();
	 	list(1);	
	 }

	 function del_art(art_id){
		 if(confirm('确认要删除吗？')){
 			$.get('art_action.php?action=del_art',
		 	{r:Math.random(),art_id:art_id,page_name:'art.php'},
		 	function(data){
				 if(data=='success'){
					go_page(last_page_id);
				 }
			 }
		 	);
		 }
		
	 }

	 function move_up(art_id){
	 	 $.get('art_action.php?action=reorder_art',
	 	 {page_name:'art.php',art_id:art_id,move_action:'up',r:Math.random()},
	 	 	 function(data){
	 	 	 	 if(data=='success'){
	 	 	 	 	 	go_page(last_page_id);
	 	 	 	 } else {
	 	 	 	 	alert(data); 
	 	 	 	 }
	 	 	 });
	 }
	 
	 function move_down(art_id){
	 	  $.get('art_action.php?action=reorder_art',
	 	 {page_name:'art.php',art_id:art_id,move_action:'down',r:Math.random()},
	 	 	 function(data){
	 	 	 	 if(data=='success'){
	 	 	 	 	 	go_page(last_page_id);
	 	 	 	 }else {
	 	 	 	 	alert(data); 
	 	 	 	 }
	 	 	 });
	 }
     

</script>
<body>
	  <div id='wrapper' style="position:absolute;left:0px;top:0px;">
            <div class="navbar" id='top_div'>
                <div class="navbar-inner">
                  <?php include("top_div1.php") ?>                             
                
                  <div style="position:inline; float: right;">
                            <ul class="nav">  
                                <li>
                                    <a href='javascript:void(0)' onclick='add_init()'>新增记事</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='list_art()'>记事列表</a>
                                </li>
                            </ul>
                        </div>	 
	  
		</div>
		</div>
	<div id='add_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
			
  <form class="form-horizontal">
    <fieldset>
      <div id="legend" class="">
        <legend class="">记事</legend>
      </div>
  
	

    <div class="control-group">

          <!-- Text input-->
          <label class="control-label" for="input01">标题</label>
          <div class="controls">
          	  <input type="hidden" id="art_id" value="0" />
            <input type="text" placeholder="" class="input-xlarge" id="art_title" style='width:500px;'>
            <p class="help-block"></p>
          </div>
        </div>

    <div class="control-group">

          <!-- Select Basic -->
          <label class="control-label">分类</label>
          <div class="controls">
            <select class="input-xlarge" id="cat_id">
      <option>Enter</option>
      <option>Your</option>
      <option>Options</option>
      <option>Here!</option></select>
          </div>

        </div><div class="control-group">

          <!-- Textarea -->
          <label class="control-label">摘要</label>
          <div class="controls">
            <div class="textarea">
                  <textarea type="" class="" id="summary" style='width:500px;height:70px;'></textarea>
            </div>
          </div>
        </div>

    

    <div class="control-group">

          <!-- Textarea -->
          <label class="control-label">内容</label>
          <div class="controls">
            <div class="textarea">
            <script id="art_content" type="text/plain" style="width:512px;height:200px;"></script>
            
            </div>
          </div>
        </div>

    <div class="control-group">

          <!-- Text input-->
          <label class="control-label" for="input01">排序</label>
          <div class="controls">
            <input id="sort_order" type="text" placeholder="" class="input-xlarge">
            <p class="help-block"></p>
          </div>
        </div>

    <div class="control-group">

          <!-- Text input-->
          <label class="control-label" for="input01">日期</label>
          <div class="controls">
            <input id="add_date" type="text" placeholder="" class="input-xlarge" class='Wdate' onClick='WdatePicker()'>
            <p class="help-block"></p>
          </div>
        </div>

    <div class="control-group">
          <label class="control-label"></label>

          <!-- Button -->
          <div class="controls">
            <button class="btn btn-success" onclick='save_art();return false;'>提交</button>
               <button class="btn btn-primary" onclick='ret();return false;'>返回</button>
          </div>
        </div>

    </fieldset>
  </form>
  		
  		    </div>
  		   <div id='list_div' style='padding-left:0px;padding-top:20px;margin-left:50px;margin-right:50px;'>
			
<table id='search_tbl'  class='table table-bordered table-striped table-hover'>
	<tr><td>


		筛选：<input id='filter' name='filter' style='width:150px;' />                                
                &nbsp;
          分类：<select id="sel_cat_id">
          
          </select>             
           &nbsp;
         排序：  <select id="sel_order">
          <option value='date'>按日期</option>
          <option value='sort'>按排序字</option>
          </select>             
           &nbsp;
      日期范围：     <input id='start_time' name='start_time' style='width:100px;' class='Wdate' onClick='WdatePicker()'  />
      至
            <input id='end_time' name='end_time' style='width:100px;' class='Wdate' onClick='WdatePicker()'  />
&nbsp;           
		<input type='button' value='搜索' onclick='list_art()' />

</td></tr></table>

				<div id='list_div_tbl'>

				</div>
		
	    		
	    					
	     	</div>
	<div id='info' style='margin-left:50px;margin-right:50px;'>
		
		</div>
	</div>
			
			
			<div id="oLayer" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
            width: 500px; display:none;">
                <div id='oLayer_content'>
                
                </div>
                
                <div style='float:right;'>
                	       <input type='button' value='x' onclick='hideme()' />
                	</div>
			</div>
    
      <div id="oLayerC" style="position: absolute; left: 0; top:80px; z-index: 2; background: #e6e6e6; margin-left:6px;
            width: 800px; display:none;">
                <div id='oLayer_contentC'>
                
                </div>
                
                <div style='float:right;'>
                	       <input type='button' value='x' onclick='hidemeC()' />
                	</div>
			</div>
					
						<?php
	require_once ( 'change_password.php');
		
		?>
		<script type="text/javascript">
	    var ue = UE.getEditor('art_content');

		</script>
</body>
</html>
