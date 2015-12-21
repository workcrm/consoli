<?php 
###########
## Módulo para alteraçao de cheques
## Criado: 05/07/2007 - Maycon Edinger
## Alterado: 11/09/2007 - Maycon Edinger
## Alterações:
###########

if ($_GET["headers"] == 1) 
{
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

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

//Monta o lookup aa tabela de bancos
//Monta o SQL
$lista_banco = "SELECT * FROM bancos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_banco = mysql_query($lista_banco); 

?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
	ID = document.getElementById(id);
	ID.style.display = "none";
}

function wdSubmitChequeAltera() 
{
	var Form;
	Form = document.frmChequeAltera;
	if (Form.edtNumero.value.length == 0) 
	{
		alert("É necessário informar o numero do cheque !");
		Form.edtNumero.focus();
		return false;
	}
	
	if (Form.cmbBancoId.value == 0) 
	{
      alert("É necessário selecionar um Banco !");
      Form.cmbBancoId.focus();
      return false;
	}    
	if (Form.edtVencimento.value.length == 0) 
	{
		alert("É necessário informar a data do vencimento !");
		Form.edtVencimento.focus();
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
</script>

<form name='frmChequeAltera' action='sistema.php?ModuloNome=ChequeAltera' method='post' onSubmit='return wdSubmitChequeAltera()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Cheque </span></td>
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

						<?php
							
							//Verifica se a página está abrindo vindo de uma postagem
							if($_POST['Alterar']) 
							{
								
								//Recupera os valores vindo do formulário e atribui as variáveis
								$id = $_POST["Id"];			
								$edtEmpresaId = $empresaId;
								$edtNumero = strtoupper($_POST["edtNumero"]);
								$chkPredatado = $_POST["chkPredatado"];
								$edtBomPara = DataMySQLInserir($_POST["edtBomPara"]);
								$cmbBancoId = $_POST["cmbBancoId"];
								$edtAgenciaTerceiro = $_POST["edtAgenciaTerceiro"];
								$edtContaTerceiro = $_POST["edtContaTerceiro"];
								$edtFavorecido = $_POST["edtFavorecido"];
								$edtValor = MoneyMySQLInserir($_POST["edtValor"]);
								$edtRecebimento = DataMySQLInserir($_POST["edtDataRecebimento"]);
								$edtPago = $_POST["edtPago"];		        		        		        
								$edtStatus = $_POST["edtStatus"];
								$edtDisposicao = $_POST["edtDisposicao"];
								$edtDataDevolucao = DataMySQLInserir($_POST["edtDataDevolucao"]);
								$edtObservacoes = $_POST["edtObservacoes"];								

								//Monta e executa a query
								$sql = mysql_query("
               									UPDATE cheques SET 
												numero_cheque = '$edtNumero',
												pre_datado = '$chkPredatado',
												banco_id = '$cmbBancoId',
												agencia = '$edtAgenciaTerceiro',
												conta = '$edtContaTerceiro',
												bom_para = '$edtBomPara',
												valor = '$edtValor',
												favorecido = '$edtFavorecido',
												status = '$edtStatus',
												disposicao = '$edtDisposicao',
												observacoes = '$edtObservacoes',
												data_devolucao = '$edtDataDevolucao',
												data_recebimento = '$edtRecebimento'
												WHERE id = '$id' ");	
										

								//Desativado por solicitacao da janaina em 23/02
															
								//Verifica se o cheque foi marcado como devolvido
								if ($edtStatus == 3)
								{
								
									//Captura as contas a pagar e receber do cheque que esta sendo devolvido
									//Monta o sql
									$sql = "SELECT conta_receber_id, conta_pagar_id, valor, numero_cheque, formando_id, altera_contas FROM cheques WHERE id = $id";
									
									//Executa a query
									$resultado = mysql_query($sql);
									
									//Monta o array dos dados
									$campos = mysql_fetch_array($resultado);
									
									$ContaReceberId = $campos["conta_receber_id"];
									$ContaPagarId = $campos["conta_pagar_id"];
									$ValorCheque = $campos["valor"];
									$NumeroCheque = $campos["numero_cheque"];
									$FormandoId = $campos["formando_id"];
									$AlteraContas = $campos["altera_contas"];
									
									
									//Verifica se eh a primeira vez do processo
									if ($AlteraContas == 0)
									{
									
										//Caso tiver uma conta receber vinculada
										if ($ContaReceberId > 0)
										{
										
											//Busca os dados da conta a receber vinculada ao cheque
											//Monta o sql
											$sql = "SELECT * FROM contas_receber WHERE id = $ContaReceberId";
											
											//Executa a query
											$resultadoReceber = mysql_query($sql);
											
											//Monta o array dos dados
											$campos_receber = mysql_fetch_array($resultadoReceber);
											
											$NovaObservacao = "Valor de R$ $ValorCheque estornado referente ao cheque Nro $NumeroCheque DEVOLVIDO\n\n" . $campos_receber["observacoes"];
											$NovoValor = $campos_receber["valor_recebido"] - $ValorCheque;
											
											mysql_query("UPDATE contas_receber SET valor_recebido = '$NovoValor', observacoes = '$NovaObservacao', situacao = 1 WHERE id = $ContaReceberId"); 
										
										}
										
										//Caso tiver uma conta a pagar vinculada
										if ($ContaPagarId > 0)
										{
										
											//Busca os dados da conta a pagar vinculada ao cheque
											//Monta o sql
											$sql = "SELECT * FROM contas_pagar WHERE id = $ContaPagarId";
											
											//Executa a query
											$resultadoPagar = mysql_query($sql);
											
											//Monta o array dos dados
											$campos_pagar = mysql_fetch_array($resultadoPagar);
											
											$NovaObservacao = "Valor de R$ $ValorCheque estornado referente ao cheque Nro $NumeroCheque DEVOLVIDO\n\n" . $campos_receber["observacoes"];
											$NovoValor = $campos_pagar["valor_pago"] - $ValorCheque;
											
											mysql_query("UPDATE contas_pagar SET valor_pago = '$NovoValor', observacoes = '$NovaObservacao', situacao = 1 WHERE id = $ContaPagarId"); 
										
										}
										
										//Verifica se tem um formando associado
										if ($FormandoId > 0)
										{
										
											//Marca o formando como inadimplente
											mysql_query("UPDATE eventos_formando SET situacao = 2 WHERE id = $FormandoId"); 
										
										}
										
										//Marca o cheque como 1, para nao processar mais uma vez
										mysql_query("UPDATE cheques SET altera_contas = 1 WHERE id = $id"); 
										
									}
																		
								}
								
							
														
								//Exibe a mensagem de inclusão com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Cheque alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
							}

							//Recebe os valores passados do form anterior para edição do registro
							if($_POST) 
							{
								
								$ChequeId = $_POST["Id"]; 
							} 
							
							else 
							
							{
								
								$ChequeId = $_GET["Id"]; 
							}
							
							//Monta o sql
							$sql = "SELECT * FROM cheques WHERE id = $ChequeId";
							
							//Executa a query
							$resultado = mysql_query($sql);
							
							//Monta o array dos dados
							$campos = mysql_fetch_array($resultado);
							
							//Efetua o switch para o campo de pre-datado
							switch ($campos[pre_datado]) 
							{
								  
								case 00: $ativo_status = "value='1'";	  break;
								case 01: $ativo_status = "value='1' checked";  break;
								
							}

							//Efetua o switch para o campo de status do cheque
							switch ($campos[status]) 
							{
								case 01: 
									$status_1 = "checked";	
									$status_2 = ""; 
									$status_3 = ""; 
									$tr_display = "display: none";
								break;
								case 02: 
									$status_1 = "";		
									$status_2 = "checked"; 
									$status_3 = ""; 
									$tr_display = "display: none";
								break;
								case 03: 
									$status_1 = "";		
									$status_2 = ""; 
									$status_3 = "checked"; 
									$tr_display = "";
								break;
							}
							
							//Efetua o switch para o campo de disposicao do cheque
							switch ($campos[disposicao]) 
							{
								case 01: 
									$disposicao_1 = "checked";	
									$disposicao_2 = ""; 
									$disposicao_3 = ""; 
									$disposicao_4 = "";	
									$disposicao_5 = ""; 
									$disposicao_6 = ""; 
									$disposicao_7 = "";	
									$disposicao_8 = ""; 
									$disposicao_9 = "";
									$disposicao_10 = ""; 
									$disposicao_11 = ""; 
								break;
								case 02: 
									$disposicao_1 = "";	
									$disposicao_2 = "checked"; 
									$disposicao_3 = ""; 
									$disposicao_4 = "";	
									$disposicao_5 = ""; 
									$disposicao_6 = ""; 
									$disposicao_7 = "";	
									$disposicao_8 = ""; 
									$disposicao_9 = ""; 
									$disposicao_10 = ""; 
								break;
								case 03: 
									$disposicao_1 = "";	
									$disposicao_2 = ""; 
									$disposicao_3 = "checked"; 
									$disposicao_4 = "";	
									$disposicao_5 = ""; 
									$disposicao_6 = ""; 
									$disposicao_7 = "";	
									$disposicao_8 = ""; 
									$disposicao_9 = ""; 
									$disposicao_10 = ""; 
									$disposicao_11 = "";
								break;
								case 04: 
									$disposicao_1 = "";	
									$disposicao_2 = ""; 
									$disposicao_3 = ""; 
									$disposicao_4 = "checked";	
									$disposicao_5 = ""; 
									$disposicao_6 = ""; 
									$disposicao_7 = "";	
									$disposicao_8 = ""; 
									$disposicao_9 = ""; 
									$disposicao_10 = ""; 
									$disposicao_11 = "";
								break;
								case 05: 
									$disposicao_1 = "";	
									$disposicao_2 = ""; 
									$disposicao_3 = ""; 
									$disposicao_4 = "";	
									$disposicao_5 = "checked"; 
									$disposicao_6 = ""; 
									$disposicao_7 = "";	
									$disposicao_8 = ""; 
									$disposicao_9 = "";
									$disposicao_10 = "";
									$disposicao_11 = "";									
								break;
								case 06: 
									$disposicao_1 = "";	
									$disposicao_2 = ""; 
									$disposicao_3 = ""; 
									$disposicao_4 = "";	
									$disposicao_5 = ""; 
									$disposicao_6 = "checked"; 
									$disposicao_7 = "";	
									$disposicao_8 = ""; 
									$disposicao_9 = "";
									$disposicao_11 = "";
								break;
								case 07: 
									$disposicao_1 = "";	
									$disposicao_2 = ""; 
									$disposicao_3 = ""; 
									$disposicao_4 = "";	
									$disposicao_5 = ""; 
									$disposicao_6 = ""; 
									$disposicao_7 = "checked";	
									$disposicao_8 = ""; 
									$disposicao_9 = "";
									$disposicao_10 = ""; 
									$disposicao_11 = "";
								break;
								case 08: 
									$disposicao_1 = "";	
									$disposicao_2 = ""; 
									$disposicao_3 = ""; 
									$disposicao_4 = "";	
									$disposicao_5 = ""; 
									$disposicao_6 = ""; 
									$disposicao_7 = "";	
									$disposicao_8 = "checked"; 
									$disposicao_9 = ""; 
									$disposicao_10 = "";
									$disposicao_11 = "";
								break;
								case 09: 
									$disposicao_1 = "";	
									$disposicao_2 = ""; 
									$disposicao_3 = ""; 
									$disposicao_4 = "";	
									$disposicao_5 = ""; 
									$disposicao_6 = ""; 
									$disposicao_7 = "";	
									$disposicao_8 = ""; 
									$disposicao_9 = "checked"; 
									$disposicao_10 = "";
									$disposicao_11 = "";
								break;
								case 10: 
									$disposicao_1 = "";	
									$disposicao_2 = ""; 
									$disposicao_3 = ""; 
									$disposicao_4 = "";	
									$disposicao_5 = ""; 
									$disposicao_6 = ""; 
									$disposicao_7 = "";	
									$disposicao_8 = ""; 
									$disposicao_9 = "";
									$disposicao_10 = "checked"; 
									$disposicao_11 = "";
								break;
								case 11: 
									$disposicao_1 = "";	
									$disposicao_2 = ""; 
									$disposicao_3 = ""; 
									$disposicao_4 = "";	
									$disposicao_5 = ""; 
									$disposicao_6 = ""; 
									$disposicao_7 = "";	
									$disposicao_8 = ""; 
									$disposicao_9 = "";
									$disposicao_10 = ""; 
									$disposicao_11 = "checked";
								break;
							}
						?>

					<table cellSpacing='0' cellPadding='0' width='100%' border='0'>
						<tr>
							<td width="484"></td>
						</tr>
						<tr>
							<td style="PADDING-BOTTOM: 2px">
								<input name="Id" type="hidden" value="<?php echo $ChequeId ?>" />
								<input name='Alterar' type='submit' class='button' id="Alterar" accessKey='S' title="Salva o registro atual [Alt+S]" value='Salvar Registro' >
								<input class=button title="Cancela as alterações efetuadas no registro [Alt+C]" accessKey='C' name='Reset' type='reset' id='Reset' value='Cancela Alterações'>
							</td>
							<td align="right">
								<input class="button" title="Retorna a visualizacao do cheque" name='btnVoltar' type='button' id='btnVoltar' value='Voltar' style="width:70px" onclick="wdCarregarFormulario('ChequeTerceiroExibe.php?ChequeId=<?php echo $ChequeId ?>','conteudo')" />						 
							</td>
						</tr>					
					</table>
           
					<table class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
						<tr>
							<td class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='18'>
								<table cellSpacing=0 cellPadding=0 width="100%" border=0>
									<tr>
										<td class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os novos dados do registro e clique em [Salvar Registro] </TD>
									</tr>
								</table>				 
							</td>
						</tr>
						<tr>
							<td class="dataLabel">
								<span class="dataLabel">Número:</span>             
							</td>
							<td colspan="3" class=tabDetailViewDF>
								<input name="edtNumero" type="text" class='datafield' id="edtNumero" style="width: 60px" size="60" maxlength="10" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos[numero_cheque] ?>">
							</td>
						</tr>
						<tr>
							<td class="dataLabel" width="20%">
								<span class="dataLabel">Pré-datado:</span>             
							</td>
							<td class="tabDetailViewDF">
								<input name="chkPredatado" type="checkbox" id="chkPredatado" <?php echo $ativo_status ?> > Pré-datado				 
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
									$objData->strValor = DataMySQLRetornar($campos["bom_para"]);
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
							<td width="140" class='dataLabel'>Banco:</td>
							<td colspan="3" class='tabDetailViewDF'>
								<select name="cmbBancoId" id="cmbBancoId" style="width:350px">
								<?php 
									while ($lookup_banco = mysql_fetch_object($dados_banco)) 
									{ 
								?>
								<option <?php if ($lookup_banco->id == $campos[banco_id]) 
								{
									echo " selected ";
								} 
								?>
								value="<?php echo $lookup_banco->id ?>"><?php echo $lookup_banco->codigo . " - " . $lookup_banco->nome ?>				 
								</option>
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
								<input name="edtAgenciaTerceiro" type="text" class='datafield' id="edtAgenciaTerceiro" size="10" maxlength="10" value="<?php echo $campos["agencia"] ?>">
							</td>
							<td class='dataLabel' width='100'>
								<span class="dataLabel">Nº Conta:</span>             
							</td>
							<td class="tabDetailViewDF">
								<input name="edtContaTerceiro" type="text" class='datafield' id="edtContaTerceiro" size="10" maxlength="10" value="<?php echo $campos["conta"] ?>">
							</td>
						</tr>
						<tr>
							<td class="dataLabel">Titular:</td>
							<td colspan="3" valign="middle" class="tabDetailViewDF">
								<input name="edtFavorecido" type="text" class="datafield" style="width: 350px" size="84" maxlength="80" value="<?php echo $campos["favorecido"] ?>">
							</td>
						</tr>					           
						<tr>
							<td class='dataLabel'>Valor:</td>
							<td colspan="3" class=tabDetailViewDF>
								<?php
									
									//Acerta a variável com o valor a alterar
									$valor_alterar = str_replace(".",",",$campos[valor]);
								
									//Verifica se o cheque não está compensado
									if ($campos[status] > 1) 
									{
									
										echo "R$ " . number_format($campos[valor], 2, ",", ".");
										echo "<input name='edtValor' type='hidden' value='$valor_alterar'>";									
									
									} 
									
									else 
									
									{
	
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
									
										//Define nome do componente
										$objWDComponente->strNome = "edtValor";
										//Define o tamanho do componente
										$objWDComponente->intSize = 16;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "$valor_alterar";
										//Busca a descrição do XML para o componente
										$objWDComponente->strLabel = "";
										//Determina um ou mais eventos para o componente
										$objWDComponente->strEvento = "";
										//Define numero de caracteres no componente
										$objWDComponente->intMaxLength = 14;
									
										//Cria o componente edit
										$objWDComponente->Criar();  
								
									}
								?>								
							</td>
						</tr>
						<tr>
							<td class='dataLabel'>Data Recebimento:</td>
							<td colspan="3" class=tabDetailViewDF>
					 			<?php
									//Define a data do formulário
									$objData->strFormulario = "frmChequeAltera";  
									//Nome do campo que deve ser criado
									$objData->strNome = "edtDataRecebimento";
									//Valor a constar dentro do campo (p/ alteração)
									$objData->strValor = DataMySQLRetornar($campos[data_recebimento]);
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
							<td class='dataLabel'>Status:</td>
							<td colspan="3" class=tabDetailViewDF>									
								<table width="400" cellpadding="0" cellspacing="0">
									<tr valign="middle">
										<td width="111" height='20'>
											<input name="edtStatus" type="radio" value="1" <?php echo $status_1 ?> onclick="oculta(900);oculta(922);" /> Recebido
										</td>
										<td width="112">
											<input type="radio" name="edtStatus" value="2" <?php echo $status_2 ?> onclick="oculta(900);oculta(922);" /> Compensado
										</td>
										<td>
											<input type="radio" name="edtStatus" value="3" <?php echo $status_3 ?> onclick="oculta(900); oculta(922); var ID = document.getElementById(922); ID.style.display = '';var ID2 = document.getElementById(900); ID2.style.display = '';" /> Devolvido
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr id="900" style="<?php echo $tr_display ?>">
								<td class="dataLabel">Data de Devolução:</td>
								<td colspan="3" class="tabDetailViewDF">
									<?php
										
										if ($campos[data_devolucao] != "0000-00-00")
										{
										
											$edtDataDevolucaoFormata = DataMySQLRetornar($campos[data_devolucao]);
										
										}
										
										else
										
										{
										
											$edtDataDevolucaoFormata = '';
										
										}
										
										//Define a data do formulário
										$objData->strFormulario = "frmChequeAltera";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataDevolucao";
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = $edtDataDevolucaoFormata;
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
						<tr id="922" style="<?php echo $tr_display ?>">
							<td class="dataLabel">Disposição:</td>
							<td colspan="3" class="tabDetailViewDF">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr valign="middle">
										<td width="130" height="20">
											<input type="radio" name="edtDisposicao" value="1" <?php echo $disposicao_1 ?> /> Primeiro Contato
										</td>
										<td width="130">
											<input type="radio" name="edtDisposicao" value="2" <?php echo $disposicao_2 ?> /> Em Negociação
										</td>
										<td width="130">
											<input type="radio" name="edtDisposicao" value="3" <?php echo $disposicao_3 ?> /> Reapresentado
										</td>
										<td>
											<input type="radio" name="edtDisposicao" value="4" <?php echo $disposicao_4 ?> /> Pago
										</td>
									</tr>
									<tr>
										<td style="padding-top: 4px">
											<input type="radio" name="edtDisposicao" value="5" <?php echo $disposicao_5 ?> /> Para Registrar
										</td>
										<td style="padding-top: 4px">
											<input type="radio" name="edtDisposicao" value="6" <?php echo $disposicao_6 ?> /> No SPC
										</td>
										<td style="padding-top: 4px">
											<input type="radio" name="edtDisposicao" value="7" <?php echo $disposicao_7 ?> /> Não Pode SPC
										</td>
										<td style="padding-top: 4px">
											<input type="radio" name="edtDisposicao" value="8" <?php echo $disposicao_8 ?> /> SPC Pago
										</td>
										
									</tr>
									<tr>
										<td style="padding-top: 4px">
											<input type="radio" name="edtDisposicao" value="9" <?php echo $disposicao_9 ?> /> Devolvido ao Titular
										</td>
										<td style="padding-top: 4px">
											<input type="radio" name="edtDisposicao" value="10" <?php echo $disposicao_10 ?> /> Cobrança Judicial
										</td>
										<td colspan="2" style="padding-top: 4px">
											<input type="radio" name="edtDisposicao" value="11" <?php echo $disposicao_11 ?> /> ACC
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class='dataLabel' valign="top">Observações:</td>
							<td colspan='3' class=tabDetailViewDF>
								<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 100px"><?php echo $campos[observacoes] ?></textarea>
							</td>
						</tr>             
					</table>
				</td>
			</tr>
		</form>
	</table>  	 
	</tr>
</table>
