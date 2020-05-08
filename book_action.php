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
    require_once ( 'pinyin.php');
    require_once ( 'db/db_book.class.php');
    
    require_once ( 'filter.php');
    require_once('Pager.php');
    
    $db = new mysql_db($dbhost,$dbuser,$dbpass,$dbname,"pconn","utf8");
    $cls_book = new db_book($db);
	$book_name="";
	$page_num = "";
	$author = "";
	$add_date="";
	$publisher ="";
			
    $action = $_GET['action'];
    
     if($action=='add_book'){
    	$book_name = $_POST['book_name'];
    	$page_num = $_POST['page_num'];
    	$author = $_POST['author'];
    	$add_date =  $_POST['add_date'];
    	$publisher =  $_POST['publisher'];
     }
    if($action=='add_book' && $book_name==""){
    	echo "名称不能为空";
    	return;
    }
    if($action=='get_book'){
    	$book_id = $_GET['book_id'];
    
       $result = $cls_book->get_by_id($book_id);     
    
       $row = $db->fetch_assoc($result );
       if($row!=null){
       	$ret = json_encode($row);
       	echo $ret;
       }
       
    } else if($action=='add_book'){
	    $count1 = $cls_book->count_book_by_name($book_name);
	    if($count1==0){	    	 	       
	       $cls_book->insert_book($book_name,$page_num,$author, $add_date, $publisher);
	        echo "success";
	    } else {
	    	echo  $count1;
	    	  echo "已经存在相同数据";
		}
} else if($action=='list_book'){
        $page_id = $_GET["page_id"];
        $page_size = $_GET["page_size"];
        $from = $_GET["from"];
        $to = $_GET["to"];
        $filter1 = trim($_GET['filter1']);
        $total = $cls_book->count_book($filter1,$from,$to);
        $result= $cls_book->list_book($page_id,$page_size,$filter1,$from,$to);
		if($result==false){
			echo mysql_error();
		}
		$row_data = $db->fetch_assoc($result );
		echo "<table id='list_tb1'  class='table table-bordered table-striped table-hover' data-toggle='table'>";
		echo "<thead>"; 
		echo "<tr>";
		echo "<th>编号</th>";
		echo "<th>名称</th>";
		echo "<th>页数</th>";
		echo "<th>作者</th>";
		echo "<th>添加日期</th>";
		echo "<th>出版社</th>";
		echo "<th>操作</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";	
		while($row_data!=null){
		echo "<tr>";
		echo "<td>".$row_data['BOOK_ID']."";
		echo "</td>";
		echo "<td>".$row_data['BOOK_NAME']."</td>";
		echo "<td>".$row_data['PAGE_NUM']."</td>";
		echo "<td>".$row_data['AUTHOR']."</td>";
		echo "<td>".$row_data['ADD_DATE']."</td>";
		echo "<td>".$row_data['PUBLISHER']."</td>";
		echo "<td>"."<a href='javascript:void(0)' onclick='edit1(";
		echo $row_data['BOOK_ID'].")'>编辑</a> ";
		echo " <a href='javascript:void(0)' onclick='del1(";
		echo $row_data['BOOK_ID'] .")'>删除</a> ";
		echo " <a href='javascript:void(0)' onclick='copy1(";
		echo $row_data['BOOK_ID'] .")'>复制</a> ";
		echo "</td>";
		echo "</tr>";
		$row_data = $db->fetch_assoc($result );
	
	}
	echo "<tr><td colspan='7'>";
    	 $Pager = new Pager();
    	 $Pager->mypage($total,$page_id,$page_size);
    	 echo "</td></tr>";
		echo "</tbody>";
		echo "</table>";
    }  else if($action=='edit_book'){
    		$book_name = $_POST['book_name'];
     	$book_id = $_POST['book_id'];
     	$page_num = $_POST['page_num'];
      	$add_date = $_POST['add_date'];
        $author = $_POST['author'];
        $publisher = $_POST['publisher'];
        $c  = $cls_book->count_book_by_name($book_name);
       if($c==0){
        echo "未找到要编辑的产品";
        return;   
       }
       $cls_book->update_book($book_id,$book_name,$page_num,$author,$add_date,$publisher);
        echo "success";
    	
     
    }else  if($action=='del_book'){
    	$book_id = $_POST['book_id'];
        $cls_book->delete_by_id($book_id);		
        echo "success";
    } else if($action=="export_csv"){
        header("Content-Type:text/xml");
        header("Content-Disposition:attachment;filename=export.csv");
        $filter1 = $_GET["filter1"];
        $from = $_GET["from"];
        $to = $_GET["to"];        
        $result= $cls_book->list_book_all($filter1,$from,$to);
        if($result==false){
            echo mysql_error();
	}
	$row_data = $db->fetch_assoc($result );
        while($row_data!=null){
            $book_name = $row_data["BOOK_NAME"];
            $add_date = $row_data["ADD_DATE"];
            $page_num = $row_data["PAGE_NUM"];
            echo $book_name;
            echo "||,";
            echo $add_date;
            echo "||,";
            echo $page_num;
            echo "||\n";            
            $row_data = $db->fetch_assoc($result );
        }
        
    }
      
?>