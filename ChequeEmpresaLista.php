<?php
###########
## Módulo para Listagem dos Cheques
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

//Pega os valores padrão que vem do formulario
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);

//Recebe os valores vindos do formulário
//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) 
{
	
	//Se for 1 então é visualização por situacao
	case 1: 
		//Monta o título da página
		$titulo = "Relação de Cheques da Empresa por Situação"; 
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
  		
			//Se for 0 então é visualização de todos
			case 0:
				$texto_topo = "Listando <b>Todos</b> os Cheques da Empresa";
				$where_situacao = "";
			break;		
			//Se for 1 então é visualização dos recebidos
			case 1:
				$texto_topo = "Listando Cheques da Empresa com o Status: <b>Emitido</b>";
				$where_situacao = "AND che.status = '$TipoSituacao'";
				
				$ExibeTopo = 1;
			break;		
			//Se for 2 então é visualização dos compensados
			case 2:
				$texto_topo = "Listando Cheques da Empresa com o Status: <b>Compensado</b>";
				$where_situacao = "AND che.status = '$TipoSituacao'";
				
				$ExibeTopo = 1;
			break;
		
		}		
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$ExibeTopo = 1;
			
			$texto_topo .= "</br><b>Emitidos entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
			$TextoSQLData = "AND che.data_emissao >= '$dataIni' AND che.data_emissao <= '$dataFim' ";

		}
		
		
		//Monta o sql de filtragem
		$sql = "SELECT 
				che.id,
				che.conta_corrente_id,
				che.numero_cheque,
				che.data_emissao,
				che.pre_datado,
				che.bom_para,
				che.valor,
				che.status,
				che.conta_pagar_id,
				che.data_compensacao,
				cont.nome AS conta_corrente_nome,
				cont.agencia,
				cont.conta,
				cpag.descricao AS conta_pagar_nome
				FROM cheques_empresa che
				LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
				LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
				WHERE 1 = 1 
				$where_situacao $TextoSQLData
				ORDER BY conta_corrente_nome, che.data_emissao";
				

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há cheques da empresa com a situação $texto_situacao";
	break;
	
	//Se for 2 então é visualização por pre datado
	case 2: 
		//Monta o título da página
		$titulo = "Relação de Cheques Pre-datados"; 
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
  		
			//Se for 0 então é visualização de todos
			case 0:
				$ExibeTopo = 1;
				
				$texto_topo = "Listando <b>Todos</b> os Cheques da Empresa";
				$where_situacao = "";
			break;		
			//Se for 1 então é visualização dos recebidos
			case 1:
				$texto_topo = "Listando Cheques da Empresa com o Status: <b>Emitido</b>";
				$where_situacao = "AND che.status = '$TipoSituacao'";
				
				$ExibeTopo = 1;
			break;		
			//Se for 2 então é visualização dos compensados
			case 2:
				$texto_topo = "Listando Cheques da Empresa com o Status: <b>Compensado</b>";
				$where_situacao = "AND che.status = '$TipoSituacao'";
				
				$ExibeTopo = 1;
			break;
		
		}		
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$ExibeTopo = 1;
			
			$texto_topo .= "</br><b>E com a data de Pre-datação (Bom Para) entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
			$TextoSQLData = "AND che.bom_para >= '$dataIni' AND che.bom_para <= '$dataFim' ";

		}
		
		
		//Monta o sql de filtragem
		$sql = "SELECT 
				che.id,
				che.conta_corrente_id,
				che.numero_cheque,
				che.data_emissao,
				che.pre_datado,
				che.bom_para,
				che.valor,
				che.status,
				che.data_compensacao,
				cont.nome AS conta_corrente_nome,
				cont.agencia,
				cont.conta,
				cpag.descricao AS conta_pagar_nome
				FROM cheques_empresa che
				LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
				LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
				WHERE 1 = 1 
				$where_situacao $TextoSQLData
				ORDER BY conta_corrente_nome, che.bom_para";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há cheques pre-datados para o periodo especificado";
	break;
	
	//Se for 3 então é visualização por banco
	case 3: 
		//Monta o título da página
		$titulo = "Relação de Cheques por Conta-Corrente"; 
		$TipoSituacao = $_GET[TipoSituacao];
		$BancoId = $_GET[BancoId];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
  		
			//Se for 0 então é visualização de todos
			case 0:
				$texto_topo = "Listando <b>Todos</b> os Cheques da Empresa";
				$where_situacao = "";
			break;		
			//Se for 1 então é visualização dos recebidos
			case 1:
				$texto_topo = "Listando Cheques da Empresa com o Status: <b>Emitido</b>";
				$where_situacao = "AND che.status = '$TipoSituacao'";
				
				$ExibeTopo = 1;
			break;		
			//Se for 2 então é visualização dos compensados
			case 2:
				$texto_topo = "Listando Cheques da Empresa com o Status: <b>Compensado</b>";
				$where_situacao = "AND che.status = '$TipoSituacao'";
				
				$ExibeTopo = 1;
			break;
		
		}		
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$ExibeTopo = 1;
			
			$texto_topo .= "</br><b>E com a data de Pre-datação (Bom Para) entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
			$TextoSQLData = "AND che.bom_para >= '$dataIni' AND che.bom_para <= '$dataFim' ";

		}
		
		//Captura o valor do banco
		$lista_banco = mysql_query("SELECT
									cco.id,
									cco.nome AS conta_nome,
									cco.agencia,
									cco.conta,
									ban.nome AS banco_nome
									FROM conta_corrente cco 
									LEFT OUTER JOIN bancos ban ON ban.id = cco.banco_id
									WHERE cco.id = $BancoId");
				
		//Executa a query
		$dados_banco = mysql_fetch_array($lista_banco); 
		
		$ExibeTopo = 1;
		
		$BancoNome = $dados_banco["conta_nome"]  . " - Ag: " . $dados_banco["agencia"] . " - Conta: " . $dados_banco["conta"];
		
		//Monta a descrição a exibir
		$texto_topo .="<br/><b>E da Conta-Corrente: </b><span style='color: 990000'>$BancoNome</span>";

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				che.id,
				che.conta_corrente_id,
				che.numero_cheque,
				che.data_emissao,
				che.pre_datado,
				che.bom_para,
				che.valor,
				che.status,
				che.data_compensacao,
				cont.nome AS conta_corrente_nome,
				cont.agencia,
				cont.conta,
				cpag.descricao AS conta_pagar_nome
				FROM cheques_empresa che
				LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
				LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
				WHERE che.conta_corrente_id = $BancoId
				$where_situacao $TextoSQLData
				ORDER BY conta_corrente_nome, che.bom_para";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há cheques desta conta-corrente para o periodo especificado";
		
	break;
	
	//Se for 4 então é visualização por evento
	case 4: 
		//Monta o título da página
		$titulo = "Relação de Cheques por Evento"; $TipoSituacao = $_GET[TipoSituacao];
		$EventoId = $_GET["EventoId"];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
  		
			//Se for 0 então é visualização de todos
			case 0:
				$texto_topo = "Listando <b>Todos</b> os Cheques da Empresa";
				$where_situacao = "";
			break;		
			//Se for 1 então é visualização dos recebidos
			case 1:
				$texto_topo = "Listando Cheques da Empresa com o Status: <b>Emitido</b>";
				$where_situacao = "AND che.status = '$TipoSituacao'";
				
				$ExibeTopo = 1;
			break;		
			//Se for 2 então é visualização dos compensados
			case 2:
				$texto_topo = "Listando Cheques da Empresa com o Status: <b>Compensado</b>";
				$where_situacao = "AND che.status = '$TipoSituacao'";
				
				$ExibeTopo = 1;
			break;
		
		}		
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$ExibeTopo = 1;
			
			$texto_topo .= "</br><b>E com a data de Pre-datação (Bom Para) entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
			$TextoSQLData = "AND che.bom_para >= '$dataIni' AND che.bom_para <= '$dataFim' ";

		}
		
		//Captura o valor do evento
		$lista_evento = mysql_query("SELECT nome FROM eventos WHERE id = $EventoId");
		
		//Executa a query
		$dados_evento = mysql_fetch_array($lista_evento); 
		
		$EventoNome = "(" . $EventoId . ") - " . $dados_evento["nome"];
		
		$ExibeTopo = 1;
		
		//Monta a descrição a exibir
		$texto_topo .="<br/><b>Exibindo Cheques do Evento: </b><span style='color: 990000'>$EventoNome</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				che.id,
				che.conta_corrente_id,
				che.numero_cheque,
				che.data_emissao,
				che.pre_datado,
				che.bom_para,
				che.valor,
				che.status,
				che.data_compensacao,
				cont.nome AS conta_corrente_nome,
				cont.agencia,
				cont.conta,
				cpag.descricao AS conta_pagar_nome
				FROM cheques_empresa che
				LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
				LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
				WHERE che.evento_id = $EventoId 
				$where_situacao $TextoSQLData
				ORDER BY conta_corrente_nome, che.bom_para";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há cheques do evento para o periodo especificado";
		
	break;
	
}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="2" valign='top'>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo"><?php echo $titulo ?></span>			  	
					</td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">			    	
						<?php 
							
							if ($ExibeTopo == 1)
							{
								
								echo $texto_topo; 
							
							}
							
						?>
						<br/>
						<br/>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="PADDING-BOTTOM: 2px">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="right">
						<input class="button" title="Retorna ao Módulo de Cheques da Empresa" name='btnVoltar' type='button' id='btnVoltar' value='Retornar a Cheques da Empresa' onclick="wdCarregarFormulario('ModuloChequesEmpresa.php','conteudo')" />	
					</td>
				</tr>
			</table>
		</td>		
	</tr>
 </table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<?php					   
			 

				//Executa a Query
				$query = mysql_query($sql);		  	  
		  
				//verifica o número total de registros
				$tot_regs = mysql_num_rows($query);
				
				$total_geral = 0;
				$total_recebido = 0;
				$total_compensado = 0;
				$total_devolvido = 0;
			 
			?>
	   
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">

			<?php
			
				//Caso houverem registros
				if ($tot_regs > 0) 
				{ 
					
					
					?>
					
					<tr height="20">
						<td width="80" class="listViewThS1">
							&nbsp;&nbsp;Nr Cheque
						</td>
						<td class="listViewThS1">
							Descrição da Conta
						</td>
						<td width="80" align="right" class="listViewThS1">
							Valor
						</td>
						<td width="80" align="center" class="listViewThS1">
							Emissão
						</td>
						<td width="80" align="center" class="listViewThS1">
							Bom Para
						</td>
						<td width="80" class="listViewThS1">
							<div align="center">Situação</div>
						</td>
					</tr>
					
					<?php
						
				}
	  
				//Caso não houverem registros
				if ($tot_regs == 0) 
				{ 

					//Exibe uma linha dizendo que nao há registros
					echo "
						<tr height='24'>
							<td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
								<slot><font color='#33485C'><strong>$texto_vazio</strong></font></slot>
							</td>
						</tr>";	  
	  
				} 
    
				else 
    
				{

					$edtContaCorrenteId = 0;
					
					//Cria o array e o percorre para montar a listagem dinamicamente
					while ($dados = mysql_fetch_array($query))
					{

						//Efetua o switch para a descrição do status
						switch ($dados["status"]) 
						{
		
							//Se for em aberto
							case 1: 
								$desc_status = "Emitido";
								$total_recebido = $total_recebido + $dados_rec[valor];								
							break;
							//Se for compensado
							case 2: 
								$desc_status = "Compensado"; 
								$total_compensado = $total_compensado + $dados_rec[valor];
							break;
									
						
						}
						
						//Caso seja de outra conta-corrente
						if ($dados["conta_corrente_id"] != $edtContaCorrenteId)
						{
						
							//Verifica se nao e a primeira conta listada, para dai mostrar o total
							if ($edtContaCorrenteId != 0)
							{
							
							?>
							<tr height="18">
								<td colspan="2" align="right">
									Total da Conta:
								</td>
								<td align="right">
									<b><?php echo number_format($edtTotalValor, 2, ",", ".") ?></b>
								</td>
								<td colspan="3">
									&nbsp;
								</td>
							</tr>
							<?php
							
							$edtTotalValor = 0;
							
							}
						?>
						
						<tr height="18">
							<td colspan="6" style="padding-left: 5px">
								<span style='color: #990000'><b><?php echo $dados["conta_corrente_nome"] . " - Ag: " . $dados["agencia"] . " - Conta: " . $dados["conta"]  ?></b></span>
							</td>
						</tr>
						
						<?php
							
						}
    

					//Fecha o php, mas o while continua
					?>

					<tr height="18">
						<td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-left: 20px; border-bottom: 1px dashed;">
							<span style="color: #33485C"><b><?php echo $dados["numero_cheque"] ?></b></span>
						</td> 
						<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
							<?php echo $dados["conta_pagar_nome"] ?>
						</td>
						<td align="right" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
							<?php echo number_format($dados["valor"],2,",",".") ?>
						</td>
						<td align="center" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
							<?php echo DataMySQLRetornar($dados["data_emissao"]) ?>
						</td>
						<td align="center" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
							<?php echo DataMySQLRetornar($dados["bom_para"]) ?>
						</td>
						<td align="center" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
							<?php echo $desc_status ?>
						</td>
					</tr>
					<?php
					
					$edtTotalValor = $edtTotalValor + $dados["valor"];
					
					$edtTotalGeralValor = $edtTotalGeralValor + $dados["valor"];
					
					$edtContaCorrenteId = $dados["conta_corrente_id"];
						
					}
	
					//Fecha o if de se tem registros
					}

					//Verifica se precisa imprimir o rodapé
					if ($tot_regs > 0) 
					{ 
				?>

				<tr height="22">
					<td colspan="2" align="right">
						Total da Conta:
					</td>
					<td align="right">
						<b><?php echo number_format($edtTotalValor, 2, ",", ".") ?></b>
					</td>
					<td colspan="3">
						&nbsp;
					</td>
				</tr>
				<tr height="22">
					<td colspan="2" align="right">
						Total Geral:
					</td>
					<td align="right">
						<b><?php echo number_format($edtTotalGeralValor, 2, ",", ".") ?></b>
					</td>
					<td colspan="3">
						&nbsp;
					</td>
				</tr>	
	
				<?php
				
					//Fecha o IF
					};
				
				?>
		
			</table>	
		</td>
	</tr>  
</table>
