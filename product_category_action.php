<?php

ini_set("display_errors", "On");

require_once ( 'data/config.php');
require_once ( 'db/mysqli.class.php');
require_once('bean/product_category.class.php');
require_once("db/db_log.class.php");
require_once ( 'db/db_product_category.class.php');
require_once ( 'db/db_category.class.php');
require_once ('utils.php');

require_once ('Pager.php');


$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_product_category = new db_product_category($db,$cls_log);
$cls_category = new db_category($db,$cls_log);

$action = $_GET['action'];

if ($action == 'list_pro') {
    $page_size = 100;
    $page_id = $_GET["page_id"];
    $filter = trim($_GET['filter']);
    $category = trim($_GET["category"]);    
    $page_name = $_GET["page_name"];
    $display = $_GET["display"];    
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $total = $cls_product_category->count_product($filter, $category,$display, $log_info);
    $result = $cls_product_category->list_product($filter, $category, $display, $page_id, $page_size, $log_info);   
    $row_data = $db->fetch_assoc($result);
    echo "<table class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>全选<input type='checkbox' id='checkAll' onclick='check_all(this)' /></th>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>规格</th>";
    echo "<th>品牌/产地</th>";
    echo "<th>分类</th>";
    echo "<th>价格</th>";
    echo "<th>标签</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row_data != null) {
        $product_id = $row_data['product_id'];
        $product_name = $row_data['product_name'];
        $product_model = $row_data["product_model"];
        $product_made = $row_data["product_made"];
        $c_name = $row_data["c_name"];
        $product_price = $row_data["product_price"];
        $prodcut_tags = $row_data["product_tags"];      
        
        echo "<tr>";              
        echo "<td><input type='checkbox' value='$product_id' name='chk'/></td>";
        echo "<td>$product_id</td>";
        echo "<td>$product_name</td>";
        echo "<td>$product_model</td>";
        echo "<td>$product_made</td>";
        echo "<td>$c_name</td>";
        echo "<td>$product_price</td>";
        echo "<td>$prodcut_tags</td>";
                
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }
    $a = new Pager();
    echo "<td colspan='8'>";
    $a->mypage($total, $page_id, $page_size);
    echo "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
} else if($action=='list_category_sel'){
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_category->list_category_all("",$log_info);
      $row_data = $db->fetch_assoc($result);
      echo "<option value=''></option>";      
    while($row_data!=null){
        echo "<option value='".$row_data['c_id']."'>".$row_data['c_name']."</option>";
      $row_data = $db->fetch_assoc($result);
    }
    return;
} else if($action=='give_category_to'){
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_category->list_category_all(" where c_id_parent>0 ",$log_info);
      $row_data = $db->fetch_assoc($result);
    while($row_data!=null){
        echo "<option value='".$row_data['c_id']."'>".$row_data['c_name']."</option>";
      $row_data = $db->fetch_assoc($result);
    }
    return;
} else if($action=='change_category_to'){
    $page_name = $_POST["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $pro_id_strs = $_POST['pro_id_strs'];
    $category_id = $_POST['category_id'];
    $pro_id_array = strsToArray($pro_id_strs);
    foreach($pro_id_array as $pro_id){
        $m_product_category = new product_category();
        $m_product_category->m_category_id = $category_id;
        $m_product_category->m_product_id = $pro_id;
        $cls_product_category->insert($m_product_category, $log_info);
    }
    echo "success";
    return;
} else if($action=='cancel_category'){
    $page_name = $_POST["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $pro_id_strs = $_POST['pro_id_strs'];
    $pro_id_array = strsToArray($pro_id_strs);
    foreach($pro_id_array as $pro_id){
        $cls_product_category->delete($pro_id, $log_info);
    }
    echo "success";
    return;
}
?>
