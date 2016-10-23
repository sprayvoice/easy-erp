<?php
	
	
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
    require_once ( 'mysql.class.php');
    require_once ( 'pinyin.php');
    require_once ( 'db_product.class.php');
    require_once ( 'db_tag.class.php');
    require_once ( 'db_location.class.php');
    require_once ( 'db_price.class.php');
    require_once ( 'db_py.class.php');
    require_once ( 'db_stock.class.php');
    
    $db = new mysql($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
    $cls_pro = new db_product($db);
    $cls_tag = new db_tag($db);
    $cls_location = new db_location($db);
    $cls_price = new db_price($db);
    $cls_py = new db_py($db);
    $cls_stock=new db_stock($db);
    $action = $_GET['action'];
    $pro = "";
    $tag1 = "";
    $model = "";
    $made = "";	
  
      if($action=='list_pro'){

		echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>编号</th>";
		echo "<th>名称</th>";
		echo "<th>规格</th>";
		echo "<th>品牌/产地</th>";
		echo "<th>价格</th>";
		echo "<th>标签</th>";
		echo "<th>库存</th>";
		echo "<th>日期</th>";
		echo "<th>操作</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";

        $sort1 = $_GET['sort1'];
        $filter1 = trim($_GET['filter1']);
        $result= $cls_pro->list_pro_stock($filter1,$sort1,'');
		if($result==false){
			echo mysql_error();
		}
		$row_data = $db->fetch_assoc($result );		
		if($row_data==null){
			$result= $cls_pro->list_pro_pym_stock($filter1);
			$row_data = $db->fetch_assoc($result );
		} 
		while($row_data!=null){
			echo "<tr>";
			echo "<td>".$row_data['product_id']."";
			echo "</td>";
			echo "<td>"."<a href='javascript:void(0)' onclick='fill_tag(\"".$row_data['product_name']."\")'>".$row_data['product_name']."</a></td>";
			echo "<td>"."<a href='javascript:void(0)' onclick='fill_tag(\"".$row_data['product_model']."\")'>".$row_data['product_model']."</a></td>";
			echo "<td>"."<a href='javascript:void(0)' onclick='fill_tag(\"".$row_data['product_made']."\")'>".$row_data['product_made']."</a></td>";
			echo "<td>".$row_data['product_price']."</td>";
			echo "<td>"."<a href='javascript:void(0)' onclick='fill_tag(\"".$row_data['product_tags']."\")'>".$row_data['product_tags']."</a></td>";
			echo "<td>".$row_data['stock_quantity']."</td>"; 
			echo "<td>".$row_data['last_upd_date']."</td>";  	  	  
			echo "<td>"."<a href='javascript:void(0)' onclick='pandian1(";
			echo $row_data['product_id'].",this)'>盘点</a> ";
			echo "</td>";
			echo "</tr>";
			$row_data = $db->fetch_assoc($result );	
		}
    	 
		echo "</tbody>";
		echo "</table>";
    }  
       else if($action=='list_tags'){
         	echo "<br />";
         	$result = $cls_tag->list_tag();
         	$row = $db->fetch_assoc($result );
         	while($row!=null){
         		$tag_name = $row['tag_name'];
         		echo " <a href='javascript:void(0)' onclick='fill_tag(\"".$tag_name."\")'>$tag_name</a> &nbsp;";
         		$row = $db->fetch_assoc($result );
         	}
         } else if($action=='save_stock'){
         	$pro_id = $_GET['product_id'];
         	$stock_quantity=$_GET['stock_quantity'];
         	if($pro_id!='' && $stock_quantity!=''){
          		$cls_stock->update_stock_quantity_3($pro_id,$stock_quantity);
          		echo "success";
          	}
          	
         } else if($action=='get_stock'){
         	 $pro_id = $_GET['product_id'];
         	 if($pro_id!=''){
         	 	$result = $cls_stock->get_stock($pro_id);
         	 	$row = $db->fetch_assoc($result );
		       if($row!=null){
		       	$ret = json_encode($row);
		       	echo $ret;
		       }
         	 }
         }
      
?>