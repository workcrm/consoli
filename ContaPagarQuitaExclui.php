<?php
###########
## Módulo para Processar a exclusão de um pagamento de uma conta a pagar
## Criado: 21/07/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

header("Content-Type: text/html;  charset=ISO-8859-1",true);
  
//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$PagamentoId = $_GET["PagamentoId"];
$ContaId = $_GET["ContaId"];

//Busca os dados para estornar a conta
$sql_pagamento = mysql_query("SELECT id, conta_pagar_id, total_pago FROM contas_pagar_pagamento WHERE id = $PagamentoId");
$dados_pagamento = mysql_fetch_array($sql_pagamento);

//Pega os dados da conta a pagar pai
$sql_conta = mysql_query("SELECT id, valor, valor_pago, situacao FROM contas_pagar WHERE id = $ContaId");
$dados_conta = mysql_fetch_array($sql_conta);

//Monta a variável do novo valor recebido da conta
$novo_valor_pago = $dados_conta["valor_pago"] - $dados_pagamento["total_pago"];

//Atualiza a conta a pagar, abrindo-a novamente e diminuindo o valor pago
$atualiza_conta = mysql_query("UPDATE contas_pagar SET valor_pago = '$novo_valor_pago', situacao = '1' WHERE id = $ContaId"); 

//Apaga o lançamento do recebimento
$sql4 = mysql_query("DELETE FROM contas_pagar_pagamento WHERE id = $PagamentoId");

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Estorno de Pagamento de Conta a Pagar</span></td>
  </tr>
  <tr>
    <td colspan="5">
	    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
	  </td>
  </tr>
</table>
<br/>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td height="22" width="20" valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px">
			<img src="./image/bt_informacao.gif" border="0" />
		</td>
		<td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px">
			<strong>Pagamento da Conta a Pagar estornado com sucesso !</strong>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-top: 8px">
			<input class="button" value="Voltar" type="button" name="btVoltar" onclick="wdCarregarFormulario('ContaPagarExibe.php?ContaId=<?php echo $ContaId ?>','conteudo')" style="width: 100px" />
		</td>
	</tr>
</table>
