<?php 
###########
## Módulo para Atualização da base de dados para o módulo online
## Criado: 11/03/2010 - Maycon Edinger
## Alterado: 25/11/2007 - Maycon Edinger
## Alterações: 
###########

//Dados do servidor remoto
$Server_atu = "myadmin.softhouse.com.br";
//$Server_atu = "localhost";
$Login_atu = "consolieventos";
//$Login_atu = "root";
$Senha_atu = "consoli2010";
//$Senha_atu = "";
$DB_atu = "consolieventos";
//$DB_atu = "workeventos";



//Conecta ao banco de dados online
//Define a sting de conexão
$conexao = @mysql_connect($Server_atu,$Login_atu,$Senha_atu) or die('Nao foi possivel se conectar com o banco de dados do servidor de destino !');

//Conecta ao banco de dados principal
$base = @mysql_select_db($DB_atu) or die("Nao foi possivel selecionar a base: $DB_atu no servidor de destino !");

echo "Conexão OK";

?>
