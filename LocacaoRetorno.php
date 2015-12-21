<?php 
###########
## Módulo para Gerenciamento do retorno dos itens da locação
## Criado: 17/09/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

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

//Pega o valor da locacao a exibir
if ($_GET["LocacaoId"]) 
{
	
	$LocacaoId = $_GET["LocacaoId"];

} 

else 

{
	
	$LocacaoId = $_POST["LocacaoId"];

}

//Recupera dos dados do evento
$sql_evento = "SELECT 
				loc.id,
				loc.data,
				loc.tipo_pessoa,
				loc.pessoa_id,
				loc.descricao,
				loc.situacao,
				loc.devolucao_prevista,
				loc.devolucao_realizada,
				loc.observacoes,
				loc.recebido_por,
				cli.id as cliente_id,
				cli.nome as cliente_nome,
				forn.id as fornecedor_id,
				forn.nome as fornecedor_nome,
				col.id as colaborador_id,
				col.nome as colaborador_nome
				FROM locacao loc 
				LEFT OUTER JOIN clientes cli ON cli.id = loc.pessoa_id
				LEFT OUTER JOIN fornecedores forn ON forn.id = loc.pessoa_id
				LEFT OUTER JOIN colaboradores col ON col.id = loc.pessoa_id							
				WHERE loc.id = $LocacaoId";
  
//Executa a query
$resultado = mysql_query($sql_evento);

//Monta o array dos campos
$dados_locacao = mysql_fetch_array($resultado);

//Efetua o switch para o campo de status
switch ($dados_locacao[situacao]) 
{
	case 1: 
		$desc_status = "Pendente"; 
	break;
	case 2: 
		$desc_status = "Recebida"; 
	break;
}    

//Efetua o switch para o campo de pessoa
switch ($dados_locacao[tipo_pessoa]) 
{
	
	//Se for cliente
	case 1: 
		$pessoa_tipo = "Cliente";
		$pessoa_nome = $dados_locacao[cliente_nome]; 
	break;
	//Se for fornecedor
	case 2: 
		$pessoa_tipo = "Fornecedor"; 
		$pessoa_nome = $dados_locacao[fornecedor_nome];
	break;
	//Se for colaborador
	case 3: 
		$pessoa_tipo = "Colaborador"; 
		$pessoa_nome = $dados_locacao[colaborador_nome];							
	break;

}

//Efetua o switch para o campo de situacao
switch ($dados_locacao[situacao]) 
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
?>


<script language="javascript">

function valida_retorno()
{
	
	var Form = document.frmLocacaoRetorno;
  
	if (Form.edtDataDevolucaoRealizada.value = "" || Form.edtDataDevolucaoRealizada.value = "0/0/0")
	{
  	
		alert('É necessário informar a data do retorno da locação !')
		return false;	
	
	}
	
	return true;
}
</script>

<form name="frmLocacaoRetorno" action="sistema.php?ModuloNome=LocacaoRetorno" method="post" onsubmit="return valida_retorno()">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Retorno dos Itens da Locação</span></td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12" />
					</td>
				</tr> 
				<tr>
					<td style="padding-bottom: 2px">
						<input class="button" title="Retorna a exibição do detalhamento da locação" name="btnVoltar" type="button" id="btnRelatorio" value="Retornar a Locação" style="width:120px" onclick="wdCarregarFormulario('LocacaoExibe.php?LocacaoId=<?php echo $LocacaoId ?>&headers=1','conteudo')"/>						
						<br />	   	   		   		
 					</td>   
				</tr> 
			</table> 	
			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="100%" class="text">
							<table cellspacing="0" cellpadding="0" width="100%" border="0">
								<tr>
									<td colspan="5" style="PADDING-BOTTOM: 2px">
										<?php
											
											//Recupera os valores vindos do formulário e armazena nas variaveis
											if($_POST["Submit"])
											{
						
												$edtStatus = $_POST["edtSituacao"];
												$edtDataDevolucaoRealizada = DataMySQLInserir($_POST["edtDataDevolucaoRealizada"]);
												$edtTotalChk = $_POST["edtTotalChk"];
												$edtLocacaoId = $_POST["LocacaoId"];
																																				
												$status_locacao = 1;																											
																													
												//Primeiro apaga todos os itens que já existem na base de itens do evento
												$sql_exclui_item = "UPDATE locacao_item SET quantidade_retorno = 0 WHERE locacao_id = $LocacaoId";
							
												//Executa a query
												$query_exclui_item = mysql_query($sql_exclui_item);
						
												//Define o valor inicial para efetuar o FOR
												for ($contador_for = 1; $contador_for < $edtTotalChk; $contador_for++)
												{
											
													//Monta a variável com o nome dos campos
													$texto_qtde_locada = "QtdeItemId" . $contador_for;
													$texto_qtde = "edtRetorno" . $contador_for;
													$texto_item = "ItemId" . $contador_for;																																			
																							
													$sql_insere_item = "UPDATE locacao_item SET
																		 quantidade_retorno =  '$_POST[$texto_qtde]'
																		 WHERE locacao_id = $edtLocacaoId
																		 AND item_id = $_POST[$texto_item]";						
												
												
													//echo $sql_insere_item;
													
													//Insere os registros na tabela de eventos_itens
													mysql_query($sql_insere_item);
													
													$saldo_item =  $_POST[$texto_qtde_locada] -  $_POST[$texto_qtde];
													 
													//Verifica o saldo do item
													if ($saldo_item > 0)
													{
														
														//Marca que a locação ainda está em aberto
														$status_locacao = 1;
														
													} 
													
													else 
													
													{
														
														//Marca que a locação como concluída
														$status_locacao = 2;
														
													}
																					
												//Fecha o FOR
												}						  								  				 
											
												//Verifica se deve modificar o status da locação
												if ($status_locacao == 2)
												{
												
													//Atualiza a locação com o status e a data de retorno
													$sql_atualiza_locacao = "UPDATE locacao SET 
																			situacao = '2', 
																			devolucao_realizada = '$edtDataDevolucaoRealizada',
																			alteracao_timestamp = now(),
																			alteracao_operador_id = '$usuarioId' 
																			WHERE id = $LocacaoId";
												
												} 
												
												else if ($status_locacao == 1)
												
												{
												
													//Atualiza a locação com o status e a data de retorno
													$sql_atualiza_locacao = "UPDATE locacao SET 
																			situacao = '1', 
																			devolucao_realizada = '$edtDataDevolucaoRealizada',
																			alteracao_timestamp = now(),
																			alteracao_operador_id = '$usuarioId' 
																			WHERE id = $LocacaoId";
												
												}
												
												//Roda a query que seta a situação da locação
												$query_atualiza_locacao = mysql_query($sql_atualiza_locacao);
								
												//Exibe a mensagem de inclusão com sucesso
												echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Retorno da Locação Cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></div></br><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
											}
										?>
									</td>
								</tr>				          
							</table>
           
							<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
								<tr>
									<td class="dataLabel" width="15%">
										<span class="dataLabel">Data:</span>             
									</td>
									<td colspan="5" class="tabDetailViewDF">
										<b><?php echo DataMySQLRetornar($dados_locacao["data"]) ?></b>
									</td>
								</tr>
								<tr>
									<td valign="top" class="dataLabel">Tipo de Pessoa:</td>
									<td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo $pessoa_tipo ?></td>
								</tr>
								<tr>
									<td valign="top" class="dataLabel">Locador:</td>
									<td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo $pessoa_nome ?></td>
								</tr>
								<tr>
									<td valign="top" class="dataLabel">Descrição:</td>
									<td colspan="5" valign="middle" class="tabDetailViewDF"><b><?php echo $dados_locacao["descricao"] ?></b></td>
								</tr>
								<tr>
									<td class="dataLabel">Devolução Prevista:</td>
									<td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo DataMySQLRetornar($dados_locacao["devolucao_prevista"]) ?></td>
								</tr>
								<tr>
									<td valign="top" class="dataLabel">Situação:</td>
									<td valign="middle" class="tabDetailViewDF">
										<span style="font-size: 12px; color: #990000"><b><?php echo $desc_status ?></b></span>
									</td>
									<td width="130" valign="middle" class="dataLabel">Devolução Realizada:</td>
									<td width="28%" valign="middle" class="tabDetailViewDF">
										<?php
										    
											//Define a data do formul&aacute;rio
										    $objData->strFormulario = "frmLocacaoRetorno";  
										    //Nome do campo que deve ser criado
										    $objData->strNome = "edtDataDevolucaoRealizada";
										    //Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
										    $objData->strValor = DataMySQLRetornar($dados_locacao[devolucao_prevista]);
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
									<td colspan="5" valign="middle" class="tabDetailViewDF">
										<?php echo $dados_locacao[recebido_por] ?>             
									</td>
								</tr> 		          
								<tr>
									<td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares :</td>
									<td colspan="5" class="tabDetailViewDF"><?php echo nl2br($dados_locacao["observacoes"]) ?></td>
								</tr>			           
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<br/>
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Itens da Locação</span></td>
								</tr>
								<tr>
									<td colspan="5">
										<img src="image/bt_espacohoriz.gif" width="100%" height="5">
									</td>
								</tr>
							</table>
							<table width="100%" cellpadding="0" cellspacing="0" border="0" >
								<tr valign="middle">
									<td style="PADDING-BOTTOM: 2px">
										Informe as quantidades de retorno dos itens locados. Após, clique em Confirmar Retorno dos Itens.<br/>
										<input class="button" title="Confirma a devolução dos itens da locação" name="Submit" type="submit" id="btnRetorno" value="Confirmar Retorno dos Itens" style="width:160px"/>									
									</td>
								</tr>
							</table>		
							<?php
						
								//verifica todos os itens cadastrados na base para montar o primeiro array (para comparar com os que estão inclusos no evento
								//Monta a query de filtragem dos itens
								$filtra_item = "SELECT
												locacao_id														 
												FROM locacao_item
												WHERE locacao_id = $LocacaoId";
					
								//Executa a query
								$lista_item = mysql_query($filtra_item);
					 
								//Cria um contador com o número de contar que a query retornou
								$registros = mysql_num_rows($lista_item);
					   
							?>
			   
							<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
							<?php
		
								//Caso houverem registros
								if ($registros > 0) 
								{ 
			  
									echo "<tr height='20'>
											<td width='60' align='center' class='listViewThS1' style='padding-right: 5px'>Qtde Locada</td>
											<td width='25' class='listViewThS1'>Un</td>
											<td width='60' align='center' class='listViewThS1' style='padding-right: 5px'>Qtde Retorno</td>
											<td width='60' align='center' class='listViewThS1' style='padding-right: 5px'>Qtde Pendente</td>
											<td width='355' class='listViewThS1'>&nbsp;&nbsp;Descrição do Item</td>
											<td class='listViewThS1'>Observações</td>
										</tr>"; 
								
								}
		
								//Caso não houverem registros
								if ($registros == 0) 
								{ 
		
								
									echo "<tr height='24'>
											<td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
												<font color='#33485C'><b>Não há itens cadastrados para esta locação</b></font>
											</td>
										</tr>";	  
								
								} 	  
				
								//Monta a variável de total do evento
								$total_evento = 0;
				
				
								//Monta a query para capturar as categorias que existem cadastrados itens
								$sql_categoria = mysql_query("SELECT 
															ite.id,
															ite.categoria_id,											
															cat.nome as categoria_nome,
															loc.valor_venda
															FROM item_evento ite
															LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
															INNER JOIN locacao_item loc ON loc.item_id = ite.id
															WHERE loc.locacao_id = $LocacaoId											
															GROUP BY cat.nome
															ORDER BY cat.nome");
			

								//Gera a variável do contador de registros
								$edtItemChk = 1;
								
								//Percorre o array das funcoes
								while ($dados_categoria = mysql_fetch_array($sql_categoria))
								{				
				
									//Fecha o php para imprimir o texto da categoria
									?>
						   
									<tr height="22">
										<td colspan="7" valign="bottom" style="padding-left: 8px">    				 	 
											<span style="font-size: 14px"><b>
											<?php 
												
												if ($dados_categoria["categoria_id"] == 0) 
												{
													
													echo "Sem categoria definida";
												} 
												
												else 
												
												{
													
													echo $dados_categoria["categoria_nome"];
												}			
											
											?>
											</b></span>
										</td>						 
									</tr>
									
									<?php
					
										//Monta a query de filtragem dos itens
										$filtra_item = "SELECT 
														ite.id,
														ite.nome,
														ite.unidade,											
														cat.nome as categoria_nome,
														loc.quantidade,
														loc.quantidade_retorno,
														loc.observacoes
														FROM item_evento ite
														LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
														INNER JOIN locacao_item loc ON loc.item_id = ite.id
														WHERE loc.locacao_id = $LocacaoId
														AND ite.categoria_id = '$dados_categoria[categoria_id]'
														ORDER BY cat.nome, ite.nome";
					
										//Executa a query
										$lista_item = mysql_query($filtra_item);
								  
								   
										//Percorre o array
										while ($dados_item = mysql_fetch_array($lista_item))
										{				  
												
									?>
		
									<tr valign="middle">
										<td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 5px">
											<?php echo $dados_item[quantidade]	?>
											<input name="QtdeItemId<?php echo $edtItemChk ?>" type="hidden" value="<?php echo $dados_item[quantidade] ?>" />
										</td>
										<td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
											<?php echo $dados_item[unidade] ?>
										</td>					 
										<td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 5px">
											<?php
												
												//verifica se a quantidade de retorno for igual a zero
												if ($dados_item[quantidade_retorno] == 0)
												{
						 			
											//gera o input com o quantidade da locação
											?>
											<input name="edtRetorno<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 46px" maxlength="10" title="Informe a quantidade de retorno do item" value="<?php echo $dados_item[quantidade] ?>" />
											<?php
										
												//Determina o valor da quantiadade
												$vlr_quantidade = $dados_item[quantidade];
									
												} 
											
												else 
											
												{
											?>
											<input name="edtRetorno<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 46px" maxlength="10" title="Informe a quantidade de retorno do item" value="<?php echo $dados_item[quantidade_retorno] ?>" />
											<?php
									
												//Determina o valor da quantiadade
												$vlr_quantidade = $dados_item[quantidade_retorno];
									
												}
											?>
											<input name="ItemId<?php echo $edtItemChk ?>" type="hidden" value="<?php echo $dados_item[id] ?>" />
										</td>
										<td align="center" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-top: 1px; padding-bottom: 2px">
											<?php 
						 
												$saldo_item = $dados_item[quantidade] - $vlr_quantidade;
						 		
												echo $saldo_item;
								 
											?>								 								
										</td>
										<td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-top: 1px; padding-bottom: 2px">
											<?php echo $dados_item[nome] ?>
										</td>
										<td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
											<?php echo nl2br($dados_item[observacoes]) ?>
										</td>
									</tr>
									
									<?php

										//Incrementa o contador
										$edtItemChk++;
			
										//Fecha o while dos itens
										}
			
										//Fecha o while das categorias
									}
								?>
			</table>
			<br/>
		</td>
	</tr>				
</table> 
 
<input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>"/>
<input name="LocacaoId" type="hidden" value="<?php echo $LocacaoId ?>"/>
	 
</form>
</td>
</tr>



</table>
</td>
