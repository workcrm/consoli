<?php 
###########
## M�dulo para Processamento das contas a pagar por lote
## Criado: 04/12/2014 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipula��o de datas
include "./include/ManipulaDatas.php";

$edtDataProcessa = DataMySQLInserir($_POST['edtDataProcessa']);
$edtArrayContas = $_POST['conta_id'];

//echo 'Data: ' . $edtDataProcessa;

//Explode os usu�rios marcados
$dados_contas = explode(";", $edtArrayContas);
$quantidade_contas = count($dados_contas) -1;
$linha_contas = 0;

//Caso tenha contas marcadas
if ($quantidade_contas > 0)
{

  //Repassa todos os usu�rios
  foreach ($dados_contas AS $array_contas)
  {

  	//Captura o valor do usu�rio respons�vel
    $edtContaId = $array_contas;

    if ($edtContaId != '')
    {

      //Busca o total j� recebido da conta
			$saldo_conta = mysql_query("SELECT id, valor, valor_pago FROM contas_pagar WHERE id = $edtContaId");
    
			$dados_conta = mysql_fetch_array($saldo_conta);
    
			$saldo_pagar = $dados_conta["valor"] - $dados_conta["valor_pago"];

			//Efetua o lan�amento para os campos de dinheiro
			$sql = "INSERT INTO contas_pagar_pagamento (
							conta_pagar_id,
							data_pagamento, 
							tipo_pagamento,
							total_pago,
							obs 
							) values (				
							'$edtContaId',
							'$edtDataProcessa',
							'1',
							'$saldo_pagar',
							'Pagamento em Lote - Quitacao Automatica'
							);";
			
								
			$paga_conta = mysql_query($sql);		
		
			//Faz o update do valor pago da conta e a marca como quitada
			$sql = mysql_query("UPDATE contas_pagar SET valor_pago = '$saldo_pagar', situacao = '2' WHERE id = '$edtContaId'");
		
      //echo "<br/><br/>Conta ID: " . $edtContaId . ' - Valor Pagar: ' . $saldo_pagar;
			//echo "<br/>$sql";
    
    	$linha_contas++;

    }

  }

}

echo "Contas Processadas com SUCESSO! - Total de Contas Processadas: " . $linha_contas;

?>
