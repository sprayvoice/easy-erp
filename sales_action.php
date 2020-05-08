<?php

ini_set("display_errors", "On");



require_once ( 'data/config.php');

require_once ( 'db/mysqli.class.php');

require_once ( 'db/db_log.class.php');

require_once ( 'db/db_log2.class.php');

require_once ( 'db/db_sales.class.php');

require_once("db/db_client.class.php");

require_once("db/db_stock.class.php");

require_once("db/db_product.class.php");

require_once("db/db_price.class.php");

require_once("db/db_product_component.class.php");

require_once("db/db_stock_detail.class.php");

require_once ('Pager.php');





$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");

$cls_log = new db_log();

$cls_sales = new db_sales($db,$cls_log);

$cls_client = new db_client($db,$cls_log);

$cls_pro = new db_product($db,$cls_log);

$cls_component = new db_product_component($db,$cls_log);

$cls_stock = new db_stock($db,$cls_log);

$cls_stock_detail = new db_stock_detail($db, $cls_log);

$cls_price = new db_price($db,$cls_log);

$action = $_GET['action'];

if ($action == 'list_company') {

    $filter1 = trim($_GET["filter1"]);

    

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

    

    $result = $cls_client->get_client_company_filter($filter1,$log_info);

    $row_data = $db->fetch_assoc($result);

    $ret = "<table style='width:600px;'>";

    $c = 0;

    while ($row_data != null) {

        $ret = $ret . "<tr><td><a href='javascript:void(0);' onclick='selectCompany(\"" .

                $row_data["client_company"] . "\")' >" . $row_data["client_company"] . "</a></td></tr>";

        $row_data = $db->fetch_assoc($result);

        $c = $c + 1;

    }

    $ret = $ret . "</table>";

    if ($c == 0) {

        $ret = "";

    }

    echo $ret;

} else if ($action == 'get_sales') {

    $batch_id = $_GET['batch_id'];

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

    

    $result = $cls_sales->show_sales($batch_id,$log_info);

    $row_data = $db->fetch_assoc($result);

    if ($row_data != null) {

        $ret = json_encode($row_data);

        echo $ret;

    }

    $cls_log2 = new db_log2($db);

    $cls_log2->del_log("","","","success",0,"",date("Y-m-d",strtotime("-3 day")));

} else if ($action == 'get_sales_detail') {

    $batch_id = $_GET['batch_id'];

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

    $result = $cls_sales->show_detail($batch_id,$log_info);

    $list = array();

    $row_data = $db->fetch_assoc($result);

    while ($row_data != null) {

        array_push($list, $row_data);

        $row_data = $db->fetch_assoc($result);

    }

    $ret = json_encode($list);

    echo $ret;

} else if ($action == 'save_sales') {

    $batch_id = $_POST['batch_id'];

    

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_POST["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

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

    $money_real_s = $_POST['money_real'];

    $remark_s = $_POST['remark'];

    $remark_t = $_POST['remark_t'];



    $hj = $_POST['hj'];

    $hj_real = $_POST['hj_real'];



    $name_gg_array = explode(',', $name_gg_s);

    $pro_id_array = explode(',', $pro_id_s);

    $p_model_array = explode(',', $p_model_s);

    $p_made_array = explode(',', $p_made_s);

    $unit_array = explode(',', $unit_s);

    $quantity_array = explode(',', $quantity_s);

    $price_array = explode(',', $price_s);

    $money_array = explode(',', $money_s);

    $money_real_array = explode(',', $money_real_s);

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



    $client_no = $cls_client->check_exist_company_exact($company_name,$log_info);

    if ($client_no == 0) {

        $client_company = $company_name;

        $client_addr = "";

        $tax_no = "";

        $bank_name = "";

        $client_phone = "";

        $remark = "";

        $client_no = $cls_client->insert_client($client_company, $client_addr, $tax_no, $bank_name, $client_phone, $remark,$log_info);

    }

    if ($batch_id > 0) {

        $cls_sales->update_sales($batch_id, $hj, $hj_real, $date, $client_no, $remark_t, $summary,$log_info);        

    } else {

        $batch_id = $cls_sales->insert_sales($hj, $hj_real, $date, $client_no, $remark_t, $summary,$log_info);

    }

    if ($batch_id > 0) {

        if ($mode == 'edit') {

            $result = $cls_sales->show_detail($batch_id,$log_info);

            $row_data = $db->fetch_assoc($result);

            $list1 = array();

            $i = 0;

            while ($row_data != null) {

                $pro_id = $row_data['product_id'];

                $ammount = $row_data['sales_ammount'];

                $list1[$i]['product_id'] = $pro_id;

                $list1[$i]['sales_ammount'] = $ammount;

                $list1[$i]['unit'] = $row_data['unit'];

                $row_data = $db->fetch_assoc($result);

                $i++;

            }

            for ($j = 0; $j < $i; $j++) {

                if ($list1[$j]['product_id'] > 0) {

                    $is_include_component = $cls_pro->is_include_component($list1[$j]['product_id'],$log_info);

                    if($is_include_component){

                        $components_data = $cls_component->get_by_product_id($list1[$j]['product_id'], $log_info);

                        for($qq=0;$qq<count($components_data);$qq++){

                            $record = $components_data[$qq];

                            $cls_stock_detail->insert_for_sales($record['product_id'], "修改销售", 0, $list1[$j]['sales_ammount']*$record['component_product_quantity'], "", time(), $log_info);

                            $cls_stock->update_stock_quantity_32($record['product_id'], $list1[$j]['sales_ammount']*$record['component_product_quantity'], $log_info);

                            

                        }

                    } else {

                        $cls_stock_detail->insert_for_sales($list1[$j]['product_id'], "修改销售", 0, 

                                $list1[$j]['sales_ammount'], $list1[$j]['unit'], time(), $log_info);

                        $cls_stock->update_stock_quantity_2($list1[$j]['product_id'], 

                            $list1[$j]['sales_ammount'], $list1[$j]['unit'],$log_info);

                        

                        

                    }

                    

                }

            }

            $cls_sales->del_sales_detail($batch_id,$log_info);

        }

        for ($i = 0; $i < count($name_gg_array); $i++) {

            if ($name_gg_array[$i] != "") {

                $pro_id = $pro_id_array[$i];

                if ($pro_id == "") {

                    $pro_id = 0;

                }

                $name_gg = $name_gg_array[$i];

                $price = $price_array[$i];

                $quantity = $quantity_array[$i];

                $money = $money_array[$i];

                $money_real = $money_real_array[$i];

                $unit = $unit_array[$i];

                $remark = $remark_array[$i];

                $p_model = $p_model_array[$i];

                $p_made = $p_made_array[$i];

                $detail_id = $cls_sales->insert_sales_detail($batch_id, $pro_id, $name_gg, $p_model, $p_made, $price, $quantity, $money, $money_real, $unit, $remark,$log_info);

                $stock_count = $cls_stock->count_stock($pro_id,$log_info);

                if ($pro_id > 0) {

                    $is_include_component = $cls_pro->is_include_component($pro_id,$log_info);

                    if ($stock_count > 0) {

                        $quantity_2 = -$quantity;                        

                        if($is_include_component){

                            $components_data = $cls_component->get_by_product_id($pro_id, $log_info);

                            for($qq=0;$qq<count($components_data);$qq++){

                                $record = $components_data[$qq];

                                $cls_stock_detail->insert_for_sales($pro_id, "修改销售", $detail_id, $quantity_2*$record['component_product_quantity'], "", time(), $log_info);

                                $cls_stock->update_stock_quantity_32($record['product_id'], $quantity_2*$record['component_product_quantity'], $log_info);

                                

                            }

                        } else {

                            $cls_stock_detail->insert_for_sales($pro_id, "修改销售", $detail_id, $quantity_2, $unit, time(), $log_info);

                            $cls_stock->update_stock_quantity_2($pro_id, $quantity_2, $unit,$log_info);

                            

                        }

                    } else {

                        $quantity_2 = -$quantity;

                        if($is_include_component){

                            

                        } else {

                            $cls_stock_detail->insert_for_sales($pro_id, "新增销售", $detail_id, $quantity_2, $unit, time(), $log_info);

                            $cls_stock->insert_stock_2(

                                $pro_id, $name_gg, $p_model, $p_made, $quantity_2, $unit,$log_info);

                            

                        }

                    }

                }

            }

        }

    }

    echo 'success,'.$batch_id;

} else if ($action == 'list_sales') {

    $page_size = 10;

    $page_id = $_GET["page_id"];

    $client_name = $_GET['client_name'];

    $filter = $_GET['filter'];

    $start_time = $_GET['start_time'];

    $end_time = $_GET['end_time'];

    $min_money = $_GET["min_money"];

    $max_money = $_GET["max_money"];

    

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $total = $cls_sales->count_sales($client_name, $filter, $start_time, $end_time,$min_money,$max_money,$log_info);

    $sales_money_total = array();

    $sales_money_total["sales_money"] = 0;

    $sales_money_total["sales_money_real"] = 0;

    if ($total > 0) {

        $sales_money_total = $cls_sales->sum_sales($client_name, $filter, $start_time, $end_time,$min_money,$max_money,$log_info);

    }

    $result = $cls_sales->list_sales($page_id, $page_size, $client_name, $filter, $start_time, $end_time,$min_money,$max_money,$log_info);

    if($result==null)

        return;

    $row_data = $db->fetch_assoc($result);

    echo "<table class='table table-bordered table-striped table-hover'>";

    echo "<thead>";

    echo "<tr>";

    echo "<th>编号</th>";

    echo "<th>日期</th>";

    echo "<th>金额</th>";

    echo "<th>实收金额</th>";

    echo "<th>客户名称</th>";

    echo "<th>摘要</th>";

    echo "<th>备注</th>";

    echo "<th>操作</th>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    $sales_money_page = 0;

    $sales_money_real_page = 0;

    while ($row_data != null) {

        $batch_id = $row_data['batch_id'];

        $sales_day = $row_data['sales_day'];

        $sales_money = $row_data['sales_money'];

        $sales_money_real = $row_data['sales_money_real'];

        $sales_money_page += $sales_money;

        $sales_money_real_page += $sales_money_real;

        $client_company = '';

        if ($row_data['client_company'] != null) {

            $client_company = $row_data['client_company'];

        }

        $remark = $row_data['remark'];

        $summary = $row_data['summary'];

        echo "<tr>";

        echo "<td>$batch_id</td>";

        echo "<td>$sales_day</td>";

        echo "<td>$sales_money</td>";

        echo "<td>$sales_money_real</td>";

        echo "<td>$client_company</td>";

        echo "<td>$summary</td>";

        echo "<td>$remark</td>";

        //echo "<td><a href='javascript:void(0)' onclick='show_detail($batch_id,this)'>详细 </a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='get_sales($batch_id)'> 编辑</a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='del_sales($batch_id)'>删除</a>&nbsp;&nbsp; <a href='sales_print.php?batch_id=" . $batch_id . "' target='_blank'>打印</a></td>";

        echo "<td><a href='javascript:void(0)' onclick='show_detail($batch_id,this)'>详细 </a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='get_sales($batch_id)'> 编辑</a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='del_sales($batch_id)'>删除</a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='print_sales_pre($batch_id)'>打印</a></td>";

        echo "</tr>";

        $row_data = $db->fetch_assoc($result);

    }

    $a = new Pager();

    echo "<tr><td></td><td style='text-align:center'>页计</td><td>$sales_money_page</td><td>$sales_money_real_page</td><td colspan='4'></td></tr>";

    echo "<tr><td></td><td style='text-align:center'>合计</td><td>" . $sales_money_total["sales_money"] . "</td><td>" . $sales_money_total["sales_money_real"] . "</td><td colspan='4'></td></tr>";

    echo "<tr><td colspan='7' style='text-align:center'>";

    $a->mypage($total, $page_id, $page_size);

    echo "</td>";

    echo "</tr>";

    echo "</tbody>";

    echo "</table>";

} else if ($action == 'del_sales') {

    $batch_id = $_GET['batch_id'];

    

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $result = $cls_sales->show_detail($batch_id,$log_info);

    $row_data = $db->fetch_assoc($result);

    $list1 = array();

    $i = 0;

    while ($row_data != null) {

        $pro_id = $row_data['product_id'];

        $ammount = $row_data['sales_ammount'];

        $list1[$i]['product_id'] = $pro_id;

        $list1[$i]['sales_ammount'] = $ammount;

        $list1[$i]['unit'] = $row_data['unit'];

        $row_data = $db->fetch_assoc($result);

        $i++;

    }

    for ($j = 0; $j < $i; $j++) {

        $is_include_component = $cls_pro->is_include_component($list1[$j]['product_id'],$log_info);

        if($is_include_component){

            $components_data = $cls_component->get_by_product_id($list1[$j]['product_id'], $log_info);

            for($qq=0;$qq<count($components_data);$qq++){

                $record = $components_data[$qq];

                $cls_stock_detail->insert_for_sales($record['product_id'], "修改销售", 0, $list1[$j]['sales_ammount']*$record['component_product_quantity'], "", time(), $log_info);

                $cls_stock->update_stock_quantity_32($record['product_id'], $list1[$j]['sales_ammount']*$record['component_product_quantity'], $log_info);                

            }

        } else {

            $cls_stock_detail->insert_for_sales($list1[$j]['product_id'], "修改销售", 0, $list1[$j]['sales_ammount'], $list1[$j]['unit'], time(), $log_info);

            $cls_stock->update_stock_quantity_2($list1[$j]['product_id'], $list1[$j]['sales_ammount'], $list1[$j]['unit'],$log_info);            

        }                

    }

    $result = $cls_sales->del_sales($batch_id,$log_info);

    echo $result;

} else if ($action == 'show_detail') {

    $batch_id = $_GET['batch_id'];    

    $page_name = $_GET["page_name"];

    $log_batch_id = $cls_log->get_batch_id();

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $result = $cls_sales->show_detail($batch_id,$log_info);

    $row_data = $db->fetch_assoc($result);

    echo "<table class='table table-bordered' id='detail_$batch_id'>";

    echo "<thead>";

    echo "<tr>";

    echo "<td>品名及规格</td><td>单位</td><td>数量</td><td>单价</td><td>金额</td><td>实收金额</td><td>备注</td>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    while ($row_data != null) {

        $product_id = $row_data["product_id"];

        $product_name = $row_data['product_name'];

        $unit = $row_data['unit'];

        $sales_ammount = $row_data['sales_ammount'];

        $sales_price = $row_data['sales_price'];

        $sales_money = $row_data['sales_money'];

        $sales_money_real = $row_data['sales_money_real'];

        $remark = $row_data['remark'];

        echo "<tr>";

        echo "<td><a target='_blank' href='sales_detail_single.php?product_id=".$product_id."'>$product_name</a></td>";

        echo "<td>$unit</td>";

        echo "<td>$sales_ammount</td>";

        echo "<td>$sales_price</td>";

        echo "<td>$sales_money</td>";

        echo "<td>$sales_money_real</td>";

        echo "<td>$remark</td>";

        echo "</tr>";

        $row_data = $db->fetch_assoc($result);

    }

    echo "<tr>";

    echo "<td colspan='7' style='text-align:right;'><a href='javascript:void(0)' onclick='hide_this(this)'>隐藏</a></td>";



    echo "</tr>";

    echo "</tbody>";

    echo "</table>";

} else if ($action == 'get_price_by_product_id_and_price_name') {

    $product_id = $_GET["product_id"];

    $price_name = $_GET["price_name"];

    $page_name = $_GET["page_name"];

    $company = $_GET["company"];

    $log_batch_id = $cls_log->get_batch_id();

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $result = $cls_price->get_by_product_id_and_price_name($company,$product_id, $price_name, $log_info);

    echo $result;    

}

?>