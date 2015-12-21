<?php  
###########
## Módulo para Exibição da Agenda de compromissos
## Criado: 19/04/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

$SQL="SELECT * FROM compromissos WHERE usuario_id = $usuarioId";

$result = mysql_query($SQL);

$linha=mysql_num_rows($result);

$cor_topo='6666CC';

//Aplica a cor padrão
$cor_diahoje = 'FFFFCD';

//Aplica a cor padrão
$cor_compromisso = '9999FF';

//Aplica a cor padrão
$cor_dianormal = 'CDCDCD';


if (isset($show_month)) {
if ($show_month==">") {
  if($month==12) {
     $month=1;
     $year++;
     } else {
     $month++;
     }
     }
if ($show_month=="<") {
  if($month==1) {
     $month=12;
     $year--;
     } else {
     $month--;
     }
     }
}

//Busca os parametros de data caso forem passados pela URL
$day = $_GET['Dia'];
$dia_hoje = $_GET['Dia'];
$month = $_GET['Mes'];
$mes_hoje = $_GET['Mes'];
$year = $_GET['Ano'];
$ano_hoje = $_GET['Ano'];

//Caso o dia venha vazio (sem nada na url)
if (!isset($day)) {
  //Seta as variaveis para os valores do dia padrão da data atual
  $day=date("d",mktime()); 
  $dia_hoje=date("d",mktime()); 
}

//Caso o mes venha vazio (sem nada na url)
if (!isset($month)) {
  //Seta as variaveis para os valores do dia padrão da data atual
  $month=date("m",mktime());
  $mes_hoje=date("m",mktime());
}

//Caso o ano venha vazio (sem nada na url)
if (!isset($year)) {
  //Seta as variaveis para os valores do dia padrão da data atual
  $year=date("Y",mktime());
  $ano_hoje=date("Y",mktime());
}

if (isset($day)) {
if ($day <= "9"&ereg("(^[1-9]{1})",$day)) {
  $day="0".$day;
  }
}

$thisday="$year-$month-$day";
//Cria o array com as iniciais do dia da semana para a primeira linha do calendário
$day_name=array("Segunda","Terça","Quarta","Quinta","Sexta","Sábado","<font color=\"#990000\">Domingo</font>");
//Cria o array com a descrição do mes
$month_abbr=array("","Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

//Cria o switch com a descrição do mes
$y=date("Y");
   switch ($month) {
    case 1:  $month_name = "Janeiro";	break;
    case 2:  $month_name = "Fevereiro";	break;
    case 3:  $month_name = "Março";	break;
    case 4:  $month_name = "Abril";	break;
    case 5:  $month_name = "Maio";	break;
    case 6:  $month_name = "Junho";	break;
    case 7:  $month_name = "Julho";	break;
    case 8:  $month_name = "Agosto";	break;
    case 9:  $month_name = "Setembro";	break;
    case 10: $month_name = "Outubro";	break;
    case 11: $month_name = "Novembro";	break;
    case 12: $month_name = "Dezembro";	break;
   }

$next = mktime(0,0,0,$month + 1,1,$year); 
$nextano = date("Y",$next); 
$nextmes = date("m",$next); 
$nextdia = date("d",$next);

$prev = mktime(0,0,0,$month - 1,1,$year); 
$prevano = date("Y",$prev); 
$prevmes = date("m",$prev); 
$prevdia = date("d",$prev); 

$d = mktime(0,0,0,$month,1,$year); 
$diaSem = date('w',$d);
?> 

<table width="100%" border="0" align="left" cellpadding='0' cellspacing='0'>
  <tr>
    <td valign="top" align="left">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width='100%'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Compromissos</span>
		</td>
	  </tr>
	  <tr>
	    <td colspan='5'>
		    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
		</td>
	  </tr>
	</table>
	
	</td>
  </tr>
  
    <tr>
    <td valign="top" align="center">

    <?php /*Título do calendário*/ ?>

    <table id="2" class="listView" width="100%" border="0" align="center" valign="middle" cellpadding="0" cellspacing="0">
      <tr valign="middle" height="30">
        <td width="30" background="./image/fundo_tabela.gif">
          <div align="center" valign="middle">
		    		<font size="2" face="Tahoma">
					  <img src="./image/bt_anterior_gd.gif" alt='Exibe os Compromissos do M&ecirc;s Anterior' border='0' align="middle" onclick="wdCarregarFormulario('ModuloCompromissos.php?Mes=<?php echo $prevmes ?>&Ano=<?php echo $prevano ?>','conteudo')" style="cursor: hand"/>
					  </font>          
		  		</div>
        </td>
        <td background="./image/fundo_tabela.gif">
          <div align="center">
		    <font size="4" face="Tahoma" color='#424542'>
		    <b><?php echo "Compromissos de $usuarioNome $usuarioSobrenome em $month_name de $year"; ?></b>
		    </font>
          </div>
        </td>
        <td width="30" background="./image/fundo_tabela.gif">
          <div align="center">
		    	<font size="2" face="Tahoma">
					<img src="./image/bt_proximo_gd.gif" alt='Exibe os Compromissos para o Próximo M&ecirc;s' border='0' align="middle" onclick="wdCarregarFormulario('ModuloCompromissos.php?Mes=<?php echo $nextmes ?>&Ano=<?php echo $nextano ?>','conteudo')" style="cursor: hand"/>
		      </font>          
		  </div>
        </td>
      </tr>
    </table>

    <?php /*IMPRESSÃO DA PRIMEIRA LINHA DOS DIAS DA SEMANA*/  ?>
    <table id='3' bgcolor="<?php echo $cor_topo ?>" class="listView" width="100%" border="0" align="center" cellpadding="1" cellspacing="1">
      <tr align="center">
        <?php for ($i=0;$i<7;$i++) { ?>
       
	      <td width="14%" height="30" align="center" background="./image/fundo_consulta.gif">
		
          <b><div align="center"><font size="2" face="Tahoma">
		    <?php echo "$day_name[$i]"; ?>
		    </font></div></b>
		    </td>
        <?php } ?>
      </tr>
      <tr align="center" height="70">
        <?php

          if (date("w",mktime(0,0,0,$month,1,$year))==0) {
            $start=7;
          } else {
            $start=date ("w",mktime(0,0,0,$month,1,$year));
          }
            for($a=($start-2);$a>=0;$a--)
          {
            $d=date("t",mktime(0,0,0,$month,0,$year))-$a;
         ?>
        
		<td width="14%" bgcolor="#EEEEEE" align="center"><?php $d?></td>
        
		<?php }

        for($d=1;$d<=date("t",mktime(0,0,0,($month+1),0,$year));$d++)
                {
        global $linha;
        
        if($month==date("m",mktime()) && $year==date("Y",mktime()) && $d==date("d",mktime())) {

  				//Se for o dia de hoje
          $bg='bgcolor="#'. $cor_diahoje . '"';
  				echo 
          $links="<a>";
  	      $alinks="</a>";
  		    $st="";
  	      $sb="";
  				$hoje="<img src='image/bt_hoje.gif' alt='Hoje' />";
  				$aviso="";

       	 } else {
 
         //Se for um dia normal...
			   $bg='bgcolor="#'. $cor_dianormal . '"';
       	 $links="<a>";
	       $alinks="</a>";
		     $st="";
	       $sb="";
			   $hoje="";
			   $aviso="";

	       }

         for ($i=0;$i<$linha;$i++){
	           global $month,$year,$d;
	           $id_sql=mysql_result($result,$i,'id');
             $dia_sql=mysql_result($result,$i,'dia');
	           $mes_sql=mysql_result($result,$i,'mes');
	           $ano_sql=mysql_result($result,$i,'ano');
             $id = ltrim(rtrim($id_sql));
	           $ano = ltrim(rtrim($ano_sql));
	           $mes = ltrim(rtrim($mes_sql));
	           $dia = ltrim(rtrim($dia_sql));
			   
			   //Caso o dia tenha compromisso
	       if($d==$dia&$year==$ano&$month==$mes) {	           
	            //Alimenta a variável da cor do fundo da tabela do dia com compromisso
							$bg='bgcolor="#'. $cor_compromisso . '"';
							//Alimenta a variável contendo o link para aparecer a "maozinha"
	            $links="<a title=\"Exibir compromissos desta data\" href=\"#\" onclick=\"wdCarregarFormulario('ModuloCompromissos.php?Dia=$dia&Mes=$mes&Ano=$ano','conteudo')\">";
							//Alimenta a variável para fechar a tag de link
	            $alinks="</a>";
	            //Alimenta a variável de negrito
	            $st="<strong>";
	            //Alimenta a variável de remove negrito
	            $sb="</strong>";
							//Exibe a figura contendo o aviso apra clicar e exibir os compromissos
	            $aviso="<img src='image/dir_cadastro.gif' alt='Clique sobre o dia para exibir os compromissos desta data' />";
	           } 
			   
			   }
             ?>
			 
			 
          <td <?php echo $bg ?> height='100' width='86' valign="top">
			
          <?php 
		      //Faz a pesquisa para verificar os compromissos desse dia
		  
					$consultaCompromissos = "SELECT * FROM compromissos WHERE usuario_id = $usuarioId AND dia = $d AND mes = $month AND ano = $year ORDER BY hora";
    
	        $listagemCompromissos = mysql_query($consultaCompromissos);
	        $contaCompromissos = mysql_num_rows($listagemCompromissos);  
		
		  ?>
		  
		  
		  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="1">
			<tr valign="top" width="15">

			<?php /*Cabeçalho dos dias*/ ?>
			  <td width=25% height="20">
		  		<font size="3" face="Tahoma"><strong>
		    	<?php echo $links; ?> <?php echo $st; ?> <?php echo $d;?> <?php echo $sb; ?> <?php echo $alinks; ?>			  
			  </strong></td>
			  <td width=25% valign='middle' align='left'>			  
				<?php echo $aviso; ?>
			  </td>		
			  <td width=25%>		  
				<?php /*Essa célula fica vazia*/ ?>
			  </td>				  		
			  <td width=25%>
			    <?php echo $hoje; ?>
			  </td>			  	
			</tr>

			<?php /*Linha 2 de compromissos*/ ?>
			<tr valign="top" width="15">
			  <td align="left" colspan="4">

		      <?php
  		    //Exibe os compromissos na celula do dia
				  while ($dadosCompromissos = mysql_fetch_array($listagemCompromissos)){
		
		  		  $atividade = $dadosCompromissos["atividade"];
	      		$assunto = $dadosCompromissos["assunto"];
	      		$hora = substr($dadosCompromissos["hora"],0,5);
	      		$prioridade = $dadosCompromissos["prioridade"];
	  
          		switch ($prioridade) {
            		case 01: $prior_desc = "Alta"; break;
            		case 02: $prior_desc = "Média"; break;
            		case 03: $prior_desc = "Baixa"; break;
	      		}
     	  
          		switch ($atividade) {
            		case 01: $ativ_figura = "<img src='./image/bt_reuniao.gif' alt='Reunião: ($hora) - Prioridade: $prior_desc \n$assunto \n(Clique para exibir os detalhes deste compromisso)' border='0' align='middle' onclick=\"wdCarregarFormulario('CompromissoExibe.php?CompromissoId=$dadosCompromissos[id]','conteudo')\" style='cursor: hand'/>&nbsp;"; break;
            		case 02: $ativ_figura = "<img src='./image/bt_ligacao.gif' alt='Ligação: ($hora) - Prioridade: $prior_desc \n$assunto \n(Clique para exibir os detalhes deste compromisso)' border='0' align='middle' onclick=\"wdCarregarFormulario('CompromissoExibe.php?CompromissoId=$dadosCompromissos[id]','conteudo')\" style='cursor: hand'/>&nbsp;"; break;
            		case 03: $ativ_figura = "<img src='./image/bt_compromisso.gif' alt='Compromisso: ($hora) - Prioridade: $prior_desc \n$assunto \n(Clique para exibir os detalhes deste compromisso)' border='0' align='middle' onclick=\"wdCarregarFormulario('CompromissoExibe.php?CompromissoId=$dadosCompromissos[id]','conteudo')\" style='cursor: hand'/>&nbsp;"; break;
          		}
                     
				//Exibe o icone do compromisso com os dados
				echo $ativ_figura;  } ?>			
			
			  </td>
			  
			</tr>									
		  </table>
		</td>
        
		<?php        
		  if(date("w",mktime(0,0,0,$month,$d,$year))==0&date("t",mktime(0,0,0,($month+1),0,$year))>$d)
            {
            ?>

      </tr>

      <tr align="center">
        <?php   }}
             $da=$d+1;
             if(date("w",mktime(0,0,0,$month+1,1,$year))<>1)
             {
             $d=1;
             while(date("w",mktime(0,0,0,($month+1),$d,$year))<>1)
                  {
            ?>

        <td bgcolor="#EEEEEE" align="center">
		  <?php $d?>
		</td>
        
		<?php
            $d++;
                  }
             }
         ?>
      </tr>

    </table>
	</td>
  </tr>
  <tr>
    <td valign="top" align="center">
      <table id="5" width="100%" height="21" border="0" cellpadding="0" cellspacing="0" bordercolor="#666666">
  		<tr>
		  <td height="5" colspan="4"></td>		  
  		</tr>
		<tr>
    	  <td width="18" bgcolor="#<?php echo $cor_compromisso ?>" style="border: solid 1px">&nbsp;</td>
    	  <td width="140" align="left">&nbsp;Dia COM compromisso</td>
    	  <td width="18" bgcolor="#<?php echo $cor_diahoje ?>" style="border: solid 1px"><img src="./image/bt_hoje.gif" />				</td>
    	  <td align="left">&nbsp;Hoje</td>
  		</tr>
  		<tr>
		  <td height="15" colspan="7"></td>		  
  		</tr>
	  </table>
	</td>
  </tr>
  
  
  <tr>
    <td valign="top">

    <?php /*Título do calendário*/ ?>

    <table id='2' class="listView" width="100%" border="0" valign="middle" cellpadding="0" cellspacing="0">
      <tr valign="middle" height='30'>
        <td background="./image/fundo_tabela.gif">
          <div align="center">
		    <font size="4" face="Tahoma" color='#424542'>
		    <b><?php echo "Agendamento diário para $day de $month_name de $year"; ?></b>
		    </font>
          </div>
        </td>
      </tr>
    </table>
		</td>
	</tr>	

	<tr>
  	<td>

    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="listView" id='9'>
      <tr bgcolor="#9CB6CE">

		
        <?php 
		//Rotina para pesquisar no banco e retornar os compromissos por hora
		for ($linhaHora = 00; $linhaHora < 24; $linhaHora++) {

		if ($linhaHora < 10) {
		  $horaPesquisa = '0' . $linhaHora;
		} else {
		  $horaPesquisa = $linhaHora;
		}

		$consultaHora = "SELECT * FROM compromissos WHERE usuario_id = $usuarioId AND dia = $dia_hoje AND mes = $mes_hoje AND ano = $ano_hoje AND hora LIKE '$horaPesquisa%' ORDER BY hora";
		
		//Verifica se o sistema não está bloqueado
		if ($bloqueio == '1') {
			//Define a variável de total de registros pra simular uma consulta zerada
			$tot_regs = 0;
		  	
		 	//Gera um erro maluco
		 	echo "</br><b>mysql_error:<b> <i>could not perform query. Please verify if the database engine system is up and running. Fatal error (0208)</br></br>";

	  } else {
			
			$listagemHora = mysql_query($consultaHora);	  
	
		}
		?>	  	  	    

		<td width='80' height="20" bgcolor="#9CB6CE" style="border-bottom: 1px solid">
          <div align="center"><font size="2" face="Tahoma"><strong><?php echo $horaPesquisa ?>:00</strong></font></div>
        </td>

		<td bgcolor="#FFFFFF" style="border-bottom: 1px solid">

		<?php /*Monta o início da tabela interna dos compromissos da hora*/ ?>
		<table width='100%' border='0' cellpadding='0' cellspacing='0'>
    &nbsp;
		
		<?php while ($dadosHora = mysql_fetch_array($listagemHora)){
		
  		$atividade = $dadosHora["atividade"];
  	    $assunto = $dadosHora["assunto"];
  	    $hora = substr($dadosHora["hora"],0,5);
  	    $prioridade = $dadosHora["prioridade"];
  	  
          switch ($prioridade) {
           case 01: $prior_desc = "Alta";	
  		 		  $prior_figura = "<img src='./image/bt_prior_alta.gif' alt='Alta Prioridade' />";break;
           case 02: $prior_desc = "Média";	
  		 		  $prior_figura = "<img src='./image/bt_prior_media.gif' alt='Média Prioridade' />";break;
           case 03: $prior_desc = "Baixa";	
  		 		  $prior_figura = "<img src='./image/bt_prior_baixa.gif' alt='Baixa Prioridade' />";break;
  	    }
       	  
          switch ($atividade) {
           case 01: $ativ_figura = "<img valign='middle' src='./image/bt_reuniao.gif' border='0' alt='Reunião' />";	
  		 break;
           case 02: $ativ_figura = "<img valign='middle' src='./image/bt_ligacao.gif' border='0' alt='Ligação' />";	
  		 break;
           case 03:  $ativ_figura = "<img valign='middle' src='./image/bt_compromisso.gif' border='0' alt='Compromisso' />";	
  		 break;
          }
        
        //Monta o restante do conteudo da tabela aberta lá em cima
        
        $hora_curta = substr($dadosHora[hora], 0, 5);

		echo "	
      	  <tr valign='middle'>
      	    <td>
			&nbsp; $ativ_figura &nbsp; $prior_figura <font size='1'>($hora_curta)</font><font size='2' face='Tahoma'> - <a title='Clique para exibir os detalhes deste compromisso' href='#' onclick=\"wdCarregarFormulario('CompromissoExibe.php?CompromissoId=$dadosHora[id]','conteudo')\">$dadosHora[assunto]</a></font>  
			</td>
		  </tr>";						
		}
		?>
		<?php /*Fecha a tabela para exibição dos compromissos*/ ?>		  
      	</table>

		</td>
      </tr>

	  <?php 
	}
	?>

	</font>
    </table>

  </td>
</tr>

</table>
  
