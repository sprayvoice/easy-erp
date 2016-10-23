<?php

class db_py {
	private $db;
 	public function __construct($db){
 		$this->db = $db;
 	}
 	
 	
 
 	public function count_py($pro_id,$pym){
 		$sql = "select count(*) c from ".constant("TABLE_PREFIX")."py where product_id=$pro_id and pym='$pym'";
    		$result = $this->db->query($sql);
	       $row = $this->db->fetch_assoc($result );
	       $count1=0;
	        if($row!=null){
	        	$count1=$row['c'];
	        }
	        return $count1;
 	}

	public function add_to_array($array,$item){
		if($item!=""){
			$array[] = $item; 
		}
		return $array;
	}

	public function delete_by_pro_id($pro_id){
		$sql = "delete from ".constant("TABLE_PREFIX")."py where product_id=$pro_id";
			$result = $this->db->query($sql);
		 if($result==false){
		 	 return mysql_error();
		 }
 		return "success";
	}
 	
 	public function insert_py_array($pro_id,$pym_array){
 		$ar = "";
 		for($i=0;$i<count($pym_array);$i++){
 			if($i>0){
 				$ar = $ar . ",";
 			}
 			$ar = $ar . "'".$pym_array[$i] . "'";
 		}
 		$sql = "delete from ".constant("TABLE_PREFIX")."py where product_id=$pro_id and pym not in ($ar)";
	
 		$result = $this->db->query($sql);
		 if($result==false){
		 	 return mysql_error();
		 }
		 for($i=0;$i<count($pym_array);$i++){
 			$count1 = $this->count_py( $pro_id,$pym_array[$i]);
 			if($count1==0){
 				 $this->insert_py($pro_id,$pym_array[$i]);
 			}
 		}
		 return "success";
 	}
 	
 	public function insert_py($pro_id,$pym){
 		$sql = "insert into ".constant("TABLE_PREFIX")."py(product_id,pym) values($pro_id,'$pym')";
		$result = $this->db->query($sql);
		 if($result==false){
		 	 return mysql_error();
		 }
		 return "success";
 	}
 	
 
}
?>