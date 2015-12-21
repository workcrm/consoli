<?php 
###########
## Módulo para alteração de conta a pagar
## Criado: 17/05/2007 - Maycon Edinger
## Alterado: 11/07/2007 - Maycon Edinger
## Alterações:
## 06/06/2007 - Implementado todos os novos campos solicitados
## 18/06/2007 - Implementado objeto para controle monetário
## 03/07/2007 - Implementado campo para condição de pagamento
## 05/07/2007 - Implementado para incluir o cheque na conta
## 11/07/2007 - Implementado campo para cadastro de subgrupos e nro do documento
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

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//Monta o lookup aa tabela de categorias
//Monta o SQL
$lista_categoria = "SELECT * FROM categoria_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '1' ORDER BY nome";

//Executa a query
$dados_categoria = mysql_query($lista_categoria);

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

//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_evento = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";
//Executa a query
$dados_evento = mysql_query($lista_evento);

//Monta o lookup da tabela de subgrupos
//Monta o SQL
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);

//Monta o lookup da tabela de regionais
//Monta o SQL
$lista_regiao = "SELECT * FROM regioes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_regiao = mysql_query($lista_regiao);
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

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
		var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
   
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

	function wdSubmitContaAltera() 
	{
		
		var Form;
		Form = document.frmContaAltera;
   
		if (Form.edtData.value.length == 0) 
		{
			
			alert("É necessário Informar a Data !");
			Form.edtData.focus();
			return false;
		}
   
		//Captura o valor referente ao radio button selecionado
		var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
   
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
	 
		if (Form.cmbGrupoId.value == 0) 
		{
			
			alert("É necessário selecionar um Centro de Custo da Conta !");
			Form.cmbGrupoId.focus();
			return false;
		
		}
		
		if (Form.cmbSubgrupoId.value == 0) 
		{
			
			alert("É necessário selecionar uma Conta-caixa !");
			Form.cmbSubgrupoId.focus();
			return false;
		}   
   
		if (Form.edtDescricao.value.length == 0) 
		{
			
			alert("É necessário Informar a Descrição !");
			Form.edtDescricao.focus();
			return false;
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
   
		return true;
	}
</script>

<form name='frmContaAltera' action='sistema.php?ModuloNome=ContaPagarAltera' method='post' onSubmit='return wdSubmitContaAltera()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Conta a Pagar</span></td>
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
						
							//Verifica se a flag está vindo de uma postagem para liberar a alteração
							if($_POST['Submit'])
							{

								//Recupera os valores do formulario e alimenta as variáveis
								$id = $_POST["Id"];
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
								$cmbCondicaoId = $_POST["cmbCondicaoId"];
								$edtValor = MoneyMySQLInserir($_POST["edtValor"]);
								$edtDataVencimento = DataMySQLInserir($_POST["edtDataVencimento"]);	          	          
								$edtObservacoes = $_POST["edtObservacoes"];	
								$edtOperadorId = $usuarioId;

								//Executa a query de alteração da conta
								$sql = mysql_query("UPDATE contas_pagar SET 
													data = '$edtData',
													tipo_pessoa = '$edtTipoPessoa',
													regiao_id = '$cmbRegiaoId',
													pessoa_id = '$cmbPessoaId', 
													grupo_conta_id = '$cmbGrupoId',
													subgrupo_conta_id = '$cmbSubgrupoId', 
													categoria_id = '$cmbCategoriaId', 
													descricao = '$edtDescricao',
													nro_documento = '$edtNroDocumento',
													condicao_pgto_id = '$cmbCondicaoId', 
													valor = '$edtValor', 
													data_vencimento = '$edtDataVencimento', 																
													observacoes = '$edtObservacoes',
													alteracao_timestamp = now(),
													alteracao_operador_id = '$edtOperadorId'
													WHERE id = '$id' ");			 

								//Exibe a mensagem de alteração com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Conta a Pagar alterada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500);</script>";
							}

							//RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
							//Captura o id da cleinte a alterar
							if ($_GET["Id"]) 
							{
								
								$ContaId = $_GET["Id"];
							} 
							
							else 
							
							{
								
								$ContaId = $_POST["Id"];
							
							}
				
				//Monta o sql para busca da conta
				$sql = "SELECT * FROM contas_pagar WHERE id = $ContaId";

				//Executa a query
				$resultado = mysql_query($sql);

				//Monta o array dos dados
				$campos = mysql_fetch_array($resultado);

				//Efetua o switch para o campo de tipo de pessoa
				switch ($campos[tipo_pessoa]) {
					case 01: 
						$pess_1 = "checked";	
						$pess_2 = ""; 
						$pess_3 = "";		  
						$tbcli = "inline";
						$tbfor = "none";
						$tbcol = "none";
					break;
					case 02: 
						$pess_1 = "";		
						$pess_2 = "checked";	
						$pess_3 = "";  
						$tbcli = "none";
						$tbfor = "inline";
						$tbcol = "none";						
						break;
					case 03: 
						$pess_1 = "";		
						$pess_2 = "";	
						$pess_3 = "checked";  
						$tbcli = "none";
						$tbfor = "none";
						$tbcol = "inline";
					break;
				}
								
				//Efetua o switch para o campo de situacao
				switch ($campos[situacao]) 
				{
					case 1: 
						$sit_1 = "checked";	
						$sit_2 = ""; 		  
					break;
					case 2: 
						$sit_1 = "";		
						$sit_2 = "checked";  
					break;
				}	
				
				//Efetua o switch para o campo de tipo de pagamento
				switch ($campos[tipo_pagamento]) 
				{
					case 1: 
						$pag_1 = "checked";	
						$pag_2 = ""; 		  
					break;
					case 2: 
						$pag_1 = "";		
						$pag_2 = "checked";  
					break;
				}	           					
			?>

			<table cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td width="100%"> </td>
				</tr>
				<tr>
					<td style="PADDING-BOTTOM: 2px">
						<input name="Id" type="hidden" value="<?php echo $ContaId ?>" />
						<input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Conta a Pagar">
						<input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações">
					</td>
					<td width="36" align="right">
						<input class="button" title="Retorna a exibição do registro" name="btnVoltar" type="button" id="btnVoltar" value="Retornar a Conta a Pagar" onclick="wdCarregarFormulario('ContaPagarExibe.php?ContaId=<?php echo $ContaId ?>','conteudo')" />						
					</td>
				</tr>
			</table>
           
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colSpan="20">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da Conta a Pagar e clique em [Salvar Conta a Pagar] <br />
									<br />
									<span class="style1">Aten&ccedil;&atilde;o:</span> Esta transa&ccedil;&atilde;o ser&aacute; monitorada pelo sistema e ser&aacute; gerado um log da atividade para fins de auditoria.
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
					<td width="140" class="dataLabel">Descrição:</td>
					<td colspan="4" valign="middle" class="tabDetailViewDF">
						<input name="edtDescricao" type="text" class="requerido" id="edtDescricao" style="width: 400px" size="84" maxlength="80" value="<?php echo $campos[descricao] ?>">
					</td>
				</tr>         
				<tr>
					<td width="140" valign="top" class="dataLabel">Tipo Pessoa:</td>
					<td colspan="4" class="tabDetailViewDF">
						<table cellpadding="0" cellspacing="0">
							<tr valign="middle">
								<td width="117" height="20">
									<input type="radio" name="edtTipoPessoa" value="1" <?php echo $pess_1 ?> onclick="wdExibir()">
									Cliente
								</td>
								<td width="120" height="20">
									<input type="radio" name="edtTipoPessoa" value="2" <?php echo $pess_2 ?> onclick="wdExibir()">
									Fornecedor
								</td>
								<td width="120" height="20">
									<input type="radio" name="edtTipoPessoa" value="3" <?php echo $pess_3 ?> onclick="wdExibir()">
									Colaborador
								</td>
							</tr>
						</table>
              
              
						<table id="20" cellpadding="0" cellspacing="0" style="display: <?php echo $tbcli ?>">
							<tr valign="middle">
								<td style="padding-top: 7px">
									Cliente:</br>
									<select name="cmbClienteId" id="cmbClienteId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_cliente = mysql_fetch_object($dados_cliente)) { 
										 ?>
										<option <?php if ($campos[tipo_pessoa] == 1) {
										 	if ($lookup_cliente->id == $campos[pessoa_id]) {
												echo " selected ";
											} 
											}
											?>
											value="<?php echo $lookup_cliente->id ?>"><?php echo $lookup_cliente->nome ?> </option>
											<?php } ?>
									</select>
								</td>
							</tr>
						</table>

						<table id="30" cellpadding="0" cellspacing="0" style="display: <?php echo $tbfor ?>">
							<tr valign="middle">
								<td style="padding-top: 7px">
									Fornecedor:<br/>                  
									<select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) { 
										 ?>
										<option <?php if ($campos[tipo_pessoa] == 2) {
										 	if ($lookup_fornecedor->id == $campos[pessoa_id]) 
											{
											echo " selected ";
											} 
											}
											?>
											value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . " - " . $lookup_fornecedor->nome ?> </option>
											<?php } ?>
									</select>
								</td>
							</tr>
						</table>

						<table id="40" cellpadding="0" cellspacing="0" style="display: <?php echo $tbcol ?>">
							<tr valign="middle">
								<td style="padding-top: 7px">
									Colaborador:<br/>                  
									<select name="cmbColaboradorId" id="cmbColaboradorId" style="width:350px">
										<option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_colaborador = mysql_fetch_object($dados_colaborador)) { 
										 ?>
										<option <?php if ($campos[tipo_pessoa] == 3) {
										 	if ($lookup_colaborador->id == $campos[pessoa_id]) 
											{
												echo " selected ";
											} 
											}
											?>
											value="<?php echo $lookup_colaborador->id ?>"><?php echo $lookup_colaborador->id . " - " . $lookup_colaborador->nome ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
						</table>              
				  	</td>
				</tr>
				<tr>
					<td width="140" class="dataLabel">Região:</td>
					<td colspan="4" valign="middle" class="tabDetailViewDF">
						<select name="cmbRegiaoId" id="cmbRegiaoId" style="width:350px">
							<?php 
							
								while ($lookup_regiao = mysql_fetch_object($dados_regiao)) 
								{ 
							
							?>
							<option <?php if ($lookup_regiao->id == $campos[regiao_id]) {echo " selected ";} ?>
							value="<?php echo $lookup_regiao->id ?>"><?php echo $lookup_regiao->id . " - " . $lookup_regiao->nome ?></option>
							<?php } ?>
						</select>								 						 
					</td>
				</tr>
				<tr>
					<td width="140" class="dataLabel">Conta-caixa:</td>
					<td colspan="4" valign="middle" class="tabDetailViewDF">
						<select name="cmbSubgrupoId" id="cmbSubgrupoId" style="width:350px">
							<?php while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo)) { ?>
							<option <?php if ($lookup_subgrupo->id == $campos[subgrupo_conta_id]) {
							echo " selected ";
							} ?>
							value="<?php echo $lookup_subgrupo->id ?>"><?php echo $lookup_subgrupo->id . " - " . $lookup_subgrupo->nome ?></option>
							<?php } ?>
						</select>								 						 
					</td>
				</tr>
				<tr>
					<td width="140" class="dataLabel">Centro de Custo:</td>
					<td colspan="4" valign="middle" class="tabDetailViewDF">
						<select name="cmbGrupoId" id="cmbGrupoId" style="width:350px">
							<?php while ($lookup_grupo = mysql_fetch_object($dados_grupo)) { ?>
							<option <?php if ($lookup_grupo->id == $campos[grupo_conta_id]) {
							echo " selected ";
							} ?>
							value="<?php echo $lookup_grupo->id ?>"><?php echo $lookup_grupo->id . " - " . $lookup_grupo->nome ?></option>
							<?php } ?>
						</select>								 						 
					</td>
				</tr>
				<tr>
					<td width="140" class="dataLabel">Evento:</td>
					<td colspan="4" valign="middle" class="tabDetailViewDF">
						<select name="cmbEventoId" id="cmbEventoId" style="width:350px">
							<option value="0">Selecione uma Opção</option>	
							<?php while ($lookup_evento = mysql_fetch_object($dados_evento)) { ?>
							<option <?php if ($lookup_evento->id == $campos[evento_id]) {
							echo " selected ";
							} ?>
							value="<?php echo $lookup_evento->id ?>"><?php echo $lookup_evento->id . " - " . $lookup_evento->nome ?></option>
							<?php } ?>
						</select>								 						 
					</td>
				</tr>
				<tr>
					<td width="140" class="dataLabel">Nº do Documento:</td>
					<td colspan="4" valign="middle" class="tabDetailViewDF">
						<input name="edtNroDocumento" type="text" class="datafield" id="edtNroDocumento" style="width: 140px" maxlength="20" value="<?php echo $campos[nro_documento] ?>">             
					</td>
				</tr>          
				<tr>
					<td width="140" valign="top" class="dataLabel">Valor: </td>
					<td width="173" class="tabDetailViewDF">
						<?php
							//Acerta a variável com o valor a alterar
							$valor_alterar = str_replace(".",",",$campos[valor]);
							
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
						?>												
					</td>
					<td width="146" class="dataLabel">Data Vencimento:</td>
					<td colspan="2" class="tabDetailViewDF">
						<?php
							
							if ($campos["data_vencimento"] != "0000-00-00")
							{
							
								$ValorDataVencimento = DataMySQLRetornar($campos["data_vencimento"]);
								
							}
							
							else
							
							{
							
								$ValorDataVencimento = '';
								
							}
							
							//Define a data do formulário
							$objData->strFormulario = "frmContaAltera";  
							//Nome do campo que deve ser criado
							$objData->strNome = "edtDataVencimento";
							$objData->strRequerido = true;
							//Valor a constar dentro do campo (p/ alteração)
							$objData->strValor = $ValorDataVencimento;
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
						<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"><?php echo $campos[observacoes] ?></textarea>    							
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>  	 
</form>
</td>
</tr>
</table>
