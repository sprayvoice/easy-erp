<?php

class db_stock {
	private $db;
 	public function __construct($db){
		 $this->db = $db;
 	}
 	
 	public function count_stock($product_id){
 		$sql = "select count(*) c from ".constant("TABLE_PREFIX")."stock where product_id=$product_id";
 		$result = $this->db->query($sql);
		$result = $this->db ->fetch_assoc($result );
		$c= $result['c'];
		return $c;	 
 	}
 	
 	public function get_stock($pro_id){
 		 $sql = "select * from ".constant("TABLE_PREFIX")."stock where product_id = ".$pro_id;
	       $result = $this->db->query($sql);
	        if($result==false){
	    	  	  echo mysql_error();
	    	  }
	    	  return $result;
 	}
 	
 	public function insert_stock(
		 $product_id,$product_name,$product_model,$product_made,$stock_quantity,$stock_money,$stock_unit,$stock_price){
		 $sql = "insert into ".constant("TABLE_PREFIX")."stock(product_id,product_name,product_model,product_made,stock_quantity,stock_money,stock_unit,stock_price,last_upd_date) "
		 	." values($product_id,'$product_name','$product_model','$product_made',$stock_quantity,$stock_money,'$stock_unit',$stock_price,now())";
		 $result = $this->db->query($sql);
		 return $product_id;	 
	 }
	 
	 public function insert_stock_2(
		 $product_id,$product_name,$product_model,$product_made,$stock_quantity,$stock_unit){
		 $sql = "insert into ".constant("TABLE_PREFIX")."stock(product_id,product_name,product_model,product_made,stock_quantity,stock_money,stock_unit,stock_price,last_upd_date) "
		 	." values($product_id,'$product_name','$product_model','$product_made',$stock_quantity,0,'$stock_unit',0,now())";
		 $result = $this->db->query($sql);
		 return $product_id;	 
	 }
	 
	 public function update_stock(
		 $product_id,$product_name,$product_model,$product_made,$stock_unit){
		 $sql = "update ".constant("TABLE_PREFIX")."stock set product_name='$product_name',product_model='$product_model',product_made='$product_made',stock_unit='$stock_unit',last_upd_date=now() "
		 	 ." where product_id=$product_id ";
		 $result = $this->db->query($sql);
		 return $product_id;
	 }
	 
 	

 	 public function update_stock_quantity($product_id,$quantity,$money,$price,$unit){
 	 	 if($quantity>0){
 	 	 	$sql = "update ".constant("TABLE_PREFIX")."stock set stock_quantity=stock_quantity+$quantity,stock_money=stock_money+$money,stock_price= $price,last_upd_date=now(),stock_unit='$unit' "
		 	 ." where product_id=$product_id ";
 	 	 } else {
 	 	 	$quantity=-$quantity;
 	 	 	$money=-$money;
 	 	 		$sql = "update ".constant("TABLE_PREFIX")."stock set stock_quantity=stock_quantity-$quantity,stock_money=stock_money-$money,stock_price= $price,stock_unit='$unit' "
		 	 ." where product_id=$product_id ";
 	 	 }
		 $result = $this->db->query($sql);
		 return $product_id;	 
 	 }
 	 
 	  public function update_stock_quantity_2($product_id,$quantity){
 	 	 if($quantity>0){
 	 	 	$sql = "update ".constant("TABLE_PREFIX")."stock set stock_quantity=stock_quantity+$quantity,stock_money=stock_quantity*stock_price,last_upd_date=now() "
		 	 ." where product_id=$product_id ";
 	 	 } else {
 	 	 	$quantity=-$quantity;
 	 	 		$sql = "update ".constant("TABLE_PREFIX")."stock set stock_quantity=stock_quantity-$quantity,stock_money=stock_quantity*stock_price,last_upd_date=now() "
		 	 ." where product_id=$product_id ";
 	 	 }
		 $result = $this->db->query($sql);
		 return $product_id;	 
 	 }
 	 
 	 public function update_stock_quantity_3($product_id,$quantity){
 	 	 $sql = "update ".constant("TABLE_PREFIX")."stock set stock_quantity=$quantity,stock_money=stock_quantity*stock_price,last_upd_date=now() "
		 	 ." where product_id=$product_id ";
		 $result = $this->db->query($sql);
		 return $product_id;	 
 	 }

 
	 public function list_instock($page_id,$page_size,$filter1){
		if($page_id==""){
			$page_id = 1;
		}
		$limit = $page_size;
		$offset = ($page_id-1) * $page_size;
		$sql = "select distinct a.* from ".constant("TABLE_PREFIX")."stock a ";
		if($filter1!=""){
			$sql = $sql . " inner join ".constant("TABLE_PREFIX")."py b on a.product_id = b.product_id ";
			$sql = $sql . " where (a.product_name like '%$filter1%' or a.product_model like '%$filter1%' or a.product_made like '%$filter1%' )";
			$sql = $sql . " or (b.pym like '%$filter1%')  ";
		}
	
		$sql = $sql . " order by a.product_id desc";
		$sql = $sql." limit ".$offset.",".$limit;

		$result = $this->db->query($sql);
		if($result==false){
			return mysql_error();
		}
		return $result;
	 }
	 
	 public function del_stock($product_id){
	 	$sql = "delete from ".constant("TABLE_PREFIX")."stock where product_id = $product_id";
		$result = $this->db->query($sql);
		if($result){
			return "success";
		} else {
			return "从".constant("TABLE_PREFIX")."stock表删除失败";
		}
	 }



}
?>