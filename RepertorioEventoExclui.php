<?php
###########
## Módulo para Processar a exclusão da música do repertorio do evento
## Criado: 11/10/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$RepertorioId = $_GET["RepertorioId"];
$EventoId = $_GET["EventoId"];

//Exclui a música do evento
mysql_query("DELETE FROM eventos_repertorio WHERE id = $RepertorioId");

//Volta a exibição do evento
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
