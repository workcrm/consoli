<?php 
###########
## M�dulo para Atualiza��o dos cpfs das senhas dos formandos
## Criado: 21/06/2010 - Maycon Edinger
## Altera��es: 
###########

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
//require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

echo "<b>Inciando a rotina de troca de senha dos formandos</b><br/>Processo iniciado em <b>" . date("d/m/Y", mktime()) . "</b> as <b>" . date("H:i:s", mktime()) . "</b><br/>";
              
//Recupera dos dados do evento
$sql = "SELECT id, nome, cpf FROM eventos_formando";
  
//Executa a query
$resultado = mysql_query($sql);

//Rotina para upload dos eventos
while ($dados = mysql_fetch_array($resultado)){
  
 
  //Captura os 5 ultimos numeros do CPF
  $PegaDigitos = substr($dados[cpf],(strlen($dados[cpf])-5),strlen($dados[cpf]));                    
  
  $senha_formando = str_replace("-","",$PegaDigitos);
  $formando_id = $dados[id];  
 
  $sql_atualiza = mysql_query("UPDATE eventos_formando SET senha = '$senha_formando' WHERE id = $formando_id");
  
}


//Exibe as mensagens finais
echo "<br/>Processo Conclu�do !<br/>";

?>
