<?php
###########
## M�dulo para Processar a exclus�o do participante do evento
## Criado: 31/05/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$ParticipanteId = $_GET["ParticipanteId"];
$UsuarioNotificaId = $_GET["UsuarioNotificaId"];
$EventoId = $_GET["EventoId"];

//Exclui o participante do evento
mysql_query("DELETE FROM eventos_participante WHERE colaborador_id = $ParticipanteId AND evento_id = $EventoId");

//Exclui a notifica��o na agenda do participante se houver
mysql_query("DELETE FROM compromissos WHERE usuario_id = $UsuarioNotificaId AND evento_id = $EventoId");

//Configura a assinatura digital
mysql_query("UPDATE eventos SET participantes_timestamp = now(), participantes_operador_id = $usuarioId WHERE id = $EventoId");	

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
