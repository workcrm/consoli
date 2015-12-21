<?php
###########
## Módulo de pesquisa para COLABORADORES
## Criado: - 23/04/2007 - Maycon Edinger
## Alterado: - 05/04/2010 - Maycon Edinger
## Alterações: 
## 05/04/2010 - Implementado que as pesquisas agora tb mostram o campo de ID
###########

//Monta a query para pegar os dados do cliente
$sql_conta = "SELECT 
							con.id,
							con.nome,
							con.tipo,
							con.funcao_id,
							con.telefone,
							con.celular,
							con.ativo,
							con.foto,
							fun.nome as funcao_nome
							
							FROM colaboradores con
							INNER JOIN funcoes fun ON con.funcao_id = fun.id 
							
							WHERE (con.nome LIKE '%$chavePesquisa%' AND con.empresa_id = '$empresaId') OR (con.id = '$chavePesquisa' AND con.empresa_id = '$empresaId')
              ORDER BY con.nome";

//Executa a query
$query_conta = mysql_query($sql_conta);

//Conta o numero de registros da query
$registros_conta = mysql_num_rows($query_conta);

//Caso não houver registros
if ($registros_conta == 0) {
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
		<tr>
      <td valign='middle'><span class='TituloModulo'>Colaboradores: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
    </tr>";
} else {
	
	echo "
  <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
      <td valign='middle'><span class='TituloModulo'>Colaboradores: </span><span class='style1'>A pesquisa retornou $registros_conta resultado(s)</br>
		  </td>
    </tr>
		<tr>
      <td>		  
	      <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
		 	    <tr>
       		  <td width='44' class='listViewThS1' align='center'>Código</td>
            <td colspan='2' width='70' class='listViewThS1' style='padding-left: 40px'>Tipo</td>
						<td class='listViewThS1'>&nbsp;&nbsp;Nome do Colaborador</td>
       		  <td width='170' class='listViewThS1'>Função</td>
    		    <td width='66' class='listViewThS1'>Telefone</td>
    		    <td width='66' class='listViewThS1'>Celular</td>
        		<td width='20' class='listViewThS1'>Ativo&nbsp;&nbsp;</td>
	  		  </tr>  		  
		  ";						

	//efetua o loop na pesquisa
	while ($dados_conta = mysql_fetch_array($query_conta)){		
	
		//Gera a figura de ativo
  	switch ($dados_conta[ativo]) {
      case 0: $ativo_figura = "";	break;
		  case 1: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Cadastro Ativo' />";	break;     	
    }
    
		switch ($dados_conta["tipo"]) {
		  case 1: $desc_tipo = "FREELANCE"; break;
			case 2: $desc_tipo = "FUNCIONÁRIO"; break;
      case 3: $desc_tipo = "EX-FUNCIONÁRIO"; break;
		}
    
    //Verifica se há uma foto definida para o colaborador
    if ($dados_conta["foto"] != ""){
    	
    	$caminho_foto = "<img src='imagem_colaborador/$dados_conta[foto]' width='30' height='40' />";
    	
   	} else {
    	
			$caminho_foto = "Sem<br/>foto";
			
		}

	  ?>

    <tr height="40">
			
			<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" align="center">
		    <?php echo $dados_conta[id] ?>
		  </td>
      
      <td width="32" height="40" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-bottom: 0px">
		    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="padding-bottom: 0px">
		    	<td width="32" height="40" style="margin-bottom: 0px; padding-bottom: 0px" valign="middle" align="center">
					  <?php echo $caminho_foto ?>
			  	</td>
		    </table>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $desc_tipo ?>
		  </td>
			
      <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
		    <a title="Clique para exibir os detalhes deste colaborador" href="#" onclick="wdCarregarFormulario('ColaboradorExibe.php?ColaboradorId=<?php echo $dados_conta[id] ?>','conteudo')"><?php echo $dados_conta[nome] ?></a>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $dados_conta[funcao_nome] ?>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $dados_conta[telefone] ?>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $dados_conta[celular] ?>
		  </td>
		
      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <div align="center"><?php echo $ativo_figura ?></div>
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