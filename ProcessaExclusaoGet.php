<?php
###########
## M�dulo para Processar a exclus�o do registro via GET
## Criado: 19/04/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########
/**
* @package workeventos
* @abstract M�dulo para processar a exclus�o do registro via GET
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Estabelece a conex�o com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os par�metros para montar a exclus�o
$intCodigo = $_GET["Id"];
$modulo = $_GET["Modulo"];
$retorno = $_GET["Retorno"] . ".php";

//Monta a query de exclus�o
$sql = "DELETE FROM $modulo WHERE id = $intCodigo";
//Executa a query
$result = mysql_query($sql);

//Cria o script JS pra retornar a p�gina que originou a exclus�o, usando AJAX
echo "
<script language='JavaScript'>
  wdCarregarFormulario('$retorno?headers=1','conteudo');
</script>
";
?>
