<?php 
###########
## Módulo para cadastro de entradas e baixas da Ordem de Compra
## Criado: 18/04/2012 - Maycon Edinger
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

//Recupera o id da OC e produto
if($_POST) 
{
	
	$OrdemId = $_POST["OrdemId"]; 
	$ProdutoId = $_POST["ProdutoId"];
	$ItemId = $_POST["ItemId"];

} 

else 

{
  
	$OrdemId = $_GET["OrdemId"]; 
	$ProdutoId = $_GET["ProdutoId"];
	$ItemId = $_GET["ItemId"];

}

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA)
{
	$ANO = 0000;
	$MES = 00;
	$DIA = 00;
	$HORA = "00:00:00";
	$data_array = split("[- ]",$DATA);
	
	if ($DATA <> "")
	{
		
		$ANO = $data_array[0];
		$MES = $data_array[1];
		$DIA = $data_array[2];
		$HORA = $data_array[3];
		return $DIA."/".$MES."/".$ANO. " - " . $HORA;
	
	}
	
	else 
	
	{
		
		$ANO = 0000;
		$MES = 00;
		$DIA = 00;
		return $DIA."/".$MES."/".$ANO;
	
	}

}

//Efetua o lookup na tabela de produtos
//Monta o sql de pesquisa
$sql_produtos = "SELECT 
				prod.id,
				prod.ordem_compra_id,
				prod.produto_id,
				prod.quantidade,
				prod.entrada,
				prod.valor_unitario,
				produto.id AS produto_id,
				produto.nome AS produto_nome,
				forn.id AS fornecedor_id,
				forn.nome AS fornecedor_nome
				FROM ordem_compra_produto prod
				LEFT OUTER JOIN item_evento produto ON produto.id = prod.produto_id
				LEFT OUTER JOIN ordem_compra oc ON oc.id = prod.ordem_compra_id
				LEFT OUTER JOIN fornecedores forn ON forn.id = oc.fornecedor_id
				WHERE prod.produto_id = $ProdutoId
				AND prod.ordem_compra_id = $OrdemId";
				

//Executa a query
$resultado = mysql_query($sql_produtos);

//Monta o array dos campos
$dados_produto = mysql_fetch_array($resultado);

$edtQuantidade = $dados_produto["quantidade"];
$edtEntrada = $dados_produto["entrada"];
$edtSaldo = number_format($dados_produto["quantidade"] - $dados_produto["entrada"],2,'.','');
$edtItemId = $dados_produto["id"];

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
	Form = document.entradas;
	
	if (Form.edtData.value.length == 0) 
	{
	
		alert("É necessário informar a data da entrada !");
		return false;
	
	}
	
	if (Form.edtNf.value.length == 0) 
	{
	
		alert("É necessário informar a NF entrada !");
		return false;
	
	}
	
	if (Form.edtQuantidade.value.length == 0) 
	{
	
		alert("É necessário informar a quantidade do produto da entrada !");
		return false;
	
	}
	
	if (Form.edtQuantidade.value == 0) 
	{
	
		alert("A Quantidade da Entrada nao pode ser ZERO !");
		return false;
	
	}
	
	if (Form.edtValor.value.length == 0) 
	{
	
		alert("É necessário informar o valor do produto da entrada !");
		return false;
	
	}
	
	if (Form.edtValor.value == 0) 
	{
	
		alert("O Valor do Produto nao pode ser ZERO !");
		return false;
	
	}
	
	return true;
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<form id="form" name="entradas" action="sistema.php?ModuloNome=OrdemCompraProdutoEntrada" method="post" onsubmit="return valida_form()"> 

<input type="hidden" id="OrdemId" name="OrdemId" value="<?php echo $OrdemId ?>" />
<input type="hidden" id="ProdutoId" name="ProdutoId" value="<?php echo $ProdutoId ?>" />
<input type="hidden" id="ItemId" name="ItemId" value="<?php echo $ItemId ?>" />
<input type="hidden" id="FornecedorId" name="FornecedorId" value="<?php echo $dados_produto["fornecedor_id"] ?>" />

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Entrada/Baixa de Produto da Ordem de Compra</span>
					</td>
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
						if($_POST['Submit'])
						{

							//Variaveis
							$edtOrdemId = $_POST['OrdemId'];
							$edtFornecedorId = $_POST['FornecedorId'];
							$edtProdutoId = $_POST['ProdutoId'];
							$edtItemId = $_POST['ItemId'];
							$edtNf = $_POST['edtNf'];
							$edtData = DataMySQLInserir($_POST['edtData']);
							$edtQuantidade = $_POST['edtQuantidade'];
							$edtValor = MoneyMySQLInserir($_POST['edtValor']);
							$edtObs = $_POST['edtObs'];
							
							//echo "Ordem: " . $edtOrdemId . "<br/>";
							//echo "Produto: " . $edtProdutoId . "<br/>";
							//echo "Item: " . $edtItemId . "<br/>";
							//echo "Fornecedor: " . $edtFornecedorId . "<br/>";
							//echo "Data: " . $edtData . "<br/>";
							//echo "Quantidade: " . $edtQuantidade . "<br/>";
							//echo "Valor: " . $edtValor . "<br/>";
							//	echo "Obs: " . $edtObs . "<br/>";
							
							//Monta o sql e executa a query de inserção
							$AtuEntrada = "UPDATE ordem_compra_produto SET entrada = entrada + $edtQuantidade WHERE id = $ItemId";
							
							echo $AtuEntrada;
							
							mysql_query($AtuEntrada);
							
							
							$AtuEstoque = "UPDATE item_evento SET 
											estoque_atual = estoque_atual + $edtQuantidade,
											ent_data = '$edtData',
											ent_oc_id = '$edtOrdemId',
											ent_nf = '$edtNf',
											ent_fornecedor_id = '$edtFornecedorId',
											ent_quantidade = '$edtQuantidade',
											ent_valor_unitario = '$edtValor'
											WHERE id = $ProdutoId";
							
							echo $AtuEstoque;
							
							mysql_query($AtuEstoque);
							
							
							$AtuMovimento = "INSERT INTO item_evento_movimenta (
											item_id,
											data,
											movimento,
											tipo_movimentacao_id,
											fornecedor_id,
											ordem_compra_id,
											quantidade,
											valor,
											obs,
											cadastro_timestamp,
											cadastro_operador_id
											) VALUES (
											'$edtProdutoId',
											'$edtData ',
											'1',
											'998',
											'$edtFornecedorId',
											'$edtOrdemId',
											'$edtQuantidade',
											'$edtValor',
											'$edtObs',
											now(),
											'$usuarioId')";
											
							
							echo $AtuMovimento;
							
							mysql_query($AtuMovimento);
							
	
							//Exibe a mensagem de inclusão com sucesso
							echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Entrada de Produto da OC cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
						}
						
						?>
          
						<table cellSpacing="0" cellPadding="0" width="520" border="0">
							<tr>
								<td style="padding-bottom: 2px">
									<input name="BtnVoltar" type="button" class="button" id="BtnVoltar" title="Retorna para a exibicao da OC" value="Voltar" onclick="wdCarregarFormulario('OrdemCompraExibe.php?OrdemId=<?php echo $OrdemId ?>','conteudo')" style="width: 110px">
									<input name="Submit" type="submit" class="button" id="Submit" title="Processa a entrada do produto atual [Alt+S]" value="Processar Entrada">
									<input class="button" title="Limpa o conteúdo dos campos digitados [Alt+L]" name="Reset" type="reset" id="Reset" value="Limpar Campos">
									<input name="OrdemId" type="hidden" value="<?php echo $OrdemId ?>" />
								</td>
								<td width="36" align="right">&nbsp;</td>
							</tr>
						</table>
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" >
							<tr>
								<td class="listViewPaginationTdS1" style="padding-right: 0px; padding-left: 0px; font-weight: normal; padding-bottom: 0px; padding-top: 0px; border-bottom: 0px" colspan="2">
									<table cellSpacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="text-align: left">
												<img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da entrada do produto da OC e clique em [Processar Entrada] 
											</td>
										</tr>
									</table>            
								</td>
							</tr>
							<tr>
								<td height="24" width="130" valign="middle" class="dataLabel">Produto:</td>
								<td width="620" valign="middle" class="tabDetailViewDF">
									<b><?php echo '(' . $ProdutoId . ') - ' . $dados_produto['produto_nome'] ?></b>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Data da Entrada:</td>
								<td valign="middle" class="tabDetailViewDF">
									<?php
										
										//Adiciona o acesso a entidade de criação do componente data
										include_once("CalendarioPopUp.php");  
										//Cria um objeto do componente data
										$objData = new tipData();
										//Define que não deve exibir a hora no calendario
										$objData->bolExibirHora = false;
										//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
										$objData->MontarJavaScript();

										//Define a data do formulário
										$objData->strFormulario = "entradas";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtData";
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = date("d/m/Y", mktime());
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
								<td valign="top" class="dataLabel">Fornecedor:</td>
								<td valign="middle" class="tabDetailViewDF">
									<b><?php echo '(' . $dados_produto['fornecedor_id'] . ') - ' . $dados_produto['fornecedor_nome'] ?></b>
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">NF:</td>
								<td valign="middle" class="tabDetailViewDF">
									<input name="edtNf" type="text" class="datafield" id="edtNf" style="width: 80px" maxlength="8" value="" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" /> 
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Quantidade:</td>
								<td valign="middle" class="tabDetailViewDF">
									<input name="edtQuantidade" type="text" class="datafield" id="edtQuantidade" style="width: 80px" maxlength="8" value="<?php echo $edtSaldo ?>" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" /> 
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Preço Unitário:</td>
								<td valign="middle" class="tabDetailViewDF">
									<?php
										//Acerta a variável com o valor a alterar
										$valor_alterar = str_replace(".",",",$dados_produto[valor_unitario]);
										
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtValor";
										//Define o tamanho do componente
										$objWDComponente->intSize = 16;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = $valor_alterar;
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
								<td valign="top" class="dataLabel">Observações:</td>
								<td valign="middle" class="tabDetailViewDF">
									<textarea name="edtObs" wrap="virtual" class="datafield" id="edtObs" style="width: 100%; height: 80px"></textarea>
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