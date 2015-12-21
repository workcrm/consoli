<?php
###########
## Módulo para Processar a exclusão do registro via GET
## Criado: 19/04/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo para processar a exclusão do registro via GET
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$intCodigo = $_GET["Id"];
$modulo = $_GET["Modulo"];
$retorno = $_GET["Retorno"] . ".php";

//Monta a query de exclusão
$sql = "DELETE FROM $modulo WHERE id = $intCodigo";
//Executa a query
$result = mysql_query($sql);

//Cria o script JS pra retornar a página que originou a exclusão, usando AJAX
echo "
<script language='JavaScript'>
  wdCarregarFormulario('$retorno?headers=1','conteudo');
</script>
";
?>
