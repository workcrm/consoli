<?php
###########
## Módulo para Processar a exclusão do item da locacao
## Criado: 30/08/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$ItemId = $_GET["ItemId"];
$LocacaoId = $_GET["LocacaoId"];

//Exclui os itens
mysql_query("DELETE FROM locacao_item WHERE locacao_id = $LocacaoId AND item_id = $ItemId");

//Volta ao meu portal
header("location: LocacaoExibe.php?LocacaoId=$LocacaoId&headers=1");
?>
