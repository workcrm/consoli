<?php 
###########
## Módulo para cadastro de Adendos do evento
## Criado: 28/02/2013 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

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

//Recupera o id do evento
if($_POST) 
{
	
	$EventoId = $_POST["EventoId"]; 

} 

else 
{
	
	$EventoId = $_GET["EventoId"]; 

}

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

//Monitora o evento escolhido para o bactracking do usuário
$sql_backtracking = mysql_query("UPDATE usuarios SET evento_id = '$EventoId' WHERE usuario_id = '$usuarioId'");

echo "<script>wdCarregarFormulario('UltimoEvento.php','ultimo_evento',2)</script>";

//Recupera dos dados do evento
$sql_evento  = "SELECT 
				eve.id,
				eve.nome,
				eve.descricao,
				eve.tipo,
				eve.status,
				eve.grupo_id,
				eve.cliente_id,
				eve.responsavel_orcamento,
				eve.responsavel,
				eve.contato1,
				eve.contato_obs1,
				eve.contato_fone1,
				eve.contato2,
				eve.contato_obs2,
				eve.contato_fone2,
				eve.contato3,
				eve.contato_obs3,
				eve.contato_fone3,
				eve.contato4,
				eve.contato_obs4,
				eve.contato_fone4,
				eve.contato5,
				eve.contato_obs5,
				eve.contato_fone5,
				eve.contato6,
				eve.contato_obs6,
				eve.contato_fone6,
				eve.contato7,
				eve.contato_obs7,
				eve.contato_fone7,
				eve.contato8,
				eve.contato_obs8,
				eve.contato_fone8,
				eve.contato9,
				eve.contato_obs9,
				eve.contato_fone9,
				eve.contato10,
				eve.contato_obs10,
				eve.contato_fone10,
				eve.contato11,
				eve.contato_obs11,
				eve.contato_fone11,
				eve.contato12,
				eve.contato_obs12,
				eve.contato_fone12,
				eve.data_realizacao,
				eve.hora_realizacao,
				eve.duracao,
				eve.numero_confirmado,
				eve.lugares_ocupados,
				eve.alunos_colacao,
				eve.alunos_baile,
				eve.participantes_baile,
				eve.observacoes,
				eve.observacoes_financeiro,
				eve.exibir_observacoes,
				eve.cadastro_timestamp,
				eve.cadastro_operador_id,
				eve.alteracao_timestamp,
				eve.alteracao_operador_id,
				eve.data_jantar,
				eve.hora_jantar,
				eve.data_certame,
				eve.hora_certame,
				eve.data_foto_convite,
				eve.hora_foto_convite,
				eve.local_foto_convite,
				eve.data_ensaio,
				eve.obs_ensaio,
				eve.data_culto,
				eve.obs_culto,
				eve.data_colacao,
				eve.obs_colacao,
				eve.data_baile,
				eve.obs_baile,
				eve.quebras,
				eve.valor_foto,
				eve.valor_dvd,
				eve.obs_fotovideo,
				eve.numero_nf,
				eve.roteiro,
				eve.posicao_financeira,
				eve.valor_colacao,
				eve.valor_baile,
				eve.valor_evento,
				eve.valor_geral_evento,
				eve.valor_desconto_evento,
				usu_cad.nome AS operador_cadastro_nome, 
				usu_cad.sobrenome AS operador_cadastro_sobrenome,
				usu_alt.nome AS operador_alteracao_nome, 
				usu_alt.sobrenome AS operador_alteracao_sobrenome,
				cli.id AS cliente_id,
				cli.nome AS cliente_nome,
				cli.endereco AS cliente_endereco,
				cli.complemento AS cliente_complemento,
				cli.bairro AS cliente_bairro,
				cli.cidade_id,
				cli.cep AS cliente_cep,
				cli.uf AS cliente_uf,
				cli.telefone AS cliente_telefone,
				cli.fax AS cliente_fax,
				cli.celular AS cliente_celular,
				cli.email AS cliente_email,
				cid.nome AS cliente_cidade,
				reg.nome AS regiao_nome
				FROM eventos eve 
				LEFT OUTER JOIN clientes cli ON cli.id = eve.cliente_id
				LEFT OUTER JOIN regioes reg ON reg.id = eve.regiao_id
				LEFT OUTER JOIN cidades cid ON cid.id = cli.cidade_id
				LEFT OUTER JOIN usuarios usu_cad ON eve.cadastro_operador_id = usu_cad.usuario_id 
				LEFT OUTER JOIN usuarios usu_alt ON eve.alteracao_operador_id = usu_alt.usuario_id							
				WHERE eve.id = $EventoId";
  
//Executa a query
$resultado = mysql_query($sql_evento);

//Cria o array dos dados
$dados_evento = mysql_fetch_array($resultado);

//Efetua o switch para o campo de status
switch ($dados_evento['status']) 
{
	
	case 0: $desc_status = "Em orçamento"; break;
	case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Realizado"; break;
	case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;

} 

switch ($dados_evento['grupo_id']) 
{
	
	case 1: $grupo_status = "Consoli Rio do Sul"; break;
	case 2: $grupo_status = "Consoli Joinville"; break;
	case 3: $grupo_status = "Gerri Adriani Consoli ME"; break;	

}
?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
	
	ID = document.getElementById(id);
	ID.style.display = "none";

}

function valida_form() 
{
	
	var Form;
	Form = document.cadastro;
	
	if (Form.edtDescricao.value == 0) 
	{
		
		alert("É necessário informar a descricao da data do Evento !");
		Form.edtDescricao.focus();
		return false;
   
   }
   
   return true;
   
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Adendos do Evento</span>
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
		<td>
			<input name="voltar" type="button" class="button" id="voltar" title="Voltar para a exibição do evento" value="Voltar para Exibição do Evento" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo',0,0,'1')">
			<br/>
			<br/>
			<?php
		
				//Recupera os valores vindos do formulário e armazena nas variaveis
				if($_POST['Submit'])
				{

					$edtEventoId = $_POST["EventoId"];
					$edtConfirmados = $_POST["edtConfirmados"];
					$edtLugaresOcupados = $_POST["edtLugaresOcupados"];
					$edtAlunosColacao = $_POST["edtAlunosColacao"];
					$edtAlunosBaile = $_POST["edtAlunosBaile"];
					$edtConvidadosBaile = $_POST["edtConvidadosBaile"];
					$edtValorColacao = MoneyMySQLInserir($_POST["edtValorColacao"]);
					$edtValorBaile = MoneyMySQLInserir($_POST["edtValorBaile"]);
					$edtValorDesconto = MoneyMySQLInserir($_POST["edtValorDesconto"]);
					$edtTotalFormando = MoneyMySQLInserir($_POST["edtTotalFormando"]);
					$edtTotalGeral = MoneyMySQLInserir($_POST["edtTotalGeral"]);
					$edtObservacoes = $_POST["edtObservacoes"];

					//Monta o sql e executa a query de inserção
					$sql = mysql_query("INSERT INTO eventos_adendo (
										evento_id,
										data,								
										hora,
										usuario_id,
										pessoas_confirmadas,
										lugares_montados,
										alunos_colacao,
										alunos_baile,
										participantes_baile,
										valor_colacao,
										valor_baile,
										valor_desconto_individual,
										valor_total_individual,
										valor_geral_evento,
										detalhamento

										) VALUES (

										'$edtEventoId',
										now(),
										now(),
										'$usuarioId',
										'$edtConfirmados',
										'$edtLugaresOcupados',
										'$edtAlunosColacao',
										'$edtAlunosBaile',
										'$edtConvidadosBaile',
										'$edtValorColacao',
										'$edtValorBaile',
										'$edtValorDesconto',
										'$edtTotalFormando',
										'$edtTotalGeral',
										'$edtObservacoes'
										);");
										
					$sql_atu_evento = mysql_query("UPDATE eventos SET
												numero_confirmado = '$edtConfirmados',
												lugares_ocupados = '$edtLugaresOcupados',
												alunos_colacao = '$edtAlunosColacao',
												alunos_baile = '$edtAlunosBaile',
												participantes_baile = '$edtConvidadosBaile',
												valor_colacao = '$edtValorColacao',
												valor_baile = '$edtValorBaile',
												valor_evento = '$edtTotalFormando',
												valor_geral_evento = '$edtTotalGeral',
												valor_desconto_evento = '$edtValorDesconto'
												WHERE id = $edtEventoId;");
										
										
				?>
				<div id="99">
					<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
						<tr>
							<td height="22" width="20" valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px">
								<img src="./image/bt_informacao.gif" border="0" />
							</td>
							<td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px">
								<strong>Adendo cadastrado com sucesso !</strong>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
				<script>setTimeout('oculta(99)', 2500)</script>
				<?php
				
				}
			?>
		</td>
	</tr>
	<tr>
		<td>
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
				<tr>
					<td class="dataLabel" width="130">Nome do Evento: </td>
					<td colspan="5" class="tabDetailViewDF">
						<span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
					</td>
				</tr>
				<tr>
					<td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
					<td colspan="5" valign="middle" class="tabDetailViewDF">
						<?php echo nl2br($dados_evento["descricao"]) ?>
					</td>
				</tr>
				<tr>
					<td valign="top" class='dataLabel'>Status:</td>
					<td colspan="5" valign="middle" class="tabDetailViewDF">
						<?php echo $desc_status ?>
				  	</td>
				</tr>
				<tr>
					<td width="130" valign="top" class="dataLabel">Data:</td>
					<td width="100" valign="middle" class="tabDetailViewDF">
						<?php echo DataMySQLRetornar($dados_evento["data_realizacao"]) ?>
				  	</td>
					<td width="40" valign="middle" class="dataLabel">Hora:</td>
					<td width="80" valign="middle" class="tabDetailViewDF">
						<?php echo $dados_evento["hora_realizacao"] ?>								
				  	</td>
					<td width="40" valign="middle" class="dataLabel">Dura&ccedil;&atilde;o:</td>
					<td valign="middle" class="tabDetailViewDF">
						<?php echo $dados_evento["duracao"] ?>								
				  	</td>
				</tr>
				<tr>
					<td valign="top" class="dataLabel">Cliente:</td>
					<td colspan="5" valign="middle" class="tabDetailViewDF">
						<?php echo $dados_evento["cliente_nome"] ?>
				  	</td>
				</tr>
				<tr>
					<td valign="top" class="dataLabel">Grupo:</td>
					<td colspan="5" valign="middle" class="tabDetailViewDF">
						<?php echo $grupo_status ?>
				  	</td>
				</tr>               
				<tr>
					<td class="dataLabel">Respons&aacute;vel:</td>
					<td colspan="5" valign="middle" class="tabDetailViewDF">
						<?php echo $dados_evento["responsavel"] ?>								
				  	</td>
				</td>
				<tr>
					<td valign="top" class="dataLabel">Contatos:</td>
					<td colspan="5" valign="middle" class="tabDetailViewDF">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr valign="middle">
								<td width="280" height="20">Nome:</td>
								<td width="260" height="20">Observações:</td>
								<td height="20">Telefone:</td>
							</tr>
							<tr valign="middle">
								<td height="20">
									<span style="font-size: 12px"><?php echo $dados_evento[contato1] ?></span>
								</td>
								<td height='20'>
									<span style="font-size: 12px"><?php echo $dados_evento[contato_obs1] ?></span>
								</td>
								<td height="20">
									<span style="font-size: 12px"><?php echo $dados_evento[contato_fone1] ?></span>
								</td>
							</tr>
							<tr valign="middle">
								<td height="20">
									<span style="font-size: 12px"><?php echo $dados_evento[contato2] ?></span>                   
								</td>
								<td height="20">
									<span style="font-size: 12px"><?php echo $dados_evento[contato_obs2] ?></span>
								</td>
								<td height="20">
									<span style="font-size: 12px"><?php echo $dados_evento[contato_fone2] ?></span>
								</td>
							</tr>
							<tr valign="middle">
								<td height="20">
									<span style="font-size: 12px"><?php echo $dados_evento[contato3] ?></span>
								</td>
								<td height="20">
									<span style="font-size: 12px"><?php echo $dados_evento[contato_obs3] ?></span>
								</td>
								<td height="20">
									<span style="font-size: 12px"><?php echo $dados_evento[contato_fone3] ?></span>
								</td>
							</tr>                 
						</table>               							 
					</td>
				</tr>             
			</table>

			<?php 
			
				//Monta um sql para pesquisar se há registros
				$sql_datas = mysql_query("SELECT 
										ade.evento_id,
										ade.data,
										ade.hora,
										ade.usuario_id,
										ade.pessoas_confirmadas,
										ade.lugares_montados,
										ade.alunos_colacao,
										ade.alunos_baile,
										ade.participantes_baile,
										ade.valor_colacao,
										ade.valor_baile,
										ade.valor_desconto_individual,
										ade.valor_total_individual,
										ade.valor_geral_evento,
										ade.detalhamento,
										usu.nome AS usuario_nome
										FROM eventos_adendo ade
										LEFT OUTER JOIN usuarios usu ON usu.usuario_id = ade.usuario_id
										WHERE ade.evento_id = $EventoId 
										ORDER BY ade.data");
																		 
				$registros = mysql_num_rows($sql_datas); 														

			?>

			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>  
					<td>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Adendos cadastrados para o Evento:</span></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table id="4" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">

							<?php
							
								//Caso houverem registros
								if ($registros > 0) 
								{ 

									//Exibe o cabeçalho da tabela
									echo "
									<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
										<td width='140' style='padding-left: 8px'>Data/Hora</td>
										<td>Usuário</td>
										<td width='90' align='right'>Total do Evento</td>
									</tr>";
									
								}
    	
								//Caso não houverem registros
								if ($registros == 0) 
								{ 
	
									//Exibe uma linha dizendo que nao registros
									echo "
									<tr height='24'>
										<td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
											<font color='#33485C'><b>Não há adendos cadastradas para este evento</b></font>
										</td>
									</tr>";
									
								}     	

								//Cria o array e o percorre para montar a listagem das categorias
								while ($dados_consulta = mysql_fetch_array($sql_datas))
								{

									?>

									<tr valign="middle">
										<td height="24" valign="middle"  bgcolor="#fdfdfd" style="border-top: 1px #aaa solid; padding-left: 8px">
											<span style="font-size: 12px"><?php echo DataMySQLRetornar($dados_consulta['data']) . " - " . substr($dados_consulta['hora'],0,5) ?></span>
										</td>
										<td valign="middle" bgcolor="#fdfdfd" style="border-top: 1px #aaa solid; padding-bottom: 1px;">
											<span style="color: #990000; font-size: 12px"><b><?php echo $dados_consulta['usuario_nome'] ?></b></span>       
										</td>
										<td valign="middle" bgcolor="#fdfdfd" style="border-top: 1px #aaa solid; padding-bottom: 1px; padding-right: 6px" align="right">
											<span style="font-size: 12px"><b><?php echo number_format($dados_consulta['valor_geral_evento'],2,',','.') ?></b></span>       
										</td>
									</tr>
									<tr>
										<td>
											&nbsp;
										</td>
										<td>
											<?php 
											
												echo "[Pessoas Confirmadas: <b>" . $dados_consulta['pessoas_confirmadas'] . '</b>] - ';
												echo "[Lugares Montados: <b>" . $dados_consulta['lugares_montados'] . '</b>]<br/>';
												echo "[Alunos na Colação: <b>" . $dados_consulta['alunos_colacao'] . '</b>] - ';
												echo "[Alunos no Baile: <b>" . $dados_consulta['alunos_baile'] . '</b>]<br/>';
												echo "[Participantes no Baile: <b>" . $dados_consulta['participantes_baile'] . '</b>] - ';
												echo "[Pessoas Confirmadas: <b>" . $dados_consulta['pessoas_confirmadas'] . '</b>]<br/>';
												echo "[Valor Colaçao/Formando: <b>" . $dados_consulta['valor_colacao'] . '</b>] - ';
												echo "[Valor Baile/Formando: <b>" . $dados_consulta['valor_baile'] . '</b>] - ';
												echo "[Total do Formando: <b>" . $dados_consulta['valor_total_individual'] . '</b>]<br/>';
												echo "[Total de Descontos do Evento: <b>" . $dados_consulta['valor_desconto_individual'] . '</b>]<br/>';
												echo "[Total GERAL do Evento: <b>" . $dados_consulta['valor_geral_evento'] . '</b>]<br/>';
												
												echo "-----<br/><i>" . nl2br($dados_consulta['detalhamento']) . "</i>"; 
												
											?>
										</td>
										<td>&nbsp;</td>
									</tr>

									<?php
		  
								//Fecha o WHILE
								}
		
							?>
						</table>
					</td>
				</tr>
			</table>
			</br>
			<form id="form" name="cadastro" action="sistema.php?ModuloNome=AdendosEventoCadastra" method="post" onsubmit="return valida_form()">
			<table cellSpacing="0" cellPadding="0" width="520" border="0">
				<tr>
					<td style="padding-bottom: 2px">
						<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Adendo">
						<input name="Reset" type="reset" class="button" title="Limpa o conteúdo dos campos digitados" id="Reset" value="Limpar Campos">
						<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
					</td>
				</tr>
			</table>
           
			<table class="tabDetailView" cellSpacing="0" cellPadding="0" width="100%" border="0">
				<tr>
					<td class="listViewPaginationTdS1" style="padding-right: 0px; padding-left: 0px; font-weight: normal; padding-bottom 0px; padding-top: 0px; border-bottom: 0px" colspan="6">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="tabDetailViewDL" style="text-align: left">
									<img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do adendo do evento e clique em [Salvar Adendo] <br/><span style='color: #990000'><b>ATENÇÃO: Adendos não podem ser editados nem excluídos posteriormente. Caso necessite alguma alteração, crie um novo adendo e justifique a mudança.</b></span>
								</td>
							</tr>
						</table>             
					</td>
				</tr>
				<tr>
					<td valign="middle" class="dataLabel">Data:</td>
					<td valign="middle" class="tabDetailViewDF">
						<b><?php echo date('d/m/Y') ?></b>				 
					</td>
					<td valign="middle" class="dataLabel">Hora:</TD>
					<td colspan="3" valign="middle" class="tabDetailViewDF">
						<b><?php echo date('H:i') ?></b>
					</td>
				</tr>
				<tr>
					<td class="dataLabel">Usuário:</td>
					<td colspan="5" class="tabDetailViewDF">
						<b><?php echo $usuarioNome ?></b>
					</td>
				</tr>
				<tr>
					<td valign="top" class="dataLabel">Pessoas Confirmadas:</td>
					<td class="tabDetailViewDF" valign="middle">
						<input name="edtConfirmados" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de pessoas confirmadas para o evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $dados_evento[numero_confirmado] ?>"/>
					</td>
					<td valign="top" class="dataLabel">Lugares Montados:</td>
					<td colspan="3" class="tabDetailViewDF" valign="middle">
						<input name="edtLugaresOcupados" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de lugares ocupados para o evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $dados_evento[lugares_ocupados] ?>" />
					</td>
				</tr>
				<tr>
					<td width="120" valign="top" class="dataLabel">Alunos na Colação:</td>
					<td width="90" class="tabDetailViewDF" valign="middle">
						<input name="edtAlunosColacao" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de alunos na colação do evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $dados_evento[alunos_colacao] ?>" />
					</td>
					<td width="120" valign="top" class="dataLabel">Alunos no Baile:</td>
					<td width="90" class="tabDetailViewDF" valign="middle">
						<input name="edtAlunosBaile" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de alunos no baile do evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $dados_evento[alunos_baile] ?>" />
					</td>
					<td width="140" valign="top" class="dataLabel">Participantes no Baile:</td>
					<td width="120" class="tabDetailViewDF" valign="middle">
						<input name="edtConvidadosBaile" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de convidados no baile do evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $dados_evento[participantes_baile] ?>" />
					</td>
				</tr>
				<tr>
					<td valign="top" class="dataLabel">Valor da Colação/Formando:</td>
					<td valign="middle" class="tabDetailViewDF">
						<?php
							$valor_alterar = str_replace(".",",",$dados_evento[valor_colacao]);
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorColacao";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = $valor_alterar;
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();
						?>						 
					</td>
					<td valign="middle" class="dataLabel">Valor do Baile/Formando:</td>
					<td valign="middle" class="tabDetailViewDF">
						<?php
							$valor_alterar = str_replace(".",",",$dados_evento[valor_baile]);
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorBaile";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = $valor_alterar;
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();
						?>						 
					</td>
					<td valign="middle" class="dataLabel">Total do Formando:</td>
					<td valign="middle" class="tabDetailViewDF">
						<?php
							
							$valor_alterar = str_replace(".",",",$dados_evento[valor_evento]);
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtTotalFormando";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = $valor_alterar;
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();
						?>	
												 
					</td> 								
				</tr>
				<tr>
					<td valign="top" class="dataLabel">Total de Desconto/Evento:</td>
					<td valign="middle" class="tabDetailViewDF">
						<?php
							
							$valor_alterar = str_replace(".",",",$dados_evento[valor_desconto_evento]);
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorDesconto";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = $valor_alterar;
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();
						?>					 
					</td>
					<td valign="middle" class="dataLabel">Total GERAL do Evento:</td>
					<td colspan="3" valign="middle" class="tabDetailViewDF">
						<?php
							
							$valor_alterar = str_replace(".",",",$dados_evento[valor_geral_evento]);
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtTotalGeral";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = $valor_alterar;
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();
						?>						 
					</td>
				</tr>	
				<tr>
					<td valign="top" class="dataLabel">Detalhamento/Observações:</td>
					<td colspan="5" class=tabDetailViewDF>
						<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>
					</td>
				</tr>
			</table>
			</form>	
		</td>
	</tr>
</table>  
 