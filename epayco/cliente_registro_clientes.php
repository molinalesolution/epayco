<?php

if($_POST['documento']!=''){

	require_once('nusoap/nusoap.php');
	$serverURL 		= $_POST['servicio'];
	$serverScript   = 'ws_epayco.php';
	$metodoALlamar  = 'registrarClientes';

	// Crear un cliente de NuSOAP para el WebService

	$cliente = new nusoap_client("$serverURL/$serverScript?wsdl", 'wsdl');
	// Se pudo conectar?
	$error = $cliente->getError();
	if ($error) {
		echo '<pre style="color: red">' . $error  . '</pre>';
		echo '<p style="color:red;'>htmlspecialchars($cliente->getDebug(), ENT_QUOTES).'</p>';
		die();
	}

	$result = $cliente->call(
	"$metodoALlamar", // Funcion a llamar
	array(
		'documento'          =>   trim($_POST['documento']),
		'nombres'            =>   trim($_POST['nombres']),
		'email'              =>   trim($_POST['email']),
		'celular'            =>   trim($_POST['celular']),
	   ),// '5'// Parametros pasados a la funcion
		"uri:$serverURL/$serverScript", // namespace
		"uri:$serverURL/$serverScript/$metodoALlamar" // SOAPAction
	);

	// Verificacion que los parametros estan ok, y si lo estan. mostrar rta. 
	if ($cliente->fault){
		echo '<b>Error: ';
		print_r($result);
		echo '</b>';
	} else {
		$error = $cliente->getError();
		if ($error) {
		echo '<b style="color: red">Error: ' . $error . '</b>';
		} else {
		echo 'Respuesta: '.$result;
		}
	}
}
?>

<form id="form1" name="form1" method="post" action="cliente_registro_clientes.php">
<table width="90px" border="0" class='tablaSubtitulo' cellspacing="0" cellpadding="0">
<caption>REGISTRO DE CLIENTES </caption>
<BR>
	<tr>
		<br>
		<th width='70' scope="col" align="right">documento</th>
		<td width="70">
			<input type="text" id="documento" name="documento" size="20" value='<?php echo $_POST['documento']; ?>'/>			
		</td>
	</tr>
	&nbsp;
	<tr>
		<th width='70' scope="col" align="right">nombres</th>
		<td width="70">
			<input type="text" id="nombres" name="nombres" size="20" value='<?php echo $_POST['nombres']; ?>'/>			
		</td>
	</tr>
	<tr>
		<th width='70' scope="col" align="right">email</th>
		<td width="70">
			<input type="text" id="email" name="email" size="20" value='<?php echo $_POST['email']; ?>'/>			
		</td>
	</tr>
	<tr>
		<th width='70' scope="col" align="right">celular</th>
		<td width="70">
			<input type="text" id="celular" name="celular" size="20" value='<?php echo $_POST['celular']; ?>'/>			
		</td>
	</tr>
	<tr>
		<th width='70' scope="col" align="right">servicio</th>
		<td width="70">
			<input type="text" id="servicio" name="servicio" size="100" value='http://localhost/epayco'/>			
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td width="70">
			<input type="submit" value="Aceptar">		 
		</td>
	</tr>
	</table>
</form>
