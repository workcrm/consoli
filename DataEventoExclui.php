<?php
###########
## M�dulo para Processar a exclus�o da data do evento
## Criado: 06/12/2008 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$DataId = $_GET["DataId"];
$EventoId = $_GET["EventoId"];

//Exclui os endere�os do evento
mysql_query("DELETE FROM eventos_data WHERE id = $DataId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
