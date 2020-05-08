<?php

require_once(dirname(__FILE__).'/../data/config.php');
require_once ( 'db_log.class.php');


class db_instock {
	private $db;
        private $cls_log;
 	public function __construct($db,$cls_log){
            $this->db = $db;
            $this->cls_log = $cls_log;
        }
        
        public function get_last($product_id,$log_info){
            $sql = "select * from ".constant("TABLE_PREFIX")."instock_detail where product_id = $product_id order by add_time desc limit 1 ";
            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "select", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }
            
            $result = $this->db->query($sql);
            
            if($result!=false){                    
                $this->cls_log->update_log_result($log_id, "success");
            }
            return $result;
        }
        
        public function get_in_company_filter($filter1,$log_info){
            $sql = "select distinct(in_company) from ".constant("TABLE_PREFIX")."instock where in_company like '%".$filter1."%' order by CONVERT(in_company USING gbk) ";
            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
            $result = $this->db->query($sql);
            if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }
            return $result;
        }
 	
 	public function insert_instock(
		 $total_money,$in_company,$add_date,$remark,$summary,$log_info){
		 $sql = "insert into ".constant("TABLE_PREFIX")."instock(total_money,in_company,add_date,add_time,remark,summary) "
		 	." values($total_money,'$in_company','$add_date',NOW(),'$remark','$summary')";
		 $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "insert", "error", $log_info["user_id"]);
                 if ($log_id == false) {
                    return false;
                }  
                 $result = $this->db->query($sql);
                 if($result!=false){                    
                    $this->cls_log->update_log_result($log_id, "success");
                }
		$sql = "select @@IDENTITY as id";
//                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
//                $sql, "select", "error", $log_info["user_id"]);
		$result = $this->db->query($sql);
		$row = $this->db ->fetch_assoc($result );		 
//                if($result!=false){                    
//                    $this->cls_log->update_log_result($log_id, "success");
//                }
                $id= $row['id'];
		return $id;	 
	 }
	 
	 public function update_instock(
		 $in_batch_id,$total_money,$in_company,$add_date,$remark,$summary,$log_info){             
		 $sql = "update ".constant("TABLE_PREFIX")."instock set total_money=$total_money,in_company='$in_company',add_date='$add_date',"
		 	 ."remark='$remark',summary='$summary' where in_batch_id=$in_batch_id ";
                 $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "update", "error", $log_info["user_id"]);
                if ($log_id == false) {
                    return false;
                }                      
		$result = $this->db->query($sql);
                if($result!=false){                    
                    $this->cls_log->update_log_result($log_id, "success");
                    return $in_batch_id;
                }
		 return $result;
	 }
         
         public function update_instock_detail_for_merge(
                $product_id,$product_name,$product_model,$product_made,$to_merge_product_id,$log_info){
            $sql = "update ".constant("TABLE_PREFIX")."instock_detail set product_id=".$product_id.",product_name='$product_name',product_model='$product_model',product_made='$product_made' "
		 	 ." where product_id=$to_merge_product_id ";
            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
		 $result = $this->db->query($sql);
                 if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }
		 return $product_id;
        }
	 
 	

 	 public function insert_instock_detail($in_batch_id,$product_id,$product_name,$product_model,$product_made,$in_price,$in_quantity,$unit,$remark,$log_info){
 	 	 $sql = "insert into ".constant("TABLE_PREFIX")."instock_detail(in_batch_id,product_id,product_name,product_model,product_made,in_price,in_quantity,unit,remark) "
 	 	 ."values($in_batch_id,$product_id,'$product_name','$product_model','$product_made',$in_price,$in_quantity,'$unit','$remark')";
                 $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
 	 	 $result = $this->db->query($sql);
                 if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }

		 $sql = "select @@IDENTITY as id";
//                 $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
//                $sql, "select", "error", $log_info["user_id"]);
//        if ($log_id == false) {
//            return false;
//        }
		 $result = $this->db->query($sql);
//                 if($result!=false){                    
//            $this->cls_log->update_log_result($log_id, "success");
//        }

		 $row = $this->db ->fetch_assoc($result );
		 $id= $row['id'];
		 return $id;	 
 	 }

	public function show_detail($in_batch_id,$log_info){
		$sql = "select *,in_quantity*in_price in_money from ".constant("TABLE_PREFIX")."instock_detail where in_batch_id = $in_batch_id";
                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "select", "error", $log_info["user_id"]);
                if ($log_id == false) {
                    return false;
                }                      
		$result = $this->db->query($sql);
                if($result!=false){                    
                    $this->cls_log->update_log_result($log_id, "success");
                    return $result;
                }
		return $result;
	}
	
	public function tj_quantity_by_product_id($product_id,$log_info){
		$sql = "SELECT sum(in_quantity) in_quantity,unit FROM ".constant("TABLE_PREFIX")."instock_detail WHERE product_id = $product_id group by unit";
		$log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        } 
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        } 
        return $result;
	}
        
    public function show_detail_by_product_id($product_id,$log_info){
        $sql = "select a.*,b.add_date from ".constant("TABLE_PREFIX")."instock_detail a inner join ".constant("TABLE_PREFIX")."instock b on a.in_batch_id= b.in_batch_id where a.product_id=".$product_id ." order by b.add_date desc limit 200 ";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        } 
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        } 
        return $result;
    }
	
	public function show_instock($in_batch_id,$log_info){
		$sql = "select a.* from ".constant("TABLE_PREFIX")."instock a  where a.in_batch_id = $in_batch_id";
                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }

		$result = $this->db->query($sql);
                
if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }
		return $result;
	}

	public function count_instock($company_name,$filter,$start_time,$end_time,$log_info){
		$sql = "select count(*) c,sum(total_money) total_money from ".constant("TABLE_PREFIX")."instock a where 1=1 ";
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
                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
		$result = $this->db->query($sql);
                if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }

		if($result==false){
			return mysql_error();
		}
		$result = $this->db ->fetch_assoc($result );
		if($result==false){
			return mysql_error();
		}
		$id= array($result['c'],$result['total_money']);
		return $id;	 
	}
 
	 public function list_instock($page_id,$page_size,$in_company,$filter,$start_time,$end_time,$log_info){
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
		$sql = $sql . " order by a.add_date desc,a.in_batch_id desc";
		$sql = $sql." limit ".$offset.",".$limit;
                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "select", "error", $log_info["user_id"]);
                if ($log_id == false) {
                    return false;
                }
		$result = $this->db->query($sql);
		if($result==false){
			return mysql_error();
		}
                if($result!=false){                    
                    $this->cls_log->update_log_result($log_id, "success");
                }
		return $result;
	 }
	 
	 public function del_instock_detail($batch_id,$log_info){
	 	$sql = "delete from ".constant("TABLE_PREFIX")."instock_detail where in_batch_id = $batch_id";
                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                        $sql, "select", "error", $log_info["user_id"]);
                if ($log_id == false) {
                    return false;
                }
		$result = $this->db->query($sql);
                if($result!=false){                    
                    $this->cls_log->update_log_result($log_id, "success");
                }
		if($result){
			return "success";
		} else {
			return "从".constant("TABLE_PREFIX")."instock_detail表删除失败";
		}
	 }

	 public function del_instock($in_batch_id,$log_info){
		$sql = "delete from ".constant("TABLE_PREFIX")."instock_detail where in_batch_id = $in_batch_id";                
                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],
                $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
		$result = $this->db->query($sql);
                if($result!=false){                    
            $this->cls_log->update_log_result($log_id, "success");
        }
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