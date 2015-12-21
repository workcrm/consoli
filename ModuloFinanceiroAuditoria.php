<?php 
###########
## Módulo para relatório das auditorias (Bruna)
## Criado: 20/04/2011 - Maycon Edinger
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

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

?>
<script language="JavaScript">
function ExecutaConsulta() 
{
	
	var Form;
 	Form = document.consulta_data;
  
	if (Form.edtDataIni.value == 0) 
	{
		alert("É necessário Informar a Data Inicial !");
		Form.edtDataIni.focus();
		return false;
   	}
	if (Form.edtDataFim.value == 0) 
	{
		alert("É necessário Informar a Data Final !");
		Form.edtDataFim.focus();
		return false;
   	}
  
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
 
	//Captura o valor referente ao radio button do tipo de capa
	var edtTipoConsulta = document.getElementsByName('edtTipoConsulta');
   
	for (var i=0; i < edtTipoConsulta.length; i++) 
	{
		
		if (edtTipoConsulta[i].checked == true) 
		{
			
			edtTipoConsulta = edtTipoConsulta[i].value;
			break;
			
		}
	}
  
	//Captura o valor referente ao radio button do tipo de status
	var edtTipoStatus = document.getElementsByName('edtStatus');
   
	for (var i=0; i < edtTipoStatus.length; i++) 
	{
		if (edtTipoStatus[i].checked == true) 
		{
		
			edtTipoStatus = edtTipoStatus[i].value;
			break;
			
		}
	}
	
	//Captura o valor referente ao radio button do formato
	var edtTipoFormato = document.getElementsByName('edtFormato');
   
	for (var i=0; i < edtTipoFormato.length; i++) 
	{
		if (edtTipoFormato[i].checked == true) 
		{
		
			edtTipoFormato = edtTipoFormato[i].value;
			break;
			
		}
	}
   
   
	if (edtTipoFormato == 1)
	{
	
		//Monta url que do relatório que será carregado	
		url = "./relatorios/AuditoriaRelatorioDataComJurosPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>" + "&TipoConsulta=" + edtTipoConsulta + "&TipoStatus=" + edtTipoStatus;
  
	}
	
	if (edtTipoFormato == 2)
	{
	
		//Monta url que do relatório que será carregado	sem juros
		url = "./relatorios/AuditoriaRelatorioDataSemJurosPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>" + "&TipoConsulta=" + edtTipoConsulta + "&TipoStatus=" + edtTipoStatus;
  
	}
	
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
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relatório de Eventos para Auditoria</span>
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
			<span >
				<input name="Button" type="button" class="button" id="consulta" title="Emite o relatório de eventos pelas datas informadas" value='Emitir Relatório' onclick="ExecutaConsulta()" />
			</span>
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
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe o per&iacute;odo a pesquisar</td>
										</tr>
									</table>
								</td>
							</tr>			  
							<tr>
								<td class="dataLabel" width="65">
									In&iacute;cio:
								</td>
								<td width="107" class="tabDetailViewDF">
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
								<td width="61" class="dataLabel">T&eacute;rmino:</td>
								<td width="100" class="tabDetailViewDF">
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
								<td class="dataLabel">
									Tipo de Evento:
								</td>
								<td colspan="3" style="padding-top: 8px;" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">								
										<tr valign="middle">
											<td width="130" height="20">
												<input name="edtTipoConsulta" type="radio" value="0" checked="checked" />&nbsp;&nbsp;Todos
											</td>
											<td width="130" height="20">
												<input name="edtTipoConsulta" type="radio" value="1" />&nbsp;&nbsp;Eventos Sociais
											</td>
											<td width="130" height="20">
												<input name="edtTipoConsulta" type="radio" value="2" />&nbsp;&nbsp;Formaturas
											</td>
											<td height="20">
												<input name="edtTipoConsulta" type="radio" value="3" />&nbsp;&nbsp;Pregão/Edital
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Status:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="130" height="20">
												<input name="edtStatus" type="radio" value="4" checked="checked" />&nbsp;Todos
											</td>
											<td width="130" height="20">
												<input name="edtStatus" type="radio" value="0" />&nbsp;Em Orçamento
											</td>
											<td width="130" height="20">
												<input name="edtStatus" type="radio" value="1" />&nbsp;Em Aberto
											</td>
											<td width="130" height="20">
												<input name="edtStatus" type="radio" value="2" />&nbsp;Realizado
											</td>
											<td height="20">
												<input name="edtStatus" type="radio" value="3" />&nbsp;Não-Realizado
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Formato:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="130" height="20">
												<input name="edtFormato" type="radio" value="1" checked="checked" />&nbsp;Com Juros
											</td>
											<td height="20">
												<input name="edtFormato" type="radio" value="2" />&nbsp;Sem Juros
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