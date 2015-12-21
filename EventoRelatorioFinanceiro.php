<?php 
###########
## M�dulo para relat�rio dos eventos por posi�ao financeira
## Criado: 07/12/2009 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Rotina para verificar se necessita ou n�o montar o header para o ajax
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
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
function ExecutaConsulta() {
	var Form;
 	Form = document.consulta_data;
  
  //Captura o valor referente ao radio button de financeiro
  var edtFinanceiroValor = document.getElementsByName('edtFinanceiro');   
  
  for (var i=0; i < edtFinanceiroValor.length; i++) {
    if (edtFinanceiroValor[i].checked == true) {
      edtFinanceiroValor = edtFinanceiroValor[i].value;
      break;
    }
  }
  
  //Monta url que do relat�rio que ser� carregado	
	url = "./relatorios/EventoRelatorioFinanceiroPDF.php?TipoFin=" + edtFinanceiroValor + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>";
  
	//Executa o relat�rio selecionado
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
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relat�tio de Eventos por Posi��o Financeira</span>
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
      <input name="Button" type="button" class="button" id="consulta" title="Emite o relat�rio de eventos pelas datas informadas" value="Emitir Relat�rio" onclick="ExecutaConsulta()" />
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
              	<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe a posi��o financeira a listar no relat�rio</td>
            	</tr>
            </table>
          </td>
        </tr>			  
			  <tr>
    			<td class="dataLabel">Financeiro:</td>
    			<td colspan="3" class="tabDetailViewDF">
  	  			<table cellpadding="0" cellspacing="0" >
        			<tr valign="middle">
								<td width="140">
              		<input type="radio" name="edtFinanceiro" value="1" checked="checked"> A Receber 
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