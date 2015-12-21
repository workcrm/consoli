<?php
###########
## Módulo para Escolha de evento para a proxima etapa
## Criado: - 21/05/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Captura o valor para gerar o tipo de destino após selecionar
$DestinoEvento = $_GET['Destino'];

//Efetua o switch para determinar as varíaveis para o destino
switch ($DestinoEvento) {
  //Caso for participantes
	case 1: 
		//Configura o título da janela
		$TituloJanela = "Participantes do Evento";
		//Define a ação Javascript para executar quando clicar no botão de prosseguir
		$AcaoBotao = "AcaoBotaoParticipante()";
		break;
  //Caso for endereços
	case 2: 
		//Configura o título da janela
		$TituloJanela = "Endereços do Evento";
		//Define a ação Javascript para executar quando clicar no botão de prosseguir
		$AcaoBotao = "AcaoBotaoEndereco()";
		break;
  //Caso for itens do evento
	case 3: 
	  //Configura o título da janela
		$TituloJanela = "Itens do Evento";
		//Define a ação Javascript para executar quando clicar no botão de prosseguir
		$AcaoBotao = "AcaoBotaoItem()";
	break; 
}

//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";
//Executa a query
$dados_eventos = mysql_query($lista_eventos);
?>

<script language="JavaScript">

function AcaoBotaoParticipante() {
   var Form;

   Form = document.frmEventoSeleciona;

	 //Recebe o valor do combo de eventos
	 var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
	 var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value

	 window.location='sistema.php?ModuloNome=ParticipanteEventoCadastra&EventoId=' + cmbEventoIdValor;
   /* wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=' + cmbEventoIdValor,'conteudo');	*/

	 return true;
}

function AcaoBotaoEndereco() {
   var Form;

   Form = document.frmEventoSeleciona;

	 //Recebe o valor do combo de eventos
	 var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
	 var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value

	 window.location='sistema.php?ModuloNome=EnderecoEventoCadastra&EventoId=' + cmbEventoIdValor;
   /* wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=' + cmbEventoIdValor,'conteudo');	    */

	 return true;
}

function AcaoBotaoItem() {
   var Form;

   Form = document.frmEventoSeleciona;

	 //Recebe o valor do combo de eventos
	 var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
	 var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value

   window.location='sistema.php?ModuloNome=ItemEventoCadastra&EventoId=' + cmbEventoIdValor;
   /* wdCarregarFormulario('ItemEventoCadastra.php?EventoId=' + cmbEventoIdValor,'conteudo');	   */

	 return true;
}
</script>

<FORM name='frmEventoSeleciona' action='#'>
    
<table width="100%" border="0" align="left" cellpadding='0' cellspacing='0'>
  <tr>
    <td valign="top" align="left">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo"><?php echo $TituloJanela ?></span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
			  </tr>
			</table>

    <TABLE id="2" cellSpacing='0' cellPadding='0' width='520' border='0'>
      <tr>
        <TD style="PADDING-BOTTOM: 2px">
          <INPUT type="button" class=button title="Novo Evento [Alt+N]" accessKey='N' name='Cadastra' value='Novo Evento' onclick="window.location='sistema.php?ModuloNome=EventoCadastra';">
        </TD>
        <TD width="36" align=right></TD>
      </TR>
    </TABLE>

		<table width="626" id="3" cellpadding="0" cellspacing="0" border="0">
      <tr>
	      <td>
          <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
            <TR>
              <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='21'>
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                  <TR>
                    <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'>
				  						<img src="image/bt_cadastro.gif" width="16" height="15"> Selecione o Evento e clique em [Prosseguir]</TD>
                  </TR>
                </TABLE>
              </TD>
            </TR>
			  
			  		<TR>
              <TD class='dataLabel' width='50'>Evento:</TD>
              <TD colspan="3" width="490" class=tabDetailViewDF>
								<select name="cmbEventoId" id="cmbEventoId" style="width: 400px">                  
								  <?php 
								    //Cria o componente de lookup de eventos
								    while ($lookup_eventos = mysql_fetch_object($dados_eventos)) { 
								  ?>
                   <option value="<?php echo $lookup_eventos->id ?>"><?php echo $lookup_eventos->nome ?> </option>
                  <?php 
									  //Fecha o while
									  } 
									?>
                </select>
              </TD>

                <TD class=tabDetailViewDF>
                  <div align="center">
									  <span style="PADDING-BOTTOM: 2px">
											<input class=button title="Prosegue a operação [Alt+P]" accesskey='P' name='btnProsseguir' type='button' id='btnProsseguir' value='Prosseguir' style="width: 120px" onClick="<?php echo $AcaoBotao ?>" />
                    </span>
									</div>
                </TD>
			  			</TR>              		
          	</TABLE>		 
		  		</td>
				</tr> 
	  	</table>
 	  </FORM>
	  </br>
    </td>
  </tr>     
</table>
  
