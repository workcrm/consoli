<?php
  include("./conexao/ConexaoMySQL.php");
  $intCodigo = $_POST["Id"];
  $modulo = $_POST["Modulo"];

  $sql = "DELETE FROM $modulo WHERE id = $intCodigo";
  $result = mysql_query($sql);

  header("location: sistema.php");
?>
