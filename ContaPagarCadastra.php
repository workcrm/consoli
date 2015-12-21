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

<script language="JavaScript">
	//Função que alterna a visibilidade do painel especificado.
	function oculta(id)
	{
		ID = document.getElementById(id);
		ID.style.display = "none";
	}

	function wdExibir() 
	{

		//Captura o valor referente ao radio button selecionado
		var edtTipoPessoaValor = document.getElementsByName("edtTipoPessoa");
   
		for (var i=0; i < edtTipoPessoaValor.length; i++) 
		{
			if (edtTipoPessoaValor[i].checked == true) 
			{
				edtTipoPessoaValor = edtTipoPessoaValor[i].value;
				break;
			}
		}

		if (edtTipoPessoaValor == 1) 
		{
			IDCli = document.getElementById(20);
			IDFor = document.getElementById(30);
			IDCol = document.getElementById(40);
			IDFor.style.display = "none";
			IDCol.style.display = "none";
			IDCli.style.display = "inline";
		}
	
		if (edtTipoPessoaValor == 2) 
		{
			IDCli = document.getElementById(20);
			IDFor = document.getElementById(30);
			IDCol = document.getElementById(40);
			IDFor.style.display = "inline";
			IDCol.style.display = "none";
			IDCli.style.display = "none";		
		}
	
		if (edtTipoPessoaValor == 3) 
		{
			IDCli = document.getElementById(20);
			IDFor = document.getElementById(30);
			IDCol = document.getElementById(40);
			IDFor.style.display = "none";
			IDCol.style.display = "inline";
			IDCli.style.display = "none";		
		}
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
		
		
		if (Form.edtDescricao.value.length == 0) 
		{
      
			alert("É necessário Informar a Descrição !");
			Form.edtDescricao.focus();
			return false;
		
		}
	 
		if (Form.cmbRegiaoId.value == 0) 
		{
		
			alert("É necessário selecionar uma Região !");
			Form.cmbRegiaoId.focus();
			return false;
		}
		
		if (Form.cmbSubgrupoId.value == 0) 
		{
		
			alert("É necessário selecionar uma Conta-caixa !");
			Form.cmbSubgrupoId.focus();
			return false;
		}
		
		
		if (Form.cmbGrupoId.value == 0) 
		{
      
			alert("É necessário selecionar um Centro de Custo para a Conta !");
			Form.cmbGrupoId.focus();
			return false;
		}
		
		//Captura o valor referente ao radio button selecionado
		var edtTipoPessoaValor = document.getElementsByName("edtTipoPessoa");
   
		for (var i=0; i < edtTipoPessoaValor.length; i++) 
		{
			
			if (edtTipoPessoaValor[i].checked == true) 
			{
				edtTipoPessoaValor = edtTipoPessoaValor[i].value;
				break;
			}
		}

		if (edtTipoPessoaValor == 1) 
		{
			
			if (Form.cmbClienteId.value == 0) 
			{
				
				alert("É necessário selecionar um Cliente !");
				Form.cmbClienteId.focus();
				return false;
			}
		}
	
		if (edtTipoPessoaValor == 2) 
		{
			
			if (Form.cmbFornecedorId.value == 0) 
			{
      
				alert("É necessário selecionar um Fornecedor !");
				Form.cmbFornecedorId.focus();
				return false;
			}
		}
	
		if (edtTipoPessoaValor == 3) 
		{
		
			if (Form.cmbColaboradorId.value == 0) 
			{
				
				alert("É necessário selecionar um Colaborador !");
				Form.cmbColaboradorId.focus();
				return false;
			}
		}
	
		if (Form.edtValor.value.length == 0) 
		{
			
			alert("É necessário Informar o Valor !");
			Form.edtValor.focus();
			return false;
		}   
   
		if (Form.edtDataVencimento.value.length == 0) 
		{
      
			alert("É necessário Informar a Data do Vencimento !");
			Form.edtDataVencimento.focus();
			return false;
   
		}
		
		//Verifica se o numero de parcelas informado é válido  
		if (Form.edtParcelas.value.length == 0 || Form.edtParcelas.value == "0") 
		{
			alert("É necessário Informar o número de parcelas desta conta !");
			Form.edtParcelas.value = 1;
			Form.edtParcelas.focus();
			return false;
		}

		//Verifica se o numero de parcelas informado é maior que 1, daí precisa informar o numero de dias
		if (Form.edtParcelas.value > 1 && Form.edtDias.value == "0") 
		{
			alert("É necessário Informar o dia de vencimento das parcelas subsequentes desta conta !");
			Form.edtDias.focus();
			return false;
		} 

		//Verifica se o numero de parcelas informado é maior que 1, e se o numero de dias informado for superior a 31
		if (Form.edtParcelas.value > 1 && Form.edtDias.value > 31) 
		{
			alert("O dia para o vencimento das parcelas não pode ser superior a 31 !");
			Form.edtDias.focus();
			return false;
		} 
		
		return true;
	}

	function busca_formandos()
	{
  
		var Form;
		Form = document.cadastro;   
    
		eventoId = Form.cmbEventoId.value;
     
		wdCarregarFormulario('ContaReceberBuscaFormando.php?EventoId=' + eventoId,'recebe_formandos');
    
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
$lista_colaborador = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId ORDER BY nome";
//Executa a query
$dados_colaborador = mysql_query($lista_colaborador);

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
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Conta a Pagar</span></td>
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
							
							//Recupera os valores vindos do formulário e armazena nas variaveis
							if($_POST["Submit"])
							{

								$edtEmpresaId = $empresaId;
								$edtData = DataMySQLInserir($_POST["edtData"]);
								$edtTipoPessoa = $_POST["edtTipoPessoa"];

								if ($edtTipoPessoa == 1) 
								{
									$cmbPessoaId = 	$_POST["cmbClienteId"];
								}

								if ($edtTipoPessoa == 2) 
								{
									$cmbPessoaId = 	$_POST["cmbFornecedorId"];
								}

								if ($edtTipoPessoa == 3) 
								{
									$cmbPessoaId = 	$_POST["cmbColaboradorId"];
								}
													
								$cmbRegiaoId = $_POST["cmbRegiaoId"];
								$cmbGrupoId = $_POST["cmbGrupoId"];
								$cmbSubgrupoId = $_POST["cmbSubgrupoId"];
								$cmbCategoriaId = $_POST["cmbCategoriaId"];
								$edtDescricao = $_POST["edtDescricao"];
								$edtNroDocumento = $_POST["edtNroDocumento"];
								$edtValor = MoneyMySQLInserir($_POST["edtValor"]);	          
								
								$edtDataVencimento = DataMySQLInserir($_POST["edtDataVencimento"]);
								$edtDataVencimentoCalcula = $_POST["edtDataVencimento"];
								
								$cmbEventoId = $_POST["cmbEventoId"];
								$cmbFormandoId = $_POST["cmbFormandoId"];
								
								$edtObservacoes = $_POST["edtObservacoes"];
								$edtOperadorId = $usuarioId;

								//Captura o numero de desmembramento da parcela
								$Parcelas = $_POST["edtParcelas"];
								
								//Captura o numero de dias entre os vencimentos
								$Dias_original = $_POST["edtDias"];
								$Dias = $_POST["edtDias"];
											
								//Pega o mês da data original do vencimento
								$mes_original = substr($edtDataVencimento, 5, 2);
								$ano_original = substr($edtDataVencimento, 0, 4);
								
								$edtMostraVencimento .= $edtDataVencimentoCalcula . "<br/>";
								
								//Cria o for para lanças quantas contas forem necessárias
								for($i = 01; $i <= $Parcelas; $i ++ )
								{                                                        
															  
									//Cria a variável que mostrará a parte das parcelas
									$edtMostraParcelas .= "Parcela <strong>" . $i . "</strong> de <strong>" . $Parcelas . "</strong><br/>";
								  
									$edtMostraValor .= "R$: " . number_format($edtValor, 2, ",", ".") . "<br/>";
									
									//Cria a nova data de vencimento
									//Somente após a primeira parcela
								  
									if ($i > 1)
									{
								
									  
										$mes_original = ++$mes_original;
									
										//Verifica se o mês não é inferior a 10
										if ($mes_original < 10)
										{
									  
											$mes_original = "0" . $mes_original;
									  
										}
									
										//Verifica se o mês não é superior a 12
										if ($mes_original > 12)
										{
									  
											$mes_original = "01";
											$ano_original = ++$ano_original;
									  
										}
																	 
										//Verifica se é um mês com 30 dias
										if (($mes_original == 4) || ($mes_original == 6) || ($mes_original == 9) || ($mes_original == 11))
										{
									
											//Verifica se o dia do vencimento informado for = a 31
											if ($Dias == 31)
											{
									  
												$Dias = "30";
									
											}                  
														
										}
										
										else
										
										{
											
											$Dias = $Dias_original;               
										}
									
										//Verifica se é um mês de fevereiro
										if ($mes_original == 2)
										{
									
											//Verifica se o dia do vencimento informado for > que 28
											if ($Dias_original > 28)
											{
									  
												$Dias = "28";
									
											} 
											
											else
											
											{
											
												$Dias = $Dias_original;               
											}                 
														
										}
									
									
										$data_calculada = $ano_original . "-" . $mes_original . "-" . $Dias;
									
										$edtMostraVencimento .= DataMySQLRetornar($data_calculada) . "<br/>";                
             
										$edtDataVencimentoCalculaFormata = $data_calculada;
									
									} 
									
									else
									
									{
									
										$edtDataVencimentoCalcula = $edtDataVencimentoCalcula;
										$edtDataVencimentoCalculaFormata = DataMySQLInserir($edtDataVencimentoCalcula);
									
									}								
							
									//Monta o sql e executa a query de inserção da conta sem desmembrar
									$sql = mysql_query("INSERT INTO contas_pagar (
														empresa_id, 
														data,
														tipo_pessoa,
														regiao_id,
														pessoa_id,
														grupo_conta_id,
														subgrupo_conta_id,
														evento_id, 
														formando_id,													
														descricao,
														origem_conta,
														nro_documento,
														valor,
														data_vencimento,
														situacao,
														observacoes,
														cadastro_timestamp,
														cadastro_operador_id
										
														) VALUES (
										
														'$edtEmpresaId',
														'$edtData',
														'$edtTipoPessoa',
														'$cmbRegiaoId',
														'$cmbPessoaId',
														'$cmbGrupoId',
														'$cmbSubgrupoId',
														'$cmbEventoId',
														'$cmbFormandoId',
														'$edtDescricao',
														1,
														'$edtNroDocumento',
														'$edtValor',
														'$edtDataVencimentoCalculaFormata',
														1,										
														'$edtObservacoes',
														now(),
														'$edtOperadorId'				
														);"); 
									
								
								}
								
								//Exibe a mensagem de inclusão com sucesso
								//echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Conta a Pagar cadastrada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
								
								?>
								
								<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
									<tr>
										<td height="22" width="20" valign="top" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 3px; border-right: 0px">
											<img src="./image/bt_informacao.gif" border="0" />
										</td>
										<td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px; padding-top: 3px; padding-bottom: 4px">
											<strong>Conta a Pagar cadastrada com sucesso !</strong>
											<br/>
											<table width="500" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
												<tr>
													<td colspan="4" style="padding-top: 5px;">
														<span style="color: #990000"><strong>Desdobramento das Parcelas:</strong></span>
													</td>
												</tr>
												<tr>
													<td width="120">
														<strong>Número da Parcela</strong>
													</td>
													<td width="90">
														<strong>Valor</strong>
													</td>
													<td>
														<strong>Vencimento</strong>
													</td>
												</tr>
												<tr>
													<td>
														<?php echo $edtMostraParcelas ?>
													</td>
													<td>
														<strong><?php echo $edtMostraValor ?></strong>
													</td>
													<td>
														<span style="color: #990000">
															<strong>
																<?php echo $edtMostraVencimento ?>
															</strong>
														</span>
													</td>
												</tr>
											</table>                    
										</td>
									</tr>
									<tr>
										<td colspan="2">
											&nbsp;
										</td>
									</tr>
								</table>
								</td></tr><tr><td>
								
								<?php
								
							//Fecha o if de postagem
							}
						
						?>

						<table cellspacing="0" cellpadding="0" width="520" border="0">
							<tr>
								<td width="484">
									<form id="form" name="cadastro" action="sistema.php?ModuloNome=ContaPagarCadastra" method="post" onsubmit="return valida_form()">
								</td>
							</tr>
							<tr>
								<td style="PADDING-BOTTOM: 2px">
									<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Conta a Pagar" />
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
												<img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da conta a pagar e clique em [Salvar Conta a Pagar]									 
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
								<td width="140" class="dataLabel">Descrição:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<input name="edtDescricao" type="text" class="requerido" id="edtDescricao" style="width: 550px; color: #6666CC; font-weight: bold" maxlength="80" />             
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Região:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="cmbRegiaoId" id="cmbRegiaoId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
										<?php 
											
											//Monta o while para gerar o combo de escolha
											while ($lookup_regiao = mysql_fetch_object($dados_regiao)) 
											{ 
										
										?>
										<option value="<?php echo $lookup_regiao->id ?>"><?php echo $lookup_regiao->id . " - " . $lookup_regiao->nome ?></option>
										<?php 
											
											} 
											
										?>
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
								<td width="140" valign="top" class="dataLabel">Tipo de Pessoa:<br/><br/>Sacado:</td>
								<td colspan="4" class="tabDetailViewDF">
									<table cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="117" height="20">
												<input type="radio" name="edtTipoPessoa" value="1" onclick="wdExibir()" /> Cliente
											</td>
											<td width="120" height="20">
												<input type="radio" name="edtTipoPessoa" value="2" checked="checked" onclick="wdExibir()" /> Fornecedor
											</td>
											<td width="120" height="20">
												<input type="radio" name="edtTipoPessoa" value="3" onclick="wdExibir()" /> Colaborador
											</td>
										</tr>
									</table>
               
									<table id="20" cellpadding="0" cellspacing="0" style="display: none">
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
									</table>

									<table id="30" cellpadding="0" cellspacing="0">
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
									</table>

									<table id="40" cellpadding="0" cellspacing="0" style="display: none">
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
										</tr>
									</table>              
								</td>
							</tr>

							<tr>
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
			
							<tr>
								<td class="dataLabel" width="50">Formando:</td>
								<td colspan="4" width="490" class="tabDetailViewDF">
									<div id="recebe_formandos">
										[ Selecione um evento ] <input type="hidden" name="cmbFormandoId" id="cmbFormandoId" value="0">
									</div>
								</td>
							</tr>

							<tr>
								<td width="140" class="dataLabel">Nº do Documento:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<input name="edtNroDocumento" type="text" class="datafield" id="edtNroDocumento" style="width: 140px" maxlength="20" />             
								</td>
							</tr>
          

							<tr>
								<td width="140" valign="top" class="dataLabel">Valor Total: </td>
								<td width="173" class="tabDetailViewDF">
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
								<td width="110" class="dataLabel">Data Vencimento:</td>
								<td colspan="2" class="tabDetailViewDF">
									<?php
										//Define a data do formulário
										$objData->strFormulario = "cadastro";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataVencimento";
										$objData->strRequerido = true;
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
								<td width="140" valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
								<td colspan="4" class="tabDetailViewDF">
									<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>    							
								</td>
							</tr>							
						</table>
					</td>
				</tr>
				<tr>
					<td class="text" valign="top">
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #FFFFCD; border: #aaa solid 1px; padding: 5px;">
							<tr>
								<td style="padding-left: 5px; padding-bottom: 5px"><span class="TituloModulo">Duplicar Conta</span></td>
							</tr>
							<tr>
								<td style="padding-left: 5px; padding-bottom: 5px">
									<b>Caso desejar duplicar esta conta, informe o número de vezes:</b>
									<br/>
									Duplicar esta conta&nbsp;&nbsp;&nbsp;<input name="edtParcelas" type="text" class="requerido" id="edtParcelas" style="width: 20px" maxlength="2" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="1" />&nbsp;vez(es), com vencimentos para o dia&nbsp;&nbsp;<input name="edtDias" type="text" class="requerido" id="edtDias" style="width: 20px" maxlength="2" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="0" />&nbsp;&nbsp;de cada mês subsequente. 
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>  	 
		</tr>
</table>
</form>