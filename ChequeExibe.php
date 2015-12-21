<?php 
###########
## Módulo para Exibição dos dados dos cheques
## Criado: 11/09/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA)
{
	
	$ANO = 0000;
	$MES = 00;
	$DIA = 00;
	$HORA = "00:00:00";
	$data_array = split("[- ]",$DATA);
	if ($DATA <> "")
	{
		$ANO = $data_array[0];
		$MES = $data_array[1];
		$DIA = $data_array[2];
		$HORA = $data_array[3];
		return $DIA."/".$MES."/".$ANO. " - " . $HORA;
	}
	
	else 
	
	{
    
		$ANO = 0000;
		$MES = 00;
		$DIA = 00;
		return $DIA."/".$MES."/".$ANO;
	
	}

}

//Pega o valor da cliente a exibir
$ChequeId = $_GET["ChequeId"];

//Monta o SQL
$sql = "SELECT 
		che.id,
		che.numero_cheque,
		che.pre_datado,
		che.bom_para,
		che.banco_id,
		che.conta,
		che.agencia,
		che.favorecido,
		che.valor,
		che.data_vencimento,
		che.data_recebimento,
		che.status,
		che.disposicao,
		che.observacoes,
		che.data_devolucao,
		ban.nome as banco_nome
		FROM cheques che
		LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
		WHERE che.id = '$ChequeId'";
  
//Executa a query
$resultado = mysql_query($sql);

//Monta o array dos campos
$campos = mysql_fetch_array($resultado);

//Efetua o switch para o campo de status
switch ($campos[status]) 
{
	case 1: $desc_status = "Recebido"; break;
	case 2: $desc_status = "Compensado"; break;
	case 3: $desc_status = "Devolvido"; break;
} 

//Efetua o switch para o campo de disposicao
switch ($campos[disposicao]) 
{
	case 1: $desc_disposicao = "Primeiro Contato"; break;
	case 2: $desc_disposicao = "Em Negociação"; break;
	case 3: $desc_disposicao = "Reapresentado"; break;
	case 4: $desc_disposicao = "Pago"; break;
	case 5: $desc_disposicao = "Para Registrar"; break;
	case 6: $desc_disposicao = "No SPC"; break;
	case 7: $desc_disposicao = "Nao Pode SPC"; break;
	case 8: $desc_disposicao = "SPC Pago"; break;
	case 9: $desc_disposicao = "Devolvido ao Titular"; break;
	case 10: $desc_disposicao = "Cobrança Judicial"; break;
	case 11: $desc_disposicao = "ACC"; break;
	
}      

//Efetua o switch para o campo de pre datado
switch ($campos[pre_datado]) 
{
	case 0: $desc_pre = "Näo"; break;
	case 1: $desc_pre = "Sim"; break;
}

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização do Cheque</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>
	
			<table width="750" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="text">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td width="100" style="PADDING-BOTTOM: 2px">
									<form name="frmContaExibe" action="#">
										<input name="btnEditarConta" type="button" class="button" title="Edita este Cheque" value="Editar Cheque" onclick="wdCarregarFormulario('ChequeAltera.php?Id=<?php echo $campos[id] ?>&headers=1','conteudo')">
									</form>
								</td>

								<td width="90" style="PADDING-BOTTOM: 2px">
									<?php
										
										//Verifica o nível de acesso do usuário
									
										//Exibe o botão de excluir
										echo "<form id='exclui' name='exclui' action='ProcessaExclusao.php' method='post'><input class=button title='Exclui este Cheque [Alt+X]' accessKey='X' onClick='return confirm(\"Confirma a exclusão deste Cheque ?\")' type='submit' value='Excluir' name='Delete'><input name='Id' type='hidden' value=$campos[id] /><input name='Modulo' type='hidden' value='cheques' /></form>";
									
									?>
								</td>
								<td align="right" style="PADDING-BOTTOM: 2px">
									&nbsp;
								</td>
							</tr>
						</table>
           
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" >
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="2">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left">
												<img src="image/bt_cadastro.gif" width="16" height="15" /> Caso desejar alterar este Cheque, clique em [Editar Cheque]								
											</td>
										</tr>
									</table>					
								</td>
							</tr>
							<tr>
								<td class="dataLabel" width="18%">Número:</td>
								<td valign="middle" class="tabDetailViewDF">
									<span><strong><?php echo $campos[numero_cheque] ?></strong></span>				  
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Pré-datado:</td>
								<td valign="middle" class="tabDetailViewDF">
									<span><strong><?php echo $desc_pre ?></strong></span>				  
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Bom Para:</td>
								<td valign="middle" class="tabDetailViewDF">
									<span><strong>
									<?php 
									
										if ($campos["bom_para"] != "0000-00-00")
										{
										
											echo DataMySQLRetornar($campos[bom_para]);
											
										}
										
										else
										
										{
											
											echo  " ";
											
										}
										
									?>
									</strong></span>				  
								</td>
							</tr>							
							<tr>
								<td class="dataLabel">Banco:</td>
								<td valign="middle" class="tabDetailViewDF"><?php echo $campos[banco_nome] ?></td>
							</tr>
							<tr>
								<td class="dataLabel">Agência:</td>
								<td class='tabDetailViewDF'><?php echo $campos[agencia] ?></td>
							</tr>
							<tr>
								<td class="dataLabel">Conta:</td>
								<td valign="middle" class="tabDetailViewDF"><?php echo $campos[conta] ?></td>
							</tr>
							<tr>
								<td class="dataLabel">Titular:</td>
								<td class='tabDetailViewDF'><?php echo $campos[favorecido] ?></td>
							</tr>
							<tr>
								<td class="dataLabel">Valor:</td>
								<td class='tabDetailViewDF'><?php echo "R$ " . number_format($campos[valor], 2, ",", ".") ?></td>
							</tr>							
							<tr>
								<td class="dataLabel">Data Recebimento:</td>
								<td class='tabDetailViewDF'><?php echo DataMySQLRetornar($campos[data_recebimento]) ?></td>
							</tr>
							<tr>
								<td class="dataLabel">Status:</td>
								<td class='tabDetailViewDF'><b><?php echo $desc_status ?></b></td>
							</tr>
							<?php
							
								//Caso o status seja 3, entao exibe as disposicao
								if ($campos["status"] == 3)
								{
								
							?>
							<tr>
								<td class="dataLabel">Data de Devolução:</td>
								<td valign="middle" class="tabDetailViewDF">
									<span><strong>
									<?php 
									
										if ($campos["data_devolucao"] != "0000-00-00")
										{
										
											echo DataMySQLRetornar($campos["data_devolucao"]);
											
										}
										
										else
										
										{
											
											echo  " ";
											
										}
										
									?>
									</strong></span>				  
								</td>
							</tr>
							<tr>
								<td class="dataLabel">Disposição:</td>
								<td class='tabDetailViewDF'><b><?php echo $desc_disposicao ?></b></td>
							</tr>
							<?php
							
								}
								
							?>
							<tr>
								<td valign="top" class="dataLabel">Observa&ccedil;&otilde;es:</td>
								<td class="tabDetailViewDF"><?php echo nl2br($campos[observacoes]) ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>  	 
		</td>
	</tr>
</table>
</td>
