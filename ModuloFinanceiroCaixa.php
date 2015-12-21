<?php 
###########
## Módulo para relatório dos lançamentos no caixa
## Criado: 09/06/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Rotina para verificar se necessita ou não montar o header para o ajax
header("Content-Type: text/html; charset=ISO-8859-1",true);

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

//Monta o lookup da tabela de grupos
//Monta o SQL
$lista_grupo = "SELECT * FROM grupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_grupo = mysql_query($lista_grupo);


//Monta o lookup da tabela de regionais
//Monta o SQL
$lista_regiao = "SELECT * FROM regioes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_regiao = mysql_query($lista_regiao);


//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";
//Executa a query
$dados_eventos = mysql_query($lista_eventos);

?>
<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
	ID = document.getElementById(id);
	ID.style.display = "none";
}


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
		url = "./relatorios/CaixaConsolidadoRelatorioDataPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&TipoLancamento=" + edtTipoLancamento+ "&DataProcura=" + edtDataProcura + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&EventoId=" + document.getElementById('cmbEventoId').value + "&GrupoId=" + document.getElementById('cmbGrupoId').value + '&Regiao=' + document.getElementById('cmbRegiaoId').value;
  
	} 
	
	else if (edtTipoFormato == 2)
	{
	
		//Monta url que do relatório que será carregado	
		url = "./relatorios/CaixaSinteticoRelatorioDataPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&TipoLancamento=" + edtTipoLancamento+ "&DataProcura=" + edtDataProcura + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&EventoId=" + document.getElementById('cmbEventoId').value + "&GrupoId=" + document.getElementById('cmbGrupoId').value + '&Regiao=' + document.getElementById('cmbRegiaoId').value;
  
	
	}
	
	else if (edtTipoFormato == 3)
	{
	
		var opcao = Form.chkAgrupar.checked;
		
		if (opcao == true)
		{
			
			//Monta url que do relatório que será carregado	
			url = "./relatorios/CaixaAnaliticoRelatorioDataPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&TipoLancamento=" + edtTipoLancamento + "&DataProcura=" + edtDataProcura + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&EventoId=" + document.getElementById('cmbEventoId').value + "&GrupoId=" + document.getElementById('cmbGrupoId').value + '&Regiao=' + document.getElementById('cmbRegiaoId').value;
  	
		}
		
		else
		
		{
			
			//Monta url que do relatório que será carregado	
			url = "./relatorios/CaixaAnaliticoAgrupadoRelatorioDataPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&TipoLancamento=" + edtTipoLancamento + "&DataProcura=" + edtDataProcura + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&EventoId=" + document.getElementById('cmbEventoId').value + "&GrupoId=" + document.getElementById('cmbGrupoId').value + '&Regiao=' + document.getElementById('cmbRegiaoId').value;
		
		}
	
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
					<td>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relatório de Lançamentos no Caixa</span>
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
		<td style="padding-bottom: 2px">
			<span >
				<input name="Button" type="button" class="button" id="consulta" title="Emite o relatório pelas datas informadas" value='Emitir Relatório' onclick="ExecutaConsulta()" />
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
								<td class="listViewPaginationTdS1" style="padding-right: 0px; padding-left: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
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
								<td class="dataLabel">Tipo de Lancamento:</td>
								<td colspan="3" style="padding-top: 8px;" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">								
										<tr valign="middle">
											<td width="130" height="20">
												<input name="edtTipoLancamento" type="radio" value="0" checked="checked" />&nbsp;&nbsp;Todos
											</td>
											<td width="150" height="20">
												<input name="edtTipoLancamento" type="radio" value="1" />&nbsp;&nbsp;Somente Entradas
											</td>
											<td height="20">
												<input name="edtTipoLancamento" type="radio" value="2" />&nbsp;&nbsp;Somente Saidas
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Evento:</td>
								<td colspan="3" class="tabDetailViewDF">
									<select name="cmbEventoId" id="cmbEventoId" style="width: 400px">                  
										<option value="0">**** Todos os Eventos ****</option>
										<?php 
											//Cria o componente de lookup de eventos
											while ($lookup_eventos = mysql_fetch_object($dados_eventos)) 
											{ 
										?>
										<option value="<?php echo $lookup_eventos->id ?>"><?php echo $lookup_eventos->id . " - " . $lookup_eventos->nome ?></option>
										<?php 
											//Fecha o while
											} 
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Centro de Custo:</td>
								<td colspan="3" valign="middle" class="tabDetailViewDF">
									<select name="cmbGrupoId" id="cmbGrupoId" style="width:350px">
										<option value="0">**** Todos os Centros de Custo ****</option>
										<?php 
											//Monta o while para gerar o combo de escolha
											while ($lookup_grupo = mysql_fetch_object($dados_grupo)) { 
										?>
										<option value="<?php echo $lookup_grupo->id ?>"><?php echo $lookup_grupo->id . " - " . $lookup_grupo->nome ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Região:</td>
								<td colspan="3" valign="middle" class="tabDetailViewDF">
									<select name="cmbRegiaoId" id="cmbRegiaoId" style="width:350px">
										<option value="0">**** Todas as Regiões ****</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_regiao = mysql_fetch_object($dados_regiao)) 
											{ 
											
										?>
										<option value="<?php echo $lookup_regiao->id ?>"><?php echo $lookup_regiao->id . " - " . $lookup_regiao->nome ?></option>
										<?php 
										
											} 
										
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Formato do Relatorio:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="130" height="20">
												<input name="edtFormato" type="radio" value="1" checked="checked" onclick="oculta(998);" />&nbsp;Consolidado
											</td>
											<td width="150" height="20">
												<input name="edtFormato" type="radio" value="2" onclick="oculta(998);" />&nbsp;Sintético
											</td>
											<td height="20">
												<input name="edtFormato" type="radio" value="3" onclick="oculta(998); change(998)" />&nbsp;Analítico
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr id="998" style="display: none">
								<td valign="top" class="dataLabel">Agrupamento:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<input name="chkAgrupar" type="checkbox" id="chkAgrupar" value="1" checked="checked">&nbsp;<span style="font-size: 11px">Agrupar por Centro de Custo</span>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Agrupar pelas Datas:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="210" height="20">
												<input name="edtDataProcura" type="radio" value="1" checked="checked" />&nbsp;Data Recebimento/Pagamento
											</td>
											<td width="210">
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