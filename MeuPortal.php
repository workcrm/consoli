<?php

/*
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='440'>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Ho Ho Ho !</span>
					</td>
				</tr>
				<tr>
					<td colspan='5'>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">		
					</td>
				</tr>
			</table>
 	 	</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
				<tr>
					<td height="22" align="middle" width="45" valign="middle" bgcolor="#FFFFCD" style="border: #D3BE96 solid 1px; padding-top: 1px; padding-bottom: 0px;padding-right: 0px; border-right: 0px">
						<img src="image/bt_natal.gif" width="28" height="40" border="0" />
					</td>
					<td height="22" width="820" valign="middle" align="left" bgcolor="#FFFFCD" style="border: #D3BE96 solid 1px; padding-left: 0px; border-left: 0px; padding-bottom: 4px; padding-top: 4px">
						Queremos renova��o e buscamos os grandes milagres da vida a cada instante. Todo ano � hora de renascer, de florescer, de viver de novo. <br/>Aproveite este ano que est� chegando para realizar todos os seus sonhos !<br/><b>FELIZ NATAL E UM PR�SPERO ANO NOVO !</b></span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<img src="image/fundo_frame.png" width="800" height="12" />
					</td>
				</tr>									
			</table>
		</td>
	</tr>
</table>
		
*/
?>

<?php
###########
## M�dulo principal do portal
## Criado: 16/04/2007 - Maycon Edinger
## Alterado: 04/12/2007 - Maycon Edinger
## Altera��es: 
###########
	
//Seta a var�avel indicando que como vai usar ajax, tem de usar os headers
$header = 1;


//Carregar� os m�dulos principais do sistema
include 'CompromissoLista.php'; //Exibe os compromissos do dia

//P�e um espa�o pra n�o ficar feio
echo '<br>'; 

//Exibe os eventos dos pr�ximos dias
include 'EventosLista.php';

//P�e um espa�o pra n�o ficar feio
echo '<br>'; 

//Exibe as atividades dos pr�ximos dias
include 'AtividadesLista.php';

//P�e um espa�o pra n�o ficar feio
echo '<br>'; 

//Exibe as loca��es pendentes
include 'LocacaoLista.php';

//P�e um espa�o pra n�o ficar feio
echo '<br>'; 

//Exibe os �ltimos recados
include 'RecadoLista.php'; 

//P�e um espa�o pra n�o ficar feio
echo '<br>';

//Exibe as datas comemorativas
include 'DataComemorativaLista.php'; 

//P�e um espa�o pra n�o ficar feio
echo '<br>';

//Exibe os anivers�rios de clientes
include 'DataAniversarioLista.php'; 

//P�e um espa�o pra n�o ficar feio
echo '<br>';

//Exibe o n�mero de formandos cadastrados
include 'TotalFormandosLista.php';
?>	