<?php
###########
## Módulo de pesquisa para CONTAS A RECEBER de EVENTO
## Criado: - 14/07/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Monta a query para pegar os dados
$sql_eve = "SELECT 
			con.id,
			con.data,
			con.descricao,
			con.nro_documento,
			con.data_vencimento,
			con.valor,
			con.valor_recebido,
			con.situacao,
			con.restricao,
			eve.nome as evento_nome 
			FROM contas_receber con
			INNER JOIN eventos eve ON eve.id = con.pessoa_id
			WHERE (eve.nome LIKE '%$chavePesquisa%' AND con.tipo_pessoa = 5 AND con.empresa_id = '$empresaId') OR (con.nro_documento = '$chavePesquisa' AND con.tipo_pessoa = 5 AND con.empresa_id = '$empresaId') 
			ORDER BY con.data_vencimento DESC";            

//Executa a query
$query_eve = mysql_query($sql_eve);

//Conta o numero de registros da query
$registros_eve = mysql_num_rows($query_eve);

//Caso não houver registros
if ($registros_eve == 0) 
{
	echo "
		<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Evento Social: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
			</tr>
		</table>
		</br>
		</br>";

} 

else 

{
	
	echo "
		<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Evento Social: </span><span class='style1'>A pesquisa retornou $registros_eve resultado(s)</br>
				</td>
			</tr>
			<tr>
				<td>		  
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
						<tr>
							<td width='70' class='listViewThS1'>&nbsp;Nro Docto:</td>
							<td width='436' class='listViewThS1'>&nbsp;&nbsp;Dados do Evento/Descrição da Conta a Receber</td>
							<td width='70' class='listViewThS1'>Emissão</td>
							<td width='70' class='listViewThS1'>Vencimento</td>
							<td width='80' class='listViewThS1' align='center'>Valor</td>
							<td width='65' class='listViewThS1' align='center'>Situação</td>        		
						</tr>";						

	//efetua o loop na pesquisa
	while ($dados_eve = mysql_fetch_array($query_eve))
	{		
		
		//Efetua o switch para o campo de situacao
		switch ($dados_eve['situacao']) 
		{
			case 1: 
			
				$desc_situacao = "Em aberto"; 
				
				//Caso esteja com pagamento parcial
				if ($dados_cli['valor_recebido'] > 0) $desc_situacao = "Rec Parcial"; break;
				
			case 2: $desc_situacao = "Recebido"; break;
		}
		
		//Se o formando estiver com restricoes financeiras, muda a cor da celula
		if ($dados_eve["restricao"] == 2)
		{
		
			$cor_celula = "#F0D9D9";
			
		}
		
		else
		
		{
		
			$cor_celula = "#FFFFFF";
			
		}
	  
	?>

    <tr height="16">	
		<td vbgcolor="<?php echo $cor_celula ?>" align="middle" class="currentTabList" align="center" style="border-top: 1px dotted #aaa">
		    <?php echo $dados_eve[nro_documento] ?>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding: 0px; padding-left: 5px; border-top: 1px dotted #aaa">
		    <a title="Clique para exibir os detalhes desta conta" href="#" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $dados_eve[id] ?>&headers=1','conteudo')"><?php echo $dados_eve[evento_nome] ?></a></br><span style="font-size: 9px"><?php echo $dados_eve["descricao"] ?></span>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_eve[data]) ?>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_eve[data_vencimento]) ?>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" align="center" valign="middle" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo "R$ " . number_format($dados_eve[valor], 2, ",", ".") ?>
		</td>  
		<td bgcolor="<?php echo $cor_celula ?>" align="center" style="border-top: 1px dotted #aaa">
			<?php echo $desc_situacao ?>				
		</td>
	</tr>

	<?php 
		
		//Fecha o while
		}
	
		echo "</table><br />";
		}


//Filtra as contas a receber de FORNECEDORES

//Monta a query para pegar os dados
$sql_for = "SELECT 
			con.id,
			con.data,
			con.descricao,
			con.nro_documento,
			con.data_vencimento,
			con.valor,
			con.valor_recebido,
			con.situacao,
			con.restricao,
			form.nome as formando_nome 
			FROM contas_receber con
			INNER JOIN eventos_formando form ON form.id = con.pessoa_id
			WHERE (form.nome LIKE '%$chavePesquisa%' AND con.tipo_pessoa = 4 AND con.empresa_id = '$empresaId') OR (con.nro_documento = '$chavePesquisa' AND con.tipo_pessoa = 4 AND con.empresa_id = '$empresaId')
			ORDER BY con.data_vencimento DESC";

//Executa a query
$query_for = mysql_query($sql_for);

//Conta o numero de registros da query
$registros_for = mysql_num_rows($query_for);

//Caso não houver registros
if ($registros_for == 0) 
{
	
	echo "
		<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Formaturas: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
			</tr>
		</table>
		</br>
		</br>";

} 

else 

{
	
	echo "
		<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Formaturas: </span><span class='style1'>A pesquisa retornou $registros_for resultado(s)</br>
				</td>
			</tr>
			<tr>
				<td>		  
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
						<tr>
							<td width='70' class='listViewThS1'>&nbsp;Nro Docto:</td>
							<td width='436' class='listViewThS1'>&nbsp;&nbsp;Dados do Formando/Descrição da Conta a Receber</td>
							<td width='70' class='listViewThS1'>Emissão</td>
							<td width='70' class='listViewThS1'>Vencimento</td>
							<td width='80' class='listViewThS1' align='center'>Valor</td>
							<td width='65' class='listViewThS1' align='center'>Situação</td>         		
						</tr>";						
  
  $total = 0;
  
	//efetua o loop na pesquisa
	while ($dados_for = mysql_fetch_array($query_for))
	{		
		
		//Efetua o switch para o campo de situacao
		switch ($dados_for[situacao]) 
		{
	  
			case 1: 
			
				$desc_situacao = "Em aberto";
				
				//Caso esteja com pagamento parcial
				if ($dados_for['valor_recebido'] > 0) $desc_situacao = "Rec Parcial";  break;
				
			case 2: $desc_situacao = "Recebida"; break;
		
		}
		
		//Se o formando estiver com restricoes financeiras, muda a cor da celula
		if ($dados_for["restricao"] == 2)
		{
		
			$cor_celula = "#F0D9D9";
			
		}
		
		else
		
		{
		
			$cor_celula = "#FFFFFF";
			
		}
	
	?>

    <tr height="20">
		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" class="currentTabList" align="center" style="border-top: 1px dotted #aaa">
			<?php echo $dados_for[nro_documento] ?>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" class="oddListRowS1" style="padding: 0px; padding-left: 5px; border-top: 1px dotted #aaa">
		    <a title="Clique para exibir os detalhes desta conta" href="#" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $dados_for[id] ?>&headers=1','conteudo')"><?php echo $dados_for[formando_nome] ?></a></br><span style="font-size: 9px"><?php echo $dados_for["descricao"] ?></span>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_for[data]) ?>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_for[data_vencimento]) ?>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" align="right" valign="middle" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php 
          
				echo "R$ " . number_format($dados_for[valor], 2, ",", ".");
          
				$total = $total + $dados_for[valor];
			?>
		</td>  
		<td bgcolor="<?php echo $cor_celula ?>" align="center" style="border-top: 1px dotted #aaa">
			<?php echo $desc_situacao ?>				
		</td>
	</tr>

	<?php 
		
		//Fecha o while
		}
    
		$total_final = "R$ " . number_format($total, 2, ",", ".");
		echo "<tr>
		<td colspan='4' style='border-top: 1px dashed' height='24'>
		  &nbsp;<b>Total de Registros: <span style='color: #990000'>$registros_for</span></b>
		</td>
		<td style='border-top: 1px dashed' align='right'>
		<b><span style='color: #990000'>$total_final</span></b>
		</td>
		<td style='border-top: 1px dashed'>&nbsp;</td>
	  </tr></table><br />";
	}

    
    ?>    
    </td>
	</tr> 		
</table>
