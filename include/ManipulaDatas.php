<?php
###########
## Módulo de Funções de Manipulação de Datas
## Criado: 06/02/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Converte a data de normal para mysql
function DataMySQLInserir($DATA)
{
	
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $data_array = split("/",$DATA);

  if ($DATA <> "")
  {

    $DIA = $data_array[0];
    $MES = $data_array[1];
    $ANO = $data_array[2];
    return $ANO."-".$MES."-".$DIA;

  }

  else

  {

    $ANO = 0000;
    $MES = 00;
    $DIA = 00;
    return $ANO."-".$MES."-".$DIA;

  }

} 

//Converte a data de mysql para normal
function DataMySQLRetornar($DATA)
{
	
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $data_array = split("-",$DATA);
	
  if ($DATA <> "")
  {

    $ANO = $data_array[0];
    $MES = $data_array[1];
    $DIA = $data_array[2];
    return $DIA."/".$MES."/".$ANO;

  }

  else 

  {

    $ANO = 0000;
    $MES = 00;
    $DIA = 00;
    return $DIA."/".$MES."/".$ANO;

  }

}

//Cria a função que permite a soma entre datas
function som_data($data, $dias) 
{
	
  $data_e = explode("/",$data);
  $data2 = date("m/d/Y", mktime(0,0,0,$data_e[1],$data_e[0] + $dias,$data_e[2]));
  $data2_e = explode("/",$data2);
  $data_final = $data2_e[1] . "/". $data2_e[0] . "/" . $data2_e[2];
  return $data_final;

}


//Subtrai dias de uma data
function subDias($date,$days) 
{
	
  $thisyear = substr ( $date, 0, 4 );
  $thismonth = substr ( $date, 5, 2 );
  $thisday =  substr ( $date, 8, 2 );
  $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday - $days, $thisyear );
  return strftime("%Y-%m-%d", $nextdate);

}

//Função para cálculos entre datas
function diffDate($d1, $d2, $type='D', $sep='-')
{
  
  //Datas no padrão americano. Se quiser passar outro separador informar na var SEP
  $d1 = explode($sep, $d1);
  $d2 = explode($sep, $d2);
  
  switch ($type)
  {

    //Retorna o número de ANOS entre as datas
    case 'A':
      $X = 31536000;
    break;
    //Retorna o número de MESES entre as datas
    case 'M':
      $X = 2592000;
    break;
    //Retorna o número de DIAS entre as datas
    case 'D':
      $X = 86400;
    break;
    //Retorna o número de HORAS entre as datas
    case 'H':
      $X = 3600;
    break;
    //Retorna o número de MINUTOS entre as datas
    case 'MI':
      $X = 60;
    break;
    //Retorna o número de SEGUNDOS (default) entre as datas
    default:
      $X = 1;
    break;
  }

  return floor((mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]) - mktime(0, 0, 0, $d1[1], $d1[2], $d1[0] )) / $X);

  //return floor( ( ( mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]) – mktime(0, 0, 0, $d1[1], $d1[2], $d1[0] ) ) / $X ) );

}
?>