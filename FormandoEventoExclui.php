<?php
###########
## M�dulo para Processar a exclus�o do formando do evento
## Criado: 14/10/2008 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$FormandoId = $_GET["FormandoId"];
$EventoId = $_GET["EventoId"];

//Exclui o formando do evento
mysql_query("DELETE FROM eventos_formando WHERE id = $FormandoId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
