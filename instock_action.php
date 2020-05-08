<?php

require_once ( 'data/config.php');
require ( 'db/mysqli.class.php');
require_once("db/db_log.class.php");
require_once ( 'db/db_instock.class.php');
require_once("db/db_client.class.php");
require_once("db/db_stock.class.php");
require_once("db/db_price.class.php");
require_once("db/db_stock_detail.class.php");
require_once("db/db_big_product_stock.class.php");


require_once ('Pager.php');


$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_instock = new db_instock($db,$cls_log);
$cls_stock = new db_stock($db,$cls_log);
$cls_price = new db_price($db,$cls_log);
$cls_stock_detail = new db_stock_detail($db, $cls_log);
$cls_big_product_stock = new db_big_product_stock($db,$cls_log);

$action = $_GET['action'];
if ($action == 'list_company') {
    $filter1 = $_GET["filter1"];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_instock->get_in_company_filter($filter1,$log_info);
    $row_data = $db->fetch_assoc($result);
    $ret = "<table style='width:6000px;'>";
    $c = 0;
    while ($row_data != null) {
        $ret = $ret . "<tr><td><a href='javascript:void(0);' onclick='selectCompany(\"" .
                $row_data["in_company"] . "\")' >" . $row_data["in_company"] . "</a></td></tr>";
        $row_data = $db->fetch_assoc($result);
        $c = $c + 1;
    }
    $ret = $ret . "</table>";
    if ($c == 0) {
        $ret = "";
    }
    echo $ret;
}
if ($action == 'get_instock') {
    $batch_id = $_GET['batch_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_instock->show_instock($batch_id,$log_info);
    $row_data = $db->fetch_assoc($result);
    if ($row_data != null) {
        $ret = json_encode($row_data);
        echo $ret;
    }
} else if ($action == 'get_instock_detail') {
    $batch_id = $_GET['batch_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_instock->show_detail($batch_id,$log_info);
    $list = array();
    $row_data = $db->fetch_assoc($result);
    while ($row_data != null) {
        array_push($list, $row_data);
        $row_data = $db->fetch_assoc($result);
    }
    $ret = json_encode($list);
    echo $ret;
} else if ($action == 'save_instock') {
    $batch_id = $_POST['batch_id'];
    $mode = 'add';
    if ($batch_id > 0) {
        $mode = 'edit';
    }
    $company_name = $_POST['company_name'];
    $date = $_POST['date'];

    $pro_id_s = $_POST['pro_id'];
    $name_gg_s = $_POST['name_gg'];
    $p_model_s = $_POST['p_model'];
    $p_made_s = $_POST['p_made'];
    $unit_s = $_POST['unit'];
    $quantity_s = $_POST['quantity'];
    $price_s = $_POST['price'];
    $money_s = $_POST['money'];
    $remark_s = $_POST['remark'];
    $remark_t = $_POST['remark_t'];
    $page_name = $_POST["page_name"];


    $hj = $_POST['hj'];


    $name_gg_array = explode(',', $name_gg_s);
    $pro_id_array = explode(',', $pro_id_s);
    $p_model_array = explode(',', $p_model_s);
    $p_made_array = explode(',', $p_made_s);
    $unit_array = explode(',', $unit_s);
    $quantity_array = explode(',', $quantity_s);
    $price_array = explode(',', $price_s);
    $money_array = explode(',', $money_s);
    $remark_array = explode(',', $remark_s);

    $summary = '';

    for ($i = 0; $i < count($name_gg_array); $i++) {
        if ($name_gg_array[$i] != "") {
            if ($summary != "") {
                $summary = $summary . ",";
            }
            $summary = $summary . $name_gg_array[$i];
        }
    }
    $client_no = 0;
    $log_batch_id = $cls_log->get_batch_id();    
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);
    if ($batch_id > 0) {
        $cls_instock->update_instock($batch_id, $hj, $company_name, $date, $remark_t, $summary,$log_info);
    } else {
    	if($summary!=''){
        	$batch_id = $cls_instock->insert_instock($hj, $company_name, $date, $remark_t, $summary,$log_info);
        }
    }
    if ($batch_id > 0) {
        if ($mode == 'edit') {          
            $result = $cls_instock->show_detail($batch_id,$log_info);
            $row_data = $db->fetch_assoc($result);
            $list1 = array();
            $i = 0;
            while ($row_data) {
                $pro_id = $row_data['product_id'];
                $ammount = $row_data['in_quantity'];
                $list1[$i]['product_id'] = $pro_id;
                $list1[$i]['in_quantity'] = -$ammount;
                $list1[$i]['unit'] = $row_data['unit'];
                $row_data = $db->fetch_assoc($result);
                $i++;
            }
            for ($j = 0; $j < $i; $j++) {
                $cls_stock_detail->insert_for_instock($list1[$j]['product_id'], "修改入库", 0, $list1[$j]['in_quantity'], $list1[$j]['unit'], time(), $log_info);
                $cls_stock->update_stock_quantity_2($list1[$j]['product_id'], $list1[$j]['in_quantity'], $list1[$j]['unit'],$log_info);
            }
            $cls_instock->del_instock_detail($batch_id,$log_info);
        }
        for ($i = 0; $i < count($name_gg_array); $i++) {
            if ($name_gg_array[$i] != "") {
                $pro_id = $pro_id_array[$i];
                $name_gg = $name_gg_array[$i];
                $price = $price_array[$i];
                $quantity = $quantity_array[$i];
                $money = $money_array[$i];
                $unit = $unit_array[$i];
                $remark = $remark_array[$i];
                $p_model = $p_model_array[$i];
                $p_made = $p_made_array[$i];
                if ($pro_id == '') {
                    $pro_id = 0;
                }
                $detail_id = $cls_instock->insert_instock_detail($batch_id, $pro_id, $name_gg, $p_model, $p_made, $price, $quantity, $unit, $remark,$log_info);
                $stock_count = $cls_stock->count_stock($pro_id,$log_info);
                $cls_stock_detail->insert_for_instock($pro_id, "修改入库", $detail_id, $quantity, $unit, time(), $log_info);
                if ($stock_count > 0 && $pro_id > 0) {
                    $cls_stock->update_stock_quantity($pro_id, $quantity, $money, $price, $unit,$log_info);
                } else if ($pro_id > 0) {
                    $cls_stock->insert_stock(
                            $pro_id, $name_gg, $p_model, $p_made, $quantity, $money, $unit, $price,0,'',$log_info);
                }
                if ($pro_id > 0  && $price > 0) {
                    $cls_price->update_price2($pro_id, "进价", $price, $unit, 1,$log_info);
                }
            }
        }
    }
    echo 'success';
} else if ($action == 'list_instock') {
    $page_size = 10;
    $page_id = $_GET["page_id"];
    $client_name = trim($_GET['client_name']);
    $filter = trim($_GET['filter']);
    $start_time = $_GET['start_time'];
    $end_time = $_GET['end_time'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $total_2 = $cls_instock->count_instock($client_name, $filter, $start_time, $end_time,$log_info);
    $total=$total_2[0];
    $total_money = $total_2[1];
    $result = $cls_instock->list_instock($page_id, $page_size, $client_name, $filter, $start_time, $end_time,$log_info);
    $row_data = $db->fetch_assoc($result);
    echo "<table class='table table-bordered table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>编号</th>";
    echo "<th>日期</th>";
    echo "<th>金额</th>";
    echo "<th>供应商</th>";
    echo "<th>摘要</th>";
    echo "<th>备注</th>";
    echo "<th>操作</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row_data != null) {
        $batch_id = $row_data['in_batch_id'];
        $sales_day = $row_data['add_date'];
        $sales_money = $row_data['total_money'];
        $client_company = '';
        if ($row_data['in_company'] != null) {
            $client_company = $row_data['in_company'];
        }
        $remark = $row_data['remark'];
        $summary = $row_data['summary'];
        echo "<tr>";
        echo "<td>$batch_id</td>";
        echo "<td>$sales_day</td>";
        echo "<td>$sales_money</td>";
        echo "<td>$client_company</td>";
        echo "<td>$summary</td>";
        echo "<td>$remark</td>";
        echo "<td><a href='javascript:void(0)' onclick='show_detail($batch_id,this)'>详细 </a>"
        ."&nbsp;&nbsp;<a href='instock_print.php?in_batch_id=$batch_id' target='_blank'>打印</a>"
        ."&nbsp;&nbsp; <a href='javascript:void(0)' onclick='get_instock($batch_id)'> 编辑</a>"
        ."&nbsp;&nbsp; <a href='javascript:void(0)' onclick='del_sales($batch_id)'>删除</a>"
        ."</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }
    $a = new Pager();
    echo "<tr><td colspan='2' style='text-align:right;'>合计金额：</td><td colspan='5'>".$total_money."</td></tr>";
    echo "<tr><td colspan='7'>";
    $a->mypage($total, $page_id, $page_size);
    echo "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
} else if ($action == 'del_instock') {
    $batch_id = $_GET['batch_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_instock->show_detail($batch_id,$log_info);
    $row_data = $db->fetch_assoc($result);
    $list1 = array();
    $i = 0;
    while ($row_data != null) {
        $pro_id = $row_data['product_id'];
        $ammount = $row_data['in_quantity'];
        $list1[$i]['product_id'] = $pro_id;
        $list1[$i]['in_quantity'] = -$ammount;
        $list1[$i]['unit'] = $row_data['unit'];
        $row_data = $db->fetch_assoc($result);
        $i++;
    }
    for ($j = 0; $j < $i; $j++) {
        $cls_stock_detail->insert_for_instock($list1[$j]['product_id'], "修改入库", 0, $list1[$j]['in_quantity'], $list1[$j]['unit'], time(), $log_info);
        $cls_stock->update_stock_quantity_2($list1[$j]['product_id'], $list1[$j]['in_quantity'], $list1[$j]['unit'],$log_info);
    }
    $result = $cls_instock->del_instock($batch_id,$log_info);
    echo $result;
} else if ($action == 'show_detail') {
    $batch_id = $_GET['batch_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_instock->show_detail($batch_id,$log_info);
    $row_data = $db->fetch_assoc($result);
    echo "<table class='table table-bordered' id='detail_$batch_id'>";
    echo "<thead>";
    echo "<tr>";
    echo "<td>品名及规格</td><td>单位</td><td>数量</td><td>单价</td><td>金额</td><td>备注</td>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row_data != null) {
        $product_name = $row_data['product_name'];
        $unit = $row_data['unit'];
        $sales_ammount = $row_data['in_quantity'];
        $sales_price = $row_data['in_price'];
        $sales_money = $row_data['in_money'];
        $remark = $row_data['remark'];
        echo "<tr>";
        echo "<td>$product_name</td>";
        echo "<td>$unit</td>";
        echo "<td>$sales_ammount</td>";
        echo "<td>$sales_price</td>";
        echo "<td>$sales_money</td>";
        echo "<td>$remark</td>";
        echo "</tr>";
        $row_data = $db->fetch_assoc($result);
    }
    echo "<tr>";
    echo "<td colspan='6' style='text-align:right;'><a href='javascript:void(0)' onclick='hide_this(this)'>隐藏</a></td>";

    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
}  else if ($action == 'get_last_price_by_product_id') {
    $pro_id = $_GET["product_id"];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_instock->get_last($pro_id, $log_info);
        
    if($result!=false){
         $row_data = $db->fetch_assoc($result);
           if ($row_data != null) {
           $ret = json_encode($row_data);
            echo $ret;
        }
    } else {
        echo "result = false";
    }
   
}  else if ($action == 'get_big_stock_full') {
    $product_id = $_GET["product_id"];
    $instock_batch_id = $_GET["instock_batch_id"];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_big_product_stock->get_full($product_id, $instock_batch_id ,$log_info);
    
    $array_result = array();


    if($result!=false){
      
          $row_data = $db->fetch_assoc($result);
          while($row_data){
              array_push($array_result,$row_data);
              $row_data = $db->fetch_assoc($result);
          }
          $ret = json_encode($array_result);
          echo $ret;
      
    } else {
        echo "result = false";
    }
   
} else if ($action=='count_by_batch_id_and_product_id'){
    $pro_id = $_GET["product_id"];
    $batch_id = $_GET['batch_id'];
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    $result = $cls_big_product_stock->count_by_batch_id_and_product_id($batch_id,$pro_id,$log_info);
    echo $result;
} else if($action=='save_big'){
    $page_name = $_POST["page_name"];
    $b_id = $_POST['b_id'];
    $pro_id = $_POST['pro_id'];
    $b_quantity = $_POST['b_quantity'];
    $b_unit= $_POST['b_unit'];
    $b_save_location = $_POST['b_save_location'];
    $b_state = $_POST['b_state'];
    $batch_id = $_POST['batch_id'];
    $log_batch_id = $cls_log->get_batch_id();
    if($b_id==''){
        $bean = new big_product_stock();
        $bean->m_product_id = $pro_id;
        $bean->m_product_state = $b_state;
        $bean->m_stock_position = $b_save_location;
        $bean->m_quantity = $b_quantity;
        $bean->m_unit = $b_unit;
        $bean->m_instock_batch_id = $batch_id;
        $bean->m_add_date = date('Y-m-d H:i:s',time());
        $bean->m_update_date = date('Y-m-d H:i:s',time());
        $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
        $cls_big_product_stock->insert($bean,$log_info);
        echo "success";
    } else {
        $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
        $bean = $cls_big_product_stock->get($b_id,$log_info);
        if($bean!=null){            
            $bean->m_product_id = $pro_id;
            $bean->m_product_state = $b_state;
            $bean->m_stock_position = $b_save_location;
            $bean->m_quantity = $b_quantity;
            $bean->m_unit = $b_unit;
            $bean->m_instock_batch_id = $batch_id;
            $bean->m_update_date = date('Y-m-d H:i:s',time());
            $cls_big_product_stock->update($bean,$log_info);
            echo "success";
        }
    }

}
?>
