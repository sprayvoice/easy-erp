<?php

class db_instock {
	private $db;
 	public function __construct($db){
		 $this->db = $db;
 	}
 	
 	public function insert_instock(
		 $total_money,$in_company,$add_date,$remark,$summary){
		 $sql = "insert into ".constant("TABLE_PREFIX")."instock(total_money,in_company,add_date,add_time,remark,summary) "
		 	." values($total_money,'$in_company','$add_date',NOW(),'$remark','$summary')";
		 $result = $this->db->query($sql);
		 $sql = "select @@IDENTITY as id";
		 $result = $this->db->query($sql);
		 $result = $this->db ->fetch_assoc($result );
		 $id= $result['id'];
		 return $id;	 
	 }
	 
	 public function update_instock(
		 $in_batch_id,$total_money,$in_company,$add_date,$remark,$summary){
		 $sql = "update ".constant("TABLE_PREFIX")."instock set total_money=$total_money,in_company='$in_company',add_date='$add_date',"
		 	 ."remark='$remark',summary='$summary' where in_batch_id=$in_batch_id ";
		 $result = $this->db->query($sql);
		 return $in_batch_id;
	 }
	 
 	

 	 public function insert_instock_detail($in_batch_id,$product_id,$product_name,$product_model,$product_made,$in_price,$in_quantity,$unit,$remark){
 	 	 $sql = "insert into ".constant("TABLE_PREFIX")."instock_detail(in_batch_id,product_id,product_name,product_model,product_made,in_price,in_quantity,unit,remark) "
 	 	 ."values($in_batch_id,$product_id,'$product_name','$product_model','$product_made',$in_price,$in_quantity,'$unit','$remark')";
 	 	 $result = $this->db->query($sql);
		 $sql = "select @@IDENTITY as id";
		 $result = $this->db->query($sql);
		 $result = $this->db ->fetch_assoc($result );
		 $id= $result['id'];
		 return $id;	 
 	 }

	public function show_detail($in_batch_id){
		$sql = "select *,in_quantity*in_price in_money from ".constant("TABLE_PREFIX")."instock_detail where in_batch_id = $in_batch_id";
		$result = $this->db->query($sql);
		return $result;
	}
	
	public function show_instock($in_batch_id){
		$sql = "select a.* from ".constant("TABLE_PREFIX")."instock a  where a.in_batch_id = $in_batch_id";
		$result = $this->db->query($sql);
		return $result;
	}

	public function count_instock($company_name,$filter,$start_time,$end_time){
		$sql = "select count(*) c from ".constant("TABLE_PREFIX")."instock a where 1=1 ";
		if($company_name!=""){
			$sql = $sql . " and a.in_company like '%$company_name%' ";
		}
		if($filter!=""){
			$sql = $sql ." and (a.remark like '%$filter%' or a.summary like '%$filter%') ";
		}
		if($start_time!=""){
			$sql = $sql ." and a.add_date >= '$start_time' ";
		}
		if($end_time!=""){
			$sql = $sql ." and a.add_date <= '$end_time' ";
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
 
	 public function list_instock($page_id,$page_size,$in_company,$filter,$start_time,$end_time){
		if($page_id==""){
			$page_id = 1;
		}
		$limit = $page_size;
		$offset = ($page_id-1) * $page_size;
		$sql = "select a.* from ".constant("TABLE_PREFIX")."instock a where 1=1 ";
		if($in_company!=""){
			$sql = $sql . " and a.in_company like '%$in_company%' ";
		}
		if($filter!=""){
			$sql = $sql . " and (a.remark like '%$filter%' or a.summary like '%$filter%') ";
		}
		if($start_time!=""){
			$sql = $sql . " and a.add_date >= '$start_time' ";
		}
		if($end_time!=""){
			$sql = $sql . " and a.add_date <= '$end_time' ";
		}
		$sql = $sql . " order by a.add_date desc";
		$sql = $sql." limit ".$offset.",".$limit;

		$result = $this->db->query($sql);
		if($result==false){
			return mysql_error();
		}
		return $result;
	 }
	 
	 public function del_instock_detail($batch_id){
	 	$sql = "delete from ".constant("TABLE_PREFIX")."instock_detail where in_batch_id = $batch_id";
		$result = $this->db->query($sql);
		if($result){
			return "success";
		} else {
			return "从".constant("TABLE_PREFIX")."instock_detail表删除失败";
		}
	 }

	 public function del_instock($in_batch_id){
		$sql = "delete from ".constant("TABLE_PREFIX")."instock_detail where in_batch_id = $in_batch_id";
		$result = $this->db->query($sql);
		if($result){
			$sql = "delete from ".constant("TABLE_PREFIX")."instock where in_batch_id = $in_batch_id";
			$this->db->query($sql);
			if($result){
				return "success";
			} else {
				return "从".constant("TABLE_PREFIX")."instock表删除失败";
			}
		} else 
			return "从".constant("TABLE_PREFIX")."instock_detail表删除失败";
		
	 }

}
?>