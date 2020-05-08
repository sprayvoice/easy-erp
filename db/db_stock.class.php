<?php

class db_stock {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }

    public function count_stock($product_id, $log_info) {
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "stock where product_id=$product_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
              $row = $this->db->fetch_assoc($result);
        	$c = $row['c'];
            	$this->cls_log->update_log_result($log_id, "success");
            	return $c;
        } else {
            return false;
        }
    }

    public function get_stock($pro_id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "stock where product_id = " . $pro_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result == false) {
            echo mysql_error();
        }
        return $result;
    }

    public function insert_stock(
    $product_id, $product_name, $product_model, $product_made, $stock_quantity, $stock_money, $stock_unit, $stock_price, $low_quantity,$remark,$log_info) {
        $sql = "insert into " . constant("TABLE_PREFIX") . "stock(product_id,product_name,product_model,product_made,stock_quantity,stock_money,stock_unit,stock_price,low_quantity,last_upd_date,remark) "
                . " values($product_id,'$product_name','$product_model','$product_made',$stock_quantity,$stock_money,'$stock_unit',$stock_price,$low_quantity,now(),'$remark')";

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        return $product_id;
    }

    public function insert_stock_2(
    $product_id, $product_name, $product_model, $product_made, $stock_quantity, $stock_unit, $log_info) {
        $sql = "insert into " . constant("TABLE_PREFIX") . "stock(product_id,product_name,product_model,product_made,stock_quantity,stock_money,stock_unit,stock_price,last_upd_date) "
                . " values($product_id,'$product_name','$product_model','$product_made',$stock_quantity,0,'$stock_unit',0,now())";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }

        return $product_id;
    }

    public function update_stock(
    $product_id, $product_name, $product_model, $product_made, $stock_unit, $log_info) {
        $sql = "update " . constant("TABLE_PREFIX") . "stock set product_name='$product_name',product_model='$product_model',product_made='$product_made',stock_unit='$stock_unit',last_upd_date=now() "
                . " where product_id=$product_id ";
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

    public function update_stock_full(
    $product_id, $product_name, $product_model, $product_made, $stock_quantity, $stock_price, $stock_money, $stock_unit, $low_quantity,$remark,$log_info) {
        $c = $this->count_stock($product_id, $log_info);
        if ($c == 0) {
            $product_id = $this->insert_stock($product_id, $product_name, $product_model, $product_made, 
                    $stock_quantity, $stock_money, $stock_unit, $stock_price, $low_quantity,$remark,$log_info);
        } else {
            $sql = "update " . constant("TABLE_PREFIX")
                    . "stock set product_name='$product_name',product_model='$product_model'"
                    . ",product_made='$product_made',stock_quantity=$stock_quantity,"
                    . "stock_price=$stock_price,stock_money=$stock_money,stock_unit='$stock_unit',low_quantity=$low_quantity,last_upd_date=now(),remark='$remark' "
                    . " where product_id=$product_id ";

            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }
            $result = $this->db->query($sql);

            if ($result != false) {
                $this->cls_log->update_log_result($log_id, "success");
            }
        }
        return $product_id;
    }

    public function update_stock_quantity($product_id, $quantity, $money, $price, $unit, $log_info) {
        $quantity = $this->getQuantity($product_id, $quantity, $unit, $log_info);
        if ($quantity == 0) {
            return $product_id;
        }
        if ($quantity > 0) {
            $sql = "update " . constant("TABLE_PREFIX") . "stock set stock_quantity=stock_quantity+$quantity,stock_money=stock_money+$money,stock_price= $price,last_upd_date=now(),stock_unit='$unit' "
                    . " where product_id=$product_id ";
        } else {
            $quantity = -$quantity;
            $money = -$money;
            $sql = "update " . constant("TABLE_PREFIX") . "stock set stock_quantity=stock_quantity-$quantity,stock_money=stock_money-$money,stock_price= $price,stock_unit='$unit' "
                    . " where product_id=$product_id ";
        }
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

    public function getQuantity($product_id, $quantity, $unit, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "stock where product_id=$product_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result1 = $this->db->query($sql);

        if ($result1 != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }

        if ($result1 == false) {
            return 0;
        }
        $row1 = $this->db->fetch_assoc($result1);
        if ($row1 == null) {
            return 0;
        }

        $unit1 = $row1["stock_unit"];
        if ($unit1 == $unit) {
            return $quantity;
        }
        if(($unit=="公斤"||$unit=="kg") && ($unit1=="斤" || $unit1=="市斤")){
            return $quantity*2;
        }
        if(($unit1=="公斤"||$unit1=="kg") && ($unit=="斤" || $unit=="市斤")){
            return $quantity/2;
        }

        $qty1 = 0;
        $qty2 = 0;
        $sql = "select * from " . constant("TABLE_PREFIX") . "product_unit where product_id = $product_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result2 = $this->db->query($sql);

        if ($result2 != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result2 == false) {
            return 0;
        }
		$has_row = false;
        $row2 = $this->db->fetch_assoc($result2);
        while ($row2 != null) {

            if ($row2["unit_name"] == $unit1) {
                $qty1 = $row2["unit_quantity"];
            }
            if ($row2["unit_name"] == $unit) {
                $qty2 = $row2["unit_quantity"];
            }
            $row2 = $this->db->fetch_assoc($result2);
            
            $has_row = true;
        }
        //如果在单位换算表(product_unit)未找到数据，则无换算规格，直接返回这个数
        if($has_row ==false){
        	return $quantity;
        }

        if ($qty1 > 0 && $qty2 > 0) {
            $quantity = $qty1 * $quantity / $qty2;
            return $quantity;
        }

        return 0;
    }

    public function update_stock_quantity_2($product_id, $quantity, $unit, $log_info) {
        if ($quantity == 0) {
            return $product_id;
        }
        $quantity1 = $this->getQuantity($product_id, $quantity, $unit, $log_info);

        if ($quantity1 > 0) {
            $sql = "update " . constant("TABLE_PREFIX") . "stock set stock_quantity=stock_quantity+$quantity1,stock_money=stock_quantity*stock_price,last_upd_date=now() "
                    . " where product_id=$product_id";
        } else {
            $quantity1 = -$quantity1;
            $sql = "update " . constant("TABLE_PREFIX") . "stock set stock_quantity=stock_quantity-$quantity1,stock_money=stock_quantity*stock_price,last_upd_date=now() "
                    . " where product_id=$product_id";
        }
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

    public function update_stock_quantity_22($product_id, $quantity, $unit, $log_info) {
        
        $quantity1 = $this->getQuantity($product_id, $quantity, $unit, $log_info);
        
        $sql = "update " . constant("TABLE_PREFIX") . "stock set stock_quantity=$quantity1,stock_money=stock_quantity*stock_price,last_upd_date=now() "
                    . " where product_id=$product_id";      
      
        $result = $this->db->query($sql);
      
        return $product_id;
    }

    public function update_stock_quantity_3($product_id, $quantity, $log_info) {
        $sql = "update " . constant("TABLE_PREFIX") . "stock set stock_quantity=$quantity,stock_money=stock_quantity*stock_price,last_upd_date=now() "
                . " where product_id=$product_id ";
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
    
    public function update_stock_quantity_32($product_id, $quantity, $log_info) {
        if($quantity>0){
             $sql = "update " . constant("TABLE_PREFIX") . "stock set stock_quantity=stock_quantity+$quantity,stock_money=stock_quantity*stock_price,last_upd_date=now() "
                . " where product_id=$product_id ";
        } else {
            $quantity=-$quantity;
            $sql = "update " . constant("TABLE_PREFIX") . "stock set stock_quantity=stock_quantity-$quantity,stock_money=stock_quantity*stock_price,last_upd_date=now() "
                . " where product_id=$product_id ";
        }        
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

    public function list_instock($page_id, $page_size, $filter1, $log_info) {
        if ($page_id == "") {
            $page_id = 1;
        }
        $limit = $page_size;
        $offset = ($page_id - 1) * $page_size;
        $sql = "select distinct a.* from " . constant("TABLE_PREFIX") . "stock a ";
        if ($filter1 != "") {
            $sql = $sql . " inner join " . constant("TABLE_PREFIX") . "py b on a.product_id = b.product_id ";
            $sql = $sql . " where (a.product_name like '%$filter1%' or a.product_model like '%$filter1%' or a.product_made like '%$filter1%' )";
            $sql = $sql . " or (b.pym like '%$filter1%')  ";
        }

        $sql = $sql . " order by a.product_id desc";
        $sql = $sql . " limit " . $offset . "," . $limit;
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
        return $result;
    }

    public function del_stock($product_id, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "stock where product_id = $product_id";
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
            return "从" . constant("TABLE_PREFIX") . "stock表删除失败";
        }
    }

}

?>