<?php
###########
## Módulo de pesquisa para CONTAS A PAGAR
## Criado: - 14/07/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo de pesquisa para CONTAS A PAGAR
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Filtra as contas a pagar de CLIENTES

//Monta a query para pegar os dados
$sql_cli = "SELECT 
						con.id,
						con.data,
						con.descricao,
						con.data_vencimento,
						con.valor,
						con.situacao,
						cli.nome as cliente_nome 
						FROM contas_pagar con
						INNER JOIN clientes cli ON cli.id = con.pessoa_id
						WHERE cli.nome LIKE '%$chavePesquisa%'
						AND con.tipo_pessoa = 1 
						AND con.empresa_id = '$empresaId' 
						ORDER BY con.data_vencimento DESC";

//Executa a query
$query_cli = mysql_query($sql_cli);

//Conta o numero de registros da query
$registros_cli = mysql_num_rows($query_cli);

//Caso não houver registros
if ($registros_cli == 0) {
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
		<tr>
      <td valign='middle'><span class='TituloModulo'>Contas a Pagar de Clientes: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
    </tr>
	</table></br></br>";
} else {
	echo "
  <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
      <td valign='middle'><span class='TituloModulo'>Contas a Pagar de Clientes: </span><span class='style1'>A pesquisa retornou $registros_cli resultado(s)</br>
		  </td>
    </tr>
		<tr>
      <td>		  
	      <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
		 	    <tr>
       		  <td width='436' class='listViewThS1'>&nbsp;&nbsp;Dados do Cliente/Descrição da Conta a Pagar</td>
          	<td width='70' class='listViewThS1'>Emissão</td>
          	<td width='70' class='listViewThS1'>Vencimento</td>
          	<td width='80' class='listViewThS1' align='center'>Valor</td>
          	<td width='65' class='listViewThS1' align='center'>Situação</td>
						<td class='listViewThS1'>&nbsp;</td>         		
	  		  </tr>  		  
		  ";						

	//efetua o loop na pesquisa
	while ($dados_cli = mysql_fetch_array($query_cli)){		
		
	//Efetua o switch para o campo de situacao
	switch ($dados_cli[situacao]) {
	  case 1: $desc_situacao = "Em aberto"; break;
		case 2: $desc_situacao = "Pago"; break;
	}
	  ?>

    <tr height='16'>	
      <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style="padding: 0px; padding-left: 5px">
		    <a title="Clique para exibir os detalhes desta conta" href="#" onClick="wdCarregarFormulario('ContaPagarExibe.php?ContaId=<?php echo $dados_cli[id] ?>&headers=1','conteudo')"><?php echo $dados_cli[cliente_nome] ?></a></br><span style="font-size: 9px"><?php echo $dados_cli['descricao'] ?></span>
		  </td>

      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo DataMySQLRetornar($dados_cli[data]) ?>
		  </td>

      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo DataMySQLRetornar($dados_cli[data_vencimento]) ?>
		  </td>

      <td align='center' valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo "R$ " . number_format($dados_cli[valor], 2, ",", ".") ?>
		  </td>
		  
		  <td align='center'>
				<?php echo $desc_situacao ?>				
			</td>
		
			<td>
				<?php 
					if ($desc_situacao == "Em aberto" && $nivelAcesso >= 5) {
				?>
					<img src="image/bt_pagamento.gif" alt="Pagar Conta" onClick="wdCarregarFormulario('ContaPagarQuita.php?ContaId=<?php echo $dados_cli[id] ?>&headers=1','conteudo')" style="cursor: pointer" />
				<?php
					}
				?>			
			</td>	

	  </tr>

		<?php 
		//Fecha o while
		}
	echo "</table><br />";
	}


//Filtra as contas a pagar de FORNECEDORES

//Monta a query para pegar os dados
$sql_for = "SELECT 
						con.id,
						con.data,
						con.descricao,
						con.data_vencimento,
						con.valor,
						con.situacao,
						forn.nome as fornecedor_nome 
						FROM contas_pagar con
						INNER JOIN fornecedores forn ON forn.id = con.pessoa_id
						WHERE forn.nome LIKE '%$chavePesquisa%'
						AND con.tipo_pessoa = 2 
						AND con.empresa_id = '$empresaId' 
						ORDER BY con.data_vencimento DESC";

//Executa a query
$query_for = mysql_query($sql_for);

//Conta o numero de registros da query
$registros_for = mysql_num_rows($query_for);

//Caso não houver registros
if ($registros_for == 0) {
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
		<tr>
      <td valign='middle'><span class='TituloModulo'>Contas a Pagar de Fornecedores: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
    </tr>
	</table></br></br>";
} else {
	echo "
  <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
      <td valign='middle'><span class='TituloModulo'>Contas a Pagar de Fornecedores: </span><span class='style1'>A pesquisa retornou $registros_for resultado(s)</br>
		  </td>
    </tr>
		<tr>
      <td>		  
	      <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
		 	    <tr>
       		  <td width='436' class='listViewThS1'>&nbsp;&nbsp;Dados do Fornecedor/Descrição da Conta a Pagar</td>
          	<td width='70' class='listViewThS1'>Emissão</td>
          	<td width='70' class='listViewThS1'>Vencimento</td>
          	<td width='80' class='listViewThS1' align='center'>Valor</td>
          	<td width='65' class='listViewThS1' align='center'>Situação</td>
						<td class='listViewThS1'>&nbsp;</td>         		
	  		  </tr>  		  
		  ";						

	//efetua o loop na pesquisa
	while ($dados_for = mysql_fetch_array($query_for)){		
		
	//Efetua o switch para o campo de situacao
	switch ($dados_for[situacao]) {
	  case 1: $desc_situacao = "Em aberto"; break;
		case 2: $desc_situacao = "Pago"; break;
	}
	  ?>

    <tr height='16'>	
      <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style="padding: 0px; padding-left: 5px">
		    <a title="Clique para exibir os detalhes desta conta" href="#" onClick="wdCarregarFormulario('ContaPagarExibe.php?ContaId=<?php echo $dados_for[id] ?>&headers=1','conteudo')"><?php echo $dados_for[fornecedor_nome] ?></a></br><span style="font-size: 9px"><?php echo $dados_for['descricao'] ?></span>
		  </td>

      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo DataMySQLRetornar($dados_for[data]) ?>
		  </td>

      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo DataMySQLRetornar($dados_for[data_vencimento]) ?>
		  </td>

      <td align='center' valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo "R$ " . number_format($dados_for[valor], 2, ",", ".") ?>
		  </td>
		  
		  <td align='center'>
				<?php echo $desc_situacao ?>				
			</td>
		
			<td>
				<?php 
					if ($desc_situacao == "Em aberto" && $nivelAcesso >= 5) {
				?>
					<img src="image/bt_pagamento.gif" alt="Pagar Conta" onClick="wdCarregarFormulario('ContaPagarQuita.php?ContaId=<?php echo $dados_for[id] ?>&headers=1','conteudo')" style="cursor: pointer" />
				<?php
					}
				?>			
			</td>	

	  </tr>

		<?php 
		//Fecha o while
		}
	echo "</table><br />";
	}

//Filtra as contas a pagar de COLABORADORES

//Monta a query para pegar os dados
$sql_col = "SELECT 
						con.id,
						con.data,
						con.descricao,
						con.data_vencimento,
						con.valor,
						con.situacao,
						col.nome as colaborador_nome 
						FROM contas_pagar con
						INNER JOIN colaboradores col ON col.id = con.pessoa_id
						WHERE col.nome LIKE '%$chavePesquisa%'
						AND con.tipo_pessoa = 3 
						AND con.empresa_id = '$empresaId' 
						ORDER BY con.data_vencimento DESC";

//Executa a query
$query_col = mysql_query($sql_col);

//Conta o numero de registros da query
$registros_col = mysql_num_rows($query_col);

//Caso não houver registros
if ($registros_col == 0) {
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
		<tr>
      <td valign='middle'><span class='TituloModulo'>Contas a Pagar de Colaboradores: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
    </tr>";
} else {
	echo "
  <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
      <td valign='middle'><span class='TituloModulo'>Contas a Pagar de Colaboradores: </span><span class='style1'>A pesquisa retornou $registros_col resultado(s)</br>
		  </td>
    </tr>
		<tr>
      <td>		  
	      <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
		 	    <tr>
       		  <td width='436' class='listViewThS1'>&nbsp;&nbsp;Dados do Colaborador/Descrição da Conta a Pagar</td>
          	<td width='70' class='listViewThS1'>Emissão</td>
          	<td width='70' class='listViewThS1'>Vencimento</td>
          	<td width='80' class='listViewThS1' align='center'>Valor</td>
          	<td width='65' class='listViewThS1' align='center'>Situação</td>
						<td class='listViewThS1'>&nbsp;</td>         		
	  		  </tr>  		  
		  ";						

	//efetua o loop na pesquisa
	while ($dados_col = mysql_fetch_array($query_col)){		
		
	//Efetua o switch para o campo de situacao
	switch ($dados_col[situacao]) {
	  case 1: $desc_situacao = "Em aberto"; break;
		case 2: $desc_situacao = "Pago"; break;
	}
	  ?>

    <tr height='16'>	
      <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style="padding: 0px; padding-left: 5px">
		    <a title="Clique para exibir os detalhes desta conta" href="#" onClick="wdCarregarFormulario('ContaPagarExibe.php?ContaId=<?php echo $dados_col[id] ?>&headers=1','conteudo')"><?php echo $dados_col[colaborador_nome] ?></a></br><span style="font-size: 9px"><?php echo $dados_col['descricao'] ?></span>
		  </td>

      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo DataMySQLRetornar($dados_col[data]) ?>
		  </td>

      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo DataMySQLRetornar($dados_col[data_vencimento]) ?>
		  </td>

      <td align='center' valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo "R$ " . number_format($dados_col[valor], 2, ",", ".") ?>
		  </td>
		  
		  <td align='center'>
				<?php echo $desc_situacao ?>				
			</td>
		
			<td>
				<?php 
					if ($desc_situacao == "Em aberto" && $nivelAcesso >= 5) {
				?>
					<img src="image/bt_pagamento.gif" alt="Pagar Conta" onClick="wdCarregarFormulario('ContaPagarQuita.php?ContaId=<?php echo $dados_col[id] ?>&headers=1','conteudo')" style="cursor: pointer" />
				<?php
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