<?php
###########
## Módulo para Processar a exclusão da data do evento
## Criado: 06/12/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$DataId = $_GET["DataId"];
$EventoId = $_GET["EventoId"];

//Exclui os endereços do evento
mysql_query("DELETE FROM eventos_data WHERE id = $DataId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
