<?php
###########
## Módulo de pesquisa para FORNECEDORES
## Criado: - 23/04/2007 - Maycon Edinger
## Alterado: - 05/04/2010 - Maycon Edinger
## Alterações: 
## 05/04/2010 - Implementado que as pesquisas agora tb mostram o campo de ID
###########

//Monta a query para pegar os dados do cliente
$sql_conta = "SELECT 
              id,
              nome,
              tipo_pessoa,
              telefone, 
              celular,
              uf,
              ativo 
              FROM fornecedores 
              WHERE (nome LIKE '%$chavePesquisa%' AND empresa_id = '$empresaId') OR (id = '$chavePesquisa' AND empresa_id = '$empresaId') 
              ORDER BY nome";

//Executa a query
$query_conta = mysql_query($sql_conta);

//Conta o numero de registros da query
$registros_conta = mysql_num_rows($query_conta);

//Caso não houver registros
if ($registros_conta == 0) {
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
		<tr>
      <td valign='middle'><span class='TituloModulo'>Fornecedores: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
    </tr>";
} else {
	echo "
  <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
      <td valign='middle'><span class='TituloModulo'>Fornecedores: </span><span class='style1'>A pesquisa retornou $registros_conta resultado(s)</br>
		  </td>
    </tr>
		<tr>
      <td>		  
	      <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
		 	    <tr>
       		  <td width='44' class='listViewThS1' align='center'>Código</td>
            <td width='470' class='listViewThS1'>&nbsp;&nbsp;Nome/Razão Social</td>
       		  <td class='listViewThS1'>Telefone</td>
    		    <td class='listViewThS1'>Celular</td>
						<td width='26' class='listViewThS1'>UF</td>
        		<td width='36' class='listViewThS1'>Ativo</td>
        		<td width='30' class='listViewThS1'>Tipo</td>
	  		  </tr>  		  
		  ";						

	//efetua o loop na pesquisa
	while ($dados_conta = mysql_fetch_array($query_conta)){		
		//Gera a figura de tipo de conta
    switch ($dados_conta[tipo_pessoa]) {
      case 1: $conta_figura = "<img src='./image/bt_prospect.gif' alt='Pessoa Física' />";	break;
      case 2: $conta_figura = "<img src='./image/bt_cliente.gif' alt='Pessoa Jurídica' />"; break;
    }
		
		//Gera a figura de ativo
  	switch ($dados_conta[ativo]) {
      case 0: $ativo_figura = "";	break;
		  case 1: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Cadastro Ativo' />";	break;     	
    }

	  ?>

    <tr height="16">
      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList" align="center">
		    <?php echo $dados_conta[id] ?>
		  </td>
      	
      <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
		    <a title="Clique para exibir os detalhes deste fornecedor" href="#" onclick="wdCarregarFormulario('FornecedorExibe.php?FornecedorId=<?php echo $dados_conta[id] ?>','conteudo')"><?php echo $dados_conta[nome] ?></a>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $dados_conta[telefone] ?>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $dados_conta[celular] ?>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $dados_conta[uf] ?>
		  </td>
		
      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <div align="center"><?php echo $ativo_figura ?></div>
		  </td>

		  <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <div align="center"><?php echo $conta_figura ?></div>
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