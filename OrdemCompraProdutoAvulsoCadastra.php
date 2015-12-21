<?php 
###########
## Módulo para cadastro de produtos avulsos da ordem de compra
## Criado: 23/03/2012 - Maycon Edinger
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

//Recupera o id da OC
if($_POST) 
{
	
	$OrdemId = $_POST["OrdemId"]; 

} 

else 

{
  
	$OrdemId = $_GET["OrdemId"]; 

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
$lista_produtos = "SELECT id, nome FROM item_evento WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_produtos = mysql_query($lista_produtos);

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
	
	if (Form.cmbLocalId.value == 0) 
	{
	
		alert("É necessário selecionar um tipo de local para o Evento !");
		Form.cmbLocalId.focus();
		return false;
	
	}
	
	if (Form.cmbFornecedorId.value == 0) 
	{
		
		alert("É necessário selecionar um fornecedor para o Evento !");
		Form.cmbFornecedorId.focus();
		return false;
		
	}
	
	return true;
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<form id="form" name="produtos" action="sistema.php?ModuloNome=OrdemCompraProdutoAvulsoCadastra" method="post" onsubmit=""> 

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Produto da Ordem de Compra</span>
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
							$numero_oc = $_POST['OrdemId'];

							$cmbProdutoId = $_POST['cmbProdutoId'];

							$edtProdutoQuantidade = $_POST['edtProdutoQuantidade'];

							$edtProdutoValor = MoneyMySQLInserir($_POST['edtProdutoValor']);
							
							//Monta o sql e executa a query de inserção
							$sql = mysql_query("INSERT INTO ordem_compra_produto (
												ordem_compra_id,
												produto_id, 
												quantidade, 
												valor_unitario
								
												) VALUES (
								
												'$numero_oc',
												'$cmbProdutoId',
												'$edtProdutoQuantidade',
												'$edtProdutoValor'		
												);");
	
							//Exibe a mensagem de inclusão com sucesso
							echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Produto da OC cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
						}
						
						?>
          
						<table cellSpacing="0" cellPadding="0" width="520" border="0">
							<tr>
								<td style="padding-bottom: 2px">
									<input name="BtnVoltar" type="button" class="button" id="BtnVoltar" title="Retorna para a exibicao da OC" value="Voltar" onclick="wdCarregarFormulario('OrdemCompraExibe.php?OrdemId=<?php echo $OrdemId ?>','conteudo')" style="width: 110px">
									<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual [Alt+S]" value="Salvar Produto">
									<input class="button" title="Limpa o conteúdo dos campos digitados [Alt+L]" name="Reset" type="reset" id="Reset" value="Limpar Campos">
									<input name="OrdemId" type="hidden" value="<?php echo $OrdemId ?>" />
								</td>
								<td width="36" align="right">&nbsp;</td>
							</tr>
						</table>
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" >
							<tr>
								<td class="listViewPaginationTdS1" style="padding-right: 0px; padding-left: 0px; font-weight: normal; padding-bottom: 0px; padding-top: 0px; border-bottom: 0px" colspan="20">
									<table cellSpacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="text-align: left">
												<img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do produto da OC e clique em [Salvar Produto] 
											</td>
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
					</td>
				</tr>
			</table>  	 
		</td>
	</tr>
</table>
</form>