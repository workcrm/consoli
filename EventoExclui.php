<?php
###########
## Módulo para Processar a exclusão geral do evento
## Criado: 27/05/2007 - Maycon Edinger
## Alterado: 05/06/2007 - Maycon Edinger
## Alterações: 
## 05/06/2007 - Corrigido bug que não excluia os participantes do evento a ser excluido
###########
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$EventoId = $_GET["EventoId"];

//Exclui os brindes do evento
mysql_query("DELETE FROM eventos_brinde WHERE evento_id = $EventoId");

//Exclui as datas do evento
mysql_query("DELETE FROM eventos_data WHERE evento_id = $EventoId");

//Exclui os endereços do evento
mysql_query("DELETE FROM eventos_endereco WHERE evento_id = $EventoId");

//Exclui os formandos do evento
mysql_query("DELETE FROM eventos_formando WHERE evento_id = $EventoId");

//Exclui o foto e vídeo do evento
mysql_query("DELETE FROM eventos_fotovideo WHERE evento_id = $EventoId");

//Exclui os congelamentos de foto e vídeo do evento
mysql_query("DELETE FROM eventos_fotovideo_congela WHERE evento_id = $EventoId");

//Exclui os fechamento de congelamento de foto e vídeo do evento
mysql_query("DELETE FROM eventos_fotovideo_congela_fechamento WHERE evento_id = $EventoId");

//Exclui os itens do evento
mysql_query("DELETE FROM eventos_item WHERE evento_id = $EventoId");

//Exclui as composições de itens do evento
mysql_query("DELETE FROM eventos_item_composição WHERE evento_id = $EventoId");

//Exclui os participantes do evento
mysql_query("DELETE FROM eventos_participante WHERE evento_id = $EventoId");

//Exclui os repertorios do evento
mysql_query("DELETE FROM eventos_repertorio WHERE evento_id = $EventoId");

//Exclui os servicos do evento
mysql_query("DELETE FROM eventos_servico WHERE evento_id = $EventoId");

//Exclui os terceiros do evento
mysql_query("DELETE FROM eventos_terceiro WHERE evento_id = $EventoId");

//Exclui todas as notificações na agenda desse evento
mysql_query("DELETE FROM compromissos WHERE evento_id = $EventoId");

//Exclui o evento em si
mysql_query("DELETE FROM eventos WHERE id = $EventoId");

//Monitora o evento escolhido para o bactracking do usuário
$sql_backtracking = mysql_query("UPDATE usuarios SET evento_id = 0 WHERE usuario_id = '$usuarioId'");

echo "<script>wdCarregarFormulario('UltimoEvento.php','ultimo_evento',2)</script>";

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td>
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Manutenção de Eventos</span>
          </td>
			  </tr>
			  <tr>
			    <td>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				  </td>
			  </tr>
			</table>
      <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
        <tr>
          <td height="22" width="20" valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px">
            <img src="./image/bt_informacao.gif" border="0" />
          </td>
          <td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px">
            <strong>Evento excluído com sucesso !</strong>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>