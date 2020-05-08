<?php

ini_set("display_errors", "On");

require_once ( 'data/config.php');
require_once ( 'db/mysqli.class.php');
require_once('bean/art.class.php');
require_once("db/db_log.class.php");
require_once ( 'db/db_art.class.php');

require_once ('Pager.php');


$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_art = new db_art($db,$cls_log);

$action = $_GET['action'];

if ($action == 'get_art') {
    $art_id = $_GET['art_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_art->get_art($art_id,$log_info);
    $row_data = $db->fetch_assoc($result);
    if ($row_data != null) {
        
        $ret = json_encode($row_data);
        echo $ret;
    }
    return;
} else if ($action == 'save_art') {
    $art_id = $_POST['art_id'];
    
    $mode = 'add';
    if ($art_id > 0) {
        $mode = 'edit';
    }
    $cat_id = $_POST['cat_id'];
    $art_title = $_POST['art_title'];    
    $art_content = $_POST["art_content"];
    $add_date = $_POST["add_date"];
    $sort_order = $_POST["sort_order"];
    $summary = $_POST["summary"];
    
    $page_name = $_POST["page_name"];
    
    $bean = new art();
    $bean->m_art_id = $art_id;
    $bean->m_cat_id = $cat_id;
    $bean->m_art_title = $art_title;
    $bean->m_art_content = $art_content;
    $bean->m_add_date = $add_date;
    $bean->m_sort_order = $sort_order;
    $bean->m_summary = $summary;
    
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    if($art_id==''||$art_id==0){
        $cls_art->insert($bean, $log_info);
    } else {
        $cls_art->update($bean, $log_info);
    }    
    
    echo 'success';
    return;
}  else if ($action == 'list_art') {
    $page_size = 10;
    $page_id = $_GET["page_id"];
    $filter = trim($_GET['filter']);
    $cat_id = $_GET['cat_id'];
    $sort_method = $_GET['sort_method'];
    $start_time = $_GET["start_time"];
    $end_time = $_GET["end_time"];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $total = $cls_art->count_art($filter,$cat_id, $log_info);
    $result = $cls_art->list_art($filter, $cat_id,$sort_method,$start_time,$end_time,
        $page_id, $page_size, $log_info);  
    $row_data = null;
    if($result!=null){
        $row_data = array_shift($result);
    }   
    echo "<table class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>标题</th>";
    echo "<th>摘要</th>";
    echo "<th>分类</th>";
    echo "<th>日期</th>";
    echo "<th>排序</th>";
    echo "<th>操作</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row_data != null) {
        $art_id = $row_data['art_id'];
        $art_title = $row_data['art_title'];
        $summary = $row_data["summary"];
        $cat_name = $row_data["cat_name"];
        $add_date = $row_data["add_date"];
        $sort_order = $row_data["sort_order"];
        
        echo "<tr>";
             
        echo "<td>$art_id</td>";
        echo "<td>$art_title</td>";
        echo "<td>$summary</td>";
        echo "<td>$cat_name</td>";
        echo "<td>$add_date</td>";        
        echo "<td>$sort_order <a href='javascript:void(0)' onclick='move_up($art_id)'>↑</a> &nbsp;&nbsp;<a href='javascript:void(0)' onclick='move_down($art_id)'>↓</a>       </td>";        
        
        echo "<td><a href='javascript:void(0)' onclick='show_art($art_id,this)'>详细</a>&nbsp;&nbsp;<a href='javascript:void(0)' onclick='get_art($art_id)'> 编辑</a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='del_art($art_id)'>删除</a></td>";
        echo "</tr>";
        $row_data = array_shift($result);
    }
    $a = new Pager();
    echo "<td colspan='7'>";
    $a->mypage($total, $page_id, $page_size);
    echo "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
} else if ($action == 'del_art') {
    $art_id = $_GET['art_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);       
    $result = $cls_art->delete($art_id,$log_info);
    echo $result;
    
} else if ($action == 'reorder_art') {
	$move_action = $_GET["move_action"];
	$page_name = $_GET["page_name"];
	$art_id = $_GET["art_id"];
	$log_batch_id = $cls_log->get_batch_id();	
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_art->get_art($art_id,$log_info);
    $row_data = $db->fetch_assoc($result);
    if ($row_data != null) {
        $sort_index = 1;
        $result = $cls_art->list_art_all($log_info);
        $row_data = $db->fetch_assoc($result);     
        $list1 = array();  
        $to_down = false;         
        $ret = "";
        while($row_data != null) {
        	$c_id = $row_data["art_id"];
        	if($to_down){
        		array_push($list1,array('c_id'=>$c_id,'c_sort'=>$sort_index-1));
        		array_push($list1,array('c_id'=>$art_id,'c_sort'=>$sort_index));
        		$to_down = false;
        		$row_data = $db->fetch_assoc($result);
        		$sort_index++;
        		continue;
        	}
        	if($c_id==$art_id){
        		if($move_action=='up'){
        			$ele = array_pop($list1);
        			array_push($list1,array('c_id'=>$c_id,'c_sort'=>$sort_index-1));
        			array_push($list1,array('c_id'=>$ele['c_id'],'c_sort'=>$sort_index));
        		} else {
        			$to_down = true;
        			$sort_index++;
        			$row_data = $db->fetch_assoc($result);
        			continue;
        		}
        	} else {
        		array_push($list1,array('c_id'=>$c_id,'c_sort'=>$sort_index));
        	}     	
        	$sort_index++;
        	
        	$row_data = $db->fetch_assoc($result);
        }
        
        foreach($list1 as $item){
        	$ret .= $item['c_id']."=>".$item['c_sort']."|";
        	$cls_art->update_sort($item['c_id'],$item['c_sort'],$log_info); 
        } 
    }
	
	echo "success";
}
?>
