<?php
ini_set("display_errors", "On");

require_once ( 'utils.php');
require_once ( 'data/config.php');
require_once ( 'db/mysqli.class.php');
require_once ( 'db/db_log.class.php');
require_once ( 'db/db_product.class.php');
require_once ( 'db/db_sales.class.php');
require_once ( 'db/db_product_unit.class.php');

$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");
$cls_log = new db_log();
$cls_pro = new db_product($db,$cls_log);
$cls_sales = new db_sales($db,$cls_log);
$cls_product_unit = new db_product_unit($db,$cls_log);

$action = $_GET['action'];


if ($action == 'list_tj1') {
    $start_time = $_GET["start_time"];
    $end_time = $_GET["end_time"];
    $tj_type = $_GET["tj_type"];
    
    $page_name = $_GET["page_name"];
    $log_batch_id = $cls_log->get_batch_id();
    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   
    if ($tj_type == "day") {
        $result = $cls_sales->tj_sales_by_day($start_time, $end_time,$log_info);
        echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>日期</th>";
        echo "<th>金额</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        if ($result == false) {
            echo mysql_error();
        }
        $row_data = $db->fetch_assoc($result);
        while ($row_data != null) {
            echo "<tr>";
            echo "<td>" . $row_data['sales_day'] . "";
            echo "</td>";
            echo "<td>" . $row_data['money'] . "";
            echo "</td>";
            echo "</tr>";
            $row_data = $db->fetch_assoc($result);
        }
        $result2 = $cls_sales->tj_sales_by_day_2($start_time, $end_time,$log_info);
        $row_data = $db->fetch_assoc($result2);
        if ($row_data != null) {
            echo "<tr>";
            echo "<td>合计：" . round($row_data['money'], 2) . "";
            echo "</td>";
            echo "<td>平均：" . round($row_data['avg_money'], 2) . "";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else if ($tj_type == "month") {
        $result = $cls_sales->tj_sales_by_month($start_time, $end_time,$log_info);
        echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>月份</th>";
        echo "<th>金额</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        if ($result == false) {
            echo mysql_error();
        }
        $row_data = $db->fetch_assoc($result);
        while ($row_data != null) {
            echo "<tr>";
            echo "<td>" . $row_data['month'] . "";
            echo "</td>";
            echo "<td>" . $row_data['money'] . "";
            echo "</td>";
            echo "</tr>";
            $row_data = $db->fetch_assoc($result);
        }
        $result2 = $cls_sales->tj_sales_by_month_2($start_time, $end_time,$log_info);
        $row_data = $db->fetch_assoc($result2);
        if ($row_data != null) {
            echo "<tr>";
            echo "<td>合计：" . round($row_data['money'], 2) . "";
            echo "</td>";
            echo "<td>平均：" . round($row_data['avg_money'], 2) . "";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else if($tj_type=="tag"){
        $result = $cls_sales->tj_sales_by_tag($start_time, $end_time,$log_info);
        echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>标签</th>";
        echo "<th>金额</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        if ($result == false) {
            echo mysql_error();
        }
        $row_data = $db->fetch_assoc($result);
        while ($row_data != null) {
            echo "<tr>";
            echo "<td>" . $row_data['tag_name'] . "";
            echo "</td>";
            echo "<td>" . $row_data['money'] . "";
            echo "</td>";
            echo "</tr>";
            $row_data = $db->fetch_assoc($result);
        }        
        echo "</tbody>";
        echo "</table>";
    } else if ($tj_type == "detail_and_profit") {
		$result = $cls_sales->list_sales_all($start_time, $end_time,$log_info);
        $cb_list = $cls_sales->get_in_sales_by_date($start_time, $end_time, $log_info);
		echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>id</th>";
        echo "<th>batch_id</th>";
        echo "<th>产品编号</th>";
        echo "<th>产品</th>";
        echo "<th>规格</th>";
        echo "<th>产地/品牌</th>";
        echo "<th>单价</th>";
        echo "<th>数量</th>";
        echo "<th>单位</th>";
        echo "<th>备注</th>";
        echo "<th>销售金额</th>";
        echo "<th>成本</th>";
        echo "<th>毛利</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $cb_total_batch=0;
        $cb_total=0;
        $profit = 0;
        $sales_total = 0;
      for($i=0;$i<count($result);$i++){
          $row_data = $result[$i];      
          $pro_id = $row_data['product_id'];
          $cb = $row_data['sales_money_real'];
          if(array_key_exists($pro_id,$cb_list)){
              $cb_row_data = $cb_list[$pro_id];
              if($row_data['unit']==$cb_row_data["unit"]){
                  $cb = $cb_row_data["product_price"] * $row_data['sales_ammount'];                  
              } else {

                  if(is_in_similar_unit($row_data['unit'],$cb_row_data["unit"])){
                      $cb = $cb_row_data["product_price"] * $row_data['sales_ammount'];

                  } else {
                    $result1 = $cls_product_unit->get_by_product_id($pro_id,$log_info);
                    $convert_list = unit_q_2_array($result1);
                    $in_unit3 = pick_similar_unit($row_data['unit'],$convert_list);
                    $out_unit3 = pick_similar_unit($cb_row_data["unit"],$convert_list);
                    if($in_unit3!=''){
                        $amount_converted = convert_unit($in_unit3,$row_data['sales_ammount'],$out_unit3,$convert_list);
                        $cb = $cb_row_data["product_price"] * $amount_converted;                  
                    }
                  }
              }
          }
          $cb_total_batch += $cb;
          $cb_total += $cb;
          
            echo "<tr>";
            echo "<td>" . $row_data['id'] . "</td>";
            echo "<td>" . $row_data['batch_id'] . "</td>";
            echo "<td>" . $row_data['product_id'] . "</td>";
            echo "<td>" . $row_data['product_name'] . "</td>";
            echo "<td>" . $row_data['product_model'] . "</td>";
            echo "<td>" . $row_data['product_made'] . "</td>";
            echo "<td>" . $row_data['sales_price'] . "</td>";
            echo "<td>" . $row_data['sales_ammount'] . "</td>";
            echo "<td>" . $row_data['unit'] . "</td>";
            echo "<td>" . $row_data['remark'] . "</td>";
            echo "<td>" . round($row_data['sales_money_real'],2) . "</td>";
            echo "<td>" . round($cb,2) . "</td>";
            echo "<td>" . round($row_data['sales_money_real']-$cb,2) . "</td>";
            echo "</td>";
            echo "</tr>";
            
            if(($i+1)<count($result)){
            	if($row_data['batch_id']!=$result[$i+1]["batch_id"]){
            		echo "<tr><td colspan='12' style='text-align:right'>&nbsp;小计：";
            		echo "</td><td>";
            		$total_sales_money_real = $row_data['total_sales_money_real'];
            		$profit += round($total_sales_money_real-$cb_total_batch,2);
            		echo round($total_sales_money_real-$cb_total_batch,2);
            		echo "</td></tr>";
            		
            		$cb_total_batch = 0;
            		$sales_total += $row_data['total_sales_money_real'];
            	}
            } 
            if($i==count($result)-1){
            		
            		$total_sales_money_real = $row_data['total_sales_money_real'];
            		$profit += round($total_sales_money_real-$cb_total_batch,2);
            		$sales_total += $row_data['total_sales_money_real'];
            		
            		
    				echo "<tr><td colspan='12' style='text-align:right'>&nbsp;小计：";
    				echo "</td><td>";
    				echo round($total_sales_money_real-$cb_total_batch,2);
    				echo "</td></tr>";            			
            		
            		
            		echo "<tr><td colspan='10' style='text-align:right'>&nbsp;金额总计：</td><td>$sales_total</td><td style='text-align:right'>&nbsp;毛利总计：";
            		echo "</td><td>";
            		echo round($profit,2);
            		echo "</td></tr>";
      		}
      }
      echo "</tbody>";
      echo "</table>";
		
      
      }
} 
?>