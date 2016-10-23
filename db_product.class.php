<?php

class db_product {
	private $db;
 	public function __construct($db){
 		$this->db = $db;
 	}
 	
 	public function get_product($pro_id){
 		 $sql = "select * from ".constant("TABLE_PREFIX")."product where product_id = ".$pro_id;
	       $result = $this->db->query($sql);
	        if($result==false){
	    	  	  echo mysql_error();
	    	  }
	    	  return $result;
 	}
 	
 	public function get_count_by_product_id($pro_id){
 		$sql = "select count(*) c from ".constant("TABLE_PREFIX")."product where product_id = ".$pro_id;
    		$result = $this->db->query($sql);
	       $result = $this->db->fetch_assoc($result );
	       $c= $result['c'];
	       return $c;
 	}
 	
 	public function get_next_product_id(){
 		$sql = "SELECT IFNULL(MAX(product_id),0)+1 pid FROM ".constant("TABLE_PREFIX")."product";
	    	 $result = $this->db->query($sql);
	       $result = $this->db ->fetch_assoc($result );
	       $pid= $result['pid'];
	       return $pid;
 	}
 	
 	public function get_count_by_p_m_m($product_name,$model,$made){
 	  $sql = "select count(*) c from ".constant("TABLE_PREFIX")."product where product_name = '$product_name' and product_model='$model' and product_made='$made'";
	    $result = $this->db->query($sql);
	        if($result==false){
    	  	  return mysql_error();
    	  }
	    $result = $this->db->fetch_assoc($result );
	    $count1= $result['c'];
	    return $count1;
 	}
 	
 	public function get_pro_id_by_p_m_m($product_name,$model,$made){
 		$sql = "select product_id from ".constant("TABLE_PREFIX")."product where product_name = '$product_name' and product_model='$model' and product_made='$made'";
 		 $result = $this->db->query($sql);
	        if($result==false){
    	  	  return mysql_error();
    	  }
	    $result = $this->db->fetch_assoc($result );
	    $count1= $result['product_id'];
	    return $count1;
 		
 	}
 	
 	public function get_count_by_p_m_m_p($pro,$model1,$made,$pro_id){
 		  $sql = "select count(*) c from ".constant("TABLE_PREFIX")."product where product_name='$pro' and product_model='$model1' and product_made='$made' and product_id<>".$pro_id;
	       $result = $this->db->query($sql);
	       $result = $this->db->fetch_assoc($result );
	       $c= $result['c'];
	       return $c;
	}
 	
 	public function insert_product($pid,$product_name,$model,$made,$tag1){
 		 $sql = "insert into ".constant("TABLE_PREFIX")."product(product_id,product_name,product_model,product_made,product_tags) values($pid,'$product_name','$model','$made','$tag1')";
	        $result = $this->db->query($sql);
 		
 	}
 	
 	public function update_product($pro,$model1,$made,$tag1,$pro_id){
 		 $sql = "update ".constant("TABLE_PREFIX")."product set product_name='$pro' ,product_model='$model1',product_made='$made',product_tags='$tag1'  where product_id=".$pro_id;
	       $this->db->query($sql);
 	}
 	
 	public function delete_pro($pro_id){
 		$sql = "delete from ".constant("TABLE_PREFIX")."product where product_id = ".$pro_id;
       	$result = $this->db->query($sql);
	}
	
	public function list_pro($filter1,$sort1,$id){
		  $sql = "select product_id,product_name,product_model,product_made,product_tags,product_locations,product_price from ".constant("TABLE_PREFIX")."product";
		  if($id!=""){
		  	$sql = $sql." where product_id =".$id; 
		  } else {
		  	 $sql = $sql." where 1 =1 "; 
		  }
	    	  if($filter1!=""){
	    	  	  $sql = $sql." and (product_name like '%$filter1%' or product_model like '%$filter1%' or product_made like '%$filter1%'  or product_tags like '%$filter1%' or product_locations like '%$filter1%') ";	    	  	  
	    	  }
	    	  if($sort1=='1'){
	    	  	   $sql = $sql." order by product_id";
	    	  } else if($sort1=='2'){
	    	  	   $sql = $sql." order by product_id desc";
	    	  } else if($sort1=='3'){
	    	  	   $sql = $sql." order by product_name,product_model,product_made";
	    	  }  else if($sort1=='4'){
	    	  	   $sql = $sql." order by product_name,product_made,product_model";
	    	  }
			  $sql = $sql." limit 100";
    	  	$result = $this->db->query($sql);
    	  	return $result;
	}
	
		public function list_pro_stock($filter1,$sort1,$id){
		  $sql = "SELECT a.product_id,a.product_name,a.product_model,a.product_made,a.product_tags,a.product_locations,a.product_price,b.stock_quantity,b.stock_money,b.stock_price,b.last_upd_date from ".constant("TABLE_PREFIX")."product a INNER JOIN ".constant("TABLE_PREFIX")."stock b ON a.product_id = b.product_id ";
		  if($id!=""){
		  	$sql = $sql." where a.product_id =".$id; 
		  } else {
		  	 $sql = $sql." where 1 =1 "; 
		  }
	    	  if($filter1!=""){
	    	  	  $sql = $sql." and (a.product_name like '%$filter1%' or a.product_model like '%$filter1%' or a.product_made like '%$filter1%'  or a.product_tags like '%$filter1%' or a.product_locations like '%$filter1%') ";	    	  	  
	    	  }
	    	  if($sort1=='1'){
	    	  	   $sql = $sql." order by a.product_id";
	    	  } else if($sort1=='2'){
	    	  	   $sql = $sql." order by a.product_id desc";
	    	  } else if($sort1=='3'){
	    	  	   $sql = $sql." order by a.product_name,a.product_model,a.product_made";
	    	  }  else if($sort1=='4'){
	    	  	   $sql = $sql." order by a.product_name,a.product_made,a.product_model";
	    	  }
			  $sql = $sql." limit 100";
    	  	$result = $this->db->query($sql);
    	  	return $result;
	}

	public function list_pro_pym($pym){
		$sql = "SELECT DISTINCT ".constant("TABLE_PREFIX")."product.product_id,product_name,product_model,product_made,product_tags,product_locations,product_price FROM " 
			." ".constant("TABLE_PREFIX")."product "
			." INNER JOIN (SELECT product_id,pym FROM ".constant("TABLE_PREFIX")."py WHERE pym LIKE '%$pym%') b "
			." ON ".constant("TABLE_PREFIX")."product.product_id = b.product_id ";
		$sql = $sql." limit 100";
		$result = $this->db->query($sql);		  
    	return $result;
		
	}
	
	public function list_pro_pym_stock($pym){
		$sql = "SELECT DISTINCT a.product_id,a.product_name,a.product_model,a.product_made,product_tags,product_locations,a.product_price,c.stock_quantity,c.stock_money,c.stock_price,c.last_upd_date FROM " 
			." ".constant("TABLE_PREFIX")."product a "
			." INNER JOIN (SELECT product_id,pym FROM ".constant("TABLE_PREFIX")."py WHERE pym LIKE '%$pym%') b "
			." ON a.product_id = b.product_id "
			." INNER JOIN ".constant("TABLE_PREFIX")."stock c ON a.product_id = c.product_id ";
		$sql = $sql." limit 100";
		$result = $this->db->query($sql);		  
    	return $result;
		
	}
}
?>