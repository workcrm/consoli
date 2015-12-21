<?php
###########
## Módulo para Processar a exclusão de boletos
## Criado: 29/03/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$BoletoId = $_GET["BoletoId"];
$ContaReceberId = $_GET["ContaReceberId"];

//Apaga o Boleto
$sql2 = mysql_query("DELETE FROM boleto WHERE id = $BoletoId");

//Apaga os pagamentos feitos para a conta a receber vinculada se houver
$sql3 = mysql_query("DELETE FROM contas_receber_recebimento WHERE conta_receber_id = $ContaReceberId");

//Apaga a conta a receber vinculada ao boleto
$sql3 = mysql_query("DELETE FROM contas_receber WHERE id = $ContaReceberId");

?>
<br/>
<br/>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td height="22" width="20" valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px">
			<img src="./image/bt_informacao.gif" border="0" />
		</td>
		<td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px">
			<strong>Boleto excluído com sucesso !</strong>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-top: 8px">
			<input class="button" value="Voltar" type="button" name="btVoltar" onclick="wdCarregarFormulario('ModuloBoletos.php','conteudo')" style="width: 100px" />
		</td>
	</tr>
</table>
