<?php

//define o caminho absoluto até a pasta do arquivo
$strCaminhoAbsoluto = './';

//nome do arquivo a ser aberto
$strNomeDoArquivo = 'retorno.ret';

//tenta abrir o arquivo de retorno do banco
if(!$arrArquivo = file($strCaminhoAbsoluto.$strNomeDoArquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
        exit('Não foi possível abrir o arquivo de retorno do banco!');
        
//linha atual
$i = 0;
$linha = 1;

//retira de um vez as quebras de linhas, retorno de carros e linhas vazias
foreach($arrArquivo as &$linhas){
        
    //retira \r
    $linhas = str_replace(chr(13), "", $linhas);
    
    //retira \n
    $linhas = str_replace(chr(10), "", $linhas);
    
    //apaga as linhas vazias
    if(empty($linhas))
            unset($arrArquivo[$i]);
            
    //proxima linha
    $i++;
    
    if ($linha > 2)
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
        
        $valor_boleto = number_format($valor_ok, 2,",",".");
        
        //Verifica se o numero do boleto não está na linha impar
        if ($numero_boleto != "00000000000000000")
        {
          
          if ($linha < 10)
          {
            
            $linha = "0" . $linha;
            
          }
          
          //Estabelece a conexão com o banco de dados
          include "./conexao/ConexaoMySQL.php";
          
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
          	
            $sacado = "<b>BOLETO NÃO ENCONTRADO !!!</b>";
          
          } 
          
          else 
          
          {				
          
          	//efetua o loop na pesquisa
          	while ($dados_rec = mysql_fetch_array($query_cli))
            {		
          		
              $sacado = "<span style='color: #990000'><b>$dados_rec[sacado]</b></span>";
          				
              $valor_sacado = "R$ " . number_format($dados_rec["valor_boleto"], 2, ",", ".");
          					
          	}
          
                    
          }
        
        echo "Boleto: " . $numero_boleto . "       -  Valor R$: " . $valor_boleto . "    - Sacado: " . $sacado;
      
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
        
        if ($juros_valor_boleto > 0)
        {
        
          echo "<br/><b>Boleto recebido com Juros: " . $juros_valor_boleto . "</b><br/>";
          
        }
        
        else
        
        {
          
          echo "<br/>";
          
        }
      
      }
    
    }
    
    $linha++;

}


?>