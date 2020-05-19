<?php

ini_set("display_errors", "On");

require_once ( 'data/config.php');

require_once ( 'db/mysqli.class.php');

require_once ( 'db/db_log.class.php');

require_once ( 'bean/basic_info.class.php');

require_once ( 'db/db_basic_info.class.php');

require_once ( 'filter.php');

$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");

$cls_basic_info = new db_basic_info($db);

$action = $_GET['action'];



if ($action == 'get_info') {

    $company_name1 = '';
    $company_name2 = '';
    $company_name3 = '';
    $company_phone = '';
    $company_addr = '';
    
    $result = $cls_basic_info->list_all();

    if($result){
      
        $row = $db->fetch_assoc($result);
        while ($row != null) {
            if($row['key_name']=='company_name1'){
                $company_name1 = $row['key_value'];
            } else if($row['key_name']=='company_name2'){
                $company_name2 = $row['key_value'];
            } else if($row['key_name']=='company_name3'){
                $company_name3 = $row['key_value'];
            } else if($row['key_name']=='company_addr'){
                $company_addr = $row['key_value'];
            } else if($row['key_name']=='company_phone'){
                $company_phone = $row['key_value'];
            }

            $row = $db->fetch_assoc($result);        
        }


        $list1 = array('company_name1'=>$company_name1,'company_name2'=>$company_name2,
        'company_name3'=>$company_name3,
           'company_addr'=>$company_addr, 'company_phone'=>$company_phone);
        

        $ret1 = json_encode($list1);

        

        echo $ret1;

        return;
    }

} else if ($action == 'save_info') {

    $company_name1 = $_POST["company_name1"];
    $company_name2 = $_POST["company_name2"];
    $company_name3 = $_POST["company_name3"];
    $company_addr = $_POST["company_addr"];
    $company_phone = $_POST["company_phone"];

    

    $result = $cls_basic_info->get_by_key_name("company_name1");
    

    if($result->num_rows==0){
        $bean = new basic_info();
        $bean->m_key_name = "company_name1";
        $bean->m_key_value = $company_name1;
        $cls_basic_info->insert($bean);
    } else {
        $bean = new basic_info();
        $row_data = $db->fetch_assoc($result);
        $bean->m_key_name = 'company_name1';
        $bean->m_id = $row_data['id'];
        $bean->m_key_value = $company_name1;
        $cls_basic_info->update($bean);
    }

    $result = $cls_basic_info->get_by_key_name("company_name2");
    if($result->num_rows==0){
        $bean = new basic_info();
        $bean->m_key_name = "company_name2";
        $bean->m_key_value = $company_name2;
        $cls_basic_info->insert($bean);
    } else {
        $bean = new basic_info();
        $row_data = $db->fetch_assoc($result);
        $bean->m_key_name = 'company_name2';
        $bean->m_id = $row_data['id'];
        $bean->m_key_value = $company_name2;
        $cls_basic_info->update($bean);
    }

    $result = $cls_basic_info->get_by_key_name("company_name3");
    if($result->num_rows==0){
        $bean = new basic_info();
        $bean->m_key_name = "company_name3";
        $bean->m_key_value = $company_name3;
        $cls_basic_info->insert($bean);
    } else {
        $bean = new basic_info();
        $row_data = $db->fetch_assoc($result);
        $bean->m_key_name = 'company_name3';
        $bean->m_id = $row_data['id'];
        $bean->m_key_value = $company_name3;
        $cls_basic_info->update($bean);
    }
    $result = $cls_basic_info->get_by_key_name("company_addr");
    if($result->num_rows==0){
        $bean = new basic_info();
        $bean->m_key_name = "company_addr";
        $bean->m_key_value = $company_addr;
        $cls_basic_info->insert($bean);
    } else {
        $bean = new basic_info();
        $row_data = $db->fetch_assoc($result);
        $bean->m_key_name = 'company_addr';
        $bean->m_id = $row_data['id'];
        $bean->m_key_value = $company_addr;
        $cls_basic_info->update($bean);
    }
    $result = $cls_basic_info->get_by_key_name("company_phone");
    if($result->num_rows==0){
        $bean = new basic_info();
        $bean->m_key_name = "company_phone";
        $bean->m_key_value = $company_phone;
        $cls_basic_info->insert($bean);
    } else {
        $bean = new basic_info();
        $row_data = $db->fetch_assoc($result);
        $bean->m_key_name = 'company_phone';
        $bean->m_id = $row_data['id'];
        $bean->m_key_value = $company_phone;
        $cls_basic_info->update($bean);
    }





    echo "success";





} 

?>