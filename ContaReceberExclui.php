<?php
###########
## Módulo para Processar a exclusão da conta a receber
## Criado: 07/02/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$ContaId = $_GET["ContaId"];

//Apaga a Conta a Pagar
//echo "DELETE FROM contas_pagar WHERE id = $ContaId<br/><br/>";
$sql2 = mysql_query("DELETE FROM contas_receber WHERE id = $ContaId");

//Apaga os pagamentos feitos para esta conta se houver
//echo "DELETE FROM contas_pagar_pagamento WHERE conta_pagar_id = $ContaId<br/><br/>";
$sql3 = mysql_query("DELETE FROM contas_receber_recebimento WHERE conta_receber_id = $ContaId");

//Apaga os cheques que tenham sido cadastrados
$sql3 = mysql_query("DELETE FROM cheques WHERE conta_receber_id = $ContaId");

//Apaga os boletos que tenham sido cadastrados
$sql_boleto = mysql_query("DELETE FROM boleto WHERE conta_receber_id = $ContaId");

?>
<br/>
<br/>
<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
	<tr>
		<td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
			<img src='./image/bt_informacao.gif' border='0' />
		</td>
		<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
			<strong>Conta a Receber excluida com sucesso !</strong>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-top: 8px">
			<input class="button" value="Voltar" type="button" name="btVoltar" onclick="wdCarregarFormulario('ModuloContasReceber.php','conteudo')" style="width: 100px" />
		</td>
	</tr>
</table>
