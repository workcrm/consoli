<?php
###########
## Módulo para Listagem dos retornos por data
## Criado: 03/02/2011 - Maycon Edinger
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

//Verifica se a funcão já foi declarada
if (function_exists('DataMySQLRetornar') == false) 
{
	
  //Inclui o arquivo para manipulação de datas
	include './include/ManipulaDatas.php';

}

//Captura o evento informado
$DataIni = DataMySQLInserir($_GET['DataIni']);
$DataFim = DataMySQLInserir($_GET['DataFim']);
$edtTipoConsulta = $_GET["TipoConsulta"];
 
switch ($edtTipoConsulta) 
{
  
  case 0: 
    $desc_situacao = "<span style='color: #990000'>Todos</span>"; 
    $where_tipo = "";
  break;		  
  case 1: 
    $desc_situacao = "<span style='color: #6666CC'>Títulos Processados</span>";
    $where_tipo = "AND proc.status = 1";
  break;
  case 2: 
    $desc_situacao = "<span style='color: #6666CC'>Titulos Não Processados - Juros</span>";
    $where_tipo = "AND proc.status = 2";
  break;
  case 3: 
    $desc_situacao = "<span style='color: #6666CC'>Títulos Não encontrados</span>";
    $where_tipo = "AND proc.status = 3";
  break;
}

//Busca no banco de dados se existe o boleto
//Monta a query para pegar os dados
$sql = "SELECT 
        proc.data_processamento,
        proc.titulo,
        proc.valor,
        proc.juros,
        proc.status,
        bol.sacado AS sacado
        FROM retornos proc
        LEFT OUTER JOIN boleto bol ON bol.nosso_numero = proc.titulo
				WHERE proc.data_processamento >= '$DataIni' AND proc.data_processamento <= '$DataFim' $where_tipo
				ORDER BY proc.status, sacado";

//echo $sql . "<br/>";

//Executa a query
$query = mysql_query($sql);

//Conta o numero de registros da query
$registros = mysql_num_rows($query);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css" />

<br/>

<?php 

//Verifica se há registros
if ($registros == 0)
{

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="24" style="border: 1px #444444 solid; padding-left: 8px">
      <span style="font-size: 12px;"><b>Não há retornos para o período e tipo especificado !</b></span>
    </td>
  </tr>
</table>
  
<?php 

} 

//Caso tenha registros
else 

{
        
  //Define o status zero para fazer a quebra
  $status_atual = 0;
    
  while ($dados = mysql_fetch_array($query))
  {  
    
    switch ($dados["status"]) 
    {
      		  
      case 1: 
        $desc_situacao = "<span style='color: #6666CC'>Títulos Processados</span>";
      break;
      case 2: 
        $desc_situacao = "<span style='color: #6666CC'>Titulos Não Processados - Juros</span>";
      break;
      case 3: 
        $desc_situacao = "<span style='color: #6666CC'>Títulos Não encontrados</span>";
      break;
    }
    
    
    if ($status_atual != $dados["status"])
    {      
      
      if ($status_atual > 0)
      {
        
        $subtotal_geral =  "R$ " . number_format($soma_subtotal, 2, ",", ".");
        $subtotal_geral_juros =  "R$ " . number_format($soma_juros_subtotal, 2, ",", ".");
  
        echo "
        <tr height='24' class='listViewThS1' background='image/fundo_consulta.gif'>  			
          <td colspan='3' align='right' style='padding-right: 5px; border-bottom: 1px solid'>
            <font size='2' face='Tahoma' ><b>
        		  Subtotal:
        		<b></font>     
        	</td>	
        	<td style='border-bottom: 1px solid' align='right'>
        		$subtotal_geral			
        	</td>			
          <td style='border-bottom: 1px solid; padding-right: 6px' align='right'>
            $subtotal_geral_juros
        	</td>
        </tr>";
        
        $soma_subtotal = 0;
        $soma_juros_subtotal = 0;
  
      }
      
      echo "</table>
            <br/>
            <span class='TituloModulo'>$desc_situacao</span>
            <br/>
            <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
              <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>                
                <td width='60' align='center'>Data</td>
                <td width='116'>Nosso Número</td>
              	<td>Dados do Sacado</td>          
                <td width='80' align='right'>Valor</td>
                <td width='60' align='right' style='padding-right: 6px'>Juros</td>        
              </tr>";
    
    }
            

?>
               
  <tr height="24">
  	<td style="border-bottom: 1px solid" align="center">
  		<?php echo DataMySQLRetornar($dados["data_processamento"]) ?>				
  	</td>
    <td style="border-bottom: 1px solid">
  		<span style="color: #6666CC"><?php echo $dados["titulo"] ?></span>		
  	</td>      			
    <td style="border-bottom: 1px solid">
      <font size="2" face="Tahoma"><b>
  		  <?php 
        
          if ($dados["sacado"] != "")
          {
            
            echo $dados["sacado"]; 
          
          }
          
          else
          
          {
            
            echo "&nbsp;";
          
          }
            
        ?>
  		<b></font>     
  	</td>	
  	<td style="border-bottom: 1px solid" align="right">
  		<?php 
  			echo "R$ " . number_format($dados["valor"], 2, ",", ".");
  		?>			
  	</td>			
    <td style="border-bottom: 1px solid; padding-right: 6px" align="right">
      <?php
        
        if ($dados["juros"] > 0)
        {
          
  			   echo "<span style='color: #990000'>R$ " . number_format($dados["juros"], 2, ",", ".") . "</span>";
           
        }
        
        else
        
        {
          echo "&nbsp;";
          
        }
        
  		?>
  	</td>
  </tr>

<?php

      $soma_total = $soma_total + $dados["valor"];
      $soma_subtotal = $soma_subtotal + $dados["valor"];
      
      $soma_juros_total = $soma_juros_total + $dados["juros"];
      $soma_juros_subtotal = $soma_juros_subtotal + $dados["juros"];
      
      $status_atual = $dados["status"];
  
  }
 
  $total_geral =  "R$ " . number_format($soma_total, 2, ",", ".");
  $total_geral_juros =  "R$ " . number_format($soma_juros_total, 2, ",", ".");
  
  $subtotal_geral =  "R$ " . number_format($soma_subtotal, 2, ",", ".");
  $subtotal_geral_juros =  "R$ " . number_format($soma_juros_subtotal, 2, ",", ".");
  
  if ($edtTipoConsulta == "0")
  {
    
    echo "
    <tr height='24' class='listViewThS1' background='image/fundo_consulta.gif'>  			
      <td colspan='3' align='right' style='padding-right: 5px; border-bottom: 1px solid'>
        <font size='2' face='Tahoma' ><b>
    		  Subtotal:
    		<b></font>     
    	</td>	
    	<td style='border-bottom: 1px solid' align='right'>
    		$subtotal_geral			
    	</td>			
      <td style='border-bottom: 1px solid; padding-right: 6px' align='right'>
        $subtotal_geral_juros
    	</td>
    </tr>";
  
  }
  
  echo "
  <tr height='24' class='listViewThS1' background='image/fundo_consulta.gif'>  			
    <td colspan='3' align='right' style='padding-right: 5px; border-bottom: 1px solid'>
      <font size='2' face='Tahoma' ><b>
  		  Total Geral:
  		<b></font>     
  	</td>	
  	<td style='border-bottom: 1px solid' align='right'>
  		$total_geral			
  	</td>			
    <td style='border-bottom: 1px solid; padding-right: 6px' align='right'>
      $total_geral_juros
  	</td>
  </tr>";
  
}
?>          

</table>