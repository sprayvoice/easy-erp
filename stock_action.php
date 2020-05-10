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

require_once ( 'db/db_tag.class.php');

require_once ( 'db/db_price.class.php');

require_once ( 'db/db_py.class.php');

require_once ( 'db/db_stock.class.php');

require_once("db/db_stock_detail.class.php");



$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");

$cls_log = new db_log();

$cls_pro = new db_product($db,$cls_log);

$cls_tag = new db_tag($db,$cls_log);

$cls_price = new db_price($db,$cls_log);

$cls_py = new db_py($db,$cls_log);

$cls_stock = new db_stock($db,$cls_log);

$cls_stock_detail = new db_stock_detail($db, $cls_log);



$action = $_GET['action'];

$pro = "";

$tag1 = "";

$model = "";

$made = "";



if ($action == 'list_pro') {



    echo "<table id='list_tb1'  class='table table-hover'>";

    echo "<thead>";

    echo "<tr>";

    echo "<th><input type='checkbox' name='check_all' onclick='check_it_all(this)'/></th>";

    echo "<th>编号</th>";

    echo "<th>名称</th>";

    echo "<th>规格</th>";

    echo "<th>品牌/产地</th>";

    echo "<th>价格</th>";

    echo "<th>标签</th>";

    echo "<th>库存</th>";

    echo "<th>单位</th>";

    echo "<th>预警库存</th>";

    echo "<th>日期</th>";

    echo "<th>备注</th>";

    echo "<th>操作</th>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";



    $sort1 = $_GET['sort1'];

    $filter1 = trim($_GET['filter1']);

    $filter1 = str_replace('\'','',$filter1);

    $show_low = "0";

    if(isset($_GET["show_low"])){

        $show_low = $_GET["show_low"];

    }

    $show_recent = "0";

    if(isset($_GET["show_recent"])){

        $show_recent = $_GET["show_recent"];

    }

    

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    

    $result = $cls_pro->list_pro_stock($filter1, $sort1, '',$show_low,$show_recent,$log_info);

    if ($result == false) {


        return;
    }

    $row_data = $db->fetch_assoc($result);

    if ($row_data == null) {

        $result = $cls_pro->list_pro_pym_stock($filter1,$sort1,$show_low,$show_recent,$log_info);

        $row_data = $db->fetch_assoc($result);

    }

    $line_count = 1;

    while ($row_data != null) {

        echo "<tr>";  

        echo "<td><input type='checkbox' name='hid_id' value='" . $row_data['product_id'] . "'/></td>";      

        if($row_data['low_quantity']!=null && $row_data['stock_quantity']!=null && $row_data['low_quantity'] >$row_data['stock_quantity'] ){

            echo "<td class='red'>";

        } else {

            echo "<td>";

        }

        echo "<span style='color:green;font-style:italic;'>" .$line_count."</span>&nbsp;<a href='stock_detail.php?product_id=".$row_data['product_id']."' target='_blank'>" . $row_data['product_id'] . "</a>";

        $line_count++;

        echo "</td>";

        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_name'] . "\")'>" . $row_data['product_name'] . "</a></td>";

        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_model'] . "\")'>" . $row_data['product_model'] . "</a></td>";

        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_made'] . "\")'>" . $row_data['product_made'] . "</a></td>";

        echo "<td>" . $row_data['product_price'] . "</td>";

        echo "<td>" . "<a href='javascript:void(0)' onclick='fill_tag(\"" . $row_data['product_tags'] . "\")'>" . $row_data['product_tags'] . "</a></td>";

        echo "<td>" . $row_data['stock_quantity'] . "</td>";

        echo "<td>" . $row_data['stock_unit'] . "</td>";

        echo "<td>" . $row_data['low_quantity'] . "</td>";

        echo "<td>" . $row_data['last_upd_date'] . "</td>";

        echo "<td>" . $row_data['remark'] . "</td>";

        echo "<td>" . "<a href='javascript:void(0)' onclick='pandian1(";

        echo $row_data['product_id'] . ",this)'>盘点</a> ";

        echo "<a href='javascript:void(0)' onclick='edit_stock1(" . $row_data['product_id'] . ",this)'>编辑</a> ";

        echo "</td>";

        echo "</tr>";

        $row_data = $db->fetch_assoc($result);

    }



    echo "</tbody>";

    echo "</table>";

} else if ($action == 'list_tags') {

    echo "<br />";

    

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $result = $cls_tag->list_tag($log_info);

    if(!$result){
        return;
    }

    $row = $db->fetch_assoc($result);

    while ($row != null) {

        $tag_name = $row['tag_name'];

        echo " <a href='javascript:void(0)' onclick='fill_tag(\"" . $tag_name . "\")'>$tag_name</a> &nbsp;";

        $row = $db->fetch_assoc($result);

    }

} else if ($action == 'save_stock') {

    $pro_id = $_GET['product_id'];    

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

    $stock_quantity = $_GET['stock_quantity'];

    if ($pro_id != '' && $stock_quantity != '') {

        $cls_stock_detail->insert_for_stock($pro_id,$stock_quantity,$log_info);

        $cls_stock->update_stock_quantity_3($pro_id, $stock_quantity,$log_info);

        echo "success";

    }

} else if ($action == 'save_stock_full') {

    $pro_id = $_POST['product_id'];

    $product_name = $_POST["product_name"];

    $product_model = $_POST["product_model"];

    $product_made = $_POST["product_made"];

    $stock_price = $_POST["stock_price"];

    if($stock_price==''){

    	$stock_price='0';

    }

    $stock_quantity = $_POST['stock_quantity'];

    $stock_unit = $_POST["stock_unit"];

    $low_quantity  = $_POST["low_quantity"];

    if($low_quantity==''){

    	$low_quantity='0';

    }

    $remark = $_POST["remark"];

    

    $page_name = $_POST["page_name"];

    $log_batch_id = $cls_log->get_batch_id();

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

    $stock_money = $stock_price * $stock_quantity;

    if ($pro_id != '' && $stock_quantity != '') {

        $cls_stock_detail->insert_for_stock_full($pro_id,$stock_quantity,$stock_unit,$log_info);

        $cls_stock->update_stock_full($pro_id, $product_name, $product_model, $product_made, 

                $stock_quantity, $stock_price, $stock_money, $stock_unit,$low_quantity,$remark,$log_info);

        echo "success";

    }

} else if ($action == 'get_stock') {

    $pro_id = $_GET['product_id'];

    

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

    if ($pro_id != '') {

        $result = $cls_stock->get_stock($pro_id,$log_info);

        $row = $db->fetch_assoc($result);

        if ($row != null) {

            $ret = json_encode($row);

            echo $ret;

        }

    }

} else if ($action == "del_stock") {

    $pro_id = $_GET['product_id'];

    

    $log_batch_id = $cls_log->get_batch_id();

    $page_name = $_GET["page_name"];

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

    if ($pro_id != '') {

        $cls_stock_detail->insert_for_stock_full($pro_id,0,"",$log_info);

        echo $cls_stock->del_stock($pro_id,$log_info);

    }

}

?>