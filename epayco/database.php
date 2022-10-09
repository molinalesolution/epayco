<?php

function conectar_epayco(){
   $mysqli_epayco =conectar_bd("127.0.0.1", "root", "123456", "epayco", 3306,'Error al conectar a mysqli_autenticador');
   return $mysqli_epayco;		
}

?>