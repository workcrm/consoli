<?php
###########
## M�dulo para Processar a exclus�o da m�sica do repertorio do evento
## Criado: 11/10/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$RepertorioId = $_GET["RepertorioId"];
$EventoId = $_GET["EventoId"];

//Exclui a m�sica do evento
mysql_query("DELETE FROM eventos_repertorio WHERE id = $RepertorioId");

//Volta a exibi��o do evento
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
