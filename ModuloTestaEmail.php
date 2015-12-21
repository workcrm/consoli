<?php
###########
## M�dulo para teste dos dados de email usando SMTP
## Projeto: Work Eventos 
###########

//Armazena as vari�veis com os valores vindos do formulario
$edtHost = "smtp.consolieventos.com.br";
$edtPorta = "26";
$edtUserName = "work@consolieventos.com.br";
$edtSenha = "ConWRK951";
$edtEmail = "work@consolieventos.com.br";
$edtNome = "Consoli Eventos Ltda - Mailer Work"
$chkAutenticacao = true;
$chkSSL = $_POST["chkSSL"];

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td valign="top" bgcolor="#FFFFCD" style="border: #D3BE96 solid 1px; padding: 5px">
    <span style="color: #6666CC; font-weight: bold">
    Resultado do teste de servidor de e-mail:<br/><br/>

    <?php
    
    require_once('../PHPMailer/class.phpmailer.php');
    //include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
    
    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
    
    $mail->IsSMTP(); // telling the class to use SMTP
    
    $hoje = date("d/m/Y - H:i", mktime());
    
    try 
    {
      
      //Dados do Host
      $mail->Host       = "$edtHost";
      
      //Ativa o modo de debug da classe phpmailer para mostrar os erros de conex�o do teste
      $mail->SMTPDebug  = 2;
      
      
      //Verifica se o servidor requer autentica��o
      if ($chkAutenticacao == 1)
      {
        
        //Indica ao servidor para usar autentica��o
        $mail->SMTPAuth   = true;
      
      }
      
      //Verifica se o servidor requer conex�o segura por SSL
      if ($chkSSL == 1)
      {
        
        //Indica ao servidor para usar autentica��o
        $mail->SMTPSecure = "ssl";                 
      
      }
      
      //Configura a porta de conex�o
      $mail->Port       = $edtPorta;
      
      //Nome de login SMTP
      $mail->Username   = "$edtUserName";
      
      //Senha do SMTP
      $mail->Password   = "$edtSenha";
      
      //Adiciona o email de retorno
      $mail->AddReplyTo("$edtEmail", "$edtNome");
      
      //Adiciona o destinat�rio
      $mail->AddAddress('testaemail@workcrm.com.br', 'Teste de Email');
      
      //Adiciona o nome do remetente
      $mail->SetFrom("$edtEmail", "$edtNome");  
      
      //Assunto do email
      $mail->Subject = 'Teste de E-mail - ' . $empresaNome;
      
      //$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
      
      //Corpo da mensagem
      $mail->MsgHTML("Este � um email de teste para o servi�o de E-Mail do Work CRM<br/><br/>Empresa: <strong>$empresaNome</strong><br/><br/>Enviado por: <strong>$usuarioNome</strong> em <strong>$hoje</strong>");
      
      
      //Anexos
      //$mail->AddAttachment('images/phpmailer.gif');      // attachment
        
      //Envia o email
      $mail->Send();
      
      //mensagem de sucesso do envio
      echo "<br/><span style='color: #990000'><b>E-mail enviado com sucesso ! As configura��es do servidor est�o corretas.</span></b>\n";
    
    } catch (phpmailerException $e) {
    
      echo $e->errorMessage(); //Pretty error messages from PHPMailer
      echo "<br/><span style='color: #990000'><b>Houve um erro ao tentar enviar o E-mail. Verifique as configura��es do servidor.</span></b>\n";
    
    } catch (Exception $e) {
    
      echo $e->getMessage(); //Boring error messages from anything else!
    
    }
    	
    ?>
    </td>
  </tr>
  <tr>
  	<td>
  		<img src="imagens/fundo_frame.png" width="100%" height="12" />
  	</td>
  </tr>
</table>