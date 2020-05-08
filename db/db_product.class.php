<?php

require_once ( 'db_log.class.php');

class db_product {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }

    public function get_product($pro_id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "product where product_id = " . $pro_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            echo mysql_error();
        }
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

    public function get_count_by_product_id($pro_id, $log_info) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "product where product_id = " . $pro_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $result = $this->db->fetch_assoc($result);
        $c = $result['c'];
        return $c;
    }

    public function get_next_product_id($log_info) {
        $sql = "SELECT IFNULL(MAX(product_id),0)+1 pid FROM " . constant("TABLE_PREFIX") . "product";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $result = $this->db->fetch_assoc($result);
        $pid = $result['pid'];
        return $pid;
    }

    public function get_count_by_p_m_m($product_name, $model, $made, $log_info) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "product where product_name = '$product_name' and product_model='$model' and product_made='$made'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            return mysql_error();
        }
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $result = $this->db->fetch_assoc($result);
        $count1 = $result['c'];
        return $count1;
    }

    public function get_pro_id_by_p_m_m($product_name, $model, $made, $log_info) {
        $sql = "select product_id from " . constant("TABLE_PREFIX") . "product where product_name = '$product_name' and product_model='$model' and product_made='$made'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result == false) {
            return mysql_error();
        }
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $result = $this->db->fetch_assoc($result);
        $count1 = $result['product_id'];
        return $count1;
    }

    public function get_count_by_p_m_m_p($pro, $model1, $made, $pro_id, $log_info) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "product where product_name='$pro' and product_model='$model1' and product_made='$made' and product_id<>" . $pro_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $result = $this->db->fetch_assoc($result);
        $c = $result['c'];
        return $c;
    }

    public function insert_product($pid, $product_name, $model, $made, $tag1, $is_stock, $product_remark, $is_include_component,$is_not_used,$log_info) {
        $sql = "insert into " . constant("TABLE_PREFIX") . "product(product_id,product_name,product_model,product_made,product_tags,is_stock,product_remark,is_include_component,is_not_used) values($pid,'$product_name','$model','$made','$tag1',$is_stock,'$product_remark',$is_include_component,$is_not_used)";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"],0);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function update_product($pro, $model1, $made, $tag1, $is_stock, $product_remark, $pro_id, $is_include_component,$is_not_used,$log_info) {
        $sql = "update " . constant("TABLE_PREFIX") . "product set product_name='$pro' ,product_model='$model1',product_made='$made',product_tags='$tag1',is_stock=$is_stock,product_remark='$product_remark',is_include_component=$is_include_component,is_not_used=$is_not_used where product_id=" . $pro_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }
    
    public function is_include_component($pro_id,$log_info){
        $sql = "select count(*) a from ying_product where is_include_component = 1 and product_id = $pro_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $row = $this->db->fetch_assoc($result);
        if($row){
            $c1 = $row['a'];
            if($c1>0){
                return true;
            }                
        }
        return false;        
    }
    
    

    public function delete_pro($pro_id, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "product where product_id = " . $pro_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function list_pro($filter1, $sort1, $id, $filter_type, $log_info) {        
        return $this->list_pro_all($filter1, $sort1, $id, $filter_type,100, $log_info);
    }
    
    public function list_pro_in($filter1,$size,$log_info){
        $sql = "select a.product_id,a.product_name,a.product_model,a.product_made,a.product_tags,a.product_sort,a.product_remark,concat(concat(b.product_price,'元/'),b.unit) as product_price from "
                . constant("TABLE_PREFIX") . "product a left join ( select * from ".constant("TABLE_PREFIX")."product_price where price_name = '进价') b on a.product_id = b.product_id "
                ."where 1=1 ";
        if (strpos($filter1, " ") != -1) {
            $list1 = strsToArray($filter1);
            if (count($list1) == 2 && $list1[0] != "" && $list1[1] != "") {
                $sql = $sql . " and ((a.product_name like '%$list1[0]%' and a.product_model like '%$list1[1]%') or (a.product_name like '%$list1[0]%' and a.product_made like '%$list1[1]%') )";
            } else {
                $sql = $sql . " and (a.product_name like '%$filter1%' or a.product_model like '%$filter1%' or a.product_made like '%$filter1%'  or a.product_tags like '%$filter1%') ";
            }
        } else {
            $sql = $sql . " and (a.product_name like '%$filter1%' or a.product_model like '%$filter1%' or a.product_made like '%$filter1%'  or a.product_tags like '%$filter1%') ";
        }
        $sql = $sql . " order by CONVERT( a.product_name USING gbk ) COLLATE gbk_chinese_ci ,CONVERT( a.product_made USING gbk ) COLLATE gbk_chinese_ci ,a.product_sort , CONVERT( a.product_model USING gbk ) COLLATE gbk_chinese_ci ";
        if($size==0){
            
        } else {
            $sql = $sql . " limit ".$size;
        }
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
    
    public function list_pro_all($filter1, $sort1, $id, $filter_type,$size, $log_info) {
        $sql = "select product_id,product_name,product_model,product_made,product_tags,product_locations,product_price,product_sort,product_remark from " . constant("TABLE_PREFIX") . "product";
        if ($id != "") {
            $sql = $sql . " where product_id =" . $id;
        } else {
            $sql = $sql . " where 1 =1 ";
        }
        if ($filter1 != "" && $filter_type == "all") {
            if (strpos($filter1, " ") != -1) {
                $list1 = strsToArray($filter1);
                if (count($list1) == 2 && $list1[0] != "" && $list1[1] != "") {
                    $sql = $sql . " and ((product_name like '%$list1[0]%' and product_model like '%$list1[1]%') or (product_name like '%$list1[0]%' and product_made like '%$list1[1]%') )";
                } else {
                    $sql = $sql . " and (product_name like '%$filter1%' or product_model like '%$filter1%' or product_made like '%$filter1%'  or product_tags like '%$filter1%') ";
                }
            } else {
                $sql = $sql . " and (product_name like '%$filter1%' or product_model like '%$filter1%' or product_made like '%$filter1%'  or product_tags like '%$filter1%') ";
            }
        } else if ($filter1 != "" && $filter_type == "name") {
            $sql = $sql . " and (product_name like '%$filter1%' ) ";
        } else if ($filter1 != "" && $filter_type == "name_and_model_made") {
            if (strpos($filter1, " ") != -1) {
                $list1 = strsToArray($filter1);
                if (count($list1) == 2 && $list1[0] != "" && $list1[1] != "") {
                    $sql = $sql . " and ((product_name like '%$list1[0]%' and product_model like '%$list1[1]%') or (product_name like '%$list1[0]%' and product_made like '%$list1[1]%') )";
                } else if (count($list1) == 3 && $list1[0] != "" && $list1[1] != "" && $list1[2] != "") {
                    $sql = $sql . " and ((product_name like '%$list1[0]%' and product_model like '%$list1[1]%' and product_made like '%$list1[2]%') or (product_name like '%$list1[0]%' and product_model like '%$list1[1] "."$list1[2]%')) ";
                } else if (count($list1) == 4 && $list1[0] != "" && $list1[1] != "" && $list1[2] != "" && $list1[3] != "") {
                    $sql = $sql . " and (product_name like '%$list1[0]%' and product_model like '%$list1[1] "."$list1[2]%' and product_made like '%$list1[3]%') ";
                } else {
                    $sql = $sql . " and (product_name like '%$filter1%' or product_model like '%$filter1%' or product_made like '%$filter1%' ) ";
                }
            } else {
                $sql = $sql . " and (product_name like '%$filter1%' or product_model like '%$filter1%' or product_made like '%$filter1%' ) ";
            }
        } else if ($filter1 != "" && $filter_type == "tag") {
            $sql = $sql . " and (product_tags like '%$filter1%' ) ";
        } else if ($filter1 != "" && $filter_type == "model") {
            $sql = $sql . " and (product_model like '%$filter1%' ) ";
        } else if ($filter1 != "" && $filter_type == "made") {
            $sql = $sql . " and (product_made like '%$filter1%' ) ";
        }
        if ($sort1 == '1') {
            $sql = $sql . " order by product_id";
        } else if ($sort1 == '2') {
            $sql = $sql . " order by product_id desc";
        } else if ($sort1 == '3') {
            $sql = $sql . " order by CONVERT( product_name USING gbk ) COLLATE gbk_chinese_ci  ,CONVERT( product_model USING gbk ) COLLATE gbk_chinese_ci   ,CONVERT( product_made USING gbk ) COLLATE gbk_chinese_ci  ";
        } else if ($sort1 == '4') {
            $sql = $sql . " order by CONVERT( product_name USING gbk ) COLLATE gbk_chinese_ci ,CONVERT( product_made USING gbk ) COLLATE gbk_chinese_ci ,CONVERT( product_model USING gbk ) COLLATE gbk_chinese_ci ";
        } else if ($sort1 == '5') {
            $sql = $sql . " order by CONVERT( product_name USING gbk ) COLLATE gbk_chinese_ci ,CONVERT( product_made USING gbk ) COLLATE gbk_chinese_ci ,product_sort , CONVERT( product_model USING gbk ) COLLATE gbk_chinese_ci ";
        }
        if($size==0){
        	
        } else {
        	$sql = $sql . " limit ".$size;
        }
        
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

    public function list_pro_price($filter1, $price_name, $sort1, $id, $log_info) {
        $sql = "select a.product_id,a.product_name,a.product_model,a.product_made,a.product_tags,a.product_locations,b.product_price,a.product_sort,b.unit product_unit from " . constant("TABLE_PREFIX") . "product a left join " . constant("TABLE_PREFIX") . "product_price b on a.product_id = b.product_id and b.price_name = '" . $price_name . "'";
        if ($id != "") {
            $sql = $sql . " where a.product_id =" . $id . "  ";
        } else {
            $sql = $sql . " where 1=1 ";
        }
        if ($filter1 != "") {
            if (strpos($filter1, " ") != -1) {
                $list1 = strsToArray($filter1);
                if (count($list1) == 2 && $list1[0] != "" && $list1[1] != "") {
                    $sql = $sql . " and ((product_name like '%$list1[0]%' and product_model like '%$list1[1]%') or (product_name like '%$list1[0]%' and product_made like '%$list1[1]%') )";
                } else {
                    $sql = $sql . " and (product_name like '%$filter1%' or product_model like '%$filter1%' or product_made like '%$filter1%'  or product_tags like '%$filter1%') ";
                }
            } else {
                $sql = $sql . " and (product_name like '%$filter1%' or product_model like '%$filter1%' or product_made like '%$filter1%'  or product_tags like '%$filter1%') ";
            }
        }
        if ($sort1 == '1') {
            $sql = $sql . " order by a.product_id";
        } else if ($sort1 == '2') {
            $sql = $sql . " order by a.product_id desc";
        } else if ($sort1 == '3') {
            $sql = $sql . " order by CONVERT( a.product_name USING gbk ) COLLATE gbk_chinese_ci  ,CONVERT( a.product_model USING gbk ) COLLATE gbk_chinese_ci   ,CONVERT( a.product_made USING gbk ) COLLATE gbk_chinese_ci  ";
        } else if ($sort1 == '4') {
            $sql = $sql . " order by CONVERT( a.product_name USING gbk ) COLLATE gbk_chinese_ci ,CONVERT( a.product_made USING gbk ) COLLATE gbk_chinese_ci ,CONVERT( a.product_model USING gbk ) COLLATE gbk_chinese_ci ";
        } else if ($sort1 == '5') {
            $sql = $sql . " order by CONVERT( a.product_name USING gbk ) COLLATE gbk_chinese_ci ,CONVERT( a.product_made USING gbk ) COLLATE gbk_chinese_ci ,a.product_sort , CONVERT( a.product_model USING gbk ) COLLATE gbk_chinese_ci ";
        }
        $sql = $sql . " limit 100";
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

    public function list_pro_stock($filter1, $sort1, $id, $show_low,$show_recent,$log_info) {
        $sql = "SELECT a.product_id,a.product_name,a.product_model,a.product_made,a.product_tags,a.product_locations,a.product_price,b.stock_quantity,b.stock_unit,b.stock_money,b.stock_price,b.low_quantity,b.last_upd_date,b.remark from " . constant("TABLE_PREFIX") . "product a INNER JOIN " . constant("TABLE_PREFIX") . "stock b ON a.product_id = b.product_id ";
        if($show_recent=="1"){
            $sql = $sql . " inner join "." ( select distinct product_id from ". constant("TABLE_PREFIX")."sales c inner join ". constant("TABLE_PREFIX")."sales_detail d on c.batch_id = d.batch_id where c.sales_day = curdate()) e on a.product_id  = e.product_id ";            
        }
        if ($id != "") {
            $sql = $sql . " where a.product_id =" . $id;
        } else {
            $sql = $sql . " where 1 =1 ";
        }
        if ($filter1 != "") {
            if (strpos($filter1, " ") != -1) {
                $list1 = strsToArray($filter1);
                if (count($list1) == 2 && $list1[0] != "" && $list1[1] != "") {
                    $sql = $sql . " and ((a.product_name like '%$list1[0]%' and a.product_model like '%$list1[1]%') or (a.product_name like '%$list1[0]%' and a.product_made like '%$list1[1]%')) ";
                } else {
                    $sql = $sql . " and (a.product_name like '%$filter1%' or a.product_model like '%$filter1%' or a.product_made like '%$filter1%'  or a.product_tags like '%$filter1%' or a.product_locations like '%$filter1%') ";
                }
            } else {
                $sql = $sql . " and (a.product_name like '%$filter1%' or a.product_model like '%$filter1%' or a.product_made like '%$filter1%'  or a.product_tags like '%$filter1%' or a.product_locations like '%$filter1%') ";
            }
        }
        if($show_low=="1"){
            $sql = $sql . " and b.stock_quantity < b.low_quantity ";
        }        
        if ($sort1 == '1') {
            $sql = $sql . " order by a.product_id";
        } else if ($sort1 == '2') {
            $sql = $sql . " order by a.product_id desc";
        } else if ($sort1 == '3') {
            $sql = $sql . " order by a.product_name,a.product_model,a.product_made";
        } else if ($sort1 == '4') {
            $sql = $sql . " order by a.product_name,a.product_made,a.product_model";
        } else if ($sort1 == '5') {
            $sql = $sql . " order by a.product_name,a.product_made,a.product_sort,a.product_model";
        }
        $sql = $sql . " limit 100";
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

    public function list_pro_pym($pym, $sort1,$log_info) {
        $sql = "SELECT DISTINCT a.product_id,product_name,product_model,product_made,product_tags,product_locations,product_price,product_sort FROM "
                . " " . constant("TABLE_PREFIX") . "product a "
                . " INNER JOIN (SELECT product_id,pym FROM " . constant("TABLE_PREFIX") . "py WHERE pym LIKE '%$pym%') b "
                . " ON a.product_id = b.product_id ";
          if ($sort1 == '1') {
            $sql = $sql . " order by a.product_id";
        } else if ($sort1 == '2') {
            $sql = $sql . " order by a.product_id desc";
        } else if ($sort1 == '3') {
            $sql = $sql . " order by a.product_name,a.product_model,a.product_made";
        } else if ($sort1 == '4') {
            $sql = $sql . " order by a.product_name,a.product_made,a.product_model";
        } else if ($sort1 == '5') {
            $sql = $sql . " order by a.product_name,a.product_made,a.product_sort,a.product_model";
        }
        $sql = $sql . " limit 100";
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

    public function list_pro_pym_stock($pym, $sort1,$show_low,$show_recent,$log_info) {
        $sql = "SELECT DISTINCT a.product_id,a.product_name,a.product_model,a.product_made,product_tags,product_locations,a.product_price,c.stock_unit,c.stock_quantity,c.stock_money,c.stock_price,c.low_quantity,c.last_upd_date,b.remark FROM "
                . " " . constant("TABLE_PREFIX") . "product a "
                . " INNER JOIN (SELECT product_id,pym FROM " . constant("TABLE_PREFIX") . "py WHERE pym LIKE '%$pym%') b "
                . " ON a.product_id = b.product_id "
                . " INNER JOIN " . constant("TABLE_PREFIX") . "stock c ON a.product_id = c.product_id ";        
        if($show_recent=="1"){
            $sql = $sql . " inner join "." ( select distinct product_id from ". constant("TABLE_PREFIX")."sales c inner join ". constant("TABLE_PREFIX")."sales_detail d on c.batch_id = d.batch_id where c.sales_day = curdate()) e on a.product_id  = e.product_id ";            
        }
        if($show_low=="1"){
            $sql = $sql . " where c.stock_quantity < c.low_quantity ";
        }        
        if ($sort1 == '1') {
            $sql = $sql . " order by a.product_id";
        } else if ($sort1 == '2') {
            $sql = $sql . " order by a.product_id desc";
        } else if ($sort1 == '3') {
            $sql = $sql . " order by a.product_name,a.product_model,a.product_made";
        } else if ($sort1 == '4') {
            $sql = $sql . " order by a.product_name,a.product_made,a.product_model";
        } else if ($sort1 == '5') {
            $sql = $sql . " order by a.product_name,a.product_made,a.product_sort,a.product_model";
        }
        $sql = $sql . " limit 100";
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

    public function update_product_sort($product_id, $product_sort, $log_info) {

        $sql = "update " . constant("TABLE_PREFIX") . "product set product_sort = "
                . $product_sort . " where product_id=" . $product_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $result;
    }

}

?>