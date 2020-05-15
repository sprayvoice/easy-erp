<?php

class db_basic_info {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function list_all(){
        $sql = "select * from ".constant("TABLE_PREFIX")."basic_info";
        $result = $this->db->query($sql);
        return $result;
    }

    public function get_by_key_name($key_name){
        $sql = "select * from ".constant("TABLE_PREFIX")."basic_info where key_name='".$key_name."'";
        $result = $this->db->query($sql);        
        return $result;
    }

    public function insert($m_ying_basic_info) {
        $bean = $m_ying_basic_info;
        $mysql = "insert into " . constant("TABLE_PREFIX") 
        . "basic_info(`key_name`,`key_value`) values('$bean->m_key_name','$bean->m_key_value')";         
        $result = $this->db->query($mysql);       
        $mysql = "select @@IDENTITY as id";
        $result = $this->db->query($mysql);
        $row = $this->db ->fetch_assoc($result );
        $id= $row['id'];
        return $id;
    }

    

    public function update($m_ying_basic_info) {
        $bean = $m_ying_basic_info;
        $mysql = "update " . constant("TABLE_PREFIX") 
        . "basic_info set key_name = '$bean->m_key_name',key_value = '$bean->m_key_value' where  id = $bean->m_id";        
        $result = $this->db->query($mysql);        
    }

    public function delete( $id) 
    {    
        $mysql = "delete from " . constant("TABLE_PREFIX") . "basic_info where  id = $id";              
        $result = $this->db->query($mysql);        	
	}

}

