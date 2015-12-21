<?php
###########
## Módulo para Pesquisa de Ordem de Compra
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{
	
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";

}

//Monta o lookup da tabela de departamentos
//Monta o SQL
$lista_departamento = "SELECT * FROM departamentos WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";
//Executa a query
$dados_departamento = mysql_query($lista_departamento);

//Monta o lookup da tabela de fornecedores
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//Efetua o lookup na tabela de eventos
$lista_evento = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_evento = mysql_query($lista_evento);
?>

<script language="JavaScript">
function EfetuaConsulta() 
{
	
	var Form;
	Form = document.frmOrdemCompraPesquisa;

	if (Form.edtDataIni.value == 0) 
	{
		
		if (Form.edtDataFim.value != 0) 
		{
			alert('É necessário informar a data inicial !');
			Form.edtDataIni.focus();
			return false;
   		}			
   	
	}

	if (Form.edtDataFim.value == 0) 
	{
		
		if (Form.edtDataIni.value != 0) 
		{
			
			
			alert('É necessário informar a data final !');
			Form.edtDataFim.focus();
			return false;
   		
		}			
   	
	}		
		
	if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) 
	{
				
		// Verifica se data final é maior que a data inicial
		var data_inicial = Form.edtDataIni;
		var data_final = Form.edtDataFim;
			
		//Aplica a validação das datas informadas	
		dia_inicial      = data_inicial.value.substr(0,2);
		dia_final        = data_final.value.substr(0,2);
		mes_inicial      = data_inicial.value.substr(3,2);
		mes_final        = data_final.value.substr(3,2);
		ano_inicial      = data_inicial.value.substr(6,4);
		ano_final        = data_final.value.substr(6,4);
			
		if (ano_inicial > ano_final)
		{
			
			alert("A data inicial deve ser menor que a data final."); 
			data_inicial.focus();
			return false
		
		} 
		
		else 
		
		{
			
			if (ano_inicial == ano_final)
			{
				
				if (mes_inicial > mes_final)
				{
				  	
					alert("A data inicial deve ser menor que a data final.");
					data_final.focus();
					return false
				
				} 
				
				else 
				
				{
					
					if (mes_inicial == mes_final)
					{
						
						if (dia_inicial > dia_final)
						{
							
							alert("A data inicial deve ser menor que a data final.");
							data_final.focus();
							return false
						
						}
					
					}
						
				}
			
			}
				
		}
	
	} 
   
	urlPesquisa = "ModuloOrdemCompraLista.php?edtPesquisa=" + Form.edtPesquisa.value + "&DepartamentoId=" + Form.cmbDepartamentoId.value + "&FornecedorId=" + Form.cmbFornecedorId.value + "&EventoId=" + Form.cmbEventoId.value + "&edtDataIni=" + Form.edtDataIni.value + "&edtDataFim=" + Form.edtDataFim.value + "&edtDataEntregaIni=" + Form.edtDataEntregaIni.value + "&edtDataEntregaFim=" + Form.edtDataEntregaFim.value;
	
	wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');
	
	return true;
}

</script>

<form id="form" name="frmOrdemCompraPesquisa" action="#">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Pesquisar Ordens de Compra</span>
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
		<td>
			<input class="button" title="Inicia a pesquisa da(s) ordem(ns) de compra com base nos valores informados" name="btnPesquisa" type="button" id="btnPesquisa" value="Pesquisar Ordens de Compra" onclick="EfetuaConsulta()"></span>
			<table width="100%" id="2" cellpadding="0" cellspacing="0" border="0" style="margin-top: 4px">
				<tr>
					<td>
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="text-align: left"><img src="image/bt_pesquisar.gif"> Informe os dados da Ordem de Compra e clique em [Pesquisar Ordens de Compra] </td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="110">Número da OC:</td>
								<td colspan="3" class="tabDetailViewDF">
									<input name="edtPesquisa" type="text" class="datafield" id="edtPesquisa"  maxlength="8" style="width: 80px" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"/>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Departamento:</td>
								<td colspan="3" class="tabDetailViewDF">
									<select name="cmbDepartamentoId" id="cmbDepartamentoId" style="width:350px">
										<option value="0">--- Todos os Departamentos ---</option>
										<?php 
										 
											//Monta o while para gerar o combo de escolha
											while ($lookup_departamento = mysql_fetch_object($dados_departamento)) 
											{ 
										
										?>
										<option value="<?php echo $lookup_departamento->id ?>"><?php echo $lookup_departamento->id . ' - ' . $lookup_departamento->nome ?> </option>
										<?php 
										
											} 
											
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Fornecedor:</td>
								<td colspan="3" class="tabDetailViewDF">
									<select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
										<option value="0">--- Todos os Fornecedores ---</option>
										<?php 
										 
											//Monta o while para gerar o combo de escolha
											while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) 
											{ 
										
										?>
										<option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . ' - ' . $lookup_fornecedor->nome ?> </option>
										<?php 
										
											} 
											
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Evento:</td>
								<td colspan="3" class="tabDetailViewDF">
									<select name="cmbEventoId" id="cmbEventoId" style="width:350px">
										<option value="0">--- Todos os Eventos ---</option>
										<?php 
										 
											//Monta o while para gerar o combo de escolha
											while ($lookup_evento = mysql_fetch_object($dados_evento)) 
											{ 
										
										?>
										<option value="<?php echo $lookup_evento->id ?>"><?php echo $lookup_evento->id . ' - ' . $lookup_evento->nome ?> </option>
										<?php 
										
											} 
											
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Emissão Início:</td>
								<td width="160" class="tabDetailViewDF">
									<?php
										
										//Adiciona o acesso a entidade de criação do componente data
										include_once("CalendarioPopUp.php");  
										//Cria um objeto do componente data
										$objData = new tipData();
										//Define que não deve exibir a hora no calendario
										$objData->bolExibirHora = false;
										//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
										$objData->MontarJavaScript();
			
										//Define a data do formulário
										$objData->strFormulario = "frmOrdemCompraPesquisa";  
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
								<td class="dataLabel" width="70">Término:</td>
								<td class="tabDetailViewDF">
									<?php
								    
										//Define a data do formulário
										$objData->strFormulario = "frmOrdemCompraPesquisa";  
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
								<td class="dataLabel">Entrega Início:</td>
								<td width="160" class="tabDetailViewDF">
									<?php
										
										//Adiciona o acesso a entidade de criação do componente data
										include_once("CalendarioPopUp.php");  
										//Cria um objeto do componente data
										$objData = new tipData();
										//Define que não deve exibir a hora no calendario
										$objData->bolExibirHora = false;
										//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
										$objData->MontarJavaScript();
			
										//Define a data do formulário
										$objData->strFormulario = "frmOrdemCompraPesquisa";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataEntregaIni";
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
								<td class="dataLabel" width="70">Término:</td>
								<td class="tabDetailViewDF">
									<?php
								    
										//Define a data do formulário
										$objData->strFormulario = "frmOrdemCompraPesquisa";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataEntregaFim";
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
						</table>		 
						<br/>
					</td>
				</tr> 	
				<tr>
				 	<td>
						<div id="retornopesquisa"></div>
					</td>
				</tr>
			</table>	
		</td>
	</tr>
</table>

</form>