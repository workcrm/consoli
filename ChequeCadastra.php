<?php 
###########
## Módulo para Cadastro de Cheques de terceiros
## Criado: 05/07/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########


//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

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
$lista_banco = "SELECT * FROM bancos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_banco = mysql_query($lista_banco); 

//Efetua o lookup na tabela de eventos de formatura
//Monta o sql de pesquisa
$lista_eventos_formatura = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND tipo = 2 ORDER BY nome";

//Executa a query
$dados_eventos_formatura = mysql_query($lista_eventos_formatura);
 
?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
	
	ID = document.getElementById(id);
	ID.style.display = "none";

}

function wdSubmitCheque() 
{
	var Form;
	Form = document.cheque;
	
	if (Form.edtNumero.value.length == 0) 
	{
		alert("É necessário informar o numero do cheque !");
		Form.edtNumero.focus();
		return false;
	}
	
	//Verifica se foi informado o banco
	if (Form.cmbBancoId.value == 0) 
	{
		alert("É necessário selecionar um Banco !");
		Form.cmbBancoId.focus();
		return false;
	}
	
	if (Form.edtValor.value.length == 0) 
	{
		
		alert("É necessário informar o valor do cheque !");
		Form.edtValor.focus();
		return false;
   
	}
    
	return true;
}

function busca_formandos()
{
  
	var Form;
	Form = document.cheque;   
  
	if (Form.cmbEventoFormaturaId.value != 0)
	{  
    
		eventoId = Form.cmbEventoFormaturaId.value;
     
		wdCarregarFormulario('ContaReceberBuscaFormando.php?EventoId=' + eventoId,'recebe_formandos');
   
	} 
	
	else 
	
	{
    
		document.getElementById("recebe_formandos").innerHTML = "[ Selecione um evento ]";
      
	}
      
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="cheque" action="sistema.php?ModuloNome=ChequeCadastra" method="post" onsubmit="return wdSubmitCheque()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440">
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Manutenção de Cheques de Terceiros</span>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">

						<?php
							
							//Verifica se a página está abrindo vindo de uma postagem
							if($_POST['Submit']) 
							{
								
								//Recupera os valores vindo do formulário e atribui as variáveis
								$edtEmpresaId = $empresaId;
								$edtNumero = strtoupper($_POST["edtNumero"]);
								$chkPredatado = $_POST["chkPredatado"];
								$edtBomPara = DataMySQLInserir($_POST["edtBomPara"]);
								$cmbBancoId = $_POST["cmbBancoId"];
								$edtAgenciaTerceiro = $_POST["edtAgenciaTerceiro"];
								$edtContaTerceiro = $_POST["edtContaTerceiro"];
								$edtFavorecido = $_POST["edtFavorecido"];
								$edtValor = MoneyMySQLInserir($_POST["edtValor"]);
								$edtRecebimento = DataMySQLInserir($_POST["edtRecebimento"]);
								$edtPago = $_POST["edtPago"];		        		        		        
								$edtStatus = $_POST["edtStatus"];
								$edtObservacoes = $_POST["edtObservacoes"];	
								$edtDataDevolucao = DataMySQLInserir($_POST["edtDataDevolucao"]);
								$edtDisposicao = $_POST["edtDisposicao"];
								$cmbEventoFormaturaId = $_POST["cmbEventoFormaturaId"];
								$cmbFormandoId = $_POST["cmbFormandoId"];	      		        
								
								//Monta e executa a query
								$sql = mysql_query("INSERT INTO cheques (
													numero_cheque,
													empresa_id, 
													pre_datado,
													banco_id,
													agencia,
													conta,																
													bom_para,																
													valor,
													disponivel,
													favorecido,
													status,
													observacoes,
													data_recebimento,
													origem,
													disposicao,
													evento_id,
													formando_id,
													data_devolucao
													) values (				
													'$edtNumero',
													'$edtEmpresaId',
													'$chkPredatado',
													'$cmbBancoId',
													'$edtAgenciaTerceiro',
													'$edtContaTerceiro',
													'$edtBomPara',
													'$edtValor',
													1,
													'$edtFavorecido',
													'$edtStatus',
													'$edtObservacoes',
													'$edtRecebimento',
													1,
													'$edtDisposicao',
													'$cmbEventoFormaturaId',
													'$cmbFormandoId',
													'$edtDataDevolucao'
													);");

								//Exibe a mensagem de inclusão com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Cheque cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
							}
						?>

						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td style="PADDING-BOTTOM: 2px">
									<input name="Submit" type="submit" class="button" accesskey="S" title="Salva o registro atual [Alt+S]" value="Salvar Cheque" />
									<input class="button" title="Limpa o conteúdo dos campos digitados [Alt+L]" accesskey="L" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
								</td>
								<td align="right">
									<input class="button" title="Emite o relatório dos cheques cadastrados" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="abreJanela('./relatorios/ChequeRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" />
								</td>
							</tr>
						</table>
						   
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="18">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Cheque e clique em [Salvar Cheque] </td>
										</tr>
									</table>								
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="19%">
									<span class="dataLabel">Número:</span>             
								</td>
								<td colspan="3" class="tabDetailViewDF">              
									<input name="edtNumero" type="text" class="datafield" id="edtNumero" style="width: 80px; text-transform: uppercase" maxlength="15" />
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="20%">
									<span class="dataLabel">Pré-datado:</span>             
								</td>
								<td class="tabDetailViewDF">
									<input name="chkPredatado" type="checkbox" id="chkPredatado" value="1"> Pré-datado				 
								</td>
								<td class="dataLabel" width="100">
									<span class="dataLabel">Bom Para:</span>             
								</td>
								<td class="tabDetailViewDF">
									<?php
										//Define a data do formul&aacute;rio
										$objData->strFormulario = "cheque";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtBomPara";
										//Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
										$objData->strValor = "";
										//Define o tamanho do campo 
										//$objData->intTamanho = 15;
										//Define o n&uacute;mero maximo de caracteres
										//$objData->intMaximoCaracter = 20;
										//define o tamanho da tela do calendario
										//$objData->intTamanhoCalendario = 200;
										//Cria o componente com seu calendario para escolha da data
										$objData->CriarData();
									?>				 
								</td>
							</tr>	
							<tr>
								<td class="dataLabel">Banco:</td>
								<td colspan="3" valign="middle" class="tabDetailViewDF">
									<select name="cmbBancoId" id="cmbBancoId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
											<?php 
												//Monta o while para gerar o combo de escolha de funcao
												while ($lookup_banco = mysql_fetch_object($dados_banco)) 
												{ 
											?>
												<option value="<?php echo $lookup_banco->id ?>"><?php echo $lookup_banco->codigo . " - " . $lookup_banco->nome ?> </option>
											<?php 
												
												} 
											
											?>
									</select>						 
								</td>
							</tr>
							<tr>
								<td class='dataLabel'>
									<span class="dataLabel">Agência:</span>             
								</td>
								<td class="tabDetailViewDF">
									<input name="edtAgenciaTerceiro" type="text" class='datafield' id="edtAgenciaTerceiro" size="10" maxlength="10">				 
								</td>
								<td class='dataLabel' width='100'>
									<span class="dataLabel">Nº Conta:</span>             
								</td>
								<td class="tabDetailViewDF">
									<input name="edtContaTerceiro" type="text" class='datafield' id="edtContaTerceiro" size="10" maxlength="10">				 
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Titular:</td>
								<td colspan="3" valign="middle" class="tabDetailViewDF">
									<input name="edtFavorecido" type="text" class="datafield" style="width: 350px" size="84" maxlength="80">						 				 
								</td>
							</tr>          
							<tr>
								<td class="dataLabel">Valor:</td>
								<td colspan="3" class="tabDetailViewDF">
									<?php
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtValor";
										//Define o tamanho do componente
										$objWDComponente->intSize = 16;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "";
										//Busca a descrição do XML para o componente
										$objWDComponente->strLabel = "";
										//Determina um ou mais eventos para o componente
										$objWDComponente->strEvento = "";
										//Define numero de caracteres no componente
										$objWDComponente->intMaxLength = 14;
										
										//Cria o componente edit
										$objWDComponente->Criar();  
									?>								
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Data de Recebimento:</td>
								<td colspan="3" class="tabDetailViewDF">
									<?php
										//Define a data do formulário
										$objData->strFormulario = "cheque";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtRecebimento";
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
								<td class="dataLabel">Status:</td>
								<td colspan="3" class="tabDetailViewDF">
									<table width="400" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="130" height="20">
												<input name="edtStatus" type="radio" value="1" checked="checked" onclick="oculta(900);oculta(922);"/> Recebido
											</td>
											<td width="130">
												<input type="radio" name="edtStatus" value="2" onclick="oculta(900);oculta(922);" /> Compensado
											</td>
											<td>
												<input type="radio" name="edtStatus" value="3" onclick="oculta(900);oculta(922); var ID = document.getElementById(922); ID.style.display = '';; var ID2 = document.getElementById(900); ID2.style.display = '';" /> Devolvido
											</td>
										</tr>
									</table>					 
								</td>
							</tr>
							<tr id="900" style="display: none">
								<td class="dataLabel">Data de Devolução:</td>
								<td colspan="3" class="tabDetailViewDF">
									<?php
										//Define a data do formulário
										$objData->strFormulario = "cheque";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataDevolucao";
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
							<tr id="922" style="display: none">
								<td class="dataLabel">Disposição:</td>
								<td colspan="3" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="130" height="20">
												<input type="radio" name="edtDisposicao" value="1" checked="checked" /> Primeiro Contato
											</td>
											<td width="130">
												<input type="radio" name="edtDisposicao" value="2" /> Em Negociação
											</td>
											<td width="130">
												<input type="radio" name="edtDisposicao" value="3" /> Reapresentado
											</td>
											<td>
												<input type="radio" name="edtDisposicao" value="4" /> Pago
											</td>
										</tr>
										<tr>
											<td>
												<input type="radio" name="edtDisposicao" value="5" /> Para Registrar
											</td>
											<td style="padding-top: 4px">
												<input type="radio" name="edtDisposicao" value="6" /> No SPC
											</td>
											<td style="padding-top: 4px">
												<input type="radio" name="edtDisposicao" value="7" /> Não Pode SPC
											</td>
											<td style="padding-top: 4px">
												<input type="radio" name="edtDisposicao" value="8" /> SPC Pago
											</td>
										</tr>
										<tr>
											<td style="padding-top: 4px">
												<input type="radio" name="edtDisposicao" value="9" /> Devolvido ao Titular
											</td>
											<td style="padding-top: 4px">
												<input type="radio" name="edtDisposicao" value="10" /> Cobrança Judicial
											</td>
											<td colspan="2" style="padding-top: 4px">
												<input type="radio" name="edtDisposicao" value="11" /> ACC
											</td>
										</tr>
									</table>
								</td>
							</tr>					           
							<tr>
								<td class="dataLabel" valign="top">Observações:</td>
								<td colspan="3" class="tabDetailViewDF">
									<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 100px"></textarea>
								</td>
							</tr>              
						</table>
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="440">
									<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Vincular Cheque Avulso a Evento</span>
								</td>
							</tr>
							<tr>
								<td colspan="5">
									<img src="image/bt_espacohoriz.gif" width="100%" height="12">
								</td>
							</tr>
						</table>
					  
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="dataLabel" width="150">Evento:</td>
								<td class="tabDetailViewDF">
									<div id="900">
										<select name="cmbEventoFormaturaId" id="cmbEventoFormaturaId" style="width: 400px" onchange="busca_formandos()" style="disabled: true">                  
											<option value="0">Selecione uma Opção</option>
												<?php 
													//Cria o componente de lookup de eventos formatura
													while ($lookup_eventos_formatura = mysql_fetch_object($dados_eventos_formatura)) 
													{ 
												?>
											<option value="<?php echo $lookup_eventos_formatura->id ?>"><?php echo $lookup_eventos_formatura->id . " - " . $lookup_eventos_formatura->nome ?></option>
												<?php 
													//Fecha o while
													} 
												?>
										</select>
									</div>
								</td>
							</tr>            
							<tr>
								<td class="dataLabel">Formando:</td>
								<td class="tabDetailViewDF">
									<div id="recebe_formandos">
									[ Selecione um evento ] <input type="hidden" name="cmbFormandoId" id="cmbFormandoId" value="0">
									</div>
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