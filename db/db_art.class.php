<?php



ini_set("display_errors", "On");



class db_art {



    private $db;

    private $cls_log;



    public function __construct($db, $cls_log) {

        $this->db = $db;

        $this->cls_log = $cls_log;

    }

    

    public function get_art($art_id, $log_info) {

        $sql = "select art_id,cat_id,art_title,art_content,date_format(add_date,'%Y-%m-%d') add_date,sort_order,summary from " . constant("TABLE_PREFIX") . "art where art_id = " . $art_id;

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);

        if ($log_id == false) {

            return false;

        }

        $result = $this->db->query($sql);

        if ($result == false) {

            echo $this->db->mysql_error();

        }

        if ($result != false) {

            $this->cls_log->update_log_result($log_id, "success");

        }

        return $result;

    }

    

    public function insert($m_art, $log_info) {

        $bean = $m_art;

        $mysql = "insert into " . constant("TABLE_PREFIX") . "art(`cat_id`,`art_title`,`art_content`,`add_date`,`sort_order`,`summary`) values($bean->m_cat_id,'$bean->m_art_title','$bean->m_art_content','$bean->m_add_date',$bean->m_sort_order,'$bean->m_summary')";

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], 

            $log_info["page_name"], $log_info["action_name"],

            $mysql, "insert", "error", $log_info["user_id"]);

        if ($log_id == false) {

            return false;

        }

        $result = $this->db->query($mysql);

        if($result!=false){

            $this->cls_log->update_log_result($log_id, "success");

        }

        $mysql = "select @@IDENTITY as id";

        $result = $this->db->query($mysql);

        $row = $this->db ->fetch_assoc($result );

        $id= $row['id'];

        return $id;

    }

    

    public function update($m_art, $log_info) {

        $bean = $m_art;

        $mysql = "update " . constant("TABLE_PREFIX") 

        . "art set cat_id = $bean->m_cat_id,art_title = '$bean->m_art_title',art_content = '$bean->m_art_content',add_date = '$bean->m_add_date',sort_order = $bean->m_sort_order,summary = '$bean->m_summary' where  art_id = $bean->m_art_id";

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],

            $mysql, "update", "error", $log_info["user_id"]);

        if ($log_id == false) {

            return false;

        }

        $result = $this->db->query($mysql);

        if($result!=false){

            $this->cls_log->update_log_result($log_id, "success");

        }       

    }

    

    public function update_sort($art_id,$sort_order,$log_info){

        $mysql = "update " . constant("TABLE_PREFIX")

        . "art set sort_order = $sort_order where  art_id = $art_id";

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],

            $mysql, "update", "error", $log_info["user_id"]);

        if ($log_id == false) {

            return false;

        }

        $result = $this->db->query($mysql);

        if($result!=false){

            $this->cls_log->update_log_result($log_id, "success");

        }

        return $result;

    }

    

    public function delete( $art_id, $log_info) {        

        $mysql = "delete from " . constant("TABLE_PREFIX") 

        . "art where  art_id = $art_id";

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"],

            $mysql, "delete", "error", $log_info["user_id"]);

        if ($log_id == false) {

            return false;

        }

        $result = $this->db->query($mysql);

        if($result!=false){

            $this->cls_log->update_log_result($log_id, "success");

        }       

        return "success";

    }

    

    public function count_art($filter, $cat_id, $log_info) {

        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "art a where 1=1 ";

        if (trim($filter) != "") {

            $sql .= " and (art_title like '%$filter%' or art_content like '%$filter%')";

        }

        if ($cat_id > 0) {

            $sql .= " and cat_id = $cat_id";

        }

        

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);

        if ($log_id == false) {

            return false;

        }

        $result = $this->db->query($sql);

        if ($result == false) {

            return $this->db->mysql_error();

        }

        

        if ($result != false) {

            $this->cls_log->update_log_result($log_id, "success");

        }

        $result = $this->db->fetch_assoc($result);

        $c = $result['c'];

        return $c;

    }

    

    public function list_art_all($log_info) {

        $sql = "select a.art_id,a.sort_order from " . constant("TABLE_PREFIX") . "art a order by a.sort_order";

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);

        if ($log_id == false) {

            return false;

        }

        $result = $this->db->query($sql);

        if ($result == false) {

            return $this->db->mysql_error();

        }

        

        if ($result != false) {

            $this->cls_log->update_log_result($log_id, "success");

        }

        return $result;

    }

    

    public function list_art($filter, $cat_id,$sort_method,

        $start_time,$end_time, $page_id, $page_size, $log_info) {

        if ($page_id == "") {

            $page_id = 1;
            

        }

        $limit = $page_size;

        $offset = ($page_id - 1) * $page_size;

        $sql = "select a.art_id,a.cat_id,a.art_title,date_format(a.add_date,'%Y-%m-%d') add_date,a.sort_order,a.summary,b.cat_name cat_name from " . constant("TABLE_PREFIX") . "art a inner join ".constant("TABLE_PREFIX")."art_cat b on a.cat_id = b.cat_id where 1=1 ";

        if (trim($filter) != "") {

            $sql .= " and (a.art_title like '%$filter%' or a.art_content like '%$filter%')";

        }

        if ($cat_id > 0) {

            $sql .= " and a.cat_id = $cat_id";

        }

        if($start_time!=""){

            $sql .= " and a.add_date >= '$start_time'";

        }

        if($end_time!=""){

            $sql .= " and a.add_date <= '$end_time'";

        }

        if($sort_method=="date"){

            $sql = $sql . " order by a.add_date desc";

        } else {

            $sql = $sql . " order by a.sort_order desc";

        }

        

        $sql = $sql . " limit " . $offset . "," . $limit;

        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $sql, "select", "error", $log_info["user_id"]);

        if ($log_id == false) {

            return false;

        }

        $result = $this->db->query($sql);

        if ($result == false) {

            return $this->db->mysql_error();

        }

        

        if ($result != false) {

            $this->cls_log->update_log_result($log_id, "success");

        }

        $list = array();

        $row_data = $this->db->fetch_assoc($result);

        while($row_data!=null){

            array_push($list,$row_data);

            $row_data = $this->db->fetch_assoc($result);

        }

        return $list;

    }

    

    

}

