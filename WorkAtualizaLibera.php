<?php 
###########
## M�dulo para liberar a base de boletos online caso a atualiza��o n�o d� certo
## Criado: 11/05/2011 - Maycon Edinger
## Altera��es: 
###########

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
//require("Diretivas.php");

//Dados do servidor remoto
$Server_atu = 'mysql.consolieventos.com.br';
//$Server_atu = 'localhost';
$Login_atu = 'consolieventos';
//$Login_atu = 'root';
$Senha_atu = 'consoli2010';
//$Senha_atu = '';
$DB_atu = 'consolieventos';
//$DB_atu = 'workeventos';


//Marca no servidor que o sistema est� atualizando
//Conecta ao banco de dados online
//Define a sting de conex�o
$conexao = @mysql_connect($Server_atu,$Login_atu,$Senha_atu) or die('Nao foi possivel se conectar com o banco de dados do servidor de destino !');

//Conecta ao banco de dados principal
$base = @mysql_select_db($DB_atu) or die('Nao foi possivel selecionar a base: $DB_atu no servidor de destino !');

//Marca que esta atualizando
$query = mysql_query("UPDATE parametros_sistema SET atualizando = 0");

echo "<script>alert('Acesso online aos boletos LIBERADO !'); wdCarregarFormulario('MeuPortal.php','conteudo')</script>";

?>
