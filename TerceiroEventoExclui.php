<?php
###########
## Módulo para Processar a exclusão do torceiro do evento
## Criado: 28/09/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$TerceiroId = $_GET["TerceiroId"];
$EventoId = $_GET["EventoId"];

$sql = mysql_query("SELECT conta_pagar_id FROM eventos_terceiro WHERE id = $TerceiroId");

//echo "SELECT conta_pagar_id FROM eventos_terceiro WHERE id = $TerceiroId<br/>";

$dados = mysql_fetch_array($sql);

$contaId = $dados["conta_pagar_id"];

//Exclui o participante do evento
mysql_query("DELETE FROM eventos_terceiro WHERE id = $TerceiroId");
mysql_query("DELETE FROM contas_pagar WHERE id = $contaId");
mysql_query("DELETE FROM contas_pagar_pagamento WHERE conta_pagar_id = $contaId");


//echo "DELETE FROM eventos_terceiro WHERE id = $TerceiroId<br/>";
//echo "DELETE FROM contas_pagar WHERE id = $contaId<br/>";
//echo "DELETE FROM contas_pagar_pagamento WHERE conta_pagar_id = $contaId<br/>";


//Volta ao meu portal
header("location: TerceiroEventoCadastra.php?EventoId=$EventoId&headers=1");
?>
