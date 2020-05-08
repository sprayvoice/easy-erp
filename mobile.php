<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"] = "mobile.php";
    $url = "login.php";
    echo "<script language='javascript' type='text/javascript'>";
    echo "window.location.href='$url'";
    echo "</script>";
    return;
}
?><!DOCTYPE html> 
<html>
<head>
	<title>easy erp</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="tpl/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.css" />	
	<style type='text/css'>
	
	#sorter li {
    height: 3.8%;
    padding: 0;
    font-size: 8px;
    padding-left: 5px;
    line-height: 1.8em;
    text-align: left;
}
#sorter li span {
    margin-top: 8%;
    display: block;
}
#sorter{
    position: fixed;
    top: 60px;
    right: 0;
    width: 20px;
    z-index: 1;
}
#sorter ul{
    height: 100%;
}
#sortedList{
    padding-right: 35px;
}

td { line-height:30px;}
	</style>
	<script src="tpl/jquery.mobile-1.4.5/demos/js/jquery.js"></script>
	<script src="tpl/jquery.mobile-1.4.5/demos/_assets/js/index.js"></script>
	<script src="tpl/jquery.mobile-1.4.5/demos/js/jquery.mobile-1.4.5.min.js"></script>
	
		
</head>
<?php
	
	
    require_once ( 'data/config.php'); 
    require ( 'db/mysqli.class.php');
    require_once("db/db_log.class.php");
    require_once ( 'pinyin.php');
    require ( 'db/db_tag.class.php');
    
     $db = new mysql_db($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
     $cls_log = new db_log();
     $cls_tag = new db_tag($db,$cls_log);
       
    
    
    ?>
    
    	<?php
		$array_py = array();
		$array_tag_name= array();
                 $page_name = "mobile.php";
                  $log_batch_id = $cls_log->get_batch_id();   
                  $action="get_tag_list";
                  $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
		$result = $cls_tag->list_tag($log_info);
         	$row = $db->fetch_assoc($result );
         	while($row!=null){
         		$tag_name = $row['tag_name'];
         		$pym = pinyin($tag_name);
         		$pym1 = strtoupper(substr($pym,0,1));   	
         		if(array_key_exists($pym1,$array_py)){
         			
         		} else {
         			$array_py[$pym1]=$pym1;
         		}
         		$array_tag_name[] = $pym1.".".$tag_name;
         		$row = $db->fetch_assoc($result );
         	}
         	
	?>
<body>
<div data-role="page"  id="tag_list_div" class='ui-page-active'>
	<div data-role="header"> 商品标签  </div>
	<div role="main" class="ui-content">
		<div id="sorter">
			<ul data-role="listview">
	<?php
		$array_py = array_keys($array_py);
		$len = count($array_py);
		sort($array_py);
		for ($i=0;$i<$len ;$i++){
			echo "<li><span>".$array_py[$i]."</span></li>";	
		}
		
	?>
			</ul>
		</div>
		<ul data-role="listview" data-autodividers="true" id="sortedList">
		<?php
	sort($array_tag_name);
	$len = count($array_tag_name);
	for ($i=0;$i<$len ;$i++){
		echo "<li><a href='#product_list_div' onclick='go_tag(\"$array_tag_name[$i]\")'>".$array_tag_name[$i]."</a></li>";	
	}
	
		?>
		</ul>		
			</div>
			<div data-role="footer"></div>
		</div>
		
		<div data-role="page"  id="product_list_div">
			<div data-role="header"> <span id="tag_name">商品标签</span>  </div>
			<div role="main" class="ui-content" id="product_list_div_content">
				
			</div>
			<div data-role="footer"><a href='#tag_list_div'> 返回 </a></div>
		</div>
				
		<div data-role="page"  id="product_detail_div">
			<div data-role="header"> <span id="tag_name">商品标签</span>  </div>
			<div role="main" class="ui-content" id="product_detail_div_content">
				
			</div>	
			<div data-role="footer"><a href='#product_list_div'> 返回 </a></div>
		</div>
					
</div>	
				
				
		



		<script type='text/javascript'>
			
		$.mobile.document.on( "pagecreate", "#tag_list_div", function(){
	var head = $( ".ui-page-active [data-role='header']" );

	$.mobile.document.on( "click", "#sorter li", function() {
		
		var top,
			letter = $( this ).text();
			var divider = $("#sortedList").find( "li.ui-li-divider:contains(" + letter + ")" );
			
		if ( divider.length > 0 ) {
			top = divider.offset().top;
			$.mobile.silentScroll( top );
		} else {
			return false;
		}
	});
	$( "#sorter li" ).hover(function() {
		$( this ).addClass( "ui-btn" ).removeClass( "ui-li-static" );
	}, function() {
		$( this ).removeClass( "ui-btn" ).addClass( "ui-li-static" );
	});
});
$( function(){
	$.mobile.window.on( "scroll", function( e ) {
		var headTop = $(window).scrollTop(),
			foot = $( ".ui-page-active [data-role='footer']" ),
			head = $( ".ui-page-active [data-role='header']" ),
			headerheight = head.outerHeight();

		if( headTop < headerheight && headTop > 0 ) {
			$( "#sorter" ).css({
				"top": headerheight + 15 - headTop,
				"height": window.innerHeight - head[ 0 ].offsetHeight + window.pageYOffset - 10
			});
			$("#sorter li").height( "3.7%" );
		} else if ( headTop >= headerheight && headTop > 0 && parseInt( headTop +
			$.mobile.window.height( )) < parseInt( foot.offset().top ) ) {

			$( "#sorter" ).css({
				"top": "15px",
				"height": window.innerHeight - 8
			});
			$("#sorter li").height( "3.7%" );
		} else if ( parseInt( headTop + window.innerHeight ) >= parseInt( foot.offset().top ) &&
			parseInt( headTop + window.innerHeight ) <= parseInt( foot.offset().top ) +
			foot.height() ) {

			$( "#sorter" ).css({
				"top": "15px",
				"height": window.innerHeight - ( parseInt( headTop + window.innerHeight ) -
					parseInt( foot.offset().top ) + 8 )
			});
		} else if( parseInt( headTop + window.innerHeight ) >= parseInt( foot.offset().top ) ) {
			$( "#sorter" ).css({
				"top": "15px"
			});
		} else {
			$( "#sorter" ).css( "top", headerheight + 15 );
		}
	});
});
$.mobile.window.on( "throttledresize", function() {
	//var headerheight = $( ".ui-page-active [data-role='header']" ).outerHeight();
	$( "#sorter" ).height( window.innerHeight - headerheight - 20 ).css( "top", headerheight + 18 );
});
$.mobile.document.on( "pageshow", "#tag_list_div", function() {
	var headerheight = $( ".ui-page-active [data-role='header']" ).outerHeight();

	$( "#sorter" ).height( window.innerHeight - headerheight - 20 ).css( "top", headerheight + 18 );
});


function go_tag(tag_name){
	$('#tag_name').html(tag_name);
	tag_name = tag_name.substr(2);
	var url = 'mobile_action.php?action=list_pro';
	var para = {r:Math.random(),filter:tag_name,page_name:'mobile.php'};
	$.post(url,para,function(data){
		$('#product_list_div_content').html(data);
		$('#product_listview1').listview();
	});
}

function show_product(product_id){
	var url = 'mobile_action.php?action=show_detail';
	var para = {r:Math.random(),product_id:product_id,page_name:'mobile.php'};
	$.get(url,para,function(data){
		$('#product_detail_div_content').html(data);
		//$('.ui-grid-a').gridview();
	});
}

function pandian(){
	var id = $('#hid_pro_id').val();
	var quantity = $('#txt_quantity').val();
	$.post('mobile_action.php?action=pandian',
	{r:Math.random(),product_id:id,quantity:quantity,page_name:'mobile.php'},
		function(data){
			if(data=='success'){
				show_product(id);
			}
		});
	

}		

</script>


</body>
</html>