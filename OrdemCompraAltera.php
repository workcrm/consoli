<?php 
###########
## Módulo para alteração da Ordem de Compra
## Criado: 29/03/2012 - Maycon Edinger
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

//Monta o lookup da tabela de fornecedores
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//Monta o lookup da tabela de departamentos
//Monta o SQL
$lista_departamento = "SELECT id, nome FROM departamentos WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";
//Executa a query
$dados_departamento = mysql_query($lista_departamento);

//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_evento = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_evento = mysql_query($lista_evento);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="JavaScript">
	
	//Função que alterna a visibilidade do painel especificado.
	function oculta(id)
	{
		
		ID = document.getElementById(id);
		ID.style.display = "none";
	}

	function wdSubmitContaAltera() 
	{
		
		var Form;
		Form = document.frmContaAltera;
   
		if (Form.edtData.value.length == 0) 
		{
			
			alert("É necessário Informar a Data da Ordem de Compra !");
			Form.edtData.focus();
			return false;
		
		}
		
		if (Form.cmbFornecedorId.value == 0) 
		{
			
			alert("É necessário Selecionar um Fornecedor !");
			Form.cmbFornecedorId.focus();
			return false;
		
		}	
   
		return true;
	}
</script>

<form name="frmContaAltera" action="sistema.php?ModuloNome=OrdemCompraAltera" method="post" onSubmit="return wdSubmitContaAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração da Ordem de Compra</span></td>
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
						
							//Verifica se a flag está vindo de uma postagem para liberar a alteração
							if($_POST['Submit'])
							{

								//Recupera os valores do formulario e alimenta as variáveis
								$id = $_POST["Id"];
								$edtData = DataMySQLInserir($_POST["edtData"]);
								$edtSolicitante = $_POST["edtSolicitante"];
								$cmbDepartamentoId = $_POST["cmbDepartamentoId"];
								$cmbEventoId = $_POST["cmbEventoId"];
								$cmbFornecedorId = $_POST["cmbFornecedorId"];
								$edtTransportadora = $_POST["edtTransportadora"];
								$edtCondPgto = $_POST["edtCondPgto"];
								$edtPrazoEntrega = DataMySQLInserir($_POST["edtPrazoEntrega"]);
								$edtObs = $_POST["edtObs"];
								$edtDesconto = MoneyMySQLInserir($_POST["edtDesconto"]);
								$edtOperadorId = $usuarioId;

								//Executa a query de alteração da conta
								$sql = mysql_query("UPDATE ordem_compra SET 
													data = '$edtData',
													solicitante = '$edtSolicitante',
													departamento_id = '$cmbDepartamentoId', 
													evento_id = '$cmbEventoId',
													fornecedor_id = '$cmbFornecedorId', 
													transportadora = '$edtTransportadora', 
													cond_pgto = '$edtCondPgto',
													prazo_entrega = '$edtPrazoEntrega',
													obs = '$edtObs',
													desconto = '$edtDesconto',
													alteracao_timestamp = now(),
													alteracao_operador_id = '$edtOperadorId'
													WHERE id = '$id' ");			 

								//Exibe a mensagem de alteração com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Ordem de Compra Alterada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500);</script>";
							}
							
							if ($_GET["OrdemId"]) 
							{
								
								$OrdemId = $_GET["OrdemId"];
							} 
							
							else 
							
							{
								
								$OrdemId = $_POST["Id"];
							
							}

							//Monta o sql para busca da conta
							$sql = "SELECT * FROM ordem_compra WHERE id = $OrdemId";

							//Executa a query
							$resultado = mysql_query($sql);

							//Monta o array dos dados
							$campos = mysql_fetch_array($resultado);
							
						?>

						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td width="100%"> </td>
							</tr>
							<tr>
								<td style="padding-bottom: 2px">
									<input name="Id" type="hidden" value="<?php echo $OrdemId ?>" />
									<input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Ordem de Compra">
									<input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações">
								</td>
								<td width="36" align="right">
									<input class="button" title="Retorna a exibição do registro" name="btnVoltar" type="button" id="btnVoltar" value="Retornar a Ordem de Compra" onclick="wdCarregarFormulario('OrdemCompraExibe.php?OrdemId=<?php echo $OrdemId ?>','conteudo')" />						
								</td>
							</tr>
						</table>
					   
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="padding-right: 0px; padding-left: 0px; font-weight: normal; padding-bottom: 0px; padding-top: 0px; border-bottom: 0px" colSpan="2">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="text-align: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da Ordem de Compra e clique em [Salvar Ordem de Compra]
											</td>
										</tr>
									</table>						 
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">
									<span class="dataLabel">Data:</span>             
								</td>
								<td class="tabDetailViewDF">
									<?php
										//Define a data do formulário
										$objData->strFormulario = "frmContaAltera";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtData";
										$objData->strRequerido = true;
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = DataMySQLRetornar($campos[data]);
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
								<td class="dataLabel">Solicitante:</td>
								<td valign="middle" class="tabDetailViewDF">
									<input name="edtSolicitante" type="text" class="campo" id="edtSolicitante" style="width: 200px" maxlength="35" value="<?php echo $campos[solicitante] ?>">
								</td>
							</tr> 
							<tr>
								<td valign="top" class="dataLabel">Departamento:</td>
								<td class="tabDetailViewDF">
									<select name="cmbDepartamentoId" id="cmbDepartamentoId" style="width:400px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_departamento = mysql_fetch_object($dados_departamento)) 
											{ 
										
										?>
										<option <?php if ($lookup_departamento->id == $campos[departamento_id]) echo " selected " ?>
											value="<?php echo $lookup_departamento->id ?>"><?php echo $lookup_departamento->id . " - " . $lookup_departamento->nome ?> </option>
											<?php } ?>
									</select>              
								</td>
							</tr>				
							<tr>
								<td valign="top" class="dataLabel">Fornecedor:</td>
								<td class="tabDetailViewDF">
									<select name="cmbFornecedorId" id="cmbFornecedorId" style="width:400px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) 
											{ 
										
										?>
										<option <?php if ($lookup_fornecedor->id == $campos[fornecedor_id]) echo " selected " ?>
											value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . " - " . $lookup_fornecedor->nome ?> </option>
											<?php } ?>
									</select>              
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Evento:</td>
								<td class="tabDetailViewDF">
									<select name="cmbEventoId" id="cmbEventoId" style="width:400px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_evento = mysql_fetch_object($dados_evento)) 
											{ 
										
										?>
										<option <?php if ($lookup_evento->id == $campos[evento_id]) echo " selected " ?>
											value="<?php echo $lookup_evento->id ?>"><?php echo $lookup_evento->id . " - " . $lookup_evento->nome ?> </option>
											<?php } ?>
									</select>              
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Transportadora:</td>
								<td colspan="3" valign="middle" class=tabDetailViewDF>
									<input name="edtTransportadora" type="text" class="datafield" id="edtTransportadora" style="width: 200px" maxlength="35" value="<?php echo $campos[transportadora] ?>">
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Condição Pagamento:</td>
								<td class="tabDetailViewDF">
									<input name="edtCondPgto" type="text" class="datafield" id="edtCondPgto" style="width: 200px" maxlength="20" value="<?php echo $campos[cond_pgto] ?>">
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Prazo de Entrega:</td>
								<td class="tabDetailViewDF">
									<?php
										
										
										//Define a data do formulário
										$objData->strFormulario = "frmContaAltera";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtPrazoEntrega";
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = DataMySQLRetornar($campos[prazo_entrega]);
										//Cria o componente com seu calendario para escolha da data
										$objData->CriarData();
										
									?>
								</td>
							</tr>         
							<tr>
								<td valign="top" class="dataLabel">Observações:</td>
								<td class="tabDetailViewDF">
									<textarea name="edtObs" wrap="virtual" class="datafield" id="edtObs" style="width: 100%; height: 130px"><?php echo $campos[obs] ?></textarea>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Desconto: </td>
								<td class="tabDetailViewDF">
									<?php
										//Acerta a variável com o valor a alterar
										$valor_alterar = str_replace(".",",",$campos['desconto']);
										
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtDesconto";
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
									?>												
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
