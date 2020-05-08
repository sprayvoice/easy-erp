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

require_once ( 'db/db_stock_group.class.php');



$group_id = $_GET["group_id"];


$db = new mysql_db($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");

$cls_log = new db_log();

$cls_stock_group = new db_stock_group($db);    

$log_batch_id = $cls_log->get_batch_id();

    $page_name = "stock_group_print.php";

    $action = "stock_group_print";

  $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  


$result = $cls_stock_group->list_detail_by_group_id($group_id);

$row = $db ->fetch_assoc($result);

echo "<table>";

echo "<tr><td colspan='3' class='title'>"."库存盘点单</td></tr>";

echo "<tr><td colspan='1'></td><td colspan='2'>日期：".""."</td></tr>";

echo "<tr><td class='bb'>产品品名及规格</td><td class='bb'>库存数量</td><td class='bb bc'>单位</td></tr>";

while($row!=null){

    $product = $row["product_name"] . " ". $row["product_model"]." ".$row["product_made"];

    echo "<tr><td class='bb'>".$product."</td><td class='bb'>".""."</td><td class='bb bc'>".$row["stock_unit"]."</td>";

    echo "</tr>";

    $row = $db ->fetch_assoc($result );       

}




echo "<tr><td colspan='3' class='bd bc' style='font-size:10px;'>";

echo "</td></tr>";

echo "</table>";

   // 

   

?>



</body>

</html>

