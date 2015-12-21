<?php 
###########
## Módulo para Ajuste dos boletos na segunda-feira
## Criado: 29/05/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
###########


//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

?>

<script language="JavaScript">

</script>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="vale" action="AjustaSite.php" method="post" onsubmit="return wdSubmit">

<br/>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440">
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Ajuste de Datas do Processamento de Titulos</span>
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">

						<?php
		    		
							//Verifica se a página está abrindo vindo de uma postagem
							if($_POST['Submit']) 
							{
				  	
								//Recupera os valores vindo do formulário e atribui as variáveis
								$edtEmpresaId = $empresaId;
								$edtDataAtual = DataMySQLInserir($_POST["edtDataAtual"]);
								$edtDataAjustada = DataMySQLInserir($_POST["edtDataAjustada"]);      		        
		        
								//Conecta ao servidor local
								//Seta o banco principal
								$DB = 'base_consoli';

								//Define a sting de conexão
								$conexao = @mysql_connect("localhost","root","") or die('Nao foi possivel se conectar com o banco de dados');

								//Conecta ao banco de dados principal
								$base = @mysql_select_db($DB) or die("Nao foi possivel selecionar a base: $DB");
								
								//Monta e executa a query
								$sql = mysql_query("update boleto set data_vencimento = '$edtDataAjustada', data_reajuste = '$edtDataAjustada' where data_reajuste = '$edtDataAtual'");
            
								
								//Dados do servidor remoto
								$Server_atu = 'mysql.consolieventos.com.br';
								//$Server_atu = 'localhost';
								$Login_atu = 'consolieventos';
								//$Login_atu = 'root';
								$Senha_atu = 'consoli2010';
								//$Senha_atu = '';
								$DB_atu = 'consolieventos';
								//$DB_atu = 'workeventos';


								//Conecta ao banco de dados online
								//Define a sting de conexão
								$conexao = @mysql_connect($Server_atu,$Login_atu,$Senha_atu) or die('Nao foi possivel se conectar com o banco de dados do servidor de destino !');

								//Conecta ao banco de dados principal
								$base = @mysql_select_db($DB_atu) or die('Nao foi possivel selecionar a base: $DB_atu no servidor de destino !');

								//Monta e executa a query
								$sql = mysql_query("update WORK_boleto set data_vencimento = '$edtDataAjustada', data_reajuste = '$edtDataAjustada' where data_reajuste = '$edtDataAtual'");
								
								//echo "update WORK_boleto set data_vencimento = '$edtDataAjustada', data_reajuste = '$edtDataAjustada' where data_reajuste = '$edtDataAtual'";
								
								//echo "Data Atual no Site: " . $edtDataAtual;
								//echo "<br/>Data para Ajuste: " . $edtDataAjustada;
								
								echo "Ajustes definidos com sucesso !!!<br/><br/>";
             
								//Exibe a mensagem de inclusão com sucesso
								//echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Vale ao colaborador cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
							
							}
						
						?>

						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td style="PADDING-BOTTOM: 2px">
									<input name="Submit" type="submit" class="button" title="Processa os Titulos no Site" value="Ajustar Processamento" />
								</td>
							</tr>
						</table>
           
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="500" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="18">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Ajuste dos Titulos </td>
										</tr>
									</table>								
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="130">
									<span class="dataLabel">Data no Site:</span>             
								</td>
								<td class="tabDetailViewDF">
									<?php
								   
										//Define a data do formul&aacute;rio
										$objData->strFormulario = "vale";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataAtual";
										$objData->strRequerido = true;
										//Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
										$objData->strValor = '';
										//Define o tamanho do campo 
										//$objData->intTamanho = 15;
										//Define o n&uacute;mero maximo de caracteres
										//$objData->intMaximoCaracter = 20;
										//define o tamanho da tela do calendario
										//$objData->intTamanhoCalendario = 200;
										//Cria o componente com seu calendario para escolha da data
										$objData->CriarData();
									?>				 
								</td>
							</tr>	
							<tr>
								<td class="dataLabel">
									<span class="dataLabel">Data Ajustada:</span>             
								</td>
								<td class="tabDetailViewDF">
									<?php
								   
										//Define a data do formul&aacute;rio
										$objData->strFormulario = "vale";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataAjustada";
										$objData->strRequerido = true;
										//Valor a constar dentro do campo (p/ altera&ccedil;&atilde;o)
										$objData->strValor = date("d/m/Y", mktime());
										//Define o tamanho do campo 
										//$objData->intTamanho = 15;
										//Define o n&uacute;mero maximo de caracteres
										//$objData->intMaximoCaracter = 20;
										//define o tamanho da tela do calendario
										//$objData->intTamanhoCalendario = 200;
										//Cria o componente com seu calendario para escolha da data
										$objData->CriarData();
									?>				 
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