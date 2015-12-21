<?php 
###########
## Módulo para alteração de locacao
## Criado: 29/08/2007 - Maycon Edinger
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

//pesquisa as diretivas do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";													  													  
							  
//Executa a query
$resultado_usuario = mysql_query($sql_usuario);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_usuario);

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

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
	 
	if (Form.edtDescricao.value.length == 0) 
	{
      
		alert("É necessário Informar a Descrição !");
		Form.edtDescricao.focus();
		return false;
	}

	if (Form.edtDataDevolucaoPrevista.value.length == 0) 
	{
      
		alert("É necessário Informar a Data de Devolução Prevista !");
		Form.edtDataDevolucaoPrevista.focus();
		return false;
	
	}
   
	 return true;
}
</script>

<form name="frmContaAltera" action="sistema.php?ModuloNome=LocacaoAltera" method="post" onsubmit="return wdSubmitContaAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Locação</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12" />
					</td>
				</tr>
			</table>
			<table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">
						<?php
						
							//Verifica se a flag está vindo de uma postagem para liberar a alteração
							if($_POST["Submit"])
							{

								//Recupera os valores do formulario e alimenta as variáveis
								$id = $_POST["Id"];
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
											          
								$edtDescricao = $_POST["edtDescricao"];
								$edtDataDevolucaoPrevista = DataMySQLInserir($_POST["edtDataDevolucaoPrevista"]);
								$edtSituacao = $_POST["edtSituacao"];
								$edtDataDevolucaoRealizada = DataMySQLInserir($_POST["edtDataDevolucaoRealizada"]);
								$edtObservacoes = $_POST["edtObservacoes"];	
								$edtOperadorId = $usuarioId;
								$edtRecebidoPor = $_POST["edtRecebidoPor"];    
								$edtObservacoesFinanceiro = $_POST["edtObservacoesFinanceiro"];
								$edtNotaFiscal = $_POST["edtNotaFiscal"];
								$edtPosicaoFinanceira = $_POST["edtPosicaoFinanceira"];

								//Executa a query de alteração da conta
								$sql = mysql_query("UPDATE locacao SET 
													tipo_pessoa = '$edtTipoPessoa',
													pessoa_id = '$cmbPessoaId',  
													descricao = '$edtDescricao',
													devolucao_prevista = '$edtDataDevolucaoPrevista', 
													situacao = '$edtSituacao', 
													devolucao_realizada = '$edtDataDevolucaoRealizada', 
													recebido_por = '$edtRecebidoPor',
													observacoes = '$edtObservacoes',
													alteracao_timestamp = now(),
													alteracao_operador_id = '$edtOperadorId',
													obs_financeira = '$edtObservacoesFinanceiro',
													numero_nf = '$edtNotaFiscal',
													posicao_financeira = '$edtPosicaoFinanceira'
													WHERE id = $id ");			 

								//Exibe a mensagem de alteração com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Locação alterada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
							}

							//RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
							//Captura o id da cleinte a alterar
							if ($_GET["Id"]) 
							{
							
								$LocacaoId = $_GET["Id"];
							
							} 
							
							else 
							
							{
				  
								$LocacaoId = $_POST["Id"];
							
							}
				
							//Monta o sql para busca da conta
							$sql = "SELECT * FROM locacao WHERE id = $LocacaoId";

							//Executa a query
							$resultado = mysql_query($sql);

							//Monta o array dos dados
							$campos = mysql_fetch_array($resultado);

							//Efetua o switch para o campo de tipo de pessoa
							switch ($campos[tipo_pessoa]) 
							{
								
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
									
									case 01: 
										$sit_1 = "checked";	
										$sit_2 = ""; 		  
									break;
									case 02: 
										$sit_1 = "";		
										$sit_2 = "checked";  
									break;
								
								}	
        
								//Efetua o switch para o campo de posicao financeira
								switch ($campos[posicao_financeira]) 
								{
									
									case 1: 
										$financeiro_1 = "checked"; 		  
										$financeiro_2 = ""; 		  
										$financeiro_3 = ""; 		  
									break;
									case 2: 
										$financeiro_1 = ""; 		  
										$financeiro_2 = "checked"; 		  
										$financeiro_3 = ""; 		  
									break;
									case 3: 	
										$financeiro_1 = ""; 		  
										$financeiro_2 = ""; 		  
										$financeiro_3 = "checked"; 		  
									break;
				
								}		       				          					
							
							?>

		<table cellspacing="0" cellpadding="0" width="100%" border="0">          
			<tr>
	        	<td style="PADDING-BOTTOM: 2px">
	        		<input name="Id" type="hidden" value="<?php echo $LocacaoId ?>" />
					<input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Locação" />
					<input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações" />
				</td>
				<td width="36" align="right">
					<input class="button" title="Retorna a exibição do registro" name="btnVoltar" type="button" id="btnVoltar" value="Retornar a Locação" onclick="wdCarregarFormulario('LocacaoExibe.php?LocacaoId=<?php echo $LocacaoId ?>&headers=1','conteudo')" />						
				</td>
	       	</tr>
        </table>
           
        <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
			<tr>
				<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="2">
					<table cellspacing="0" cellpadding="0" width="100%" border="0">
						<tr>
							<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da Locação e clique em [Salvar Locação] <br />
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
				<td class="tabDetailViewDF">
					<?php echo DataMySQLRetornar($campos[data]) ?>
				</td>
			</tr>
			<tr>
				<td valign="top" class="dataLabel">Tipo Pessoa:</td>
				<td class="tabDetailViewDF">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr valign="middle">
							<td width="117" height="20">
								<input type="radio" name="edtTipoPessoa" value="1" <?php echo $pess_1 ?> onclick="wdExibir()" />
								Cliente
							</td>
							<td width="120" height="20">
								<input type="radio" name="edtTipoPessoa" value="2" <?php echo $pess_2 ?> onclick="wdExibir()" />
								Fornecedor
							</td>
							<td width="120" height="20">
								<input type="radio" name="edtTipoPessoa" value="3" <?php echo $pess_3 ?> onclick="wdExibir()" />
								Colaborador
							</td>
						</tr>
					</table>
              
					<table id="20" cellpadding="0" width="100%" cellspacing="0" style="display: <?php echo $tbcli ?>" >
						<tr valign="middle">
							<td style="padding-top: 7px">
								Cliente:<br/>
								<select name="cmbClienteId" id="cmbClienteId" style="width:350px">
								<option value="0">Selecione uma Opção</option>
								<?php 
									
									//Monta o while para gerar o combo de escolha
									while ($lookup_cliente = mysql_fetch_object($dados_cliente)) 
									{ 
								
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
										 		if ($lookup_fornecedor->id == $campos[pessoa_id]) {
                        echo " selected ";
                      } 
											}
											?>
											value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->nome ?> </option>
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
									<option <?php  if ($campos[tipo_pessoa] == 3) 
										{
										 	
											if ($lookup_colaborador->id == $campos[pessoa_id]) 
											{
												echo " selected ";
											} 
										}
										
									?>
									value="<?php echo $lookup_colaborador->id ?>"><?php echo $lookup_colaborador->nome ?> </option>
									<?php } ?>
									</select>
								</td>
							</tr>
						</table>              
				  	</td>
				</tr>
				<tr>
					<td class="dataLabel">Descrição:</td>
					<td valign="middle" class="tabDetailViewDF">
						<input name="edtDescricao" type="text" class="requerido" id="edtDescricao" style="width: 400px" size="84" maxlength="80" value="<?php echo $campos[descricao] ?>" />
					</td>
				</tr>   
				<tr>
					<td class="dataLabel">Devolução Prevista:</td>
					<td valign="middle" class="tabDetailViewDF">
						<?php
							//Define a data do formulário
							$objData->strFormulario = "frmContaAltera";  
							//Nome do campo que deve ser criado
							$objData->strNome = "edtDataDevolucaoPrevista";
							$objData->strRequerido = true;
							//Valor a constar dentro do campo (p/ alteração)
							$objData->strValor = DataMySQLRetornar($campos[devolucao_prevista]);
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
					<td valign="top" class="dataLabel">Situação:</td>
					<td class="tabDetailViewDF">
						<table width="197" cellpadding="0" cellspacing="0">
							<tr valign="middle">
								<td width="117" height="20">
									<input name="edtSituacao" type="radio" value="1" <?php echo $sit_1 ?>>
									Pendente
								</td>
								<td width="78" height="20">
									<input type="radio" name="edtSituacao" value="2" <?php echo $sit_2 ?>>
									Recebida
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Data da Devolução:</td>
					<td class="tabDetailViewDF">
						<?php
							//Define a data do formul&aacute;rio
							$objData->strFormulario = "frmContaAltera";  
							//Nome do campo que deve ser criado
							$objData->strNome = "edtDataDevolucaoRealizada";
							$objData->strRequerido = false;
							//Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
							$objData->strValor = DataMySQLRetornar($campos[devolucao_realizada]);
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
					<td class="dataLabel">Recebido Por:</td>
					<td valign="middle" class="tabDetailViewDF">
						<input name="edtRecebidoPor" type="text" class="datafield" id="edtRecebidoPor" size="74" maxlength="70" value="<?php echo $campos[recebido_por] ?>" />             
					</td>
				</tr>          
				<tr>
					<td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
					<td class="tabDetailViewDF">
						<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"><?php echo $campos[observacoes] ?></textarea>    							
					</td>
				</tr>
	   	 	 </table>
			<?php
						
				//verifica a exibição
				if ($dados_usuario["evento_financeiro"] == 1 || $usuarioNome == 'Zulaine')
				{
			?> 
			<br/> 
			<span class="TituloModulo">Informações Financeiras:</span>
         	<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0"> 	
				<tr>
					<td valign="top" width="130" class="dataLabel">Posição Financeira:</td>
					<td colspan="5" class="tabDetailViewDF">						   
						<table width="500" cellpadding="0" cellspacing="0">
					        <tr valign="middle">
					            <td width="150" height="20">
					                <input name="edtPosicaoFinanceira" type="radio" value="1" <?php echo $financeiro_1 ?> />&nbsp;A Receber
					            </td>
					            <td width="150" height="20">
					                <input name="edtPosicaoFinanceira" type="radio" value="2" <?php echo $financeiro_2 ?> />&nbsp;Recebido
					            </td>
					            <td width="200" height="20">
					                <input name="edtPosicaoFinanceira" type="radio" value="3" <?php echo $financeiro_3 ?> />&nbsp;Cortesia
					            </td>
					        </tr>
					    </table>	
					</td>
				</tr>           
				<tr>
					<td valign="top" class="dataLabel">Número da NF:</td>
					<td colspan="5" class="tabDetailViewDF">
						<input name="edtNotaFiscal" type="text" class="datafield" id="edtNotaFiscal" style="width: 110px" maxlength="20" value="<?php echo $campos[numero_nf] ?>" />
					</td>
				</tr>
				<tr>
					<td width="130" valign="top" class="dataLabel">Obs. Financeiras:</td>
				    <td colspan="5" class="tabDetailViewDF">
						<textarea name="edtObservacoesFinanceiro" wrap="virtual" class="datafield" id="edtObservacoesFinanceiro" style="width: 100%; height: 100px"><?php echo $campos[obs_financeira] ?></textarea>
					</td>
				</tr>
			</table>
			<?php

				}
				
			?>
     	</td>
	</tr>
</table>  	 
</form>

</td>
</tr>
</table>
