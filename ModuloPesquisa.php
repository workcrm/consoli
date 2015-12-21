<?php 
###########
## Módulo para Exibição da caixa de pesquisas
## Criado: 24/04/2007 - Maycon Edinger
## Alterado: 11/07/2007 - Maycon Edinger
## Alterações: 
## 11/07/2007 - Implementados os módulos de eventos, contas a pagar e receber na pesquisa
###########

<script language="JavaScript">
function wdSubmitPesquisa() {
     var Form;

     Form = document.pesquisa;
     if (Form.ChavePesquisa.value.length == 0) {
        alert("É necessário informar o argumento de pesquisa !");
        Form.ChavePesquisa.focus();
        return false;
     }
     
     var urlCadastro;
     urlCadastro = "ModuloPesquisaResultado.php?ChavePesquisa=" + Form.ChavePesquisa.value + "&cmbModulo=" + Form.cmbModulo.value;
     
     wdCarregarFormulario(urlCadastro,'conteudo');
     return true;
}

function wdSubmitPesquisaEnter() {
     var Form;

     Form = document.pesquisa;
     if (Form.ChavePesquisa.value.length == 0) {
        alert("É necessário informar o argumento de pesquisa !");
        Form.ChavePesquisa.focus();
        return false;
     }
     
     var urlCadastro;
     urlCadastro = "ModuloPesquisaResultado.php?ChavePesquisa=" + Form.ChavePesquisa.value + "&cmbModulo=" + Form.cmbModulo.value;
     
     wdCarregarFormulario(urlCadastro,'conteudo');
     return false;
}
</script>

<table width="100%" border='0' align="left" cellpadding='0' cellspacing='0'>
  <tr>
    <td class="TituloModulo">
	  	<img src="image/lat_cadastro.gif"> Pesquisar <br />
      <img src="image/bt_espacohoriz.gif" width="100%" height="12">	
		</td>
  </tr>
  <tr>
    <td>
      <form name="pesquisa" action="#">
	  		<table class='tabDetailView' cellSpacing='0' cellPadding='0' width='175' border='0'>
           <TR>
             <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='17'>
               <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                 <TR>
                   <TD background="image/fundo_tabela.gif" class='tabDetailViewDL' style='TEXT-ALIGN: left'>
									   <img src="image/bt_pesquisar.gif"> Pesquisar por: 
									 </TD>
			     			 </TR>
				 
		   		 			 <TR height="15">
             	     <TD class='tabDetailViewDL 'style='TEXT-ALIGN: left'>                 	 
                     <input name="ChavePesquisa" type="text" id="ChavePesquisa" size="28" maxlength="35" onKeyPress="if ((window.event ? event.keyCode : event.which) == 13) { return wdSubmitPesquisaEnter(); }" />
             	     </TD>
           		   </TR>				 
                 <TR>
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'>   
							       em:
							       <select name="cmbModulo" id="cmbModulo" style="width:114px">
							         <option value="0" selected="selected">Todos os M&oacute;dulos</option>
							         <option value="1">Compromissos</option>
							         <option value="6">Eventos</option>
							         <option value="2">Clientes</option>
							         <option value="3">Fornecedores</option>
							         <option value="4">Colaboradores</option>
							         <?php
											 	 //Verifica o nivel de acesso
											 	 if ($nivelAcesso >= 4) {
											 ?>
											 <option value="7">Contas a Pagar</option>
							         <?php
							           } else if ($nivelAcesso >= 5) {
							         ?>
											 <option value="8">Contas a Receber</option>
							         <?php
							           }
							         ?>
											 <option value="5">Recados</option>
						         </select>
							       <span style="PADDING-BOTTOM: 2px">
							         <input class="button" title="Efetua a pesquisa com base no texto informado" name='btnPesquisa' type='button' id='btnPesquisa' value='Ok' style="width:22px" onClick="wdSubmitPesquisa()" />
						         </span>			                            
				   				 </TD>
			     			 </TR>
		       		 </TABLE>             
			 			 </TD>
	         </TR>
	   		 </TABLE>
	     </form>
	   </td>
  </tr>  
</table>