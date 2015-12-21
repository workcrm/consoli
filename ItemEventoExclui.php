<?php
###########
## Módulo para Processar a exclusão do item do evento
## Criado: 27/05/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo para processar a exclusão do item do evento
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$ItemId = $_GET["ItemId"];
$EventoId = $_GET["EventoId"];

//Exclui os endereços do evento
mysql_query("DELETE FROM eventos_item WHERE evento_id = $EventoId AND item_id = $ItemId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
