<?php

class db_tag {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }

    public function list_tag($log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "tag order by CONVERT( tag_name USING gbk ) COLLATE gbk_chinese_ci  ";
        
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

    public function get_next_tag_id($log_info) {
        $sql = "SELECT IFNULL(MAX(tag_id),0)+1 tid FROM " . constant("TABLE_PREFIX") . "tag";
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
        $tid = $result['tid'];
        return $tid;
    }

    public function delete_by_pro_id($pro_id, $log_info) {
        $sql = "delete from " . constant("TABLE_PREFIX") . "product_tag where product_id=$pro_id";

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);

        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function clean_unused_tags($log_info) {
        $sql = "SELECT DISTINCT a.tag_id FROM " . constant("TABLE_PREFIX") . "tag a LEFT JOIN " . constant("TABLE_PREFIX") . "product_tag b ON a.tag_id = b.tag_id WHERE b.tag_id IS NULL";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }

        $result = $this->db->query($sql);


        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $row = $this->db->fetch_assoc($result);
        $c = array();
        while ($row != null) {
            $tag_id = $row['tag_id'];
            $c[] = $tag_id;
            $row = $this->db->fetch_assoc($result);
        }

        for ($i = 0; $i < count($c); $i++) {
            $sql = "delete from " . constant("TABLE_PREFIX") . "tag where tag_id = " . $c[$i];
            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }
            $result = $this->db->query($sql);

            if ($result != false) {
                $this->cls_log->update_log_result($log_id, "success");
            }
        }
    }

    public function merge_tag($to_merge_tag, $merge_to_tag, $log_info) {
        $to_merge_tag_id = $this->get_tag_id_by_tag_name($to_merge_tag, $log_info);
        $merge_to_tag_id = $this->get_tag_id_by_tag_name($merge_to_tag, $log_info);

        if ($to_merge_tag_id != false && $merge_to_tag_id != false) {

            $sql = "select product_id from " . constant("TABLE_PREFIX") . "product_tag where tag_id = " . $to_merge_tag_id;
            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }
            $result = $this->db->query($sql);

            if ($result != false) {
                $this->cls_log->update_log_result($log_id, "success");
            }
            $list1 = array();
            if ($result != false) {
                $row = $this->db->fetch_assoc($result);
                while ($row != null) {
                    array_push($list1, $row["product_id"]);
                    $row = $this->db->fetch_assoc($result);
                }
            }
//                foreach($list1 as $pid){
//                   echo $pid."<br />";
//               }   
//               return;
//                 echo "3";
//               return;
            $sql = "update " . constant("TABLE_PREFIX") . "product_tag set tag_id = "
                    . $merge_to_tag_id . " where tag_id = " . $to_merge_tag_id;

            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }
            $result = $this->db->query($sql);
            if ($result != false) {
                $this->cls_log->update_log_result($log_id, "success");
            }
            $sql = "delete from " . constant("TABLE_PREFIX") . "tag where tag_id = " . $to_merge_tag_id;
            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }
            $result = $this->db->query($sql);
            if ($result != false) {
                $this->cls_log->update_log_result($log_id, "success");
            }
            foreach ($list1 as $pid) {
                $this->update_tag_by_pro_id($pid, $log_info);
            }
        }
        return "success";
    }

    public function update_tag_by_pro_id($product_id, $log_info) {
        $sql = "select a.*,b.tag_name from " . constant("TABLE_PREFIX") . "product_tag a inner join " . constant("TABLE_PREFIX")
                . "tag b on a.tag_id = b.tag_id where a.product_id=$product_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result == false) {
            return;
        }
        $row = $this->db->fetch_assoc($result);
        $str = "";
        while ($row != null) {
            if ($str != "") {
                $str = $str . ",";
            }
            $str .= $row["tag_name"];
            $row = $this->db->fetch_assoc($result);
        }
        //return  $str;
        $sql = "update " . constant("TABLE_PREFIX") . "product set product_tags = '" . $str . "' where product_id = " . $product_id;
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($sql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function get_tag_id_by_tag_name($tag_name, $log_info) {
        $sql = "select tag_id from " . constant("TABLE_PREFIX") . "tag where tag_name = '$tag_name'";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result1 = $this->db->query($sql);

        if ($result1 != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        if ($result1 == false) {
            return false;
        }
        $result = $this->db->fetch_assoc($result1);
        if ($result == null) {
            return false;
        }
        return $result['tag_id'];
    }

    public function add_tag($product_id, $tag_name, $log_info) {
        $sql = "select count(*) c1 from " . constant("TABLE_PREFIX") . "tag where tag_name = '$tag_name'";
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
        $c1 = $result['c1'];
        if ($c1 == 0) {
            $tid = $this->get_next_tag_id($log_info);
            $sql = "insert into " . constant("TABLE_PREFIX") . "tag(tag_id,tag_name) values($tid,'$tag_name')";

            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }
            $result = $this->db->query($sql);
            if ($result != false) {
                $this->cls_log->update_log_result($log_id, "success");
            }
            $sql = "insert into " . constant("TABLE_PREFIX") . "product_tag(product_id,tag_id) values($product_id,$tid)";
            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }
            $result = $this->db->query($sql);

            if ($result != false) {
                $this->cls_log->update_log_result($log_id, "success");
            }
            return "success";
        } else {
            $sql = "select tag_id from " . constant("TABLE_PREFIX") . "tag where tag_name = '$tag_name'";
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
            $tid = $result['tag_id'];
            $sql = "insert into " . constant("TABLE_PREFIX") . "product_tag(product_id,tag_id) values($product_id,$tid)";

            $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "insert", "error", $log_info["user_id"]);
            if ($log_id == false) {
                return false;
            }

            $result = $this->db->query($sql);


            if ($result != false) {
                $this->cls_log->update_log_result($log_id, "success");
            }
            return "success";
        }
    }

}

?>