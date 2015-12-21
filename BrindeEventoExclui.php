<?php
###########
## Módulo para Processar a exclusão do brinde do evento
## Criado: 02/10/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$BrindeId = $_GET["BrindeId"];
$EventoId = $_GET["EventoId"];

//Exclui o registro
mysql_query("DELETE FROM eventos_brinde WHERE evento_id = $EventoId AND brinde_id = $BrindeId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
