<?php

class db_location {
	private $db;
 	public function __construct($db){
 		$this->db = $db;
 	}
 	
 	public function delete_by_pro_id($pro_id){
 		$sql = 'delete from ".constant("TABLE_PREFIX")."product_location where product_id=$pro_id';
		$this->db->query($sql);
 	}
 	
 	public function delete_by_id($id){
 		$sql = "delete from ".constant("TABLE_PREFIX")."product_location where id = $id";
		$this->db->query($sql);
 	}
 	
 	public function get_location_by_pid($pro_id){
 		$sql = "select * from ".constant("TABLE_PREFIX")."product_location where product_id=".$pro_id;
     	 $result = $this->db->query($sql);
     	 return $result;
 	}
 
 	public function get_location_by_id($id){
 		$sql = "select * from ".constant("TABLE_PREFIX")."product_location where id=".$id;
	      $result = $this->db ->query($sql);
	    	return $result;
 	}
 	
 	public function insert_location($pro_id,$location1,$quantity1){
 		$sql = "insert into ".constant("TABLE_PREFIX")."product_location(product_id,product_location,product_quantity) values($pro_id,'$location1','$quantity1')";
    		$this->db->query($sql);
 	}
 	
 	public function update_location($id,$location1,$product_quantity){
 		$sql = "update ".constant("TABLE_PREFIX")."product_location set product_location='$location1',product_quantity='$product_quantity' where id=$id";
    		$this->db->query($sql);
 	}
 	
 	public function update_product_location($pro_id){
 		$sql = "select * from ".constant("TABLE_PREFIX")."product_location where product_id = $pro_id";
 		$result = 	$this->db->query($sql);
 		$row = $this->db->fetch_assoc($result );
    	      $locations = "";
             $c1=1;
        	while($row!=null){
        		$location1=$row['product_location'];
        		$quantity1 = $row['product_quantity'];
        		if($c1>1){
        			$locations=$locations.",";
        		}
        		$locations = $locations.$location1."(".$quantity1.")";
        		$c1 = $c1+1;
        		$row = $this->db->fetch_assoc($result );
    		    }
       	 $sql = "update ".constant("TABLE_PREFIX")."product set product_locations='$locations' where product_id=$pro_id";
      	  $this->db->query($sql);
 	}
}
?>