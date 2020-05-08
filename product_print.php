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
     .b1 {border-left:solid 1px;font-size:16px;}
</style>
</head>
<body>
    
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
require_once ( 'db/db_product.class.php');
    
$filter1 = $_GET["filter1"];
$price_name = $_GET["price_name"];

$db = new mysql_db($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
$cls_log = new db_log();
$cls_product = new db_product($db,$cls_log);    
$log_batch_id = $cls_log->get_batch_id();
    $page_name = "product_print.php";
    $action = "product_print";
  $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);  
$result = $cls_product->list_pro_price($filter1,$price_name,"5","",$log_info);

$row = $db ->fetch_assoc($result );
echo "<table>";
echo "<tr><td class='bb'>品名及规格</td><td class='bb bc'>".$price_name."</td>";
while($row!=null){
    echo "<tr><td class='bb'>".$row["product_name"]." ".$row["product_model"]." ".$row["product_made"]."</td><td class='bb bc'>".$row["product_price"]."</td>";
    echo "</tr>";
    $row = $db ->fetch_assoc($result );       
}
	 echo "<tr><td class='bd'>&nbsp;</td><td class='bd'>&nbsp;</td> </tr>";
	 
echo "</table>";
   // 
   
?>

</body>
</html>