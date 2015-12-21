<?php
###########
## Módulo para Pesquisa de Clientes
## Criado: - 17/04/2007 - Maycon Edinger
## Alterado: 
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
//Inclui o arquivo para manipulação de datas
//include "./include/ManipulaDatas.php";
?>

<script language="JavaScript">
function EfetuaConsulta() {
  var Form;
  Form = document.frmContasPesquisa;
   
	if (Form.edtPesquisa.value.length == 0) {
    alert("É necessário informar um argumento de pesquisa !");
    Form.edtPesquisa.focus();
    return false;
  }
   
	if (frmContasPesquisa.edtVisualizar[0].checked == true) 
	  {
		valor = 1
		} 
	else 
	  {
		valor = 2
		}; 
	
	urlPesquisa = "ModuloClientesLista.php?edtPesquisa=" + Form.edtPesquisa.value + "&edtVisualizar=" + valor;
	
	wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');
  return true;
}

function EfetuaPesquisa(Letra) {
  var Form;
  Form = document.frmContasPesquisa;
   
	if (frmContasPesquisa.edtVisualizar[0].checked == true) 
	  {
		valor = 1
		} 
	else 
	  {
		valor = 2
		}; 
	
	urlPesquisa = "ModuloClientesLista.php?ChaveFiltragem=" + Letra + "&Modo_vis=" + valor;
	wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');
  return true;
}
</script>

<form id='form' name='frmContasPesquisa' action='#'>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign='top'>
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
		    <tr>
		      <td width='440'>
			    <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Pesquisar Clientes</span>
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
    <td>
      <table width="100%" id="2" cellpadding="0" cellspacing="0" border="0">
        <tr>
	      	<td>
          	<table class='tabDetailView' cellspacing='0' cellpadding='0' width='100%' border='0'>
              <tr>
                <td class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='20'>
                  <table cellspacing='0' cellpadding='0' width="100%" border=0>
                      <tr>
                        <td class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_pesquisar.gif"> Informe o nome do Cliente a pesquisar e clique em [Pesquisar Clientes] </TD>
                      </tr>
                  </TABLE>
                </TD>
              </TR>
              <TR>
                <TD class='dataLabel' width='59'>Pesquisar:</TD>

                <TD colspan='3' class=tabDetailViewDF>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="505" height='20'>
					  						<input name="edtPesquisa" type="text" class='datafield' id="edtPesquisa"  size="65" maxlength="50">
                      </td>
                      <td><span style="PADDING-BOTTOM: 2px">
                        <INPUT class="button" title="Inicia a pesquisa do(s) cliente(s) com base nos valores informados [Alt+P]" accessKey='P' name='btnPesquisa' type='button' id='btnPesquisa' value='Pesquisar Clientes' onClick="EfetuaConsulta()"></span></td>
                    </tr>
                  </table>
                </TD>
              </TR>
			  
			  			<tr>
			    			<TD class='dataLabel' width='59'>Visualizar:</td>
			    			<TD colspan='3' class="tabDetailViewDF">
			  	  			<table width="300" cellpadding="0" cellspacing="0" >
              			<tr valign="middle">
                			<td width="150">
                  			<label>
                    		<input name="edtVisualizar" type="radio" value="1" checked>
                    		<img src="image/bt_cartao_vis.gif" alt="Exibe os Clientes no formato Cartão de Visitas" align="middle"> Cartão de Visitas 
												</label>
                			</td>
                			<td width="100">
                  			<label>
                    		<input type="radio" name="edtVisualizar" value="2">
                    		<img src="image/bt_listagem.gif" alt="Exibe os Clientes no formato Listagem" align="middle"> Listagem 
												</label>
                			</td>
              			</tr>
          				</table>
			    			</td>
			  			</tr>   		  			  
        			<TR>
          			<TD class='dataLabel' width='59'>Iniciar em:</TD>
								<TD colspan="3" class='tabDetailViewDF'>
					  			<span style="PADDING-BOTTOM: 2px">
              		<INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnA' type='button' id='btnA' value='A' onClick="EfetuaPesquisa('A')" style="width:18px">
              		<INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnB' type='button' id='btnB' value='B' onClick="EfetuaPesquisa('B')" style="width:18px">
              		<INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnC' type='button' id='btnC' value='C' onClick="EfetuaPesquisa('C')" style="width:18px">
              		<INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnD' type='button' id='btnD' value='D' onClick="EfetuaPesquisa('D')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnE' type='button' id='btnE' value='E' onClick="EfetuaPesquisa('E')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnF' type='button' id='btnF' value='F' onClick="EfetuaPesquisa('F')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnG' type='button' id='btnG' value='G' onClick="EfetuaPesquisa('G')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnH' type='button' id='btnH' value='H' onClick="EfetuaPesquisa('H')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnI' type='button' id='btnI' value='I' onClick="EfetuaPesquisa('I')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnJ' type='button' id='btnJ' value='J' onClick="EfetuaPesquisa('J')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnK' type='button' id='btnK' value='K' onClick="EfetuaPesquisa('K')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnL' type='button' id='btnL' value='L' onClick="EfetuaPesquisa('L')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnM' type='button' id='btnM' value='M' onClick="EfetuaPesquisa('M')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnN' type='button' id='btnN' value='N' onClick="EfetuaPesquisa('N')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnO' type='button' id='btnO' value='O' onClick="EfetuaPesquisa('O')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnP' type='button' id='btnP' value='P' onClick="EfetuaPesquisa('P')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnQ' type='button' id='btnQ' value='Q' onClick="EfetuaPesquisa('Q')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnR' type='button' id='btnR' value='R' onClick="EfetuaPesquisa('R')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnS' type='button' id='btnS' value='S' onClick="EfetuaPesquisa('S')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnT' type='button' id='btnT' value='T' onClick="EfetuaPesquisa('T')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnU' type='button' id='btnU' value='U' onClick="EfetuaPesquisa('U')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnV' type='button' id='btnV' value='V' onClick="EfetuaPesquisa('V')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnW' type='button' id='btnW' value='W' onClick="EfetuaPesquisa('W')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnX' type='button' id='btnX' value='X' onClick="EfetuaPesquisa('X')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnY' type='button' id='btnY' value='Y' onClick="EfetuaPesquisa('Y')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes iniciando com a letra selecionada" name='btnZ' type='button' id='btnZ' value='Z' onClick="EfetuaPesquisa('Z')" style="width:18px">
		              <INPUT class=button title="Exibe todos os clientes" name='btnTodos' type='button' id='btnTodos' value='Exibir Todos' onClick="EfetuaPesquisa('todos')" style="width:85px">
            			</span>
								</TD>
        			</TR>
      				</FORM>
    				</TABLE>		 
	  				<br/>
  				</td>
				</tr> 
		
				<tr>
				 	<td>
				  	<div id="retornopesquisa">
				  		
				  	</div>
				  </td>
				</tr>
			</table>	

		</td>
	</tr>
</table>