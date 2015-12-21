<?php 
###########
## Módulo para Consulta de CPF do Formando
## Criado: 04/12/2012 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

$cpf = $_GET['cpf'];

if ($cpf == '') die("<span style='color: #990000'><b>Nenhum CPF Informado !</b></span>");

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Efetua o lookup na tabela de categorias
//Monta o SQL de pesquisa
$sql = "SELECT id, nome FROM eventos_formando WHERE cpf = '$cpf'";

//Executa a query
$query = mysql_query($sql);

//Verifica o número total de registros
$registros = mysql_num_rows($query);

if ($registros == 0) die("<span style='color: #990000'><b>CPF Não Encontrado !</b></span>");

$dados_cpf = mysql_fetch_array($query);

$id_cpf = $dados_cpf['id'];
$nome_cpf = $dados_cpf['nome'];

die("<b><span style='color: #990000'>CPF Encontrado:</span><br/>($id_cpf) - $nome_cpf</b>");

?>
