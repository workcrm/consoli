<?php 
###########
## Módulo para exibição do relógio e do calendário
## Criado: 16/04/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo para exibição do relógio e do calendário
* @author Maycon Edinger
* @copyright 2007 - Maycon Edinger
*/

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET['header'] == 1) {
  //Se precisa usar header (for ajax)
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";


$SQL="SELECT * FROM compromissos WHERE usuario_id = '$usuarioId'";

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
$month = $_GET['Mes'];
$year = $_GET['Ano'];

//Caso o dia venha vazio (sem nada na url)
if (!isset($day)) {
  //Seta as variaveis para os valores do dia padrão da data atual
  $day=date("d",mktime()); 
}

//Caso o mes venha vazio (sem nada na url)
if (!isset($month)) {
  //Seta as variaveis para os valores do dia padrão da data atual
  $month=date("m",mktime());
  }

//Caso o ano venha vazio (sem nada na url)
if (!isset($year)) {
  //Seta as variaveis para os valores do dia padrão da data atual
  $year=date("Y",mktime());
}

if (isset($day)) {
if ($day <= "9"&ereg("(^[1-9]{1})",$day)) {
  $day="0".$day;
  }
}

$thisday="$year-$month-$day";
//Cria o array com as iniciais do dia da semana para a primeira linha do calendário
$day_name=array("S","T","Q","Q","S","S"	,"<font color=\"#990000\">D</font>");
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

$prev = mktime(0,0,0,$month - 1,1,$year); 
$prevano = date("Y",$prev); 
$prevmes = date("m",$prev); 

$d = mktime(0,0,0,$month,1,$year); 
$diaSem = date('w',$d); 
?> 

<table width="100%" border="0" align="left" cellpadding='0' cellspacing='0'>
  <tr>
    <td width="100%" valign="top" align="center"> </td>
  </tr>

  <tr>  
    <td valign="top" align="center">

      <table class="listView" width="164" border="0" align="center" valign="middle" cellpadding="0" cellspacing="0">
        <tr valign="middle" height='20'>
          <td width="20" background="./image/fundo_tabela.gif">
            <div align="center" valign="top">
						  <font size="2" face="Tahoma">
					    <img src="./image/bt_anterior.gif" alt='Exibe o calendário do M&ecirc;s Anterior' border='0' align="middle" onclick="wdCarregarFormulario('ModuloCalendario.php?Mes=<?php echo $prevmes ?>&Ano=<?php echo $prevano ?>&header=1','calendario','1')" style="cursor: hand">
					    </font>          
					  </div>
          </td>
          <td background="./image/fundo_tabela.gif">
            <div align="center">
						<font size="2" face="Tahoma" color='#424542'>
						<b><?php echo "$month_name de $year"; ?></b>
						</font>
            </div>
          </td>
          <td width="20" background="./image/fundo_tabela.gif">
            <div align="center">
		        <font size="2" face="Tahoma">
					  <img src="./image/bt_proximo.gif" alt='Exibe o calendário do Próximo M&ecirc;s' border='0' align="middle" onclick="wdCarregarFormulario('ModuloCalendario.php?Mes=<?php echo $nextmes ?>&Ano=<?php echo $nextano ?>&header=1','calendario','1')" style="cursor: hand"/>
			      </font>          
		        </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td>
      <table bgcolor="#<?php echo $cor_topo ?>" class="listView" width="164" border="0" align="center" cellpadding="1" cellspacing="1">
        <tr align="center">
          <?php for ($i=0;$i<7;$i++) { ?>
          <td width="37" align="center" background="./image/fundo_consulta.gif" bgcolor='#C0C0C0'>
          <b><font size="2" face="Tahoma"><?php echo "$day_name[$i]"; ?></font></b>
          </td>
          <?php } ?>
        </tr>
        <tr  align="center">
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
          <td width='37' bgcolor="#EEEEEE" align="center"><?php $d?></td>
          <?php }

                for($d=1;$d<=date("t",mktime(0,0,0,($month+1),0,$year));$d++)
                {
         		global $linha;
   	            if($month==date("m") && $year==date("Y") && $d==date("d")) {

							//Se for o dia de hoje
              $bg='bgcolor="#'. $cor_diahoje . '"';
          	  $links="<a>";
	            $alinks="</a>";
	            $linkAjax = "";
		          $st="";
	            $sb="";

             	} else {

              //Se for um dia normal...  
 					    $bg='bgcolor="#'. $cor_dianormal . '"';
       	      $links="<a>";
	            $alinks="</a>";
	            $linkAjax = "";
			        $st="";
	            $sb="";

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
	           
	        //Caso o dia conter compromissos   
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
	            } 
				}
             ?>
          <td <?php echo $bg ?> ><font size="2" face="Tahoma"><?php echo $links; ?> <?php echo $st; ?> <?php echo $d;?> <?php echo $sb; ?> <?php echo $alinks; ?></td>
        
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
          </font>
		</tr>
    </table>
  </td>
</tr>

<tr>
  <td valign="top" align="center">
    <table width="164" height="21" border="0" cellpadding="0" cellspacing="0" bordercolor="#666666">
  		<tr>
		  <td height="5" colspan="2"></td>		  
  		</tr>
		<tr>
    	  <td width="14" bgcolor="#<?php echo $cor_compromisso ?>" style="border: solid 1px">&nbsp;</td>
    	  <td align="left">&nbsp;Dia COM compromisso</td>
  		</tr>
  		<tr>
		  <td height="2" colspan="7"></td>		  
  		</tr>  	 
	</table>
  </td>
</tr>
</table>
	