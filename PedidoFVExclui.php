<?php
###########
## Módulo para Processar a exclusão de pedido do foto e vídeo
## Criado: 14/10/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$PedidoId = $_GET["PedidoId"];

//Apaga os itens do pedido
$sql2 = mysql_query("DELETE FROM pedido_fv_produtos WHERE pedido_id = $PedidoId");

//Apaga o pedido
$sql3 = mysql_query("DELETE FROM pedido_fv WHERE id = $PedidoId");

?>
<br/>
<br/>
<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
	<tr>
		<td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
			<img src='./image/bt_informacao.gif' border='0' />
		</td>
		<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
			<strong>Pedido do Foto e Vídeo excluido com sucesso !</strong>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding-top: 8px">
			<input class="button" value="Voltar" type="button" name="btVoltar" onclick="wdCarregarFormulario('ModuloPedidoFotoVideo.php?headers=1','conteudo')" style="width: 100px" />
		</td>
	</tr>
</table>
