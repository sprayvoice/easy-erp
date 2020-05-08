<?php
if (isset($_COOKIE["login_true"]) == false) {
    session_start();
    $_SESSION["last_url"]="sales_detail_single.php";
    $url = "login.php";
    echo "<script language='javascript' type='text/javascript'>";
    echo "window.location.href='$url'";
    echo "</script>";
    return;
}
?><!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<title>销售记录</title>
<style type="text/css">
    .title {
        font-weight:bold;
        text-align: center;
        margin:15px;
        padding:15px;
        font-size:18px;
    }
    .bb {
        border-left:solid 1px;
        border-top:solid 1px;
        margin:5px;
        padding:5px;
        font-size:16px;
    }
    .bc {border-right:solid 1px;font-size:16px;}
    .bd {border-top:solid 1px;font-size:16px;}
</style>
</head>
<body>
    
<?php
require_once ( 'data/config.php'); 
require_once ( 'db/mysql.class.php');
require_once ( 'db/db_log.class.php');
require_once ( 'db/db_sales.class.php');
    
$product_id = $_GET["product_id"];
$db = new mysql($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
$cls_log = new db_log();
$cls_sales = new db_sales($db,$cls_log);    
$log_batch_id = $cls_log->get_batch_id();
    $page_name = "sales_detail_single.php";
    $action = "sales_detail_single";
  $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
$sales = $cls_sales->show_detail_by_product_id($product_id,$log_info);
$row = $db ->fetch_assoc($sales);
echo "<table>";
echo "<tr><td class='bb'>品名及规格</td><td class='bb'>单位</td><td class='bb'>数量</td><td class='bb'>单价</td><td class='bb'>金额</td><td class='bb'>日期</td><td class='bb'>备注</td><td class='bb bc'>单位</td></tr>";
while($row!=null){
    echo "<tr><td class='bb'>".$row["product_name"]."</td><td class='bb'>".$row["unit"]."</td><td class='bb'>".$row["sales_ammount"]."</td><td class='bb'>".$row["sales_price"]."</td><td class='bb'>".$row["sales_money"]."</td><td class='bb'>".$row["sales_day"]."</td><td class='bb'>".$row["remark"]."</td><td class='bb bc'>".$row["client_company"]."</td>";
    echo "</tr>";
    $row = $db ->fetch_assoc($sales );       
}
echo "<tr><td colspan='8' class='bd'></td></tr>";
echo "</table>";
   // 
   
?>

</body>
</html>