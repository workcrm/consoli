<?php 
###########
## Módulo para Alteracao de preço de venda
## Criado: 19/06/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
## 
###########
/**
* @package workeventos
* @abstract Módulo Alteracao de preço de venda
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Converte o valor de money para o formato do mySQL
function MoneyMySQLInserir($VALOR){
  $tira_virgula = str_replace(".","",$VALOR);
  $converte = str_replace(",",".",$tira_virgula);
  return $converte;
} 

//Recupera o tipo da alteração
$Tipo = $_GET[Tipo];
$ItemId = $_GET[ItemId];
$Preco = $_GET[Preco];

if ($Tipo == 1) {	
	//Atualiza o preço de venda com base no preço de custo
	mysql_query("UPDATE item_evento SET valor_custo = '$Preco' WHERE id = $ItemId");	
}

if ($Tipo == 2) {
	//Recupera o preço a ser aplicado
	$Preco = $_GET[Preco];
	//Atualiza o preço de venda com base no preço de custo
	mysql_query("UPDATE item_evento SET valor_custo = '$Preco', valor_venda = '$Preco' WHERE id = $ItemId");	
}

if ($Tipo == 3) {
	//Recupera o preço a ser aplicado
	$Preco = $_GET[Preco];
	//Recupera a margem a ser aplicado
	$Margem = $_GET[Margem];
	//Calcula o valor do percentual a somar ao preço
	$Percentual = number_format($Preco * ($Margem / 100), 2, ".", "");
	//Soma o percentual ao preço
	$Valor = number_format($Preco + $Percentual, 2, ".", "");
	//Atualiza o preço de venda com base no valor calculado
	mysql_query("UPDATE item_evento SET valor_custo = '$Preco', valor_venda = '$Valor' WHERE id = $ItemId");	
}

if ($Tipo == 4) {
	//Recupera o preço a ser aplicado
	$Valor = MoneyMySQLInserir($_GET[Valor]);
	//Atualiza o preço de venda com base no valor informado
	mysql_query("UPDATE item_evento SET valor_custo = '$Preco', valor_venda = '$Valor' WHERE id = $ItemId");	
}

header("location: ItemCadastra.php?headers=1");
?>