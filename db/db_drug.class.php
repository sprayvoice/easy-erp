<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db_drug
 *
 * @author Administrator
 */
class db_drug {

    private $db;
    private $cls_log;

    public function __construct($db, $cls_log) {
        $this->db = $db;
        $this->cls_log = $cls_log;
    }
    
     public function get_drug($d_id, $log_info) {
        $sql = "select * from " . constant("TABLE_PREFIX") . "drug where d_id = " . $d_id;
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
    
    public function get_going_expire_drug($log_info) {
        $sql = "select d_name,in_date,expire_date from " . constant("TABLE_PREFIX") . "drug where DATEDIFF(expire_date,now())<60 and DATEDIFF(expire_date,now())>0 and del_flag = 0 ";
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
    
    public function del($id,$log_info){
        $mysql = "update ".constant("TABLE_PREFIX")."drug set del_flag = 1 where d_id = $id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "delete", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function update($m_ying_drug, $log_info) {
        $bean = $m_ying_drug;
        $mysql = "update ".constant("TABLE_PREFIX")."drug set d_name = '$bean->m_d_name',d_model_made = '$bean->m_d_model_made',"
                . "d_remark = '$bean->m_d_remark',in_date = '$bean->m_in_date',expire_date = '$bean->m_expire_date',"
                . "del_flag = $bean->m_del_flag where d_id = $bean->m_d_id";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "update", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
    }

    public function insert($m_ying_drug, $log_info) {
        $bean = $m_ying_drug;
        $mysql = "insert into ".constant("TABLE_PREFIX")."drug(`d_name`,`d_model_made`,`d_remark`,`in_date`,`expire_date`,`del_flag`,`add_date`) "
                . "values('$bean->m_d_name','$bean->m_d_model_made','$bean->m_d_remark','$bean->m_in_date',"
                . "'$bean->m_expire_date',$bean->m_del_flag,now())";
        $log_id = $this->cls_log->insert_log($log_info["log_batch_id"], $log_info["page_name"], $log_info["action_name"], $mysql, "insert", "error", $log_info["user_id"]);
        if ($log_id == false) {
            return false;
        }
        $result = $this->db->query($mysql);
        if ($result != false) {
            $this->cls_log->update_log_result($log_id, "success");
        }
        $mysql = "select @@IDENTITY as id";
        $result = $this->db->query($mysql);
        $row = $this->db->fetch_assoc($result);
        $id = $row['id'];
        return $id;
    }
    
    public function count_drug($filter,$time_type,$start,$end,$del_flag,$log_info){
        $sql = "select count(*) c from " . constant("TABLE_PREFIX") . "drug a where 1=1 ";  
        if(trim($filter)!=""){
            $sql .= " and (d_name like '%$filter%' or d_model_made like '%$filter%')";
        }
        if($start!=""||$end!=""){
            $time = "in_date";
            if($time_type==1){
                $time = "in_date";
            } else if($time_type==2){
                $time = "expire_date";
            } else if($time_type==3){
                $time = "add_date";
            }
            if($start!=""){
                $sql .= " and (".$time . " >= '" .$start . "') ";
            }
            if($end!=""){
                $sql .= " and (".$time . " <= '" .$end . "') ";
            }
        }
        if($del_flag==0){
            //显示全部数据
        } else if($del_flag==1){
            $sql .= " and del_flag=0 ";//显示未删除数据
        }
        
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
        $c = $result['c'];
        return $c;
    }
    
    public function list_drug($filter,$time_type,$start,$end,$del_flag,$page_id, $page_size, $log_info) {
        if ($page_id == "") {
            $page_id = 1;
        }
        $limit = $page_size;
        $offset = ($page_id - 1) * $page_size;
        $sql = "select * from " . constant("TABLE_PREFIX") . "drug a where 1=1 ";  
        if(trim($filter)!=""){
            $sql .= " and (d_name like '%$filter%' or d_model_made like '%$filter%')";
        }
        if($start!=""||$end!=""){
            $time = "in_date";
            if($time_type==1){
                $time = "in_date";
            } else if($time_type==2){
                $time = "expire_date";
            } else if($time_type==3){
                $time = "add_date";
            }
            if($start!=""){
                $sql .= " and (".$time . " >= '" .$start . "') ";
            }
            if($end!=""){
                $sql .= " and (".$time . " <= '" .$end . "') ";
            }
        }
        if($del_flag==0){
            //显示全部数据
        } else if($del_flag==1){
            $sql .= " and del_flag=0 ";//显示未删除数据
        }
        $sql = $sql . " order by a.expire_date";
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

}
