<?php

class db_tag {
	private $db;
 	public function __construct($db){
 		$this->db = $db;
 	}
 	
 	public function list_tag(){
 		$sql = "select * from ".constant("TABLE_PREFIX")."tag";
 		 	$result = $this->db->query($sql);
 		 	return  	$result ;
 	}
 
 	
 	public function get_next_tag_id(){
 		$sql = "SELECT IFNULL(MAX(tag_id),0)+1 tid FROM ".constant("TABLE_PREFIX")."tag";
		$result = $this->db->query($sql);
	 	if($result==false){
			return mysql_error();
		}
		$result = $this->db->fetch_assoc($result );
		$tid= $result['tid'];
		return $tid;
 	}
 	
 	public function delete_by_pro_id($pro_id){
 		 $sql = "delete from ".constant("TABLE_PREFIX")."product_tag where product_id=$pro_id";
	       $this->db->query($sql);
 	}
 	
 	public function clean_unused_tags(){
 		$sql = "SELECT DISTINCT a.tag_id FROM ".constant("TABLE_PREFIX")."tag a LEFT JOIN ".constant("TABLE_PREFIX")."product_tag b ON a.tag_id = b.tag_id WHERE b.tag_id IS NULL";
 		 $result = $this->db->query($sql);
 		  $row = $this->db->fetch_assoc($result );
 		  $c = array();
 		  while($row!=null){
 		       $tag_id = $row['tag_id'];
 		       $c[] = $tag_id; 		  
 		  	 $row = $this->db->fetch_assoc($result );
 		  }
 		  
 		  for($i=0;$i<count($c);$i++){
 		  	   $sql = "delete from ".constant("TABLE_PREFIX")."tag where tag_id = ".$c[$i];
 		  	   $this->db->query($sql);
 		  }
 	}
 	
 	public function add_tag($product_id,$tag_name){
 			$sql = "select count(*) c1 from ".constant("TABLE_PREFIX")."tag where tag_name = '$tag_name'";
	        		$result = $this->db->query($sql);
	        		 if($result==false){
    	  	  			return mysql_error();
    	  			}
	       		$result = $this->db->fetch_assoc($result );
	       		 if($result==false){
    	  	  			return mysql_error();
    	  			}
	       		$c1= $result['c1'];
	       		if($c1==0){	       		
	       			$tid = $this->get_next_tag_id();	       				       			
	       			$sql = "insert into ".constant("TABLE_PREFIX")."tag(tag_id,tag_name) values($tid,'$tag_name')";
	       			$this->db->query($sql);
	       			$sql = "insert into ".constant("TABLE_PREFIX")."product_tag(product_id,tag_id) values($product_id,$tid)";
	       			$this->db->query($sql);
	       			return "success";
	       		} else {
	       			$sql = "select tag_id from ".constant("TABLE_PREFIX")."tag where tag_name = '$tag_name'";
	       			 $result = $this->db->query($sql);
	       			 if($result==false){
    	  	  				return mysql_error();
    	  				}
	       			 $result = $this->db->fetch_assoc($result );
	       			 $tid= $result['tag_id'];
	       			 $sql = "insert into ".constant("TABLE_PREFIX")."product_tag(product_id,tag_id) values($product_id,$tid)";
	       			$this->db->query($sql); 
	       			return "success";
	       		}
 	}
}
?>