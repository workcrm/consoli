<?php 
###########
## Módulo para geracao de etiquetas dos itens
## Criado: 13/01/2012 - Maycon Edinger
##########

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

$edtProdutoId = $_GET["ProdutoId"];

//Monta o lookup da tabela de categorias de item
//Monta o SQL
$sql = "SELECT
		ite.id,
		ite.nome,
		ite.localizacao_1,
		ite.localizacao_2,
		ite.localizacao_3
		FROM item_evento ite
		WHERE ite.id = $edtProdutoId";

//Executa a query
$resultado = mysql_query($sql);

//Monta o array dos campos
$dados = mysql_fetch_array($resultado);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="380" border="0" align="left" cellpadding="0" cellspacing="0" class="text" style="margin: 8px; border-bottom: 1px dashed">
	<tr>
		<td class="text" valign="top">
			<img src='image/logo_consoli2_pq.jpg' width="70">
			<br/>
			<span style='font-size: 8px;'>Produto: <?php echo $dados["id"] ?></span></b>
			<br/>
			<b><span style='font-size: 16px; margin-top: 6px'><?php echo $dados["nome"] ?></span></b> 
			<br/>
			<b><span style='font-size: 12px;'>Localização: <?php echo $dados["localizacao_1"] . ' / ' . $dados["localizacao_2"] . ' / ' . $dados["localizacao_3"] ?></span></b> 
		</td>
		<td width='170' align='right' valign='bottom'>
			<img src='CodigoBarrasGera.php?CodigoId=<?php echo $dados["id"] ?>' width="150">
		</td>
	</tr>
</table>

<script language="javascript">
  	window.print();
</script>