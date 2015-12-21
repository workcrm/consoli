<?php
	
$edtTotalChk = $_POST['edtTotalChk'];
$edtDataCompensacao = date("Y-m-d", mktime());
$TemMensagem = 0;
		
//Define o valor inicial para efetuar o FOR
for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++)
{						
	
	//Enquanto não chegar ao final do contador total de itens
	if ($_POST[$contador_for] != 0) 
	{
											
		$sql =  mysql_query("UPDATE cheques_empresa SET status = 2, data_compensacao = '$edtDataCompensacao' WHERE id = '$_POST[$contador_for]'");
		
		$TemMensagem++;
		
	}
		
}

if ($TemMensagem > 0)
{

	$Mensagem = "$TemMensagem Cheques compensados com sucesso !";
	
}

else

{

	$Mensagem = "<span style='color: #990000'>Atenção:</span> Nenhum cheque foi marcado para compensação !";

}

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Compensação de Cheques</span>
		</td>
	</tr>
	<tr>
		<td><img src="image/bt_espacohoriz.gif" width="100%" height="12" /></td>
	</tr>
</table>
<table width="100%" border="0" align='left' cellpadding='0' cellspacing='0'>
	<tr>
		<td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
			<img src='./image/bt_informacao.gif' border='0' />
		</td>
		<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
			<strong><?php echo $Mensagem ?></strong>
		</td>
	</tr>
	<tr>
		<td colspan='2' style='padding-top: 5px; padding-bottom: 10px'>
			<input class='button' title="Retorna ao módulo de compensação de cheques" name="Voltar" type="button" id="Voltar" value="Voltar" onclick="wdCarregarFormulario('ModuloCompensaCheque.php','conteudo')" style="width: 110px"/>
		</td>
	</tr>
</table>