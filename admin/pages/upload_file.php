<?php
header('Content-Type: application/json; charset=utf-8');
if(isset($_FILES['file'])){
  $file = $_FILES['file'];
  $dir = __DIR__."/help_gfx/";
  $data['error']=true;
  $file_name = preg_replace('/\s+/', '_', $file['name']);
  if(copy($file['tmp_name'],$dir.$file_name)){
    $files = array_diff(scandir($dir),array(".",".."));
    $data['error']=false;
    $data['content']=$files;
  }else{
    $data["content"]="Failed to Upload File";
  }
}else{
  $data["content"]="No File";
}
echo(json_encode($data)); flush(); ob_flush(); exit();
?>
