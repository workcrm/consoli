<?php
###########
## Módulo de pesquisa para RECADOS
## Criado: 23/04/2007 - Maycon Edinger
## Alterado:
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo de pesquisa para RECADOS
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Monta a query para pegar os dados do contato
$sql_recado = "SELECT 
			  		rec.id, 
						rec.empresa_id, 
						rec.remetente_id, 
						rec.destinatario_id, 
						rec.data, 
						rec.assunto, 
						rec.mensagem,
						rec.global, 
						usu.nome as remetente_nome, 
						usu.sobrenome as remetente_sobrenome 
						FROM recados rec 
						INNER JOIN usuarios usu ON rec.remetente_id = usu.usuario_id 
						WHERE rec.assunto LIKE '%$chavePesquisa%' AND rec.empresa_id = '$empresaId' 
						AND rec.destinatario_id = '$usuarioId'
						ORDER BY rec.data DESC";

//Executa a query
$query_recado = mysql_query($sql_recado);

//Verifica o numero de registros da query
$registros_recado = mysql_num_rows($query_recado);

//Caso não houver registros
if ($registros_recado == 0) {
	echo "
	<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
      <td valign='middle'>
				<span class='TituloModulo'>Recados: </span>
				<span class='style1'>Não há ocorrências que satisfaçam os critérios de pesquisa</span>
			</td>
    </tr>";
} else {
	echo "
  <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
	  <tr>
      <td valign='middle'><span class='TituloModulo'>Recados: </span><span class='style1'>A pesquisa retornou $registros_recado resultado(s)</br>
	    </td>
    </tr>
	  <tr>
      <td>		  
        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
		  	  <tr>
        	  <td scope='col' width='430' class='listViewThS1'>&nbsp;&nbsp;Assunto</td>
        		<td scope='col' width='70' class='listViewThS1'>Enviado</td>
     		    <td scope='col' class='listViewThS1'>Remetente</td>
	  		  </tr>  		  
		  ";						

		//efetua o loop na pesquisa
	  while ($dados_recado = mysql_fetch_array($query_recado)){		
	  ?>

    <tr height='16'>
	
      <td scope='row' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
		    <a title="Clique para exibir este recado" href="#" onClick="wdCarregarFormulario('RecadoExibe.php?RecadoId=<?php echo $dados_recado[id] ?>','conteudo')"><?php echo $dados_recado[assunto] ?></a>
		  </td>

      <td scope='row' valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo DataMySQLRetornar($dados_recado[data]) ?>
		  </td>

      <td scope='row' valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
		    <?php echo $dados_recado[remetente_nome] . " " . $dados_recado[remetente_sobrenome] ?>
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