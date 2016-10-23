<?php

class db_price {
	private $db;
 	public function __construct($db){
 		$this->db = $db;
 	}
 	
 	public function delete_by_id($id){
 		$sql = "delete from ".constant("TABLE_PREFIX")."product_price where price_id = $id";
     	$this->db->query($sql);
 	}
 	
  	public function get_by_product_id($pro_id){
  		$sql = "select * from ".constant("TABLE_PREFIX")."product_price where product_id=".$pro_id." order by price_name";
     		$result = $this->db->query($sql);
     		return $result;
  	}
 
 	public function get_by_id($id){
 	      $sql = "select * from ".constant("TABLE_PREFIX")."product_price where price_id=".$id;
      	$result = $this->db->query($sql);
      	return $result;
 	}
 	
 	public function insert_price($pro_id,$price_name,$price,$unit,$is_hide){
 		$sql = "insert into ".constant("TABLE_PREFIX")."product_price(product_id,price_name,product_price,unit,is_hide) values($pro_id,'$price_name',$price,'$unit',$is_hide)";
		$result = $this->db->query($sql);
		 if($result==false){
		 	 return mysql_error();
		 }
		 return "success";
 	}
 	
 	public function update_price($price_id,$pro_id,$price_name,$price,$unit,$is_hide){
 		 $sql = "update ".constant("TABLE_PREFIX")."product_price set price_name='$price_name',product_price=$price,unit='$unit',is_hide=$is_hide where price_id=$price_id";
 		 $this->db->query($sql);
 	}
 	
 	public function update_product_price($pro_id){
 		 $sql = "select * from ".constant("TABLE_PREFIX")."product_price where product_id=$pro_id and is_hide=0 order by price_name";
    		$result = $this->db->query($sql);
	       $row = $this->db->fetch_assoc($result );
	       $prices = "";
	       $c1=1;
	        while($row!=null){
	        	$price_name=$row['price_name'];
	        	$product_price = $row['product_price'];
	        	$unit = $row['unit'];
	        	if($c1>1){
	        		$prices=$prices.",";
	        	}
	        	$prices = $prices.$price_name.":".$product_price."元/".$unit."";
	        	$c1 = $c1+1;
	        	$row = $this->db->fetch_assoc($result );
	        }
	        $sql = "update ".constant("TABLE_PREFIX")."product set product_price='$prices' where product_id=$pro_id";
	        $this->db->query($sql);
 		
 	}
}
?>