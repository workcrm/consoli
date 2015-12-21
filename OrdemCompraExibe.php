<?php 
###########
## Módulo para Exibição dos dados da ordem de compra
## Criado: 23/03/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

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

//Pega o valor a exibir
$OrdemId = $_GET["OrdemId"];

//Monta o SQL
$sql = "SELECT 
		oc.id,
		oc.data,
		oc.prazo_compra,
		oc.solicitante,
		oc.departamento_id,
		oc.evento_id,
		oc.fornecedor_id,
		oc.transportadora,
		oc.cond_pgto,
		oc.prazo_entrega,
		oc.obs,
		oc.desconto,
		oc.cadastro_timestamp,
		oc.cadastro_operador_id,
		oc.alteracao_timestamp,
		oc.alteracao_operador_id,
		dep.nome AS departamento_nome,
		eve.nome AS evento_nome,
		forn.nome AS fornecedor_nome
		FROM ordem_compra oc
		LEFT OUTER JOIN departamentos dep ON dep.id = oc.departamento_id
		LEFT OUTER JOIN eventos eve ON eve.id = oc.evento_id
		LEFT OUTER JOIN fornecedores forn ON forn.id = oc.fornecedor_id
		WHERE oc.id = '$OrdemId'";
  
//Executa a query
$resultado = mysql_query($sql);

//Monta o array dos campos
$campos = mysql_fetch_array($resultado);

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização da Ordem de Compra</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>
	
			<table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="text">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td width="144" style="padding-bottom: 2px">
									<input name="btnEditarConta" type="button" class="button" title="Edita esta Ordem de Compra" value="Editar OC" onclick="wdCarregarFormulario('OrdemCompraAltera.php?OrdemId=<?php echo $OrdemId ?>&headers=1','conteudo')" style="width: 140px">
								</td>
								<td width="90" style="padding-bottom: 2px">
									<input name="btnExcluiOC" type="button" class="button" title="Exclui esta Ordem de Compra" value="Excluir OC" onclick="if(confirm('Confirma a exclusão desta OC ?')) {wdCarregarFormulario('OrdemCompraExclui.php?OrdemId=<?php echo $OrdemId ?>&headers=1','conteudo')}" style="width: 110px">
								</td>
								<td align="right" style="padding-bottom: 2px">
									<input name="ImprimeOc" type="button" class="button" style="width:120px" id="ImprimeOc" title="Imprime esta OC" value="Imprimir OC" onclick="abreJanela('./relatorios/OrdemCompraRelatorioPDF.php?OrdemId=<?php echo $OrdemId ?>');" />
								</td>
							</tr>
						</table>
           
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" >
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="2">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left">
												<img src="image/bt_cadastro.gif" width="16" height="15" /> Caso desejar alterar esta Ordem de Compra, clique em [Editar Ordem de Compra]								
											</td>
										</tr>
									</table>					
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="18%">Número:</td>
								<td valign="middle" class="tabDetailViewDF">
									<span style="font-size: 18px; color: #990000"><strong><?php echo $campos["id"] ?></strong></span>				  
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Data:</td>
								<td valign="middle" class="tabDetailViewDF">
									<span><strong><?php echo DataMySQLRetornar($campos["data"]) ?></strong></span>				  
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Solicitante:</td>
								<td valign="middle" class="tabDetailViewDF">
									<span><?php echo $campos["solicitante"] ?></span>				  
								</td>
							</tr>							
							<tr>
								<td class="dataLabel">Departamento:</td>
								<td valign="middle" class="tabDetailViewDF"><?php echo $campos["departamento_nome"] ?></td>
							</tr>
							<tr>
								<td class="dataLabel">Fornecedor:</td>
								<td class='tabDetailViewDF'><?php echo $campos["fornecedor_nome"] ?></td>
							</tr>
							<tr>
								<td class="dataLabel">Evento:</td>
								<td valign="middle" class="tabDetailViewDF"><?php echo $campos["evento_nome"] ?></td>
							</tr>
							<tr>
								<td class="dataLabel">Transportadora:</td>
								<td class='tabDetailViewDF'><?php echo $campos["transportadora"] ?></td>
							</tr>
							<tr>
								<td class="dataLabel">Condição Pagamento:</td>
								<td class='tabDetailViewDF'><?php echo $campos["cond_pgto"] ?></td>
							</tr>							
							<tr>
								<td class="dataLabel">Prazo de Entrega:</td>
								<td class='tabDetailViewDF'><?php echo DataMySQLRetornar($campos["prazo_entrega"]) ?></td>
							</tr>
							<tr>
								<td valign="top" class="dataLabel">Observa&ccedil;&otilde;es:</td>
								<td class="tabDetailViewDF"><?php echo nl2br($campos["obs"]) ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<br/>
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Produtos da Ordem de Compra</span>
					</td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
				<tr>
					<td>
					<input name="btnNovoProduto" type="button" class="button" title="Insere um produto para esta OC" value="Adicionar Produto" onclick="wdCarregarFormulario('OrdemCompraProdutoAvulsoCadastra.php?OrdemId=<?php echo $campos[id] ?>&headers=1','conteudo')" style="margin-bottom: 4px; width: 140px">
					<br/>
					<?php
					
					//Consulta todos os produtos da OC
					$consulta = "SELECT 
								prod.id,
								produto.id AS produto_id,
								produto.nome AS produto_nome,
								produto.unidade,
								prod.quantidade,
								prod.entrada,
								prod.valor_unitario 
								FROM ordem_compra_produto prod 
								LEFT OUTER JOIN item_evento produto ON produto.id = prod.produto_id
								WHERE ordem_compra_id = $OrdemId				
								ORDER BY produto.nome";

					//Executa a query
					$listagem = mysql_query($consulta);
					
					$registros = mysql_num_rows($listagem);
					
					//Caso tenha produtos associados
					if ($registros > 0)
					{
					
						?>
						
						<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
						<tr>
							<td colspan="9" align="right">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td height="20" nowrap align="left"  class="listViewPaginationTdS1"><span class="pageNumbers">Produtos da Ordem de Compra</span></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr height="20">
							<td width="40" class="listViewThS1">
								<div align="center">A&ccedil;&atilde;o</div>
							</td>
							<td width="50" align="center" class="listViewThS1">Código</td>
							<td class="listViewThS1">&nbsp;&nbsp;Descrição do Produto</td>
							<td align="right" width="60" class="listViewThS1">Quantid.</td>
							<td align="center" width="26" class="listViewThS1">Un</td>
							<td align="right" width="60" class="listViewThS1">Entrada</td>
							<td align="right" width="60" class="listViewThS1">Saldo</td>
							<td align="right" width="66" class="listViewThS1">Unitario</td>				        
							<td align="right" width="66" class="listViewThS1" style="padding-right: 6px">Total</td>
						</tr>
						
						<?php	
					
						$categoria_lista = 0;
							
						$linha = 1; 

						//Monta e percorre o array com os dados da consulta
						while ($dados = mysql_fetch_array($listagem))
						{

							if ($linha < $registros)
							{
							
								$borda = "border-bottom: 1px #aaa dashed;";
								
							}
							
							else
							
							{
							
								$borda = '';
								
							}
						
							//Caso seja uma outra categoria de produtos.
							if ($categoria_lista != $dados["categoria_id"])
							{
							
								echo "<tr height='16'>
										<td colspan='8' style='padding-left: 6px; padding-top: 5px; padding-bottom: 5px'><span class='TituloModulo'>$dados[categoria_nome]</span></td>
										</tr>";
							
							}
							
							$SaldoProduto = number_format($dados["quantidade"] - $dados["entrada"], 2,'.',',');
							
							$cor_celula = '#FDFDFD';
							
							//Verifica se a atividade esta concluida
							if ($SaldoProduto < 1)
							{
							
								$cor_celula = "#99FF99";
								
							}
							
							//Fecha o php, mas o while continua
							?>

							<tr height="16">
								<td height="24" style="<?php echo $borda ?>" bgcolor="<?php echo $cor_celula ?>">
									<div align="center">
										<img src="image/grid_exclui.gif" alt="Excluir Registro" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclusão deste produto da OC ?')) {wdCarregarFormulario('OrdemCompraProdutoAvulsoExclui.php?OrdemId=<?php echo $OrdemId ?>&ProdutoId=<?php echo $dados[id] ?>&headers=1','conteudo')}" style="cursor: pointer"></a>
										<img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('OrdemCompraProdutoAvulsoAltera.php?OrdemId=<?php echo $OrdemId ?>&ProdutoId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">   
									</div>
								</td>
								<td style="<?php echo $borda ?>" valign="middle" align="center" bgcolor="<?php echo $cor_celula ?>" class="currentTabList">
									<?php echo $dados["produto_id"] ?>
								</td>
								<td style="<?php echo $borda ?>" valign="middle" bgcolor="<?php echo $cor_celula ?>" class="oddListRowS1" onclick="wdCarregarFormulario('OrdemCompraProdutoAvulsoAltera.php?OrdemId=<?php echo $OrdemId ?>&ProdutoId=<?php echo $dados[id] ?>&headers=1','conteudo')">
									<a title="Clique para exibir este registro" href="#"><?php echo $dados[produto_nome] ?></a>
									<br/>
									<input name="btnEntradaProduto" type="button" class="button" title="Efetua a entrada (baixa) deste produto da OC" value="Efetuar Entrada/Baixa" onclick="wdCarregarFormulario('OrdemCompraProdutoEntrada.php?OrdemId=<?php echo $OrdemId ?>&ProdutoId=<?php echo $dados[produto_id] ?>&ItemId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="margin-bottom: 4px; width: 140px; height: 18px">
								</td>
								<td style="<?php echo $borda ?>" valign="middle" align="right" bgcolor="<?php echo $cor_celula ?>" class="currentTabList">
									<?php echo number_format($dados["quantidade"], 2,'.','') ?>
								</td>
								<td style="<?php echo $borda ?>" valign="middle" align="center" bgcolor="<?php echo $cor_celula ?>" class="currentTabList">
									<?php echo $dados["unidade"] ?>
								</td>
								<td style="<?php echo $borda ?>" valign="middle" align="right" bgcolor="<?php echo $cor_celula ?>" class="currentTabList">
									<?php echo number_format($dados["entrada"], 2,'.','') ?>
								</td>
								<td style="<?php echo $borda ?>" valign="middle" align="right" bgcolor="<?php echo $cor_celula ?>" class="currentTabList">
									<?php echo $SaldoProduto ?>
								</td>
								<td style="<?php echo $borda ?>" align="right" bgcolor="<?php echo $cor_celula ?>" class="currentTabList">
									<?php echo number_format($dados["valor_unitario"], 2, "", ".") ?>
								</td>
								<td style="padding-right: 6px; <?php echo $borda ?>" align="right" bgcolor="<?php echo $cor_celula ?>" class="currentTabList">
									<?php 
									
										$total_item = $dados["quantidade"] * $dados["valor_unitario"];
										
										$total_geral = $total_geral + $total_item;
										
										echo number_format($total_item, 2, ",", "."); 
										
									?>
								</td>
							</tr>
							
							<?php
								
								$categoria_lista = $dados["categoria_id"];
								
								$linha++;
								
						//Fecha o while
						}
						
						
					?>
					<tr height="16">
						<td height="24" colspan="8" align="right" style="border-top: 1px #aaa solid;">
							<b>TOTAL DOS PRODUTOS:</b>&nbsp;
						</td>
						<td height="24" style="border-top: 1px #aaa solid; padding-right: 6px" align="right">
							<b><?php echo number_format($total_geral, 2, ",", ".") ?></b>
						</td>
					</tr>
					<tr height="16">
						<td height="24" colspan="8" align="right">
							<b>DESCONTO:</b>&nbsp;
						</td>
						<td style="padding-right: 6px" align="right">
							<b><?php echo number_format($campos['desconto'], 2, ",", ".") ?></b>
						</td>
					</tr>
					<tr height="16">
						<td height="24" colspan="8" align="right">
							<b>TOTAL GERAL:</b>&nbsp;
						</td>
						<td style="padding-right: 6px" align="right">
							<b><?php echo number_format($total_geral - $campos['desconto'], 2, ",", ".") ?></b>
						</td>
					</tr>					
					</table>
					<?php
					
					}
					
					?>
					</td>
				</tr>
			</table>  	 
		</td>
	</tr>
</table>
</td>
