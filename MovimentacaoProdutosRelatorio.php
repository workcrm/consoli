<?php 
###########
## Módulo para relatório de movimentacao dos produtos
## Criado: 09/02/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
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

//Efetua o lookup na tabela de produtos
//Monta o sql de pesquisa
$lista_produtos = "SELECT id, nome FROM item_evento WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_produtos = mysql_query($lista_produtos);

//Efetua o lookup na tabela de categorias
//Monta o SQL de pesquisa
$lista_categoria = "SELECT * FROM categoria_item WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_categoria = mysql_query($lista_categoria);

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");  
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

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
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relatótio de Movimentação de Produtos</span>
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
			<input name="Button" type="button" class="button" id="tela" value='Visualizar na Tela' onclick="ExecutaConsulta(1)" />
			<input name="Button" type="button" class="button" id="consulta" value='Emitir Relatório' onclick="ExecutaConsulta(2)" />
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
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados para emissão do relatório</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="70">Produto:</td>
								<td colspan="3" class="tabDetailViewDF">
									<select name="cmbItemId" id="cmbItemId" style="width:350px">
										<option value="0">--- Todos os Produtos ---</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_produtos = mysql_fetch_object($dados_produtos)) 
											{ 
											
										?>
										<option value="<?php echo $lookup_produtos->id ?>"><?php echo $lookup_produtos->id . " - " . $lookup_produtos->nome ?></option>
										<?php 
										
											} 
										
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="70">Categoria:</td>
								<td colspan="3" class="tabDetailViewDF">
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
							<tr>
								<td class='dataLabel'>In&iacute;cio:</td>
								<td width="107" class=tabDetailViewDF>
									<?php
										//Define a data do formulário
										$objData->strFormulario = "consulta_data";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataIni";
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = "";
										//Define o tamanho do campo 
										//$objData->intTamanho = 15;
										//Define o número maximo de caracteres
										//$objData->intMaximoCaracter = 20;
										//define o tamanho da tela do calendario
										//$objData->intTamanhoCalendario = 200;
										//Cria o componente com seu calendario para escolha da data
										$objData->CriarData();
									?>
								</td>
								<td width="61" class=dataLabel>T&eacute;rmino:</td>
								<td width="100" class=tabDetailViewDF>
									<?php
										//Define a data do formulário
										$objData->strFormulario = "consulta_data";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataFim";
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = "";
										//Define o tamanho do campo 
										//$objData->intTamanho = 15;
										//Define o número maximo de caracteres
										//$objData->intMaximoCaracter = 20;
										//define o tamanho da tela do calendario
										//$objData->intTamanhoCalendario = 200;
										//Cria o componente com seu calendario para escolha da data
										$objData->CriarData();
									?>
								</td>                
							</tr>
							<tr>
								<td class='dataLabel'>Movimentação</td>
								<td colspan="3" class=tabDetailViewDF>
									<table width="530" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="110">
												<input type="radio" name="edtMovimentacao" value="0" checked/> Todas
											</td>
											<td width="110">
												<input type="radio" name="edtMovimentacao" value="1" /> Entradas
											</td>
											<td width="110">
												<input type="radio" name="edtMovimentacao" value="2" /> Saídas
											</td>						
										</tr>
									</table>		  				
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