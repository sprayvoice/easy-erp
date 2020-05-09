<?php
	$file_tmp_name=$_FILES['file']['tmp_name'];// 服务器端的临时文件名
	$file_name=$_FILES['file']['name'];//文件名字 可以对文件名字进行加密 然后再执行move_uploaded_file
	$falg=move_uploaded_file($file_tmp_name,"uploads/".$file_name);//在你的project下面建立uploads文件夹
	if($falg){
		$error="success";
	}else{
		$error="failed";
	}
	$row=array(
		"error"=>$error,
		"filename"=>$file_name,
		"filetempname"=>$file_tmp_name
	);
	echo json_encode($row);
?>
