<?php

function strsToArray($strs) {
    $result = array();
    $array = array();
    $strs = str_replace('，', ',', $strs);
    $strs = str_replace("n", ',', $strs);
    $strs = str_replace("rn", ',', $strs);
    $strs = str_replace(' ', ',', $strs);
    $array = explode(',', $strs);
    foreach ($array as $key => $value) {
        if ('' != ($value = trim($value))) {
            $result[] = $value;
        }
    }
    return $result;
}

require_once ( 'data/config.php');
require_once ( 'db/mysqli.class.php');
require_once ( 'db/db_log.class.php');
require_once ( 'db/db_client.class.php');
require_once ( 'filter.php');

$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_client = new db_client($db,$cls_log);

$action = $_GET['action'];

if ($action == 'get_company') {
    $client_no = $_GET['client_no'];

    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_client->get_client_by_id($client_no,$log_info);

    $row = $db->fetch_assoc($result);
    if ($row != null) {
        $ret = json_encode($row);
        echo $ret;
    }
} else if ($action == 'add_company') {
    $company_name = $_POST["client_company"];
    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $client_no = 0;
    $client_no = $cls_client->check_exist_company_exact($company_name,$log_info);
    if($client_no==0){
        $client_addr = $_POST["client_addr"];
        $tax_no = $_POST["tax_no"];
        $bank_name = $_POST["bank_name"];
        $client_phone = $_POST["client_phone"];
        $remark = $_POST["remark"];
        $cls_client->insert_client($company_name, $client_addr, $tax_no, $bank_name, $client_phone, $remark,$log_info);
//    } else {
//        $client_addr = $_POST["client_addr"];
//        $tax_no = $_POST["tax_no"];
//        $bank_name = $_POST["bank_name"];
//        $client_phone = $_POST["client_phone"];
//        $remark = $_POST["remark"];
//        $cls_client->update_client($client_no, $company_name, $client_addr, $tax_no, $bank_name, $client_phone, $remark);
//    }  
        echo "success";
    } else {
        echo "已经存在相同数据";
    }
} else if ($action == 'list_company') {
    $filter1 = trim($_GET['filter1']);
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_client->list_client($filter1,$log_info);
    if ($result == false) {
        echo mysql_error();
    }
    $row_data = $db->fetch_assoc($result);
    echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>单位名称</th>";
    echo "<th>地址</th>";
    echo "<th>税号</th>";
    echo "<th>开户行</th>";
    echo "<th>电话</th>";
    echo "<th>备注</th>";
    echo "<th>操作</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";    
    while ($row_data != null) {
        echo "<tr>";
        echo "<td>" . $row_data['client_no'] . "";        
        echo "</td>";
        echo "<td>" . "" . $row_data['client_company'] . "</td>";
        echo "<td>" . "" . $row_data['client_addr'] . "</td>";
        echo "<td>" . "" . $row_data['tax_no'] . "</td>";
        echo "<td>" . $row_data['bank_name'] . "</td>";
        echo "<td>" . $row_data['client_phone'] . "</td>";
        echo "<td>" . $row_data['remark'] . "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='edit1(";
        echo $row_data['client_no'] . ")'>编辑</a> ";
        echo " <a href='javascript:void(0)' onclick='del1(";
        echo $row_data['client_no'] . ")'>删除</a>";          
        echo "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    echo "</tbody>";
    echo "</table>";
} else if ($action == 'edit_company') {
    $client_no = $_POST["client_no"];
    $company_name = $_POST["client_company"];
    $client_addr = $_POST["client_addr"];
    $tax_no = $_POST["tax_no"];
    $bank_name = $_POST["bank_name"];
    $client_phone = $_POST["client_phone"];
    $remark = $_POST["remark"];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $c = $cls_client->get_client_by_id($client_no,$log_info);
    if ($c == 0) {
        echo "未找到要编辑的单位";
        return;
    }    
    $cls_client->update_client($client_no, $company_name, $client_addr, $tax_no, $bank_name, $client_phone, $remark);

    echo "success";
} else if ($action == 'del_company') {
    $client_no = $_POST['client_no'];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $c = $cls_client->get_count_from_sales($client_no,$log_info);
    if($c>0){
        echo "单位名称已经被使用，无法删除，请先删除或修改关联的销售单据";
    } else {
        $cls_client->delete_client($client_no,$log_info);    
        echo "success";
    }
} 
?>