<?php 
###########
## Módulo para exibição da filtragem do relatório das contas - por situação
## Criado: 14/05/2007- Maycon Edinger
## Alterado: 26/06/2007 - Maycon Edinger 
## Alterações: 
## 26/06/2007 - Incluída opção de filtrar junto as datas 
###########

if ($_GET['headers'] == 1) {
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");  
//Cria um objeto do componente data
$objData = new tipData();
//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 
?>

<input name="TipoObjeto" type="hidden" value="combobox">

<br />
<span class="TituloModulo"> Filtragem: <font color="#990000">Por Situação</font><br />
</span>

<table width="626" border="0" cellspacing="0" cellpadding="0">
  <tr>
		<td valign="middle"> 

				<TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
              <TR>
                <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='21'>
                  <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                      <TR>
                        <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Selecione a Situação desejada para filtragem das contas: </TD>
                      </TR>
                  </TABLE>
                </TD>
              </TR>
			  			<tr>
			  				<td class='dataLabel'>
			  				Situação
			  				</td>
			  				<td colspan="3" class=tabDetailViewDF>
									<table width="530" border="0" cellspacing="0" cellpadding="0">
						    	  <tr>
						    	  	<td width="110">
						    	  		<input type="radio" name="edtSituacao" value="1" checked/> A Vencer
											</td>
						    	  	<td width="110">
						    	  		<input type="radio" name="edtSituacao" value="2" /> Recebidas
											</td>
						    	  	<td>
						    	  		<input type="radio" name="edtSituacao" value="3" /> Vencidas
											</td>						
						    	  </tr>
						    	</table>		  				
			  				</td>
			  			</tr>
							<TR>
                <TD class='dataLabel' width='65'>
                  <SLOT>In&iacute;cio:</SLOT>
                </TD>
                <TD width="107" class=tabDetailViewDF>
									<?php
									    //Define a data do formulário
									    $objData->strFormulario = "cadastro";  
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
									    $objData->strFormulario = "cadastro";  
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