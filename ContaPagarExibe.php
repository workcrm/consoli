<?php 
###########
## Módulo para Exibição dos dados das contas a pagar
## Criado: 17/05/2007 - Maycon Edinger
## Alterado: 11/07/2007 - Maycon Edinger
## Alterações:
## 06/06/2007 - Implementado todos os novos campos solicitados 
## 03/07/2007 - Implementado campo para condição de pagamento
## 05/07/2007 - Implementado para incluir o cheque na conta
## 11/07/2007 - Implementado para incluir o campo de subgrupo e nro do documento
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

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

//Recupera o id da conta a exeibir
$ContaId = $_GET["ContaId"];

//Monta o sql para recuperar os dados da conta
$sql = "SELECT 
		con.id,
		con.data,
		con.tipo_pessoa,
		con.pessoa_id,
		con.grupo_conta_id,
		con.subgrupo_conta_id,
		con.evento_id,
		con.descricao,
		con.nro_documento,
		con.condicao_pgto_id,
		con.valor,
		con.data_vencimento,
		con.situacao,
		con.data_pagamento,
		con.tipo_pagamento,
		con.cheque_id,
		con.valor_pago,
		con.observacoes,
		con.cadastro_timestamp,
		con.cadastro_operador_id,
		con.alteracao_timestamp,
		con.alteracao_operador_id,
		usu_cad.nome AS operador_cadastro_nome, 
		usu_cad.sobrenome AS operador_cadastro_sobrenome,
		usu_alt.nome AS operador_alteracao_nome, 
		usu_alt.sobrenome AS operador_alteracao_sobrenome,
		cat.nome AS categoria_nome,
		gru.nome AS grupo_nome,
		sub.nome AS subgrupo_nome,
		cond.nome AS condicao_pgto_nome,
		evento.nome AS evento_nome,
		regi.nome AS regiao_nome

		FROM contas_pagar con
		LEFT OUTER JOIN usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
		LEFT OUTER JOIN usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
		LEFT OUTER JOIN categoria_conta cat ON con.categoria_id = cat.id 
		LEFT OUTER JOIN grupo_conta gru ON con.grupo_conta_id = gru.id 							 
		LEFT OUTER JOIN subgrupo_conta sub ON con.subgrupo_conta_id = sub.id 							 
		LEFT OUTER JOIN condicao_pgto cond ON con.condicao_pgto_id = cond.id 							 
		LEFT OUTER JOIN eventos evento ON con.evento_id = evento.id 
		LEFT OUTER JOIN regioes regi ON regi.id = con.regiao_id
				
		WHERE con.id = '$ContaId'";			
			   
//Executa a query
$resultado = mysql_query($sql);

//Monta o array dos dados
$campos = mysql_fetch_array($resultado);

//Efetua o switch para o campo de situacao
switch ($campos[situacao]) 
{
	case 1: 
		$desc_situacao = "<span style='color: #990000'><strong>Em aberto</strong></span>"; 
		$mostra_pagar = 1;
	break;
	case 2: 
		$desc_situacao = "<span style='color: blue'><strong>Pago</strong></span>"; 
		$mostra_pagar = 0;   
	break;
}    

//Caso a conta já tenha um valor recebido mas ainda está em aberta, então ela possui um recebimento parcial
if ($campos[valor_pago] > 0 AND $campos[situacao] == 1)
{
 
  $desc_situacao = "<span style='color: #018B0F'><strong>Pagamento Parcial</strong></span>";
  $mostra_pagar = 1; 
  
}

//Efetua o switch para o campo tipo de pessoa
switch ($campos[tipo_pessoa]) 
{
  case 1: 
		$desc_pessoa = "Cliente:"; 
		$busca_pessoa = mysql_query("SELECT id, nome FROM clientes WHERE id = '$campos[pessoa_id]'");
		$dados_pessoa = mysql_fetch_array($busca_pessoa);
		$id_pessoa = $dados_pessoa[id];
		$nome_pessoa = $dados_pessoa[nome];
	break;
	case 2: 
		$desc_pessoa = "Fornecedor:"; 
		$busca_pessoa = mysql_query("SELECT id, nome FROM fornecedores WHERE id = '$campos[pessoa_id]'");
		$dados_pessoa = mysql_fetch_array($busca_pessoa);
		$id_pessoa = $dados_pessoa[id];
		$nome_pessoa = $dados_pessoa[nome];	
	break;
	case 3: 
		$desc_pessoa = "Colaborador:"; 
		$busca_pessoa = mysql_query("SELECT id, nome FROM colaboradores WHERE id = '$campos[pessoa_id]'");
		$dados_pessoa = mysql_fetch_array($busca_pessoa);
		$id_pessoa = $dados_pessoa[id];
		$nome_pessoa = $dados_pessoa[nome];	
	break;	
}    

//Efetua o switch para o campo tipo de pagamento
switch ($campos[tipo_pagamento]) 
{
	case 1: $desc_pago = "Dinheiro"; break;
	case 2: $desc_pago = "Cheque - Nº: " . $campos[cheque_numero]; break;
}
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização da Conta a Pagar</span></td>
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
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td width="88" style="PADDING-BOTTOM: 2px">
									<input name="btnEditarConta" type="button" class="button" title="Edita esta Conta" value="Editar Conta" onclick="wdCarregarFormulario('ContaPagarAltera.php?Id=<?php echo $campos[id] ?>&headers=1','conteudo')" />
								</td>
								<td style="PADDING-BOTTOM: 2px">
									<?php
										
										//Verifica o nível de acesso do usuário
										if ($nivelAcesso >= 3) 
										{
					    	
										//Exibe o botão de excluir
									?>
					    	
									<input class="button" title="Exclui esta conta a pagar" value="Excluir esta Conta a Pagar" type="button" name="btExcluir" onclick="if(confirm('Confirma a exclusão desta Conta a Pagar ?\n\nCaso tenha algum serviço tercerizado vinculado a esta conta a pagar, o mesmo será excluída automaticamento do evento.')) {wdCarregarFormulario('ContaPagarExclui.php?ContaId=<?php echo $campos[id] ?>','conteudo')}" style="width: 150px" />
					    
									<?php
										}
									?>
								</td>
								<td width="90" align="right">	  </td>
							</tr>
						</table>	

						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="22">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td width="450" class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Caso desejar alterar esta conta a pagar, clique em [Editar Conta] </td>
											<td align="right" style="padding-right: 10px;" class="tabDetailViewDL">
												<span style="font-size: 16px; color: #990000"><strong><?php echo $desc_situacao ?></strong></span>
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
									<?php echo DataMySQLRetornar($campos[data]) ?>
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Descrição:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<b><?php echo $campos[descricao] ?></b>
								</td>
							</tr> 
							<tr>
								<td width="140" class="dataLabel">Região:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<?php echo $campos[regiao_nome] ?>
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Conta-caixa:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<?php echo $campos[subgrupo_nome] ?>
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Centro de Custo:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<?php echo $campos[grupo_nome] ?>
								</td>
						   </tr>
						   <tr>
								<td width="140" class="dataLabel">Tipo Pessoa/Sacado:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<?php echo $desc_pessoa ?><br/>
									<b><?php echo $nome_pessoa ?></b>
								</td>
						   </tr>
						   <tr>
								<td width="140" class="dataLabel">Evento:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<?php echo $campos[evento_nome] ?>
								</td>
							</tr>						  
							<tr>
								<td width="140" class="dataLabel">Nº do Documento:</td>
								<td colspan="4" valign="middle" class=tabDetailViewDF>
									<?php echo $campos[nro_documento] ?>
								</td>
							</tr>
							<tr>
								<td width="140" valign="top" class=dataLabel>Valor: </td>
								<td width="173" class=tabDetailViewDF>
									<?php echo "R$ " . number_format($campos[valor], 2, ",", ".") ?>
								</td>
								<td width="146" class=dataLabel>Data Vencimento:</td>
								<td colspan="2" class=tabDetailViewDF>
									<?php echo DataMySQLRetornar($campos[data_vencimento]) ?>
								</td>
							</tr>
							<tr>
								<td width="140" valign="top" class=dataLabel>Situação:</td>
								<td colspan="4" width="173" class=tabDetailViewDF>
									<table cellSpacing="0" cellPadding="0" width="100%" border="0">
										<tr>
											<td width="90">
												<span style="font-size: 12px"><?php echo $desc_situacao ?></span>
											</td>
											<td>									
												<?php 
													if ($mostra_pagar == 1)
													{
												?>
												<input name="btnPagar" type="button" class="button" id="btnPagar" title="Pagar a Conta" value="Pagar Conta" style="width: 90px" onclick="wdCarregarFormulario('ContaPagarQuita.php?ContaId=<?php echo $campos[id] ?>&headers=1','conteudo')" />											
												<?php
													}
												?>
											</td>
										</tr>
									</table>
								</td>            
							</tr>
							<tr>
								<td width="140" valign="top" class=dataLabel>Valor Pago:</td>
								<td width="173" class=tabDetailViewDF>
									<?php echo "R$ " . number_format($campos[valor_pago], 2, ",", ".") ?>
								</td>
								<td width="146" class=dataLabel>Saldo a Pagar:</td>
								<td colspan="2" class=tabDetailViewDF>
									<?php echo "R$ " . number_format($campos[valor] - $campos[valor_pago], 2, ",", ".") ?>
								</td>
							</tr>          
							<tr>
								<td width="140" valign="top" class=dataLabel>Informa&ccedil;&otilde;es Complementares:</td>
								<td colspan="4" class=tabDetailViewDF>
									<?php echo nl2br($campos[observacoes]) ?>
								</td>
							</tr>
							<tr>
								<td valign="top" class=dataLabel>Data de Cadastro: </td>
								<td class=tabDetailViewDF>
									<?php 
										//Exibe o timestamp do cadastro da conta
										echo TimestampMySQLRetornar($campos[cadastro_timestamp]) 
									?>					
								</td>
								<td class=dataLabel>Operador:</td>
								<td colspan="2" class=tabDetailViewDF>
									<?php 
										//Exibe o nome do operador do cadastro da conta
										echo $campos[operador_cadastro_nome] . " " . $campos[operador_cadastro_sobrenome] 
									?>					 
								</td>
							</tr>
							<tr>
								<td valign="top" class=dataLabel>Data de Altera&ccedil;&atilde;o: </td>
								<td class=tabDetailViewDF>
									<?php 
										//Verifica se este registro já foi alterado
										if ($campos[alteracao_operador_id] <> 0) 
										{
											
											//Exibe o timestamp da alteração da conta
											echo TimestampMySQLRetornar($campos[alteracao_timestamp]);
										
										}
									?>			 		
								</td>
								<td class=dataLabel>Operador:</td>
								<td colspan="2" class=tabDetailViewDF>
									<?php 
										
										//Verifica se este registro já foi alterado
										if ($campos[alteracao_operador_id] <> 0) 
										{
											//Exibe o nome do operador da alteração da conta
											echo $campos[operador_alteracao_nome] . " " . $campos[operador_alteracao_sobrenome];
										}
									?>			 		 
								</td>
							</tr>           	
						</table>

						<?php 

							//Monta um sql para pesquisar se há algum pagamento lançado para esta conta
							$sql_consulta = mysql_query("SELECT * FROM contas_pagar_pagamento WHERE conta_pagar_id = $ContaId ORDER BY data_pagamento");
																				 
							$registros = mysql_num_rows($sql_consulta); 
																		
						?>

						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>  
								<td>
									<table width="100%" cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Pagamentos efetuados para esta conta:</span></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table id="4" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">

  		<?php
    	
			//Caso não houverem registros
			if ($registros == 0) 
			{ 
	
				//Exibe uma linha dizendo que nao registros
				echo "
				<tr height='24'>
				<td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
					<font color='#33485C'><b>Não há pagamentos efetuados para esta conta a pagar</b></font>
					</td>
				</tr>	
				";	  
			
			} 
			
			else 
			
			{		  			   

				//Exibe o cabeçalho da tabela
				echo "
					<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
						<td width='22' style='padding-left: 12px'>&nbsp;A</td>
						<td width='70' style='padding-left: 12px'>&nbsp;Data</td>
						<td width='110'>Tipo Pgto</td>
						<td width='110' align='center'>Nº do Cheque</td>
						<td width='100' align='right'>Valor Pago</td> 		      
						<td>&nbsp;&nbsp;Observações</td>
					</tr>";
		   	
				
				$Linha = 1;
				
				//Cria o array e o percorre para montar a listagem dinamicamente
				while ($dados_consulta = mysql_fetch_array($sql_consulta))
				{
    
					if ($Linha < $registros)
					{
					
						$linha_display = "border-bottom: 1px dotted #aaa;";
						
					}
					
					else
					
					{
					
						$linha_display = "";
						
					}
	
					//Efetua o switch do tipo de pagamento
					switch ($dados_consulta[tipo_pagamento]) 
					{
						case 1: $nome_tipo = "Dinheiro";	break;
						case 2: $nome_tipo = "Cheque da Empresa";	break;       	
						case 3: $nome_tipo = "Cheque de Terceiro"; break;
					}
    
				?>
				<tr valign="middle">
					<td height="24" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>padding-bottom: 1px; padding-left: 12px">
						<img src="image/grid_exclui.gif" alt="Clique para estornar este pagamento" onclick="if(confirm('Confirma o estorno deste pagamento ?')) {wdCarregarFormulario('ContaPagarQuitaExclui.php?PagamentoId=<?php echo $dados_consulta[id] ?>&ContaId=<?php echo $dados_consulta[conta_pagar_id] ?>','conteudo')}" style="cursor: pointer" />
					</td>	
					<td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>padding-bottom: 1px; padding-left: 12px">
						&nbsp;<?php echo DataMySQLRetornar($dados_consulta[data_pagamento]) ?>
						</font>        
					</td>
					<td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>">
						<?php echo $nome_tipo ?>
					</td>
					<td align="center" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>">
						<?php echo $dados_consulta[numero_cheque] ?>
					</td>
					<td align="right" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>">
						<?php echo "R$ " . number_format($dados_consulta[total_pago], 2, ",", ".") ?>
					</td>				
					<td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>">
						&nbsp;&nbsp;<?php echo $dados_consulta[obs] ?>
					</td>						
				</tr>

				<?php
					
					$Linha++;
					
					//Fecha o WHILE
					}
		  
				//Fecha o if de se tiver pagamentos
				}
			
			?>
			</table>
		</td>
	</tr>
</table>
			</td>
	  </tr>
	</table>
	
	</td>
  </tr>
</table>
