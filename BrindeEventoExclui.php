<?php
###########
## M�dulo para Processar a exclus�o do brinde do evento
## Criado: 02/10/2008 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$BrindeId = $_GET["BrindeId"];
$EventoId = $_GET["EventoId"];

//Exclui o registro
mysql_query("DELETE FROM eventos_brinde WHERE evento_id = $EventoId AND brinde_id = $BrindeId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
