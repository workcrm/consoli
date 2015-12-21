<?php
###########
## Módulo para Processar a exclusão do servicos do evento
## Criado: 25/11/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$ServicoId = $_GET["ServicoId"];
$EventoId = $_GET["EventoId"];

//Exclui o registro
mysql_query("DELETE FROM eventos_servico WHERE evento_id = $EventoId AND servico_id = $ServicoId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
