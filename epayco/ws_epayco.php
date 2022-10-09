<?php
require_once('nusoap/nusoap.php');
require_once('lib.php');
require_once('modelo.php');
require_once('database.php');

$sProtocolo='http';
if (isset($_SERVER['HTTPS'])!=0){
	if ($_SERVER['HTTPS']=='on'){$sProtocolo='https';}
	}
$miURL=$sProtocolo.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$server = new soap_server();
$server->configureWSDL('ws_epayco', $miURL);
$server->wsdl->schemaTargetNamespace=$miURL;

////////////////////////// 	EPAYCO  /////////////////////////////////////////////////

#### REGISTRO CLIENTES ####
$server->register('registrarClientes',array('documento'=>'xsd:string','nombres'=>'xsd:string','email'=>'xsd:string','celular'=>'xsd:string'),array('return'=>'xsd:string'));

function registrarClientes($documento,$nombres,$email,$celular){
	$resultado = registrar_cliente(conectar_epayco(),$documento,$nombres,$email,$celular);	
	return new soapval('return', 'xsd:string', $resultado);
}

#### RECARGA BILLETERA ####
$server->register('recargaBilletera',array('documento'=>'xsd:string','celular'=>'xsd:string','valor'=>'xsd:string'),array('return'=>'xsd:string'));

function recargaBilletera($documento,$celular,$valor){
	$resultado = recarga_billetera(conectar_epayco(),$documento,$celular,$valor);	
	return new soapval('return', 'xsd:string', $resultado);
}

#### PAGAR ####
$server->register('pagar',array('documento'=>'xsd:string','valor'=>'xsd:string'),array('return'=>'xsd:string'));

function pagar($documento,$valor){
	$resultado = pagar_compra(conectar_epayco(),$documento,$valor);	
	return new soapval('return', 'xsd:string', $resultado);
}

#### CONFIRMAR PAGO ####
$server->register('confirmarPago',array('documento'=>'xsd:string','id_session'=>'xsd:string','token'=>'xsd:string'),array('return'=>'xsd:string'));

function confirmarPago($documento,$id_session,$token){
	$resultado = confirmar_pago(conectar_epayco(),$documento,$id_session,$token);	
	return new soapval('return', 'xsd:string', $resultado);
}

#### CONSULTAR SALDO ####
$server->register('consultarSaldo',array('documento'=>'xsd:string','celular'=>'xsd:string'),array('return'=>'xsd:string'));

function consultarSaldo($documento,$celular){
	$resultado = consultar_saldo(conectar_epayco(),$documento,$celular);	
	return new soapval('return', 'xsd:string', $resultado);
}

if(!isset($HTTP_RAW_POST_DATA))
$HTTP_RAW_POST_DATA = file_get_contents('php://input');
$server->service($HTTP_RAW_POST_DATA);

?>
