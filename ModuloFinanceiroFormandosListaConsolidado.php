fal<?php
###########
## Módulo para Listagem da posição financeira dos formandos de um evento consolidado por turma
## Criado: 05/03/2010 - Maycon Edinger
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

//Captura o evento informado
$edtEventoId = $_GET["EventoId"];
$edtContaCaixaId = $_GET["ContaCaixaId"];

//Busca o nome do evento
//Monta o sql
$sql_evento = mysql_query("SELECT 
														id, 
														nome, 
														valor_geral_evento,
														valor_desconto_evento,
														alunos_colacao,
														alunos_baile
													FROM 
														eventos 
													WHERE 
														id = $edtEventoId");

//Monta o array com os dados
$dados_evento = mysql_fetch_array($sql_evento);

if ($edtContaCaixaId > 0)
{

	$sql_conta_caixa = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = $edtContaCaixaId");

	//Monta o array com os dados
	$dados_conta_caixa = mysql_fetch_array($sql_conta_caixa);

	$TextoContaCaixa = "<br/>Conta-caixa: <b>($edtContaCaixaId) - " . $dados_conta_caixa["nome"] . "</b><br/>";
	$where_conta_caixa = "AND rec.subgrupo_conta_id = $edtContaCaixaId";

}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css" />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="padding-bottom: 4px;">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Evento: <span style="color: #990000;">[<?php echo $dados_evento["id"] . ']<br/>' . $dados_evento["nome"] ?></span></span>			  	
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 10px;">
						Consulta: <span style=1color: #990000;><strong>POR POSIÇÃO FINANCEIRA DOS FORMANDOS</strong></span>
						<?php echo $TextoContaCaixa ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
  
<?php


//Monta o sql de filtragem das contas
$sql = "SELECT 
					rec.id,			
					rec.pessoa_id,			
					eve.nome AS evento_nome,
					form.nome AS formando_nome,
					form.chk_culto,
					form.chk_colacao,
					form.chk_jantar,
					form.chk_baile
				FROM 
					contas_receber rec
				LEFT OUTER JOIN 
					eventos eve ON eve.id = rec.evento_id
				LEFT OUTER JOIN 
					eventos_formando form ON form.id = rec.pessoa_id
				WHERE 
					rec.empresa_id = $empresaId 
				AND 
					rec.evento_id = $edtEventoId
				AND 
					rec.formando_id > 0
					$where_conta_caixa
				GROUP BY 
					formando_nome
				ORDER BY 
					formando_nome";   
		
$query = mysql_query($sql);

$registros = mysql_num_rows($query);


//Caso não encontrar contas
if ($registros == 0)
{

	?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			  Nenhuma conta a receber encontrada !
			</td>
		</tr>
	</table>
	<?php
	
}
	
else

{
	
	?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">	
		<tr>
      <td>
        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
					<tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">                
		      	<td style="padding-left: 4px">Dados do Sacado/Evento/Formando</td>
						<td width="36" align="center" style="border-left: #aaa 1px dotted">Part.</td>
						<td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Contrato</td>
						<td width="36" align="center" style="border-left: #aaa 1px dotted">Parc.</td>
						<td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Parcela</td>
						<td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Recebido</td>
						<td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">A Receber</td>
						<td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Em Atraso</td>          
          </tr>
					<?php
				
						//Percorre as contas
						while ($dados = mysql_fetch_array($query))
						{
						
							$desc_participante = "&nbsp;";
						
							if ($dados["chk_culto"] == 1)
							{
							
								$desc_participante .= "<span title='Formando Participa do Culto'>M</span>&nbsp;";
								$conta_culto++;
								
							}
							
							if ($dados["chk_colacao"] == 1)
							{
							
								$desc_participante .= "<span title='Formando Participa da Colação'>C</span>&nbsp;";
								$conta_colacao++;
								
							}
							
							if ($dados["chk_jantar"] == 1)
							{
							
								$desc_participante .= "<span title='Formando Participa do Jantar'>J</span>&nbsp;";
								$conta_jantar++;
								
							}
							
							if ($dados["chk_baile"] == 1)
							{
							
								$desc_participante .= "<span title='Formando Participa do Baile'>B</span>";
								$conta_baile++;
								
							}	
						
							$FormandoId = $dados["pessoa_id"];
							
							//Pega o numero de parcelas e o valor
							//Monta o sql de filtragem das contas
							$sql = "SELECT 
												count(1) AS total_parcelas,
												valor_original AS valor_parcela
											FROM 
												contas_receber rec
											WHERE 
												empresa_id = $empresaId 
											AND 
												pessoa_id = $FormandoId
												$where_conta_caixa
											GROUP BY 
												pessoa_id";   
								
							$query_parcela = mysql_query($sql);

							$registros_parcela = mysql_num_rows($query_parcela);
							
							//Verifica se possuem registros
							if ($registros_parcela > 0)
							{
							
								//Percorre as contas
								while ($dados_parcela = mysql_fetch_array($query_parcela))
								{
								
									$numero_parcelas = $dados_parcela["total_parcelas"];
									$valor_parcela = $dados_parcela["valor_parcela"];
									
								}

							}
							
							$hoje = date('Y-m-d', mktime());
	    
							$TextoSituacao = " ";
			
							//Pega o numero contas atrasadas
							//Monta o sql de filtragem das contas
							$sql = "SELECT 
	                      SUM(valor_original) AS total_atraso								
                      FROM 
                      	contas_receber rec
                      WHERE 
                      	empresa_id = $empresaId AND pessoa_id = $FormandoId
                      AND 
                      	situacao = 1 
                    	AND 
                    		data_vencimento < '$hoje'
	                      $where_conta_caixa";   
                                                
                                                		
							$query_atraso = mysql_query($sql);

							$registros_atraso = mysql_num_rows($query_atraso);
							
							//Verifica se possuem registros
							if ($registros_atraso > 0)
							{
						
                //Percorre as contas
                while ($dados_atraso = mysql_fetch_array($query_atraso))
                {

                  $valor_atraso = $dados_atraso["total_atraso"];

                  $geral_atraso = $geral_atraso + $dados_atraso["total_atraso"];

                }

							}
						
							//Monta o sql de filtragem das contas
							$sql = "SELECT 
                        rec.id,
                        SUM(rec.valor_original) AS total_valor_original,
                        SUM(rec.valor) AS total_valor,
                        SUM(rec.valor_recebido) AS total_recebido,
                        SUM(rec.valor_boleto) AS total_boleto,
                        SUM(rec.valor_multa_juros) AS total_multa,								
                        rec.pessoa_id								
                      FROM 
                      	contas_receber rec
                      WHERE 
                      	rec.empresa_id = $empresaId 
                    	AND 
                    		rec.pessoa_id = $FormandoId
                        $where_conta_caixa
                      GROUP BY 
                      	rec.pessoa_id";   
								
							$query_formando = mysql_query($sql);

							$registros_formando = mysql_num_rows($query_formando);
							
							//Verifica se possuem registros
							if ($registros_formando > 0)
							{
							
								//Percorre as contas
								while ($dados_formando = mysql_fetch_array($query_formando))
								{
							
									$geral_contrato = $geral_contrato + $dados_formando["total_valor_original"];
									$geral_recebido = $geral_recebido + $dados_formando["total_recebido"];
									$geral_receber = $geral_receber + ($dados_formando["total_valor"] - $dados_formando["total_recebido"]);
									
									$total_receber = $dados_formando["total_valor"] - $dados_formando["total_recebido"];
						
									?>
									<tr height="22">                
										<td style="padding-left: 4px; border-bottom: #aaa 1px dotted"><b><?php echo $dados["formando_nome"] ?></b></td>
										<td align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted"><?php echo $desc_participante ?></td>
										<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($dados_formando["total_valor_original"],2,",",".") ?></td>
										<td align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted"><?php echo $numero_parcelas ?></td>
										<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($valor_parcela,2,",",".") ?></td>
										<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($dados_formando["total_recebido"],2,",",".") ?></td>
										<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($total_receber,2,",",".") ?></td>
										<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($valor_atraso,2,",",".") ?></td>								
									</tr>
									<?php
						
								}
							
							}					
					
						}
					
						//Imprime os totais
						?>
						<tr height="22">                
							<td colspan="2" align="right" style="padding-left: 4px; border-bottom: #aaa 1px dotted"><b>TOTAL:&nbsp;</b></td>
							<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_contrato,2,",",".") ?></b></td>
							<td align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted">&nbsp;</td>
							<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted">&nbsp;</td>
							<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_recebido,2,",",".") ?></b></td>
							<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_receber,2,",",".") ?></b></td>
							<td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_atraso,2,",",".") ?></b></td>						
						</tr>			
					</table>
				</td>
			</tr>
		</table>
		<br/>
		<?php

			//Fechamento dos totais atualizados de produtos e serviços
			//Produto do Culto
			$lista_total_culto =  mysql_query("SELECT SUM(quantidade * valor_venda) AS total_culto FROM eventos_item WHERE evento_id = $edtEventoId AND chk_culto = 1");

			$registros_culto = mysql_num_rows($lista_total_culto);

			if ($registros_culto > 0)
			{

				$dados_culto = mysql_fetch_array($lista_total_culto);
				$valor_total_culto = $dados_culto['total_culto'];

			}


			//Fechamento dos totais atualizados de produtos e serviços
			//Produto do Baile
			$lista_total_baile =  mysql_query("SELECT SUM(quantidade * valor_venda) AS total_baile FROM eventos_item WHERE evento_id = $edtEventoId AND chk_baile = 1");

			$registros_baile = mysql_num_rows($lista_total_baile);

			if ($registros_baile > 0)
			{

				$dados_baile = mysql_fetch_array($lista_total_baile);
				$valor_total_baile = $dados_baile['total_baile'];

			}


			//Fechamento dos totais atualizados de produtos e serviços
			//Produto do Janta
			$lista_total_jantar =  mysql_query("SELECT SUM(quantidade * valor_venda) AS total_jantar FROM eventos_item WHERE evento_id = $edtEventoId AND chk_jantar = 1");

			$registros_jantar = mysql_num_rows($lista_total_jantar);

			if ($registros_jantar > 0)
			{

				$dados_jantar = mysql_fetch_array($lista_total_jantar);
				$valor_total_jantar = $dados_jantar['total_jantar'];

			}


			//Fechamento dos totais atualizados de produtos e serviços
			//Produto do Colação
			$lista_total_colacao =  mysql_query("SELECT SUM(quantidade * valor_venda) AS total_colacao FROM eventos_item WHERE evento_id = $edtEventoId AND chk_colacao = 1");

			$registros_colacao = mysql_num_rows($lista_total_colacao);

			if ($registros_colacao > 0)
			{

				$dados_colacao = mysql_fetch_array($lista_total_colacao);
				$valor_total_colacao = $dados_colacao['total_colacao'];

			}

			$total_geral_produtos = $valor_total_colacao + $valor_total_jantar + $valor_total_baile + $valor_total_culto;

		?>
		<table width="350" border="0" cellpadding="0" cellspacing="0" class="listView">	
			<tr>
        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px; border-bottom: 1px solid">
					Valor Orçado do Evento R$: 
				</td>
				<td style="border-left: 1px solid; border-bottom: 1px solid; padding-right: 8px" align="right">
					<b><?php echo number_format($dados_evento["valor_geral_evento"], 2, ",", ".") ?></b>
				</td>
			</tr>
			<tr>
        <td class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px; border-bottom: 1px solid">
          Valor Orçamento Atual R$: 
        </td>
        <td style="border-left: 1px solid; border-bottom: 1px solid; padding-right: 8px" align="right">
          <b><?php echo number_format($total_geral_produtos - $dados_evento['valor_desconto_evento'], 2, ",", ".") ?></b>
        </td>
      </tr>
			<tr>
        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px;">
					Valor em Contratos Individuais R$: 
				</td>
				<td style="border-left: 1px solid; border-bottom: 1px solid; padding-right: 8px" align="right">
					<b><?php echo number_format($geral_contrato, 2, ",", ".") ?></b>
				</td>
			</tr>
			<tr>
        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px;">
					<?php
					
						$diferenca_total = $geral_contrato - ($total_geral_produtos - $dados_evento['valor_desconto_evento']);
						@$diferenca_percentual = ($diferenca_total / $total_geral_produtos) * 100;
									
					?>
					Previsão de Débito/Crédito R$: 
				</td>
				<td style="border-left: 1px solid; padding-right: 8px" align="right">
					<span style="color: #990000"><b><?php echo number_format($diferenca_total, 2, ",", ".") . ' (' .  number_format($diferenca_percentual, 0, ",", ".") . '%)' ?></b></span>
				</td>
			</tr>
		</table>
		<br/>
		<?php

			//Busca os formandos em colacao e baile
			//Monta o sql
			// $sql_evento = mysql_query("SELECT 
			// 														count(chk_colacao) AS total_colacao,
			// 														count(chk_baile) AS total_baile
			// 													FROM 
			// 														eventos_formando 
			// 													WHERE 
			// 														evento_id = $edtEventoId");

			// //Monta o array com os dados
			// $dados_fechamento = mysql_fetch_array($sql_evento);

			?>
		<table width="350" border="0" cellpadding="0" cellspacing="0" class="listView">	
			<tr>
        <td colspan="2" height="22" align="right">
          <span style="font-size: 12px"><b>ALUNOS COLAÇÃO - INICIAL:</b></span>
        </td>
        <td style="padding-right: 5px;" align="right">
          <span style="font-size: 12px"><b>
            <?php echo $dados_evento[alunos_colacao] ?>
          </b></span>
        </td>
      </tr>
      <tr>
        <td colspan="2" height="22" align="right">
          <span style="font-size: 12px"><b>ALUNOS COLACAO - ATUAL:</b></span>
        </td>
        <td style="padding-right: 5px;" align="right">
          <span style="font-size: 12px">
          	<b><?php echo $conta_colacao ?></b>
        	</span>
        </td>
      </tr>
      <tr>
        <td colspan="2" height="22" align="right">
          <span style="font-size: 12px"><b>ALUNOS BAILE - INICIAL:</b></span>
        </td>
        <td style="padding-right: 5px;" align="right">
          <span style="font-size: 12px"><b>
            <?php echo $dados_evento[alunos_baile] ?>
          </b></span>
        </td>
      </tr>
      <tr>
        <td colspan="2" height="22" align="right">
          <span style="font-size: 12px"><b>ALUNOS BAILE - ATUAL:</b></span>
        </td>
        <td style="padding-right: 5px;" align="right">
          <span style="font-size: 12px">
          	<b><?php echo $conta_baile ?></b>
        	</span>
        </td>
      </tr>
    </table>
		<?php
	
	}

?>