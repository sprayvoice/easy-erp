<?php

class db_sales {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }
    
    

    public function tj_sales_by_day($start_day, $end_day, $log_info) {
        $sql = "SELECT SUM(sales_money_real) money,sales_day FROM "
                . constant("TABLE_PREFIX") . "sales WHERE sales_day BETWEEN '$start_day' AND '$end_day' 
                    GROUP BY sales_day
                    ORDER BY sales_day";
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
    
    public function tj_sales_by_month($start_day, $end_day, $log_info) {
        $sql = "SELECT date_format(sales_day, '%Y-%m') month,SUM(sales_money_real) money FROM "
                . constant("TABLE_PREFIX") . "sales WHERE sales_day BETWEEN '$start_day' AND '$end_day' 
                    group by date_format(sales_day, '%Y-%m')
                    ORDER BY date_format(sales_day, '%Y-%m')";
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

    public function tj_sales_by_day_2($start_day, $end_day, $log_info) {
        $sql = "SELECT SUM(sales_money_real) money,sum(sales_money_real)/count(distinct(sales_day)) avg_money from "
                . constant("TABLE_PREFIX") . "sales WHERE sales_day BETWEEN '$start_day' AND '$end_day'";
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
    
    public function tj_sales_by_month_2($start_day, $end_day, $log_info) {
        $sql = "SELECT SUM(sales_money_real) money,sum(sales_money_real)/count(distinct(date_format(sales_day, '%Y-%m'))) avg_money from "
                . constant("TABLE_PREFIX") . "sales WHERE sales_day BETWEEN '$start_day' AND '$end_day'";
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

    public function tj_sales_by_tag($start_day, $end_day, $log_info) {
        $sql = "SELECT SUM(a.sales_money_real) money,d.tag_name FROM " . constant("TABLE_PREFIX") . "sales_detail a "
                . "INNER JOIN " . constant("TABLE_PREFIX") . "product b ON a.product_id = b.product_id "
                . "INNER JOIN " . constant("TABLE_PREFIX") . "product_tag c ON b.product_id = c.product_id "
                . "INNER JOIN " . constant("TABLE_PREFIX") . "tag d ON c.tag_id = d.tag_id "
                . "INNER JOIN " . constant("TABLE_PREFIX") . "sales e ON a.batch_id = e.batch_id "
                . "WHERE sales_day BETWEEN '$start_day' AND '$end_day' "
                . "GROUP BY d.tag_name "
                . "ORDER BY SUM(a.sales_money_real) DESC "
                . " LIMIT 25";
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

    public function insert_sales(
    $sales_money, $sales_money_real, $sales_day, $client_no, $remark, $summary, $log_info) {
        $sql = "insert into " . constant("TABLE_PREFIX")
                . "sales(sales_money,sales_day,sales_time,client_no,remark,summary,sales_money_real) "
                . " values($sales_money,'$sales_day',NOW(),$client_no,'$remark','$summary',$sales_money_real)";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $sql = "select @@IDENTITY as id";

//        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
//        if ($log_id == false) {
//            return false;
//        }
        $result = $this->db->query($sql);
//        if ($result != false) {
//            $this->cls_log->update_log_result($log_id, "success");
//        }
        $row = $this->db->fetch_assoc($result);
        $id = $row['id'];
        return $id;
    }

    public function update_sales(
    $batch_id, $sales_money, $sales_money_real, $sales_day, $client_no, $remark, $summary, $log_info) {
        $sql = "update " . constant("TABLE_PREFIX") . "sales set sales_money=$sales_money,sales_money_real=$sales_money_real,sales_day='$sales_day',client_no=$client_no,"
                . "remark='$remark',summary='$summary' where batch_id=$batch_id ";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $batch_id;
    }
    
    public function tj_quantity_by_product_id($product_id,$log_info){
		$sql = "SELECT sum(sales_ammount) sales_ammount,unit FROM ".constant("TABLE_PREFIX")."sales_detail WHERE product_id = $product_id group by unit";
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

    public function insert_sales_detail($batch_id, $product_id, $product_name, $product_model, $product_made, $sales_price, $sales_ammount, $sales_money, $sales_money_real, $unit, $remark, $log_info) {
        $sql = "insert into " . constant("TABLE_PREFIX") . "sales_detail(batch_id,product_id,product_name,product_model,product_made,sales_price,sales_ammount,sales_money,sales_money_real,unit,remark) "
                . "values($batch_id,$product_id,'$product_name','$product_model','$product_made',$sales_price,$sales_ammount,$sales_money,$sales_money_real,'$unit','$remark')";

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $sql = "select @@identity as id";
//        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
//        if ($log_id == false) {
//            return false;
//        }
        $result = $this->db->query($sql);
//        if ($result != false) {
//            $this->cls_log->update_log_result($log_id, "success");
//        }
        $row = $this->db->fetch_assoc($result);
        $id = $row['id'];
        return $id;
    }

    public function update_sales_detail_for_merge(
    $product_id, $product_name, $product_model, $product_made, $to_merge_product_id, $log_info) {
        $sql = "update " . constant("TABLE_PREFIX") . "sales_detail set product_id=" . $product_id . ",product_name='$product_name',product_model='$product_model',product_made='$product_made' "
                . " where product_id=$to_merge_product_id ";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $product_id;
    }

    public function show_detail($batch_id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "sales_detail where batch_id = $batch_id order by id";
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
        $sql = "select a.*,b.sales_day,c.client_company from ".constant("TABLE_PREFIX")."sales_detail a inner join ".constant("TABLE_PREFIX")."sales b on a.batch_id= b.batch_id left join ".constant("TABLE_PREFIX")."client c on b.client_no = c.client_no where a.product_id=".$product_id ." order by b.sales_day desc limit 200 ";
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

    public function show_sales($batch_id, $log_info) {
        $sql = "select a.*,IFNULL(b.client_company,'') client_company from " . constant("TABLE_PREFIX") . "sales a left join " . constant("TABLE_PREFIX") . "client b on a.client_no = b.client_no where a.batch_id = $batch_id";
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

    public function show_top_5_sales($product_id, $log_info) {
        $sql = "SELECT product_id,sales_price,unit,COUNT(*) c FROM " . constant("TABLE_PREFIX") . "sales_detail WHERE product_id = " . $product_id . " GROUP BY product_id,sales_price,unit ORDER BY COUNT(*) DESC LIMIT 5";

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

    public function count_sales($client_name, $filter, $start_time, $end_time, $min_money,$max_money, $log_info) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "sales a left join " . constant("TABLE_PREFIX") . "client b on a.client_no = b.client_no where 1=1 ";
        if ($client_name != "") {
            $sql = $sql . " and b.client_company like '%$client_name%' ";
        }
        if ($filter != "") {
            $sql = $sql . " and (a.remark like '%$filter%' or a.summary like '%$filter%') ";
        }
        if ($start_time != "") {
            $sql = $sql . " and a.sales_day >= '$start_time' ";
        }
        if ($end_time != "") {
            $sql = $sql . " and a.sales_day <= '$end_time' ";
        }
        if($min_money!=""){
            $sql = $sql . " and a.sales_money >= ".$min_money;
        }
        if($max_money!=""){
            $sql = $sql . " and a.sales_money <= ".$max_money;
        }
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }

        if ($result == false) {
            return mysql_error();
        }
        $result = $this->db->fetch_assoc($result);
        if ($result == false) {
            return mysql_error();
        }
        $id = $result['c'];
        return $id;
    }

    public function sum_sales($client_name, $filter, $start_time, $end_time, $min_money,$max_money, $log_info) {
        $sql = "select sum(sales_money) sales_money,sum(sales_money_real) sales_money_real from " . constant("TABLE_PREFIX") . "sales a left join " . constant("TABLE_PREFIX") . "client b on a.client_no = b.client_no where 1=1 ";

        if ($client_name != "") {
            $sql = $sql . " and b.client_company like '%$client_name%' ";
        }
        if ($filter != "") {
            $sql = $sql . " and (a.remark like '%$filter%' or a.summary like '%$filter%') ";
        }
        if ($start_time != "") {
            $sql = $sql . " and a.sales_day >= '$start_time' ";
        }
        if ($end_time != "") {
            $sql = $sql . " and a.sales_day <= '$end_time' ";
        }
        if($min_money!=""){
            $sql = $sql . " and a.sales_money >= ".$min_money;
        }
        if($max_money!=""){
            $sql = $sql . " and a.sales_money <= ".$max_money;
        }
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }

        $result = $this->db->query($sql);


        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result == false) {
            return mysql_error();
        }
        $result = $this->db->fetch_assoc($result);
        if ($result == false) {
            return mysql_error();
        }
        $ret["sales_money"] = $result['sales_money'];
        $ret["sales_money_real"] = $result['sales_money_real'];
        return $ret;
    }
    
    public function list_sales_all($start_time, $end_time, $log_info){
    	$sql = "select b.*,a.sales_money_real total_sales_money_real from ". constant("TABLE_PREFIX") . "sales a "." inner join ". constant("TABLE_PREFIX") ."sales_detail b on a.batch_id = b.batch_id where 1=1 ";
    	if ($start_time != "") {
            $sql = $sql . " and a.sales_day >= '$start_time' ";
        }
        if ($end_time != "") {
            $sql = $sql . " and a.sales_day <= '$end_time' ";
        }
        $sql = $sql . " order by a.sales_day desc,a.batch_id desc";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }

        $list = array();
        $result = $this->db->query($sql);                

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }

        if ($result == false) {
            return mysql_error();
        }
        $i = 0;
        $row_data = $this->db->fetch_assoc($result);
        while ($row_data != null) {
            $list[$i] = $row_data;            
            $row_data = $this->db->fetch_assoc($result);
            $i++;
        }
        
        return $list;
    }

    public function list_sales($page_id, $page_size, $client_name, $filter, $start_time, $end_time, $min_money,$max_money, $log_info) {
        if ($page_id == "") {
            $page_id = 1;
        }
        $limit = $page_size;
        $offset = ($page_id - 1) * $page_size;
        $sql = "select a.*,b.client_company from " . constant("TABLE_PREFIX") . "sales a left join " . constant("TABLE_PREFIX") . "client b on a.client_no = b.client_no where 1=1 ";
        if ($client_name != "") {
            $sql = $sql . " and b.client_company like '%$client_name%' ";
        }
        if ($filter != "") {
            $sql = $sql . " and (a.remark like '%$filter%' or a.summary like '%$filter%') ";
        }
        if ($start_time != "") {
            $sql = $sql . " and a.sales_day >= '$start_time' ";
        }
        if ($end_time != "") {
            $sql = $sql . " and a.sales_day <= '$end_time' ";
        }
        if($min_money!=""){
            $sql = $sql . " and a.sales_money >= ".$min_money;
        }
        if($max_money!=""){
            $sql = $sql . " and a.sales_money <= ".$max_money;
        }
        $sql = $sql . " order by a.sales_day desc,a.batch_id desc";
        $sql = $sql . " limit " . $offset . "," . $limit;

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }

        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }

        if ($result == false) {
            return mysql_error();
        }
        return $result;
    }

    public function del_sales_detail($batch_id, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "sales_detail where batch_id = $batch_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result) {
            return "success";
        } else {
            return "从" . constant("TABLE_PREFIX") . "sales表删除失败";
        }
    }

    public function del_sales($batch_id, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "sales_detail where batch_id = $batch_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result) {
            $sql = "delete from " . constant("TABLE_PREFIX") . "sales where batch_id = $batch_id";
            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }
            $result = $this->db->query($sql);
            if ($result != false) {
                $this->cls_log->update_log_result($log_id, "success");
            }
            if ($result) {
                return "success";
            } else {
                return "从" . constant("TABLE_PREFIX") . "sales表删除失败";
            }
        } else
            return "从" . constant("TABLE_PREFIX") . "sales_detail表删除失败";
    }
    
    
    /*
     * select aa.product_id,aa.product_price,aa.unit from 
(
select distinct a.product_id from ying_sales_detail a inner join ying_sales b on a.batch_id = b.batch_id
where b.sales_day = '2018-06-04' and a.product_id <>0
)  bb inner join 
ying_product_price aa 
 on aa.product_id = bb.product_id
 where aa.price_name = '进价';
     */
    
    public function get_in_sales_by_date($from,$to, $log_info) {
        $sql = "select aa.product_id,aa.product_price,aa.unit from ";
        $sql .= " ( ";
        $sql .= " select distinct a.product_id from ". constant("TABLE_PREFIX") ."sales_detail a "
                ." inner join ". constant("TABLE_PREFIX") ."sales b on a.batch_id = b.batch_id ";
        $sql .= " where (b.sales_day >= '$from' and b.sales_day <= '$to') and a.product_id <>0 ";
        $sql .= " )  bb inner join  ";
        $sql .= " ".constant("TABLE_PREFIX")."product_price aa   ";
        $sql .= " on aa.product_id = bb.product_id   ";
        $sql .= " where aa.price_name = '进价';  ";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], 
                $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        
        if ($log_id == false) {
            return false;
        }
        $list = array();
         
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }

        if ($result == false) {
            return mysql_error();
        }
        $row_data = $this->db->fetch_assoc($result);
        while ($row_data != null) {    
            $list[$row_data["product_id"]]=$row_data;
            $row_data = $this->db->fetch_assoc($result);
        }        
        return $list;                
    }

}

?>