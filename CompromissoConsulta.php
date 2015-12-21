<?php 
###########
## Módulo para Listagem dos compromissos por datas
## Criado: 27/01/2009 - Maycon Edinger
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
if (function_exists("DataMySQLRetornar") == false) {
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

?>
<script language="JavaScript">
function ExecutaConsulta() {
	var Form;
 	Form = document.consulta_data;
  
  if (Form.edtDataIni.value == 0) {
			alert("É necessário Informar a Data Inicial !");
			Form.edtDataIni.focus();
      return false;
   	}
		if (Form.edtDataFim.value == 0) {
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
	
		if (ano_inicial > ano_final){
			alert("A data inicial deve ser menor que a data final."); 
			data_inicial.focus();
			return false
		} else {
			if (ano_inicial == ano_final){
		 	if (mes_inicial > mes_final){
		  	alert("A data inicial deve ser menor que a data final.");
					data_final.focus();
					return false
				} else {
					if (mes_inicial == mes_final){
						if (dia_inicial > dia_final){
							alert("A data inicial deve ser menor que a data final.");
							data_final.focus();
							return false
						}
					}
				}
			}
		}  
  
	wdCarregarFormulario('CompromissoConsultaResultado.php?DataIni=' + Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value,'resultado_consulta','1');
	
}
</script>
<form id='consulta_data' name='consulta_data' method='post'>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
  	<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Consulta Compromissos por Data</span>
					</td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">		
					</td>
			  </tr>
			</table>
 	 	</td>
  </tr>
  <tr>
    <td style="PADDING-BOTTOM: 2px">
			<span >
      <input name='Button' type='button' class="button" id="consulta" title="Consulta os compromissos na data informada" value='Consultar Compromissos' onclick="ExecutaConsulta()" />
    	</span>
		</td>
  </tr>
  <tr>
    <td>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
		<td valign="middle"> 

				<TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
              <TR>
                <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='21'>
                  <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                      <TR>
                        <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe o per&iacute;odo a pesquisar</TD>
                      </TR>
                  </TABLE>
                </TD>
              </TR>
			  
			  <TR>
                <TD class='dataLabel' width='65'>
                  <SLOT>In&iacute;cio:</SLOT>
                </TD>
                <TD width="107" class=tabDetailViewDF>
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
                </TD>
                <TD width="61" class=dataLabel>T&eacute;rmino:</TD>
                <TD width="100" class=tabDetailViewDF>
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
                </TD>                
			  		</TR>              		
          </TABLE>

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
</form``>