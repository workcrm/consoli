<?php
###########
## Notificacao dos formandos de um evento, avisando que os boletos estao diponiveis
## Criado: 03/06/2011 - Maycon Edinger
## Alterado:
## Altera��es: 
###########

header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
require('Diretivas.php');

//Estabelece a conex�o com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipula��o de datas
include './include/ManipulaDatas.php';

//Inclui a classe do phpmailer
require_once('./PHPMailer/class.phpmailer.php');

//Captura o evento informado
$TipoEnvio = $_GET["TipoEnvio"];
$EventoId = $_GET["EventoId"];
$FormandoId = $_GET["FormandoId"];

//Caso for para enviar para uma turma toda
if ($TipoEnvio == 1)
{

	//Cria a variavel vazia
	$where_filtro = "WHERE evento_id = $EventoId";
	
} 

else if ($TipoEnvio == 2)

{

	//Cria a variavel para filtrar o usuario
	$where_filtro = "WHERE id = $FormandoId";

}
	
//pesquisa os formandos do evento que possuam emails
$sql_formando = "SELECT 
				nome,
				cpf,
				senha,
				email
				FROM eventos_formando 
				$where_filtro
				AND email != ''";													  													  
							  
//Executa a query
$resultado_formando = mysql_query($sql_formando);

//Percore os formandos
while ($dados_formando = mysql_fetch_array($resultado_formando))
{

	$nomeFormando = $dados_formando["nome"];
	$emailFormando = $dados_formando["email"];
	$cpfFormando = $dados_formando["cpf"];
	$senhaFormando = $dados_formando["senha"];
	
	$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

	$mail->IsSMTP(); // telling the class to use SMTP

	$mensagem = "
	<html>
		<head>
			<title>Work Eventos - Envio de Email</title>
		</head>
		<body bgcolor='#FFFFFF' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>
			<div align='center'>
				<span style='font-family:verdana,arial,sans-serif; font-size:12px; line-height:16px; color:#990000;'>
					<b>ATEN��O: Esta � uma mensagem AUTOM�TICA. N�o responda este e-mail. Em caso de duvidas, favor entrar em contato via e-mail <a href='mailto: boletos@consolieventos.com.br>boletos@consolieventos.com.br</a>.</b>
				</span>
				<br/>
				<br/>
				<span style='font-family:verdana,arial,sans-serif; font-size:10px; line-height:16px; color:#999999;'>
					Para a correta visualiza��o desta mensagem, � necess�rio habilitar a exibi��o das imagens
				</span>
			</div>
			<br/>
			<br/>
			<img src='cid:anexo' alt='Imagem Anexada' />
			<br/>
			<br/>
			<span style='font-family:verdana,arial,sans-serif; font-size:12px;'>
			Prezado(a) <b>$nomeFormando</b>
			<br/>
			<br/>
			N�s da <b>CONSOLI EVENTOS</b> nos sentimos honrados em estar ao seu lado nestes momentos t�o especiais.
			<br/>
			<br/>
			Gostar�amos de lhe informar que, conforme consta em seu contrato (Cl�usula 7), seus boletos j� se encontram dispon�veis em nosso site. Caso voc� tenha realizado o pagamento de outra forma, pedimos que desconsidere esta mensagem.
			<br/>
			<br/>
			Para acessar seus boletos, acesse <a href='http://www.consolieventos.com.br'>www.consolieventos.com.br</a> -> Na Op��o <b>[Imprima Aqui o Seu Boleto]</b>. 
			<br/>
			<br/>
			Na tela de acesso seu login � seu <b>N�MERO DE CPF</b> e a senha s�o os <b>4 �ltimos digitos do CPF, conforme abaixo:</b>
			<br/>
			<br/>
			<div>
				<table width='300' colspan='0' rowspan='0'>
					<tr>
						<td bgcolor='#FFFFCD' style='border: #D3BE96 1px solid; padding: 6px'>
							Seu Login: <b>$cpfFormando</b>
							<br/>
							Sua Senha: <span style='color: #990000'><b>[$senhaFormando]</b></span>
						</td>
					</tr>
				</table>
			</div>
			<br/>
			<br/>
			Aproveitamos a oportunidade para nos colocarmos a sua disposi��o em caso de eventuais d�vidas atrav�s dos emails <a href='mailto:boletos@consolieventos.com.br'>boletos@consolieventos.com.br</a> e <a href='mailto:contato@consolieventos.com.br'>contato@consolieventos.com.br</a>.
			<br/>
			<br/>
			Atenciosamente:
			<br/>
			<b>Consoli Eventos Ltda</b>
			<br/>
			</span>
			<hr>							
			<span style='font-family:verdana,arial,sans-serif; font-size:10px; line-height:12px; color:#999999;'>Este e-mail foi enviado para <b>$emailFormando</b>.<br/>A <b>Consoli Eventos Ltda</b>&nbsp;respeita a sua privacidade e � contra o Spam..</span><br/>							  
		</body>
		</html>";

	try 
	{
		$mail->Host       = "smtp.consolieventos.com.br"; // SMTP server
		//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
		$mail->SMTPAuth   = true;                  // enable SMTP authentication  
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		//$mail->SMTPSecure = "ssl"; 
		$mail->Username   = "work@consolieventos.com.br"; // SMTP account username
		$mail->Password   = "ConWRK951";        // SMTP account password
		$mail->AddReplyTo('work@consolieventos.com.br', 'Consoli Eventos Ltda - Mailer Work');
		$mail->SetFrom('work@consolieventos.com.br', 'Consoli Eventos Ltda - Mailer Work');
		$mail->AddAddress($emailFormando, $nomeFormando);

		$mail->Subject = 'Consoli Eventos - Disponibilidade de boletos no site !';
		$mail->AltBody = 'Para coreta visualiza��o desta mensagem, utilize um navegador compat�vel !'; // optional - MsgHTML will create an alternate automatically
		$mail->MsgHTML($mensagem);
		//$mail->AddAttachment('images/phpmailer.gif');      // attachment
		
		//Adiciona o logotipo ao corpo da mensagem
		$mail->AddEmbeddedImage('image/logo_consoli2_pq.jpg', 'anexo', 'attachment', 'base64', 'image/jpeg');
				
		//Espera 3 segundos
		sleep(3);
		
		//Envia o email
		$mail->Send();
		
		//Limpa a listagem de destinatarios
		$mail->ClearAllRecipients;
		
		//Monta a listagem dos oks
		$mensagemEnvio .= "$nomeFormando ($emailFormando) - ENVIO OK\n";
		
		
	} 

	catch (phpmailerException $e) 
	{

		$mensagem_erro = $e->errorMessage(); //Pretty error messages from PHPMailer
		
		//Monta a listagem de email com erro
		$mensagemEnvioErro .= "$nomeFormando ($emailFormando) - ERRO NO ENVIO:\n";

	} 

	catch (Exception $e) 
	{
		
		$mensagem_erro = $e->getMessage(); 
		//Monta a listagem de email com erro
		$mensagemEnvioErro .= "ERRO: ($mensagem_erro)\n-------------------\n";

	}
	

}


?>
<b>Envio da notifica��o dos boletos para os formandos:</b>
<br/>
<br/>
Resultado de envios OK:
<br/>
<textarea id="resultado" style="width: 700px; height: 230px; font-family: courier"><?php echo $mensagemEnvio ?></textarea>
<br/>
<br/>
Resultado de envios com ERRO:
<br/>
<textarea id="resultado" style="width: 700px; height: 200px; font-family: courier"><?php echo $mensagemEnvioErro ?></textarea>
<br/>