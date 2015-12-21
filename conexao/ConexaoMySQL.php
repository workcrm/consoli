<?php
  //Seta o banco principal
  $DB = 'base_consoli';
  
  //Define a sting de conexão
  $conexao = @mysql_connect("localhost","root","") or die('Nao foi possivel se conectar com o banco de dados');

  //Conecta ao banco de dados principal
  $base = @mysql_select_db($DB) or die("Nao foi possivel selecionar a base: $DB");
?>
