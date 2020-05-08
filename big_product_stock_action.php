<?php



function convertSavePosition($position_id){
    $str = '';
    if($position_id=='1'){
        $str = '店内';
    } else if($position_id=='2'){
        $str = '包家仓库';
    } else if($position_id=='3'){
        $str = '舜北仓库';
    }
    return $str;
}

function convertProductState($state_id){
    $str = '';
    if($state_id=='1'){
        $str = '在库';
    } else if($state_id=='2'){
        $str = '部分售出';
    } else if($state_id=='3'){
        $str = '售出';
    }
    return $str;
}

require_once ( 'data/config.php');
require_once ( 'db/mysqli.class.php');
require_once ( 'db/db_log.class.php');
require_once ( 'pinyin.php');
require_once ( 'db/db_product.class.php');
require_once ( 'db/db_tag.class.php');
require_once ( 'db/db_py.class.php');
require_once ( 'db/db_instock.class.php');
require_once ( 'db/db_sales.class.php');
require_once ( 'db/db_stock.class.php');
require_once ( 'db/db_product_unit.class.php');
require_once ( 'db/db_big_product_stock.class.php');
require_once ( 'Pager.php');
require_once ( 'utils.php');

require_once ( 'filter.php');

$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_pro = new db_product($db,$cls_log);
$cls_tag = new db_tag($db,$cls_log);
$cls_py = new db_py($db,$cls_log);
$cls_instock = new db_instock($db,$cls_log);
$cls_sales = new db_sales($db,$cls_log);
$cls_stock = new db_stock($db,$cls_log);
$cls_product_unit = new db_product_unit($db,$cls_log);
$cls_big_product_stock = new db_big_product_stock($db,$cls_log);
$action = $_GET['action'];
$pro = "";
$tag1 = "";
$model = "";
$made = "";
$is_stock = "";
$remark = "";
$pym = "";
$is_include_component = "0";
$com_id_str = "";
$is_not_used = "0";
$comp_quantity_str = "";

if ($action == 'get_by_id') {
    $id = $_GET['id'];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $result = $cls_big_product_stock->get_by_id($id,$log_info);
    $array = $db->fetch_array($result);
    $ret = json_encode($array);
    echo $ret;
    
} else if ($action == 'list_big_pro_stock') {
    $page_id = $_GET['page_id'];
    $page_size = $_GET['page_size'];
    $filter1 = trim($_GET['filter1']);    
    $filter1 = str_replace('\'','',$filter1);        
    $stock_state = $_GET['stock_state'];

    $total = $cls_big_product_stock->list_pro_cunt($filter1,$stock_state);    
    $result = $cls_big_product_stock->list_big_pro_stock($filter1,$stock_state,$page_id,$page_size);    
    if ($result == false) {
        echo $db->mysql_error();
        return false;
    }
    $row_data = $db->fetch_assoc($result);
    echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>规格</th>";
    echo "<th>品牌/产地</th>";
    echo "<th>标签</th>";
    echo "<th>状态</th>";
    echo "<th>数量</th>";    
    echo "<th>位置</th>";
    echo "<th>编码</th>";
    echo "<th>操作</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    $line_count = 1;
   
    while ($row_data != null) {
        echo "<tr>";
        echo "<td><span style='color:green;font-style:italic;'>" .$line_count."</span>&nbsp;". $row_data['product_id'] . "";
        $line_count++;        
        echo "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_name'] . "\")'>" . $row_data['product_name'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_model'] . "\")'>" . $row_data['product_model'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_made'] . "\")'>" . $row_data['product_made'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_tags'] . "\")'>" . $row_data['product_tags'] . "</a></td>";
        echo "<td>".convertProductState($row_data['product_state'])."</td>";
        echo "<td>".$row_data['quantity'].$row_data['unit']."</td>";
        echo "<td>".convertSavePosition($row_data['stock_position'])."</td>";
        echo "<td>".$row_data['b_no']."</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='edit1(";
        echo $row_data['id'] . ")'>编辑</a> ";
        echo " <a href='javascript:void(0)' onclick='del1(";
        echo $row_data['id'] . ")'>删除</a>";
     
        echo " " . " <a href='sales_detail_single.php?product_id=".$row_data["product_id"]."' target='_blank'> ";
        echo "销售</a>  <a href='instock_detail_single.php?product_id=".$row_data["product_id"]."' target='_blank'>入库</a> "
                ." <a href='instock_and_sales.php?product_id=".$row_data["product_id"]."' target='_blank'> 综合 </a>";
        
        echo "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }
    if($total>$page_size){
        echo "<tr>";
        echo "<td colspan='10'>";
        $cls_pager = new Pager();        
        $cls_pager->mypage($total,$page_id,$page_size);
        echo "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else if ($action == 'edit_big_pro_stock') {
    $id = $_POST['id'];    
    $stock_state = $_POST['stock_state'];
    $stock_position = $_POST['stock_position'];    
    $unit1 = $_POST["unit1"];
    $quantity1 = $_POST["quantity1"];    
    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
  
    $c = $cls_big_product_stock->update_lite($id,$stock_state,$stock_position, $quantity1, $unit1,$log_info);
    
    echo "success";
} else if ($action == 'add_big_pro_stock') {       
    $product_id = $_POST["product_id"];
    $stock_state = $_POST['stock_state'];
    $stock_position = $_POST['stock_position'];    
    $unit1 = $_POST["unit1"];
    $quantity1 = $_POST["quantity1"];

    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

    $pro_result = $cls_pro->get_product($product_id,$log_info);
    $array = $db->fetch_array($pro_result);
    $bean = new big_product_stock();
    $bean->m_id=0;
    $bean->m_product_id=$product_id;
    $bean->m_product_state=$stock_state;
    $bean->m_stock_position=$stock_position;
    $bean->m_quantity=$quantity1;
    $bean->m_unit=$unit1;
    $bean->m_instock_batch_id=0;
    $bean->m_add_date=get_now();
    $bean->m_update_date = get_now();
    $bean->m_b_no = 0;

    $c = $cls_big_product_stock->insert($bean,$log_info);
    
    echo "success";
} else if ($action == 'del_pro') {
    $id = $_POST['id'];        
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $cls_big_product_stock->delete($id,$log_info);    
    echo "success";
} else if ($action == 'list_pro') {
    
    $sort1 = "4";
    $filter1 = trim($_GET['filter']);
    $filter1 = str_replace('\'','',$filter1);
    $id = "";
    $filter_type = "all";
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $result = $cls_pro->list_pro($filter1, $sort1, $id,$filter_type,$log_info);    
    if ($result == false) {
        echo $db->mysql_error();
        return false;
    }
    $row_data = $db->fetch_assoc($result);
    echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>规格</th>";
    echo "<th>品牌/产地</th>";
    echo "<th>标签</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    if ($row_data == null) {
        $result = $cls_pro->list_pro_pym($filter1,$sort1,$log_info);
        $row_data = $db->fetch_assoc($result);
    }
    $line_count = 1;
    while ($row_data != null) {
        echo "<tr>";
        echo "<td><span style='color:green;font-style:italic;'>" .$line_count."</span>&nbsp;". $row_data['product_id'] . "";
        $line_count++;        
        echo "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='select_it(\"" . $row_data['product_id'] . "\")'>" . $row_data['product_name'] . "</a></td>";
        echo "<td>" . "" . $row_data['product_model'] . "</td>";
        echo "<td>" . "" . $row_data['product_made'] . "</td>";
        echo "<td>" . "" . $row_data['product_tags'] . "</td>";   
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    echo "</tbody>";
    echo "</table>";


}

?>