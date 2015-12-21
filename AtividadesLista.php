<?php 
###########
## Módulo para Listagem das atividades no form principal
## Criado: 10/05/2012 - Maycon Edinger
## Alterado:
## Alterações: 
###########

//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Alimenta as variáveis com a data atual
$id = "0";
$data_hoje = date("Y-m-d",mktime());

//Monta e executa a query para buscar os compromissos da data atual do usuario
$sql = mysql_query("SELECT 
					ati.id,
					ati.evento_id,
					ati.atividade_id,
					ati.data_prazo,
					ati.data_execucao,
					ati.status,
					ati.obs,
					ati.usuario_execucao,
					atividade.descricao AS atividade_nome,
					atividade.dias,
					CONCAT(usu.nome, ' ', usu.sobrenome) AS usuario_nome,
					eve.nome AS evento_nome,
					eve.data_realizacao
					FROM eventos_atividade ati 
					LEFT OUTER JOIN eventos eve ON eve.id = ati.evento_id
					LEFT OUTER JOIN atividades atividade ON atividade.id = ati.atividade_id
					LEFT OUTER JOIN usuarios usu ON usu.usuario_id = ati.usuario_execucao
					WHERE ati.status = 0
					AND eve.status < 3
					ORDER BY eve.data_realizacao, ati.evento_id, ati.data_prazo");

//Conta o numero de compromissos que a query retornou
$registros = mysql_num_rows($sql);

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{
	
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";
	
}

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440">
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Atividades Pendentes para Eventos</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">
				<tr height="12">
					<td height="12" colspan="5" class="listViewPaginationTdS1">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="40">
									<img src="image/bt_atividades.png" />
								</td>
								<td>
									<?php 
									
										if ($registros == 0) 
										{
											
											$str_titulo = "atividades";	
											$mensagem_regs = "Não há ";
										
										} 
										
										else 
										
										{
											
											if ($registros > 1) 
											{
												
												$str_titulo = "atividades";
											
											} 
											
											else 
											
											{
												
												$str_titulo = "atividade";
											
											}
											
											$mensagem_regs = "Há <span style='color: #990000'>$registros </span>";
										} 
									
									?>
									<span style="font-size: 12px; color: #444444"><b><?php echo $mensagem_regs . " " . $str_titulo ?></b></span></span>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<?php
    
					//Caso não tenha compromissos então não exibe a linha de cabeçalho.
					if ($registros > 0) 
					{ 
      
						//Define o style para fechar com a parte dos eventos para 7 dias
						$style_tabela = "style='border-top: 1px #9E9E9E solid'";
						
						echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
								<td width='580' class='listViewThS1' style='padding-left: 30px'>&nbsp;&nbsp;Evento/Atividade</td>
								<td align='center' class='listViewThS1'>Prazo</td>
							</tr>";
					}
					
					$quebra = 0;
		
					//Monta e percorre o array dos dados
					while ($dados = mysql_fetch_array($sql))
					{
			
						$data_hoje = date("Y-m-d", mktime());
						
						//Verifica se a atividade esta em atraso
						if ($dados["status"] == 0 AND $dados["data_prazo"] < $data_hoje)
						{
						
							$cor_celula = "#F0D9D9";
							
						}
			
						else
						
						{
						
							$cor_celula = "#fdfdfd";
							
						}
						
						//Verifica se a atividade esta concluida
						if ($dados["status"] == 1)
						{
						
							$cor_celula = "#99FF99";
							
						}

						//Verifica se eh necessario a quebra da pagina
						if ($quebra != $dados["evento_id"])
						{
						
							$evento_data = DataMySQLRetornar($dados[data_realizacao]);
							
							$evento_lista = $dados["evento_id"];
							
							if ($quebra >= 0) echo "</table></div></td></tr>";
							
							echo "<tr class='oddListRowS1' height='20' background='image/fundo_consulta.gif'>
										<td colspan='5' style='padding-top:5px; border: 1px #aaa solid'>
											&nbsp;&nbsp;<span class='TituloModulo'>($dados[evento_id]) - $dados[evento_nome]</span>
											<br/>
											<table width='100%' border='0' cellspacing='0' cellpadding='0'>
												<tr>
													<td width='80' style='padding-left: 20px'>
														<b>PENDÊNCIAS:</b>
													</td>
													<td>
														<div id='expande_evento_$evento_lista' style='display: inline;'><img src='image/bt_expande.png' width='46' height='14' title='Expande as Atividades para este evento' onmousedown='document.getElementById(\"expande_evento_$evento_lista\").style.display = \"none\"; document.getElementById(\"oculta_evento_$evento_lista\").style.display = \"inline\"; toggleSlide(\"div_evento_$evento_lista\");' style='cursor: pointer;'/></div><div id='oculta_evento_$evento_lista' style='display: none;'><img src='image/bt_comprime.png' width='55' height='14' title='Oculta as atividades para este evento' onmousedown='document.getElementById(\"expande_evento_$evento_lista\").style.display = \"inline\"; document.getElementById(\"oculta_evento_$evento_lista\").style.display = \"none\"; toggleSlide(\"div_evento_$evento_lista\");' style='cursor: pointer;'/></div>
													</td>
												</tr>
											</table>
											</td>
									</tr>
									<tr>
									<td colspan='5' style='border: 1px #aaa solid'>
										<div id='div_evento_$evento_lista' style='display:none; overflow:hidden; height:185px;'>
										<table width='100%' border='0' cellspacing='0' cellpadding='0'>
									";
						}
			
					?>
								
					<tr height="16">	
						<td width="20" valign="middle" bgcolor="<?php echo $cor_celula ?>" class="oddListRowS1" style="border-top: 1px dotted #aaa; padding-top: 2px">
							<img src="image/atividade_pq.gif" />
						</td>
						<td width="560" valign="middle" bgcolor="<?php echo $cor_celula ?>" class="oddListRowS1" style="border-top: 1px dotted #aaa; padding-top: 2px">
							<?php echo $dados[atividade_nome] . '&nbsp;</span></b>(' . $dados[dias] . ' Dias)' ?>
						</td>
						<td align="center" valign="middle" bgcolor="<?php echo $cor_celula ?>" class="currentTabList" style="border-top: 1px dotted #aaa; padding-top: 2px">
							<?php echo DataMySQLRetornar($dados[data_prazo]); ?> 
						</td>
					</tr>

					<?php
			  
						$quebra = $dados[evento_id];
						
						//Fecha o while
						}
						
						//echo "</table></div></td></tr>";
				
				?>
			</table>
		</td>
	</tr>
</table>
