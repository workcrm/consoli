<?php 
###########
## Módulo para cadastro do planejamento orcamentario
## Criado: 16/04/2012 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET['headers'] == 1)
{

	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Processa as diretivas de segurança 
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

//Monta o lookup da tabela de grupos
//Monta o SQL
$lista_grupo = "SELECT * FROM grupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_grupo = mysql_query($lista_grupo);

//Monta o lookup da tabela de subgrupos (CONTA_CAIXA) filtrando tipo 2 que é saída (débito)
//Monta o SQL
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '2' ORDER BY nome";
//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);

//Monta o lookup da tabela de regionais
//Monta o SQL
$lista_regiao = "SELECT * FROM regioes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_regiao = mysql_query($lista_regiao);

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
	if (Form.edtNome.value.length == 0) 
	{
		
		alert("É necessário Informar o Nome/Razão Social do Cliente !");
		Form.edtNome.focus();
		return false;
		
	}
	
	return true;
}
</script>

</head>
<body>

<form id="form" name="orcamentario" action="sistema.php?ModuloNome=OrcamentarioCadastra" method="post" onsubmit="return valida_form()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Planejamento Orçamentário</span></td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
				<tr>
					<td>
						<table id="1" style="display: none" width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td valign="midle"><img src="image/bt_ajuda.gif" width="13" height="16" /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">
						<?php
					
							//Recupera os valores vindos do formulário e armazena nas variaveis
							if($_POST["Submit"])
							{
							
								$edtTipo = $_POST["edtTipo"];
								$edtTipo = 2;
								$edtAno = $_POST["edtAno"];
								$edtRegional = $_POST["edtRegional"];
								$edtCentroCusto = $_POST["edtCentroCusto"];
								
								if ($edtTipo == 1) $edtContaCaixa = $_POST["edtContaCaixaEntrada"];
								if ($edtTipo == 2) $edtContaCaixa = $_POST["edtContaCaixaSaida"];
								
								$edtValor = MoneyMySQLInserir($_POST["edtValor"]);
							
								//Monta o sql e executa a query de inserção dos clientes
								$sql = mysql_query("INSERT INTO orcamentario (
													tipo,
													ano, 
													regional, 
													centro_custo,
													conta_caixa, 
													valor

													) VALUES (

													'$edtTipo',
													'$edtAno',
													'$edtRegional',
													'$edtCentroCusto',
													'$edtContaCaixa',
													'$edtValor');");
	
								//Exibe a mensagem de inclusão com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Planejamento Orçamentário cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
							
							}
						
						?>

						<table cellspacing="0" cellpadding="0" width="520" border="0">
							<tr>
								<td style="PADDING-BOTTOM: 2px">
									<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Planejamento" />
									<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
								</td>
							</tr>
						</table>
           
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do cliente e clique em [Salvar Cliente] </td>
										</tr>
									</table>             
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="20%">
									<span class="dataLabel">Ano-base:</span>             
								</td>
								<td colspan="3" class="tabDetailViewDF">
									<select name="edtAno" id="edtAno" style="width:70px">
										<?php 
			              
											//Efetua o for para montar o combo do ano              
											for ($a=10;$a<=20;$a++) 
											{
											
												$monta_ano = "20" . $a;
										
												//Caso o ano for igual ao ano atual
												if ($monta_ano == date("Y", mktime())) 
												{
													
													//Alimenta a variável com o valor selected
													$seleciona_ano = "selected";
												
												//Caso nao for	
												} 
												
												else 
												
												{
													
													//Alimenta a variável com o valor vazio
													$seleciona_ano = "";
												
												}
											
												//Gera o combo do ano	
												echo "<option value='$monta_ano' $seleciona_ano>$monta_ano</option>";
											}
											
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Região:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="edtRegional" id="edtRegional" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_regiao = mysql_fetch_object($dados_regiao)) 
											{

												$seleciona_regiao = '';
												
												if($_POST["Submit"])
												{
												
													$edtRegional = $_POST["edtRegional"];
													
													if ($lookup_regiao->id == $edtRegional) $seleciona_regiao = 'selected'; 
													
												}
										
										?>
										<option value="<?php echo $lookup_regiao->id ?>" <?php echo $seleciona_regiao ?>><?php echo $lookup_regiao->id . " - " . $lookup_regiao->nome ?></option>
										<?php 
											
											} 
											
										?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Centro de Custo:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="edtCentroCusto" id="edtCentroCusto" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_grupo = mysql_fetch_object($dados_grupo)) 
											{
											
												$seleciona_centro_custo = '';
												
												if($_POST["Submit"])
												{
												
													$edtCentroCusto = $_POST["edtCentroCusto"];
													
													if ($lookup_grupo->id == $edtCentroCusto) $seleciona_centro_custo = 'selected'; 
													
												}
										
										?>
										<option value="<?php echo $lookup_grupo->id ?>" <?php echo $seleciona_centro_custo ?>><?php echo $lookup_grupo->id . " - " . $lookup_grupo->nome ?></option>
										<?php 
											
											} 
											
										?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Conta-caixa:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="edtContaCaixaSaida" id="edtContaCaixaSaida" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo)) 
											{

												$seleciona_conta_caixa_saida = '';
												
												if($_POST["Submit"])
												{
												
													$edtContaCaixaSaida = $_POST["edtContaCaixaSaida"];
													
													if ($lookup_subgrupo->id == $edtContaCaixaSaida) $seleciona_conta_caixa_saida = 'selected'; 
													
												}
											
										?>
										<option value="<?php echo $lookup_subgrupo->id ?>" <?php echo $seleciona_conta_caixa_saida ?>><?php echo $lookup_subgrupo->id . " - " . $lookup_subgrupo->nome ?></option>
										<?php 
										
											} 
										
										?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Valor Total: </td>
								<td class="tabDetailViewDF">
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
						</table> 	
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>

