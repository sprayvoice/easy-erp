<html>
<head>
<title>钢丝绳</title>
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
        	
<script type='text/javascript' src='common.js'></script>
<script type='text/javascript'>
	//直径 单价 做头加长 做头价格 每米公斤数 
	function gsg(gg,price,head_add,head_price,kg){
		var g = new Object();
		this.gg = gg;
		this.price = price;
		this.head_add = head_add;
		this.head_price = head_price;
		this.kg = kg;
	}
	var g_list=new Array()
	var g1 = new gsg('2',0.8,0,0,0.01625);
	g_list.push(g1);
	var g2 = new gsg('2-3',1.5,0,0,0.023);
	g_list.push(g2);
	var g3 = new gsg('4',2,0,0,0.06);
	g_list.push(g3);
	var g4 = new gsg('4-5',2.5,0,0,0.069);
	g_list.push(g4);
	var g5 = new gsg('5',3,0,0,0.08);
	g_list.push(g5);
	var g6 = new gsg('6',3.5,0,0,0.12);
	g_list.push(g6);
	var g7 = new gsg('8',4.5,0,0,0.19);
	g_list.push(g7);
	var g8 = new gsg('10',5,0,0,0.31);
	g_list.push(g8);
	var g9 = new gsg('11',6.5,1.2,10,0.4);
	g_list.push(g9);
	var g10 = new gsg('13',8.5,1.4,15,0.6);
	g_list.push(g10);
	var g11 = new gsg('15',10,1.6,15,0.8);
	g_list.push(g11);
	var g12 = new gsg('17.5',14,1.8,20,1.04);
	g_list.push(g12);
	var g13 = new gsg('19.5',18,2,25,1.33);
	g_list.push(g13);
	$(document).ready(function(){
		var str = '<option value=""></option>';
		for(var i=0;i<g_list.length;i++){
			str += '<option value="'+g_list[i].gg+'">'+g_list[i].gg+'</option>';
		}
		$('#gss_gg').html(str);
	});
	function change_gss_gg(){
		for(var i=0;i<g_list.length;i++){
			if(g_list[i].gg==$('#gss_gg').val()){
				$('#gss_price').val(g_list[i].price);
			}
		}
	}
	
	function compute_price(){
		var head_add = 0;
		var head_price = 0;
		var kg = 0;
		var kg_total = 0;
		var zt = $('#zt').val();
		var len_type = $('#len_type').val();
		var zt2 = accDiv(zt,2);
		var str = '';
		for(var i=0;i<g_list.length;i++){
			if(g_list[i].gg==$('#gss_gg').val()){
				head_add = g_list[i].head_add;
				head_price = g_list[i].head_price;
				kg = g_list[i].kg;
			}
		}
		var price = $('#gss_price').val();
		var len = $('#gss_length').val();
		var je = 0;
		if(zt==0){
			je =  accMul(price,len);
			str += ''+price+'*'+len+'='+je+';<br />';
			kg_total = accMul(kg,len);
			str += kg+'*'+len+"="+kg_total+";"
		}
		if(zt==1){
			
			var len2 = accDiv(head_add,2);
			var len3 = 0;
			if(len_type=='1'){
				len3 = accAdd(len,len2);
				str += len + '+'+len2+'='+len3+';<br />';
			} else {
				len3 = len;
			}
			je =  accMul(price,len3)+accDiv(head_price,2);
			str += ''+price+'*'+len3+'+'+head_price+'/2='+je+';<br />';
			kg_total = accMul(kg,len3);
			str += kg+'*'+len3+"="+kg_total+";"
		}
		if(zt==2){
			
			var len2 = 0;
			if(len_type=='1'){
				len2 = accAdd(len,head_add);
				str += len + '+'+head_add+'='+len2+';<br />';
			} else {
				len2 = len;
			}
			je =  accMul(price,len2)+head_price;
			str += ''+price+'*'+len2+'+'+head_price+'='+je+';<br />';
			kg_total = accMul(kg,len2);
			str += kg+'*'+len2+"="+kg_total+";"
		}		
		$('#je').val(je);	
		$('#kg_span').html(kg_total);
		$('#log_div').html(str);
		accMul(kg,len);
		
		
	}
	
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
</script>
</head>
<body>
<?php 
//echo basename(__FILE__);

function IsActivePage($page_name){
    if(basename($_SERVER["PHP_SELF"])==$page_name){
        echo " class='active'";
    } else {
        //echo basename($_SERVER["PHP_SELF"]);
    }
}

?>
  <div id='wrapper' style="position:absolute;left:0px;top:0px;">
            <div class="navbar" id='top_div'>
                <div class="navbar-inner">
<div class="container-fluid" id="top_div1">
                       
                        <a class="brand" href='javascript:void(0)'>工具</a>
                        <ul class="nav">
                            <li<?php IsActivePage("gss.php")?>>
                                <a href='gss.php' target='_blank'>钢丝绳</a>	
                            </li>    
       						<li<?php IsActivePage("gsg.php")?>>
                                <a href='gsg.php' target='_blank'>钢丝管</a>	
                            </li>      			
                        </ul>
                    </div>
    </div>
    </div>
<div style='padding:20px;'>
	<form class="form-horizontal">
							<fieldset>
							
							<legend>钢丝绳价格计算器</legend>
								
								
								<div class="control-group">
  <label class="control-label" for="gss_gg">钢丝绳规格</label>
  <div class="controls">
    <select id="gss_gg" name="gss_gg" class="input-xlarge" onchange='change_gss_gg()'>
    </select>
  </div>
</div>

<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="gss_price">单价</label>
  <div class="controls">
    <input id="gss_price" name="gss_price" type="text" placeholder="" class="input-xlarge">
    
  </div>
</div>
		
		<div class="control-group">
  <label class="control-label" for="len_type"></label>
  <div class="controls">
    <select id="len_type" name="len_type" class="input-xlarge">
    	<option value='1'>净长</option>
		<option value='2'>下料</option>
    </select>
  </div>
</div>							
			
<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="gss_length">长度</label>
  <div class="controls">
    <input id="gss_length" name="gss_length" type="text" placeholder="" class="input-xlarge">
   	&nbsp;米&nbsp;&nbsp;
  </div>
</div>
			
<div class="control-group">
  <label class="control-label" for="zt"></label>
  <div class="controls">
    <select id="zt" name="zt" class="input-xlarge">
   		<option value='0'>不做头</option>
		<option value='2'>做两头</option>
		<option value='1'>做一头</option>
    </select>
  </div>
</div>
			
<div class="control-group">
  <label class="control-label" for=""></label>
  <div class="controls">
    <button id="" name="" class="btn btn-primary" onclick='compute_price();return false;'>计算</button>
  </div>
</div>
	
<div class="control-group">
  <label class="control-label" for="je">金额</label>
  <div class="controls">
    <input id="je" name="je" type="text" placeholder="" class="input-xlarge">
    
  </div>
</div>
			
<div class="control-group">
  <label class="control-label" for="kg_span">公斤数</label>
  <div class="controls">                     
   <span id='kg_span'></span>
  </div>
</div>
				
		<div class="control-group">
  <label class="control-label" for="log_div"></label>
  <div class="controls">                     
   <span id='log_div'></span>
  </div>
</div>
		
		</fieldset>				
</div>
								
</div>


</div>
</body>
</html>