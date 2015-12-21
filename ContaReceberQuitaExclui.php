<?php
###########
## Módulo para Processar a exclusão de um recebimento de uma conta a receber
## Criado: 31/03/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

header('Content-Type: text/html;  charset=ISO-8859-1',true);
  
//Estabelece a conexão com o banco de dados  
include('./conexao/ConexaoMySQL.php');

//Recebe os parâmetros para montar a exclusão
$RecebimentoId = $_GET['RecebimentoId'];
$ContaId = $_GET['ContaId'];
$ComBoleto = $_GET['ComBoleto'];
$BoletoId = $_GET['BoletoId'];

//Busca os dados para estornar a conta
$sql_recebimento = mysql_query("SELECT id, conta_receber_id, total_recebido FROM contas_receber_recebimento WHERE id = $RecebimentoId");
$dados_recebimento = mysql_fetch_array($sql_recebimento);

//Pega os dados da conta a receber pai

$sql_conta = mysql_query("SELECT id, valor, valor_recebido, situacao FROM contas_receber WHERE id = $ContaId");
$dados_conta = mysql_fetch_array($sql_conta);

//Monta a variável do novo valor recebido da conta
$novo_valor_recebido = $dados_conta['valor_recebido'] - $dados_recebimento['total_recebido'];

//Atualiza a conta a receber, abrindo-a novamente e diminuindo o valor recebido
$atualiza_conta = mysql_query("UPDATE contas_receber SET valor_recebido = '$novo_valor_recebido', situacao = '1' WHERE id = $ContaId"); 

//Apaga o lançamento do recebimento
$sql4 = mysql_query("DELETE FROM contas_receber_recebimento WHERE id = $RecebimentoId");

//Verifica se deve reabrir o boleto
if ($ComBoleto == 1)
{
 
	$MensagemBoleto = "<br/><br/>Conta com boleto vinculado. O Boleto foi reaberto !";
 
	$data_atualizacao = date("Y-m-d", mktime());
	
	//Atualiza o boleto reabrindo-o
	$atu_boleto = mysql_query("UPDATE boleto SET 
								boleto_recebido = 0, 
								valor_recebido = 0, 
								obs_recebimento = '', 
								data_atualizacao = '$data_atualizacao';
								usuario_recebimento_id = 0 
								WHERE id = $BoletoId");
  
}

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Estorno de Recebimento de Conta a Receber</span></td>
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
			<strong>Recebimento da Conta a Receber estornado com sucesso !<?php echo $MensagemBoleto ?></strong>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-top: 8px">
			<input class="button" value="Voltar" type="button" name="btVoltar" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $ContaId ?>','conteudo')" style="width: 100px" />
		</td>
	</tr>
</table>
