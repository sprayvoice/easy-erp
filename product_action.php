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
    
    $db = new mysql($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
    $cls_pro = new db_product($db);
    $cls_tag = new db_tag($db);
    $cls_location = new db_location($db);
    $cls_price = new db_price($db);
	$cls_py = new db_py($db);
    $action = $_GET['action'];
    $pro = "";
    $tag1 = "";
    $model = "";
    $made = "";	
     if($action=='add_pro'){
    	$pro = $_POST['pro'];
    	$tag1 = $_POST['tag'];
    	$model = $_POST['model'];
    	$made =  $_POST['made'];
     }
    if($action=='add_pro' && $pro==""){
    	echo "商品名称不能为空";
    	return;
    }
    if($action=='get_pro'){
    	$pro_id = $_GET['pro_id'];
    
       $result = $cls_pro->get_product($pro_id);     
    
       $row = $db->fetch_assoc($result );
       if($row!=null){
       	$ret = json_encode($row);
       	echo $ret;
       }
       
    } else if($action=='add_pro'){
	    $count1 = $cls_pro->get_count_by_p_m_m($pro,$model,$made);
	    if($count1==0){	    	 
	       $pid= $cls_pro->get_next_product_id();    	       
	       $cls_pro->insert_product($pid,$pro,$model, $made, $tag1);

			$array = array();
			$pym = pinyin($pro);			
			$array = $cls_py->add_to_array($array,$pym);
			if($model!=""){
				$pym = pinyin($model);
				$array = $cls_py->add_to_array($array,$pym);
			}
			if($made!=""){
				$pym = pinyin($made);
				$array = $cls_py->add_to_array($array,$pym);
			}

			if($tag1!=""){
	        	$array1 = strsToArray($tag1);
	        	$count1=count($array1);
	        	for($i=0;$i<$count1;$i++){
	        		$t1 = $array1[$i];
	        		$cls_tag->add_tag($pid,$t1);

					$pym = pinyin($t1);
					$array = $cls_py->add_to_array($array,$pym);
	        	}
	        }
			$cls_py->insert_py_array($pid,$array);			
	        
	        echo "success";
	    } else {
	    	  echo "已经存在相同数据";
		}
    }   else if($action=='get_location_by_id'){
      $id = $_GET['id'];
      $result = $cls_location->get_location_by_id($id);   
        if($result==false){
	    	 echo mysql_error();
	  }
	 $row_data = $db->fetch_assoc($result );
	  if($row_data!=null){
	      $ret = json_encode($row_data);
	       echo $ret;
	   } else {
	       	echo "未找到数据";   
	   }
    
    }    else if($action=='get_price_by_id'){
      $id = $_GET['id'];
      
      $result = $cls_price->get_by_id($id);
      
      if($result==false){
    	  	 echo mysql_error();
    	 }
    	$row_data = $db->fetch_assoc($result );
    	    if($row_data!=null){
       	$ret = json_encode($row_data);
       	echo $ret;
       } else {
       	 echo "未找到数据";  
    	}	
      
    } 
    
    
     else if($action=='get_location_by_pid'){
     	$pro_id = $_GET['pro_id'];
     	$result = $cls_location->get_location_by_pid($pro_id);
     	 if($result==false){
    	      echo mysql_error();
         }
    	  $row_data = $db->fetch_assoc($result );
    	  echo "<table class='table table-bordered'>";
    	  echo "<thead>";
    	  echo "<tr>";
    	  echo "<th>编号</th>";
    	  echo "<th>存放位置</th>";
    	  echo "<th>数量</th>";
    	  echo "<th>操作</th>";
    	  echo "</tr>";
    	  echo "</thead>";
    	  echo "<tbody>";
    	  while($row_data!=null){
    	  	    echo "<tr>";
    	  	    echo "<td>".$row_data['id']."</td>";
    	  	    echo "<td>".$row_data['product_location']."</td>";
    	  	    echo "<td>".$row_data['product_quantity']."</td>";
    	  	    echo "<td>"."<a href='javascript:void(0)' onclick='edit_location(";
    	  	    echo $row_data['id'].")'>编辑</a> ";
    	  	    echo "<a href='javascript:void(0)' onclick='del_location(";
    	  	    echo $row_data['id'] .")'>删除</a></td>";
    	  	     echo "</tr>";
    	  	     $row_data = $db->fetch_assoc($result );
    	  }
    	  echo "<tr>";
    	  echo "<td colspan='3'></span>";
    	  echo "<td><a href='javascript:void(0)' onclick='add_location($pro_id )'>新增</a></td>";
    	  echo "</tr>";
    	  echo "</tbody>";
    	  echo "</table>";
    
    }  else if($action=='get_price_by_pid'){
     	$pro_id = $_GET['pro_id'];
        $result = $cls_price->get_by_product_id($pro_id);
     	 if($result==false){
    	      echo mysql_error();
         }
    	  $row_data = $db->fetch_assoc($result );
    	  echo "<table class='table table-bordered'>";
    	  echo "<thead>";
    	  echo "<tr>";
    	  echo "<th>编号</th>";
    	  echo "<th>名称</th>";
    	  echo "<th>价格</th>";
    	  echo "<th>操作</th>";
    	  echo "</tr>";
    	  echo "</thead>";
    	  echo "<tbody>";
    	  while($row_data!=null){
    	  	    echo "<tr>";
    	  	    echo "<td>".$row_data['price_id']."</td>";
    	  	    echo "<td>".$row_data['price_name']."</td>";
    	  	    echo "<td>".$row_data['product_price']."元/".$row_data['unit']."</td>";
    	  	    echo "<td>"."<a href='javascript:void(0)' onclick='edit_price(";
    	  	    echo $row_data['price_id'].")'>编辑</a> ";
    	  	    echo "<a href='javascript:void(0)' onclick='del_price(";
    	  	    echo $row_data['price_id'] .")'>删除</a></td>";
    	  	     echo "</tr>";
    	  	     $row_data = $db->fetch_assoc($result );
    	  }
    	  echo "<tr>";
    	  echo "<td colspan='3'></span>";
    	  echo "<td><a href='javascript:void(0)' onclick='add_price($pro_id )'>新增</a></td>";
    	  echo "</tr>";
    	  echo "</tbody>";
    	  echo "</table>";
    
    }
    else  if($action=='list_pro'){
        $sort1 = $_GET['sort1'];
        $filter1 = trim($_GET['filter1']);
        $id = $_GET['id'];
        $result= $cls_pro->list_pro($filter1,$sort1,$id);
		if($result==false){
			echo mysql_error();
		}
		$row_data = $db->fetch_assoc($result );
		echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>编号</th>";
		echo "<th>名称</th>";
		echo "<th>规格</th>";
		echo "<th>品牌/产地</th>";
		echo "<th>价格</th>";
		echo "<th>标签</th>";
		echo "<th>保存位置</th>";
		echo "<th>操作</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		if($row_data==null){
			$result= $cls_pro->list_pro_pym($filter1);
			$row_data = $db->fetch_assoc($result );
		} 
		while($row_data!=null){
		echo "<tr>";
		echo "<td>".$row_data['product_id']."";
		if($id!=""){
			echo "<input type='button' value='x' onclick='clearId()' />";
		}
		echo "</td>";
		echo "<td>"."<a href='javascript:void(0)' onclick='fill_tag(\"".$row_data['product_name']."\")'>".$row_data['product_name']."</a></td>";
		echo "<td>"."<a href='javascript:void(0)' onclick='fill_tag(\"".$row_data['product_model']."\")'>".$row_data['product_model']."</a></td>";
		echo "<td>"."<a href='javascript:void(0)' onclick='fill_tag(\"".$row_data['product_made']."\")'>".$row_data['product_made']."</a></td>";
		echo "<td>".$row_data['product_price']."</td>";
		echo "<td>"."<a href='javascript:void(0)' onclick='fill_tag(\"".$row_data['product_tags']."\")'>".$row_data['product_tags']."</a></td>";
		echo "<td>".$row_data['product_locations']."</td>";    	  	  
		echo "<td>"."<a href='javascript:void(0)' onclick='edit1(";
		echo $row_data['product_id'].")'>编辑</a> ";
		echo " <a href='javascript:void(0)' onclick='del1(";
		echo $row_data['product_id'] .")'>删除</a>";
		echo " "." <a href='javascript:void(0)' onclick='copy1(";
		echo $row_data['product_id'].")'>复制</a>"; 
		echo " "." <a href='javascript:void(0)' onclick='show_location(";
		echo $row_data['product_id'].")'>存放</a>"; 
		echo " "." <a href='javascript:void(0)' onclick='show_price(";
		echo $row_data['product_id'].")'>价格</a>"; 
		echo "</td>";
		echo "</tr>";
		$row_data = $db->fetch_assoc($result );
	
	}
    	 
		echo "</tbody>";
		echo "</table>";
    }  else  if($action=='list_pro_for_instock'){
        $sort1 = '2';
        $filter1 = trim($_GET['filter1']);
        $id = '';
        $result= $cls_pro->list_pro($filter1,$sort1,$id);
		if($result==false){
			echo mysql_error();
		}
		$row_data = $db->fetch_assoc($result );
		echo "<table id='list_tb1'  class='table table-bordered'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>编号</th>";
		echo "<th>名称</th>";
		echo "<th>规格</th>";
		echo "<th>品牌/产地</th>";
		echo "<th>操作</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		if($row_data==null){
			$result= $cls_pro->list_pro_pym($filter1);
			$row_data = $db->fetch_assoc($result );
		} 
		while($row_data!=null){
		echo "<tr>";
		echo "<td>".$row_data['product_id']."";
	
		echo "</td>";
		echo "<td>"."".$row_data['product_name']."</td>";
		echo "<td>"."".$row_data['product_model']."</td>";
		echo "<td>"."".$row_data['product_made']."</td>";	  	  
		echo "<td>"."<a href='javascript:void(0)' onclick='selectone(";
		echo $row_data['product_id'].")'>选择</a> ";
		echo "</td>";
		echo "</tr>";
		$row_data = $db->fetch_assoc($result );
	
	}
    	 
		echo "</tbody>";
		echo "</table>";
    } else if($action=='edit_pro'){
    		$pro = $_POST['pro'];
     		$pro_id = $_POST['product_id'];
      		$tag1 = $_POST['tag'];
    		$model1 = $_POST['model'];
    		$made = $_POST['made'];
	        $c  = $cls_pro->get_count_by_product_id($pro_id);
	       if($c==0){
	       	echo "未找到要编辑的产品";
	       	return;   
	       }
	       $c = $cls_pro->get_count_by_p_m_m_p($pro,$model1,$made,$pro_id);
	       if($c>0){	       	
	       	echo "名称为".$pro.",规格为".$model1.",品牌/产地为".$made."的产品已经存在";
	       	return;   
	       }	      
	       $cls_pro->update_product($pro,$model1,$made,$tag1,$pro_id);
	       $cls_tag->delete_by_pro_id($pro_id);	     
	        

			$array = array();
			$pym = pinyin($pro);			
			$array = $cls_py->add_to_array($array,$pym);
			if($model1!=""){
				$pym = pinyin($model1);
				$array = $cls_py->add_to_array($array,$pym);
			}
			if($made!=""){
				$pym = pinyin($made);							
				$array = $cls_py->add_to_array($array,$pym);
			}

			if($tag1!=""){
	        	$array1 = strsToArray($tag1);
	        	$count1=count($array1);
	        	for($i=0;$i<$count1;$i++){
	        		$t1 = $array1[$i];
	        		$cls_tag->add_tag($pro_id,$t1);

					$pym = pinyin($t1);
					$array = $cls_py->add_to_array($array,$pym);
	        	}
	        }
			$cls_tag->clean_unused_tags();		
			$cls_py->insert_py_array($pro_id,$array);		

	        echo "success";
    	
     
    }else  if($action=='del_pro'){
    	$pro_id = $_POST['pro_id'];
        $cls_pro->delete_pro($pro_id);
        $cls_tag->delete_by_pro_id($pro_id);
		$cls_location->delete_by_pro_id($pro_id);
	    $cls_tag->clean_unused_tags();
	    $cls_py->delete_by_pro_id($pro_id);		
        echo "success";
    } else if($action=='del_location_by_id'){
    	$id = $_GET['id'];
    	$result = $cls_location->get_location_by_id($id);
    	$result = $db->fetch_assoc($result );
       $pro_id= $result['product_id'];
       $cls_location->delete_by_id($id);
    	$cls_location->update_product_location($pro_id);  
        echo "success";
    }    else if($action=='del_price_by_id'){
    	$id = $_GET['id'];
    	$cls_price->delete_by_id($id);
        echo "success";
    } 
    else if($action=='save_product_location'){
    	$id = $_POST['id'];
    	$pro_id = $_POST['pro_id'];
    	$location1 = $_POST['location1'];
    	$quantity1 = $_POST['quantity'];
    	if($id==''){    	
    		$cls_location->insert_location($pro_id,$location1,$quantity1);
    		$cls_location->update_product_location($pro_id);    	
    		echo "success";
    	} else {    	
    		$cls_location->update_location($id,$location1,$quantity1);
    		$cls_location->update_product_location($pro_id);    	
    		echo "success";
    	}
    }
      else if($action=='save_product_price'){
    	$id = $_POST['id'];
    	$pro_id = $_POST['pro_id'];
    	$price_name = $_POST['price_name'];
    	$price = $_POST['price'];
    	$unit = $_POST['unit'];
    	$is_hide = $_POST['is_hide'];
		if($id==''){
			$result = $cls_price->insert_price($pro_id,$price_name,$price,$unit,$is_hide);
			if($result!="success"){
				echo $result;
			}
			$cls_price->update_product_price($pro_id);
			echo "success";
				
		} else {
			$cls_price->update_price($id,$pro_id,$price_name, $price,$unit,$is_hide);
			$cls_price->update_product_price($pro_id);
			echo "success";
		}
    
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
         }  
      
?>