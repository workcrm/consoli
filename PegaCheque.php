<?php 
###########
## Módulo para Exibição dos dados do cheque cadastrado
## Criado: 01/03/2009 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Recupera o id do cheque
$ChequeId = $_GET["ChequeId"];
$ContaPagarId = $_GET["ContaPagarId"];

//Pega o valor já pago da conta
//Monta o sql para recuperar os dados do cheque
$sql  = "SELECT valor, valor_pago FROM contas_pagar WHERE id = $ContaPagarId";	

//Executa a query
$resultado = mysql_query($sql);

$registros = mysql_num_rows($resultado);

//Monta o array dos dados
$dados_conta = mysql_fetch_array($resultado);


//Monta o sql para recuperar os dados do cheque
$sql  = "SELECT
  		che.id,
        che.numero_cheque,
  		che.bom_para,
  		che.data_recebimento,
  		che.favorecido,
  		che.agencia,
  		che.pre_datado,
  		che.valor,
		che.valor_utilizado,
  		che.conta,
  		ban.nome as banco_nome			
  		FROM cheques che 
  		LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
  		WHERE che.id = $ChequeId";	

//echo $sql;
							   
//Executa a query
$resultado = mysql_query($sql);

$registros = mysql_num_rows($resultado);

//Monta o array dos dados
$dados = mysql_fetch_array($resultado);
	
$BomPara = DataMySQLRetornar($dados["bom_para"]);
$RecebidoEm = DataMySQLRetornar($dados["data_recebimento"]);

$saldo_cheque = $dados["valor"] - $dados["valor_utilizado"];
$saldo_conta = $dados_conta["valor"] - $dados_conta["valor_pago"];

//Arrendonda pra zero o saldo da conta caso seja negativo
if ($saldo_conta < 0)
{

	$saldo_conta = 0;
	
}

//Verifica se o valor do saldo a pagar da conta é menor que o valor do cheque utilizado
if ($saldo_conta < $saldo_cheque)
{
	
	//O valor a capturar é então somente o saldo a pagar da conta
	$valor_captura = number_format($saldo_conta, 2,",",".");
	
}

else

{

	//Caso o valor a pagar seja igual ou maior que o valor do cheque
	//Utiliza então o saldo a usar do cheque
	$valor_captura = number_format($saldo_cheque, 2,",",".");

}
	
if ($dados[pre_datado] == 1)
{
		
	$marca_chk = "true";
		
} 
  
else 
  
{
		
	$marca_chk = "false";
		
}
  		
echo "<input name='passa' type='hidden' value='1' />";

echo "<script>
		var Form;
		Form = document.frmContaQuita;
		Form.edtChequeIdDB.value = '$dados[id]';
		Form.edtValorPagoTerceiro.value = '$valor_captura';
		Form.edtFavorecido.value = '$dados[favorecido]';
		Form.edtBomParaTerceiro.value = '$BomPara';
		Form.edtBancoTerceiro.value = '$dados[banco_nome]';
		Form.edtAgenciaTerceiro.value = '$dados[agencia]';
		Form.edtContaTerceiro.value = '$dados[conta]';
		Form.edtRecebidoEmTerceiro.value = '$RecebidoEm';
		chk = document.getElementById('chkPredatadoTerceiro');
		chk.checked = $marca_chk;
		</script>";

?>