<?php 
###########
## Módulo para alteracao de produtos avulsos da ordem de compra
## Criado: 28/03/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Recupera o id do produto
if($_POST["ProdutoId"]) 
{
	
	$ProdutoId = $_POST["ProdutoId"]; 
	$OrdemId = $_POST["OrdemId"];

} 

else 

{
  
	$ProdutoId = $_GET["ProdutoId"]; 
	$OrdemId = $_GET["OrdemId"]; 

}

//Monta o sql
$sql = "DELETE FROM ordem_compra_produto WHERE id = $ProdutoId";

//Executa a query
$resultado = mysql_query($sql);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Exclusão de Produto da Ordem de Compra</span>
					</td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">
						<table cellSpacing="0" cellPadding="0" width="520" border="0">
							<tr>
								<td style="padding-bottom: 2px">
									<input name="BtnVoltar" type="button" class="button" id="BtnVoltar" title="Retorna para a exibicao da OC" value="Voltar" onclick="wdCarregarFormulario('OrdemCompraExibe.php?OrdemId=<?php echo $OrdemId ?>','conteudo')" style="width: 110px">
								</td>
								<td width="36" align="right">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>  	 
		</td>
	</tr>
</table>
</form>