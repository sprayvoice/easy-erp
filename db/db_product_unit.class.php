<?php

class db_product_unit {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }

    public function get_by_product_id($product_id, $log_info) {
        $unit1 = "";
        $unit2 = "";
        $unit3 = "";
        $q1 = "";
        $q2 = "";
        $q3 = "";
        $sql = "SELECT * FROM " . constant("TABLE_PREFIX") . "product_unit WHERE product_id = $product_id ";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result1 = $this->db->query($sql);
        if ($result1 != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $row = $this->db->fetch_assoc($result1);
        while ($row != null) {
            if ($row["unit_sort"] == 1) {
                $unit1 = $row["unit_name"];
                $q1 = $row["unit_quantity"];
            }
            if ($row["unit_sort"] == 2) {
                $unit2 = $row["unit_name"];
                $q2 = $row["unit_quantity"];
            }
            if ($row["unit_sort"] == 3) {
                $unit3 = $row["unit_name"];
                $q3 = $row["unit_quantity"];
            }
            $row = $this->db->fetch_assoc($result1);
        }
       
        $array = array("unit1" => $unit1, "q1" => $q1, "unit2" => $unit2, "q2" => $q2, "unit3" => $unit3, "q3" => $q3);
        return $array;
    }

    public function add_product_units($prodcut_id, $unit1, $q1, $unit2, $q2, $unit3, $q3, $log_info) {
        $this->add_product_unit($prodcut_id, 1, $unit1, $q1, $log_info);
        $this->add_product_unit($prodcut_id, 2, $unit2, $q2, $log_info);
        $this->add_product_unit($prodcut_id, 3, $unit3, $q3, $log_info);
    }

    public function add_product_unit($product_id, $sort, $unit, $quantity, $log_info) {

        $sql = "select count(*) c1 from " . constant("TABLE_PREFIX") . "product_unit where product_id = "
                . $product_id . " and unit_sort = " . $sort;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }

        $result1 = $this->db->query($sql);


        if ($result1 != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result1 == false) {
            return mysql_error();
        }
        $result = $this->db->fetch_assoc($result1);
        if ($result == false) {
            return mysql_error();
        }
        $c1 = $result['c1'];
        if ($c1 == 0) {
            if ($unit != "") {
                $sql = "insert into " . constant("TABLE_PREFIX") . "product_unit(product_id,unit_name,unit_quantity,unit_sort) "
                        . " values($product_id,'$unit',$quantity,$sort)";

                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
                if ($log_id == false) {
                    return false;
                }
                $result = $this->db->query($sql);
                if ($result != false) {
                    $this->cls_log->update_log_result($log_id, "success");
                }
            }
            return "success";
        } else {
            if ($unit != "") {
                $sql = "update " . constant("TABLE_PREFIX") . "product_unit set unit_name='$unit',unit_quantity=$quantity "
                        . " where product_id=$product_id and unit_sort=$sort";
                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
                if ($log_id == false) {
                    return false;
                }
                $result1 = $this->db->query($sql);
                if ($result1 == false) {
                    return mysql_error();
                }
                if ($result1 != false) {
                    $this->cls_log->update_log_result($log_id, "success");
                }
            } else {
                $sql = "delete from " . constant("TABLE_PREFIX") . "product_unit "
                        . " where product_id=$product_id and unit_sort=$sort";
                $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "delete", "error", $log_info["user_id"]);
                if ($log_id == false) {
                    return false;
                }
                $result2 = $this->db->query($sql);
                if ($result2 == false) {
                    return mysql_error();
                }
                if ($result2 != false) {
                    $this->cls_log->update_log_result($log_id, "success");
                }
            }



            return "success";
        }
    }

}

?>