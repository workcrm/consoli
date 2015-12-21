<?php
###########
## Módulo para listagem do numero total de formandos atendidos
## Criado: 07/02/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Processa a contagem dos formandos atendidos
$sql_formados = mysql_query("SELECT id FROM eventos_formando WHERE status = 2");

$registros_formados = mysql_num_rows($sql_formados); 


$sql_aformar = mysql_query("SELECT id FROM eventos_formando WHERE status = 1");

$registros_aformar = mysql_num_rows($sql_aformar);


$sql_desistentes = mysql_query("SELECT id FROM eventos_formando WHERE status = 3");

$registros_desistentes = mysql_num_rows($sql_desistentes);

$sql_aguardando = mysql_query("SELECT id FROM eventos_formando WHERE status = 4");

$registros_aguardando = mysql_num_rows($sql_aguardando);


$total_formandos = $registros_formados + $registros_aformar + $registros_desistentes + $registros_aguardando;

?>

<table width='100%' border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='440'><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Formandos</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table id="4" width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class="listView">
				<tr height="12">
					<td height="12" colspan='4' class="listViewPaginationTdS1">
						<table width='100%'  border='0' cellspacing='0' cellpadding='0'>
							<tr>
								<td width="40" height="32" valign="middle" align="center">
									<img src="image/bt_formando_gd.gif" />
								<td>
								<td>	      	  											
									<span style="font-size: 12px; color: #444444"><b>Atualmente nosso número de formandos atendidos é <span style="color: #990000"><?php echo $total_formandos ?></span><br/>A se Formar: <span style="color: #990000"><?php echo $registros_aformar ?></span><br/>Formados: <span style="color: #990000"><?php echo $registros_formados ?></span><br/>Aguardando Declaração da Rescisão: <span style="color: #990000"><?php echo $registros_aguardando ?></span><br/>Desistentes: <span style="color: #990000"><?php echo $registros_desistentes ?></span></b></span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
