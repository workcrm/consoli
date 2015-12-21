<?php
###########
## Módulo para processamento dos boletos vindos do arquivo de retorno
## Criado: 20/01/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Processa as diretivas de segurança 
require('Diretivas.php');
	
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

$dataProcessa = DataMySQLInserir($_GET["DataProcessa"]);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Processamento de Arquivos de Retorno</span></td>
  </tr>
  <tr>
    <td colspan='5'>
	    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
	  </td>
  </tr>
  <tr>
    <td colspan='5'>
	    <span style="font-size: 12px"><b>RESULTADO DO PROCESSAMENTO DOS BOLETOS DO ARQUIVO DE RETORNO:</b></span><br/><br/>
	  </td>
  </tr>
</table>
      
<?php
function ParseFloat($floatString){ 
    $LocaleInfo = localeconv(); 
    $floatString = str_replace("." , "", $floatString); 
    $floatString = str_replace("," , ".", $floatString); 
    return floatval($floatString); 
} 
  
//Caso tiver um nome de arquivo, armazena numa variável
$arq = $_GET["Arquivo"];

//Diretório onde a imagem será salva
$pasta = "documentos/". $arq;

//Faz o upload da imagem
//move_uploaded_file($arquivo["tmp_name"], $pasta);

//tenta abrir o arquivo de retorno do banco
if(!$arrArquivo = file($pasta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
        exit("Não foi possível abrir o arquivo de retorno do banco!<br/><input class='button' value='Voltar' type='button' name='btVoltar' onclick='wdCarregarFormulario(\"ModuloRetorno.php\",\"conteudo\")' style='width: 100px' />");
        
//linha atual
$i = 0;
$linha = 1;

//retira de um vez as quebras de linhas, retorno de carros e linhas vazias
foreach($arrArquivo as &$linhas)
{
        
  //retira \r
  $linhas = str_replace(chr(13), "", $linhas);
  
  //retira \n
  $linhas = str_replace(chr(10), "", $linhas);
  
  //apaga as linhas vazias
  if(empty($linhas))
          unset($arrArquivo[$i]);
          
  //proxima linha
  $i++;
    
  if ($linha > 9)
  {
  
    $comeco = substr($linhas, 3, 5);
    
    //Veririfica se a linha é um boleto
    if ($comeco == "15209")
    {
        
      //Monta a string do boleto
      $numero_boleto = substr($linhas, 3, 17);                  
      
      //Monta a string do valor
      $valor_float = substr($linhas, 95, 11);
      
      //Converte a string para um decimal
      $valor_boleto = ParseFloat($valor_float);
      $valor_boleto_formata = number_format(ParseFloat($valor_float), 2,",",".");
      
      //Monta a string do valor dos juros
      $valor_juros_float = substr($linhas, 115, 10);
      
      //Converte a string para um decimal
      $valor_juros_boleto = number_format(ParseFloat($valor_juros_float), 2,",",".");                    
        
                 
      //Busca no banco de dados se existe o boleto
      //Monta a query para pegar os dados
      $sql_cli = "SELECT * FROM boleto WHERE nosso_numero = '$numero_boleto' ORDER BY sacado";
    
      //Executa a query
      $query_cli = mysql_query($sql_cli);
      
      //Conta o numero de registros da query
      $registros_cli = mysql_num_rows($query_cli);
        
      //Caso não houver registros
      if ($registros_cli == 0) 
      {
      	
        $string_boletos_antigos .= "<br/>Boleto <b>$numero_boleto</b> BOLETO NÃO ENCONTRADO - ANTIGOS - REGISTRO BB - NÃO SERÁ PROCESSADO !";
        
        //Gera um lançamento na base de retornos processados com o status de não encontrado (3)
        $sql = "INSERT INTO retornos (
        			data_processamento,
        			titulo, 
        			valor,
        			status
        			) values (
        			'$dataProcessa',
        			'$numero_boleto',
        			'$valor_boleto',
        			'3'
        			);";
        
        mysql_query($sql);
        
      
      } 
    
      else 
    
      {				
        
        //efetua o loop na pesquisa
      	while ($dados_rec = mysql_fetch_array($query_cli))
        {		
        		
          if ($valor_juros_boleto == "0,00")
          {
          
            //Verifica se o boleto possui o mesmo valor cadastrado do que o valor do retorno
            if ($dados_rec["valor_boleto"] == $valor_boleto)
            {
            
              $contaId = $dados_rec["conta_receber_id"];
                
              $string_boletos_ok .= "<br/>Boleto <b>$numero_boleto</b> PROCESSADO COM SUCESSO !";
            
            
              $sql = "UPDATE boleto SET 
                      boleto_recebido = 1,
                      data_recebimento = '$dataProcessa',
                      valor_recebido = '$valor_boleto',
                      obs_recebimento = 'Recebemos via boleto na data  o valor de R$ $valor_boleto_formata',
                      usuario_recebimento_id = $usuarioId
                      WHERE nosso_numero = '$numero_boleto'";
               
               //echo $sql . "<br/><br/>";
               
               mysql_query($sql); 
              
              
               //Gera um lançamento de recebimento para a conta a receber vinculada
               $sql = "INSERT INTO contas_receber_recebimento (
              				conta_receber_id,
              				data_recebimento, 
              				tipo_recebimento,
              				total_recebido,
              				obs 
              				) values (				
              				'$contaId',
              				'$dataProcessa',
              				'4',
              				'$valor_boleto',
              				'Recebemos via boleto na data  o valor de R$ $valor_boleto_formata'
              				);";
              
               //echo $sql . "<br/><br/>";
              
               mysql_query($sql);            
                              
            
               //Efetua a baixa da conta a receber vinculada
  						 $sql = "UPDATE contas_receber SET 
                      situacao = 2, 
                      valor_recebido = '$valor_boleto'
                      WHERE id = $contaId";
                      
               //echo $sql . "<br/>";
                
               mysql_query($sql);
               
               //Gera um lançamento na base de retornos processados com o status de processado (1)
               $sql = "INSERT INTO retornos (
              				data_processamento,
              				titulo, 
              				valor,
              				status
              				) values (
              				'$dataProcessa',
              				'$numero_boleto',
              				'$valor_boleto',
              				'1'
              				);";
              
               mysql_query($sql); 
               
              }
      					
      	   }
           
           else
           
           {
            
             $string_boletos_juros .= "<br/>Boleto <b>$numero_boleto</b> COM JUROS - PROCESSO MANUAL NECESSÁRIO";
             
             $valor_juros_banco = MoneyMySQLInserir($valor_juros_boleto);
             
             //Gera um lançamento na base de retornos processados com o status de com juros (2)
             $sql = "INSERT INTO retornos (
            				data_processamento,
            				titulo, 
            				valor,
                    juros,
            				status
            				) values (
            				'$dataProcessa',
            				'$numero_boleto',
            				'$valor_boleto',
                    '$valor_juros_banco',
            				'2'
            				);";
            
             mysql_query($sql);
            
           }
                         
        }                                                               
    
      }
    
    }
    
  }
    
  //Pula a linha no txt
  $linha++;

}

?>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td height='22' width='20' valign="top" bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
      <img src='./image/bt_informacao.gif' border='0' />
    </td>
    <td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
      <?php 
      
        echo "<span style='color: #990000'><b>BOLETOS QUITADOS:</b></span>";
        echo $string_boletos_ok . "<br/>"; 
        
        echo "<br/><span style='color: #990000'><b>BOLETOS COM JUROS E QUE NÃO FORAM QUITADOS:</b></span>";
        echo $string_boletos_juros . "<br/>";
        
        echo "<br/><span style='color: #990000'><b>BOLETOS NÃO ENCONTRADOS - ANTIGOS - REGISTRO BB:</b></span>";
        echo $string_boletos_antigos . "<br/>"; 
      
      ?>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
