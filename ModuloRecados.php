<?php 
###########
## Módulo de recados
## Criado: 20/04/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo de Recados
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";
//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Recados</span></td>
	  </tr>
	  <tr>
	    <td colspan='5'>
		    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
		</td>
	  </tr>
	</table>
	
<table id="2" width='626' align='left' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='626' class="text">
      <TABLE cellSpacing=0 cellPadding=0 width="220" border=0>
      <TBODY>
        <tr>
          <td style="PADDING-BOTTOM: 2px">
 	        <input name='Button' type='button' class=button id="Submit" accessKey='N' title="Novo Recado [Alt+N]" value='Novo Recado' onclick="window.location='sistema.php?ModuloNome=RecadoCadastra';">
          </td>
	  </TR>
    </TBODY>
    </TABLE>
           
    </td>
  </tr>
	</table>  	 


	</td>
  </tr>
  <tr>
    <td>
	<?php
	/*EXIBE OS RECADOS RECEBIDOS*/ ?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Recados Recebidos</span></td>
	  </tr>
	  <tr>
	    <td colspan='5'>
		    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
		</td>
	  </tr>
	</table>

	<?php

	  //Monta a paginação dos resultados
	  $consulta_rec = "SELECT 
					rec.id, 
					rec.empresa_id, 
					rec.remetente_id, 
					rec.destinatario_id, 
					rec.data, 
					rec.assunto, 
					rec.mensagem,
					rec.global,
					rec.lido, 
					usu.nome as remetente_nome, 
					usu.sobrenome as remetente_sobrenome 
					FROM recados rec 
					LEFT OUTER JOIN usuarios usu ON rec.remetente_id = usu.usuario_id 
					WHERE rec.destinatario_id = '$usuarioId' 
					OR (rec.global = 1 AND rec.data >= '$usuarioDataCadastro')
					ORDER BY rec.data DESC";
				   
	  //Executa a Query
	  $listagem_rec = mysql_query($consulta_rec);

	  //Determina a Quantidade de registros por página
	  $regs_pagina_rec = "10"; 
	  
	  $pagina_rec = $_GET['PaginaRec']; 
	  if (!$pagina_rec) {
    	$pc_rec = "1";
	  } else {
    	$pc_rec = $pagina_rec;
	  }
	
	  $inicio_rec = $pc_rec - 1;
	  $inicio_rec = $inicio_rec * $regs_pagina_rec;
	  
	  $limite_rec = mysql_query("$consulta_rec LIMIT $inicio_rec, $regs_pagina_rec");
	  
	  $todos_rec = mysql_query("$consulta_rec");
  	  
	  // verifica o número total de registros
	  $tot_regs_rec = mysql_num_rows($todos_rec); 
      
	  //cria o contador inicial do numero do registro pra exibir na tela
	  if ($inicio_rec == 0) { 
	    $conta_inicial_rec = 1;
        //Verifica quantos registros está exibindo
	    $conta_final_rec = mysql_num_rows($limite_rec); 
	  } else {
	    $conta_inicial_rec = $inicio_rec + 1;
	    //Workaround
	    $conta_final_rec = (mysql_num_rows($limite_rec) + $conta_inicial_rec) - 1; 
	  }
	  // verifica o número total de páginas
	  $tot_pags_rec = $tot_regs_rec / $regs_pagina_rec; 
	   
	  ?>
	   
	<table width="626" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  <tr>
	    <td COLSPAN="18" align="right">
	      <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        <tr>
	          <td align="left"  class="listViewPaginationTdS1">&nbsp;&nbsp;
	          </td>
	          <td nowrap align="right"  class="listViewPaginationTdS1">
				<?php
				//Monta os botões de paginação
				$anterior_rec = $pc_rec -1;
				$proximo_rec = $pc_rec +1;
				
				//Monta o label de ANTERIOR
				if ($pc_rec > 1) {				
				  echo "<a href='sistema.php?ModuloNome=ModuloRecados&PaginaRec=$anterior_rec' title='Exibe a página anterior'>Anterior</a>";
				  } 
				?>
				&nbsp;&nbsp;
				<span class='pageNumbers'>(<?php echo $conta_inicial_rec . " - " . $conta_final_rec . " de " . $tot_regs_rec ?>)</span>
				&nbsp;&nbsp;
				<?php 
				  //Monta o label de PROXIMO
				  if ($pc_rec < $tot_pags_rec) {				
				    echo "<a href='sistema.php?ModuloNome=ModuloRecados&PaginaRec=$proximo_rec' title='Exibe a próxima página'>Próximo</a>";
				  } 
				?>
	        </tr>
	      </table>
	    </td>
	  </tr>

	<?php
	  //Caso houverem registros
	  if ($tot_regs_rec > 0) { 
	  echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          <td width='26' scope='col'>&nbsp;</td>
	      <td width='390' scope='col'>Assunto</td>
          <td width='65' scope='col'><div align='left'>Data</div></td>
	      <td scope='col'>Remetente</td>
        </tr>
	  ";}
	  
	  //Caso não houverem registros
	  if ($tot_regs_rec == 0) { 

	  //Exibe uma linha dizendo que nao registros
	  echo "
	  <tr height='24'>
        <td colspan='3' scope='row' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
		  <slot><font color='#33485C'><strong>Não há recados recebidos</strong></font></slot>
		</td>
	  </tr>	
	  ";	  
	  } 	  
	  //Cria o array e o percorre para montar a listagem dinamicamente
	  while ($dados_rec = mysql_fetch_array($limite_rec)){

      //Seta o texto e figura para o campo de recado lido
      switch ($dados_rec[lido]) {
	    //Se o recado estiver marcado como não lido
        case 0: $recado_figura = "<img src='image/bt_recado_novo.gif' alt='Recado não lido' />";	break;
        //Se o recado for lido
	    case 1: $recado_figura = "<img src='image/bt_recado_lido.gif' alt='Recado lido' />";	break;
        //Se o recado for global
	    case 2: $recado_figura = "<img src='image/bt_recado_global.gif' alt='Recado automático gerado pelo sistema' />";	break;
      }
	  ?>

	  <tr height='16'
		onmouseover="setPointer(this, '', 'over', '#fdfdfd', '#DEEFFF', '');"
		onmouseout="setPointer(this, '', 'out', '#fdfdfd', '#DEEFFF', '');"
		onmousedown="setPointer(this, '', 'click', '#fdfdfd', '#DEEFFF', '');">
	
        <td height="15" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' class='oddListRowS1' scope='row' style="padding-bottom: 1px">
          <?php echo $recado_figura ?>
				</td>
        <td height="15">
          <font color='#CC3300' size='2' face="Tahoma"><a title="Clique para exibir este recado" href="#" onclick="wdCarregarFormulario('RecadoExibe.php?RecadoId=<?php echo $dados_rec[id] ?>','conteudo')"><?php echo $dados_rec['assunto']; ?></a></font>        
				</td>
        <td height="15" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd'>
          <?php echo DataMySQLRetornar($dados_rec[data]) ?>
				</td>
        <td colspan='2' height="15" valign='middle' bgcolor='#fdfdfd'>
          <?php echo $dados_rec[remetente_nome] . " " . $dados_rec[remetente_sobrenome] ?>
				</td>						
  	  </tr>
	<?php
	//Fecha o WHILE
	}
	?>
	</table>	
	</td>
  </tr>
  
  <tr>
  <td>
	<br>



	<?php
	/*EXIBE OS RECADOS ENVIADOS*/ ?>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Recados Enviados</span></td>
	  </tr>
	  <tr>
	    <td colspan='5'>
		    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
		</td>
	  </tr>
	</table>

	<?php

	  //Monta a paginação dos resultados
	  $consulta_env = "SELECT 
					rec.id, 
					rec.empresa_id, 
					rec.remetente_id, 
					rec.destinatario_id, 
					rec.data, 
					rec.assunto, 
					rec.mensagem,
					rec.global, 
					usu.nome as destinatario_nome, 
					usu.sobrenome as destinatario_sobrenome 
					FROM recados rec 
					INNER JOIN usuarios usu ON rec.destinatario_id = usu.usuario_id 
					WHERE rec.remetente_id = '$usuarioId' 					
					ORDER BY rec.data DESC";

	  //Executa a query			   
	  $listagem_env = mysql_query($consulta_env);

	  //Determina a Quantidade de registros por página
	  $regs_pagina_env = "10"; 
	  
	  $pagina_env = $_GET['PaginaEnv']; 
	  if (!$pagina_env) {
    	$pc_env = "1";
	  } else {
    	$pc_env = $pagina_env;
	  }
	
	  $inicio_env = $pc_env - 1;
	  $inicio_env = $inicio_env * $regs_pagina_env;
	  
	  $limite_env = mysql_query("$consulta_env LIMIT $inicio_env, $regs_pagina_env");
	  
	  $todos_env = mysql_query("$consulta_env");
	  
	  // verifica o número total de registros
	  $tot_regs_env = mysql_num_rows($todos_env); 
	  
	  //cria o contador inicial do numero do registro pra exibir na tela
	  if ($inicio_env == 0) { 
	    $conta_inicial_env = 1;
		//Verifica quantos registros está exibindo
	    $conta_final_env = mysql_num_rows($limite_env); 
	  } else {
	    $conta_inicial_env = $inicio_env + 1;
	    //Workaround
		$conta_final_env = (mysql_num_rows($limite_env) + $conta_inicial_env) - 1; 
	  }
	  // verifica o número total de páginas
	  $tot_pags_env = $tot_regs_env / $regs_pagina_env; 
	   
	  ?>
	   
	<table width="626" id="6" cellpadding="0" cellspacing="0" border="0" class="listView">
	  <tr>
	    <td COLSPAN="18" align="right">
	      <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        <tr>
	          <td align="left"  class="listViewPaginationTdS1">&nbsp;&nbsp;
	          </td>
	          <td nowrap align="right"  class="listViewPaginationTdS1">
				<?php
				  //Montagem dos botões de controle de páginas
				  $anterior_env = $pc_env -1;
				  $proximo_env = $pc_env +1;
				  //Cria o label ANTERIOR
				  if ($pc_env > 1) {				
				    echo "<a href='sistema.php?ModuloNome=ModuloRecados&PaginaEnv=$anterior_env' title='Exibe a página anterior'>Anterior</a>";
				  } 
				?>
				&nbsp;&nbsp;
				<span class='pageNumbers'>(<?php echo $conta_inicial_env . " - " . $conta_final_env . " de " . $tot_regs_env ?>)</span>
				&nbsp;&nbsp;
				<?php 
				  //Cria o label de PROXIMO
				  if ($pc_env < $tot_pags_env) {				
				    echo "<a href='sistema.php?ModuloNome=ModuloRecados&PaginaEnv=$proximo_env' title='Exibe a próxima página'>Próximo</a>";
				  } 
				?>
	        </tr>
	      </table>
	    </td>
	  </tr>

	<?php
	  //Caso houverem registros
	  if ($tot_regs_env > 0) { 
	  echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          <td width='26' scope='col'>&nbsp;</td>
	      <td width='390' scope='col'>Assunto</td>
          <td width='65' scope='col'><div align='left'>Data</div></td>
	      <td scope='col'>Destinatário</td>
        </tr>
	  ";}
	  
	  //Caso não houverem registros
	  if ($tot_regs_env == 0) { 

	  //Exibe uma linha dizendo que nao há registros
	  echo "
	  <tr height='24'>
        <td colspan='3' scope='row' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
		  <slot><font color='#33485C'><strong>Não há recados enviados</strong></font></slot>
		</td>
	  </tr>	
	  ";	  
	  } 	  
	  //Cria o array e o percorre para montar a listagem dinamicamente
	  while ($dados_env = mysql_fetch_array($limite_env)){
	  ?>

	  <tr height='16'
		onmouseover="setPointer(this, '', 'over', '#fdfdfd', '#DEEFFF', '');"
		onmouseout="setPointer(this, '', 'out', '#fdfdfd', '#DEEFFF', '');"
		onmousedown="setPointer(this, '', 'click', '#fdfdfd', '#DEEFFF', '');">
	
        <td height="18" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' class='oddListRowS1' scope='row' style="padding-bottom: 1px">
					<img src='./image/bt_recado_enviado.gif' alt='Recado enviado' />
				</td>
        <td height="18">
          <font color='#CC3300' size='2' face="Tahoma"><a title="Clique para exibir este recado" href="#" onclick="wdCarregarFormulario('RecadoExibe.php?RecadoId=<?php echo $dados_env[id] ?>','conteudo')"><?php echo $dados_env['assunto']; ?></a></font>        
				</td>
        <td height="18" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd'>
          <?php echo DataMySQLRetornar($dados_env[data]) ?>
				</td>
        <td colspan='2' height="18" valign='middle' bgcolor='#fdfdfd'>
          <?php echo $dados_env[destinatario_nome] . " " . $dados_env[destinatario_sobrenome] ?>
				</td>						
  	  </tr>

	<?php
	//Fecha o WHILE
	}
	?>
	</table>	
	</td>
  </tr>
	
	</td>
  </tr>
</table>

</td>