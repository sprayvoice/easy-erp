<?php
	
	
 
    require_once ( 'data/config.php'); 
    require_once ( 'mysql.class.php');
    require_once ( 'db_sales.class.php');
	require_once("db_client.class.php");
	require_once("db_stock.class.php");
	require_once ('Pager.php');

    
    $db = new mysql($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
    $cls_sales = new db_sales($db);    
	$cls_client = new db_client($db);
	$cls_stock = new db_stock($db); 
    $action = $_GET['action'];
    if($action=='get_sales'){
    	 	$batch_id = $_GET['batch_id'];
    	 	$result = $cls_sales->show_sales($batch_id);
    	 	$row_data = $db->fetch_assoc($result );
    	 	  if($row_data!=null){
       		$ret = json_encode($row_data);
       		echo $ret;
	         }
       
    }  else if($action=='get_sales_detail'){
		$batch_id = $_GET['batch_id'];
		$result = $cls_sales->show_detail($batch_id);
		$list = array();
		$row_data = $db->fetch_assoc($result );
		while($row_data!=null){
			array_push($list,$row_data);
			$row_data = $db->fetch_assoc($result );
		}
		$ret = json_encode($list);
		echo $ret;	    				
    }
    else if($action=='save_sales'){
    		$batch_id = $_POST['batch_id'];
    		$mode = 'add';
    		if($batch_id>0){
    			$mode= 'edit';
    		}
		$company_name = $_POST['company_name'];
		$date = $_POST['date'];

		$pro_id_s = $_POST['pro_id'];
		$name_gg_s = $_POST['name_gg'];
		$p_model_s = $_POST['p_model'];
		$p_made_s = $_POST['p_made'];
		$unit_s = $_POST['unit'];
		$quantity_s = $_POST['quantity'];
		$price_s = $_POST['price'];
		$money_s = $_POST['money'];
		$remark_s = $_POST['remark'];
		$remark_t = $_POST['remark_t'];
		
		$hj = $_POST['hj'];

		$name_gg_array = explode(',', $name_gg_s);
		$pro_id_array =  explode(',', $pro_id_s);
		$p_model_array =  explode(',', $p_model_s);
		$p_made_array =  explode(',', $p_made_s);
		$unit_array = explode(',', $unit_s);
		$quantity_array = explode(',', $quantity_s);
		$price_array = explode(',', $price_s);
		$money_array = explode(',',$money_s);
		$remark_array = explode(',', $remark_s);

		$summary = '';
		
		for($i=0;$i<count($name_gg_array);$i++){
			if($name_gg_array[$i]!=""){
				if($summary!=""){
					$summary = $summary.",";
				}
				$summary = $summary.$name_gg_array[$i];				
			}
		}
		$client_no = 0;

		$client_no = $cls_client->check_exist_company_exact($company_name);
		if($client_no==0){
			$client_company = $company_name;
			$client_addr = "";
			$tax_no = "";
			$bank_name = "";
			$client_phone = "";
			$remark = "";
   			$client_no = $cls_client->insert_client($client_company,
			   $client_addr,$tax_no,$bank_name,$client_phone,$remark);   
		}
		if($batch_id>0){
			$cls_sales->update_sales($batch_id,$hj,$date,$client_no,$remark_t,$summary);
		} else {
			$batch_id = $cls_sales->insert_sales($hj,$date,$client_no,$remark_t,$summary);
		}
		if($batch_id>0){
			if($mode=='edit'){
				$result = $cls_sales->show_detail($batch_id);
				$row_data = $db->fetch_assoc($result );
				$list1=array();
				$i=0;
				while($row_data!=null){
					$pro_id = $row_data['product_id'];
					$ammount = $row_data['sales_ammount'];
					$list1[$i]['product_id']=$pro_id;
					$list1[$i]['sales_ammount']=	$ammount;
					$row_data = $db->fetch_assoc($result );
					$i++;	
				}
				for($j=0;$j<$i;$j++){
					$cls_stock->update_stock_quantity_2($list1[$j]['product_id'],$list1[$j]['sales_ammount']);
				}
				$cls_sales->del_sales_detail($batch_id);
			}
			for($i=0;$i<count($name_gg_array);$i++){
				if($name_gg_array[$i]!=""){
					$pro_id = $pro_id_array[$i];
					$name_gg =  $name_gg_array[$i];
					$price =   	$price_array[$i];
					$quantity =  $quantity_array[$i];
					$money = $money_array[$i];
					$unit =  $unit_array[$i];
					$remark = $remark_array[$i];
					$p_model = $p_model_array[$i];
					$p_made = $p_made_array[$i];
					$detail_id = $cls_sales->insert_sales_detail($batch_id, $pro_id,$name_gg,$p_model ,$p_made,$price,$quantity,$money,$unit,$remark);
					$stock_count=$cls_stock->count_stock($pro_id);
					if($stock_count>0){
						$quantity_2=-$quantity;
						$cls_stock->update_stock_quantity_2($pro_id,$quantity_2);
					} else {
						$quantity_2=-$quantity;
						 $cls_stock->insert_stock_2(
		 $pro_id,$name_gg,$p_model,$p_made,$quantity_2,$unit);
					}
				}			 
			}
		}
	echo 'success';
       
       
    } else if($action=='list_sales'){
		$page_size = 10;
		$page_id = $_GET["page_id"];
		$client_name = $_GET['client_name'];
		$filter = $_GET['filter'];
		$start_time = $_GET['start_time'];
		$end_time = $_GET['end_time'];
		$total = $cls_sales->count_sales($client_name,$filter,$start_time,$end_time);
		$result = $cls_sales->list_sales($page_id,$page_size,$client_name,$filter,$start_time,$end_time);
		$row_data = $db->fetch_assoc($result );
		  echo "<table class='table table-bordered table-striped table-hover'>";
    	  echo "<thead>";
    	  echo "<tr>";
    	  echo "<th>编号</th>";
    	  echo "<th>日期</th>";
    	  echo "<th>金额</th>";
    	  echo "<th>客户名称</th>";
		  echo "<th>摘要</th>";
		  echo "<th>备注</th>";
		  echo "<th>操作</th>";
    	  echo "</tr>";
    	  echo "</thead>";
    	  echo "<tbody>";
		while($row_data!=null){
			$batch_id = $row_data['batch_id'];
			$sales_day = $row_data['sales_day'];
			$sales_money = $row_data['sales_money'];
			$client_company = '';
			if($row_data['client_company']!=null){
				$client_company = $row_data['client_company'];
			}
			$remark = $row_data['remark'];
			$summary = $row_data['summary'];
			echo "<tr>";
			echo "<td>$batch_id</td>";
    	  	echo "<td>$sales_day</td>";
    	  	echo "<td>$sales_money</td>";
    	  	echo "<td>$client_company</td>";
		  	echo "<td>$summary</td>";
		  	echo "<td>$remark</td>";			  
		  	echo "<td><a href='javascript:void(0)' onclick='show_detail($batch_id,this)'>详细 </a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='get_sales($batch_id)'> 编辑</a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='del_sales($batch_id)'>删除</a></td>";  
			echo "</tr>";
			$row_data = $db->fetch_assoc($result );
		}
		$a = new Pager();	
		echo "<td colspan='7'>";
		$a->mypage($total, $page_id, $page_size);
		echo "</td>";
		echo "</tr>";
		echo "</tbody>";
    	echo "</table>";

	} else if($action=='del_sales'){
		$batch_id = $_GET['batch_id'];
		$result = $cls_sales->show_detail($batch_id);
		$row_data = $db->fetch_assoc($result );
		$list1=array();
		$i=0;
		while($row_data!=null){
			$pro_id = $row_data['product_id'];
			$ammount = $row_data['sales_ammount'];
			$list1[$i]['product_id']=$pro_id;
			$list1[$i]['sales_ammount']=	$ammount;
			$row_data = $db->fetch_assoc($result );
			$i++;	
		}
		for($j=0;$j<$i;$j++){
			$cls_stock->update_stock_quantity_2($list1[$j]['product_id'],$list1[$j]['sales_ammount']);
		}
		$result = $cls_sales->del_sales($batch_id);
		echo $result;
	} else if($action=='show_detail'){
		$batch_id = $_GET['batch_id'];
		$result = $cls_sales->show_detail($batch_id);
		$row_data = $db->fetch_assoc($result );
		echo "<table class='table table-bordered' id='detail_$batch_id'>";
		echo "<thead>";
		echo "<tr>";
		echo "<td>品名及规格</td><td>单位</td><td>数量</td><td>单价</td><td>金额</td><td>备注</td>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		while($row_data!=null){
			$product_name = $row_data['product_name'];
			$unit = $row_data['unit'];
			$sales_ammount = $row_data['sales_ammount'];
			$sales_price = $row_data['sales_price'];
			$sales_money = $row_data['sales_money'];
			$remark = $row_data['remark'];
			echo "<tr>";
			echo "<td>$product_name</td>";
			echo "<td>$unit</td>";
			echo "<td>$sales_ammount</td>";
			echo "<td>$sales_price</td>";
			echo "<td>$sales_money</td>";
			echo "<td>$remark</td>";
			echo "</tr>";
			$row_data = $db->fetch_assoc($result );	
		}
		echo "<tr>";
		echo "<td colspan='6' style='text-align:right;'><a href='javascript:void(0)' onclick='hide_this(this)'>隐藏</a></td>";

		echo "</tr>";
		echo "</tbody>";
		echo "</table>";
	}
      
      
?>