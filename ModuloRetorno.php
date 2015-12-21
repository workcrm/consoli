<?php 
###########
## Módulo para abrir arquivos de retorno de boletos
## Criado: 20/01/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

// Processa as diretivas de segurança 
require("Diretivas.php");

?>

<script language="JavaScript">
function valida_form() 
{
   var Form;
   Form = document.cadastro;
   
   if (Form.edtAnexo.value == 0) 
   {
      alert("É necessário selecionar um arquivo de retorno !");
      Form.edtAnexo.focus();
      return false;
   }
   
   return true;
}
</script>

<form id="form" name="cadastro" enctype="multipart/form-data" action="sistema.php?ModuloNome=ModuloRetornoVisualiza" method="post" onsubmit="return valida_form()">

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Processamento de Arquivos de Retorno</span></td>
				</tr>
				<tr>
					<td colspan='5'>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table id='2' width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
				<tr>
					<td width='100%' class='text'>								           
						<table cellspacing="0" cellpadding="0" width="520" border="0">
							<tr>
								<td style="PADDING-BOTTOM: 2px">
									<input name="Submit" type="submit" class="button" id="Submit" title="Processa o arquivo de retorno e compara com os boletos cadastrados no sistema" value='Visualizar Arquivo' />
								</td>
								<td width="36" align="right">	  </td>
							</tr>
						</table>
           
						<table class='tabDetailView' cellspacing='0' cellpadding='0' width='100%' border='0'>
							<tr>
								<td class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='20'>
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Selecione um arquivo de retorno a processar e clique em [Visualizar Arquivo] </TD>
										</tr>
									</table>             
								</td>
							</tr>                    
							<tr>
								<td width="120" valign="top" class="dataLabel">Arquivo de Retorno:</td>
								<td class="tabDetailViewDF">
									<input type="file" size="100" name="edtAnexo" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
    </tr>  
</table>  
	 
</form>
</td>
</tr>
</table>
