<?php 
###########
## Módulo para relatório dos produtos
## Criado: 09/02/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
## Exibir a listagem de compromissos com 7 dias de antecedência
###########

//Rotina para verificar se necessita ou não montar o header para o ajax
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{

	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";

}

//Efetua o lookup na tabela de categorias
//Monta o SQL de pesquisa
$lista_categoria = "SELECT * FROM categoria_item WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_categoria = mysql_query($lista_categoria);

?>

<script language="JavaScript">
function ExecutaConsulta() 
{

	var Form;
 	Form = document.consulta_data;
   
	//Monta url que do relatório que será carregado	
	url = "./relatorios/ProdutoEventoRelatorioPDF.php?CategoriaId=" + Form.cmbCategoriaId.value + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>";
  
	//Executa o relatório selecionado
	abreJanela(url);
	
}

</script>

<form id="consulta_data" name="consulta_data" method="post">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440">
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relatótio de Produtos</span>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">		
					</td>
				</tr>
			</table>
 	 	</td>
	</tr>
	<tr>
		<td style="PADDING-BOTTOM: 2px">
			<input name="Button" type="button" class="button" id="consulta" value='Emitir Relatório' onclick="ExecutaConsulta()" />
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="middle"> 
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Caso desejar, selecione uma categoria para impressão</td>
										</tr>
									</table>
								</td>
							</tr>			  
							<tr>
								<td class="tabDetailViewDF">
									<select name="cmbCategoriaId" id="cmbCategoriaId" style="width: 360px">    
										<option value="0">--- Todas as Categorias ---</option>
										<?php 
											
											while ($lookup_categoria = mysql_fetch_object($dados_categoria)) 
											{
											
										?>
										<option value="<?php echo $lookup_categoria->id ?>"><?php echo $lookup_categoria->nome ?>				 </option>
										<?php 
											
											}
											
										?>
									</select>	
								</td>                
							</tr>              		
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<div id="resultado_consulta">
		
			</div>
		</td>
	</tr>
</table>
</form>