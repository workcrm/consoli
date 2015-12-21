<?php
###########
## Módulo para Processar a exclusão do formando do evento
## Criado: 14/10/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$FormandoId = $_GET["FormandoId"];
$EventoId = $_GET["EventoId"];

//Exclui o formando do evento
mysql_query("DELETE FROM eventos_formando WHERE id = $FormandoId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
