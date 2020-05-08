<html>
<head>
<title>钢丝管</title>
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
<style type="text/css">

body { line-height:30px;}

</style>
<script type='text/javascript' src='common.js'></script>
<script type='text/javascript'>
	//内径 品牌 公斤数 每卷米数
	function gsg(gg,pp,kg,m){
		var g = new Object();
		this.gg = gg;
		this.pp = pp;
		this.kg = kg;
		this.m = m;
	}
	
	var g_list=new Array()
	//var g1 = new gsg('16',0.8,0,0,0.01625);
	//g_list.push(g1);
	var g2 = new gsg('19','',15,50);
	g_list.push(g2);
	
	var g2a = new gsg('19','现代',19,50);
	g_list.push(g2a);
	
	var g3 = new gsg('25','',15,50);
	g_list.push(g3);	
	var g3a = new gsg('25','',20,50);
	g_list.push(g3a);
	var g3b = new gsg('25','',25,50);
	g_list.push(g3b);
	var g3c = new gsg('25','现代',30,50);
	g_list.push(g3c);
	
	var g4 = new gsg('32','',20,50);
	g_list.push(g4);
	var g4a = new gsg('32','',30,50);
	g_list.push(g4a);
	var g4c = new gsg('32','',40,50);
	g_list.push(g4c);
	var g4d = new gsg('32','现代',20,50);
	g_list.push(g4d);
	
	var g5 = new gsg('38','',30,50);
	g_list.push(g5);
	var g5a = new gsg('38','',45,50);
	g_list.push(g5a);
	var g5b = new gsg('38','现代',50,50);
	g_list.push(g5b);
	
	var g6 = new gsg('40','',40,50);
	g_list.push(g6);
	var g6a = new gsg('40','',50,50);
	g_list.push(g6a);
	
	var g7 = new gsg('50','',40,50);
	g_list.push(g7);
	var g7a = new gsg('50','',50,50);
	g_list.push(g7a);
	var g7b = new gsg('50','',60,50);
	g_list.push(g7b);
	var g7c = new gsg('50','现代',70,50);
	g_list.push(g7c);
	
	var g8 = new gsg('58','',60,50);
	g_list.push(g8);
	
	var g9 = new gsg('60','',60,50);
	g_list.push(g9);
	
	var g10 = new gsg('64','',40,30);
	g_list.push(g10);
	var g10a = new gsg('64','',60,30);
	g_list.push(g10a);
	var g10b = new gsg('64','现代',70,30);
	g_list.push(g10b);
	
	var g11 = new gsg('75','',50,30);
	g_list.push(g11);
	var g11a = new gsg('75','',70,30);
	g_list.push(g11a);
	var g11b = new gsg('75','现代',80,30);
	g_list.push(g11b);
	
	var g12 = new gsg('90','',78,30);
	g_list.push(g12);
	
	var g13 = new gsg('100','',90,30);
	g_list.push(g13);
	var g13a = new gsg('100','',100,30);
	g_list.push(g13a);
	
	
	$(document).ready(function(){
		var str = '<option value=""></option>';
		for(var i=0;i<g_list.length;i++){
			var item = g_list[i].gg;
			if(g_list[i].pp!=''){
				item += '|'+g_list[i].pp+'';
			}
			item += '|'+g_list[i].kg+'公斤';
			item += '|'+g_list[i].m+'米';
			str += '<option value="'+item+'">'+item+'</option>';
		}
		$('#gss_gg').html(str);
	});
	function change_gss_gg(){
		var val = $('#gss_gg').val();
		var arr = val.split('|');
		console.log(arr.length);
		var gg = '';
		var pp = '';
		var kg = '';
		var m = '';
		if(arr.length==4){
			gg = arr[0];
			pp = arr[1];
			kg = arr[2].replace('公斤','');
			m = arr[3].replace('米','');
		} else if(arr.length==3){
			gg = arr[0];
			kg = arr[1].replace('公斤','');
			m = arr[2].replace('米','');
		}
		$('#gss_kg').val(kg);
		$('#gss_m').val(m);
	
	}
	
	function compute_price(){
	
		var kg = $('#gss_kg').val();
		var m =  $('#gss_m').val();
		var kgmm = accDiv(kg,m);
		var price_kg = $('#gss_price').val();
		var price_m = accMul(kgmm,price_kg);
		$('#price_m').val(price_m);
		
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
							<legend>钢丝管价格计算</legend>
							
							<div class="control-group">
  <label class="control-label" for="gss_gg">规格</label>
  <div class="controls">
    <select id="gss_gg" name="gss_gg" class="input-xlarge" onchange='change_gss_gg()'>
    </select>
  </div>
</div>
							
			<div class="control-group">
  <label class="control-label" for="gss_kg">整卷公斤数</label>
  <div class="controls">
    <input id="gss_kg" name="gss_kg" type="text" placeholder="" class="input-xlarge">
    
  </div>
</div>
			
			
			<div class="control-group">
  <label class="control-label" for="gss_m">整卷米数</label>
  <div class="controls">
    <input id="gss_m" name="gss_m" type="text" placeholder="" class="input-xlarge">
    
  </div>
</div>
			
			<div class="control-group">
  <label class="control-label" for="gss_price">单价(按公斤)</label>
  <div class="controls">
    <input id="gss_price" name="gss_price" type="text" placeholder="" class="input-xlarge">
    
  </div>
</div>
				
			
			<div class="control-group">
  <label class="control-label" for=""></label>
  <div class="controls">
    <button id="btn1" name="btn1" class="btn btn-primary" onclick='compute_price();return false;'>计算长度</button>
  </div>
</div>
		
			
		<div class="control-group">
  <label class="control-label" for="price_m">单价（按米）</label>
  <div class="controls">
    <input id="price_m" name="price_m" type="text" placeholder="" class="input-xlarge">
    
  </div>
</div>
	  
	  
			<div class="control-group">
  <label class="control-label" for="price_span"></label>
  <div class="controls">                     
    <textarea id="price_span" name="price_span"></textarea>
  </div>
</div>
				
						<div class="control-group">
  <label class="control-label" for="log_div"></label>
  <div class="controls">                     
    <textarea id="log_div" name="log_div"></textarea>
  </div>
</div>
					
					</fieldset>
					</form>
								
</div>


</div>
</body>
</html>