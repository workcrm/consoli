<?php 
###########
## M�dulo para exibi��o da filtragem do relat�rio dos cheques - por evento
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

<script>
function busca_formandos()
{
  
	var Form;
	Form = document.cadastro;   
  
	if (Form.cmbEventoId.value != 0)
	{
    
		eventoId = Form.cmbEventoId.value;
     
		wdCarregarFormulario('ContaReceberBuscaFormando.php?EventoId=' + eventoId,'recebe_formandos');
   
	} 
	else 
	{
    
		document.getElementById("recebe_formandos").innerHTML = "[ Selecione um evento ]";
      
	}
   
}
</script>

<input name="TipoObjeto" type="hidden" value="combobox">

<br />
<span class="TituloModulo"> Filtragem: <font color="#990000">Por Evento e Formando</font><br />
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
						<select name="cmbEventoId" id="cmbBancoId" style="width:350px" onchange="busca_formandos()">
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
					<td class="dataLabel">Formando:</td>
					<td colspan="3" width="490" class="tabDetailViewDF">
						<div id="recebe_formandos">
						[ Selecione um evento ] <input type="hidden" name="cmbFormandoId" id="cmbFormandoId" value="0" />
						</div>
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
						    	  	<input type="radio" name="edtSituacao" value="0" checked="checked" onclick="oculta(922);" /> Todos
								</td>
								<td width="130">
						    	  	<input type="radio" name="edtSituacao" value="1" onclick="oculta(922);" /> Recebido
								</td>
						    	<td width="130">
						    	  	<input type="radio" name="edtSituacao" value="2" onclick="oculta(922);" /> Compensado
								</td>
						    	<td>
						    	  	<input type="radio" name="edtSituacao" value="3" onclick="oculta(922); var ID = document.getElementById(922); ID.style.display = '';" /> Devolvido
								</td>						
						    </tr>
						</table>		  				
			  		</td>
			  	</tr>
				<tr id="922" style="display: none">
					<td class="dataLabel">Disposi��o:</td>
					<td colspan="3" class="tabDetailViewDF">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="4" style="padding-bottom: 4px">
									<input type="radio" name="edtDisposicao" value="0" checked="checked" /> Todos
								</td>
							</tr>
							<tr valign="middle">
								<td width="130" height="20">
									<input type="radio" name="edtDisposicao" value="1" /> Primeiro Contato
								</td>
								<td width="130">
									<input type="radio" name="edtDisposicao" value="2" /> Em Negocia��o
								</td>
								<td width="130">
									<input type="radio" name="edtDisposicao" value="3" /> Reapresentado
								</td>
								<td>
									<input type="radio" name="edtDisposicao" value="4" /> Pago
								</td>
							</tr>
							<tr>
								<td style="padding-top: 4px">
									<input type="radio" name="edtDisposicao" value="5" /> Para Registrar
								</td>
								<td style="padding-top: 4px">
									<input type="radio" name="edtDisposicao" value="6" /> No SPC
								</td>
								<td style="padding-top: 4px">
									<input type="radio" name="edtDisposicao" value="7" /> N�o Pode SPC
								</td>
								<td style="padding-top: 4px">
									<input type="radio" name="edtDisposicao" value="8" /> SPC Pago
								</td>
							</tr>
							<tr>
								<td style="padding-top: 4px">
									<input type="radio" name="edtDisposicao" value="9" /> Devolvido ao Titular
								</td>
								<td colspan="3" style="padding-top: 4px">
									<input type="radio" name="edtDisposicao" value="10" /> Cobran�a Judicial
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