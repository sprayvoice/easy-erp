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
require_once ( 'pinyin.php');
require_once ( 'db/db_product.class.php');
require_once ( 'db/db_py.class.php');
require_once ( 'db/db_stock.class.php');
require_once("db/db_stock_detail.class.php");
require_once ('Pager.php');

$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_py = new db_py($db,$cls_log);
$cls_stock = new db_stock($db,$cls_log);
$cls_stock_detail = new db_stock_detail($db, $cls_log);

$action = $_GET['action'];

if ($action == 'list_detail') {

    echo "<table id='list_tb1'  class='table table-hover'>";
    echo "<thead>";
    echo "<tr>";    
    echo "<th>类型</th>";
    echo "<th>产品编号</th>";
    echo "<th>名称</th>";
    echo "<th>规格</th>";
    echo "<th>品牌/产地</th>";
    echo "<th>变更数量</th>";
    echo "<th>库存原数量</th>";
    echo "<th>变更后数量</th>";
    echo "<th>变更日期</th>";    
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $product_id = trim($_GET["product_id"]);
    $filter1 = trim($_GET['filter1']);
    
    $page_size = 10;
    $page_id = $_GET["page_id"];
    
    $start_time = $_GET['start_time'];
    $end_time = $_GET['end_time'];
    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];    
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $total = $cls_stock_detail->count_stock_detail($product_id,$filter1,$start_time,$end_time, $log_info);
    
    $result = $cls_stock_detail->list_stock_detail($page_id, $page_size, $product_id,$filter1,$start_time,$end_time, $log_info);
    if ($result == false) {
        echo mysql_error();
    }
    $row_data = $db->fetch_assoc($result);
    
    $line_count = 1;
    while ($row_data != null) {
        echo "<tr>";
        echo "<td>". $row_data['action_type'] ."</td>";
        echo "<td>";
        echo "<span style='color:green;font-style:italic;'>" .$line_count."</span>&nbsp;" . $row_data['product_id'] . "";
        $line_count++;
        echo "</td>";        
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_name'] . "\")'>" . $row_data['product_name'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_model'] . "\")'>" . $row_data['product_model'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_made'] . "\")'>" . $row_data['product_made'] . "</a></td>";
        echo "<td>" . $row_data['quantity'] .$row_data['unit'] . "</td>";
        echo "<td>" . $row_data['stock_before_quantity'] .$row_data['stock_before_unit'] . "</td>";
        echo "<td>" . $row_data['stock_quantity'] .$row_data['stock_unit'] . "</td>";
        echo "<td>" . substr($row_data['action_time'],0,10)  . "</td>";        
        echo "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    
    $a = new Pager();
    
    echo "<tr><td colspan='9' style='text-align:center'>";
    $a->mypage($total, $page_id, $page_size);
    echo "</td>";
    
    echo "</tbody>";    
    echo "</table>";
} 
?>