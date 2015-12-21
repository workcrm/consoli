<?php
###########
## Listagem dos emails do formandos do evento
## Criado: 03/06/2011 - Maycon Edinger
## Alterado:
## Alterações: 
###########

header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Inclui a classe do phpmailer
require_once('./PHPMailer/class.phpmailer.php');

//Captura o evento informado
$EventoId = $_GET["EventoId"];
	
//pesquisa os formandos do evento que possuam emails
$sql_formando = "SELECT 
				email
				FROM eventos_formando 
				WHERE evento_id = $EventoId
				AND email != ''";													  													  
							  
//Executa a query
$resultado_formando = mysql_query($sql_formando);

//Percore os formandos
while ($dados_formando = mysql_fetch_array($resultado_formando))
{

	$relacao .= $dados_formando["email"] . ";\n";
	$total_eventos++;
	

}


?>
<b>Relação geral de emails do evento:</b>
<br/>
<br/>
<span style='color: #990000'>Copie e cole a relação manualmente em seu programa de envio de emails.</span>
<br/>
<textarea id="resultado" style="width: 700px; height: 450px; font-family: courier"><?php echo $relacao ?></textarea>
<br/>
<br/>
Total de emails listados: <b><?php echo $total_eventos ?></b>