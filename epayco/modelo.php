<?php
session_start();

########################## EPAYCO MODELO ################################

#### REGISTRO CLIENTES ####
function registrar_cliente($conexion,$documento,$nombres,$email,$celular){

	if ($documento!='' && $nombres!='' && $email!='' && $celular!=''){

		$insert = "INSERT INTO clientes (documento,nombres,email,celular) VALUES ('$documento','$nombres','$email','$celular')";

		if($resultado_insert = $conexion->query($insert)){
			$resultado= 'Exito';
		}else{
			$resultado = 'Fallo';
		}

	}else{
		$resultado = 'Fallo';
	}

	return $resultado;
}

#### RECARGA BILLETERA ####
function recarga_billetera($conexion,$documento,$celular,$valor){

	if ($documento!='' && $celular!='' && $valor!=''){

		$select_billetera = "SELECT * FROM billetera WHERE documento = '$documento' AND id = (SELECT MAX(id) FROM billetera);";
		$resultado_billetera = $conexion->query($select_billetera);
		$row_billetera = $resultado_billetera->fetch_assoc();

		$saldo = $row_billetera['valor'] + $valor;

		$insert = "INSERT INTO billetera (documento,celular,valor) VALUES ('$documento','$celular','$saldo')";
		if($resultado_insert = $conexion->query($insert)){
			$resultado= 'Exito';
		}else{
			$resultado = 'Fallo';
		}
	
	}else{
		$resultado = 'Fallo';
	}

	return $resultado;
}

#### PAGAR ####
function pagar_compra($conexion,$documento,$valor){

	if ($documento!='' && $valor!=''){

		session_regenerate_id();
		$id_session = session_id();
		$token = bin2hex(random_bytes(3));
	
		$select_email = "SELECT email FROM clientes WHERE documento = '$documento'";
		$resultado_select = $conexion->query($select_email);
		$row = $resultado_select->fetch_assoc();
	
		$insert = "INSERT INTO pagar (documento,id_session,token,valor,estatus)
		VALUES ('$documento','$id_session','$token','$valor','PENDIENTE')";
		$resultado_insert = $conexion->query($insert);
	
		$enviar_correo = enviar_correo($id_session,$token,$row['email']);
	
		// COLOCAMOS EL ENVIAR CORREO EN TRUE YA QUE EN OCASIONES SE TIENE QUE CONFIGURAR EL SERVIDOR SMTP,
		// Y ASI PODER SEGUIR CON EL FLUJO DEL EJERCICIO
		if($resultado_insert && $enviar_correo=true){
			$resultado= 'Exito';
		}else{
			$resultado = 'Fallo';
		}

	}else{
		$resultado = 'Fallo';
	}

	session_destroy();
	return $resultado;
}

#### CONFIRMAR PAGO ####
function confirmar_pago($conexion,$documento,$id_session,$token){

	if ($documento!='' && $id_session!='' && $token!=''){

		$select= "SELECT * FROM pagar WHERE documento='$documento' AND id_session='$id_session' AND token='$token' AND estatus = 'PENDIENTE'";
		$resultado_select = $conexion->query($select);
		$row = $resultado_select->fetch_assoc();
	
		if ($row !=''){
			$select_billetera = "SELECT * FROM billetera WHERE documento = '$documento' AND id = (SELECT MAX(id) FROM billetera);";
			$resultado_billetera = $conexion->query($select_billetera);
			$row_billetera = $resultado_billetera->fetch_assoc();
	
			$saldo = $row_billetera['valor'] - $row['valor'];
	
			if ($saldo > 0 ){
				$update_pagar= "UPDATE pagar SET estatus = 'PROCESADO' 	WHERE documento='$documento' AND id_session='$id_session' AND token='$token'";
				$resultado_update = $conexion->query($update_pagar);
	
				$insert_billetera = "INSERT INTO billetera (documento,celular,valor) VALUES ('$documento','$row_billetera[celular]','$saldo ')";
	
				if($resultado_insert_billetera = $conexion->query($insert_billetera)){
					$resultado= 'Exito';
				}else{
					$resultado = 'Fallo';
				}
	
			}else{
				$resultado = 'Fallo';
			}
	
		}else{
			$resultado = 'Fallo';
		}

	}else{
		$resultado = 'Fallo';
	}

	return $resultado;
}

#### CONSULTAR SALDO ####
function consultar_saldo($conexion,$documento,$celular){

	if ($documento!='' && $celular!=''){

		$select = "SELECT valor FROM billetera WHERE documento = '$documento' AND celular = '$celular' AND id = (SELECT MAX(ID) FROM billetera)";

		$resultado_select= $conexion->query($select);
		$row = $resultado_select->fetch_assoc();
	
		if($row){
			$resultado= $row['valor'];
		}else{
			$resultado = 'Fallo';
		}

	}else{
		$resultado = 'Fallo';
	}

	return $resultado;
}

#### ENVIAR CORREO ####
function enviar_correo($id_session,$token,$email){

$para  = $email;
$título = 'Prueba Envio OTP servicio Epayco';
$mensaje = '
<html>
<head>
  <title>Código temporal para operaciones</title>
</head>
<body>
  <table>
		<tr>
			<th>Estimado usuario, se ha generado un código temporal para realizar operaciones</th>
		</tr>
    <tr>
      <th>ID SESSION :</th><th>'.$id_session.'</th>
    </tr>
    <tr>
      <td>TOKEN :</td><td>'.$token.'</td>
    </tr>
    <tr>
      <td>Por favor ingrese estos códigos para validar la confirmación de pago</td>
    </tr>
  </table>
</body>
</html>
';

$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$cabeceras .= 'From: Recordatorio <molinal.esolution@gmail.com>' . "\r\n";

mail($para, $título, $mensaje, $cabeceras);

}

?>
