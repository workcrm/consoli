<?php 
###########
## M�dulo para relat�rio de aloca��o de produtos em eventos
## Criado: 15/08/2012 - Maycon Edinger
## Alterado: 
## Altera��es:
###########

//Rotina para verificar se necessita ou n�o montar o header para o ajax
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Verifica se a func�o j� foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{

  //Inclui o arquivo para manipula��o de datas
  include "./include/ManipulaDatas.php";

}

//Efetua o lookup na tabela de produtos
//Monta o sql de pesquisa
$sql = "SELECT id FROM boleto WHERE data_vencimento = '2015-04-10' and valor_recebido = 0";

//Executa a query
$busca = mysql_query($sql);

while ($dados = mysql_fetch_array($busca))
{

	$edtBoletoId = $dados['id'];

	$busca_data_vecimento = mysql_query("SELECT data_vencimento FROM contas_receber where boleto_id = $edtBoletoId");

	$dados_conta = mysql_fetch_array($busca_data_vecimento);

	$edtVencimento = $dados_conta['data_vencimento'];

	$atualiza = mysql_query("update boleto set data_vencimento = '$edtVencimento' where id = $edtBoletoId");

	echo "<br/>BoletoID: " . $edtBoletoId . ' - ' . $edtVencimento;

}

