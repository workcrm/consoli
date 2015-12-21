<?php 
###########
## M�dulo para busca de formando de um evento para utiliza��o numa conta a receber
## Criado: 10/12/2009 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

// Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Captura o evento para filtragem dos formandos
$EventoId = $_GET["EventoId"];

//Camptura o id original do formando, para caso for uma altera��o da conta
$FormandoId = $_GET["FormandoId"];

//Efetua o lookup na tabela de formandos
//Monta o sql de pesquisa
$lista_formandos = "SELECT id, nome FROM eventos_formando WHERE empresa_id = $empresaId AND evento_id = $EventoId ORDER BY nome";

//Executa a query
$dados_formandos = mysql_query($lista_formandos);

//Conta o total de formandos que existem no evento
$total_formandos = mysql_num_rows($dados_formandos);

//Caso o total de formandos for zero
if ($total_formandos == 0) {
 
  //Exibe a mensagem que n�o h� formandos para este evento
  echo "<span style='color: #990000'><b>[ N�o h� formandos cadastrados para o evento escolhido ! ]</b></span>
        <input type='hidden' name='cmbFormandoId' id='cmbFormandoId' value='0'>
 "; 
  
} else {
  
?>

<select name="cmbFormandoId" id="cmbFormandoId" style="width:350px">
  <option value="0">--- Selecione uma Op��o ---</option>
  <?php 
 	  //Monta o while para gerar o combo de escolha
 	  while ($lookup_formando = mysql_fetch_object($dados_formandos)) { 
  ?>
    <option value="<?php echo $lookup_formando->id ?>" <?php if ($lookup_formando->id == $FormandoId) 
    {
      echo " selected ";
    } ?> ><?php echo $lookup_formando->nome ?> </option>
  <?php } ?>
</select>	

<?php
  
}

?>