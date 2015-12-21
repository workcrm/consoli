<?php 
###########
## M�dulo para Consulta de CPF do Formando
## Criado: 04/12/2012 - Maycon Edinger
## Alterado: 
## Altera��es:
###########

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

$cpf = $_GET['cpf'];

if ($cpf == '') die("<span style='color: #990000'><b>Nenhum CPF Informado !</b></span>");

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Efetua o lookup na tabela de categorias
//Monta o SQL de pesquisa
$sql = "SELECT id, nome FROM eventos_formando WHERE cpf = '$cpf'";

//Executa a query
$query = mysql_query($sql);

//Verifica o n�mero total de registros
$registros = mysql_num_rows($query);

if ($registros == 0) die("<span style='color: #990000'><b>CPF N�o Encontrado !</b></span>");

$dados_cpf = mysql_fetch_array($query);

$id_cpf = $dados_cpf['id'];
$nome_cpf = $dados_cpf['nome'];

die("<b><span style='color: #990000'>CPF Encontrado:</span><br/>($id_cpf) - $nome_cpf</b>");

?>
