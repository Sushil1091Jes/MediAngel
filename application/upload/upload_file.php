<?php
    // 允许上传的图片后缀
    $allowedExts = array("gif", "jpeg", "jpg", "png"); 
    $temp = explode(".", $_FILES["upload_image"]["name"]);
    $file_ext=strtolower(end($temp));// 获取文件后缀名
    $successFile="";
    $errorMg="";
    if(in_array($file_ext,$allowedExts)=== false){
      
       $errorMg="不允许扩展，请选择一个jpeg或png文件。";

    }else if ($_FILES["upload_image"]["error"] > 0)
    {
        $errorMg="错误：" . $_FILES["upload_image"]["error"] . "<br>";

    }else {

        $basePath=$_SERVER['DOCUMENT_ROOT'];
        $filePath="/storage/uploads/".date("Y")."/".date("m")."";
        $uploaddir = $basePath."".$filePath."";
        //判断该用户文件夹是否已经有这个文件夹
        if(!file_exists($uploaddir)) {
            mkdir(iconv("UTF-8", "GBK", $uploaddir),0777,true);
        }
        //为上传的文件新起一个名字，保证更加安全
        $new_filename = date('YmdHis',time()).rand(100,1000).'.'.$file_ext;
        $savePath=$filePath.'/'.$new_filename;
        //将文件从临时路径移动到磁盘
        $temp_name = $_FILES['upload_image']['tmp_name'];
        if (move_uploaded_file($temp_name,$basePath.'/'.$savePath)){
            $successFile=$savePath;
        }else {
            $errorMg ="上传失败。"; 
	    }
     }
     $response = [ 'error' => $errorMg, 'file' => $successFile];
     echo json_encode($response);
?>
