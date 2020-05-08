<?php

ini_set("display_errors", "On");

require_once ( 'data/config.php');
require_once ( 'db/mysqli.class.php');
require_once('bean/category.class.php');
require_once("db/db_log.class.php");
require_once ( 'db/db_category.class.php');

require_once ('Pager.php');


$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_category = new db_category($db,$cls_log);

$action = $_GET['action'];

if ($action == 'get_category') {
    $c_id = $_GET['c_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_category->get_category($c_id,$log_info);
    $row_data = $db->fetch_assoc($result);
    if ($row_data != null) {
        
        $ret = json_encode($row_data);
        echo $ret;
    }
    return;
} else if ($action == 'save_category') {
    $c_id = $_POST['c_id'];
    $mode = 'add';
    if ($c_id > 0) {
        $mode = 'edit';
    }
    $c_id_parent = $_POST["c_id_parent"];
    if($c_id_parent==''){
        $c_id_parent = 0;
    }
    $c_name = $_POST['c_name'];
    $c_sort = $_POST['c_sort'];    
    $c_pic = $_POST["c_pic"];
    
    $page_name = $_POST["page_name"];
    
    $bean = new db_category($db,$cls_log);
    $bean->m_c_id = $c_id;
    $bean->m_c_id_parent = $c_id_parent;
    $bean->m_c_sort = $c_sort;
    $bean->m_c_name = $c_name;
    $bean->m_c_pic = $c_pic;
    
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    if($c_id==0){
        $cls_category->insert($bean, $log_info);
    } else {
        $cls_category->update($bean, $log_info);
    }    
    
    echo 'success';
    return;
}  else if($action=='list_category_sel'){
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_category->list_category_all(" where c_id_parent = 0 ",$log_info);
      $row_data = $db->fetch_assoc($result);
      echo "<option value=''></option>";      
    while($row_data!=null){
        echo "<option value='".$row_data['c_id']."'>".$row_data['c_name']."</option>";
      $row_data = $db->fetch_assoc($result);
    }
    return;
}else if($action=='list_category_json'){
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_category->list_category_all(" where c_id_parent = 0 ",$log_info);
      $row_data = $db->fetch_assoc($result);
      $list = array();
      array_push($list, $row_data);
    while($row_data!=null){
        array_push($list,$row_data);
      $row_data = $db->fetch_assoc($result);
    }
    echo json_encode($list);
    return;
}
else if ($action == 'list_category') {
    $page_size = 10;
    $page_id = $_GET["page_id"];
    $filter = trim($_GET['filter']);
    $parent_id = $_GET["parent_id"];
    
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $total = $cls_category->count_category($filter, $parent_id, $log_info);
    $result = $cls_category->list_category($filter, $parent_id, $page_id, $page_size, $log_info);   
    $row_data = $db->fetch_assoc($result);
    echo "<table class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>父分类</th>";
    echo "<th>图片</th>";
    echo "<th>排序</th>";    
    echo "<th>操作</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row_data != null) {
        $c_id = $row_data['c_id'];
        $c_name = $row_data['c_name'];
        $c_id_parent = $row_data["c_id_parent"];
        $c_name_parent = $row_data["c_name_parent"];
        $c_sort = $row_data["c_sort"];        
        $c_pic = $row_data["c_pic"];
        
        echo "<tr>";
             
        echo "<td>$c_id</td>";
        echo "<td>$c_name</td>";
        echo "<td>$c_name_parent</td>";
        $pic = "";
        if($c_pic!=""){
            $pic = "<img src='uploads/$c_pic' style='width:300px;' />";
        }
        echo "<td>$pic</td>";
        echo "<td>$c_sort <a href='javascript:void(0)' onclick='move_up($c_id)'>↑</a> &nbsp;&nbsp;<a href='javascript:void(0)' onclick='move_down($c_id)'>↓</a>       </td>";        
        
        echo "<td><a href='javascript:void(0)' onclick='get_category($c_id)'> 编辑</a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='del_category($c_id)'>删除</a></td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }
    $a = new Pager();
    echo "<td colspan='10'>";
    $a->mypage($total, $page_id, $page_size);
    echo "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
} else if ($action == 'del_category') {
    $d_id = $_GET['d_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);       
    $result = $cls_category->delete($d_id,$log_info);
    echo $result;
    
} else if ($action == 'reorder_category') {
	$move_action = $_GET["move_action"];
	$page_name = $_GET["page_name"];
	$cat_id = $_GET["cat_id"];
	$log_batch_id = $cls_log->get_batch_id();	
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_category->get_category($cat_id,$log_info);
    $row_data = $db->fetch_assoc($result);
    if ($row_data != null) {
        $c_id_parent = $row_data["c_id_parent"];
        $sort_index = 1;
        if($c_id_parent>0){        	
        	$result = $cls_category->get_category($c_id_parent,$log_info);
        	$row_data = $db->fetch_assoc($result);
        	if ($row_data != null) {
        		$sort1 = $row_data["c_sort"];
        		$sort_index = $sort1 * 100 + 1;
        	}        	
		}
        $result = $cls_category->list_category_all(" where c_id_parent = ".$c_id_parent,$log_info);
        $row_data = $db->fetch_assoc($result);     
        $list1 = array();  
        $to_down = false;         
        $ret = "";
        while($row_data != null) {
        	$c_id = $row_data["c_id"];
        	if($to_down){
        		array_push($list1,array('c_id'=>$c_id,'c_sort'=>$sort_index-1));
        		array_push($list1,array('c_id'=>$cat_id,'c_sort'=>$sort_index));
        		$to_down = false;
        		$row_data = $db->fetch_assoc($result);
        		$sort_index++;
        		continue;
        	}
        	if($c_id==$cat_id){
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
        	$cls_category->update_sort($item['c_id'],$item['c_sort'],$log_info); 
        } 
    }
	
	echo "success";
}
?>
