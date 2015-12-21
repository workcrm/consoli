<?php
###########
## Módulo para Compensacao de Cheques
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

//Armazena o mês atual na variável
$mes = date("m",mktime());
$dataHoje = date("Y-m-d", mktime());

//Busca o total de formandos do evento
$sql = mysql_query("SELECT 
					che.id,
					che.conta_corrente_id,
					che.numero_cheque,
					che.data_emissao,
					che.pre_datado,
					che.bom_para,
					che.valor,
					che.status,
					cont.nome AS conta_corrente_nome,
					cpag.descricao AS conta_pagar_nome
					FROM cheques_empresa che
					LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
					LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
					WHERE che.status = 1
					ORDER BY conta_corrente_nome, che.bom_para");

$registros = mysql_num_rows($sql);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="compensa" action="sistema.php?ModuloNome=ChequeCompensa" method="post" >

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="750">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Compensação de Cheques</span>			  	
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding-bottom: 2px">
			<?php
			
				if ($registros == 0)
				{

					?>
					<table width="100%" border="0" align="left"  cellpadding="0" cellspacing="0">
						<tr>
							<td height="22" width="20" valign="middle" bgcolor="#FFFFCD" style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
								<img src="./image/bt_informacao.gif" border="0" />
							</td>
							<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
								<strong><span style='color: #990000'>Atenção:</span> Não há cheques em aberto para compensação !</strong>
							</td>
						</tr>
					</table>
					<?php
					
					die();
					
				}
			?>
			<span><input name="Button" type="submit" class="button" id="Submit" title="Processar Compensação" value="Processar Compensação" /></span>
		</td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<br/>
						Selecione abaixo os cheques a compensar:
						<br/>
						<br/>
						<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">  
							<tr height="20">
								<td width="50" class="listViewThS1">
									<div align="center">Comp.</div>
								</td>
								<td width="80" class="listViewThS1">
									&nbsp;&nbsp;Nr Cheque
								</td>
								<td class="listViewThS1">
									Descrição da Conta
								</td>
								<td width="80" align="right" class="listViewThS1">
									Valor
								</td>
								<td width="80" align="center" class="listViewThS1">
									Emissão
								</td>
								<td width="80" align="center" class="listViewThS1">
									Bom Para
								</td>
							</tr>
							<?php
							
								//Cria a variavel zerada para o contador de checkboxes
								$edtItemChk = 0;
								
								$edtTotalValor = 0;
								
								$edtTotalGeralValor = 0;
								
								$edtContaCorrenteId = 0;
		 
								//Percorre os registros
								while ($dados = mysql_fetch_array($sql))
								{
								
									//Caso seja de outra conta-corrente
									if ($dados["conta_corrente_id"] != $edtContaCorrenteId)
									{
									
										//Verifica se nao e a primeira conta listada, para dai mostrar o total
										if ($edtContaCorrenteId != 0)
										{
										
										?>
										<tr height="18">
											<td colspan="3" align="right">
												Total a Compensar:
											</td>
											<td align="right">
												<b><?php echo number_format($edtTotalValor, 2, ",", ".") ?></b>
											</td>
											<td colspan="2">
												&nbsp;
											</td>
										</tr>
										<?php
										
										$edtTotalValor = 0;
										
										}
									?>
									
									<tr height="18">
										<td colspan="6" style="padding-left: 5px">
											<b><?php echo $dados["conta_corrente_nome"] ?></b>
										</td>
									</tr>
									
									<?php
										
									}

								?>
								<tr height="18">
									<td valign="middle" align="center" style="border-bottom: 1px dashed;">
										<input name="<?php echo ++$edtItemChk ?>" value="<?php echo $dados["id"] ?>" type="checkbox" style="border: 0px" title="Clique para marcar ou desmarcar a compensação deste cheque" <?php echo $chkItem ?>/>
									</td>
									<td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-bottom: 1px dashed;">
										<span style="color: #33485C"><b><?php echo $dados["numero_cheque"] ?></b></span>
									</td> 
									<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
										<?php echo $dados["conta_pagar_nome"] ?>
									</td> 
									<td align="right" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
										<?php echo number_format($dados["valor"],2,",",".") ?>
									</td>
									<td align="center" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
										<?php echo DataMySQLRetornar($dados["data_emissao"]) ?>
									</td>
									<td align="center" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
										<?php echo DataMySQLRetornar($dados["bom_para"]) ?>
									</td>
								</tr>
								<?php
								
								$edtTotalValor = $edtTotalValor + $dados["valor"];
								
								$edtTotalGeralValor = $edtTotalGeralValor + $dados["valor"];
								
								$edtContaCorrenteId = $dados["conta_corrente_id"];
									
								}
							
							?>
							<tr height="18">
								<td colspan="3" align="right">
									Total a Compensar:
								</td>
								<td align="right">
									<b><?php echo number_format($edtTotalValor, 2, ",", ".") ?></b>
								</td>
								<td colspan="2">
									&nbsp;
								</td>
							</tr>
							<tr height="18">
								<td colspan="3" align="right">
									Geral a Compensar:
								</td>
								<td align="right">
									<b><?php echo number_format($edtTotalGeralValor, 2, ",", ".") ?></b>
								</td>
								<td colspan="2">
									&nbsp;
								</td>
							</tr>							
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top: 12px">
			<span><input name="Button" type="submit" class="button" id="Submit" title="Processar Compensação" value="Processar Compensação" /></span>
		</td>
	</tr>
</table>
<input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>" />

</form>