<?php 
###########
## Módulo para exibição da filtragem do relatório das contas - por evento, formando e conta-caixa
## Criado: 05/05/2011 - Maycon Edinger
## Alterado: 
## Alterações:
##  
###########

if ($_GET['headers'] == 1) 
{

	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");  

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

//Efetua o lookup na tabela de eventos
//Monta o SQL de pesquisa
$lista_evento = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_evento = mysql_query($lista_evento);

//Efetua o lookup na tabela de conta-caixa
//Monta o SQL de pesquisa
$lista_conta_caixa = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = 1 AND tipo = 1 ORDER BY nome";
	
//Executa a query
$dados_conta_caixa = mysql_query($lista_conta_caixa);

?>

<script language="JavaScript">

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

<input name="TipoObjeto" type="hidden" value="combobox" />

<br />
<span class="TituloModulo"> Filtragem: <font color="#990000">Por Evento, Formando e Conta-Caixa</font><br />
</span>

<table width="626" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="middle"> 
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Selecione o Evento, o Formando e a Conta-Caixa desejado para filtragem das contas:</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="dataLabel">
						Evento
					</td>
					<td colspan="3" class="tabDetailViewDF">
						<select name="cmbEventoId" id="cmbEventoId" style="width: 450px" onchange="busca_formandos()">    
							<option value="0">-- Selecione uma opção --</option>
							<?php 
								while ($lookup_evento = mysql_fetch_object($dados_evento)) 
								{ 
							?>
							<option value="<?php echo $lookup_evento->id ?>"><?php echo $lookup_evento->id . " - " . $lookup_evento->nome ?></option>
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
			  			Conta-caixa
			  		</td>
			  		<td colspan="3" class="tabDetailViewDF">
						<select name="cmbContaCaixaId" id="cmbContaCaixaId" style="width: 360px">    
							<?php 
								while ($lookup_conta_caixa = mysql_fetch_object($dados_conta_caixa)) 
								{ 
							?>
							<option value="<?php echo $lookup_conta_caixa->id ?>"><?php echo $lookup_conta_caixa->nome ?></option>
					    	<?php 
								} 
							?>
					    </select>			  				
			  		</td>
			  	</tr>
				<tr>
					<td class="dataLabel" width="65">
						In&iacute;cio:
					</td>
					<td width="107" class="tabDetailViewDF">
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
					</td>
					<td width="61" class="dataLabel">T&eacute;rmino:</td>
					<td width="100" class="tabDetailViewDF">
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
					</td>                
				</tr>
				<tr>
					<td class="dataLabel">
						Situação
					</td>
					<td colspan="3" class="tabDetailViewDF">
						<table width="530" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="110">
									<input type="radio" name="edtSituacao" value="0" checked="checked" /> Todas
								</td>
								<td width="110">
									<input type="radio" name="edtSituacao" value="1" /> Em Aberto
								</td>
								<td width="110">
									<input type="radio" name="edtSituacao" value="2" /> Pagas
								</td>
								<td>
									<input type="radio" name="edtSituacao" value="3" /> Vencidas
								</td>						
							</tr>
						</table>		  				
					</td>
				</tr>               		
			</table>
		</td>
	</tr>
</table>