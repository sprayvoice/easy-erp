<?php

class db_sales {
	private $db;
 	public function __construct($db){
		 $this->db = $db;
 	}
 	
 	public function insert_sales(
		 $sales_money,$sales_day,$client_no,$remark,$summary){
		 $sql = "insert into ".constant("TABLE_PREFIX")."sales(sales_money,sales_day,sales_time,client_no,remark,summary) "
		 	." values($sales_money,'$sales_day',NOW(),$client_no,'$remark','$summary')";
		 $result = $this->db->query($sql);
		 $sql = "select @@IDENTITY as id";
		 $result = $this->db->query($sql);
		 $result = $this->db ->fetch_assoc($result );
		 $id= $result['id'];
		 return $id;	 
	 }
	 
	 public function update_sales(
		 $batch_id,$sales_money,$sales_day,$client_no,$remark,$summary){
		 $sql = "update ".constant("TABLE_PREFIX")."sales set sales_money=$sales_money,sales_day='$sales_day',client_no=$client_no,"
		 	 ."remark='$remark',summary='$summary' where batch_id=$batch_id ";
		 $result = $this->db->query($sql);
		 return $batch_id;
	 }
	 
 	

 	 public function insert_sales_detail($batch_id,$product_id,$product_name,$product_model,$product_made,$sales_price,$sales_ammount,$sales_money,$unit,$remark){
 	 	 $sql = "insert into ".constant("TABLE_PREFIX")."sales_detail(batch_id,product_id,product_name,product_model,product_made,sales_price,sales_ammount,sales_money,unit,remark) "
 	 	 ."values($batch_id,$product_id,'$product_name','$product_model','$product_made',$sales_price,$sales_ammount,$sales_money,'$unit','$remark')";
 	 	 $result = $this->db->query($sql);
		 $sql = "select @@IDENTITY as id";
		 $result = $this->db->query($sql);
		 $result = $this->db ->fetch_assoc($result );
		 $id= $result['id'];
		 return $id;	 
 	 }

	public function show_detail($batch_id){
		$sql = "select * from ".constant("TABLE_PREFIX")."sales_detail where batch_id = $batch_id";
		$result = $this->db->query($sql);
		return $result;
	}
	
	public function show_sales($batch_id){
		$sql = "select a.*,b.client_company from ".constant("TABLE_PREFIX")."sales a inner join ".constant("TABLE_PREFIX")."client b on a.client_no = b.client_no where a.batch_id = $batch_id";
		$result = $this->db->query($sql);
		return $result;
	}

	public function count_sales($client_name,$filter,$start_time,$end_time){
		$sql = "select count(*) c from ".constant("TABLE_PREFIX")."sales a left join ".constant("TABLE_PREFIX")."client b on a.client_no = b.client_no where 1=1 ";
		if($client_name!=""){
			$sql = $sql . " and b.client_company like '%$client_name%' ";
		}
		if($filter!=""){
			$sql = $sql ." and (a.remark like '%$filter%' or a.summary like '%$filter%') ";
		}
		if($start_time!=""){
			$sql = $sql ." and a.sales_day >= '$start_time' ";
		}
		if($end_time!=""){
			$sql = $sql ." and a.sales_day <= '$end_time' ";
		}
		$result = $this->db->query($sql);
		if($result==false){
			return mysql_error();
		}
		$result = $this->db ->fetch_assoc($result );
		if($result==false){
			return mysql_error();
		}
		$id= $result['c'];
		return $id;	 
	}
 
	 public function list_sales($page_id,$page_size,$client_name,$filter,$start_time,$end_time){
		if($page_id==""){
			$page_id = 1;
		}
		$limit = $page_size;
		$offset = ($page_id-1) * $page_size;
		$sql = "select a.*,b.client_company from ".constant("TABLE_PREFIX")."sales a left join ".constant("TABLE_PREFIX")."client b on a.client_no = b.client_no where 1=1 ";
		if($client_name!=""){
			$sql = $sql . " and b.client_company like '%$client_name%' ";
		}
		if($filter!=""){
			$sql = $sql . " and (a.remark like '%$filter%' or a.summary like '%$filter%') ";
		}
		if($start_time!=""){
			$sql = $sql . " and a.sales_day >= '$start_time' ";
		}
		if($end_time!=""){
			$sql = $sql . " and a.sales_day <= '$end_time' ";
		}
		$sql = $sql . " order by a.batch_id desc";
		$sql = $sql." limit ".$offset.",".$limit;

		$result = $this->db->query($sql);
		if($result==false){
			return mysql_error();
		}
		return $result;
	 }
	 
	 public function del_sales_detail($batch_id){
	 	$sql = "delete from ".constant("TABLE_PREFIX")."sales_detail where batch_id = $batch_id";
		$result = $this->db->query($sql);
		if($result){
			return "success";
		} else {
			return "从".constant("TABLE_PREFIX")."sales表删除失败";
		}
	 }

	 public function del_sales($batch_id){
		$sql = "delete from ".constant("TABLE_PREFIX")."sales_detail where batch_id = $batch_id";
		$result = $this->db->query($sql);
		if($result){
			$sql = "delete from ".constant("TABLE_PREFIX")."sales where batch_id = $batch_id";
			$this->db->query($sql);
			if($result){
				return "success";
			} else {
				return "从".constant("TABLE_PREFIX")."sales表删除失败";
			}
		} else 
			return "从".constant("TABLE_PREFIX")."sales_detail表删除失败";
		
	 }

}
?>