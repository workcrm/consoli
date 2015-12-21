<?php
###########
## Módulo de pesquisa para CONTAS A RECEBER
## Criado: - 14/07/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Filtra as contas a receber de CLIENTES

//Monta a query para pegar os dados
$sql_cli = "SELECT 
			con.id,
			con.data,
			con.descricao,
			con.nro_documento,
			con.data_vencimento,
			con.valor,
			con.valor_recebido,
			con.situacao,
			con.restricao,
			cli.nome as cliente_nome 
			FROM contas_receber con
			INNER JOIN clientes cli ON cli.id = con.pessoa_id
			WHERE (cli.nome LIKE '%$chavePesquisa%' AND con.tipo_pessoa = 1 AND con.empresa_id = '$empresaId') OR (con.nro_documento = '$chavePesquisa' AND con.tipo_pessoa = 1 AND con.empresa_id = '$empresaId') 
			ORDER BY con.data_vencimento DESC";            

//Executa a query
$query_cli = mysql_query($sql_cli);

//Conta o numero de registros da query
$registros_cli = mysql_num_rows($query_cli);

//Caso não houver registros
if ($registros_cli == 0) 
{
	echo "
		<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Clientes: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
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
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Clientes: </span><span class='style1'>A pesquisa retornou $registros_cli resultado(s)</br>
				</td>
			</tr>
			<tr>
				<td>		  
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
						<tr>
							<td width='70' class='listViewThS1'>&nbsp;Nro Docto:</td>
							<td width='436' class='listViewThS1'>&nbsp;&nbsp;Dados do Cliente/Descrição da Conta a Receber</td>
							<td width='70' class='listViewThS1'>Emissão</td>
							<td width='70' class='listViewThS1'>Vencimento</td>
							<td width='80' class='listViewThS1' align='center'>Valor</td>
							<td width='65' class='listViewThS1' align='center'>Situação</td>        		
						</tr>";						

	//efetua o loop na pesquisa
	while ($dados_cli = mysql_fetch_array($query_cli))
	{		
		
		//Efetua o switch para o campo de situacao
		switch ($dados_cli['situacao']) 
		{
		
			case 1: 
			
				$desc_situacao = "Em aberto"; 
				
				//Caso esteja com pagamento parcial
				if ($dados_cli['valor_recebido'] > 0) $desc_situacao = "Rec Parcial"; break;
				
			break;
				
			case 2: $desc_situacao = "Recebido"; break;
		}
		
		//Se o formando estiver com restricoes financeiras, muda a cor da celula
		if ($dados_cli["restricao"] == 2)
		{
		
			$cor_celula = "#F0D9D9";
			
		}
		
		else
		
		{
		
			$cor_celula = "#FFFFFF";
			
		}
	
	?>

    <tr height="20">	
		<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" align="center" style="border-top: 1px dotted #aaa">
		    <?php echo $dados_cli[nro_documento] ?>
		</td>
		<td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding: 0px; padding-left: 5px; border-top: 1px dotted #aaa">
		    <a title="Clique para exibir os detalhes desta conta" href="#" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $dados_cli[id] ?>&headers=1','conteudo')"><?php echo $dados_cli[cliente_nome] ?></a></br><span style="font-size: 9px"><?php echo $dados_cli["descricao"] ?></span>
		</td>
		<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_cli[data]) ?>
		</td>
		<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_cli[data_vencimento]) ?>
		</td>
		<td align="center" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo "R$ " . number_format($dados_cli[valor], 2, ",", ".") ?>
		</td>  
		<td align="center" style="border-top: 1px dotted #aaa">
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
						forn.nome as fornecedor_nome 
						FROM contas_receber con
						INNER JOIN fornecedores forn ON forn.id = con.pessoa_id
						WHERE (forn.nome LIKE '%$chavePesquisa%' AND con.tipo_pessoa = 2 AND con.empresa_id = '$empresaId') OR (con.nro_documento = '$chavePesquisa' AND con.tipo_pessoa = 2 AND con.empresa_id = '$empresaId')
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
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Fornecedores: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
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
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Fornecedores: </span><span class='style1'>A pesquisa retornou $registros_for resultado(s)</br>
				</td>
			</tr>
			<tr>
				<td>		  
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
						<tr>
							<td width='70' class='listViewThS1'>&nbsp;Nro Docto:</td>
							<td width='436' class='listViewThS1'>&nbsp;&nbsp;Dados do Fornecedor/Descrição da Conta a Receber</td>
							<td width='70' class='listViewThS1'>Emissão</td>
							<td width='70' class='listViewThS1'>Vencimento</td>
							<td width='80' class='listViewThS1' align='center'>Valor</td>
							<td width='65' class='listViewThS1' align='center'>Situação</td>         		
						</tr>";						

	//efetua o loop na pesquisa
	while ($dados_for = mysql_fetch_array($query_for))
	{		
		
		//Efetua o switch para o campo de situacao
		switch ($dados_for[situacao]) 
		{
			case 1: 
				$desc_situacao = "Em aberto"; 
				
				//Caso esteja com pagamento parcial
				if ($dados_for['valor_recebido'] > 0) $desc_situacao = "Rec Parcial"; break;
				
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
		<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" align="center" style="border-top: 1px dotted #aaa">
		    <?php echo $dados_for[nro_documento] ?>
		</td>
		<td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding: 0px; padding-left: 5px; border-top: 1px dotted #aaa">
		    <a title="Clique para exibir os detalhes desta conta" href="#" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $dados_for[id] ?>&headers=1','conteudo')"><?php echo $dados_for[fornecedor_nome] ?></a></br><span style="font-size: 9px"><?php echo $dados_for["descricao"] ?></span>
		</td>
		<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_for[data]) ?>
		</td>
		<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_for[data_vencimento]) ?>
		</td>
		<td align="center" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo "R$ " . number_format($dados_for[valor], 2, ",", ".") ?>
		</td>  
		<td align="center" style="border-top: 1px dotted #aaa">
			<?php echo $desc_situacao ?>				
		</td>
	</tr>

	<?php 
		
		//Fecha o while
		}
		
		echo "</table><br />";
	}

//Filtra as contas a receber de COLABORADORES

//Monta a query para pegar os dados
$sql_col = "SELECT 
			con.id,
			con.data,
			con.descricao,
			con.nro_documento,
			con.data_vencimento,
			con.valor,
			con.valor_recebido,
			con.situacao,
			con.restricao,
			col.nome as colaborador_nome 
			FROM contas_receber con
			INNER JOIN colaboradores col ON col.id = con.pessoa_id
			WHERE (col.nome LIKE '%$chavePesquisa%' AND con.tipo_pessoa = 3 AND con.empresa_id = '$empresaId') OR (con.nro_documento = '$chavePesquisa' AND con.tipo_pessoa = 3 AND con.empresa_id = '$empresaId')
			ORDER BY con.data_vencimento DESC";

//Executa a query
$query_col = mysql_query($sql_col);

//Conta o numero de registros da query
$registros_col = mysql_num_rows($query_col);

//Caso não houver registros
if ($registros_col == 0) 
{
	echo "
		<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Colaboradores: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
			</tr>";

} 

else 

{
	echo "
		<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Contas a Receber de Colaboradores: </span><span class='style1'>A pesquisa retornou $registros_col resultado(s)</br>
				</td>
			</tr>
			<tr>
				<td>		  
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
						<tr>
							<td width='70' class='listViewThS1'>&nbsp;Nro Docto:</td>
							<td width='436' class='listViewThS1'>&nbsp;&nbsp;Dados do Colaborador/Descrição da Conta a Receber</td>
							<td width='70' class='listViewThS1'>Emissão</td>
							<td width='70' class='listViewThS1'>Vencimento</td>
							<td width='80' class='listViewThS1' align='center'>Valor</td>
							<td width='65' class='listViewThS1' align='center'>Situação</td>         		
						</tr>";						

	//efetua o loop na pesquisa
	while ($dados_col = mysql_fetch_array($query_col))
	{		
		
		//Efetua o switch para o campo de situacao
		switch ($dados_col['situacao']) 
		{
			case 1: 
				$desc_situacao = "Em aberto"; 
				
				//Caso esteja com pagamento parcial
				if ($dados_col['valor_recebido'] > 0) $desc_situacao = "Rec Parcial"; break;
				
			case 2: $desc_situacao = "Recebida"; break;
		}
		
		//Se o formando estiver com restricoes financeiras, muda a cor da celula
		if ($dados_col["restricao"] == 2)
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
		    <?php echo $dados_col[nro_documento] ?>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" class="oddListRowS1" style="padding: 0px; padding-left: 5px; border-top: 1px dotted #aaa">
		    <a title="Clique para exibir os detalhes desta conta" href="#" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $dados_col[id] ?>&headers=1','conteudo')"><?php echo $dados_col[colaborador_nome] ?></a></br><span style="font-size: 9px"><?php echo $dados_col['descricao'] ?></span>
		</td>

		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_col[data]) ?>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" valign="middle" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo DataMySQLRetornar($dados_col[data_vencimento]) ?>
		</td>
		<td bgcolor="<?php echo $cor_celula ?>" align="center" valign="middle" class="currentTabList" style="border-top: 1px dotted #aaa">
		    <?php echo "R$ " . number_format($dados_col[valor], 2, ",", ".") ?>
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
    
    ?>    
    </td>
	</tr>	 		
</table>
