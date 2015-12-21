<?php 
###########
## Módulo para Atualização da base de dados para o módulo online
## Criado: 11/03/2010 - Maycon Edinger
## Alterado: 25/11/2007 - Maycon Edinger
## Alterações: 
###########

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

$usuario = $_GET["Usuario"];

echo "<b>Criando Arquivo de Replicação de Dados</b><br/>Processo iniciado em <b>" . date('d/m/Y', mktime()) . "</b> as <b>" . date("H:i:s", mktime()) . "</b><br/>";

//Apaga as atualizacoes anteriores
$mask = "c:\atualizacao\Atualiza_Work_CONSOLI*.sql";

array_map( "unlink", glob( $mask ) );

//Abre o novo arquivo para escrita
$arquivo = fopen('c:\atualizacao\Atualiza_Work_CONSOLI_' . date("d-m-Y_H-i", mktime()) . "_$usuario" . ".sql", "wb");

if (!$arquivo)
{

  die('<br/><b>Erro ao criar arquivo !<b>');
	
}

$inicio = fwrite($arquivo, "TRUNCATE TABLE consolieventos.WORK_boleto;\r\n

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

INSERT INTO consolieventos.WORK_boleto (id, id_hash, prazo_pagamento, taxa_boleto, valor_cobrado, valor_boleto, nosso_numero, numero_documento, data_vencimento, data_documento, data_processamento, sacado, endereco1, endereco2, demonstrativo1, demonstrativo2, demonstrativo3, instrucoes1, instrucoes2, instrucoes3, instrucoes4, boleto_recebido, data_recebimento, valor_recebido, obs_recebimento, evento_id, formando_id, reajustado, data_reajuste, data_atualizacao) VALUES \r\n");

$linha = 0;

//Recupera dos dados do evento
//$sql_boleto = "SELECT * FROM boleto"; //TODOS OS BOLETOS
$sql_boleto = "SELECT * FROM boleto WHERE data_recebimento = '0000-00-00'"; //SOMENTE OS EM ABERTO
  
//Executa a query
$resultado_boleto = mysql_query($sql_boleto);

//verifica o número total de registros
$registros_boleto = mysql_num_rows($resultado_boleto);

//Rotina para upload dos boletos
while ($dados_boleto = mysql_fetch_array($resultado_boleto))
{
		
  $escreve = fwrite($arquivo, "($dados_boleto[id], '$dados_boleto[id_hash]', '$dados_boleto[prazo_pagamento]', '$dados_boleto[taxa_boleto]', '$dados_boleto[valor_cobrado]', '$dados_boleto[valor_boleto]', '$dados_boleto[nosso_numero]', '$dados_boleto[numero_documento]', '$dados_boleto[data_vencimento]', '$dados_boleto[data_documento]', '$dados_boleto[data_processamento]', '$dados_boleto[sacado]', '$dados_boleto[endereco1]', '$dados_boleto[endereco2]', '$dados_boleto[demonstrativo1]', '$dados_boleto[demonstrativo2]', '$dados_boleto[demonstrativo3]', '$dados_boleto[instrucoes1]', '$dados_boleto[instrucoes2]', '$dados_boleto[instrucoes3]', '$dados_boleto[instrucoes4]', '$dados_boleto[boleto_recebido]', '$dados_boleto[data_recebimento]', '$dados_boleto[valor_recebido]', '$dados_boleto[obs_recebimento]', '$dados_boleto[evento_id]', '$dados_boleto[formando_id]', '$dados_boleto[reajustado]', '$dados_boleto[data_reajuste]', '$dados_boleto[data_atualizacao]')" );

  $linha++;
  $pulo++;

  if ($pulo == 100)
  {

    $quebra = ";\r\n";

    $escreve = fwrite($arquivo, "$quebra INSERT INTO consolieventos.WORK_boleto (id, id_hash, prazo_pagamento, taxa_boleto, valor_cobrado, valor_boleto, nosso_numero, numero_documento, data_vencimento, data_documento, data_processamento, sacado, endereco1, endereco2, demonstrativo1, demonstrativo2, demonstrativo3, instrucoes1, instrucoes2, instrucoes3, instrucoes4, boleto_recebido, data_recebimento, valor_recebido, obs_recebimento, evento_id, formando_id, reajustado, data_reajuste, data_atualizacao) VALUES \r\n");
    $pulo = 1;
    
  }

  else

  {

    if ($linha == $registros_boleto)
    {

      $quebra = ";\r\n";

    }

    else

    {

      $quebra = ",\r\n";

    }

    $escreve = fwrite($arquivo, "$quebra");

  }

}

//Parte dos EVENTOS
$escreve = fwrite($arquivo, "\r\n\r\n TRUNCATE TABLE consolieventos.WORK_eventos; \r\n\r\n INSERT INTO consolieventos.WORK_eventos (`id`, `nome`) VALUES \r\n");

$linha = 0;

//Recupera dos dados do evento
$sql_evento = "SELECT id, nome FROM eventos"; 

//Executa a query
$resultado_evento = mysql_query($sql_evento);

//verifica o número total de registros
$registros_evento = mysql_num_rows($resultado_evento);

//Rotina para upload dos boletos
while ($dados_evento = mysql_fetch_array($resultado_evento))
{
		
  $escreve = fwrite($arquivo, "($dados_evento[id], '$dados_evento[nome]')" );

  $linha++;
  $pulo++;

  if ($pulo == 100)
  {

    $quebra = ";\r\n";

    $escreve = fwrite($arquivo, "$quebra INSERT INTO consolieventos.WORK_eventos (`id`, `nome`) VALUES \r\n");
    $pulo = 1;
    
  }

  else

  {
		
    if ($linha == $registros_evento)
    {

      $quebra = ";\r\n";

    }

    else

    {

      $quebra = ",\r\n";

    }

    $escreve = fwrite($arquivo, "$quebra");

  }

}

//Parte dos FORMANDOS
$escreve = fwrite($arquivo, "\r\n\r\n TRUNCATE TABLE consolieventos.WORK_formandos; \r\n\r\n INSERT INTO consolieventos.WORK_formandos (`id`, `evento_id`, `nome`, `cpf`, `senha`) VALUES \r\n");

$linha = 0;

//Recupera dos dados do formando
$sql_formando = "SELECT id, evento_id, nome, cpf, senha FROM eventos_formando"; 

//Executa a query
$resultado_formando = mysql_query($sql_formando);

//verifica o número total de registros
$registros_formando = mysql_num_rows($resultado_formando);

//Rotina para upload dos boletos
while ($dados_formando = mysql_fetch_array($resultado_formando))
{
		
  $escreve = fwrite($arquivo, "($dados_formando[id], '$dados_formando[evento_id]', '$dados_formando[nome]', '$dados_formando[cpf]', '$dados_formando[senha]')" );

  $linha++;
  $pulo++;

  if ($pulo == 100)
  {
		
    $quebra = ";\r\n";

    $escreve = fwrite($arquivo, "$quebra INSERT INTO consolieventos.WORK_formandos (`id`, `evento_id`, `nome`, `cpf`, `senha`) VALUES \r\n");
    $pulo = 1;

  }

  else
	
  {

    if ($linha == $registros_formando)
    {

      $quebra = ";\r\n";

    }

    else

    {

      $quebra = ",\r\n";

    }

    $escreve = fwrite($arquivo, "$quebra");

  }

}

fclose($arquivo);

echo "<br/><br/>Arquivo gerado com sucesso !";

?>
