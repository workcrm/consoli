<?php 
###########
## Módulo para relatório dos lançamentos no caixa
## Criado: 09/06/2011 - Maycon Edinger
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
	
	//Captura o valor referente ao radio button do tipo de lancamento
	var edtTipoLancamento = document.getElementsByName('edtTipoLancamento');
   
	for (var i=0; i < edtTipoLancamento.length; i++) 
	{
		
		if (edtTipoLancamento[i].checked == true) 
		{
			
			edtTipoLancamento = edtTipoLancamento[i].value;
			break;
		}
	}
	
	if (edtTipoLancamento == 1)
	{
	
		edtContaCaixa = Form.cmbContaCaixaEntradaId.value;
		
	}
	
	else
	
	{
	
		edtContaCaixa = Form.cmbContaCaixaSaidaId.value;
		
	}
  
	//Captura o valor referente ao radio button do tipo de formato
	var edtTipoFormato= document.getElementsByName('edtFormato');
   
	for (var i=0; i < edtTipoFormato.length; i++) 
	{
		if (edtTipoFormato[i].checked == true) 
		{
		
			edtTipoFormato = edtTipoFormato[i].value;
			break;
		}
	}
	
	
	//Captura o valor referente ao radio button do da data de procura
	var edtDataProcura= document.getElementsByName('edtDataProcura');
   
	for (var i=0; i < edtDataProcura.length; i++) 
	{
		if (edtDataProcura[i].checked == true) 
		{
		
			edtDataProcura = edtDataProcura[i].value;
			break;
		}
	}
	
	//Verifica o tipo de formato
	if (edtTipoFormato == 1)
	{
	
		//Monta url que do relatório que será carregado	
		url = "./relatorios/ContaCaixaSinteticoRelatorioDataPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&TipoLancamento=" + edtTipoLancamento + "&ContaCaixaId=" + edtContaCaixa + "&DataProcura=" + edtDataProcura + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>";
  
	} 
	
	else if (edtTipoFormato == 2)
	{
	
		//Monta url que do relatório que será carregado	
		url = "./relatorios/ContaCaixaAnaliticoRelatorioDataPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&TipoLancamento=" + edtTipoLancamento + "&ContaCaixaId=" + edtContaCaixa + "&DataProcura=" + edtDataProcura + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>";
  
	
	}

	//Executa o relatório selecionado
	abreJanela(url);
	
}
</script>

<?php

	//Monta o SQL
	$lista_subgrupo_entrada = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '1' ORDER BY nome";
	//Executa a query
	$dados_subgrupo_entrada = mysql_query($lista_subgrupo_entrada);
	
	//Monta o SQL
	$lista_subgrupo_saida = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '2' ORDER BY nome";
	//Executa a query
	$dados_subgrupo_saida = mysql_query($lista_subgrupo_saida);

?>

<form id='consulta_data' name='consulta_data' method='post'>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='440'>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relatório de Lançamentos por Conta-Caixa</span>
					</td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">		
					</td>
				</tr>
			</table>
 	 	</td>
	</tr>
	<tr>
		<td style="PADDING-BOTTOM: 2px">
			<span >
				<input name='Button' type='button' class="button" id="consulta" title="Emite o relatório pelas datas informadas" value='Emitir Relatório' onclick="ExecutaConsulta()" />
			</span>
		</td>
	</tr>
	<tr>
		<td>

			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="middle"> 
						<table class='tabDetailView' cellspacing='0' cellpadding='0' width='100%' border='0'>
							<tr>
								<td class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colspan='21'>
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe o per&iacute;odo a pesquisar</td>
										</tr>
									</table>
								</td>
							</tr>			  
							<tr>
								<td class='dataLabel' width='65'>
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
									Tipo de Conta-Caixa:
								</td>
								<td colspan="3" style="padding-top: 8px;" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">								
										<tr valign="middle">
											<td width="170" height="20">
												<input name="edtTipoLancamento" type="radio" value="1" checked="checked" onclick="IDE = document.getElementById('CaixaEntrada'); IDE.style.display = 'none'; IDS = document.getElementById('CaixaSaida'); IDS.style.display = 'none'; IDE = document.getElementById('CaixaEntrada'); IDE.style.display = '';" />&nbsp;&nbsp;Contas-Caixa de Entrada
											</td>
											<td height="20">
												<input name="edtTipoLancamento" type="radio" value="2" onclick="IDE = document.getElementById('CaixaEntrada'); IDE.style.display = 'none'; IDS = document.getElementById('CaixaSaida'); IDS.style.display = 'none'; IDE = document.getElementById('CaixaEntrada'); IDS.style.display = '';" />&nbsp;&nbsp;Contas-Caixa de Saida
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Conta-caixa:</td>
								<td colspan="3" valign="middle" class="tabDetailViewDF">
									<div id="CaixaEntrada" name="CaixaEntrada">
									<select name="cmbContaCaixaEntradaId" id="cmbContaCaixaEntradaId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											//Monta o while para gerar o combo de escolha
											while ($lookup_subgrupo_entrada = mysql_fetch_object($dados_subgrupo_entrada)) 
											{ 
										?>
										<option value="<?php echo $lookup_subgrupo_entrada->id ?>"><?php echo $lookup_subgrupo_entrada->id . " - " . $lookup_subgrupo_entrada->nome ?></option>
										<?php } ?>
									</select>
									</div>
									<div id="CaixaSaida" name="CaixaSaida" style="display: none">
									<select name="cmbContaCaixaSaidaId" id="cmbContaCaixaSaidaId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											//Monta o while para gerar o combo de escolha
											while ($lookup_subgrupo_saida = mysql_fetch_object($dados_subgrupo_saida)) 
											{ 
										?>
										<option value="<?php echo $lookup_subgrupo_saida->id ?>"><?php echo $lookup_subgrupo_saida->id . " - " . $lookup_subgrupo_saida->nome ?></option>
										<?php } ?>
									</select>
									</div>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Formato do Relatorio:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="170" height="20">
												<input name="edtFormato" type="radio" value="1" checked="checked" />&nbsp;Sintético
											</td>
											<td height="20">
												<input name="edtFormato" type="radio" value="2" />&nbsp;Analítico
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Agrupar pelas Datas:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="170" height="20">
												<input name="edtDataProcura" type="radio" value="1" checked="checked" />&nbsp;Data Recebimento/Pagamento
											</td>
											<td width="170">
												<input name="edtDataProcura" type="radio" value="2" />&nbsp;Data de Emissão
											</td>
											<td height="20">
												<input name="edtDataProcura" type="radio" value="3" />&nbsp;Data de Vencimento
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