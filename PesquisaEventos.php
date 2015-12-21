<?php
###########
## Módulo de pesquisa para EVENTOS
## Criado: - 11/07/2007 - Maycon Edinger
## Alterado: - 05/04/2010 - Maycon Edinger
## Alterações: 
## 05/04/2010 - Implementado que as pesquisas agora tb mostram o campo de ID
###########

//Monta a query para pegar os dados do cliente
$sql_conta = "SELECT 
              id,
              nome,
              data_realizacao,
              hora_realizacao,
              status, 
              duracao 
              FROM eventos 
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
      <td valign='middle'><span class='TituloModulo'>Eventos: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
    </tr>";
} else {
	echo "
  <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
      <td valign='middle'><span class='TituloModulo'>Eventos: </span><span class='style1'>A pesquisa retornou $registros_conta resultado(s)</br>
		  </td>
    </tr>
		<tr>
      <td>		  
	      <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
		 	    <tr>
       		  <td width='16' class='listViewThS1'>&nbsp;&nbsp;&nbsp;S</td>
            <td width='44' class='listViewThS1' align='center'>Código</td>
					 	<td width='360' class='listViewThS1'>&nbsp;&nbsp;Evento</td>
       		  <td width='62' class='listViewThS1'>Data</td>
       		  <td width='40' class='listViewThS1'>Hora</td>
    		    <td width='50' class='listViewThS1'>Duração</td>
        		<td class='listViewThS1' align='center'>Ações</td>
	  		  </tr>  		  
		  ";						

	//efetua o loop na pesquisa
	while ($dados_conta = mysql_fetch_array($query_conta)){	
		
	  //Efetua o switch para o campo de status
		switch ($dados_conta[status]) {
		  case 0: 
				$status_fig = "<img src='./image/bt_evento_orcamento.png' title='Em Orçamento'>"; 
			break;
			case 1: 
				$status_fig = "<img src='./image/bt_evento_aberto.png' title='Em Aberto'>"; 
			break;
			case 2: 
				$status_fig = "<img src='./image/bt_evento_realiz.png' title='Realizado'>"; 
			break;
			case 3: 
				$status_fig = "<img src='./image/bt_evento_nao_realiz.png' title='Não Realizado'>"; 
			break;
		} 

	  ?>

    <tr height="16">	
      <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
      	<?php echo $status_fig ?>
      </td>
      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList" align="center">
		    <?php echo $dados_conta[id] ?>
		  </td>      
			<td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
		    <a title="Clique para exibir os detalhes deste evento" href="#" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')">
				
					<?php 
						
						//Verifica se o evento está como nao realizado
						if ($dados_conta[status] == 3){
							
							echo "<span style='color: #990000; text-decoration: line-through'>$dados_conta[nome]</span>";
											
						} else {
							
							echo $dados_conta[nome];
													
						}
							
					?>
				</a>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo DataMySQLRetornar($dados_conta[data_realizacao]) ?>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $dados_conta[hora_realizacao] ?>
		  </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <?php echo $dados_conta[duracao] ?>
		  </td>
		
      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
		    <div align="center">
					<img src="./image/bt_data_evento.gif" title="Clique para gerenciar as datas deste evento" onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">
					<img src="./image/bt_participante.gif" title="Clique para gerenciar os participantes deste evento" onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">
					<img src="./image/bt_endereco.gif" title="Clique para gerenciar os endereços deste evento" onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">
					<img src="./image/bt_item.gif" title="Clique para gerenciar os itens/produtos deste evento" onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">
					<img src="./image/bt_servico.gif" title="Clique para gerenciar os serviços deste evento" onclick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">
					<img src="./image/bt_terceiro.gif" title="Clique para gerenciar os terceiros deste evento" onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">
					<img src="./image/bt_brinde.gif" title="Clique para gerenciar os brindes deste evento" onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">											
					<img src="./image/bt_repertorio.gif" title="Clique para gerenciar o repertório musical deste evento" onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">					
					<img src="./image/bt_formando.gif" title="Clique para gerenciar os formandos deste evento" onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">						
					<img src="./image/bt_fotovideo.gif" title="Clique para gerenciar o foto e vídeo deste evento" onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_conta[id] ?>&headers=1','conteudo')" style="cursor: pointer">
				</div>
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