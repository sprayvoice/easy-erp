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
require_once ( 'db/db_sales.class.php');
require_once ( 'money.php');
    
$batch_id = $_GET["batch_id"];
$c = $_GET["c"];
$company1  = '';
$o = $_GET["o"];
if($c=='1'){
    $company1 = '余姚市渔舜五金经营部';
} else if($c=='2'){
    $company1 = '余姚市万红五金经营部';
} else if($c=='3'){
    $company1 = '余姚市朗歌五金经营部';
} else if($c=='4'){
    $company1 = $o;
} 
$db = new mysql_db($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
$cls_log = new db_log();
$cls_sales = new db_sales($db,$cls_log);    
$log_batch_id = $cls_log->get_batch_id();
    $page_name = "sales_print.php";
    $action = "sales_print";
  $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
$sales = $cls_sales->show_sales($batch_id,$log_info);
$result_sales = $db ->fetch_assoc($sales);
$client_company = $result_sales["client_company"];
$remark = $result_sales["remark"];
$sales_money = $result_sales["sales_money"];
$sales_money_real = $result_sales["sales_money_real"];
$sales_day = $result_sales["sales_day"];
$result = $cls_sales->show_detail($batch_id,$log_info);
$row = $db ->fetch_assoc($result );
echo "<table>";
echo "<tr><td colspan='4' class='title'>".$company1."销售单</td><td class='b1'> NO: $batch_id </td></tr>";
echo "<tr><td colspan='3'>单位名称：".$client_company."</td><td colspan='2'>日期：".$sales_day."</td></tr>";
echo "<tr><td class='bb'>品名及规格</td><td class='bb'>单位</td><td class='bb'>数量</td><td class='bb'>单价</td><td class='bb bc'>金额</td>";
while($row!=null){
    echo "<tr><td class='bb'>".$row["product_name"]."</td><td class='bb'>".$row["unit"]."</td><td class='bb'>".$row["sales_ammount"]."</td><td class='bb'>".$row["sales_price"]."</td><td class='bb bc'>".$row["sales_money"]."</td>";
    echo "</tr>";
    $row = $db ->fetch_assoc($result );       
}
echo "</tr><td colspan='3' class='bb'>备注：".$remark."</td>"."<td class='bb bc' colspan='2'>合计：".$sales_money
        ."</td>"."</tr>";
echo "</tr><td colspan='5' class='bb bd bc'>大写金额：";
$test = new digit2chinese;
$test->num = $sales_money;
$test->chuli();
$test->huey_print();
echo "</td></tr>";
echo "</tr><td colspan='5' class='bd' style='font-size:10px;'>";
echo "<br />地址：余姚市新建北路56号  联系电话：0574-62636034";
echo "</td></tr>";
echo "</table>";
   // 
   
?>

</body>
</html>
