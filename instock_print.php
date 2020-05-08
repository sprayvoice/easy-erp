<!DOCTYPE html>

<html lang="zh-cn">

<head>

<meta charset="utf-8">

<title>打印</title>

<style type="text/css">

    .title {

        font-weight:bold;

        text-align: center;

        margin:15px;

        padding:15px;

        font-size:16px;

    }

    .bb {

        border-left:solid 1px;

        border-top:solid 1px;

        margin:5px;

        padding:5px;

        font-size:14px;

    }

    .b1 {text-align:right;font-style:italic;font-size:12px;font-weight:normal;}

    .bc {border-right:solid 1px;font-size:14px;}

    .bd {border-top:solid 1px;font-size:14px;}

</style>

</head>

<body>

    

<?php

require_once ( 'data/config.php'); 

require_once ( 'db/mysqli.class.php');

require_once ( 'db/db_log.class.php');

require_once ( 'db/db_instock.class.php');

require_once ( 'money.php');
    

$batch_id = $_GET["in_batch_id"];

$company1  = '';


$db = new mysql_db($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");

$cls_log = new db_log();

$cls_instock = new db_instock($db,$cls_log);    

$log_batch_id = $cls_log->get_batch_id();

    $page_name = "instock_print.php";

    $action = "instock_print";

  $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  

$instock = $cls_instock->show_instock($batch_id,$log_info);

$result_instock = $db ->fetch_assoc($instock);

$in_company = $result_instock["in_company"];

$remark = $result_instock["remark"];

$total_money = $result_instock["total_money"];

$instock_day = $result_instock["add_date"];

$result = $cls_instock->show_detail($batch_id,$log_info);

$row = $db ->fetch_assoc($result );

echo "<table>";

echo "<tr><td colspan='4' class='title'>"."入库单</td><td class='b1'> NO: $batch_id </td></tr>";

echo "<tr><td colspan='3'>供货单位：".$in_company."</td><td colspan='2'>日期：".$instock_day."</td></tr>";

echo "<tr><td class='bb'>品名及规格</td><td class='bb'>单位</td><td class='bb'>数量</td><td class='bb'>单价</td><td class='bb bc'>金额</td>";

while($row!=null){

    echo "<tr><td class='bb'>".$row["product_name"]."</td><td class='bb'>".$row["unit"]."</td><td class='bb'>".$row["in_quantity"]."</td><td class='bb'>".$row["in_price"]."</td><td class='bb bc'>".$row["in_money"]."</td>";

    echo "</tr>";

    $row = $db ->fetch_assoc($result );       

}

echo "</tr><td colspan='3' class='bb'>备注：".$remark."</td>"."<td class='bb bc' colspan='2'>合计：".$total_money

        ."</td>"."</tr>";

echo "</tr><td colspan='5' class='bb bd bc'>大写金额：";

$test = new digit2chinese;

$test->num = $total_money;

$test->chuli();

$test->huey_print();

echo "</td></tr>";

echo "</tr><td colspan='5' class='bd' style='font-size:10px;'>";

echo "</td></tr>";

echo "</table>";

   // 

   

?>



</body>

</html>

