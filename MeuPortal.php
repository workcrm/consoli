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
						Queremos renovação e buscamos os grandes milagres da vida a cada instante. Todo ano é hora de renascer, de florescer, de viver de novo. <br/>Aproveite este ano que está chegando para realizar todos os seus sonhos !<br/><b>FELIZ NATAL E UM PRÓSPERO ANO NOVO !</b></span>
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
## Módulo principal do portal
## Criado: 16/04/2007 - Maycon Edinger
## Alterado: 04/12/2007 - Maycon Edinger
## Alterações: 
###########
	
//Seta a varíavel indicando que como vai usar ajax, tem de usar os headers
$header = 1;


//Carregará os módulos principais do sistema
include 'CompromissoLista.php'; //Exibe os compromissos do dia

//Põe um espaço pra não ficar feio
echo '<br>'; 

//Exibe os eventos dos próximos dias
include 'EventosLista.php';

//Põe um espaço pra não ficar feio
echo '<br>'; 

//Exibe as atividades dos próximos dias
include 'AtividadesLista.php';

//Põe um espaço pra não ficar feio
echo '<br>'; 

//Exibe as locações pendentes
include 'LocacaoLista.php';

//Põe um espaço pra não ficar feio
echo '<br>'; 

//Exibe os últimos recados
include 'RecadoLista.php'; 

//Põe um espaço pra não ficar feio
echo '<br>';

//Exibe as datas comemorativas
include 'DataComemorativaLista.php'; 

//Põe um espaço pra não ficar feio
echo '<br>';

//Exibe os aniversários de clientes
include 'DataAniversarioLista.php'; 

//Põe um espaço pra não ficar feio
echo '<br>';

//Exibe o número de formandos cadastrados
include 'TotalFormandosLista.php';
?>	