<?php
###########
## M�dulo para Processar a exclus�o do item da locacao
## Criado: 30/08/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$ItemId = $_GET["ItemId"];
$LocacaoId = $_GET["LocacaoId"];

//Exclui os itens
mysql_query("DELETE FROM locacao_item WHERE locacao_id = $LocacaoId AND item_id = $ItemId");

//Volta ao meu portal
header("location: LocacaoExibe.php?LocacaoId=$LocacaoId&headers=1");
?>
