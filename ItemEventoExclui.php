<?php
###########
## M�dulo para Processar a exclus�o do item do evento
## Criado: 27/05/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########
/**
* @package workeventos
* @abstract M�dulo para processar a exclus�o do item do evento
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$ItemId = $_GET["ItemId"];
$EventoId = $_GET["EventoId"];

//Exclui os endere�os do evento
mysql_query("DELETE FROM eventos_item WHERE evento_id = $EventoId AND item_id = $ItemId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
