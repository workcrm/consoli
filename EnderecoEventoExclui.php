<?php
###########
## M�dulo para Processar a exclus�o do endere�o do evento
## Criado: 27/05/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########
/**
* @package workeventos
* @abstract M�dulo para processar a exclus�o do evento
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$EnderecoId = $_GET["EnderecoId"];
$EventoId = $_GET["EventoId"];

//Exclui os endere�os do evento
mysql_query("DELETE FROM eventos_endereco WHERE id = $EnderecoId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
