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

$dataProcessa = DataMySQLInserir($_GET["DataProcessa"]);
  
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
$linha_conta = 1;

$pula_boleto = 0;

//retira de um vez as quebras de linhas, retorno de carros e linhas vazias
foreach($arrArquivo as &$linhas_conta)
{
  
  //retira \r
  $linhas = str_replace(chr(13), "", $linhas);
  
  //retira \n
  $linhas = str_replace(chr(10), "", $linhas);
  
  $linha_conta++;
  
}

$linha_conta = $linha_conta - 3;
   
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
    
    
    
    if ($linha > 2 AND $linha < $linhas_conta)
    {

      $comeco = substr($linhas, 37, 5);
      
      if ($comeco == "15209")
      {
        
        //Monta a string do boleto
        $numero_boleto = substr($linhas, 37, 17);
        
        //Monta a string do valor
        $valor_float = floatval(substr($linhas, 81, 15));
        
        $frente = substr($valor_float, 0, -2);
        
        $decimal = substr($valor_float,(strlen($valor_float)-2),strlen($valor_float));
        
        
        $valor_ok =  (float)$frente . "." . $decimal . "      - ";
        
        $valor_boleto = number_format($valor_ok, 2,".","");
        $valor_boleto_formata = number_format($valor_ok, 2,",",".");
        
        //Verifica se o numero do boleto não está na linha impar
        if ($numero_boleto != "00000000000000000")
        {
            
          //Busca no banco de dados se existe o boleto
          //Monta a query para pegar os dados
          $sql_cli = "SELECT * FROM boleto
          						WHERE nosso_numero = '$numero_boleto'";

          //Executa a query
          $query_cli = mysql_query($sql_cli);
          
          //Conta o numero de registros da query
          $registros_cli = mysql_num_rows($query_cli);
          
          //Caso não houver registros
          if ($registros_cli > 0) 
          {			
          
            $processa_boleto = 1;
          	
            //efetua o loop na pesquisa
          	while ($dados_rec = mysql_fetch_array($query_cli))
            {		
          		              
          		$contaId = $dados_rec["conta_receber_id"];
              			
          	}
           
           }
            
            else
            
            {
              
              $string_boletos_antigos .= "<br/>Boleto <b>$numero_boleto</b> ANTIGOS - REGISTRO BB - NÃO SERÁ PROCESSADO !";
              
              $processa_boleto = 0;
              
            }
                   
          }
                      
        
        
      }
        
      else
      
      {
      
        //Monta a string do valor
        $juros_float = floatval(substr($linhas, 25, 7));
        
        $juros_frente = substr($juros_float, 0, -2);
        
        $juros_decimal = substr($juros_float,(strlen($juros_float)-2),strlen($juros_float));
        
        $juros_ok =  (float)$juros_frente . "." . $juros_decimal . "      - ";
        
        $juros_valor_boleto = number_format($juros_ok, 2,",",".");
      
      }
      
    
    //Verifica se a linha é par
    if (($linha % 2 ) == 0) 
    {
      
      if ($juros_valor_boleto == 0)
      {
      
          if ($processa_boleto == 1)
          {
      
            $string_boletos_ok .= "<br/>Boleto <b>$numero_boleto</b> PROCESSADO COM SUCESSO !";
            
            
            $sql = "UPDATE boleto SET 
                    boleto_recebido = 1,
                    data_recebimento = '$dataProcessa',
                    valor_recebido = '$valor_boleto',
                    obs_recebimento = 'Recebemos via boleto na data  o valor de R$ $valor_boleto_formata',
                    usuario_recebimento_id = $usuarioId
                    WHERE nosso_numero = '$numero_boleto'";
             
             echo $sql . "<br/><br/>";
             
             //mysql_query($sql); 
            
            
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
              
              echo $sql . "<br/><br/>";
              
              //mysql_query($sql);            
                                
              
              //Efetua a baixa da conta a receber vinculada
  						$sql = "UPDATE contas_receber SET 
                      situacao = 2, 
                      valor_recebido = '$valor_boleto'
                      WHERE id = $contaId";
                      
              echo $sql . "<br/>";
                
              //mysql_query($sql);
              
              }
    
      }
      
      else
      
      {
        
        $string_boletos .= "<br/>Boleto <b>$numero_boleto</b> COM JUROS - PROCESSO MANUAL NECESSÁRIO";
        
      }
    
    }
    }
    
    //Pula a linha no txt
    $linha++;
}

?>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
      <img src='./image/bt_informacao.gif' border='0' />
    </td>
    <td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
      <?php 
      
        echo "<span style='color: #990000'><b>BOLETOS QUITADOS:</b></SPAN>";
        echo $string_boletos_ok . "<br/>"; 
        
        echo "<br/><span style='color: #990000'><b>BOLETOS QUE NÃO FORAM QUITADOS:</b></SPAN>";
        echo $string_boletos . "<br/>";
        
        echo "<br/><span style='color: #990000'><b>BOLETOS ANTIGOS - REGISTRO BB:</b></SPAN>";
        echo $string_boletos_antigos . "<br/>"; 
      
      ?>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
