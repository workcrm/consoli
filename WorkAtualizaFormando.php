<?php
###########
## M�dulo para Atualiza��o online de um formando
## Criado: 02/09/2010 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########


//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Desativar o CSS redundante
//<link rel='stylesheet' type='text/css' href='include/workStyle.css'>

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
require('Diretivas.php');

//Estabelece a conex�o com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipula��o de datas
include './include/ManipulaDatas.php';

//Efetua o lookup na tabela de formandos
//Monta o sql de pesquisa
$lista_formandos = "SELECT id, nome FROM eventos_formando WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_formandos = mysql_query($lista_formandos);

//Conta o total de formandos que existem no evento
$total_formandos = mysql_num_rows($dados_formandos);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
		    <tr>
		      <td width="750">
			      <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Atualiza��o Financeira ONLINE por Formando</span>
          </td>
		    </tr>
		    <tr>
		      <td colspan="5">
			    	<img src="image/bt_espacohoriz.gif" width="100%" height="12" />
		  	  </td>
		    </tr>
		  </table>
    </td>
  </tr>
 </table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td> 					
			<br/>
			Selecione o formando a atualizar:
			<br/>
			<br/>
      <select name="cmbFormandoId" id="cmbFormandoId" style="width:450px">
        <option value="0">Selecione uma Op��o</option>
        <?php 
       	  //Monta o while para gerar o combo de escolha
       	  while ($lookup_formando = mysql_fetch_object($dados_formandos)) { 
        ?>
          <option value="<?php echo $lookup_formando->id ?>" <?php if ($lookup_formando->id == $FormandoId) 
          {
            echo " selected ";
          } ?> ><?php echo $lookup_formando->id . ' - ' . $lookup_formando->nome ?> </option>
        <?php } ?>
      </select>	
		</td>
  </tr>	
  <tr>
    <td style="padding-top: 10px;">
      <input class="button" title="Atualiza online os boletos do formando escolhido" name="executar" type="button" id="executar" value="Executar Atualiza��o" onclick="if (document.getElementById('cmbFormandoId').value == 0){alert('� necess�rio selecionar um formando !')} else {abreJanela('WorkAtualizaFormandoProcessa.php?FormandoId=' + document.getElementById('cmbFormandoId').value)}" />
    </td>
  </tr>	      
</table>