<?php

function conectar_bd($host,$username,$password,$db,$puerto,$mensaje){
	$mysqli = new mysqli($host,$username,$password,$db,$puerto);
	if ($mysqli->connect_errno) {
					return "$mensaje (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	return  $mysqli;
}

?>
