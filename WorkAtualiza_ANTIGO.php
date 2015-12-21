<?php 
###########
## Módulo para Atualização da base de dados para o módulo online
## Criado: 11/03/2010 - Maycon Edinger
## Alterado: 25/11/2007 - Maycon Edinger
## Alterações: 
###########

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
//require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

$edtFormandoId = $_GET['FormandoId'];

$pula_processo = 0;

if ($edtFormandoId > 0)
{
 
  $sql = mysql_query("SELECT nome FROM eventos_formando WHERE id = $edtFormandoId");
  $dados_formando = mysql_fetch_array($sql);
  
  echo "<span style='font-size: 14px'><b>Atualização Financeira ONLINE para boletos do formando: <br/><span style='color: #990000'>" . $dados_formando['nome'] . '</span></b></span><br/><br/>';
 
  $where_formando = "WHERE formando_id = $edtFormandoId";
 
  $pula_processo = 1;  
  
}

//Cria as variáveis usadas pelo log
$data_abre_log = date('Y-m-d', mktime());
$hora_abre_log = date('H:i:s', mktime());

//Dados do servidor remoto
$Server_atu = 'myadmin.softhouse.com.br';
//$Server_atu = 'localhost';
$Login_atu = 'consolieventos';
//$Login_atu = 'root';
$Senha_atu = 'consoli2010';
//$Senha_atu = '';
$DB_atu = 'consolieventos';
//$DB_atu = 'workeventos';


//Verifica se pode atualizar
$sql_atu = "SELECT 
							atu.atualizando,
							usu.nome
							FROM parametros_sistema atu 
							LEFT OUTER JOIN usuarios usu ON usu.usuario_id = atu.usuario_atualizando";

//Executa a query de consulta
$query_atu = mysql_query($sql_atu);

//Monta a matriz com os dados
$dados_atu = mysql_fetch_array($query_atu);


//verifica se pode atualizar
if ($dados_atu['atualizando'] == 1)
{
  
  $usuario = $dados_atu['nome'];
  
  die("<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='top' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 4px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px; padding-top: 4px; padding-bottom: 4px'><strong><span style='font-family: verdana; font-size: 12px'>Já existe uma atualização em andamento sendo efetuada pelo usuário <span style='color: #990000'><b>$usuario</b></span><br/><br/>Aguarde até que esta atualização esteja concluída e efetue o processo novamente !</span></strong></td></tr><tr><td colspan='2'>&nbsp</td></tr></table>");

}

//*******************************************
//Ativa o bloqueio de uma outra atualização
//Roda o log da atualização
$marca_atualizando = mysql_query("UPDATE parametros_sistema SET atualizando = 1, usuario_atualizando = $usuarioId");


//Roda o log da atualização
$abre_log = mysql_query("INSERT INTO atualizacoes (data, hora_ini) VALUES ('$data_abre_log', '$hora_abre_log')");

//Grava o id do log
$ID_Log = mysql_insert_id();


echo "<b>Inciando a rotina de transferência dos dados</b><br/>Processo iniciado em <b>" . date('d/m/Y', mktime()) . "</b> as <b>" . date("H:i:s", mktime()) . "</b><br/>";

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
              

if ($pula_processo = 0)
{
  
  //Exibe a mensagem de atualização dos eventos
  echo "<br/><br/><br/><br/>Atualizando dados dos Eventos...<br/>";

  //Recupera dos dados do evento
  $sql_evento = "SELECT id, nome FROM eventos";
    
  //Executa a query
  $resultado_evento = mysql_query($sql_evento);
  
  //verifica o número total de registros
  $registros_evento = mysql_num_rows($resultado_evento);


  //Exibe a mensagem
  echo 'Atualizando dados dos Formandos...<br/>';
  
  //Recupera dos dados do evento
  $sql_formando = "SELECT id, evento_id, nome, cpf, senha FROM eventos_formando";
    
  //Executa a query
  $resultado_formando = mysql_query($sql_formando);
  
  //verifica o número total de registros
  $registros_formando = mysql_num_rows($resultado_formando);

}

//Exibe a mensagem para os boletos
echo 'Atualizando dados dos Boletos...<br/>';

//Recupera dos dados do evento
$sql_boleto = "SELECT * FROM boleto $where_formando";  
  
//Executa a query
$resultado_boleto = mysql_query($sql_boleto);

//verifica o número total de registros
$registros_boleto = mysql_num_rows($resultado_boleto);


//Conecta ao banco de dados online
//Define a sting de conexão
$conexao = @mysql_connect($Server_atu,$Login_atu,$Senha_atu) or die('Nao foi possivel se conectar com o banco de dados do servidor de destino !');

//Conecta ao banco de dados principal
$base = @mysql_select_db($DB_atu) or die('Nao foi possivel selecionar a base: $DB_atu no servidor de destino !');


//Rotina para limpeza das bases de dados
if ($pula_processo = 0)
{
  $limpa_eventos = mysql_query("TRUNCATE TABLE WORK_eventos");
  $limpa_formandos = mysql_query("TRUNCATE TABLE WORK_formandos");
}

if ($edtFormandoId > 0)
{
  
  $limpa_boletos = mysql_query("DELETE FROM WORK_boleto WHERE formando_id = $edtFormandoId");
  
}
else
{
  
  $limpa_boletos = mysql_query("TRUNCATE TABLE WORK_boleto");
  
}

if ($pula_processo = 0)
{
  
  //Rotina para upload dos eventos
  while ($dados_evento = mysql_fetch_array($resultado_evento))
  {
       
    $sql_atualiza_eventos = mysql_query("INSERT INTO WORK_eventos (id, nome) VALUES ('$dados_evento[id]', '$dados_evento[nome]')");
    
  }

  //Rotina para upload dos formandos
  while ($dados_formando = mysql_fetch_array($resultado_formando))
  {
       
    $sql_atualiza_formandos = mysql_query("INSERT INTO WORK_formandos (id, evento_id, nome, cpf, senha) VALUES ('$dados_formando[id]', '$dados_formando[evento_id]', '$dados_formando[nome]', '$dados_formando[cpf]', '$dados_formando[senha]')");
    
  }

}

//Rotina para upload dos boletos
while ($dados_boleto = mysql_fetch_array($resultado_boleto)){
  
 
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
                                    formando_id                                    
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
                                    '$dados_boleto[formando_id]'                                    
                                    )");
  
}

//Exibe as mensagens finais
echo "<br/>Total de Eventos transferidos: <b>" . $registros_evento . "</b><br/>";
echo "Total de Formandos transferidos: <b>" . $registros_formando . "</b><br/>";
echo "Total de Boletos transferidos: <b>" . $registros_boleto . "</b><br/>";

//Cria as variáveis usadas pelo log
$hora_fecha_log = date("H:i:s", mktime());

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Roda o fechamento o log da atualização
$fecha_log = mysql_query("UPDATE atualizacoes SET hora_fim = '$hora_fecha_log' WHERE id = $ID_Log");

//Libera uma outra atualização
//Roda o log da atualização
$marca_atualizando = mysql_query("UPDATE parametros_sistema SET atualizando = 0, usuario_atualizando = 0");

echo "<br/>Processo finalizado as: <b>" . $hora_fecha_log . "</b>";

?>
