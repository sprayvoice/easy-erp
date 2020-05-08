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
require_once ( 'db/db_stock_group.class.php');
require_once ( 'db/db_stock.class.php');
require_once ( 'Pager.php');

require_once ( 'filter.php');

$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();

$log_batch_id = $cls_log->get_batch_id();
$page_name = "stock_group.php";


$cls_stock_group = new db_stock_group($db);

$action = $_GET['action'];

$log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

$cls_pro = new db_product($db,$cls_log);
$cls_stock = new db_stock($db,$cls_log);

if ($action == 'add_group') {
    $ids = $_POST['ids'];
    $name = $_POST['name'];    
    $result = $cls_stock_group->add_group($ids,$name);
    echo "success";
    return;
} else if ($action == 'save_stock_group') {  
    $stock_group_id = $_POST["stock_group_id"];
    $stock_group_name = $_POST["stock_group_name"];
    $cls_stock_group->save_group($stock_group_id,$stock_group_name);
    echo "success";
    return;
} else if ($action == 'add_product_to_group') {  
    $stock_group_id = $_POST["stock_group_id"];
    $product_id = $_POST["product_id"];
    $cls_stock_group->add_product_to_group($stock_group_id,$product_id);
    echo "success";
    return;




} else if ($action == 'get_stock_group') {
    $id = $_GET["id"];
    $result = $cls_stock_group->get_stock_group($id);
    if ($result == false) {
        $ret = array("result"=>'error',"error_msg"=>mysql_error());
        $ret = json_encode($ret);
        echo $ret;
    }
    $row_data = $db->fetch_assoc($result);
    if ($row_data != null) {
        $group_name = $row_data['group_name'];

        $ret = array("result"=>'success',"group_name"=>$group_name);
        $ret = json_encode($ret);
        echo $ret;
    } else {
        $ret = array("result"=>'error',"error_msg"=>'no data');
        $ret = json_encode($ret);
        echo $ret;
    }
    return;
} else if ($action == 'move_up') {
    $group_id =  $_GET["group_id"];    
    $id = $_GET["id"];
    #echo "in_id:".$id;
    #echo "group_id:".$group_id;
    
    $result = $cls_stock_group->list_detail_by_group_id($group_id);
    $row_data = $db->fetch_assoc($result);
    $sort_array = array();
    while ($row_data != null) {
        array_push($sort_array,$row_data['id']);        
        $row_data = $db->fetch_assoc($result);
    }
    #print_r($sort_array);
    $num = count($sort_array);
    for($i=0;$i<$num;$i++){
        if($sort_array[$i]==$id){
            #echo "in";
            if($i>0){
                $tmp = $sort_array[$i-1];
                $sort_array[$i-1] = $sort_array[$i];
                $sort_array[$i] = $tmp;
            }
        }
    }
    $cls_stock_group->update_sort_order($sort_array);
    echo "success";
    return;

} else if ($action == 'move_down') {
    $group_id =  $_GET["group_id"];    
    $id = $_GET["id"];
    #echo "in_id:".$id;
    #echo "group_id:".$group_id;
    
    $result = $cls_stock_group->list_detail_by_group_id($group_id);
    $row_data = $db->fetch_assoc($result);
    $sort_array = array();
    while ($row_data != null) {
        array_push($sort_array,$row_data['id']);        
        $row_data = $db->fetch_assoc($result);
    }
    #print_r($sort_array);
    $num = count($sort_array);
    for($i=0;$i<$num;$i++){
        if($sort_array[$i]==$id){
            #echo "in";
            if($i<$num-1){
                $tmp = $sort_array[$i+1];
                $sort_array[$i+1] = $sort_array[$i];
                $sort_array[$i] = $tmp;
                break;
            }
        }
    }
    #print_r($sort_array);
    $cls_stock_group->update_sort_order($sort_array);
    echo "success";
    return;

} else if ($action == 'move_head') {
    $group_id =  $_GET["group_id"];    
    $id = $_GET["id"];
    #echo "in_id:".$id;
    #echo "group_id:".$group_id;
    
    $result = $cls_stock_group->list_detail_by_group_id($group_id);
    $row_data = $db->fetch_assoc($result);
    $sort_array = array();
    while ($row_data != null) {
        array_push($sort_array,$row_data['id']);        
        $row_data = $db->fetch_assoc($result);
    }
    #print_r($sort_array);
    $num = count($sort_array);
    for($i=0;$i<$num;$i++){
        if($sort_array[$i]==$id){   
            unset($sort_array[$i]);
            array_splice($sort_array, 0, 0, $id);
            break;
        }
    }
    #print_r($sort_array);
    $cls_stock_group->update_sort_order($sort_array);
    echo "success";
    return;

} else if ($action == 'get_stock_group_detail') {
    $id = $_GET["id"];
    $result = $cls_stock_group->list_detail_by_group_id($id);
    if ($result == false) {
        echo "false";
    } else {
        $i_count = 1;
        $row_data = $db->fetch_assoc($result);
        echo "<table id='list_tb2'  class='table table-hover'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>序号</th>";
        echo "<th>产品编号</th>";
        echo "<th>名称</th>";
        echo "<th>规格/型号</th>";
        echo "<th>品牌/产地</th>";
        echo "<th>操作</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row_data != null) {
            
            echo "<tr>";
            echo "<td>".$i_count."</td>";
            echo "<td>"."<input type='hidden' value='".$row_data["id"]."' name='hid_id' />".$row_data["product_id"]."</td>";
            echo "<td>".$row_data["product_name"]."</td>";
            echo "<td>".$row_data["product_model"]."</td>";
            echo "<td>".$row_data["product_made"]."</td>";
            echo "<td><a href='javascript:void(0)' onclick='del1(" . $row_data['id'] . ",this)'>删除</a>/"
                ."<a href='javascript:void(0)' onclick='move_up1(" . $row_data['id'] . ",this)'>上移</a>/"
                ."<a href='javascript:void(0)' onclick='move_head1(" . $row_data['id'] . ",this)'>置顶</a>/"
                ."<a href='javascript:void(0)' onclick='move_down1(" . $row_data['id'] . ",this)'>下移</a>"."</td>";
            echo "</tr>";

            $i_count++;
            $row_data = $db->fetch_assoc($result);
        }
        echo "</tbody>";
        echo "</table>";
    }


} else if ($action == 'show_stock_group_detail') {
    $id = $_GET["id"];
    $result = $cls_stock_group->list_detail_by_group_id($id);
    if ($result == false) {
        echo "false";
    } else {
        $i_count = 1;
        $row_data = $db->fetch_assoc($result);
        echo "<table id='list_tb2'  class='table table-hover'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>序号</th>";
        echo "<th>产品编号</th>";
        echo "<th>名称</th>";
        echo "<th>规格/型号</th>";
        echo "<th>品牌/产地</th>";
        echo "<th>库存</th>";
        echo "<th>单位</th>";
        echo "<th>操作</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row_data != null) {
            
            echo "<tr>";
            echo "<td>".$i_count."</td>";
            echo "<td>"."".$row_data["product_id"]."</td>";
            echo "<td>".$row_data["product_name"]."</td>";
            echo "<td>".$row_data["product_model"]."</td>";
            echo "<td>".$row_data["product_made"]."</td>";
            echo "<td><input type='text' name='stock_qty' value='".$row_data["stock_quantity"]."' style='width:100px;'/></td>";
            echo "<td><input type='text' name='stock_dw' value='".$row_data["stock_unit"]."' style='width:100px;'/></td>";
            echo "<td><a href='javascript:void(0)' onclick='save_stock_qty(" .$id.",". $row_data['product_id'] . ",this)'>保存</a>"
                ."</td>";
            echo "</tr>";

            $i_count++;
            $row_data = $db->fetch_assoc($result);
        }
        echo "<tr><td colspan='8'><input type='button' value='返回' onclick='ret()' /></td></tr>";
        echo "</tbody>";
        echo "</table>";
    }


} else if ($action == 'list_stock_group') {

    echo "<table id='list_tb1'  class='table table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>操作</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $result = $cls_stock_group->list_stock_group();
    if ($result == false) {
        echo mysql_error();
    }
    
    $line_count = 1;
    $row_data = $db->fetch_assoc($result);
    while ($row_data != null) {
        echo "<tr>";  
          
        echo "<td>";
        echo $row_data["group_id"];
        echo "</td>";

        echo "<td>";
        echo $row_data["group_name"];
        echo "</td>";

        echo "<td>";
        echo "<a href='javascript:void(0)' onclick='show_stock_group(" . $row_data['group_id'] . ",this)'>查看及盘点</a> ";
        echo "<a href='stock_group_print.php?group_id=" . $row_data['group_id'] . "' target='_blank'>打印盘点单</a> ";
        echo "<a href='javascript:void(0)' onclick='edit_stock_group(" . $row_data['group_id'] . ",this)'>编辑</a> ";
        echo "<a href='javascript:void(0)' onclick='del_stock_group(" . $row_data['group_id'] . ",this)'>删除</a> ";
        echo "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    echo "</tbody>";
    echo "</table>";
} else if ($action == 'del_group') {
    $id = $_GET["id"];
    $cls_stock_group->del_group_detail($id);
    echo "success";
    return;



} else if($action=="pandian"){
    $id = $_GET["id"];
    $qty = $_GET["qty"];
    $unit = $_GET["unit"];
    $cls_stock->update_stock_quantity_22($id,$qty,$unit,$log_info);
    echo "success";
    return;

}

?>