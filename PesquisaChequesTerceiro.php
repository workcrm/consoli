<?php
###########
## Módulo de pesquisa para Cheques de terceiro
## Criado: - 05/04/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########


//Monta a query para pegar os dados
$sql_che = "SELECT
			che.id,
			che.numero_cheque,
			che.bom_para,
			che.data_recebimento,
			che.favorecido,
			che.agencia,
			che.pre_datado,
			che.valor,
			che.valor_utilizado,
			che.conta,
			che.status,
			che.disposicao,
			ban.nome as banco_nome			
			FROM cheques che 
			LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
			WHERE che.numero_cheque LIKE '%$chavePesquisa%' 
			OR che.favorecido LIKE '%$chavePesquisa%' 
			ORDER BY che.favorecido";

//Executa a query
$query_che = mysql_query($sql_che);

//Conta o numero de registros da query
$registros_che = mysql_num_rows($query_che);

//Caso não houver registros
if ($registros_che == 0) 
{
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
		<tr>
			<td valign='middle'>
				<span class='TituloModulo'>Cheques de Terceiro: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span>
			</td>
		</tr>
	</table>";
}
 
else 

{
	
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
		<tr>
			<td valign='middle'>
				<span class='TituloModulo'>Cheques de Terceiro: </span><span class='style1'>A pesquisa retornou $registros_che resultado(s)</br>
			</td>
		</tr>
		<tr>
			<td>		  
				<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
					<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
						<td width='70' align='center'>Nr Cheque</td>
						<td>Titular</td>
						<td width='60' align='right' style='padding-right: 4px'>Valor</td>
						<td width='90' align='center'>Banco</td>
						<td width='60' align='center'>Agencia</td>
						<td width='60' align='center'>C/C</td>
						<td width='66' align='center'>Recebido</td>
						<td width='66' align='center'>Bom Para</td>
						<td width='90' align='center'>Status</td> 						
					</tr>";						

	//efetua o loop na pesquisa
	while ($dados_rec = mysql_fetch_array($query_che))
	{		
		
		//Verifica a situação do boleto
		switch ($dados_rec["status"]) 
		{
		  
			case 1: 
				$desc_status = "<span style='color: #990000'>Recebido</span>"; 
			break;		  
			case 2: 
				$desc_status = "<span style='color: #6666CC'>Compensado</span>"; 
			break;
			case 3: 
				$desc_status = "<span style='color: #6666CC'>Devolvido</span>"; 
				
				//Verifica a situação do boleto
				switch ($dados_rec["disposicao"]) 
				{
				  
					//Se for 1 
					case 1:
						$desc_status .= "<br/>Prim. Contato";				
					break;		
					//Se for 2
					case 2:
						$desc_status .= "<br/>Em Negociação";
					break;	
					//Se for 3
					case 3:
						$desc_status .= "<br/>Reapresentado";
					break;		
					//Se for 4
					case 4:
						$desc_status .= "<br/>Pago";
					break;
					//Se for 5
					case 5:
						$desc_status .= "<br/>Para Registrar";			
					break;
					//Se for 6 
					case 6:
						$desc_status .= "<br/>No SPC";			
					break;
					//Se for 7 
					case 7:
						$desc_status .= "<br/>Não Pode SPC";			
					break;
					//Se for 8 
					case 8:
						$desc_status .= "<br/>SPC Pago";			
					break;
					//Se for 9 
					case 9:
						$desc_status .= "<br/>Dev. Titular";			
					break;
					//Se for 10
					case 10:
						$desc_status .= "<br/>Cobr Judicial";			
					break;
					case 11:
						$desc_status .= "<br/>ACC";			
					break;
				  
				}
				
			break;
		  
		}
		
	?>

    <tr height="16" onclick="wdCarregarFormulario('ChequeTerceiroExibe.php?ChequeId=<?php echo $dados_rec[id] ?>','conteudo')" style="cursor: pointer" >
		<td style="border-bottom: 1px solid" align="center">
			<a title="Clique para exibir os detalhes deste cheque de terceiro" href="#" onclick="wdCarregarFormulario('ChequeTerceiroExibe.php?ChequeId=<?php echo $dados_rec[id] ?>','conteudo')"><?php echo $dados_rec[numero_cheque] ?></a>			
		</td>      			
		<td style="border-bottom: 1px solid" height="20">
			<?php echo $dados_rec["favorecido"] ?>	
		</td>
		<td style="border-bottom: 1px solid" align="right" style='padding-right: 4px'>
			<?php echo number_format($dados_rec["valor"], 2, ",", ".") ?>				
		</td>
		<td style="border-bottom: 1px solid" align="center">
			<?php echo $dados_rec["banco_nome"] ?>			
		</td>
		<td style="border-bottom: 1px solid" align="center">
			<?php echo $dados_rec["agencia"] ?>			
		</td>
		<td style="border-bottom: 1px solid" align="center">
			<?php echo $dados_rec["conta"] ?>			
		</td>
		<td style="border-bottom: 1px solid" align="center">
			<?php echo DataMySQLRetornar($dados_rec["data_recebimento"]) ?>			
		</td>
		<td style="border-bottom: 1px solid" align="center">
			<?php echo DataMySQLRetornar($dados_rec["bom_para"]) ?>			
		</td>
		<td style="border-bottom: 1px solid" align="center">
			<?php echo $desc_status ?>			
		</td>		
	</tr>

	<?php 
		
		//Fecha o while
		}
	
	echo "</table><br /></td></tr></table>";
	
	}

?> 
