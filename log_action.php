<?php

ini_set("display_errors", "On");

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
require_once ( 'db/mysql.class.php');
require_once ( 'db/db_log2.class.php');
require_once ('Pager.php');

require_once ( 'filter.php');

$db = new mysql($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log2($db);
$action = $_GET['action'];
if ($action == 'del_batch') {
    $filter1 = trim($_GET['filter1']);    
    $page_name = $_GET["page_name"];
    $action_name = $_GET["action_name"];
    $sql_type = $_GET["sql_type"];
    $execute_result = $_GET["execute_result"];            
    $log_batch_id = $_GET["log_batch_id"];
    $start_day = $_GET["start_day"];
    $end_day = $_GET["end_day"];    
    $result = $cls_log->del_log($page_name, $action_name, $sql_type, $execute_result, $log_batch_id, $start_day, $end_day);
    if(!$result){
        echo mysql_error();
        return false;
    }
    echo "success";
    
} else if ($action == 'list_log') {
    $page_id = trim($_GET["page_id"]);
    $filter1 = trim($_GET['filter1']);    
    $page_name = $_GET["page_name"];
    $action_name = $_GET["action_name"];
    $sql_type = $_GET["sql_type"];
    $execute_result = $_GET["execute_result"];            
    $log_batch_id = $_GET["log_batch_id"];
    $start_day = $_GET["start_day"];
    $end_day = $_GET["end_day"];    
    $total = $cls_log->get_log_count($page_name, $action_name, $sql_type, $execute_result, $log_batch_id, $start_day, $end_day);
//    echo "<span style='color:red;'>".$total."</span>";
    $result = $cls_log->get_log_list($page_id, 20, $page_name,$action_name,$sql_type,$execute_result,$log_batch_id,$start_day,$end_day);     
    if ($result == false) {
        echo mysql_error();
        return false;
    }
    $row_data = $db->fetch_assoc($result);
    echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>会话ID</th>";
    echo "<th>页面</th>";
    echo "<th>动作</th>";
    echo "<th>sql</th>";
    echo "<th>类型</th>";
    echo "<th>结果</th>";
    echo "<th>日期</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>"; 
    $line_count = 1;
    while ($row_data != null) {
        echo "<tr>";
        echo "<td><span style='color:green;font-style:italic;'>" .$line_count."</span>&nbsp;". $row_data['log_id'] . "";
        $line_count++;        
        echo "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='filter_by_batch_id(\"" . $row_data['log_batch_id'] . "\")'>" . $row_data['log_batch_id'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='filter_by_page_name(\"" . $row_data['page_name'] . "\")'>" . $row_data['page_name'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='filter_by_action_name(\"" . $row_data['action_name'] . "\")'>" . $row_data['action_name'] . "</a></td>";
        echo "<td>" . $row_data['sql_text'] . "</td>";
        echo "<td>" . $row_data['sql_type'] . "</td>";
        echo "<td>" . $row_data['execute_result'] . "</td>";
        echo "<td>" . $row_data['add_date'] . "</td>"; 
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }
    $a = new Pager();    
    echo "<tr><td colspan='7' style='text-align:center'>";
    $a->mypage($total, $page_id, 20);
    echo "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";

    
} 

?>