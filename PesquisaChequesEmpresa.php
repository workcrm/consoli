<?php
###########
## Módulo de pesquisa para Cheques da Empresa
## Criado: - 05/04/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########


//Monta a query para pegar os dados
$sql_che = "SELECT
			che.id,
			che.conta_corrente_id,
			che.numero_cheque,
			che.data_emissao,
			che.pre_datado,
			che.bom_para,
			che.valor,
			che.status,
			che.conta_pagar_id,
			che.data_compensacao,
			cont.nome AS conta_corrente_nome,
			cont.agencia,
			cont.conta,
			cpag.descricao AS conta_pagar_nome
			FROM cheques_empresa che
			LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
			LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
			WHERE che.numero_cheque LIKE '%$chavePesquisa%' 
			OR cpag.descricao LIKE '%$chavePesquisa%' 
			ORDER BY conta_corrente_nome, che.data_emissao";

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
				<span class='TituloModulo'>Cheques da Empresa: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span>
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
				<span class='TituloModulo'>Cheques da Empresa: </span><span class='style1'>A pesquisa retornou $registros_che resultado(s)</br>
			</td>
		</tr>
		<tr>
			<td>		  
				<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
					<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
						<td width='70' align='center'>Nr Cheque</td>
						<td>Dados da Conta</td>
						<td width='60' align='right' style='padding-right: 4px'>Valor</td>
						<td width='80' align='center'>Emissão</td>
						<td width='80' align='center'>Bom para</td>
						<td width='90' align='center'>Situação</td> 						
					</tr>";						

	//efetua o loop na pesquisa
	while ($dados_rec = mysql_fetch_array($query_che))
	{		
		
		//Verifica a situação do boleto
		switch ($dados_rec["status"]) 
		{
		  
			case 1: 
				$desc_status = "<span style='color: #990000'>Emitido</span>"; 
			break;		  
			case 2: 
				$desc_status = "<span style='color: #6666CC'>Compensado</span>"; 
			break;
		  
		}
		
	?>

    <tr height="16" onclick="wdCarregarFormulario('ChequeEmpresaExibe.php?ChequeId=<?php echo $dados_rec[id] ?>','conteudo')" style="cursor: pointer" >
		<td style="border-bottom: 1px solid" align="center">
			<a title="Clique para exibir os detalhes deste cheque da Empresa" href="#" onclick="wdCarregarFormulario('ChequeEmpresaExibe.php?ChequeId=<?php echo $dados_rec[id] ?>','conteudo')"><?php echo $dados_rec[numero_cheque] ?></a>			
		</td>      			
		<td style="border-bottom: 1px solid" height="20">
			<?php echo $dados_rec["conta_pagar_nome"] ?>	
		</td>
		<td style="border-bottom: 1px solid" align="right" style='padding-right: 4px'>
			<?php echo number_format($dados_rec["valor"], 2, ",", ".") ?>				
		</td>
		<td style="border-bottom: 1px solid" align="center">
			<?php echo DataMySQLRetornar($dados_rec["data_emissao"]) ?>			
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
