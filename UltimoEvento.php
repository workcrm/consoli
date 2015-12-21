<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Processa as diretivas de segurança
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";
					
//Recupera dos dados do ultimo evento trabalhado
$sql_back = "SELECT 
            usu.evento_id,
            eve.id,
            eve.nome as evento_nome
            FROM usuarios usu
            LEFT OUTER JOIN eventos eve ON usu.evento_id = eve.id
            WHERE usu.usuario_id = '$usuarioId'";													  													  
							  
//Executa a query
$resultado_back = mysql_query($sql_back);

//Monta o array dos campos
$dados = mysql_fetch_array($resultado_back);
					
?>	

<script>
  document.getElementById('barra').innerHTML = "<table width='100%' border='0' cellpadding='0' cellspacing='0'><tr><td><span style='color: #000000;'>Último evento: </span><a title='Clique para exibir os detalhes deste evento' href='#' onclick='wdCarregarFormulario(\"EventoExibe.php?EventoId=<?php echo $dados[evento_id] ?>&headers=1\",\"conteudo\")'><span class='style1'><?php echo $dados[evento_id] . " - " . $dados[evento_nome] ?></span></a></td><td width='250' align='right' style='padding-top: 1px'><img src='./image/bt_data_evento.gif' title='Clique para gerenciar as datas deste evento' onclick='wdCarregarFormulario(\"DataEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_participante.gif' title='Clique para gerenciar os participantes deste evento' onclick='wdCarregarFormulario(\"ParticipanteEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_endereco.gif' title='Clique para gerenciar os endereços deste evento' onclick='wdCarregarFormulario(\"EnderecoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_item.gif' title='Clique para gerenciar os produtos deste evento' onclick='wdCarregarFormulario(\"ItemEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_terceiro.gif' title='Clique para gerenciar os terceiros deste evento' onclick='wdCarregarFormulario(\"TerceiroEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_brinde.gif' title='Clique para gerenciar os brindes deste evento' onclick='wdCarregarFormulario(\"BrindeEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_repertorio.gif' title='Clique para gerenciar o repertório musical deste evento' onclick='wdCarregarFormulario(\"RepertorioEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_formando.gif' title='Clique para gerenciar os formandos deste evento' onclick='wdCarregarFormulario(\"FormandoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_fotovideo.gif' title='Clique para gerenciar o foto e vídeo deste evento' onclick='wdCarregarFormulario(\"FotoVideoEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;<img src='./image/bt_documentos.gif' title='Clique para gerenciar os documentos deste evento' onclick='wdCarregarFormulario(\"DocumentosEventoCadastra.php?EventoId=<?php echo $dados[id] ?>&headers=1\",\"conteudo\")' style='cursor: pointer'>&nbsp;&nbsp;</td><td width='220' align='right' style='border-left: #999999 1px solid'><form name='pesquisa' action='#'>Pesquisar:&nbsp;<input name='ChavePesquisa' type='text' id='ChavePesquisa' style='padding-left: 3px; width: 130px; color: #6666CC; font-weight: bold' maxlength='35' onKeyPress='if ((window.event ? event.keyCode : event.which) == 13) { return wdSubmitPesquisaEnter(); }' />&nbsp;<input class='button' title='Efetua a pesquisa com base no texto informado' name='btnPesquisa' type='button' id='btnPesquisa' value='Ok' style='width:22px' onClick='wdSubmitPesquisa()' /></form></td></tr></table>";
</script>					