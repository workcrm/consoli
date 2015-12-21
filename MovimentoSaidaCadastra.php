<?php 
###########
## Módulo para cadastro de Movimentacao de saidas de itens
## Criado: 15/12/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
## 
###########


//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
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

?>

<script language="JavaScript">
	
	function trocaOpcao(valor, objSel) 
	{
		for (i=0; i < objSel.length; i++)
		{
			qtd = valor.length;
			
			if (objSel.options[i].text.substring(0, qtd).toUpperCase() == valor.toUpperCase()) 
			{
				
				objSel.selectedIndex = i;
				break;
			
			}
		}
	}
	
	//Função que alterna a visibilidade do painel especificado.
	function oculta(id)
	{
		ID = document.getElementById(id);
		ID.style.display = "none";
	}

	function valida_form() 
	{
		var Form;
		Form = document.cadastro;   
	 
		if (Form.edtData.value.length == 0) 
		{
			alert("É necessário Informar a Data !");
			Form.edtData.focus();
			return false;
		}
		
		return true;
	}

</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<?php 

//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_eventos = mysql_query($lista_eventos);

//Efetua o lookup na tabela de produtos
//Monta o sql de pesquisa
$lista_produtos = "SELECT id, nome FROM item_evento WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_produtos = mysql_query($lista_produtos);

//Efetua o lookup na tabela de tipos de movimentacao
//Monta o sql de pesquisa
$lista_tipos = "SELECT id, nome FROM tipos_movimentacao WHERE empresa_id = $empresaId AND tipo = 2 ORDER BY nome";

//Executa a query
$dados_tipos = mysql_query($lista_tipos);

//Monta o lookup da tabela de clientes
//Monta o SQL
$lista_cliente = "SELECT id, nome FROM clientes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_cliente = mysql_query($lista_cliente);

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

?>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Movimentação de Saídas</span></td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">
						<?php
							
							//Recupera os valores vindos do formulário e armazena nas variaveis
							if($_POST["Submit"])
							{

								$edtEmpresaId = $empresaId;
								$edtData = DataMySQLInserir($_POST["edtData"]);
								$cmbItemId = $_POST["cmbItemId"];					
								$cmbTipoMovimentacaoId = $_POST["cmbTipoMovimentacaoId"];
								$cmbClienteId = $_POST["cmbClienteId"];
								$cmbCategoriaId = $_POST["cmbCategoriaId"];
								$cmbEventoId = $_POST["cmbEventoId"];
								$edtNumeroDocumento = $_POST["edtNroDocumento"];
								$edtQuantidade = $_POST["edtQuantidade"];
								$edtValor = MoneyMySQLInserir($_POST["edtValor"]);	          
								
								$edtObservacoes = $_POST["edtObservacoes"];
								$edtOperadorId = $usuarioId;
								
								//Monta o sql e executa a query de inserção da saida
								$sql = mysql_query("INSERT INTO item_evento_movimenta (
													item_id, 
													data,
													movimento,
													tipo_movimentacao_id,
													cliente_id,
													evento_id,
													numero_documento,
													quantidade, 
													valor,													
													obs,
													cadastro_timestamp,
													cadastro_operador_id
									
													) VALUES (
									
													'$cmbItemId',
													'$edtData',
													'2',
													'$cmbTipoMovimentacaoId',
													'$cmbClienteId',
													'$cmbEventoId',
													'$edtNumeroDocumento',
													'$edtQuantidade',
													'$edtValor',								
													'$edtObservacoes',
													now(),
													'$edtOperadorId'				
													);");
								
								//Atualiza o status do produto
								$atu_produto = mysql_query("UPDATE item_evento SET
															estoque_atual = estoque_atual - $edtQuantidade,
															sai_data = '$edtData', 
															sai_documento = '$edtNumeroDocumento',
															sai_evento_id = '$cmbEventoId',
															sai_cliente_id = '$cmbClienteId',
															sai_quantidade = '$edtQuantidade',
															sai_documento = '$edtNumeroDocumento'
															WHERE id = $cmbItemId");
								
								//Exibe a mensagem de inclusão com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Saída cadastrada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
								
								
							//Fecha o if de postagem
							}
						
						?>

						<table cellspacing="0" cellpadding="0" width="520" border="0">
							<tr>
								<td width="484">
									<form id="form" name="cadastro" action="sistema.php?ModuloNome=MovimentoSaidaCadastra" method="post" onsubmit="return valida_form()">
								</td>
							</tr>
							<tr>
								<td style="PADDING-BOTTOM: 2px">
									<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Saída" />
									<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
								</td>
							</tr>
						</table>
           
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colSpan="21">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left">
												<img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da movimentação de Saída e clique em [Salvar Saída]									 
											</td>
										</tr>
									</table>			 
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">
									<span class="dataLabel">Data:</span>             
								</td>
								<td colspan="4" class="tabDetailViewDF">
									<?php
										//Define a data do formulário
										$objData->strFormulario = "cadastro";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtData";
										$objData->strRequerido = true;
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = Date('d/m/Y', mktime());
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
								<td width="140" class="dataLabel">Produto:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<input name="edtProdutoCodigoBarras" type="text" class="datafield" id="edtProdutoCodigoBarras" onblur="this.value = this.value.replace(/^[0]+/g,''); var str = new String(this.value); this.value = str.substring(0,(str.length - 1)); trocaOpcao(this.value, document.cadastro.cmbItemId);" style="width: 100px" maxlength="13" />&nbsp;Posicione o código de barras na leitora.             
									<br/>
									<br/>
									<select name="cmbItemId" id="cmbItemId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_produtos = mysql_fetch_object($dados_produtos)) 
											{ 
											
										?>
										<option value="<?php echo $lookup_produtos->id ?>"><?php echo $lookup_produtos->id . " - " . $lookup_produtos->nome ?></option>
										<?php 
										
											} 
										
										?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Tipo de Movimentação:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="cmbTipoMovimentacaoId" id="cmbTipoMovimentacaoId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_tipos = mysql_fetch_object($dados_tipos)) 
											{ 
											
										?>
										<option value="<?php echo $lookup_tipos->id ?>"><?php echo $lookup_tipos->id . " - " . $lookup_tipos->nome ?></option>
										<?php 
										
											} 
										
										?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Cliente:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="cmbClienteId" id="cmbClienteId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_cliente = mysql_fetch_object($dados_cliente)) 
											{ 
										
										?>
										<option value="<?php echo $lookup_cliente->id ?>"><?php echo $lookup_cliente->id . " - " . $lookup_cliente->nome ?></option>
										<?php 
										
											} 
										
										?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="50">Evento:</td>
								<td colspan="4" width="490" class="tabDetailViewDF">
									<select name="cmbEventoId" id="cmbEventoId" style="width: 400px" >                  
										<option value="0">Selecione uma Opção</option>
										<?php 
											//Cria o componente de lookup de eventos
											while ($lookup_eventos = mysql_fetch_object($dados_eventos)) 
											{ 
										?>
										<option value="<?php echo $lookup_eventos->id ?>"><?php echo $lookup_eventos->id . " - " . $lookup_eventos->nome ?></option>
										<?php 
							
											//Fecha o while
											} 
						
										?>
									</select>
								</td>
							</tr>
							
							<tr>
								<td width="140" class="dataLabel">Nº Documento/Solicitante:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<input name="edtNroDocumento" type="text" class="datafield" id="edtNroDocumento" style="width: 140px" maxlength="20" />             
								</td>
							</tr>
          
							<tr>
								<td width="140" valign="top" class="dataLabel">Quantidade:</td>
								<td colspan="4" width="173" class="tabDetailViewDF">
									<input name="edtQuantidade" type="text" class="campo" id="edtQuantidade" style="width: 50px" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">																				
								</td>
							</tr>              
							<tr>
								<td width="140" valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
								<td colspan="4" class="tabDetailViewDF">
									<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>    							
								</td>
							</tr>							
						</table>
					</td>
				</tr>
			</table>  	 
		</tr>
</table>
</form>