<?php
###########
## Módulo para Gerenciamento de foto e video do evento
## Criado: 01/07/2013 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_eventos = mysql_query($lista_eventos);

//Conta o total de eventos
$total_eventos = mysql_num_rows($dados_eventos);

//Caso tenha um evento de volta
$EventoId = $_GET['EventoId'];

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
	<td valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="750">
                        <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Gerenciamento de Foto e Vídeo do Evento</span>
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
            <span style="color: #990000"><b>Selecione o Evento a Gerenciar:</b></span>
            <br/>
            <br/>
            <select name="cmbEventoId" id="cmbEventoId" style="width:650px">
              <option value="0">Selecione uma Opção</option>
              <?php 

                //Monta o while para gerar o combo de escolha
                while ($lookup_evento = mysql_fetch_object($dados_eventos))
                {

                  if ($lookup_evento->id == $EventoId)
                  {
                    
                    $seleciona_evento = "selected";
                    
                  }
                  
                  else
                    
                  {
                    
                    $seleciona_evento = '';
                    
                  }

              ?>
              <option value="<?php echo $lookup_evento->id ?>" <?php echo $seleciona_evento ?> ><?php echo $lookup_evento->id . ' - ' . $lookup_evento->nome ?> </option>
              <?php 

                } 

              ?>
            </select>	
	</td>
    </tr>	
    <tr>
	<td style="padding-top: 10px;">
            <input class="button" value="Iniciar Gerenciamento" name="btnGerencia" type="button" id="btnGerencia" onclick="if (document.getElementById('cmbEventoId').value == 0){alert('É necessário selecionar um evento !')} else {wdCarregarFormulario('ModuloFotoVideoGerencia.php?EventoId=' + document.getElementById('cmbEventoId').value,'gerencia')}" />
	</td>
    </tr>
    <tr>
        <td>
            <br/>
            <div id='gerencia'></div>
	</td>
    </tr>
</table>
<?php

  if ($EventoId)
  {

    ?>
    <script>
      document.getElementById('btnGerencia').click();
    </script>
    <?php

  }

?>