<?php 
###########
## Módulo para Exibição dos detalhes do compromisso
## Criado: 16/04/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Recebe o ID do compromisso a exibir
$CompromissoId = $_GET["CompromissoId"];

//Monta a sql para obter o compromisso requisitado
$sql = "SELECT * FROM compromissos WHERE id = $CompromissoId";

//Executa a query
$resultado = mysql_query($sql);

//Monta o array com os dados
$campos = mysql_fetch_array($resultado);

//Efetua o switch para obter os nomes dos meses
switch ($campos[mes]) {
        case 1: $mes_nome = "Janeiro";	break;
        case 2: $mes_nome = "Fevereiro";	break;
        case 3: $mes_nome = "Março";	break;
        case 4: $mes_nome = "Abril";	break;
        case 5: $mes_nome = "Maio";	break;
        case 6: $mes_nome = "Junho";	break;
        case 7: $mes_nome = "Julho";	break;
        case 8: $mes_nome = "Agosto";	break;
        case 9: $mes_nome = "Setembro";	break;
        case 10: $mes_nome = "Outubro";	break;
        case 11: $mes_nome = "Novembro";	break;
        case 12: $mes_nome = "Dezembro";	break;
      }

//Efetua o switch para obter os nomes das categorias
switch ($campos[categoria]) {
          case 1:  $cat_name = "<font color=#666666><strong>   Nenhuma</strong></font>";	break;
          case 2:  $cat_name = "<font color=#CC3300><strong>   Importante</strong></font>";	break;
          case 3:  $cat_name = "<font color=#6666CC><strong>   Negócios</strong></font>";	break;
          case 4:  $cat_name = "<font color=#669900><strong>   Pessoal</strong></font>";	break;
          case 5:  $cat_name = "<font color=#999900><strong>   Folga</strong></font>";	break;
          case 6:  $cat_name = "<font color=#FF9900><strong>   Deve ser atendido</strong></font>";	break;
          case 7:  $cat_name = "<font color=#FF00FF><strong>   Aniversário</strong></font>";	break;
          case 8:  $cat_name = "<font color=#FF3300><strong>   Ligação Telefônica</strong></font>";	break;
   }

//Efetua o switch para obter os dados da atividade
switch ($campos[atividade]) {
        case 01: $ativ_figura = "<img src='./image/bt_reuniao.gif' alt='Reunião' /> Reunião";	break;
        case 02: $ativ_figura = "<img src='./image/bt_ligacao.gif' alt='Ligação' /> Ligação";	break;
        case 03: $ativ_figura = "<img src='./image/bt_compromisso.gif' alt='Compromisso' /> Compromisso";	break;
   }

//Efetua o switch para obter os dados da prioridade
switch ($campos[prioridade]) {
        case 01: $prior_figura = "<img src='./image/bt_prior_alta.gif' alt='Alta Prioridade' /> Alta Prioridade";	break;
        case 02: $prior_figura = "<img src='./image/bt_prior_media.gif' alt='Média Prioridade' /> Média Prioridade";	break;
        case 03: $prior_figura = "<img src='./image/bt_prior_baixa.gif' alt='Baixa Prioridade' /> Baixa Prioridade";	break;
   }
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Visualização do Compromisso</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
			  </tr>
			</table>
	
			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td width="100%" class="text">
			      <table cellspacing="0" cellpadding="0" width="186" border="0">
			        <tr>
			          <td style="PADDING-BOTTOM: 2px">
			            <form id="form" name="cadastro" action="sistema.php?ModuloNome=CompromissoAltera" method="post" onsubmit="return valida_form()">
			 	        		<input name="Submit" type="submit" class="button" id="Submit" title="Altera este compromisso" value="Editar Compromisso">
			 	        		<input name="Id" type="hidden" value=<?php echo $campos[id] ?> />
			 	        	</form>
			          </td>
			
					      <td style="PADDING-BOTTOM: 2px">
				          <form id="exclui" name="exclui" action="ProcessaExclusao.php" method="post">
						    		<input class="button" title="Exclui este compromisso" onclick="return confirm('Confirma a exclusão deste registro ?')" type="submit" value="Excluir" name="Delete">
						    		<input name="Id" type="hidden" value=<?php echo $campos[id] ?> />
						    		<input name="Modulo" type="hidden" value="compromissos" />
				          </form>
				        </td>
					  	</tr>	
				    </table>
           
				    <table width="100%" class="tabDetailView" cellspacing="0" cellpadding="0" border="0">
				      <tr>
				        <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="22">
				          <table cellspacing="0" cellpadding="0" width="100%" border="0">
				            <tr>
				              <td height="14" class="tabDetailViewDL" style="TEXT-ALIGN: left">
											  <img src="image/bt_cadastro.gif"> Caso desejar alterar este compromisso, clique em [Editar Compromisso] 
											</td>
									  </tr>
					 			  </table>        
							  </td>
					    </tr>
				      <tr>
				        <td class="dataLabel" width="87">
					        <span class="dataLabel">Data:</span>
								</td>
				        <td width="190" class="tabDetailViewDF">
					        <?php echo $campos[dia] ?>&nbsp; de &nbsp;<?php echo $mes_nome ?>&nbsp; de &nbsp;<?php echo $campos[ano] ?>	 					
								</td>
				      	<td class="dataLabel">Hora:</td>
				      	<td width="66" class="tabDetailViewDF"><?php echo substr($campos[hora],0,5) ?></td>
			          <td width="60" class="dataLabel">Dura&ccedil;&atilde;o:</td>
			          <td width="71" class="tabDetailViewDF"><?php echo substr($campos[duracao],0,5) ?></td>
				      </tr>
				      <tr>
				        <td class="dataLabel">Atividade:</td>
				        <td class="tabDetailViewDF"><?php echo $ativ_figura ?></td>
				        <td class="dataLabel">Prioridade:</td>
				        <td colspan="3" class="tabDetailViewDF"><?php echo $prior_figura ?></td>
			        </tr>
				      <tr>
				        <td class="dataLabel">
					        <span class="dataLabel">Assunto:</span>
								</td>
				        <td colspan="5" class="tabDetailViewDF">
				          <strong><?php echo $campos[assunto] ?></strong>
								</td>
				      </tr>
				      <tr>
				        <td class="dataLabel">
				          Categoria:
								</td>
				        <td class="tabDetailViewDF">
					  	    <?php echo $cat_name ?> 
								</td>
				        <td width="74" class="dataLabel">
					        Local:
								</td>
				        <td colspan="3" valign="top" class="tabDetailViewDF">
					        <?php echo $campos[local] ?>	        
								</td>
					    </tr>
				      <tr>
				        <td valign="top" class="dataLabel">
					        Descri&ccedil;&atilde;o:
								</td>
				        <td colspan="5" class="tabDetailViewDF">
				          <?php echo $campos[descricao] ?> 
								</td>
			      	</tr>
						</table>
			  	</td>
			  </tr>
			</table>  	 
		</td>
  </tr>
  <tr>
    <td>
			<br/>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif">&nbsp;<span class="TituloModulo">Compromissos</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
			  </tr>
			</table>

			<?php 
			  //Monta a paginação dos resultados exibindo os compromissos
			  //Monta a query para selecionar os registros usando os LIMITS para a paginação
			  $consulta = "SELECT * FROM compromissos WHERE usuario_id = '$usuarioId' ORDER BY ano DESC, mes DESC, dia DESC, hora DESC";
			  //Executa a query
			  $listagem = mysql_query($consulta);
			  //Limita a quantidade de registros por página
			  $regs_pagina = "30"; 
			  //Recebe o parametro da página
			  $pagina = $_GET["Pagina"]; 
			  //Verifica se está passando uma paginação
			  if (!$pagina) {
					//Caso não, a página é 1    	
					$pc = "1";
			  } else {
			  	//Senão pega o numero da pagina passado
		    	$pc = $pagina;
			  }
			  
			  $inicio = $pc - 1;
			  $inicio = $inicio * $regs_pagina;
			  //Define os limites da pesquisa no sql	  
			  $limite = mysql_query("$consulta LIMIT $inicio,$regs_pagina");
				//Roda a query usando o limite
			  $todos = mysql_query("$consulta");
				//Verifica o número total de registros
			  $tot_regs = mysql_num_rows($todos); 
				//cria o contator inicial do numero do registro pra exibir na tela
			  if ($inicio == 0) { 
			    $conta_inicial = 1;
			    //Verifica quantos registros está exibindo
					$conta_final = mysql_num_rows($limite); 
			  } else {
			    $conta_inicial = $inicio + 1;
			    //Gambiarras S/A
					$conta_final = (mysql_num_rows($limite) + $conta_inicial) - 1; 
			  }
			  //Verifica o número total de páginas
			  $tot_pags = $tot_regs / $regs_pagina; 
			?>

			<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
			  <tr>
			    <td colspan="18" align="right">
			      <table border="0" cellpadding="0" cellspacing="0" width="100%">
			        <tr>
			          <td align="left"  class="listViewPaginationTdS1">&nbsp;&nbsp;
			          </td>
			          <td nowrap align="right"  class="listViewPaginationTdS1">
									<?php
									//Cria as variáveis de proximo e anterior
									$anterior = $pc -1;
									$proximo = $pc +1;
									//Se precisar, cria o botão de anterior
									if ($pc>1) {				
									  echo "<a href='#' onclick=\"wdCarregarFormulario('CompromissoExibe.php?CompromissoId=$CompromissoId&Pagina=$anterior', 'conteudo')\" title='Exibe a página anterior'>Anterior</a>";
									} ?>
									&nbsp;&nbsp;
									<span class="pageNumbers">(<?php echo $conta_inicial . " - " . $conta_final . " de " . $tot_regs ?>)</span>
									&nbsp;&nbsp;
									<?php if ($pc<$tot_pags) {				
									echo "<a href='#' onclick=\"wdCarregarFormulario('CompromissoExibe.php?CompromissoId=$CompromissoId&Pagina=$proximo', 'conteudo')\" title='Exibe a próxima página'>Próximo</a>";
									} ?>
			          </td>
			        </tr>
			      </table>
			    </td>
			  </tr>
			  <tr height="20">
		        <td width="13" class="listViewThS1"> 
		          <div align="center">&nbsp;&nbsp;A</div>
		        </td>
		        <td width="13" class="listViewThS1">
		          <div align="center">&nbsp;&nbsp;&nbsp;P</div>
		        </td>
			    	<td width="100" class="listViewThS1" nowrap="nowrap">Data/Hora</td>
			    	<td width="440" class="listViewThS1">Assunto</td>
			    	<td width="150" class="listViewThS1" nowrap="nowrap">Categoria</td>
			  </tr>

				<?php
				  //Monta e percorre o array dos dados
				  while ($dados = mysql_fetch_array($limite)){
					//Alimenta as variáveis
				  $month = $dados["mes"];
				  $categoria = $dados["categoria"];
				  $atividade = $dados["atividade"];
				  $prioridade = $dados["prioridade"];
			
				  $hora = substr($dados["hora"],0,5);
			    //Monta o switch das categorias
				  switch ($categoria) {
				    case 1:  $cat_name = "<font color=#666666><strong>   Nenhuma</strong></font>";	break;
				    case 2:  $cat_name = "<font color=#CC3300><strong>   Importante</strong></font>";	break;
				    case 3:  $cat_name = "<font color=#6666CC><strong>   Negócios</strong></font>";	break;
				    case 4:  $cat_name = "<font color=#669900><strong>   Pessoal</strong></font>";	break;
				    case 5:  $cat_name = "<font color=#999900><strong>   Folga</strong></font>";	break;
				    case 6:  $cat_name = "<font color=#FF9900><strong>   Deve ser atendido</strong></font>";	break;
				    case 7:  $cat_name = "<font color=#FF00FF><strong>   Aniversário</strong></font>";	break;
				    case 8:  $cat_name = "<font color=#FF3300><strong>   Ligação Telefônica</strong></font>";	break;
				  }
			    //Monta o switch das atividades
			    switch ($atividade) {
			        case 01: $ativ_figura = "<img src='./image/bt_reuniao.gif' alt='Reunião' />";	break;
			        case 02: $ativ_figura = "<img src='./image/bt_ligacao.gif' alt='Ligação' />";	break;
			        case 03: $ativ_figura = "<img src='./image/bt_compromisso.gif' alt='Compromisso' />";	break;
			    }
			    //Monta o switch da prioridade
			    switch ($prioridade) {
			        case 01: $prior_figura = "<img src='./image/bt_prior_alta.gif' alt='Alta Prioridade' />";	break;
			        case 02: $prior_figura = "<img src='./image/bt_prior_media.gif' alt='Média Prioridade' />";	break;
			        case 03: $prior_figura = "<img src='./image/bt_prior_baixa.gif' alt='Baixa Prioridade' />";	break;
			    }
			
				?>


			  <tr height='16'
				onmouseover="setPointer(this, '', 'over', '#fdfdfd', '#DEEFFF', '');"
				onmouseout="setPointer(this, '', 'out', '#fdfdfd', '#DEEFFF', '');"
				onmousedown="setPointer(this, '', 'click', '#fdfdfd', '#DEEFFF', '');">
			
		      <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" nowrap="nowrap">
					  <?php echo $ativ_figura ?>
					</td>
		
		      <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" nowrap="nowrap">
					  <?php echo $prior_figura ?>
					</td>
		
			    <td valign="middle" bgcolor="#fdfdfd" class="currentTabList" nowrap="nowrap">
					  <?php echo $dados[dia]?>/<?php echo $dados[mes] ?>/<?php echo $dados[ano]?> - <?php echo $hora ?>
					</td>
		
		    	<td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
					  <a title="Clique para exibir os detalhes deste compromisso" href="#" onclick="wdCarregarFormulario('CompromissoExibe.php?CompromissoId=<?php echo $dados[id] ?>&Pagina=<?php echo $pc ?>','conteudo')"><?php echo $dados[assunto] ?></a>
					</td>
		
			    <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
					  <?php echo $cat_name ?>
					</td>
				</tr>
	
				<?php
			  //Fecha o while
				}
				?>
			</table>
			<br/>
			<br/>
		</td>
  </tr>
</table>

</td>