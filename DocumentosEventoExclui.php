<?php
###########
## M�dulo para Processar a exclus�o da documento do evento
## Criado: 10/11/2011 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$DocumentoId = $_GET["DocumentoId"];
$EventoId = $_GET["EventoId"];

//Captura o nome do arquivo para excluir
$lista_anexo = "SELECT arquivo FROM eventos_documento WHERE id = $DocumentoId";
//Executa a query
$dados_anexo = mysql_query($lista_anexo);

while ($dados_anexo = mysql_fetch_array($dados_anexo))
{
  
  //Deleta o arquivo
  unlink($dados_anexo[arquivo]);
  
}

//Exclui os endere�os do evento
mysql_query("DELETE FROM eventos_documento WHERE id = $DocumentoId");

//Volta ao meu portal
header("location: EventoExibe.php?EventoId=$EventoId&headers=1");
?>
