<?php 
###########
## M�dulo para exibi��o da filtragem do relat�rio dos pedidos do FV - Por data de entrega
## Criado: 14/05/2007- Maycon Edinger
## Alterado: 
## Altera��es: 
###########

if ($_GET['headers'] == 1) 
{
	//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Adiciona o acesso a entidade de cria��o do componente data
include_once("CalendarioPopUp.php");  
//Cria um objeto do componente data
$objData = new tipData();
//Define que n�o deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

?>

<input name="TipoObjeto" type="hidden" value="combobox">

<br />
<span class="TituloModulo"> Filtragem: <font color="#990000">Pedidos do Foto e V�deo por Data de Entrega</font><br />
</span>

<table width="626" border="0" cellspacing="0" cellpadding="0">
  <tr>
		<td valign="middle"> 
      <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
          <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe o per&iacute;odo a pesquisar</TD>
              </tr>
            </table>
          </td>
        </tr>			  
			  <tr>
          <td class="dataLabel" width="65">
            In&iacute;cio:
          </td>
          <td width="107" class="tabDetailViewDF">
  					<?php
  					    //Define a data do formul�rio
  					    $objData->strFormulario = "cadastro";  
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
      			    $objData->strFormulario = "cadastro";  
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
      </table>
		</td>
  </tr>
</table>