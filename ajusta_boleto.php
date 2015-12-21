<?php 
###########
## Módulo para relatório de alocação de produtos em eventos
## Criado: 15/08/2012 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Rotina para verificar se necessita ou não montar o header para o ajax
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{

  //Inclui o arquivo para manipulação de datas
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

