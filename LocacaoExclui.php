<?php
###########
## M�dulo para Processar a exclus�o da locacao
## Criado: 27/05/2007 - Maycon Edinger
## Alterado: 05/06/2007 - Maycon Edinger
## Altera��es: 
## 05/06/2007 - Corrigido bug que n�o excluia os participantes do evento a ser excluido
###########


//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$LocacaoId = $_POST["LocacaoId"];

//Rotinas de exclus�o

//Exclui os itens da locacao
mysql_query("DELETE FROM locacao_item WHERE locacao_id = $LocacaoId");

//Exclui a locacao
mysql_query("DELETE FROM locacao WHERE id = $LocacaoId");

//Volta ao meu portal
header("location: sistema.php");
?>
