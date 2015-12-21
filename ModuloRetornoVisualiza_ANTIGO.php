<?php
###########
## Módulo para visualizar os dados dos boletos vindos do arquivo de retorno
## Criado: 20/01/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

	
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

$dataProcessa = $_POST["edtDataProcessa"];

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width='440'>
			<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Processamento de Arquivos de Retorno em <?php echo $dataProcessa ?></span>
		</td>
	</tr>
	<tr>
		<td>
			<img src="image/bt_espacohoriz.gif" width="100%" height="12">
		</td>
	</tr>
</table>
      
<?php

function ParseFloat($floatString)
{ 
    $LocaleInfo = localeconv(); 
    $floatString = str_replace("." , "", $floatString); 
    $floatString = str_replace("," , ".", $floatString); 
    return floatval($floatString); 
} 


//Recupera os valores vindos do formulário e armazena nas variaveis
if($_POST["Submit"])
{
  
	//Caso tiver um nome de arquivo, armazena numa variável
	$arq = $_FILES['edtAnexo']['name'];
    
	//Caso nao tenha sido informado um arquivo a processar
	if(!$arq)
        exit("<b><span style='color: #990000'>É necessário selecionar um arquivo de retorno para processar !</span></b><br/><br/><input class='button' value='Voltar' type='button' name='btVoltar' onclick='wdCarregarFormulario(\"ModuloRetorno.php\",\"conteudo\")' style='width: 100px' />");
	
	$arquivo = $_FILES["edtAnexo"];
  
	//Diretório onde a imagem será salva
	$pasta = "documentos/". $arq;

	//Faz o upload da imagem
	move_uploaded_file($arquivo["tmp_name"], $pasta);
  
	//tenta abrir o arquivo de retorno do banco
	if(!$arrArquivo = file($pasta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
        exit("Não foi possível abrir o arquivo de retorno do banco!<br/><input class='button' value='Voltar' type='button' name='btVoltar' onclick='wdCarregarFormulario(\"ModuloRetorno.php\",\"conteudo\")' style='width: 100px' />");
          
  //linha atual
  $i = 0;
  $linha = 1;
  
  echo "<table width='100%' cellpadding='0' cellspacing='0' border='0'>		
			<tr>
				<td>
					<span style='font-size: 12px'>Confira atentamente os valores dos boletos listados. Após a conferência, clique em [Processar Boletos].</span><br/><br/>
				</td>
			</tr>
			<tr>
				<input class='button' value='Processar Boletos' type='button' name='btProcessa' onclick='wdCarregarFormulario(\"ModuloRetornoProcessa.php?Arquivo=$arq&DataProcessa=$dataProcessa&headers=1\",\"conteudo\")' style='width: 100px' />
				<br/>
				<br/>
			</tr>
			<tr>
				<td>
					<div id='processa'></div>
				</td>
			</tr>
        </table>";
  
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
        $valor_boleto = number_format(ParseFloat($valor_float), 2,",",".");
        
        //Monta a string do valor dos juros
        $valor_juros_float = substr($linhas, 115, 10);
        
        //Converte a string para um decimal
        $valor_juros_boleto = number_format(ParseFloat($valor_juros_float), 2,",",".");                    
          
          
        
          
          //Busca no banco de dados se existe o boleto
          //Monta a query para pegar os dados
          $sql_cli = "SELECT * FROM boleto
          						WHERE nosso_numero = '$numero_boleto'
          						ORDER BY sacado";
        
          //Executa a query
          $query_cli = mysql_query($sql_cli);
          
          //Conta o numero de registros da query
          $registros_cli = mysql_num_rows($query_cli);
            
          //Caso não houver registros
          if ($registros_cli == 0) 
          {
          	
            $sacado = "<span style='color: #990000'><b>BOLETO NÃO ENCONTRADO !!!</b></span>";
          
          } 
        
          else 
        
          {				
          
          	//efetua o loop na pesquisa
          	while ($dados_rec = mysql_fetch_array($query_cli))
            {		
            		
              //Verifica a situação do boleto
            	switch ($dados_rec["boleto_recebido"]) 
              {
            	  
                case 0: $desc_situacao = "<span style='color: #990000'>Em Aberto</span>"; break;		  
                case 1: $desc_situacao = "<span style='color: #6666CC'>Recebido</span>"; break;
                
              }
              
              $sacado = "<span style='color: #990000'><b>$dados_rec[sacado]</b></span>";
          				
              $valor_sacado = "R$ " . number_format($dados_rec["valor_boleto"], 2, ",", ".");
          					
          	}
                             
          }
          
          //Imprime os detalhes do boleto
          echo "<table class='tabDetailView' cellspacing='0' cellpadding='0' width='100%' border='0'>                    
                 <tr>
                   <td width='110' valign='top' class='dataLabel'>N&deg; do Boleto:</td>
                   <td width='140' class='tabDetailViewDF'>
                     <b>$numero_boleto</b>  
                   </td>
                   <td width='70' valign='top' class='dataLabel'>Sacado:</td>
                   <td width='500' class='tabDetailViewDF'>
                     <b>$sacado</b>  
                   </td>
                 </tr>
                 <tr>
                   <td valign='top' class='dataLabel'>Valor:</td>
                   <td class='tabDetailViewDF'>
                     <b>R$ $valor_boleto</b>  
                   </td>
                   <td valign='top' class='dataLabel'>Situação:</td>
                   <td class='tabDetailViewDF'>
                     <b>$desc_situacao</b>
                   </td>
                 </tr>";
          
          //Veririca se o boleto possui juros
          if ($valor_juros_boleto != "0,00")
          {       
          
            echo "<tr>
                     <td colspan='4' class='tabDetailViewDF' align='center'>
                       <span style='color: #990000'><b>BOLETO RECEBIDO COM JUROS ! - A QUITAÇÃO DA CONTA DEVERÁ SER EFETUADA MANUALMENTE.</b></span>  
                     </td>
                   </tr>
                  <tr>
                     <td valign='top' class='dataLabel'>Valor dos Juros:</td>
                     <td colspan='3' class='tabDetailViewDF'>
                       <span style='color: #990000'><b>R$ $valor_juros_boleto</b></span>  
                     </td>
                   </tr>";
          
          }
          
          echo "</table><br/>";                      
                      
      }
      
    }
      
    //Pula a linha no txt
    $linha++;
  
  }

}
?>