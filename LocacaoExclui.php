<?php
###########
## Módulo para Processar a exclusão da locacao
## Criado: 27/05/2007 - Maycon Edinger
## Alterado: 05/06/2007 - Maycon Edinger
## Alterações: 
## 05/06/2007 - Corrigido bug que não excluia os participantes do evento a ser excluido
###########


//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$LocacaoId = $_POST["LocacaoId"];

//Rotinas de exclusão

//Exclui os itens da locacao
mysql_query("DELETE FROM locacao_item WHERE locacao_id = $LocacaoId");

//Exclui a locacao
mysql_query("DELETE FROM locacao WHERE id = $LocacaoId");

//Volta ao meu portal
header("location: sistema.php");
?>
