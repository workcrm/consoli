<?php
###########
## Módulo para Processar e compensar automaticamente os cheques
## Criado: 11/09/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Monta a data atual
$data_compara = date("Y-m-d", mktime());

//Efetua o update nos cheques
$sql = "UPDATE cheques SET status = 2 WHERE data_vencimento <= '$data_compara' AND status < 2";

$executa = mysql_query($sql);

?>