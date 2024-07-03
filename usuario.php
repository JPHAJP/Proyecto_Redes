<?php
$META_URL="/";

$TEXTO=file_get_contents("usuario.txt");
$pos1= strpos($TEXTO,"\r\n");
$USUARIO_txt=substr($TEXTO,0,$pos1);
$TEXTO=substr($TEXTO,$pos1+1);
$pos1= strpos($TEXTO,"\r\n");
$PSW_txt=substr($TEXTO,0,$pos1);

if(isset($_GET['usuario_name'])&&isset($_GET['psw_name'])){
    $USUARIO_var=$_GET['usuario_name'];
    $PSW_var=$_GET['psw_name'];

  if($USUARIO_var==$USUARIO_txt){
    if($PSW_var==$PSW_txt){
      $META_URL="/dashboard";
    }
    else{
      $META_URL="/";
    }
  }
  else{
    $META_URL="/";
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Loding USER</title>
    <meta http-equiv="refresh" content="0; url=<?php echo $META_URL?>">
  </head>
  <body>
  </body>
</html>