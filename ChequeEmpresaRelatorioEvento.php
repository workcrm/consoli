<?php 
###########
## M�dulo para exibi��o da filtragem do relat�rio dos cheques da empresa - por evento
## Criado: 31/03/2011- Maycon Edinger
## Alterado: 
## Altera��es: 
###########

if ($_GET['headers'] == 1) 
{

	//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipula��o de datas
include "./include/ManipulaDatas.php";

//Adiciona o acesso a entidade de cria��o do componente data
include("CalendarioPopUp.php");  

//Cria um objeto do componente data
$objData = new tipData();

//Define que n�o deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

//Monta o lookup da tabela de bancos
//Monta o SQL
$lista_evento = "SELECT id, nome FROM eventos WHERE empresa_id = 1 AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_evento = mysql_query($lista_evento); 

?>

<input name="TipoObjeto" type="hidden" value="combobox">

<br />
<span class="TituloModulo"> Filtragem: <font color="#990000">Por Evento</font><br />
</span>

<form id="form" name="cadastro" method="post" onsubmit="return valida_form()">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="middle"> 
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Selecione a Situa��o desejada para filtragem dos cheques: </td>
							</tr>
						</table>
					</td>
				</tr>
			  	<tr>
					<td class="dataLabel" width="120">Evento:</td>
			  		<td colspan="3" class="tabDetailViewDF">
						<select name="cmbEventoId" id="cmbBancoId" style="width:350px">
							<?php 
								
								//Monta o while para gerar o combo de escolha de evento
								while ($lookup_evento = mysql_fetch_object($dados_evento)) 
								{ 
								
							?>
							<option value="<?php echo $lookup_evento->id ?>"><?php echo $lookup_evento->id . " - " . $lookup_evento->nome ?> </option>
							<?php 
								
								} 
							
							?>
						</select>		  				
					</td>
				</tr>				
				<tr>
					<td class="dataLabel">
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
				<tr>
			  		<td class="dataLabel" width="120">Situa��o</td>
			  		<td colspan="3" class="tabDetailViewDF">
						<table width="530" border="0" cellspacing="0" cellpadding="0">
							<tr>
						      	<td width="130">
						    	  	<input type="radio" name="edtSituacao" value="0" checked="checked" /> Todos
								</td>
								<td width="130">
						    	  	<input type="radio" name="edtSituacao" value="1" /> Emitido
								</td>
						    	<td>
						    	  	<input type="radio" name="edtSituacao" value="2" /> Compensado
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