<?php
	
  @session_start();
  @session_destroy();

  //Verifica se verio algum erro de página inválida. Senão exibe mensagem de boas vindas.
  if (isset($_GET["Erro"])) 
  {
		
    $Mensagem1 = $_GET["Erro"];
    $Mensagem2 = $_GET["Solucao"];

    $alerta = $Mensagem1 . " - " . $Mensagem2;        

  } 

  else 

  {
		
    $Mensagem1 = "Login do Sistema";
    $Mensagem2 = "Por favor informe seu login e senha:";

  }							

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>

<head>
  <title>work | eventos - Login do Sistema</title>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  <meta content="MSHTML 6.00.2745.2800" name="GENERATOR" />
</head>

<link href="include/workStyle.css" type="text/css" rel="stylesheet">

<body style="background-image: url(image/fundo_novo.png);	background-repeat: repeat-x; color:#5a5a5a;">

<form action="redirect.php" name="login" method="post" enctype="multipart/form-data">


<table cellspacing="0" cellpadding="0" width="100%" border="0" style="height: 100%">
	<tr>
		<td>
			<table cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td colspan="3">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td style="padding-right: 10px; padding-bottom: 150px; vertical-align: top; width: 100%">            
									<table cellspacing="0" cellpadding="0" width="485" align="center" border="0" style="padding-top: 50px">
										<tr>
											<td align="center" style="padding-bottom: 20px">
								    		<img src="image/login_work_eventos.png" width="584" height="112"/>
											</td>
										</tr>												
										<tr>
											<td align="center">
												<table width="400" cellpadding="0" cellspacing="0" style="border:1px solid #4f6d81">
													<tr>
														<td height="235" valign="top" style="background:#fff url(./image/fundo_login_workeventos.jpg) no-repeat; padding-left: 8px; padding-top: 8px; padding-bottom:8px">																																		
															<table width="330" border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td style="padding-top:15px; padding-bottom: 10px">
																		<span style="color: white; font-size: 12px"><strong>
																		<script language="JavaScript">
  													
																			var url = document.URL;

																			var consoli = url.lastIndexOf('/consoli/');
																			var keventos = url.lastIndexOf('/keventos/');

																			if(consoli > 0)
																			{
																				
																				document.write("Consoli Eventos");	   
																			
																			} else if(keventos > 0)
																			{
																			
																				document.write("K Eventos");	
																			
																			}
																		</script>
																		</strong></span>
																		<br/>
																		<br/>
																		<span style="color: #ffffff; font-size: 11px">
																	 		<b><?php echo $Mensagem1 ?></b>
																		</span>
																		<span style="color: #ffffff; font-size: 10px">
																			<br/>
																			<?php echo $Mensagem2 ?>
																	 	</span>			
																	</td>																		
																</tr>
																<tr>
																	<td height="18" valign="middle">
																		<span style="color: #ffffff; font-size: 12px"><b>Usu&aacute;rio:</b></span>								  											
																	</td>
																</tr>
																<tr>
																	<td valign="middle" style="padding-bottom: 10px">
																		<input id="user_name" name="user_login" title="Informe seu nome de usuário" style="width: 150px; height: 20; font-size:12px; font-family:tahoma; font-weight:bold; color:#990000; text-transform:lowercase; padding-left: 22px; background:#fff url(./image/bt_usuario.png) no-repeat;" />
																	</td>																		
																</tr>
																<tr>
																	<td height="18" valign="middle">
																		<span style="color: #ffffff; font-size: 12px"><b>Senha:</b></span>								  											
																	</td>
																</tr>
																<tr>
																	<td style="padding-bottom: 10px">
																		<input id="user_password" type="password" name="user_senha" title="Informe sua senha de acesso" style="width: 150px; height: 20; font-size:12px; font-family:tahoma; font-weight:bold; color:#990000; padding-left: 22px; background:#fff url(./image/bt_senha.png) no-repeat;" />							
																	</td>
																</tr>
																<tr>
																	<td style="padding-bottom: 4px">
																	   <input class="button" id="login_button" title="Efetua o Login no Sistema" type="submit" value="Efetuar Login" name="Login" style="width: 96px; height: 26; cursor: pointer; font-weight: bold" />										
																	</td>
																</tr>
															</table>	
														</td>
													</tr>
												</table>
												<table width="400" cellpadding="0" cellspacing="0">
													<tr style="border-left: 0px">
														<td bgcolor="#FFFFFF">
															<img src="image/fundo_frame.png" width="400" height="11" />
														</td>
													</tr>
													<tr style="border-left: 0px">
														<td bgcolor="#FFFFFF" align="right">
															<a href="http://www.worklabs.com.br"><img src="image/worklabs_2.png" title="Work Labs Tecnologia e Sistemas Ltda" border="0" /></a>
														</td>
													</tr>
												</table>						
											</td>
										</tr>
									</table>			
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<script language="javascript">document.getElementById('user_name').focus();</script>

<?php
if ($alerta)
{
  echo "<script>alert('$alerta')</script>";
  
}
?>
</form>

</body>
</html>
