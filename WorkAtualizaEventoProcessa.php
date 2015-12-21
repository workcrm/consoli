<?php 
###########
## Módulo para Atualização da base de dados para o módulo online - evento individual
## Criado: 30/03/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Cria as variáveis usadas pelo log
$data_abre_log = date('Y-m-d', mktime());
$hora_abre_log = date('H:i:s', mktime());

//adicionar ao início da página php
$marcador_inicial = microtime(1);

//Captura o evento escolhido
$edtEventoId = $_GET['EventoId'];

//Dados do servidor remoto
$Server_atu = 'mysql.consolieventos.com.br';
//$Server_atu = 'localhost';
$Login_atu = 'consolieventos';
//$Login_atu = 'root';
$Senha_atu = 'consoli2010';
//$Senha_atu = '';
$DB_atu = 'consolieventos';
//$DB_atu = 'workeventos';


//Conecta ao banco de dados online
//Define a sting de conexão
$conexao = @mysql_connect($Server_atu,$Login_atu,$Senha_atu) or die('Nao foi possivel se conectar com o banco de dados do servidor de destino !');

//Conecta ao banco de dados principal
$base = @mysql_select_db($DB_atu) or die('Nao foi possivel selecionar a base: $DB_atu no servidor de destino !');

//Apaga os boletos do evento especificado
$limpa_boletos = mysql_query("DELETE FROM WORK_boleto WHERE evento_id = $edtEventoId");

//Apaga o formando da base
$limpa_formandos = mysql_query("DELETE FROM WORK_formando WHERE evento_id = $edtEventoId");

//Estabelece a conexão com o banco de dados local do work
include './conexao/ConexaoMySQL.php';

echo "<img src='image/logo_consoli2_pq.jpg'/><br/><br/><b>Inciando a rotina de transferência dos dados do evento</b><br/>Processo iniciado em <b>" . date('d/m/Y', mktime()) . "</b> as <b>" . date("H:i:s", mktime()) . "</b><br/>";

echo "<br/>
      <table width='300' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
        <tr>
          <td height='22' width='20' valign='top' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 3px; border-right: 0px'>
            <img src='./image/bt_informacao.gif' border='0' />
          </td>
          <td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px; padding-top: 3px; padding-bottom: 4px'>
            <strong>Dados do Servidor Remoto:</strong><br/><span style'color: #990000'>$Server_atu</span><br/>
          </td>
        </tr>
      </table>";
              

//Exibe a mensagem
echo '<br/><br/><br/><br/><span style="font-family: courier">Atualizando dados dos Formandos...<br/>';

//Recupera dos dados do evento
$sql_formando = "SELECT id, evento_id, nome, cpf, senha FROM eventos_formando where evento_id = $edtEventoId";

//Executa a query
$resultado_formando = mysql_query($sql_formando);

//verifica o número total de registros
$registros_formando = mysql_num_rows($resultado_formando);


//Exibe a mensagem para os boletos
echo 'Atualizando dados dos Boletos...<br/>';

//Recupera dos dados do evento
$sql_boleto = "SELECT * FROM boleto where evento_id = $edtEventoId";  
  
//Executa a query
$resultado_boleto = mysql_query($sql_boleto);

//verifica o número total de registros
$registros_boleto = mysql_num_rows($resultado_boleto);


//Conecta ao banco de dados online
//Define a sting de conexão
$conexao = @mysql_connect($Server_atu,$Login_atu,$Senha_atu) or die('Nao foi possivel se conectar com o banco de dados do servidor de destino !');

//Conecta ao banco de dados principal
$base = @mysql_select_db($DB_atu) or die('Nao foi possivel selecionar a base: $DB_atu no servidor de destino !');

//Rotina para upload dos dados do formando
while ($dados_formando = mysql_fetch_array($resultado_formando))
{
   
	$sql_atualiza_formandos = mysql_query("INSERT INTO WORK_formandos (id, evento_id, nome, cpf, senha) VALUES ('$dados_formando[id]', '$dados_formando[evento_id]', '$dados_formando[nome]', '$dados_formando[cpf]', '$dados_formando[senha]')");

}

//Rotina para upload dos boletos
while ($dados_boleto = mysql_fetch_array($resultado_boleto))
{
  
 $sql_atualiza_boleto = mysql_query("INSERT INTO WORK_boleto (
                                    id, 
                                    id_hash,
                                    prazo_pagamento, 
                                    taxa_boleto, 
                                    valor_cobrado,
                                    valor_boleto,
                                    nosso_numero,
                                    numero_documento,
                                    data_vencimento,
                                    data_documento,
                                    data_processamento,
                                    sacado,
                                    endereco1,
                                    endereco2,
                                    demonstrativo1,
                                    demonstrativo2,
                                    demonstrativo3,
                                    instrucoes1,
                                    instrucoes2,
                                    instrucoes3,
                                    instrucoes4,
                                    boleto_recebido,
                                    data_recebimento,
                                    valor_recebido,
                                    obs_recebimento,
                                    evento_id,
                                    formando_id,
									reajustado,
									data_reajuste
                                    ) VALUES (
                                    '$dados_boleto[id]', 
                                    '$dados_boleto[id_hash]', 
                                    '$dados_boleto[prazo_pagamento]', 
                                    '$dados_boleto[taxa_boleto]',
                                    '$dados_boleto[valor_cobrado]',
                                    '$dados_boleto[valor_boleto]',
                                    '$dados_boleto[nosso_numero]',
                                    '$dados_boleto[numero_documento]',
                                    '$dados_boleto[data_vencimento]',
                                    '$dados_boleto[data_documento]',
                                    '$dados_boleto[data_processamento]',
                                    '$dados_boleto[sacado]',
                                    '$dados_boleto[endereco1]',
                                    '$dados_boleto[endereco2]',
                                    '$dados_boleto[demonstrativo1]',
                                    '$dados_boleto[demonstrativo2]',
                                    '$dados_boleto[demonstrativo3]',
                                    '$dados_boleto[instrucoes1]',
                                    '$dados_boleto[instrucoes2]',
                                    '$dados_boleto[instrucoes3]',
                                    '$dados_boleto[instrucoes4]',
                                    '$dados_boleto[boleto_recebido]',
                                    '$dados_boleto[data_recebimento]',
                                    '$dados_boleto[valor_recebido]',
                                    '$dados_boleto[obs_recebimento]',
                                    '$dados_boleto[evento_id]',
                                    '$dados_boleto[formando_id]',
									'$dados_boleto[reajustado]',
									'$dados_boleto[data_reajuste]'                                    
                                    )");
  
}

//Exibe as mensagens finais
echo "<br/>Total de Formandos transferidos: <b>" . $registros_formando . "</b><br/>";
echo "Total de Boletos transferidos: <b>" . $registros_boleto . "</b></span><br/>";

//Cria as variáveis usadas pelo log
$hora_fecha_log = date("H:i:s", mktime());

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Roda o log da atualização
$abre_log = mysql_query("INSERT INTO atualizacoes (data, hora_ini, hora_fim) VALUES ('$data_abre_log', '$hora_abre_log','$hora_fecha_log')");

echo "<br/>Processo finalizado as: <b>" . $hora_fecha_log . "</b>";

//adicionar ao fim da página
$marcador_final= microtime(1);
$tempo_execucao = $marcador_final - $marcador_inicial;
echo "<br/>Tempo total de execução: <b>" .sprintf ( "%02.3f", $tempo_execucao ). "</b> segundos. <br>";
?>
