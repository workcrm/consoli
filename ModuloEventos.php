<?php
###########
## Módulo para Pesquisa de eventos
## Criado: - 22/05/2007 - Maycon Edinger
## Alterado: 13/06/2007 - Maycon Edinger
## Alterações: 
## 13/06/2007 - Implementado a opção de exibir somente eventos em aberto, concluídos ou todos
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
?>

<script language="JavaScript">
function EfetuaConsulta() 
{
	
	var Form;
	Form = document.frmContasPesquisa;
   
	if (Form.edtPesquisa.value.length == 0) 
	{
		
		alert("É necessário informar um argumento de pesquisa !");
		Form.edtPesquisa.focus();
		return false;
  
	}

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

	//Captura o valor referente ao radio button de visualização
	var edtVisualizarValor = document.getElementsByName('edtVisualizar');   
	
	for (var i=0; i < edtVisualizarValor.length; i++) 
	{
    
		if (edtVisualizarValor[i].checked == true) 
		{
			
			edtVisualizarValor = edtVisualizarValor[i].value;
			break;
		
		}
	
	}
   
	//Captura o valor referente ao radio button de financeiro
	var edtFinanceiroValor = document.getElementsByName('edtFinanceiro');   
	
	for (var i=0; i < edtFinanceiroValor.length; i++) 
	{
		
		if (edtFinanceiroValor[i].checked == true) 
		{
			
			edtFinanceiroValor = edtFinanceiroValor[i].value;
			break;
		
		}
	
	}   

	//Captura o valor referente ao radio button de ordenação
	var edtOrdenarValor = document.getElementsByName('edtOrdenar');   
	
	for (var i=0; i < edtOrdenarValor.length; i++) 
	{
		
		if (edtOrdenarValor[i].checked == true) 
		{
		
			edtOrdenarValor = edtOrdenarValor[i].value;
			break;
     
		}
	
	}

	//Captura o valor referente ao radio button de classificação
	var edtClassificarValor = document.getElementsByName('edtClassificar');   
	
	for (var i=0; i < edtClassificarValor.length; i++) 
	{
     
		if (edtClassificarValor[i].checked == true) 
		{
		
			edtClassificarValor = edtClassificarValor[i].value;
			break;
		
		}
	
	}
   
	urlPesquisa = "ModuloEventosLista.php?edtPesquisa=" + Form.edtPesquisa.value + "&edtVisualizar=" + edtVisualizarValor + "&edtFinanceiro=" + edtFinanceiroValor + "&edtOrdenar=" + edtOrdenarValor + "&edtClassificar=" + edtClassificarValor + "&edtDataIni=" + Form.edtDataIni.value + "&edtDataFim=" + Form.edtDataFim.value;
	
	wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');
	
	return true;
}

function EfetuaPesquisa(Letra) 
{
	
	var Form;
	Form = document.frmContasPesquisa;

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

	//Captura o valor referente ao radio button de visualização
	var edtVisualizarValor = document.getElementsByName('edtVisualizar');   
	
	for (var i=0; i < edtVisualizarValor.length; i++) 
	{
		
		if (edtVisualizarValor[i].checked == true) 
		{
			
			edtVisualizarValor = edtVisualizarValor[i].value;
			break;
		
		}
	}
   
	//Captura o valor referente ao radio button de financeiro
	var edtFinanceiroValor = document.getElementsByName('edtFinanceiro');   
	
	for (var i=0; i < edtFinanceiroValor.length; i++) 
	{
		
		if (edtFinanceiroValor[i].checked == true) 
		{
			
			edtFinanceiroValor = edtFinanceiroValor[i].value;
			break;
		
		}
	
	}   

	//Captura o valor referente ao radio button de ordenação
	var edtOrdenarValor = document.getElementsByName('edtOrdenar');   
	
	for (var i=0; i < edtOrdenarValor.length; i++) 
	{
		
		if (edtOrdenarValor[i].checked == true) 
		{
			
			edtOrdenarValor = edtOrdenarValor[i].value;
			break;
		
		}
	
	}

	//Captura o valor referente ao radio button de classificação
	var edtClassificarValor = document.getElementsByName('edtClassificar');   
	
	for (var i=0; i < edtClassificarValor.length; i++) 
	{
		
		if (edtClassificarValor[i].checked == true) 
		{
		
			edtClassificarValor = edtClassificarValor[i].value;
			break;
		
		}
	
	}
	
	urlPesquisa = "ModuloEventosLista.php?ChaveFiltragem=" + Letra  + "&edtVisualizar=" + edtVisualizarValor + "&edtFinanceiro=" + edtFinanceiroValor + "&edtOrdenar=" + edtOrdenarValor + "&edtClassificar=" + edtClassificarValor + "&edtDataIni=" + Form.edtDataIni.value + "&edtDataFim=" + Form.edtDataFim.value;
	
	wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');
	return true;

}
</script>

<form id="form" name="frmContasPesquisa" action="#">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Pesquisar Eventos</span>
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
			<table width="100%" id="2" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_pesquisar.gif"> Informe o nome do Evento a pesquisar e clique em [Pesquisar Eventos] </td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="70">Pesquisar:</td>
								<td colspan="3" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="505" height="20">
												<input name="edtPesquisa" type="text" class="datafield" id="edtPesquisa"  size="65" maxlength="50" />
											</td>
											<td><span style="PADDING-BOTTOM: 2px">
												<input class="button" title="Inicia a pesquisa do(s) eventos(s) com base nos valores informados" name="btnPesquisa" type="button" id="btnPesquisa" value="Pesquisar Eventos" onclick="EfetuaConsulta()"></span></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Visualizar:</td>
								<td colspan="3" class="tabDetailViewDF">
									<table cellpadding="0" cellspacing="0" >
										<tr valign="middle">
											<td width="130">
												<input name="edtVisualizar" type="radio" value="1" checked> Todos os Eventos 
											</td>
											<td width="140">
												<input type="radio" name="edtVisualizar" value="3"> Eventos em Aberto 
											</td>
											<td width="140">
												<input type="radio" name="edtVisualizar" value="4"> Eventos Realizados 
											</td>
											<td>
												<input type="radio" name="edtVisualizar" value="5"> Eventos Não-Realizados 
											</td>
										</tr>
									</table>
								</td>
							</tr>		  			
							<tr>
								<td class="dataLabel">Financeiro:</td>
								<td colspan="3" class="tabDetailViewDF">
									<table cellpadding="0" cellspacing="0" >
										<tr valign="middle">
											<td width="130">
												<input name="edtFinanceiro" type="radio" value="0" checked> Qualquer
											</td>
											<td width="140">
												<input type="radio" name="edtFinanceiro" value="1"> A Receber 
											</td>
											<td width="140">
												<input type="radio" name="edtFinanceiro" value="2"> Recebido 
											</td>
											<td>
												<input type="radio" name="edtFinanceiro" value="3"> Cortesia 
											</td>
										</tr>
									</table>
								</td>
							</tr>			  			
							<tr>
								<td class="dataLabel">Ordenar:</td>
								<td colspan="3" class="tabDetailViewDF">
									<table cellpadding="0" cellspacing="0" >
										<tr valign="middle">
											<td width="130">
												<input name="edtOrdenar" type="radio" value="1" checked> Por Data 
											</td>
											<td width="250">
												<input name="edtOrdenar" type="radio" value="2"> Por Título do Evento 
											</td>											
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Classificar:</td>
								<td colspan="3" class="tabDetailViewDF">
									<table cellpadding="0" cellspacing="0" >
										<tr valign="middle">
											<td width="130">
												<input name="edtClassificar" type="radio" value="1" checked> Ascendente (A-Z)
											</td>
											<td width="250">
												<input name="edtClassificar" type="radio" value="2"> Descendente (Z-A)
											</td>											
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Início:</td>
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
										$objData->strFormulario = "frmContasPesquisa";  
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
								<td width="380" class="tabDetailViewDF">
									<?php
								    
										//Define a data do formulário
										$objData->strFormulario = "frmContasPesquisa";  
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
								<td class="dataLabel">Iniciar em:</td>
								<td colspan="3" class="tabDetailViewDF">
									<span style="PADDING-BOTTOM: 2px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnA" type="button" id="btnA" value="A" onClick="EfetuaPesquisa('A')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnB" type="button" id="btnB" value="B" onClick="EfetuaPesquisa('B')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnC" type="button" id="btnC" value="C" onClick="EfetuaPesquisa('C')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnD" type="button" id="btnD" value="D" onClick="EfetuaPesquisa('D')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnE" type="button" id="btnE" value="E" onClick="EfetuaPesquisa('E')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnF" type="button" id="btnF" value="F" onClick="EfetuaPesquisa('F')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnG" type="button" id="btnG" value="G" onClick="EfetuaPesquisa('G')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnH" type="button" id="btnH" value="H" onClick="EfetuaPesquisa('H')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnI" type="button" id="btnI" value="I" onClick="EfetuaPesquisa('I')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnJ" type="button" id="btnJ" value="J" onClick="EfetuaPesquisa('J')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnK" type="button" id="btnK" value="K" onClick="EfetuaPesquisa('K')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnL" type="button" id="btnL" value="L" onClick="EfetuaPesquisa('L')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnM" type="button" id="btnM" value="M" onClick="EfetuaPesquisa('M')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnN" type="button" id="btnN" value="N" onClick="EfetuaPesquisa('N')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnO" type="button" id="btnO" value="O" onClick="EfetuaPesquisa('O')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnP" type="button" id="btnP" value="P" onClick="EfetuaPesquisa('P')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnQ" type="button" id="btnQ" value="Q" onClick="EfetuaPesquisa('Q')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnR" type="button" id="btnR" value="R" onClick="EfetuaPesquisa('R')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnS" type="button" id="btnS" value="S" onClick="EfetuaPesquisa('S')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnT" type="button" id="btnT" value="T" onClick="EfetuaPesquisa('T')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnU" type="button" id="btnU" value="U" onClick="EfetuaPesquisa('U')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnV" type="button" id="btnV" value="V" onClick="EfetuaPesquisa('V')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnW" type="button" id="btnW" value="W" onClick="EfetuaPesquisa('W')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnX" type="button" id="btnX" value="X" onClick="EfetuaPesquisa('X')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnY" type="button" id="btnY" value="Y" onClick="EfetuaPesquisa('Y')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos iniciando com a letra selecionada" name="btnZ" type="button" id="btnZ" value="Z" onClick="EfetuaPesquisa('Z')" style="width:18px">
										<input class="button" title="Exibe todos os Eventos" name="btnTodos" type="button" id="btnTodos" value="Exibir Todos" onClick="EfetuaPesquisa('todos')" style="width:85px">
									</span>
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