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
require_once( 'db/db_product_component.class.php');
require_once ( 'db/db_tag.class.php');
require_once ( 'db/db_price.class.php');
require_once ( 'db/db_py.class.php');
require_once ( 'db/db_instock.class.php');
require_once ( 'db/db_sales.class.php');
require_once ( 'db/db_stock.class.php');
require_once ( 'db/db_product_unit.class.php');

require_once ( 'filter.php');

$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_pro = new db_product($db,$cls_log);
$cls_pro_comp = new db_product_component($db,$cls_log);
$cls_tag = new db_tag($db,$cls_log);
$cls_price = new db_price($db,$cls_log);
$cls_py = new db_py($db,$cls_log);
$cls_instock = new db_instock($db,$cls_log);
$cls_sales = new db_sales($db,$cls_log);
$cls_stock = new db_stock($db,$cls_log);
$cls_product_unit = new db_product_unit($db,$cls_log);
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
if ($action == 'add_pro') {
    $pro = $_POST['pro'];
    $tag1 = $_POST['tag'];
    $model = $_POST['model'];
    $made = $_POST['made'];
    $is_stock = $_POST["is_stock"];
    $unit1 = $_POST["unit1"];
    $quantity1 = $_POST["quantity1"];
    $unit2 = $_POST["unit2"];
    $quantity2 = $_POST["quantity2"];
    $unit3 = $_POST["unit3"];
    $quantity3 = $_POST["quantity3"];
    $remark = $_POST["remark"];
    $pym = $_POST["pym"];    
    $is_include_component = $_POST["is_include_component"];
    $is_not_used  = $_POST["is_not_used"];
    $comp_id_str = $_POST["comp_id"];
    $comp_quantity_str = $_POST["comp_quantity"];
}
if ($action == 'add_pro' && $pro == "") {
    echo "商品名称不能为空";
    return;
}
if ($action == 'get_pro') {
    $pro_id = $_GET['pro_id'];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $result = $cls_pro->get_product($pro_id,$log_info);
    $array = $db->fetch_array($result);
    $pym = "";    
    $pym_result = $cls_py->get_by_pro_id($pro_id,$log_info);
    $row_data = $db->fetch_assoc($pym_result);
    while($row_data!=null){
        if($pym!=""){
            $pym = $pym .",";
        }
        $pym = $pym .$row_data["pym"];        
        $row_data = $db->fetch_assoc($pym_result);        
    }
    $array["pym"]=$pym;    
    $ret = json_encode($array);
    echo $ret;
    
} else if ($action == 'get_pro_unit') {
    $pro_id = $_GET['pro_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $result = $cls_product_unit->get_by_product_id($pro_id,$log_info);
    if($result!=null){
        $ret = json_encode($result);
        echo $ret;
    }
} else if ($action == 'get_pro_component') {
    $pro_id = $_GET['pro_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $result = $cls_pro_comp->get_by_product_id($pro_id,$log_info);
    if($result!=null){
        $ret = json_encode($result);
        echo $ret;
    }
} else if ($action == 'update_product_sort') {
    $pro_id = $_GET["product_id"];
    $pro_sort = $_GET["product_sort"];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $cls_pro->update_product_sort($pro_id, $pro_sort,$log_info);
    echo "success";    
} else if ($action == 'add_pro') {
    $page_name = $_POST["page_name"];    
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $count1 = $cls_pro->get_count_by_p_m_m($pro, $model, $made,$log_info);
    if ($count1 == 0) {
        $pid = $cls_pro->get_next_product_id($log_info);
        $cls_pro->insert_product($pid, $pro, $model, $made, $tag1,$is_stock,$remark,$is_include_component,$is_not_used,$log_info);
        $cls_product_unit->add_product_units($pid, $unit1, $quantity1, $unit2, $quantity2, $unit3, $quantity3,$log_info);
        
        $array = strsToArray($pym);               
        $cls_py->insert_py_array($pid, $array,$log_info);
        
        if ($tag1 != "") {
            $array1 = strsToArray($tag1);
            $count1 = count($array1);
            for ($i = 0; $i < $count1; $i++) {
                $t1 = $array1[$i];
                $cls_tag->add_tag($pid, $t1,$log_info);
            }
        }
        $cls_pro_comp->del_by_master_product_id($pid,$log_info);
        if($is_include_component=="1"){
            $comp_id_ar = strsToArray($comp_id_str);
            $comp_quantity_ar = strsToArray($comp_quantity_str);
            $count1 = count($comp_id_ar);
            for($i=0;$i<$count1;$i++){
                $cls_pro_comp->insert($pid, $comp_id_ar[$i], $comp_quantity_ar[$i], $log_info);
            }            
        }
        
        echo "success";
    } else {
        echo "已经存在相同数据";
    }
} else if ($action == 'get_price_by_id') {
    $id = $_GET['id'];

    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $result = $cls_price->get_by_id($id,$log_info);

    if ($result == false) {
        echo mysql_error();
    }
    $row_data = $db->fetch_assoc($result);
    if ($row_data != null) {
        $ret = json_encode($row_data);
        echo $ret;
    } else {
        echo "未找到数据";
    }
} else if ($action == 'get_price_by_pid') {
    $pro_id = $_GET['pro_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $result = $cls_price->get_by_product_id($pro_id,$log_info);
    if ($result == false) {
        echo mysql_error();
    }
    $row_data = $db->fetch_assoc($result);
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>价格</th>";
    echo "<th>操作</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row_data != null) {
        echo "<tr>";
        echo "<td>" . $row_data['price_id'] . "</td>";
        echo "<td>" . $row_data['price_name'] . "</td>";
        echo "<td>" . $row_data['product_price'] . "元/" . $row_data['unit'] . "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='edit_price(";
        echo $row_data['price_id'] . ")'>编辑</a> ";
        echo "<a href='javascript:void(0)' onclick='del_price(";
        echo $row_data['price_id'] . ")'>删除</a></td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }
    echo "<tr>";
    echo "<td colspan='3'></span>";
    echo "<td><a href='javascript:void(0)' onclick='add_price($pro_id )'>新增</a></td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
} else if ($action == 'list_pro') {
    $sort1 = $_GET['sort1'];
    $filter1 = trim($_GET['filter1']);
    $filter1 = str_replace('\'','',$filter1);
    $id = $_GET['id'];
    $filter_type = $_GET["filter_type"];
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
    echo "<th>价格</th>";
    echo "<th>标签</th>";
    echo "<th>操作</th>";
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
        if ($id != "") {
            echo "<input type='button' value='x' onclick='clearId()' />";
        }
        echo "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_name'] . "\")'>" . $row_data['product_name'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_model'] . "\")'>" . $row_data['product_model'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_made'] . "\")'>" . $row_data['product_made'] . "</a></td>";
        echo "<td>" . $row_data['product_price'] . "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_tags'] . "\")'>" . $row_data['product_tags'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='edit1(";
        echo $row_data['product_id'] . ")'>编辑</a> ";
        echo " <a href='javascript:void(0)' onclick='del1(";
        echo $row_data['product_id'] . ")'>删除</a>";
        echo " " . " <a href='javascript:void(0)' onclick='copy1(";
        echo $row_data['product_id'] . ")'>复制</a>"; 
        echo " " . " <a href='javascript:void(0)' onclick='show_price(";
        echo $row_data['product_id'] . ")'>价格</a>";
        echo " " . " <a href='sales_detail_single.php?product_id=".$row_data["product_id"]."' target='_blank'> ";
        echo "销售</a>  <a href='instock_detail_single.php?product_id=".$row_data["product_id"]."' target='_blank'>入库</a> "
                ." <a href='instock_and_sales.php?product_id=".$row_data["product_id"]."' target='_blank'> 综合 </a>"
                ." <a id='stock_".$row_data["product_id"]."' href='javascript:void(0)' onclick='show_stock(".$row_data["product_id"].")' ".">库存</a>";
        
        echo "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    echo "</tbody>";
    echo "</table>";
} else if ($action == 'list_pro_for_sort') {
    $sort1 = $_GET['sort1'];
    $filter1 = trim($_GET['filter1']);
    $filter1 = str_replace('\'','',$filter1);
    $filter_type = $_GET["filter_type"];
    $id = $_GET['id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('log_batch_id'=>$log_batch_id,'page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"]);
    $result = $cls_pro->list_pro($filter1, $sort1, $id,$filter_type,$log_info);
    if ($result == false) {
        echo mysql_error();
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
    echo "<th>价格</th>";
    echo "<th>标签</th>";
    echo "<th>排序</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    if ($row_data == null) {
        $result = $cls_pro->list_pro_pym($filter1,$sort1,$log_info);
        $row_data = $db->fetch_assoc($result);
    }
    while ($row_data != null) {
        echo "<tr>";
        echo "<td>" . $row_data['product_id'] . "";
        if ($id != "") {
            echo "<input type='button' value='x' onclick='clearId()' />";
        }
        echo "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_name'] . "\")'>" . $row_data['product_name'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_model'] . "\")'>" . $row_data['product_model'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_made'] . "\")'>" . $row_data['product_made'] . "</a></td>";
        echo "<td>" . $row_data['product_price'] . "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_tags'] . "\")'>" . $row_data['product_tags'] . "</a></td>";
        echo "<td>" . "<input id='sort_".$row_data['product_id']."' type='text' value='"
                .$row_data['product_sort']
                ."' onchange='change_sort(".$row_data['product_id'].")' style='width:50px;'/>" . "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }
   

    echo "</tbody>";
    echo "</table>";
} else if ($action == 'list_pro_for_price') {
    $sort1 = $_GET['sort1'];
    $price_name = $_GET["price_name"];
    $filter1 = trim($_GET['filter1']);
    $filter1 = str_replace('\'','',$filter1);
    $id = $_GET['id'];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $result = $cls_pro->list_pro_price($filter1,$price_name, $sort1, $id,$log_info);
    if ($result == false) {
        echo mysql_error();
    }
    $row_data = $db->fetch_assoc($result);
    echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>规格</th>";
    echo "<th>品牌/产地</th>";
    echo "<th>价格</th>";
    echo "<th>标签</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    if ($row_data == null) {
        $result = $cls_pro->list_pro_pym($filter1,$sort1,$log_info);
        $row_data = $db->fetch_assoc($result);
    }
     while ($row_data != null) {
        echo "<tr>";
        echo "<td>" . $row_data['product_id'] . "";
        if ($id != "") {
            echo "<input type='button' value='x' onclick='clearId()' />";
        }
        echo "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_name'] . "\")'>" . $row_data['product_name'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_model'] . "\")'>" . $row_data['product_model'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_made'] . "\")'>" . $row_data['product_made'] . "</a></td>";
        echo "<td>" . "<input id='price_".$row_data['product_id']."' type='text' value='"
                .$row_data['product_price']
                ."' onchange='change_price(".$row_data['product_id'].")' style='width:50px;'/>" . "</td>";          
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_tags'] . "\")'>" . $row_data['product_tags'] . "</a></td>";        
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    echo "</tbody>";
    echo "</table>";
} else if ($action == 'list_pro_for_price_show') {
    $sort1 = $_GET['sort1'];
    $price_name = $_GET["price_name"];
    $filter1 = trim($_GET['filter1']);
    $filter1 = str_replace('\'','',$filter1);
    $id = $_GET['id'];
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $result = $cls_pro->list_pro_price($filter1,$price_name, $sort1, $id,$log_info);
    if ($result == false) {
        echo mysql_error();
    }
    $row_data = $db->fetch_assoc($result);
    echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>规格</th>";
    echo "<th>品牌/产地</th>";
    echo "<th>价格</th>";
    echo "<th>标签</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    if ($row_data == null) {
        $result = $cls_pro->list_pro_pym($filter1,$sort1,$log_info);
        $row_data = $db->fetch_assoc($result);
    }
     while ($row_data != null) {
        echo "<tr>";
        echo "<td>" . $row_data['product_id'] . "";
        if ($id != "") {
            echo "<input type='button' value='x' onclick='clearId()' />";
        }
        echo "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_name'] . "\")'>" . $row_data['product_name'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_model'] . "\")'>" . $row_data['product_model'] . "</a></td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_made'] . "\")'>" . $row_data['product_made'] . "</a></td>";
        echo "<td>" . $row_data['product_price'] ."/".$row_data['product_unit']. "</td>";          
        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_tags'] . "\")'>" . $row_data['product_tags'] . "</a></td>";        
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    echo "</tbody>";
    echo "</table>";
} else if ($action == 'list_pro_for_instock') {
    $sort1 = '5';
    $filter1 = trim($_GET['filter1']);
    $filter1 = str_replace('\'','',$filter1);
    $id = '';
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $result = $cls_pro->list_pro($filter1, $sort1, $id,"name_and_model_made",$log_info);
    if ($result == false) {
        echo mysql_error();
    }
    $row_data = $db->fetch_assoc($result);
    if ($row_data == null) {
        $result = $cls_pro->list_pro_pym($filter1,$sort1,$log_info);
        $row_data = $db->fetch_assoc($result);
    }
    if ($row_data == null) {
        return;
    }
    echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>规格</th>";
    echo "<th>品牌/产地</th>";
    echo "<th>价格</th>";
    echo "<th>操作<input type='button' value='x' onclick='hideme()' /></th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row_data != null) {
        echo "<tr>";
        echo "<td>" . $row_data['product_id'] . " <a href='javascript:void(0)' onclick='selectone(".$row_data['product_id']. ")'>选择</a> " ;

        echo "</td>";
        echo "<td>" . "" . $row_data['product_name'] . "</td>";
        echo "<td>" . "" . $row_data['product_model'] . "</td>";
        echo "<td>" . "" . $row_data['product_made'] . "</td>";
        echo "<td>" . "" . $row_data['product_price'] . "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='selectone(";
        echo $row_data['product_id'] . ")'>选择</a> ";
        echo "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }

    echo "</tbody>";
    echo "</table>";
} else if ($action == 'list_pro_for_instock_1') {
    $sort1 = '5';
    $filter1 = trim($_GET['filter1']);
    $filter1 = str_replace('\'','',$filter1);
    $id = '';
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);
    $result = $cls_pro->list_pro_in($filter1, 100,$log_info);
    if ($result == false) {
        echo mysql_error();
    }
    $row_data = $db->fetch_assoc($result);
    if ($row_data == null) {
        $result = $cls_pro->list_pro_pym($filter1,$sort1,$log_info);
        $row_data = $db->fetch_assoc($result);
    }
    if ($row_data == null) {
        return;
    }
    echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>名称</th>";
    echo "<th>规格</th>";
    echo "<th>品牌/产地</th>";
    echo "<th>价格</th>";
    echo "<th>操作<input type='button' value='x' onclick='hideme()' /></th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    while ($row_data != null) {
        echo "<tr>";
        echo "<td>" . $row_data['product_id'] . " <a href='javascript:void(0)' onclick='selectone(".$row_data['product_id']. ")'>选择</a> " ;
        
        echo "</td>";
        echo "<td>" . "" . $row_data['product_name'] . "</td>";
        echo "<td>" . "" . $row_data['product_model'] . "</td>";
        echo "<td>" . "" . $row_data['product_made'] . "</td>";
        echo "<td>" . "" . $row_data['product_price'] . "</td>";
        echo "<td>" . "<a href='javascript:void(0)' onclick='selectone(";
        echo $row_data['product_id'] . ")'>选择</a> ";
        echo "</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }
    
    echo "</tbody>";
    echo "</table>";
} else if ($action == 'edit_pro') {
    $pro = $_POST['pro'];
    $pro_id = $_POST['product_id'];
    $tag1 = $_POST['tag'];
    $model1 = $_POST['model'];
    $made = $_POST['made'];
    $is_stock = $_POST["is_stock"];
    $unit1 = $_POST["unit1"];
    $quantity1 = $_POST["quantity1"];
    $unit2 = $_POST["unit2"];
    $quantity2 = $_POST["quantity2"];
    $unit3 = $_POST["unit3"];
    $quantity3 = $_POST["quantity3"];
    $remark = $_POST["remark"];
    $pym = $_POST["pym"];    
    $is_include_component = $_POST["is_include_component"];
    $is_not_used = $_POST["is_not_used"];
    $comp_id_str = $_POST["comp_id"];
    $comp_quantity_str = $_POST["comp_quantity"];
    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $c = $cls_pro->get_count_by_product_id($pro_id,$log_info);
    if ($c == 0) {
        echo "未找到要编辑的产品";
        return;
    }
    $c = $cls_pro->get_count_by_p_m_m_p($pro, $model1, $made, $pro_id,$log_info);
    if ($c > 0) {
        echo "名称为" . $pro . ",规格为" . $model1 . ",品牌/产地为" . $made . "的产品已经存在";
        return;
    }
    $cls_pro->update_product($pro, $model1, $made, $tag1, $is_stock,$remark,$pro_id,$is_include_component,$is_not_used,$log_info);
    $cls_product_unit->add_product_units($pro_id, $unit1, $quantity1, $unit2, $quantity2, $unit3, $quantity3,$log_info);
    $cls_tag->delete_by_pro_id($pro_id,$log_info);

    $array = strsToArray($pym);
               
    $cls_py->insert_py_array($pro_id, $array,$log_info);        

    if ($tag1 != "") {
        $array1 = strsToArray($tag1);
        $count1 = count($array1);
        for ($i = 0; $i < $count1; $i++) {
            $t1 = $array1[$i];
            $cls_tag->add_tag($pro_id, $t1,$log_info);
        }
    }
    $cls_tag->clean_unused_tags($log_info);
    
     $cls_pro_comp->del_by_master_product_id($pro_id,$log_info);
    if($is_include_component=="1"){
        $comp_id_ar = strsToArray($comp_id_str);
        $comp_quantity_ar = strsToArray($comp_quantity_str);
        $count1 = count($comp_id_ar);
        for($i=0;$i<$count1;$i++){
            $cls_pro_comp->insert($pro_id, $comp_id_ar[$i], $comp_quantity_ar[$i], $log_info);
        }            
    }

    echo "success";
} else if ($action == 'del_pro') {
    $pro_id = $_POST['pro_id'];        
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $cls_pro->delete_pro($pro_id,$log_info);
    $cls_tag->delete_by_pro_id($pro_id,$log_info);
    $cls_tag->clean_unused_tags($log_info);
    $cls_py->delete_by_pro_id($pro_id,$log_info);
    $cls_pro_comp->del_by_master_product_id($pro_id,$log_info);
    echo "success";
} else if ($action == 'del_price_by_id') {
    $id = $_GET['id'];    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $cls_price->delete_by_id($id,$log_info);
    echo "success";
} else if ($action == 'save_product_price') {
    $id = $_POST['id'];
    $pro_id = $_POST['pro_id'];
    $price_name = $_POST['price_name'];
    $price = $_POST['price'];
    $unit = $_POST['unit'];
    $is_hide = $_POST['is_hide'];    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    if ($id == '') {
        $result = $cls_price->insert_price($pro_id, $price_name, $price, $unit, $is_hide,$log_info);
        if ($result != "success") {
            echo $result;
        }
        $cls_price->update_product_price($pro_id,$log_info);
        echo "success";
    } else {
        $cls_price->update_price($id, $pro_id, $price_name, $price, $unit, $is_hide,$log_info);
        $cls_price->update_product_price($pro_id,$log_info);
        echo "success";
    }
} else if ($action == 'update_product_price') {
    $pro_id = $_POST['pro_id'];
    $price_name = $_POST['price_name'];
    $price = $_POST['price'];
    $unit = $_POST['unit'];
    $is_hide = $_POST['is_hide'];    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $cls_price->update_price2($pro_id, $price_name, $price, $unit, $is_hide,$log_info);
    echo "success";    
} else if ($action == 'list_tags') {        
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_GET["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_tag->list_tag($log_info);
    $row = $db->fetch_assoc($result);
    while ($row != null) {
        $tag_name = $row['tag_name'];
        echo " <a href='javascript:void(0)' onclick='fill_tag(\"" . $tag_name . "\")'>$tag_name</a> &nbsp;";
        $row = $db->fetch_assoc($result);
    }
} else if($action=="merge_pro"){
    $to_merge_pro = $_POST["to_merge_pro"];
    $merge_to_pro = $_POST["merge_to_pro"];    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $result = $cls_pro->get_product($merge_to_pro,$log_info);
     $row = $db->fetch_assoc($result);
     if ($row != null) {
         $product_id = $row["product_id"];
         $product_name = $row["product_name"];
         $product_model = $row["product_model"];
         $product_made = $row["product_made"];
         $cls_instock->update_instock_detail_for_merge($product_id, $product_name, $product_model, $product_made, $to_merge_pro,$log_info);
         $cls_pro->delete_pro($to_merge_pro,$log_info);
         $cls_tag->delete_by_pro_id($to_merge_pro,$log_info);
         $cls_py->delete_by_pro_id($to_merge_pro,$log_info);
         $cls_sales->update_sales_detail_for_merge($product_id, $product_name, $product_model, $product_made, $to_merge_pro,$log_info);
         $cls_stock->del_stock($to_merge_pro,$log_info);
         echo "success";
         
     }
}
else if($action=="merge_tag"){
    $to_merge_tag = $_POST["to_merge_tag"];
    $merge_to_tag = $_POST["merge_to_tag"];    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $result = $cls_tag->merge_tag($to_merge_tag, $merge_to_tag,$log_info);
     echo $result;
} else if($action=="get_pym"){
    $array = array();
    $pro= $_POST["pro"];
    $model= $_POST["model"];
    $made= $_POST["made"];
    $tag= $_POST["tag"];    
    $log_batch_id = $cls_log->get_batch_id();
    $page_name = $_POST["page_name"];
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
    $array = $cls_py->add_to_array($array,  pinyin($pro));
    if($model!=""){
        $array = $cls_py->add_to_array($array,  pinyin($model));
    }
    if($made!=""){
        $array = $cls_py->add_to_array($array,  pinyin($made));
    }
    if ($tag != "") {
        $array1 = strsToArray($tag);
        $count1 = count($array1);
        for ($i = 0; $i < $count1; $i++) {
            $t1 = $array1[$i];                
            $array = $cls_py->add_to_array($array,  pinyin($t1));                
        }
    }
    $count1 = count($array);
    for ($i = 0; $i < $count1; $i++) {
        $t1 = $array[$i];
        if($i>0){
            echo ",";
        }
        echo $t1;
    }
} else if ($action=="get_stock"){
      $pro_id = $_GET['pro_id'];
      $log_batch_id = $cls_log->get_batch_id();
      $page_name = $_GET["page_name"];
      $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
      $row = $cls_stock->get_stock($pro_id,  $log_info);
      if($row!=null){
          $result = $db->fetch_assoc($row);
          if($result!=null){
              echo $result["stock_quantity"].$result["stock_unit"];
          }
      }
      
}


?>