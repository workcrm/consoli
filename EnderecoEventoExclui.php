<?php
###########
## Módulo para Processar a exclusão do endereço do evento
## Criado: 27/05/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo para processar a exclusão do evento
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$EnderecoId = $_GET["EnderecoId"];
$EventoId = $_GET["EventoId"];

//Exclui os endereços do evento
mysql_query("DELETE FROM eventos_endereco WHERE id = $EnderecoId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
