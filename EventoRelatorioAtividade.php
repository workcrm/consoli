<?php 
###########
## Módulo para relatório de atividade por evento
## Criado: 17/05/2012 - Maycon Edinger
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

//Efetua o lookup na tabela de Eventos
//Monta o SQL de pesquisa
$lista_origem = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_origem = mysql_query($lista_origem);


//Efetua o lookup na tabela de Atividades
//Monta o SQL de pesquisa
$lista_atividade = "SELECT id, descricao FROM atividades ORDER BY descricao";

//Executa a query
$dados_atividade = mysql_query($lista_atividade);


//Monta o lookup da tabela de regionais
//Monta o SQL
$lista_regiao = "SELECT * FROM regioes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_regiao = mysql_query($lista_regiao);

?>

<script language="JavaScript">
function ExecutaConsulta() 
{

	var Form;
 	Form = document.consulta_data;
  
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
   
	//Monta url que do relatório que será carregado	
	url = "./relatorios/EventoRelatorioAtividadePDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&edtUsuarioId=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>" + "&cmbEventoId=" + Form.cmbEventoId.value  + "&cmbAtividadeId=" + Form.cmbAtividadeId.value + "&TipoStatus=" + edtTipoStatus + "&cmbRegiaoId=" + Form.cmbRegiaoId.value;
  
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
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relatótio Atividades em Eventos</span>
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
			<input name="Button" type="button" class="button" id="consulta" title="Emite o relatório de atividades pelas datas informadas" value='Emitir Relatório' onclick="ExecutaConsulta()" />
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
								<td class="dataLabel" width="110">In&iacute;cio:</td>
								<td width="130" class="tabDetailViewDF">
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
								<td class="dataLabel">Evento:</td>
								<td colspan="3" style="padding-top: 8px;" class="tabDetailViewDF">
									<select name="cmbEventoId" id="cmbEventoId" style="width: 450px">
										<option value="0">-- Selecione uma opção --</option>    
										<?php 
												
											while ($lookup_origem = mysql_fetch_object($dados_origem)) 
											{ 
												
										?>
										<option value="<?php echo $lookup_origem->id ?>"><?php echo $lookup_origem->id . " - " . $lookup_origem->nome ?></option>
										<?php 
											
											} 
												
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Atividade:</td>
								<td colspan="3" style="padding-top: 8px;" class="tabDetailViewDF">
									<select name="cmbAtividadeId" id="cmbAtividadeId" style="width: 450px">
										<option value="0">-- Selecione uma opção --</option>    
										<?php 
												
											while ($lookup_atividade = mysql_fetch_object($dados_atividade)) 
											{ 
												
										?>
										<option value="<?php echo $lookup_atividade->id ?>"><?php echo $lookup_atividade->id . " - " . $lookup_atividade->descricao ?></option>
										<?php 
											
											} 
												
										?>
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
								<td valign="top" class="dataLabel">Status:</td>
								<td colspan="5" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="130" height="20">
												<input name="edtStatus" type="radio" value="3" checked="checked" />&nbsp;Todos
											</td>
											<td width="130" height="20">
												<input name="edtStatus" type="radio" value="0" />&nbsp;Em Aberto
											</td>
											<td width="130" height="20">
												<input name="edtStatus" type="radio" value="1" />&nbsp;Em Atraso
											</td>
											<td height="20">
												<input name="edtStatus" type="radio" value="2" />&nbsp;Concluido
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