<?php
###########
## Módulo para Processar a exclusão da conta a pagar
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
$sql2 = mysql_query("DELETE FROM contas_pagar WHERE id = $ContaId");

//Apaga os pagamentos feitos para esta conta se houver
//echo "DELETE FROM contas_pagar_pagamento WHERE conta_pagar_id = $ContaId<br/><br/>";
$sql3 = mysql_query("DELETE FROM contas_pagar_pagamento WHERE conta_pagar_id = $ContaId");

//Apaga o terceiro do evento que tiver esta conta a pagar vinculada
//echo "DELETE FROM eventos_terceiro WHERE conta_pagar_id = $ContaId<br/><br/>";
$sql4 = mysql_query("DELETE FROM eventos_terceiro WHERE conta_pagar_id = $ContaId");

//Volta ao meu portal
//header("location: ModuloContasPagar.php?headers=1");
?>
<br/>
<br/>
<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
	<tr>
		<td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
			<img src='./image/bt_informacao.gif' border='0' />
		</td>
		<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
			<strong>Conta a Pagar excluida com sucesso !</strong>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-top: 8px">
			<input class="button" value="Voltar" type="button" name="btVoltar" onclick="wdCarregarFormulario('ModuloContasPagar.php','conteudo')" style="width: 100px" />
		</td>
	</tr>
</table>
