<?php
###########
## Módulo de pesquisa para BOLETOS
## Criado: - 05/04/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';


$BoletoId = $_GET['BoletoId'];
$Sacado = $_GET['Sacado'];
$Endereco1 = $_GET['Endereco1'];
$Endereco2 = $_GET['Endereco2'];

//echo "<br/>BoletoId: " . $BoletoId;
//echo "<br/>Sacado: " . $Sacado;
//echo "<br/>Endereco1: " . $Endereco1;
//echo "<br/>Endereco2: " . $Endereco2;

//Monta a query para pegar os dados
$sql = "UPDATE boleto SET sacado = '$Sacado', endereco1 = '$Endereco1', endereco2 = '$Endereco2' WHERE id = '$BoletoId'";

//Executa a query
$query = mysql_query($sql);

?>
<br/>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td height="22" width="20" valign="top" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 3px; border-right: 0px">
			<img src="./image/bt_informacao.gif" border="0" />
		</td>
		<td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px; padding-top: 3px; padding-bottom: 4px">
			<strong>Dados do Sacado Atualiados com sucesso !</strong>                   
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
</table>