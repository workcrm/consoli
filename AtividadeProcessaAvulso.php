<?php

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

$EventoId = $_GET['EventoId'];
$TipoEvento = $_GET['TipoEvento'];

if ($TipoEvento == '') die('Nao foi especificado o tipo de Evento. 1 = Evento Social, 2 = Formatura');

//Monta a query para pegar as atividades do evento
$sql_evento = "SELECT * FROM eventos WHERE id = $EventoId";

echo $sql_evento . '<br/>';

//Executa a query
$query_evento = mysql_query($sql_evento);
							
//Conta o numero de registros da query
$registros_evento = mysql_num_rows($query_evento);

if ($registros_evento > 0)
{
	
	$dados_evento = mysql_fetch_array($query_evento);
	
	$data_evento = $dados_evento[data_realizacao];
			
	echo "Data do Evento: $data_evento<br>";
	
	//Monta a query para pegar as atividades do evento
	$sql_atividade = "SELECT * FROM atividades WHERE tipo_evento = $TipoEvento";
	
	//Executa a query
	$query_atividade = mysql_query($sql_atividade);
								
	//Conta o numero de registros da query
	$registros_atividade = mysql_num_rows($query_atividade);

	//Caso não houver registros
	if ($registros_atividade > 0) 
	{

		//efetua o loop na pesquisa
		while ($dados_atividade = mysql_fetch_array($query_atividade))
		{
			
			$atividade_id = $dados_atividade[id];
			$dias_prazo = $dados_atividade[dias];
			
			$data_prazo = subDias("$data_evento", "$dias_prazo");
			
			//Insere a atividade ao evento
			$insere_atividade = mysql_query("INSERT INTO eventos_atividade (evento_id, atividade_id, data_prazo) VALUES ($EventoId, $atividade_id, '$data_prazo');");
			
			echo "<br/>INSERT INTO eventos_atividade (evento_id, atividade_id, data_prazo) VALUES ($EventoId, $atividade_id, '$data_prazo');";
		}

	}

}
	
//Exibe a mensagem de inclusão com sucesso
echo "<br/>$registros_atividade Atividades Associadas ao evento: $EventoId";




?>