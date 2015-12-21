<?php 
###########
## Módulo para exibição da filtragem do relatório dos cheques da empresa - por conta corrente
## Criado: 11/09/2007- Maycon Edinger
## Alterado: 
## Alterações: 
###########

if ($_GET['headers'] == 1) 
{

	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");  

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

//Monta o lookup da tabela de bancos
//Monta o SQL
$lista_banco = "SELECT
				cco.id,
				cco.nome AS conta_nome,
				cco.agencia,
				cco.conta,
				ban.nome AS banco_nome
				FROM conta_corrente cco 
				LEFT OUTER JOIN bancos ban ON ban.id = cco.banco_id
				WHERE cco.empresa_id = $empresaId 
				AND cco.ativo = 1 
				ORDER BY cco.nome";

//Executa a query
$dados_banco = mysql_query($lista_banco); 

?>

<input name="TipoObjeto" type="hidden" value="combobox">

<br />
<span class="TituloModulo"> Filtragem: <font color="#990000">Por Conta-Corrente</font><br />
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
								<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Selecione a Situação desejada para filtragem dos cheques: </td>
							</tr>
						</table>
					</td>
				</tr>
			  	<tr>
					<td class="dataLabel" width="120">Conta-Corrente:</td>
			  		<td colspan="3" class="tabDetailViewDF">
						<select name="cmbBancoId" id="cmbBancoId" style="width:420px">
							<option value="0">Selecione uma Opção</option>
							<?php 
							 
								//Monta o while para gerar o combo de escolha
								while ($lookup_banco = mysql_fetch_object($dados_banco)) 
								{ 
		
							?>
							<option value="<?php echo $lookup_banco->id ?>"><?php echo $lookup_banco->conta_nome . " - " . $lookup_banco->banco_nome . " - " . $lookup_banco->agencia . " - " . $lookup_banco->conta ?> </option>
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
			  		<td class="dataLabel" width="120">Situação</td>
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