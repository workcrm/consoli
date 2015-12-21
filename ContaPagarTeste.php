<?php 
###########
## Módulo para cadastro de Contas a Pagar
## Criado: 16/04/2007 - Maycon Edinger
## Alterado: 11/07/2007 - Maycon Edinger
## Alterações: 
## 04/06/2007 - Incluídos novos campos
## 18/06/2007 - Aplicado objeto para campo money
## 03/07/2007 - Implementado campo para condição de pagamento
## 04/07/2007	-	Aplicado rotinas para desdobramento da conta em parcelas
## 05/07/2007 - Implementado para incluir o cheque na conta
## 09/07/2007 - Corrigido problema de lançar o vencimento da primeira parcela
## 11/07/2007 - Implementado campo para cadastro de subgrupos e numero do documento
## 07/02/2009 - Implementado para cadstrar o valor 1 para conta originada diretamente pelo módulo
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

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<?php 

//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";
//Executa a query
$dados_eventos = mysql_query($lista_eventos);

//Monta o lookup da tabela de grupos
//Monta o SQL
$lista_grupo = "SELECT * FROM grupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_grupo = mysql_query($lista_grupo);

//Monta o lookup da tabela de clientes (para a pessoa_id)
//Monta o SQL
$lista_cliente = "SELECT id, nome FROM clientes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_cliente = mysql_query($lista_cliente);

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//Monta o lookup da tabela de colaboradores (para a pessoa_id)
//Monta o SQL
$lista_colaborador = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_colaborador = mysql_query($lista_colaborador);

//Monta o lookup da tabela de subgrupos (CONTA_CAIXA) filtrando tipo 2 que é saída (débito)
//Monta o SQL
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '2' ORDER BY nome";
//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);

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
					<td colspan="5" width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Conta a Pagar</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
							<tr>
								<td width="140" class="dataLabel">Conta-caixa:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="cmbSubgrupoId" id="cmbSubgrupoId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											//Monta o while para gerar o combo de escolha
											while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo)) { 
										?>
										<option value="<?php echo $lookup_subgrupo->id ?>"><?php echo $lookup_subgrupo->id . " - " . $lookup_subgrupo->nome ?></option>
										<?php } ?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Centro de Custo:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="cmbGrupoId" id="cmbGrupoId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_grupo = mysql_fetch_object($dados_grupo)) 
											{ 
										
										?>
										<option value="<?php echo $lookup_grupo->id ?>"><?php echo $lookup_grupo->id . " - " . $lookup_grupo->nome ?></option>
										<?php 
											
											} 
											
										?>
									</select>						 						 
								</td>
							</tr>           
							<tr valign="middle">
											<td style="padding-top: 7px"> Cliente:<br/>
												<select name="cmbClienteId" id="cmbClienteId" style="width:350px">
													<option value="0">Selecione uma Opção</option>
													<?php 
														//Monta o while para gerar o combo de escolha
														while ($lookup_cliente = mysql_fetch_object($dados_cliente)) { 
													?>
													<option value="<?php echo $lookup_cliente->id ?>"><?php echo $lookup_cliente->nome ?></option>
													<?php } ?>
												</select>
											</td>
										</tr>
										<tr valign="middle">
											<td style="padding-top: 7px"> Fornecedor:<br/>                  
												<select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
													<option value="0">Selecione uma Opção</option>
													<?php 
														//Monta o while para gerar o combo de escolha
														while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) { 
													?>
													<option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->nome ?></option>
													<?php } ?>
												</select>
											</td>
										</tr>
										<tr valign="middle">
											<td style="padding-top: 7px"> Colaborador:<br/>                  
												<select name="cmbColaboradorId" id="cmbColaboradorId" style="width:350px">
													<option value="0">Selecione uma Opção</option>
													<?php 
														//Monta o while para gerar o combo de escolha
														while ($lookup_colaborador = mysql_fetch_object($dados_colaborador)) { 
													?>
													<option value="<?php echo $lookup_colaborador->id ?>"><?php echo $lookup_colaborador->nome ?></option>
													<?php } ?>
												</select>
											</td>
										</tr><tr>
								<td class="dataLabel" width="50">Evento:</td>
								<td colspan="4" width="490" class="tabDetailViewDF">
									<select name="cmbEventoId" id="cmbEventoId" style="width: 400px" onchange="busca_formandos()">                  
										<option value="0">Selecione uma Opção</option>
										<?php 
											//Cria o componente de lookup de eventos
											while ($lookup_eventos = mysql_fetch_object($dados_eventos)) { 
										?>
										<option value="<?php echo $lookup_eventos->id ?>"><?php echo $lookup_eventos->id . " - " . $lookup_eventos->nome ?></option>
										<?php 
							
											//Fecha o while
											} 
						
										?>
									</select>
								</td>
							</tr>
			</table>  	 
		</tr>
</table>
</form>