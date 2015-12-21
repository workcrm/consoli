<?php
###########
## Módulo de pesquisa para COMPROMISSOS
## Criado: - 23/04/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo de pesquisa para Compromissos
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Monta a query para pegar os dados do compromisso
$sql_compromisso = "SELECT id, dia, mes, ano, hora, duracao, prioridade, atividade, assunto, categoria FROM compromissos WHERE assunto LIKE '%$chavePesquisa%' AND usuario_id = '$usuarioId' ORDER BY ano DESC, mes DESC, dia DESC, hora DESC";

//Executa a query
$query_compromisso = mysql_query($sql_compromisso);

//Conta a quantidade de registros de retorno da query
$registros_compromisso = mysql_num_rows($query_compromisso);

//Caso não houver registros
if ($registros_compromisso == 0) {
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
      <td valign='middle'><span class='TituloModulo'>Compromissos: </span><span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span></td>
    </tr>";
} else {
	echo "
	  <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	    <tr>
        <td valign='middle'><span class='TituloModulo'>Compromissos: </span><span class='style1'>A pesquisa retornou $registros_compromisso resultado(s)</br>
		    </td>
      </tr>
		  <tr>
        <td>		  
	        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
	  		 		<tr height='20'>
        		  <td width='13' class='listViewThS1'>
          			<div align='center'>&nbsp;&nbsp;A</div>
        			</td>
        		  <td width='13' class='listViewThS1'>
          			<div align='center'>&nbsp;&nbsp;&nbsp;P</div>
        			</td>
	    		  	<td width='100' class='listViewThS1' nowrap='nowrap'>&nbsp;Data/Hora</td>
	    		  	<td width='450' class='listViewThS1'>&nbsp;&nbsp;Assunto</td>
	    		  	<td width='150' class='listViewThS1' nowrap='nowrap'>Categoria</td>
	  				</tr>  		  
		  ";						

		//efetua o loop na pesquisa
	  while ($dados_compromisso = mysql_fetch_array($query_compromisso)){
			//Monta o switch para exibir as categorias do compromisso		
    	switch ($dados_compromisso["categoria"]) {
        case 01: 
					$cat_name = "<font size='1' face='Tahoma' color=#666666><strong>   (Nenhuma)</strong></font>";	break;
        case 02: 
					$cat_name = "<font size='1' face='Tahoma' color=#CC3300><strong>   (Importante)</strong></font>";	break;
        case 03: 
					$cat_name = "<font size='1' face='Tahoma' color=#6666CC><strong>   (Negócios)</strong></font>";	break;
        case 04: 
					$cat_name = "<font size='1' face='Tahoma' color=#669900><strong>   (Pessoal)</strong></font>";	break;
        case 05: 
					$cat_name = "<font size='1' face='Tahoma' color=#999900><strong>   (Folga)</strong></font>";	break;
        case 06: 
					$cat_name = "<font size='1' face='Tahoma' color=#FF9900><strong>   (Deve ser atendido)</strong></font>";	break;
        case 07: 
					$cat_name = "<font size='1' face='Tahoma' color=#FF00FF><strong>   (Aniversário)</strong></font>";	break;
        case 08: 
					$cat_name = "<font size='1' face='Tahoma' color=#FF3300><strong>   (Ligação Telefônica)</strong></font>    ";	break;
        }
      //Monta o switch para o tipo de atividade 
    	switch ($dados_compromisso["atividade"]) {
        case 01: $ativ_figura = "<img src='./image/bt_reuniao.gif' alt='Reunião' />";	break;
        case 02: $ativ_figura = "<img src='./image/bt_ligacao.gif' alt='Ligação' />";	break;
        case 03: $ativ_figura = "<img src='./image/bt_compromisso.gif' alt='Compromisso' />";	break;
      }
      //Monta o switch para a prioridade 
    	switch ($dados_compromisso["prioridade"]) {
        case 01: $prior_figura = "<img src='./image/bt_prior_alta.gif' alt='Alta Prioridade' />";	break;
        case 02: $prior_figura = "<img src='./image/bt_prior_media.gif' alt='Média Prioridade' />";	break;
        case 03: $prior_figura = "<img src='./image/bt_prior_baixa.gif' alt='Baixa Prioridade' />";	break;
      }

	    ?>

    <tr height='16'>

      <td scope='row' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
		    <?php echo $ativ_figura ?>
		  </td>

      <td scope='row' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
		    <?php echo $prior_figura ?>
		  </td>
		
      <td scope='row' valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <div align="center"><?php echo $dados_compromisso['dia'] . "/" . $dados_compromisso['mes'] . "/" . $dados_compromisso['ano'] . " - " . substr($dados_compromisso[hora],0,5)?></div>
		  </td>

		  <td scope='row' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
        <a title='Clique para exibir os detalhes deste compromisso' href="#" onClick="wdCarregarFormulario('CompromissoExibe.php?CompromissoId=<?php echo $dados_compromisso["id"] ?>','conteudo')"><?php echo $dados_compromisso['assunto']; ?></a>       
		  </td>
		  
		  <td scope='row' valign='middle' bgcolor='#fdfdfd' class='currentTabList'>		
		    <?php echo $cat_name; ?>
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