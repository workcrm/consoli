<?php
  @session_start();
  @session_destroy();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>work | eventos - Login do Sistema</TITLE>
<META http-equiv=Content-Type content="text/html; charset=ISO-8859-1">
<META content="MSHTML 6.00.2745.2800" name=GENERATOR></HEAD>

<LINK href="include/workStyle.css" type=text/css rel=stylesheet>

<body onload="document.getElementById('user_name').focus()">

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
.body {
	FONT-SIZE: 12px
}
.buttonLogin {
	BORDER-RIGHT: #444444 1px solid; BORDER-TOP: #444444 1px solid; FONT-WEIGHT: bold; FONT-SIZE: 11px; BORDER-LEFT: #444444 1px solid; COLOR: #ffffff; BORDER-BOTTOM: #444444 1px solid; BACKGROUND-COLOR: #666666
}
TABLE.tabForm TD {
	BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none
}
TABLE {
	
}
TD {
	
}
P {
	MARGIN-TOP: 0px; MARGIN-BOTTOM: 10px
}
FORM {
	MARGIN: 0px
}
</style>

<?php

$dia = date("d",mktime());
$mes = date("m",mktime());
$ano = date("Y",mktime());

switch ($mes) {
	case 1: $mes_nome = "Janeiro";	break;
	case 2: $mes_nome = "Fevereiro";	break;
	case 3: $mes_nome = "Março";	break;
	case 4: $mes_nome = "Abril";	break;
	case 5: $mes_nome = "Maio";	break;
	case 6: $mes_nome = "Junho";	break;
	case 7: $mes_nome = "Julho";	break;
	case 8: $mes_nome = "Agosto";	break;
	case 9: $mes_nome = "Setembro";	break;
	case 10: $mes_nome = "Outubro";	break;
	case 11: $mes_nome = "Novembro";	break;
	case 12: $mes_nome = "Dezembro";	break;
}

//Verifica se verio algum erro de página inválida. Senão exibe mensagem de boas vindas.
	if (isset($_GET["Erro"])) {
	  $Mensagem1 = $_GET["Erro"];
	  $Mensagem2 = $_GET["Solucao"];
	} else {
	  $Mensagem1 = "Login do Sistema";
	  $Mensagem2 = "Por favor informe seu login e senha:";
	}							

?>


<form action="redirect.php" name="login" method="post" enctype="multipart/form-data">

<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
  	<td colspan="3">
    	<table cellspacing="0" cellpadding="0" width="100%" border="0">
   			<tr>
      		<td height="60" valign="top" background="image/fundo_cab.gif">&nbsp;</td>
    		</tr>       
   			<tr height="20">
         	<td class="subTabBar">
            <table height="20" cellspacing="0" cellpadding="0" width="100%" border="0">
          		<tr>
                <td width="100%" class="welcome" id="welcome">				  
	    						<?php 
										//Mostra a data atual
										echo $dia . " de " . $mes_nome . " de " . $ano 
									?>				  
	  						</td>
              </tr>
  					</table>
    			</td>    
				</tr>
	  	</table>
		</td>
  </tr>
  <tr height="20">
    <td class="lastView" colspan="3"></td>
  	</tr>
  	<TR height=11>
    	<TD style="PADDING-LEFT: 10px" colSpan=3></TD>
  	</TR>
  	<TR>
    	<TD colSpan=3>
      	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        		<TR>
          		<TD style="PADDING-RIGHT: 10px; VERTICAL-ALIGN: top; WIDTH: 100%">  
            		<BR><BR>
            		<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
              			<TR>
                			<TD>
                  			<TABLE width="299" border=0 align=center cellPadding=0 cellSpacing=0>
                    				<TR>
                      				<TD class=body style="PADDING-BOTTOM: 10px"><B>Bem-vindo ao</B><BR>
					    									<IMG src="image/logo_sistema_gd.jpg" />
															</TD>
														</TR>
                    				<TR>
                      				<TD align=middle class=body>						
                        				<table width="300" border="0" align="center" cellPadding="0" cellSpacing="0">
			                    <tr>
			                      <td class="tabForm">						
		                        	<table cellspacing="0" cellpadding="0" width="300" align="center" border="0">
		                          	<tr>
		                            	<td class="dataLabel" style="padding-top: 0px; padding-left: 0px; padding-right: 0px; font-weight: bold; font-size: 13px; padding-bottom: 0px">
		                            	<div align="center"><span style="font-size: 16px; color: #990000"><b>
												<script language="JavaScript">
													
													var url = document.URL;
													
													var consoli = url.lastIndexOf('/consoli/');
													var keventos = url.lastIndexOf('/keventos/');
													    
													if(consoli > 0){
															document.write("Consoli Eventos");	   
													} else if(keventos > 0){
															document.write("K Eventos");	
													} else {
															
											  	}
												</script>
												</b></span></div>
										  							<?php 
																			echo $Mensagem1 
																		?>							
																	</td>
								  							</tr>                          
		                          	<tr>
		                            	<td valign="top" class="dataLabel" style="padding-left: 0px; padding-right: 0px; font-size:10px; padding-top:0px">										  								
									  									<?php 
																				echo $Mensagem2 
																			?>							  
																	</td>
		                            </tr>
		                          	<tr>
		                            	<td class="dataLabel" style="padding-bottom: 0px;">
																		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
																			<tr>
																				<td width="35">
																					<div align="center">
																					<img src="image/bt_pessoas_gd.gif">
																					</div>
																				</td>
		                            				<td class="dataLabel" width="185" style="padding-bottom: 0px;">
																					<strong>Usu&aacute;rio:</strong></br>
									  											<input id="user_name" name="user_login" title="Informe seu nome de usuário" style="width: 120px; height: 18; font-size:12px; font-family:tahoma; font-weight:bold; color:#990000">
																				</td>
																				<td rowspan="2" width="80" class="dataLabel" style="padding-top: 2px;">
																					&nbsp;
																				</td>																		
		                          				</tr>
					                          	<tr>
					                            	<td class="dataLabel" style="padding-top: 2px;">
																					<div align="center">
																						<img src="image/bt_senha.gif">
																					</div>
																				</td>
					                            	<td class="dataLabel" style="padding-top: 2px;">
																					<strong>Senha:</strong></br>
												  								<input id="user_password" type="password" name="user_senha" title="Informe sua senha de acesso" style="width: 120px; height: 18; font-size:12px; font-family:tahoma; font-weight:bold; color:#990000">							
																				</td>
					                          	</tr>
					                          </table>
					                        </td>
					                      </tr>
		                          	<tr>
		                            	<td align="center" style="padding-top: 5px">
									  								<input class="button" id="login_button" title="Efetua o Login no Sistema" accesskey="Login" type="submit" value="Efetuar Login no Sistema" name="Login" style="width: 250px; height: 22">							
																	</td>
								  							</tr>
															</table>					  
														</td>
														<td width="7" background="image/sombra_direita.gif" valign="top">
														 <img src="image/bt_brancovert.gif" width="7" height="6" align="top">
														</td>
													</tr>
													<tr>
														<td colspan="2" valign="top" align="right" height="6">     		
															<img src="image/sombra_baixo.gif" width="320" height="6"  align="top">
			                    	</td>
			                    </tr>
							  				</table>					  
															</TD>
														</TR>
				  							</TABLE>
											</TD>
			  						</TR>
								</TABLE>			
								<BR><BR>
		  				</TD>
						</TR>
	  		</TABLE>
			</TD>
  	</TR>
</TABLE>
</form>

<table class="underFooter" cellspacing="0" cellpadding="0" width="100%" border="0">
  	<tr>
			<td>&nbsp;</td>
    	<td colspan="5">
			<br/>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			
      <div align="left">© 2007 : 2009 - Todos os direitos reservados - Desenvolvido por Maycon Edinger (mayconedinger@gmail.com)</div>
    	</td>
  	</tr>
</table>

</body>
</html>
