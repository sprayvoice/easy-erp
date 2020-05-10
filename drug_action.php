<?php



require_once ( 'data/config.php');

require_once ( 'db/mysqli.class.php');

require_once('bean/drug.class.php');

require_once("db/db_log.class.php");

require_once ( 'db/db_drug.class.php');



require_once ('Pager.php');





$db = new mysql_db($dbhost, $dbuser, $dbpass, $dbname, "pconn", "utf8");

$cls_log = new db_log();

$cls_drug = new db_drug($db,$cls_log);



$action = $_GET['action'];



if ($action == 'get_drug') {

    $d_id = $_GET['d_id'];

    $page_name = $_GET["page_name"];

    $log_batch_id = $cls_log->get_batch_id();

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $result = $cls_drug->get_drug($d_id,$log_info);

    $row_data = $db->fetch_assoc($result);

    if ($row_data != null) {

        $ret = json_encode($row_data);

        echo $ret;

    }

    return;

} else if ($action == 'get_going_expire_drug') {

    $page_name = $_GET["page_name"];

    $log_batch_id = $cls_log->get_batch_id();

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $result = $cls_drug->get_going_expire_drug($log_info);

    if($result){

        $row_data = $db->fetch_assoc($result);

        $str = "<span style='color:red;'>";
    
        while ($row_data != null) {
    
            $str .= "".$row_data["d_name"]."(".$row_data["expire_date"]."过期) "." ";
    
            $row_data = $db->fetch_assoc($result);
    
        }
    
        $str .= "</span>";
    
        echo $str;
    
        return;

    }

   

} else if ($action == 'save_drug') {

    $d_id = $_POST['d_id'];

    $mode = 'add';

    if ($d_id > 0) {

        $mode = 'edit';

    }

    $d_name = $_POST['d_name'];

    $d_model_made = $_POST['d_model_made'];

    $d_remark = $_POST['d_remark'];      

    $in_date = $_POST['in_date'];   

    $expire_date = $_POST['expire_date'];   

    $del_flag = $_POST['del_flag'];     

    

    $page_name = $_POST["page_name"];

    

    $bean = new drug();

    $bean->m_d_id = $d_id;

    $bean->m_d_name = $d_name;

    $bean->m_d_remark = $d_remark;

    $bean->m_d_model_made = $d_model_made;

    $bean->m_in_date = $in_date;

    $bean->m_expire_date = $expire_date;

    $bean->m_del_flag = $del_flag;    

    $log_batch_id = $cls_log->get_batch_id();

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    if($d_id==0){

        $cls_drug->insert($bean, $log_info);

    } else {

        $cls_drug->update($bean, $log_info);

    }    

    

    echo 'success';

} else if ($action == 'list_drug') {

    $page_size = 10;

    $page_id = $_GET["page_id"];

    $filter = trim($_GET['filter']);

    $time_type = trim($_GET["time_type"]);

    $start_time = $_GET['start_time'];

    $end_time = $_GET['end_time'];

    $show_del = $_GET["show_del"];

    $page_name = $_GET["page_name"];

    $log_batch_id = $cls_log->get_batch_id();

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);   

    $total = $cls_drug->count_drug($filter, $time_type, $start_time, $end_time, $show_del, $log_info);

    $result = $cls_drug->list_drug($filter, $time_type, $start_time, $end_time, $show_del, $page_id, $page_size, $log_info);   

    if(!$result){
        return;
    }

    $row_data = $db->fetch_assoc($result);

    echo "<table class='table table-bordered table-striped table-hover'>";

    echo "<thead>";

    echo "<tr>";

    echo "<th>编号</th>";

    echo "<th>名称</th>";

    echo "<th>规格产地</th>";

    echo "<th>备注</th>";

    echo "<th>入库日期</th>";

    echo "<th>过期日期</th>";

    echo "<th>添加日期</th>";

    echo "<th>是否删除</th>";

    echo "<th>操作</th>";

    echo "</tr>";

    echo "</thead>";

    echo "<tbody>";

    while ($row_data != null) {

        $d_id = $row_data['d_id'];

        $d_name = $row_data['d_name'];

        $d_model_made = $row_data["d_model_made"];

        $d_remark = $row_data["d_remark"];

        $in_date = $row_data["in_date"];

        $expire_date = $row_data["expire_date"];

        $del_flag = $row_data["del_flag"];

        $add_date = $row_data["add_date"];        

        

        echo "<tr>";

        

        $is_del = "";

        $del_style = "";

        if($del_flag==1){

            $is_del =  "是";

            $del_style = " style='color:red;'";

        } else if($del_flag==0){

            $is_del =  "否";   

        }

        echo "<td>$d_id</td>";

        echo "<td".$del_style.">$d_name</td>";

        echo "<td>$d_model_made</td>";

        echo "<td>$d_remark</td>";

        echo "<td>$in_date</td>";

        echo "<td>$expire_date</td>";

        echo "<td>$add_date</td>";

        echo "<td>$is_del</td>";       

        

        echo "<td><a href='javascript:void(0)' onclick='get_drug($d_id)'> 编辑</a>&nbsp;&nbsp; <a href='javascript:void(0)' onclick='del_drug($d_id)'>删除</a></td>";

        echo "</tr>";

        $row_data = $db->fetch_assoc($result);

    }

    $a = new Pager();

    echo "<td colspan='9'>";

    $a->mypage($total, $page_id, $page_size);

    echo "</td>";

    echo "</tr>";

    echo "</tbody>";

    echo "</table>";

} else if ($action == 'del_drug') {

    $d_id = $_GET['d_id'];

    $page_name = $_GET["page_name"];

    $log_batch_id = $cls_log->get_batch_id();

    $log_info = array('page_name'=>$page_name,'action_name'=>$action,'user_id'=>$_COOKIE["user_id"],'log_batch_id'=>$log_batch_id);       

    $result = $cls_drug->del($d_id,$log_info);

    echo $result;

}

?>

