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
require_once ( 'bean/customer_product.class.php');
require_once ( 'db/db_customer_product.class.php');
require_once ( 'filter.php');

$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_client = new db_client($db,$cls_log);
$cls_customer_product = new db_customer_product($db,$cls_log);

$action = $_GET['action'];
if ($action == 'insert_by_client_no') {
    $client_no = $_GET['client_no'];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);
    $result = $cls_customer_product->gen_by_client_no($client_no,$log_info);
    
    for($i=0;$i<count($result);$i++){
        $row_data = $result[$i];
        $product_id = $row_data["product_id"];
        $product_name = $row_data["product_name"];
        $product_model = $row_data["product_model"];
        $product_made = $row_data["product_made"];
        $sales_price = $row_data["sales_price"];
        $client_company = $row_data["client_company"];
        $unit = $row_data["unit"];
        $cls_customer_product->insert_if_not_exist($client_no,$client_company,
            $product_id,$product_name,$product_model,$product_made,$sales_price,$unit,$log_info);
    }    
    echo "success";
    return;
} else if ($action == 'get_company') {
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
} else if ($action == 'list_all_clients') {
    

    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_customer_product->get_all_clients($log_info);

    for($i=0;$i<count($result);$i++){
        $row_data = $result[$i];
        $client_no = $row_data['client_no'];
        $client_company = $row_data['client_company'];
        echo "<a href='javascript:void(0)' onclick='find_a($client_no)'>$client_company</a>&nbsp;&nbsp;";
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
    echo "<th>操作</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";    
    while ($row_data != null) {
        echo "<tr>";
        echo "<td>" . $row_data['client_no'] . "";        
        echo "</td>";
        echo "<td>" . "" . $row_data['client_company'] . "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='select_client(";
        echo $row_data['client_no'] . ")'>选择</a> ";  
        echo "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    echo "</tbody>";
    echo "</table>";
} else if ($action == 'del_price') {
    
    $id = $_GET['id'];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $cls_customer_product->delete_by_id($id,$log_info);
    
    echo "success";
    
} else if ($action == 'save_price') {
    
    $id = $_POST['id'];
    $price = $_POST['price'];
    $fake_price = $_POST['fake_price'];
    $tax_price = $_POST['tax_price'];
    $fake_tax_price = $_POST['fake_tax_price'];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $cls_customer_product->update_price($id,$price,$fake_price,$tax_price,$fake_tax_price,$log_info);
    
    echo "success";
    
} else if ($action == 'get_by_id') {
    
    $id = $_GET['id'];    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $result = $cls_customer_product->get_by_id($id,$log_info);
    echo json_encode($result);
    return;
    
} else if ($action == 'list_price') {
    
    $client_no = $_GET['client_no'];
    $filter1 = $_GET["filter1"];
    $show_del = $_GET["show_del"];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $result = $cls_customer_product->get_by_client_no($client_no,$filter1,$show_del,$log_info);

    $hideStr = " style='display:none;'";
    
    echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>产品编号</th>";
    echo "<th>产品名称</th>";
    echo "<th>规格/型号</th>";
    echo "<th>产地/品牌</th>";
    echo "<th>价格</th>";    
    if($show_del=="0"){
        echo '<th'.$hideStr.">";        
    } else {
        echo "<th>";
    }
    echo "纸面价格</th>";
    echo "<th>含税价</th>";
    if($show_del=="0"){
        echo '<th'.$hideStr.">";   
    } else {
        echo "<th>";
    }
    echo "纸面含税价</th>";
    echo "<th>单位</th>";
    echo "<th>添加日期</th>";
    if($show_del=="1"){
        echo "<th>删除标志</th>";
        echo "<th>删除日期</th>";
    }
    
    echo "<th>操作</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";    
    for ($i=0;$i<count($result);$i++) {
        $row_data = $result[$i];
        echo "<tr>";
        echo "<td><input type='hidden' name='hid_id0' value='".$row_data['id']."' />" . $row_data['id'] . "</td>";
        echo "<td>" . "" . $row_data['product_id'] . "</td>";
        echo "<td>" . "" . $row_data['product_name'] . "</td>";
        echo "<td>" . "" . $row_data['product_model'] . "</td>";
        echo "<td>" . "" . $row_data['product_made'] . "</td>";
        echo "<td>" . "<input name='price' value='" . $row_data['price'] . "' class='price' /></td>";
        if($show_del=="0"){
            echo "<td" .$hideStr."";
        } else {
            echo '<td>';
        }
        echo  "<input name='fake_price' value='" . $row_data['fake_price'] . "'  class='price'  /></td>";
        echo "<td>" . "<input name='tax_price' value='" . $row_data['tax_price'] . "'  class='price'  /></td>";
        if($show_del=="0"){
            echo "<td" .$hideStr."";               
        } else {
            echo '<td>';
        }
        echo "<input name='fake_tax_price' value='" . $row_data['fake_tax_price'] . "'  class='price'  /></td>";     
        echo "<td>" . "" . $row_data['price_unit'] . "</td>";
        echo "<td>" . "" . substr($row_data['add_date'],0,10) . "</td>";
        if($show_del=="1"){
            echo "<td>" . "" . $row_data['del_flag'] . "</td>";
            echo "<td>" . "" . $row_data['del_date'] . "</td>";
        }
        echo "<td>" . "<a href='javascript:void(0)' onclick='del_price(";
        echo $row_data['id'] . ")'>删除</a> ";
        echo "<a href='javascript:void(0)' onclick='save_price(";
        echo $row_data['id'] . ",this)'>保存</a> ";
        echo "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    echo "</tbody>";
    echo "</table>";
} 
?>