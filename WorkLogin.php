<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>work | eventos - Login da Área de Formandos</TITLE>
<META http-equiv=Content-Type content="text/html; charset=ISO-8859-1">
<META content="MSHTML 6.00.2745.2800" name=GENERATOR></HEAD>
<LINK href="include/workStyle.css" type=text/css rel=stylesheet>

<script>

//--->Função para a formatação dos campos...<---
function Mascara(tipo, campo, teclaPress) {
        if (window.event)
        {
                var tecla = teclaPress.keyCode;
        } else {
                tecla = teclaPress.which;
        }
 
        var s = new String(campo.value);
        // Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
        s = s.replace(/(\.|\(|\)|\/|\-| )+/g,'');
 
        tam = s.length + 1;
 
        if ( tecla != 9 && tecla != 8 ) {
                switch (tipo)
                {
                case 'CPF' :
                        if (tam > 3 && tam < 7)
                                campo.value = s.substr(0,3) + '.' + s.substr(3, tam);
                        if (tam >= 7 && tam < 10)
                                campo.value = s.substr(0,3) + '.' + s.substr(3,3) + '.' + s.substr(6,tam-6);
                        if (tam >= 10 && tam < 12)
                                campo.value = s.substr(0,3) + '.' + s.substr(3,3) + '.' + s.substr(6,3) + '-' + s.substr(9,tam-9);
                break;
 
                case 'CNPJ' :
 
                        if (tam > 2 && tam < 6)
                                campo.value = s.substr(0,2) + '.' + s.substr(2, tam);
                        if (tam >= 6 && tam < 9)
                                campo.value = s.substr(0,2) + '.' + s.substr(2,3) + '.' + s.substr(5,tam-5);
                        if (tam >= 9 && tam < 13)
                                campo.value = s.substr(0,2) + '.' + s.substr(2,3) + '.' + s.substr(5,3) + '/' + s.substr(8,tam-8);
                        if (tam >= 13 && tam < 15)
                                campo.value = s.substr(0,2) + '.' + s.substr(2,3) + '.' + s.substr(5,3) + '/' + s.substr(8,4)+ '-' + s.substr(12,tam-12);
                break;
 
                case 'TEL' :
                        if (tam > 2 && tam < 4)
                                campo.value = '(' + s.substr(0,2) + ') ' + s.substr(2,tam);
                        if (tam >= 7 && tam < 11)
                                campo.value = '(' + s.substr(0,2) + ') ' + s.substr(2,4) + '-' + s.substr(6,tam-6);
                break;
 
                case 'DATA' :
                        if (tam > 2 && tam < 4)
                                campo.value = s.substr(0,2) + '/' + s.substr(2, tam);
                        if (tam > 4 && tam < 11)
                                campo.value = s.substr(0,2) + '/' + s.substr(2,2) + '/' + s.substr(4,tam-4);
                break;
                
                case 'CEP' :
                        if (tam > 5 && tam < 7)
                                campo.value = s.substr(0,5) + '-' + s.substr(5, tam);
                break;
                }
        }
}

//--->Função para verificar se o valor digitado é número...<---
function digitos(event){
        if (window.event) {
                // IE
                key = event.keyCode;
        } else if ( event.which ) {
                // netscape
                key = event.which;
        }
        if ( key != 8 || key != 13 || key < 48 || key > 57 )
                return ( ( ( key > 47 ) && ( key < 58 ) ) || ( key == 8 ) || ( key == 13 ) );
        return true;
}
</script>

<body onload="document.getElementById('user_login').focus()">

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


<form action="WorkFinanceiro.php" name="login" method="post" enctype="multipart/form-data">

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
  	<tr height="11">
    	<td style="PADDING-LEFT: 10px" colspan="3"></td>
  	</tr>
  	<tr>
    	<td colspan="3">
      	<table cellspacing="0" cellpadding="0" width="100%" border="0">
        		<tr>
          		<td style="PADDING-RIGHT: 10px; VERTICAL-ALIGN: top; WIDTH: 100%">  
            		<br/>
                <br/>
            		<table cellspacing="0" cellpadding="0" width="100%" align="center" border="0">
              			<tr>
                			<td>
                  			<table width="299" border="0" align="center" cellpadding="0" cellspacing="0">
                  				<tr>
                    				<td class="body" style="PADDING-BOTTOM: 10px"><b>Bem-vindo ao</b><br/>
				    									<img src="image/logo_sistema_gd.jpg" />
														</td>
													</tr>
                  				<tr>
                    				<td align="middle" class="body">						
                      				<table width="300" border="0" align="center" cellpadding="0" cellspacing="0">
			                    <tr>
			                      <td class="tabForm">						
		                        	<table cellspacing="0" cellpadding="0" width="300" align="center" border="0">
		                          	<tr>
		                            	<td class="dataLabel" style="padding-top: 0px; padding-left: 0px; padding-right: 0px; font-weight: bold; font-size: 13px; padding-bottom: 0px">
		                            	  <div align="center"><span style="font-size: 16px; color: #990000"><b>Consoli Eventos</b></span></div>
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
																					  <img src="image/bt_pessoas_gd.gif" />
																					</div>
																				</td>
		                            				<td class="dataLabel" width="185" style="padding-bottom: 0px;">
																					<strong>CPF do Formando:</strong><br/>
									  											<input id="user_login" name="user_login" title="Informe o CPF do formando" maxlength="14" onkeypress="return digitos(event, this);" onkeyup="Mascara('CPF',this,event);" style="width: 120px; height: 18; font-size:12px; font-family:tahoma; font-weight:bold; color:#990000">
																				</td>
																				<td rowspan="2" width="80" class="dataLabel" style="padding-top: 2px;">
																					&nbsp;
																				</td>																		
		                          				</tr>
					                          	<tr>
					                            	<td class="dataLabel" style="padding-top: 2px;">
																					<div align="center">
																						<img src="image/bt_senha.gif" />
																					</div>
																				</td>
					                            	<td class="dataLabel" style="padding-top: 2px;">
																					<strong>Senha:</strong><br/>
												  								<input id="user_password" type="password" name="user_senha" maxlength="8" title="Informe sua senha de acesso" style="width: 120px; height: 18; font-size:12px; font-family:tahoma; font-weight:bold; color:#990000; text-transform:uppercase">							
																				</td>
					                          	</tr>
					                          </table>
					                        </td>
					                      </tr>
		                          	<tr>
		                            	<td align="center" style="padding-top: 5px">
									  								<input class="button" id="login_button" title="Efetua o Login no Sistema" type="submit" value="Efetuar Login no Sistema" name="Login" style="width: 250px; height: 22">							
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
												</td>
										  </tr>
		  							</table>
									</td>
	  						</tr>
						</table>			
						<br/>
            <br/>
		      </td>
			  </tr>
 		  </table>
		</td>
  </tr>
</table>
</form>

<table class="underFooter" cellspacing="0" cellpadding="0" width="100%" border="0">
  	<tr>
			<td style="padding-top: 120px; padding-left: 10px">		
        © 2007 : 2010 - Todos os direitos reservados - Desenvolvido por Maycon Edinger (mayconedinger@gmail.com)
    	</td>
  	</tr>
</table>

</body>
</html>
