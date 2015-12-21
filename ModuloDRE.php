<?php 
###########
## Módulo para relatório do demonstrativo do resultado
## Criado: 08/11/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Rotina para verificar se necessita ou não montar o header para o ajax
header("Content-Type: text/html; charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

?>
<script language="JavaScript">

function ExecutaConsulta() 
{
	
	var Form;
 	Form = document.consulta_data;
  
	//Monta url que do relatório que será carregado	
	url = "./relatorios/DemonstrativoResultadoRelatorioPDF.php?MesPesquisa=" + Form.edtMesPesquisa.value + "&AnoPesquisa=" + Form.edtAnoPesquisa.value + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>";
	
	//Executa o relatório selecionado
	abreJanela(url);
	
}
</script>
<form id="consulta_data" name="consulta_data" method="post">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relatório de Demonstrativo de Resultado</span>
					</td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">		
					</td>
				</tr>
			</table>
 	 	</td>
	</tr>
	<tr>
		<td style="padding-bottom: 2px">
			<span >
				<input name="Button" type="button" class="button" id="consulta" title="Emite o relatório pelas datas informadas" value='Emitir Relatório' onclick="ExecutaConsulta()" />
			</span>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="middle"> 
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="padding-right: 0px; padding-left: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="4">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe o per&iacute;odo a pesquisar</td>
										</tr>
									</table>
								</td>
							</tr>			  
							<tr>
								<td class="dataLabel" width="65">Mês:</td>
								<td width="107" class="tabDetailViewDF">
									<select class="dataField" name="edtMesPesquisa" id="edtMesPesquisa">
									<?php
								
										//Efetua o for para montar o combo do dia              
										for ($m=1; $m<=12; $m++) 
										{
	
											//Cria o switch com a descrição do mes
											switch ($m) 
											{
												
												case 1:  $month_name = "Janeiro";	break;
												case 2:  $month_name = "Fevereiro";	break;
												case 3:  $month_name = "Março";	break;
												case 4:  $month_name = "Abril";	break;
												case 5:  $month_name = "Maio";	break;
												case 6:  $month_name = "Junho";	break;
												case 7:  $month_name = "Julho";	break;
												case 8:  $month_name = "Agosto";	break;
												case 9:  $month_name = "Setembro";	break;
												case 10: $month_name = "Outubro";	break;
												case 11: $month_name = "Novembro";	break;
												case 12: $month_name = "Dezembro";	break;
											
											}
								
											//Caso o mes for menor que 10
											if ($m < 10)
											{
										
												$mm = "0" . $m;
										
											} 
											
											else 
											
											{
										
												$mm = $m;
										
											}
									
	
											//Caso o dia for igual a data atual
											if ($m == date("m", mktime())) 
											{
										
												//Alimenta a variável com o valor selected
												$seleciona_mes = "selected";
									
											//Caso nao for	
											} 
											
											else 
											
											{
										
												//Alimenta a variável com o valor vazio
												$seleciona_mes = "";
									
											}
								
											//Gera o combo do dia	
											echo "<option value='$mm' $seleciona_mes>$month_name</option>";
	              
										}
									?>
									</select>
								</td>
								<td width="61" class="dataLabel">Ano:</td>
								<td class="tabDetailViewDF">
									<select class="datafield" name="edtAnoPesquisa" id="edtAnoPesquisa">
									<?php 
			              
										//Efetua o for para montar o combo do ano              
										for ($a=10; $a<=20; $a++) 
										{
											
											$monta_ano = "20" . $a;
											
											//Caso o ano for igual ao ano atual
											if ($monta_ano == date("Y", mktime())) 
											{
												
												//Alimenta a variável com o valor selected
												$seleciona_ano = "selected";
											
											//Caso nao for	
											} 
											
											else 
											
											{
												
												//Alimenta a variável com o valor vazio
												$seleciona_ano = "";
											
											}
										
											//Gera o combo do ano	
											echo "<option value='$monta_ano' $seleciona_ano>$monta_ano</option>";
			              
										}
									?>
									</select>
								</td>                
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>