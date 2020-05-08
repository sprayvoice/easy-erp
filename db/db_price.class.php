<?php

class db_price {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }

    public function delete_by_id($id, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "product_price where price_id = $id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }

        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function get_by_product_id($pro_id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "product_price where product_id=" . $pro_id . " order by price_name";
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

    public function get_by_id($id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "product_price where price_id=" . $id;
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
    
    public function get_by_product_id_and_price_name($company,$product_id,$price_name, $log_info) {
        $sql = "select distinct(unit) from ( ";
        $sql .= "select unit from ". constant("TABLE_PREFIX") . "product_price where product_id = ".$product_id . " ";
        $sql .= "union all ";
        $sql .= "select stock_unit unit from ". constant("TABLE_PREFIX") . "stock where  product_id = ".$product_id . " ";
        $sql .= ") aa ";
        $sql .= "where aa.unit is not null ";
        $sql .= "group by aa.unit";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], 
                    $log_info["page_name"], $log_info["action_name"], 
                    $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
             $this->cls_log->update_log_result($log_id, "success");
         }
        $row = $this->db->fetch_assoc($result);
        $tag_unit = "";
        while($row!=null){
            if($tag_unit!=""){
                $tag_unit .= "|";
            }        
            $tag_unit .= $row["unit"];
            $row = $this->db->fetch_assoc($result);
        }
        //return json_encode(array("unit"=>'11','product_price'=>'22','tag_unit'=>$tag_unit));
        
        if($company!=''){
            $sql = "select * from ". constant("TABLE_PREFIX") . "client where client_company = '$company'";
            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], 
                    $log_info["page_name"], $log_info["action_name"], 
                    $sql, "select", "error", $log_info["user_id"]);
                if ($log_id == false) {
              return false;
          }
          $result = $this->db->query($sql);
          if ($result != false) {
              $this->cls_log->update_log_result($log_id, "success");
          }
          $client_no = 0;
          $row = $this->db->fetch_assoc($result);
            if ($row != null) {
                $client_no = $row["client_no"];
                if($client_no>0){
                    $sql = "select a.sales_price product_price,a.unit from " 
                            . constant("TABLE_PREFIX") . "sales_detail a inner join " 
                            . constant("TABLE_PREFIX") . "sales b on a.batch_id = b.batch_id ";
                    $sql .= "where b.client_no = ".$client_no." and a.product_id = ".$product_id 
                            . " order by a.batch_id desc limit 1";
                    $result = $this->db->query($sql);
                    if ($result != false) {
                        $this->cls_log->update_log_result($log_id, "success");
                    }
                    $row_data = $this->db->fetch_assoc($result);
                    if ($row_data != null) {                        
                       $ret = array("product_price"=>$row_data["product_price"],
                           "unit"=>$row_data["unit"],"tag_unit"=>$tag_unit
                           );                                               
                        $ret = json_encode($ret);
                        return $ret;
                    }                    
                }                                
            }            
        }
        
        $sql = "select * from " . constant("TABLE_PREFIX") . "product_price where product_id=" 
                . $product_id . " and price_name='$price_name'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], 
                $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $row_data = $this->db->fetch_assoc($result);
        if ($row_data != null) {
              $ret = array("product_price"=>$row_data["product_price"],
                           "unit"=>$row_data["unit"],"tag_unit"=>$tag_unit
                           );           
            $ret = json_encode($ret);
            return $ret;
        }
        return json_encode(array("unit"=>'','product_price'=>'','tag_unit'=>$tag_unit));
    }

    public function insert_price($pro_id, $price_name, $price, $unit, $is_hide, $log_info) {
        $sql = "insert into " . constant("TABLE_PREFIX") . "product_price(product_id,price_name,product_price,unit,is_hide) "
                ."values($pro_id,'$price_name',$price,'$unit',$is_hide)";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], 
                $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
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
        return "success";
    }

    public function update_price2($pro_id, $price_name, $price, $unit, $is_hide, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "product_price where product_id=$pro_id and price_name='" 
                . $price_name . "'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], 
                $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $row = $this->db->fetch_assoc($result);
        if ($row != null) {
            $price_id = $row["price_id"];
            $unit1 = $row["unit"];
            if ($price == "") {
                $sql = "delete from " . constant("TABLE_PREFIX") . "product_price where product_id=$pro_id and price_name='" 
                        . $price_name . "'";
                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], 
                        $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
                if ($log_id == false) {
                    return false;
                }
                $result = $this->db->query($sql);
                if ($result != false) {
                    $this->cls_log->update_log_result($log_id, "success");
                }
            } else {
                $this->update_price($price_id, $pro_id, $price_name, $price, $unit, $is_hide, $log_info);
            }
            $this->update_product_price($pro_id, $log_info);
        } else {
            if ($price == "") {
                
            } else {
                $this->insert_price($pro_id, $price_name, $price, $unit, $is_hide, $log_info);
                $this->update_product_price($pro_id, $log_info);
            }
        }
    }

    public function update_price($price_id, $pro_id, $price_name, $price, $unit, $is_hide, $log_info) {
        $sql = "update " . constant("TABLE_PREFIX") . "product_price set price_name='$price_name',product_price=$price,unit='$unit',is_hide=$is_hide where price_id=$price_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function update_product_price($pro_id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "product_price where product_id=$pro_id and is_hide=0 order by price_name";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $row = $this->db->fetch_assoc($result);
        $prices = "";
        $c1 = 1;
        while ($row != null) {
            $price_name = $row['price_name'];
            $product_price = $row['product_price'];
            $unit = $row['unit'];
            if ($c1 > 1) {
                $prices = $prices . ",";
            }
            $prices = $prices . $price_name . ":" . $product_price . "元/" . $unit . "";
            $c1 = $c1 + 1;
            $row = $this->db->fetch_assoc($result);
        }
        $sql = "update " . constant("TABLE_PREFIX") . "product set product_price='$prices' where product_id=$pro_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

}

?>