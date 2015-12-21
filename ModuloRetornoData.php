<?php 
###########
## M�dulo para relat�rio dos retornos por data
## Criado: 03/02/2011 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Rotina para verificar se necessita ou n�o montar o header para o ajax
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Verifica se a func�o j� foi declarada
if (function_exists("DataMySQLRetornar") == false) {
	//Inclui o arquivo para manipula��o de datas
	include "./include/ManipulaDatas.php";
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

?>
<script language="JavaScript">
function wdVisualizarRelatorio() 
{
	
  var Form;
	Form = document.consulta_data;
  
  if (Form.edtDataIni.value == 0) {
			alert("� necess�rio Informar a Data Inicial !");
			Form.edtDataIni.focus();
      return false;
   	}
		if (Form.edtDataFim.value == 0) {
			alert("� necess�rio Informar a Data Final !");
			Form.edtDataFim.focus();
      return false;
   	}
  
		// Verifica se data final � maior que a data inicial
		var data_inicial = Form.edtDataIni;
		var data_final = Form.edtDataFim;
	
		//Aplica a valida��o das datas informadas	
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
  
  //Monta a url a acessar	 
  var urlCarrega = "ModuloRetornoDataLista.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>" + "&TipoConsulta=" + edtTipoConsulta;

  //Acessa a listagem das contas
  wdCarregarFormulario(urlCarrega,'resultado');		

}


//*** SE FOR IMPRESS�O
function wdCarregarRelatorio() 
{

  var Form;
	Form = document.consulta_data;
  
  if (Form.edtDataIni.value == 0) {
			alert("� necess�rio Informar a Data Inicial !");
			Form.edtDataIni.focus();
      return false;
   	}
		if (Form.edtDataFim.value == 0) {
			alert("� necess�rio Informar a Data Final !");
			Form.edtDataFim.focus();
      return false;
   	}
  
		// Verifica se data final � maior que a data inicial
		var data_inicial = Form.edtDataIni;
		var data_final = Form.edtDataFim;
	
		//Aplica a valida��o das datas informadas	
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
		
  //Monta a url do relat�rio		
  var urlRelatorio = "./relatorios/RetornoDataRelatorioPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>" + "&TipoConsulta=" + edtTipoConsulta;

  //Executa o relat�rio
  abreJanela(urlRelatorio);

}
</script>

<form id='consulta_data' name='consulta_data' method='post'>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
  	<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Retornos Processados por Data</span>
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
    <td style="padding-bottom: 4px">
  		<input class="button" title="Visualiza os retornos por data" name="btnVisualizar" type="button" id="btnVisualizar" value="Visualizar na Tela" style="width:100px" onclick="wdVisualizarRelatorio()" />
  		<input class="button" title="Emite o relat�rio dos retornos por data" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relat�rio" style="width:100px" onclick="wdCarregarRelatorio()" />	   	   		   		
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
					    //Adiciona o acesso a entidade de cria��o do componente data
							include_once("CalendarioPopUp.php");  
							//Cria um objeto do componente data
							$objData = new tipData();
							//Define que n�o deve exibir a hora no calendario
							$objData->bolExibirHora = false;
							//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
							$objData->MontarJavaScript();

							//Define a data do formul�rio
					    $objData->strFormulario = "consulta_data";  
					    //Nome do campo que deve ser criado
					    $objData->strNome = "edtDataIni";
					    //Valor a constar dentro do campo (p/ altera��o)
					    $objData->strValor = "";
					    //Define o tamanho do campo 
					    //$objData->intTamanho = 15;
					    //Define o n�mero maximo de caracteres
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
					    //Define a data do formul�rio
					    $objData->strFormulario = "consulta_data";  
					    //Nome do campo que deve ser criado
					    $objData->strNome = "edtDataFim";
					    //Valor a constar dentro do campo (p/ altera��o)
					    $objData->strValor = "";
					    //Define o tamanho do campo 
					    //$objData->intTamanho = 15;
					    //Define o n�mero maximo de caracteres
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
	          Tipo de Retorno:
	        </td>
          <td colspan="3" style="padding-top: 8px;" class="tabDetailViewDF">
            <table width="100%" cellpadding="0" cellspacing="0">
               <tr valign="middle">
                 <td width="160" height="20">
                   <input name="edtTipoConsulta" type="radio" value="0" checked="checked" />&nbsp;&nbsp;Todos
                 </td>
                 <td width="160" height="20">
                   <input name="edtTipoConsulta" type="radio" value="1" />&nbsp;&nbsp;Processados
                 </td>
                 <td width="190" height="20">
                   <input name="edtTipoConsulta" type="radio" value="2" />&nbsp;&nbsp;N�o Processado - Juros
                 </td>
                 <td height="20">
                   <input name="edtTipoConsulta" type="radio" value="3" />&nbsp;&nbsp;N�o Encontrados
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
			<div id="resultado">
			
			</div>
		</td>
	</tr>
</table>
</form>