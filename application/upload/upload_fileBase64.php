<?php
/**
 * base64图片上传
 * @param $base64_img
 * @return array
 */
$errorMg="";
$successFile="";
$base64_img = trim($_POST['upload_image']);
$basePath=$_SERVER['DOCUMENT_ROOT'];
$filePath="/storage/uploads";
$uploaddir = $basePath."".$filePath."";
//判断该用户文件夹是否已经有这个文件夹
if(!file_exists($uploaddir)) {
   mkdir(iconv("UTF-8", "GBK", $uploaddir),0777,true);
}
if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)){
   $type = $result[2];
  if(in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
       $new_file = $filePath."/".date('YmdHis').'.'.$type;
        if(file_put_contents($basePath.'/'.$new_file, base64_decode(str_replace($result[1], '', $base64_img)))){
            $successFile=$new_file;
        }else{
                $errorMg= '图片上传失败</br>';
        }
    }else{
        //文件类型错误
        $errorMg= '图片上传类型错误';
    }
}else{
  //文件错误
  $errorMg= '文件错误';
}
$response = [ 'error' => $errorMg, 'file' => $successFile];
echo json_encode($response);
