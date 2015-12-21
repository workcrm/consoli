<?php
###########
## Módulo de pesquisa para BOLETOS
## Criado: - 05/04/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Monta a query para pegar os dados
$sql_cli = "SELECT * FROM boleto WHERE nosso_numero = '$chavePesquisa' ORDER BY sacado";

//Executa a query
$query_cli = mysql_query($sql_cli);

//Conta o numero de registros da query
$registros_cli = mysql_num_rows($query_cli);

//Caso não houver registros
if ($registros_cli == 0) 
{
	
	echo "<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Boletos: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
			</tr>
		</table>";

} 

else 

{
	
	echo "<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'>
					<span class='TituloModulo'>Boletos: </span><span class='style1'>A pesquisa retornou $registros_cli resultado(s)</br>
				</td>
			</tr>
			<tr>
				<td>		  
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
						<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
							<td width='116' align='center'>Nosso Número</td>
							<td>Dados do Sacado/Evento/Formando</td>
							<td width='60' align='center'>Emissão</td>
							<td width='60' align='center'>Vencto</td>
							<td width='80' align='right'>Valor</td>
							<td width='65' align='center'>Situação</td>
							<td width='40' align='center' style='padding-right: 0px'>Ação</td>        		
						</tr>";						

	//efetua o loop na pesquisa
	while ($dados_rec = mysql_fetch_array($query_cli))
	{		
		
		//Verifica a situação do boleto
		switch ($dados_rec["boleto_recebido"]) 
		{
  	  
			case 0: $desc_situacao = "<span style='color: #990000'>Em Aberto</span>"; break;		  
			case 1: $desc_situacao = "<span style='color: #6666CC'>Recebido</span>"; break;
      
		}
		
		?>

		<tr height="16">
			<td style="border-bottom: 1px solid" align="center">
				<span style="color: #6666CC"><?php echo substr($dados_rec["nosso_numero"], 0,7) ?></span><span style="color: #990000"><?php echo substr($dados_rec["nosso_numero"], 7,3) ?></span><span style="color: #59AA08"><?php echo substr($dados_rec["nosso_numero"], 10,5) ?></span><?php echo substr($dados_rec["nosso_numero"], 15,2) ?>				
			</td>      			
			<td style="border-bottom: 1px solid" height="20">
				<font color="#CC3300" size="2" face="Tahoma">
				  <a title="Clique para exibir este boleto" href="#" onclick="abreJanelaBoleto('./boletos/boleto_bb.php?TipoBol=1&BoletoId=<?php echo $dados_rec[id] ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')"><?php echo $dados_rec["sacado"]; ?></a>
				</font>
				<br/>
				<?php echo "<span style='color: #990000'><b>$dados_rec[demonstrativo2]</b></span><br/>$dados_rec[demonstrativo3]" ?>
				</span>      
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo DataMySQLRetornar($dados_rec["data_documento"]) ?>				
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<span style="color: #6666CC"><?php echo DataMySQLRetornar($dados_rec["data_vencimento"]) ?></span>			
			</td>			
			<td style="border-bottom: 1px solid" align="right">
				<span onclick="wdCarregarFormulario('ContaReceberAltera.php?Id=<?php echo $dados_rec[conta_receber_id] ?>&headers=1','conteudo')">
				<?php 
					echo "R$ " . number_format($dados_rec["valor_boleto"], 2, ",", ".");
					$total_receber = $total_receber + $dados_rec["valor_boleto"]; 
				?>
				</span>
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo $desc_situacao ?>				
			</td>
			<td width="32" style="border-bottom: 1px solid" align="center">
				<?php

					if ($dados_rec["boleto_recebido"] == 0) 
					{
				
				?>
				<img src="image/bt_receber_gd.gif" title="Baixar o recebimento deste boleto" onclick="wdCarregarFormulario('BoletoQuita.php?BoletoId=<?php echo $dados_rec[id] ?>&headers=1','conteudo')" style="cursor: pointer" />					
				<?php
					
					} 
					
					else 
					
					{
					 
						echo "&nbsp;";
           
					}
				?>			
			</td>
		</tr>

		<?php 
			
			//Fecha o while
			}
			
			echo "</table><br />";
			}

		?> 
	</tr>	 		
</table>