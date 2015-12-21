<?php 
###########
## M�dulo para exclusao de item da arvore de materiais
## Criado: 21/06/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
## 
###########
/**
* @package workeventos
* @abstract M�dulo para exclusao de item da arvore de materiais
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Captura o id do produto a efetuar a consulta/composi��o
$ItemId = $_GET["ItemId"];
$ComposicaoId = $_GET["ComposicaoId"];

//Exclui o material
mysql_query("DELETE FROM item_evento_composicao WHERE id = $ComposicaoId");

//Retorna para a visualiza��o da �rvore
header("location: ItemComposicaoCadastra.php?headers=1&ItemId=$ItemId");

?>