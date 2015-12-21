<?php 
###########
## Módulo para Cadastro de Ordens de Compra
## Criado: 28/02/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
	
}

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
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_eventos = mysql_query($lista_eventos);

//Pega o id da ultima OC cadastrada
$next_increment = 0;
$qShowStatus = "SHOW TABLE STATUS LIKE 'ordem_compra'";
$qShowStatusResult = mysql_query($qShowStatus) or die ( "Erro ao obter os dados da OC: " . mysql_error() . "<br/>" . $qShowStatus );

$row = mysql_fetch_assoc($qShowStatusResult);
$numero_oc = $row['Auto_increment'];

?>

<script language="JavaScript">
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

</head>
<body>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Ordem de Compra</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<table id="1" style="display: none" width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td valign="midle"><img src="image/bt_ajuda.gif" width="13" height="16" /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			
			<?php
					
				//Recupera os valores vindos do formulário e armazena nas variaveis
				if($_POST["Submit"])
				{

					$edtData = DataMySQLInserir($_POST["edtData"]);
					$edtSolicitante = $_POST["edtSolicitante"];
					$cmbDepartamentoId = $_POST["cmbDepartamentoId"];
					$cmbEventoId = $_POST["cmbEventoId"];
					$cmbFornecedorId = $_POST["cmbFornecedorId"];
					$edtTransportadora = $_POST["edtTransportadora"];
					$edtCondPgto = $_POST["edtCondPgto"];
					$edtPrazoEntrega = DataMySQLInserir($_POST["edtPrazoEntrega"]);
					$edtObs = $_POST["edtObs"];

					//Monta o sql e executa a query de inserção
					$sql = mysql_query("INSERT INTO ordem_compra (
										data,
										solicitante,
										departamento_id,
										evento_id,
										fornecedor_id, 
										transportadora,
										cond_pgto, 
										prazo_entrega, 
										obs,
										cadastro_timestamp,
										cadastro_operador_id
						
										) VALUES (
						
										'$edtData',
										'$edtSolicitante',
										'$cmbDepartamentoId',
										'$cmbEventoId',
										'$cmbFornecedorId',
										'$edtTransportadora',
										'$edtCondPgto',
										'$edtPrazoEntrega',
										'$edtObs',
										now(),
										'$usuarioId'				
										);");
					
					$oc_id = mysql_insert_id();
					
					$busca_oc = "SELECT
								oc.id,
								oc.data,
								oc.solicitante,
								oc.departamento_id,
								oc.evento_id,
								oc.fornecedor_id,
								oc.transportadora,
								oc.cond_pgto,
								oc.prazo_entrega,
								oc.obs,
								dep.nome AS departamento_nome,
								eve.nome AS evento_nome,
								forn.nome AS fornecedor_nome
								FROM ordem_compra oc
								LEFT OUTER JOIN departamentos dep ON dep.id = oc.departamento_id
								LEFT OUTER JOIN eventos eve ON eve.id = oc.evento_id
								LEFT OUTER JOIN fornecedores forn ON forn.id = oc.fornecedor_id
								WHERE oc.id = $oc_id";
					
					
					$query = mysql_query($busca_oc);
					
					//Monta e percorre o array com os dados da consulta
					while ($dados = mysql_fetch_array($query))
					{
						
						
					?>
					
					<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
						<tr>
							<td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
								<img src='./image/bt_informacao.gif' border='0' />
							</td>
							<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
								<strong>Ordem de Compra cadastrada com sucesso !</strong>
							</td>
						</tr>
					</table>
					<br/>
					<br/>
					<br/>
					<span style="font-size:14px"><b>Dados da Ordem de Compra:</b></span><br/>
					<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
						<tr>
							<td width="130" valign="top" class="dataLabel">Número:</td>
							<td width="620" colspan="3" valign="middle" class="tabDetailViewDF">
								<span style="font-size: 18px"><b>
								<?php echo $oc_id ?>
								</b>
								</span>
							</td>
						</tr>
						<tr>
							<td valign="top" class="dataLabel">Data:</td>
							<td colspan="3" valign="middle" class="tabDetailViewDF">
								<?php echo DataMySQLRetornar($dados["data"]) ?>
							</td>
						</tr>
						<tr>
							<td class="dataLabel">Solicitante:</td>
							<td colspan="3" valign="middle" class="tabDetailViewDF">
								<?php echo $dados["solicitante"] ?>
							</td>
						</tr>
						<tr>
							<td class="dataLabel">Departamento:</td>
							<td colspan="3" valign="middle" class=tabDetailViewDF>
								<?php echo $dados["departamento_nome"] ?>
							</td>
						</tr>
						<tr>
							<td class="dataLabel">Fornecedor:</td>
							<td colspan="3" class="tabDetailViewDF">
								<?php echo $dados["fornecedor_nome"] ?>
							</td>
						</tr>
						<tr>
							<td class="dataLabel">Evento:</td>
							<td colspan="3" class="tabDetailViewDF">
								<?php echo $dados["evento_nome"] ?>
							</td>
						</tr>
						<tr>
							<td class="dataLabel">Transportadora:</td>
							<td colspan="3" valign="middle" class=tabDetailViewDF>
								<?php echo $dados["transportadora"] ?>
							</td>
						</tr>
						<tr>
							<td class="dataLabel">Condição Pagamento:</td>
							<td colspan="3" class="tabDetailViewDF">
								<?php echo $dados["cond_pgto"] ?>
							</td>
						</tr>
						<tr>
							<td class="dataLabel">Prazo de Entrega:</td>
							<td colspan="3" class="tabDetailViewDF">
								<?php echo DataMySQLRetornar($dados["prazo_entrega"]) ?>
							</td>
						</tr>         
						<tr>
							<td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares :</td>
							<td colspan="3" class="tabDetailViewDF">
								<?php echo nl2br($dados["obs"]) ?>
							</td>
						</tr>
					</table>
					<br/>
					<br/>
				
					<?php
					
					//Efetua o lookup na tabela de produtos
					//Monta o sql de pesquisa
					$lista_produtos = "SELECT id, nome FROM item_evento WHERE empresa_id = $empresaId ORDER BY nome";

					//Executa a query
					$dados_produtos = mysql_query($lista_produtos);
					
					?>
					
					<form id="form" name="produtos" action="OrdemCompraProdutoCadastra.php" method="post" onsubmit="" target="recebe_produto"> 
					
					<input name="edtProdutoOc" type="hidden" id="edtProdutoOc" value="<?php echo $oc_id ?>" />
					
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Produtos da Ordem de Compra</span></td>
						</tr>
						<tr>
							<td colspan="5">
								<img src="image/bt_espacohoriz.gif" width="100%" height="12">
							</td>
						</tr>
					</table>
					<table cellspacing="0" cellpadding="0" width="520" border="0">
						<tr>
							<td style="padding-bottom: 2px">
								<input name="Submit" type="submit" class="button" id="Submit" title="Adiciona o produto a ordem de compra" value="Adicionar Produto" />
								<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
							</td>
							<td width="36" align="right">&nbsp;</td>
						</tr>
					</table>
					<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
						<tr>
							<td class="listViewPaginationTdS1" style="padding: 0px; font-weight: normal; padding-bottom: 0px; border-bottom: 0px" colspan="4">
								<table cellspacing="0" cellpadding="0" width="100%" border="0">
									<tr>
										<td class="tabDetailViewDL" style="text-align: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do produto da Ordem de Compra e clique em [Adicionar Produto] </td>
									</tr>
								</table>             
							</td>
						</tr>
						<tr>
							<td width="130" valign="top" class="dataLabel">Produto:</td>
							<td width="620" colspan="3" valign="middle" class="tabDetailViewDF">
								<span style="font-size: 18px"><b>
								<select name="cmbProdutoId" id="cmbProdutoId" style="width:400px">
									<option value="0">Selecione uma Opção</option>
									<?php 
									 
										//Monta o while para gerar o combo de escolha
										while ($lookup_produtos = mysql_fetch_object($dados_produtos)) 
										{ 
									
									?>
									<option value="<?php echo $lookup_produtos->id ?>"><?php echo $lookup_produtos->id . ' - ' . $lookup_produtos->nome ?> </option>
									<?php 
										} 
									?>
								</select>
								</b>
								</span>
							</td>
						</tr>
						<tr>
							<td valign="top" class="dataLabel">Quantidade:</td>
							<td valign="middle" class="tabDetailViewDF">
								<input name="edtProdutoQuantidade" type="text" class="datafield" id="edtProdutoQuantidade" style="width: 80px" maxlength="8" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" /> 
							</td>
						</tr>
						<tr>
							<td valign="top" class="dataLabel">Preço Unitário:</td>
							<td valign="middle" class="tabDetailViewDF">
								<?php
									//Acerta a variável com o valor a alterar
									$valor_alterar = str_replace(".",",",$campos[valor]);
									
									//Cria um objeto do tipo WDEdit 
									$objWDComponente = new WDEditReal();
									
									//Define nome do componente
									$objWDComponente->strNome = "edtProdutoValor";
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
					</table>
					
					</form>
					
					<?php
					
					}
					
				}
				
				else
				
				{

			?>
			<form id="form" name="cadastro" action="sistema.php?ModuloNome=OrdemCompraCadastra" method="post" onsubmit="return valida_form()">
			
			<table cellspacing="0" cellpadding="0" width="520" border="0">
				<tr>
					<td style="padding-bottom: 2px">
						<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Ordem de Compra" />
						<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
					</td>
					<td width="36" align="right">&nbsp;</td>
				</tr>
			</table>
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td class="listViewPaginationTdS1" style="padding: 0px; font-weight: normal; padding-bottom: 0px; border-bottom: 0px" colspan="4">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="tabDetailViewDL" style="text-align: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da Ordem de Compra e clique em [Salvar Ordem de Compra] </td>
							</tr>
						</table>             
					</td>
				</tr>
				<tr>
					<td width="130" valign="top" class="dataLabel">Número:</td>
					<td width="620" colspan="3" valign="middle" class="tabDetailViewDF">
						<span style="font-size: 18px"><b>
						<?php
							
							echo $numero_oc;
							
						?>
						</b>
						</span>
					</td>
				</tr>
				<tr>
					<td valign="top" class="dataLabel">Data:</td>
					<td colspan="3" valign="middle" class="tabDetailViewDF">
						<?php
							
							//Define a data do formulário
							$objData->strFormulario = "cadastro";  
							//Nome do campo que deve ser criado
							$objData->strNome = "edtData";
							//Valor a constar dentro do campo (p/ alteração)
							$objData->strValor = Date('d/m/Y', mktime());
							//Cria o componente com seu calendario para escolha da data
							$objData->CriarData();
							
						?>
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Solicitante:</td>
					<td colspan="3" valign="middle" class="tabDetailViewDF">
						<input name="edtSolicitante" type="text" class="campo" id="edtSolicitante" style="width: 200px" maxlength="35" />
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Departamento:</td>
					<td colspan="3" class="tabDetailViewDF">
						<select name="cmbDepartamentoId" id="cmbDepartamentoId" style="width:400px">
							<option value="0">Selecione uma Opção</option>
							<?php 
							 
								//Monta o while para gerar o combo de escolha
								while ($lookup_departamento = mysql_fetch_object($dados_departamento)) 
								{ 
							
							?>
							<option value="<?php echo $lookup_departamento->id ?>"><?php echo $lookup_departamento->id . ' - ' . $lookup_departamento->nome ?> </option>
							<?php 
								
								} 
								
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Fornecedor:</td>
					<td colspan="3" class="tabDetailViewDF">
						<select name="cmbFornecedorId" id="cmbFornecedorId" style="width:400px">
							<option value="0">Selecione uma Opção</option>
							<?php 
							 
								//Monta o while para gerar o combo de escolha
								while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) 
								{ 
							
							?>
							<option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . ' - ' . $lookup_fornecedor->nome ?> </option>
							<?php 
								
								} 
								
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Evento:</td>
					<td colspan="3" class="tabDetailViewDF">
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
					<td class="dataLabel">Transportadora:</td>
					<td colspan="3" valign="middle" class=tabDetailViewDF>
						<input name="edtTransportadora" type="text" class="datafield" id="edtTransportadora" style="width: 200px" maxlength="35">
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Condição Pagamento:</td>
					<td colspan="3" class="tabDetailViewDF">
						<input name="edtCondPgto" type="text" class="datafield" id="edtCondPgto" style="width: 200px" maxlength="20">
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Prazo de Entrega:</td>
					<td colspan="3" class="tabDetailViewDF">
						<?php
							//Define a data do formulário
							$objData->strFormulario = "cadastro";  
							//Nome do campo que deve ser criado
							$objData->strNome = "edtPrazoEntrega";
							//Valor a constar dentro do campo (p/ alteração)
							$objData->strValor = '';
							//Cria o componente com seu calendario para escolha da data
							$objData->CriarData();
						?>
					</td>
				</tr>         
				<tr>
					<td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares :</td>
					<td colspan="3" class="tabDetailViewDF">
						<textarea name="edtObs" wrap="virtual" class="datafield" id="edtObs" style="width: 100%; height: 130px"></textarea>
					</td>
				</tr>
			</table>
			<?php
				
				}
				
			?>
			<br/>
			<iframe name="recebe_produto" id="recebe_produto" width="798" height="500" src="" frameborder="0"></iframe>
		</td>
	</tr>
</table>
</form>