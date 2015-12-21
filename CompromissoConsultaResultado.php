<?php 
###########
## Módulo para Listagem dos compromissos por data
## Criado: 29/01/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
## Exibir a listagem de compromissos com 7 dias de antecedência
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) {
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

$data_inicio = $_GET["DataIni"];
$data_termino = $_GET["DataFim"];

$data_inicio_consulta = DataMySQLInserir($_GET["DataIni"]);
$data_termino_consulta = DataMySQLInserir($_GET["DataFim"]);

//Monta e executa a query para buscar os eventos para os próximos 7 dias
$sql = mysql_query("SELECT * FROM compromissos WHERE data >= '$data_inicio_consulta' AND data <= '$data_termino_consulta' AND usuario_id = '$usuarioId' ORDER BY data, hora, prioridade");	
			
//Conta o numero de compromissos que a query retornou
$registros = mysql_num_rows($sql);
?>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
    <td style="padding-top: 14px">
    <?php
				//Armazena o mês atual na variável
        $mes_inicio = substr($data_inicio_consulta,5,2);			    
        
				//Efetua o switch para determinar o nome do mes atual
        switch ($mes_inicio) {
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
        
        //Armazena o mês de consulta final
        $mes_termino = substr($data_termino_consulta,5,2);        
        
				//Efetua o switch para determinar o nome do mes atual
        switch ($mes_termino) {
          case 1: $mes_nome_fim = "Janeiro";	break;
          case 2: $mes_nome_fim = "Fevereiro";	break;
          case 3: $mes_nome_fim = "Março";	break;
          case 4: $mes_nome_fim = "Abril";	break;
          case 5: $mes_nome_fim = "Maio";	break;
          case 6: $mes_nome_fim = "Junho";	break;
          case 7: $mes_nome_fim = "Julho";	break;
          case 8: $mes_nome_fim = "Agosto";	break;
          case 9: $mes_nome_fim = "Setembro";	break;
          case 10: $mes_nome_fim = "Outubro";	break;
          case 11: $mes_nome_fim = "Novembro";	break;
          case 12: $mes_nome_fim = "Dezembro";	break;
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
							
								if ($registros == 0) {
									
									$str_titulo = "compromissos";	
									$mensagem_regs = "Você não possui ";
								
								} else {
									
									if ($registros > 1) {
										$str_titulo = "compromissos";
									} else {
										$str_titulo = "compromisso";
									}
									
									$mensagem_regs = "Você possui <span style='color: #990000'>$registros </span>";
								} 
							
							?>
	  					<span style="font-size: 12px; color: #444444"><b><?php echo $mensagem_regs . " " . $str_titulo ?> entre <span style='color: #990000'><?php echo substr($data_inicio_consulta,8,2); ?> de <?php echo $mes_nome; ?> de <?php echo substr($data_inicio_consulta,0,4); ?></span> e <span style='color: #990000'><?php echo substr($data_termino_consulta,8,2); ?> de <?php echo $mes_nome_fim; ?> de <?php echo substr($data_termino_consulta,0,4); ?></b></span>
	  				</td>
	  			</tr>
	  		</table>
    </td>
  </tr>

  <?php
    //Caso não tenha compromissos então não exibe a linha de cabeçalho.
    if ($registros > 0) { 
      echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          <td width='20'><div align='center'>&nbsp;A</div></td>
          <td width='25'><div align='center'>&nbsp;P</div></td>
	      	<td width='54'>Data</td>
	      	<td width='37'>Hora</td>
	      	<td width='740' colspan='2'>Assunto</td>
        </tr>
    			";
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
		  <?php echo DataMySQLRetornar($dados[data]) ?>
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
  
</td>
</tr>
</table>
