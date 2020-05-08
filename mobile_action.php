<?php
	
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

	function strsToArray($strs) {
    $result = array();
    $array = array();
    $strs = str_replace('，', ',', $strs);
    $strs = str_replace("n", ',', $strs);
    $strs = str_replace("rn", ',', $strs);
    $strs = str_replace(' ', ',', $strs);
    $array = explode(',', $strs);
    foreach ($array as $key => $value) {
        if ('' != ($value = trim($value))) {
            $result[] = $value;
        }
    }
    return $result;
}
    require_once ( 'data/config.php'); 
    require ( 'db/mysqli.class.php');
    require_once("db/db_log.class.php");
    require_once ( 'db/db_product.class.php');
    require_once ( 'db/db_stock.class.php');
    
    $db = new mysql_db($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
    $cls_log = new db_log();
    $cls_product = new db_product($db,$cls_log);   
    $action = $_GET['action'];
    if($action=='list_pro'){
    	 	$filter = trim($_POST['filter']);
    	 	$log_batch_id = $cls_log->get_batch_id();
                 $page_name = $_POST["page_name"];
                $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    	 	$result = $cls_product->list_pro_all($filter,"3","","all",0,$log_info);
    	 	$row_data = $db->fetch_assoc($result );
    	 	echo "<ul data-role=\"listview\" id=\"product_listview1\">";
    	 	 while($row_data!=null){
    	 	 	$info = $row_data['product_name'];
    	 	 	if($row_data['product_model']!=""){
    	 	 		$info = $info . "/" . $row_data['product_model'];
    	 	 	}
    	 	 	if($row_data['product_made']!=""){
    	 	 		$info = $info . "/" . $row_data['product_made'];
    	 	 	}
       		echo "<li><a href='#product_detail_div' onclick='show_product(".$row_data['product_id'].")'>".$info."</a></li>";	
       		$row_data = $db->fetch_assoc($result );
	         }
	         echo "</ul>";
       
    } else if($action=="pandian"){ 
    	$id = trim($_POST['product_id']);
    		$log_batch_id = $cls_log->get_batch_id();
    	$quantity = trim($_POST['quantity']);
          $page_name = $_POST["page_name"];
              $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    	if($quantity!="" && $id!=""){
    		$cls_product = new db_product($db,$cls_log);   
    		$result = $cls_product->get_product($id,$log_info);
    		$row = $db->fetch_assoc($result );
	       if($row!=null){
	       	$product_name = $row['product_name'];
	       	$product_model = $row['product_model'];
	       	$product_made = $row['product_made'];
	       	
	       	$cls_stock = new db_stock($db,$cls_log);   
	       	$c1 = $cls_stock->count_stock($id,$log_info);
	       	if($c1>0){
	       		$cls_stock->update_stock_quantity_3($id,$quantity,$log_info);
	       	} else {
	       		$cls_stock->insert_stock_2($id,$product_name,$product_model,$product_made,$quantity,$unit,$log_info);
	       	}
	       	echo "success";
	       }
    	}
    }
    else if($action=="show_detail"){
    	$id = trim($_GET['product_id']);
         $page_name = $_GET["page_name"];
         	$log_batch_id = $cls_log->get_batch_id();
                $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    	$result = $cls_product->get_product($id,$log_info);
    	$row = $db->fetch_assoc($result );
    	
       if($row!=null){
       	$product_name = $row['product_name'];
       	$product_model = $row['product_model'];
       	$product_made = $row['product_made'];
       	echo "<table><tr><td style='vertical-align:top; text-align:right;'>产品名称 ：</td><td>";
       	echo $product_name;
       	echo "</td></tr>";
       	//echo "<div class=\"ui-grid-a\">";
    		//echo "<div class=\"ui-block-a\"><div class=\"ui-bar ui-bar-a\" style=\"height:60px\"> 产品名称</div></div>";
    		//echo "<div class=\"ui-block-b\"><div class=\"ui-bar ui-bar-a\" style=\"height:60px\">".$product_name."</div></div>";
    		//echo "</div>";
    		
    		echo "<tr><td style='vertical-align:top; text-align:right;'>产品规格 ：</td><td>";
       	echo $product_model;
       	echo "</td></tr>";
    		/*echo "<div class=\"ui-grid-a\">";
    		echo "<div class=\"ui-block-a\"><div class=\"ui-bar ui-bar-a\" style=\"height:60px\"> 产品规格</div></div>";
    		echo "<div class=\"ui-block-b\"><div class=\"ui-bar ui-bar-a\" style=\"height:60px\">".$product_model."</div></div>";
    		echo "</div>";*/
    		
    		echo "<tr><td style='vertical-align:top; text-align:right;'>品牌/产地 ：</td><td>";
       	echo $product_made;
       	echo "</td></tr>";
       	
       	/*echo "<div class=\"ui-grid-a\">";
    		echo "<div class=\"ui-block-a\"><div class=\"ui-bar ui-bar-a\" style=\"height:60px\"> 产品品牌/产地</div></div>";
    		echo "<div class=\"ui-block-b\"><div class=\"ui-bar ui-bar-a\" style=\"height:60px\">".$product_made."</div></div>";
    		echo "</div>";*/
    		
    		/* location start */
    		//require_once ( 'db/db_location.class.php');
    		
    		
    		
    		$str = "";
    		$count1 = 0;
    		
    		/*price start */
    		require_once ( 'db/db_price.class.php');
    		
    		$cls_price = new db_price($db,$cls_log);
    		$result = $cls_price->get_by_product_id($id,$log_info);
    		$row = $db->fetch_assoc($result );
    		$str = "";
    		$count1 = 0;
    		while($row!=null){
         		$price_name = $row['price_name'];
         		$product_price = $row['product_price'];
         		$unit = $row['unit'];
         		$is_hide = $row['is_hide'];
         		$count1++;
         		//if($is_hide==0){
         			$str .= $price_name.":" .$product_price." 元/".$unit."<br />";
         		//}
         		$row = $db->fetch_assoc($result );
         	}
         	if($count1>0){
         		echo " <tr><td style='vertical-align:top; text-align:right;'>价格 ：</td><td>";
         		echo $str;
         		echo "</td></tr>";
         		
	         	/*echo "<div class=\"ui-grid-a\">";
	    		echo "<div class=\"ui-block-a\"><div class=\"ui-bar ui-bar-a\" style=\"line-height:".($count1*60)."px\"> "."价格"."</div></div>";
	    		echo "<div class=\"ui-block-b\"><div class=\"ui-bar ui-bar-a\" style=\"line-height:60px\">";
	    		echo $str;
	         	echo "</div></div>";
	    		echo "</div>";*/
    		}
    		
    		/* price  end */
    		
    		/*stock start */
    		require_once ( 'db/db_stock.class.php');
    		$cls_stock = new db_stock($db,$cls_log);
    		$count1 = $cls_stock->count_stock($id,$log_info);
    		if($count1>0){
    			$result = $cls_stock->get_stock($id,$log_info);
    			$row = $db->fetch_assoc($result);
    			echo " <tr><td style='vertical-align:top; text-align:right;'>库存 ：</td><td style='vertical-align:top;'>";
         		echo "数量：".$row['stock_quantity']." ".$row['stock_unit']."<br />日期：".substr($row['last_upd_date'],0,10);
         		echo "</td></tr>";
    			
    			/*echo "<div class=\"ui-grid-a\">";
	    		echo "<div class=\"ui-block-a\"><div class=\"ui-bar ui-bar-a\" style=\"line-height:120px\"> "."库存"."</div></div>";
	    		echo "<div class=\"ui-block-b\"><div class=\"ui-bar ui-bar-a\" style=\"line-height:60px\">";
	    		echo "数量：".$row['stock_quantity']." ".$row['stock_unit']."<br />日期：".substr($row['last_upd_date'],0,10);
	         	echo "</div></div>";
	    		echo "</div>";*/
    			
    		}    		
    		/* stock end*/
    		
    		/* sales start */
    		require_once ( 'db/db_sales.class.php');
    		$cls_sales = new db_sales($db,$cls_log);
    		$result = $cls_sales->show_top_5_sales($id,$log_info);
    		$row = $db->fetch_assoc($result );
    		$str = "";
    		$count1 = 0;
    		while($row!=null){
         		$sales_price = $row['sales_price'];
         		$unit = $row['unit'];
         		$c = $row['c'];
         		$count1++;
         		$str .= $sales_price." 元/ ".$unit." : " .$c."<br />";
         		$row = $db->fetch_assoc($result );
         	}
         	if($count1>0){
         		echo " <tr><td style='vertical-align:top; text-align:right;'> 销售 ：</td><td>";
         		echo $str;
         		echo "</td></tr>";
         		
	         	/*echo "<div class=\"ui-grid-a\">";
	    		echo "<div class=\"ui-block-a\"><div class=\"ui-bar ui-bar-a\" style=\"line-height:".($count1*60)."px\"> "."价格"."</div></div>";
	    		echo "<div class=\"ui-block-b\"><div class=\"ui-bar ui-bar-a\" style=\"line-height:60px\">";
	    		echo $str;
	         	echo "</div></div>";
	    		echo "</div>";*/
    		}
    		/* sales end */
    		
    		
    		echo " <tr><td style='vertical-align:top; text-align:right;'> 盘点 ：</td><td>";
    		echo "<input type='hidden' id='hid_pro_id' value='".$id."' />";
    		echo "数量：<input type='text' id='txt_quantity' />";	
    		echo "<input type='button' value='提交' class='btn primay' onclick='pandian()'/>";
    		echo "</td></tr>";
    		
    		
    		echo "</table>";
    		
       }
    	
    
    }  
      
      
?>