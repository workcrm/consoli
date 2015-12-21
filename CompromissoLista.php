<?php 
###########
## Módulo para Listagem dos compromissos no form principal
## Criado: 17/04/2007 - Maycon Edinger
## Alterado: 02/09/2008 - Maycon Edinger
## Alterações: 
## Exibir a listagem de compromissos com 7 dias de antecedência
###########

//Rotina para verificar se necessita ou não montar o header para o ajax
//Caso venha a variável header setada como 1
if (isset($header)) 
{
 
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{
	
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";

}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Alimenta as variáveis com a data atual
$id = "0";
$dia = date("d",mktime());
$mes = date("m",mktime());
$ano = date("Y",mktime());

//Monta e executa a query para buscar os compromissos da data atual do usuario
$sql = mysql_query("SELECT * FROM compromissos WHERE dia = '$dia' AND mes = '$mes' AND ano = '$ano' AND usuario_id = '$usuarioId' ORDER BY prioridade");

//Conta o numero de compromissos que a query retornou
$registros = mysql_num_rows($sql);
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Meus Compromissos</span>
					</td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">		
					</td>
				</tr>
			</table>
 	 	</td>
	</tr>
	<tr>
		<td>
			<?php
				
				//Armazena o mês atual na variável
				$mes = date("m",mktime());
				
				//Efetua o switch para determinar o nome do mes atual
				switch ($mes) 
				{
					case 1: $mes_nome = "Janeiro";	break;
					case 2: $mes_nome = "Fevereiro";	break;
					case 3: $mes_nome = "Março";	break;
					case 4: $mes_nome = "Abril";	break;
					case 5: $mes_nome = "Maio";		break;
					case 6: $mes_nome = "Junho";	break;
					case 7: $mes_nome = "Julho";	break;
					case 8: $mes_nome = "Agosto";	break;
					case 9: $mes_nome = "Setembro";	break;
					case 10: $mes_nome = "Outubro";	break;
					case 11: $mes_nome = "Novembro";	break;
					case 12: $mes_nome = "Dezembro";	break;
				}
			?>
			<table id="2" width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class="listView">
				<tr height="12">
					<td height="12" colspan='6' class="listViewPaginationTdS1">
						<table width='100%' border='0' cellspacing='0' cellpadding='0'>
							<tr>
								<td width="40">
									<img src="image/bt_calendario_gd.gif" />
								</td>
								<td>
									<?php 
									
										if ($registros == 0) 
										{
											
											$str_titulo = "compromissos";	
											$mensagem_regs = "Você não possui ";
										
										} 
										
										else 
										
										{
											
											if ($registros > 1) 
											{
												
												$str_titulo = "compromissos";
											
											} 
											
											else 
											
											{
												
												$str_titulo = "compromisso";
											
											}
											
											$mensagem_regs = "Você possui <span style='color: #990000'>$registros </span>";
										} 
									
									?>
									<span style="font-size: 12px; color: #444444"><b><?php echo $mensagem_regs . " " . $str_titulo ?> para <span style='color: #990000'><?php echo date("d",mktime()); ?> de <?php echo $mes_nome; ?> de <?php echo date("Y",mktime()); ?></b></span></span>
								</td>
								<td width="40" align="right">
									<img src="image/bt_calendario3.gif" />
								</td>
								<td width="110" align="right" class="listViewPaginationTdS1">
									<span style="font-size: 12px"><b>[</b><a href='#' title='Exibe todos os recados' onclick="wdCarregarFormulario('ModuloCompromissos.php','conteudo')">Consultar Agenda</a><b>]</b></span>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<?php
    
					//Caso não tenha compromissos então não exibe a linha de cabeçalho.
					if ($registros > 0) 
					{ 
						
						echo "
							<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
								<td width='20'><div align='center'>&nbsp;A</div></td>
								<td width='25'><div align='center'>&nbsp;P</div></td>
								<td width='37'>Hora</td>
								<td width='740' colspan='2'>Assunto</td>
							</tr>";
		
					}
					
					//Monta e percorre o array dos dados
					while ($dados = mysql_fetch_array($sql))
					{
					
						//Alimenta as variáveis com valores vindos do banco
						$month = $dados["mes"];
						$categoria = $dados["categoria"];
						$atividade = $dados["atividade"];
						$prioridade = $dados["prioridade"];

						//Efetua o switch para dar os nomes das categorias
						switch ($categoria) 
						{
							case 1: $cat_name = "<font size='1' face='Tahoma' color=#666666><strong>   (Nenhuma)</strong></font>";	break;
							case 2: $cat_name = "<font size='1' face='Tahoma' color=#CC3300><strong>   (Importante)</strong></font>";	break;
							case 3: $cat_name = "<font size='1' face='Tahoma' color=#6666CC><strong>   (Negócios)</strong></font>";	break;
							case 4: $cat_name = "<font size='1' face='Tahoma' color=#669900><strong>   (Pessoal)</strong></font>";	break;
							case 5: $cat_name = "<font size='1' face='Tahoma' color=#999900><strong>   (Folga)</strong></font>";	break;
							case 6: $cat_name = "<font size='1' face='Tahoma' color=#FF9900><strong>   (Deve ser atendido)</strong></font>";	break;
							case 7: $cat_name = "<font size='1' face='Tahoma' color=#FF00FF><strong>   (Aniversário)</strong></font>";	break;
							case 8: $cat_name = "<font size='1' face='Tahoma' color=#FF3300><strong>   (Ligação Telefônica)</strong></font>    ";	break;
						}

						//Efetua o switch para dar os nomes das atividades       
						switch ($atividade) 
						{
							case 1: $ativ_figura = "<img src='./image/bt_reuniao.gif' alt='Reunião' />";	break;
							case 2: $ativ_figura = "<img src='./image/bt_ligacao.gif' alt='Ligação' />";	break;
							case 3: $ativ_figura = "<img src='./image/bt_compromisso.gif' alt='Compromisso' />";	break;
						}
						
						//Efetua o switch para dar os nomes das prioridades
						switch ($prioridade) 
						{
							case 1: $prior_figura = "<img src='./image/bt_prior_alta.gif' alt='Alta Prioridade' />";	break;
							case 2: $prior_figura = "<img src='./image/bt_prior_media.gif' alt='Média Prioridade' />";	break;
							case 3: $prior_figura = "<img src='./image/bt_prior_baixa.gif' alt='Baixa Prioridade' />";	break;
						}
					?>

					<tr height="16" valign="middle">
						<td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style="padding-bottom: 0px">
							<?php echo $ativ_figura ?>
						</td>
						<td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style="padding-bottom: 0px">
							<?php echo $prior_figura ?>
						</td>		
						<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style="padding-bottom: 0px">
							<?php echo substr($dados[hora],0,5) ?>
						</td>			
						<td colspan="2" style="padding-bottom: 0px">
							<font color='#CC3300' size='2' face="Tahoma"><a title="Clique para exibir os detalhes deste compromisso" href="#"  onclick="wdCarregarFormulario('CompromissoExibe.php?CompromissoId=<?php echo $dados[id] ?>','conteudo')"><?php echo $dados['assunto']; ?></a><?php echo $cat_name; ?></font>        
						</td>
					</tr>
					<tr height="10">
						<td style="padding-top: 0px">&nbsp;</td>
						<td style="padding-top: 0px">&nbsp;</td>
						<td style="padding-top: 0px">&nbsp;</td>
						<td colspan="2" style="padding-top: 0px"><font size='1' face='Tahoma'><?php echo $dados['descricao']; ?></td>
					</tr>

					<?php
						
						//Fecha o while
						}
  
					?>
  
					<tr height="12">
						<td height="12" colspan='6' class="listViewPaginationTdS1" <?php echo $style_tabela ?>>
							<?php 

								$amanha = som_data(date("d/m/Y", mktime()),1);
								$data_sete_dias = som_data(date("d/m/Y", mktime()),7);

								$data_inicio = $amanha;
								$data_termino = $data_sete_dias;

								$quebra_dia_inicio = substr($data_inicio,0,2);
								$quebra_mes_inicio = substr($data_inicio,3,2);
								$quebra_ano_inicio = substr($data_inicio,6,4);

								$quebra_dia_termino = substr($data_termino,0,2);
								$quebra_mes_termino = substr($data_termino,3,2);
								$quebra_ano_termino = substr($data_termino,6,4);

								//Monta e executa a query para buscar os eventos para os próximos 7 dias
								$sql = mysql_query("SELECT * FROM compromissos WHERE dia >= '$quebra_dia_inicio' AND dia <= '$quebra_dia_termino' AND mes >= '$quebra_mes_inicio' AND mes <= '$quebra_mes_termino' AND ano >= '$quebra_ano_inicio' AND ano <= '$quebra_ano_termino' AND usuario_id = '$usuarioId' ORDER BY prioridade");		


								//Conta o numero de compromissos que a query retornou
								$registros = mysql_num_rows($sql);			
			
							?>
							<span style="color: #444444"><b>Compromissos para os próximos 7 dias </b><?php echo "(" . $amanha . " a " . $data_sete_dias . ")" ?><b>:&nbsp;<span style='color: #990000'><?php echo $registros ?></span></b></span>
						</td>
					</tr>  
					<?php
    
						//Caso não tenha compromissos então não exibe a linha de cabeçalho.
						if ($registros > 0) 
						{ 
		
							echo "
							<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
							  <td width='20'><div align='center'>&nbsp;A</div></td>
							  <td width='25'><div align='center'>&nbsp;P</div></td>
										<td width='60'>Data</td>
								<td width='37'>Hora</td>
								<td width='750'>Assunto</td>
							</tr>";
						
						}
						
		//Monta e percorre o array dos dados
    while ($dados = mysql_fetch_array($sql)){
		//Alimenta as variáveis com valores vindos do banco
    $month = $dados["mes"];
    $categoria = $dados["categoria"];
    $atividade = $dados["atividade"];
    $prioridade = $dados["prioridade"];

		//Efetua o switch para dar os nomes das categorias
    switch ($categoria) {
        case 01: $cat_name = "<font size='1' face='Tahoma' color=#666666><strong>   (Nenhuma)</strong></font>";	break;
        case 02: $cat_name = "<font size='1' face='Tahoma' color=#CC3300><strong>   (Importante)</strong></font>";	break;
        case 03: $cat_name = "<font size='1' face='Tahoma' color=#6666CC><strong>   (Negócios)</strong></font>";	break;
        case 04: $cat_name = "<font size='1' face='Tahoma' color=#669900><strong>   (Pessoal)</strong></font>";	break;
        case 05: $cat_name = "<font size='1' face='Tahoma' color=#999900><strong>   (Folga)</strong></font>";	break;
        case 06: $cat_name = "<font size='1' face='Tahoma' color=#FF9900><strong>   (Deve ser atendido)</strong></font>";	break;
        case 07: $cat_name = "<font size='1' face='Tahoma' color=#FF00FF><strong>   (Aniversário)</strong></font>";	break;
        case 08: $cat_name = "<font size='1' face='Tahoma' color=#FF3300><strong>   (Ligação Telefônica)</strong></font>    ";	break;
       }

		//Efetua o switch para dar os nomes das atividades       
    switch ($atividade) {
        case 01: $ativ_figura = "<img src='./image/bt_reuniao.gif' alt='Reunião' />";	break;
        case 02: $ativ_figura = "<img src='./image/bt_ligacao.gif' alt='Ligação' />";	break;
        case 03: $ativ_figura = "<img src='./image/bt_compromisso.gif' alt='Compromisso' />";	break;
       }
		//Efetua o switch para dar os nomes das prioridades
    switch ($prioridade) {
        case 01: $prior_figura = "<img src='./image/bt_prior_alta.gif' alt='Alta Prioridade' />";	break;
        case 02: $prior_figura = "<img src='./image/bt_prior_media.gif' alt='Média Prioridade' />";	break;
        case 03: $prior_figura = "<img src='./image/bt_prior_baixa.gif' alt='Baixa Prioridade' />";	break;
       }
    ?>

  <tr height="16" valign='middle'>
    <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style="padding-bottom: 0px">
      <?php echo $ativ_figura ?>
		</td>

    <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style="padding-bottom: 0px">
      <?php echo $prior_figura ?>
		</td>
		
		<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style="padding-bottom: 0px">
		  <span style="color: blue"><?php echo $dados[dia] . "/" . $dados["mes"] . "/" . $dados["ano"] ?></span>
		</td>
		
		<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style="padding-bottom: 0px">
		  <?php echo substr($dados[hora],0,5) ?>
		</td>
				
    <td style="padding-bottom: 0px">
      <font color='#CC3300' size='2' face="Tahoma"><a title="Clique para exibir os detalhes deste compromisso" href="#"  onclick="wdCarregarFormulario('CompromissoExibe.php?CompromissoId=<?php echo $dados[id] ?>','conteudo')"><?php echo $dados['assunto']; ?></a><?php echo $cat_name; ?></font>        
		</td>
  </tr>
  <tr height="10">
    <td style="padding-top: 0px">&nbsp;</td>
		<td style="padding-top: 0px">&nbsp;</td>
		<td style="padding-top: 0px">&nbsp;</td>
		<td style="padding-top: 0px">&nbsp;</td>
    <td style="padding-top: 0px"><font size='1' face='Tahoma'><?php echo $dados['descricao']; ?></td>
  </tr>

  <?php
  //Fecha o while
  }
  ?>
    		
  </table>

</td>
</tr>
</table>
