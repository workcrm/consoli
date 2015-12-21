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
		$titulo = "Relação de Cheques por Situação"; 
		$TipoSituacao = $_GET[TipoSituacao];
		$TipoDisposicao = $_GET[TipoDisposicao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) 
		{
  		
			//Se for 0 então é visualização de todos
			case 0:
				$texto_situacao = "Todos";
				$where_situacao = "";
			break;		
			//Se for 1 então é visualização dos recebidos
			case 1:
				$texto_situacao = "Recebido";
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;		
			//Se for 2 então é visualização dos compensados
			case 2:
				$texto_situacao = "Compensados";
				$where_situacao = "AND che.status = '$TipoSituacao'";
			break;
			//Se for 3 então é visualização dos que voltaram
			case 3:
				$texto_situacao = "Devolvido";			
				
				//Caso esteja visualizando uma disposicao especifica
				if ($TipoDisposicao > 0)
				{
				
					$where_situacao = "AND che.status = '$TipoSituacao' AND che.disposicao = '$TipoDisposicao'";
				
				}
				
				//Caso a disposicao seja todos
				else
				
				{
				
					$where_situacao = "AND che.status = '$TipoSituacao'";
				
				}
				
			break;
		
		}

		//Efetua o switch da disposicao informada
		switch ($TipoDisposicao) 
		{
  		
			//Se for 1 
			case 1:
				$texto_situacao = "Primeiro Contato";				
			break;		
			//Se for 2
			case 2:
				$texto_situacao = "Em Negociação";
			break;	
			//Se for 3
			case 3:
				$texto_situacao = "Reapresentado";
			break;		
			//Se for 4
			case 4:
				$texto_situacao = "Pago";
			break;
			//Se for 5
			case 5:
				$texto_situacao = "Para Registrar";			
			break;
			//Se for 6 
			case 6:
				$texto_situacao = "No SPC";			
			break;
			//Se for 7 
			case 7:
				$texto_situacao = "Não Pode SPC";			
			break;
			//Se for 8 
			case 8:
				$texto_situacao = "SPC Pago";			
			break;
			//Se for 9 
			case 9:
				$texto_situacao = "Devolvido ao Titular";			
			break;
			//Se for 10
			case 10:
				$texto_situacao = "Cobrança Judicial";			
			break;
			case 11:
				$texto_situacao = "ACC";			
			break;
		
		}		
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$TextoFiltraData = "</br><b>E com data de vencimento entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
			$TextoSQLData = "	 AND che.data_vencimento >= '$dataIni' AND che.data_vencimento <= '$dataFim' ";

		}
		
		//Caso esteja visualizando uma disposicao especifica
		if ($TipoSituacao == 3 AND $TipoDisposicao > 0)
		{
		
			//Monta a descrição a exibir
			$desc_filtragem = "<b>Exibindo Cheques com o status DEVOLVIDO<br/>E com situação: </b>$texto_situacao" . $TextoFiltraData;

		}

		else
		
		{
			
			if ($TipoSituacao > 1 AND $TipoSituacao < 3)
			{
			
				//Monta a descrição a exibir
				$desc_filtragem = "<b>Exibindo Cheques com o status " . $TextoSituacao . $TextoFiltraData;
				
			}
			
					
			$quebra_disposicao = 1;
		
		}
		
		
		//Monta o sql de filtragem
		$sql = "SELECT 
				che.id,
				che.numero_cheque,
				che.pre_datado,
				che.banco_id,
				che.conta_corrente_id,
				che.bom_para,
				che.data_vencimento,
				che.valor,
				che.favorecido,
				che.status,
				che.observacoes,
				che.agencia,
				che.conta,
				che.origem,
				che.disposicao,
				che.evento_id,
				che.formando_id,
				ban.codigo AS banco_codigo,
				ban.nome AS banco_nome
				FROM cheques che
				LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
				WHERE che.empresa_id = '$empresaId' 
				$where_situacao $TextoSQLData
				ORDER BY $disposicao_ordem che.favorecido";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há cheques com a situação $texto_situacao";
	break;
	
	//Se for 2 então é visualização por pre datado
	case 2: 
		//Monta o título da página
		$titulo = "Relação de Cheques Pre-datados"; 
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$TextoFiltraData = "</br><b>E bom para: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
			$TextoSQLData = "	 AND che.bom_para >= '$dataIni' AND che.bom_para <= '$dataFim' ";

		}
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Cheques Pre-datados: </b>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				che.id,
				che.numero_cheque,
				che.pre_datado,
				che.banco_id,
				che.conta_corrente_id,
				che.bom_para,
				che.data_vencimento,
				che.valor,
				che.favorecido,
				che.status,
				che.observacoes,
				che.agencia,
				che.conta,
				che.origem,
				che.disposicao,
				che.evento_id,
				che.formando_id,
				ban.codigo AS banco_codigo,
				ban.nome AS banco_nome
				FROM cheques che
				LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
				WHERE che.empresa_id = '$empresaId' 
				AND che.pre_datado = 1 $TextoSQLData
				ORDER BY che.data_vencimento";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há cheques pre-datados para o periodo especificado";
	break;
	
	//Se for 3 então é visualização por banco
	case 3: 
		//Monta o título da página
		$titulo = "Relação de Cheques por Banco"; 
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$TextoFiltraData = "</br><b>E com data de vencimento entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
			$TextoSQLData = "	 AND che.data_vencimento >= '$dataIni' AND che.data_vencimento <= '$dataFim' ";

		}
		
		$BancoId = $_GET["BancoId"];
		
		//Captura o valor do banco
		$lista_banco = mysql_query("SELECT nome FROM bancos WHERE id = $BancoId");
		//Executa a query
		$dados_banco = mysql_fetch_array($lista_banco); 
		
		$BancoNome = $dados_banco["nome"];
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Cheques do Banco: </b><span style='color: 990000'>$BancoNome</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				che.id,
				che.numero_cheque,
				che.pre_datado,
				che.banco_id,
				che.conta_corrente_id,
				che.bom_para,
				che.data_vencimento,
				che.valor,
				che.favorecido,
				che.status,
				che.observacoes,
				che.agencia,
				che.conta,
				che.origem,
				che.disposicao,
				che.evento_id,
				che.formando_id,
				ban.codigo AS banco_codigo,
				ban.nome AS banco_nome
				FROM cheques che
				LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
				WHERE che.empresa_id = '$empresaId' 
				AND che.banco_id = $BancoId $TextoSQLData
				ORDER BY che.data_vencimento";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há cheques do banco para o periodo especificado";
		
	break;
	
	//Se for 4 então é visualização por evento
	case 4: 
		//Monta o título da página
		$titulo = "Relação de Cheques por Evento"; 
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$TextoFiltraData = "</br><b>E com data de vencimento entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
			$TextoSQLData = "	 AND che.data_vencimento >= '$dataIni' AND che.data_vencimento <= '$dataFim' ";

		}
		
		$EventoId = $_GET["EventoId"];
		
		//Captura o valor do evento
		$lista_evento = mysql_query("SELECT nome FROM eventos WHERE id = $EventoId");
		//Executa a query
		$dados_evento = mysql_fetch_array($lista_evento); 
		
		$EventoNome = $dados_evento["nome"];
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Cheques do Evento: </b><span style='color: 990000'>$EventoNome</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				che.id,
				che.numero_cheque,
				che.pre_datado,
				che.banco_id,
				che.conta_corrente_id,
				che.bom_para,
				che.data_vencimento,
				che.valor,
				che.favorecido,
				che.status,
				che.observacoes,
				che.agencia,
				che.conta,
				che.origem,
				che.disposicao,
				che.evento_id,
				che.formando_id,
				ban.codigo AS banco_codigo,
				ban.nome AS banco_nome
				FROM cheques che
				LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
				WHERE che.empresa_id = '$empresaId' 
				AND che.evento_id = $EventoId $TextoSQLData
				ORDER BY che.data_vencimento";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há cheques do evento para o periodo especificado";
		
	break;
	
	//Se for 5 então é visualização por evento
	case 5: 
		//Monta o título da página
		$titulo = "Relação de Cheques por Evento e Formando"; 
		
		//Verifica se foi informado alguma data para filtrar junto
		if ($dataIni != 0) 
		{
		
			$TextoFiltraData = "</br><b>E com data de vencimento entre: </b>$_GET[DataIni]<b> a </b>$_GET[DataFim]";
			$TextoSQLData = "	 AND che.data_vencimento >= '$dataIni' AND che.data_vencimento <= '$dataFim' ";

		}
		
		$EventoId = $_GET["EventoId"];
		$FormandoId = $_GET["FormandoId"];
		
		//Captura o valor do evento
		$lista_evento = mysql_query("SELECT nome FROM eventos WHERE id = $EventoId");
		//Executa a query
		$dados_evento = mysql_fetch_array($lista_evento); 
		
		$EventoNome = $dados_evento["nome"];
		
		//Captura o valor do formando
		$lista_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = $FormandoId");
		//Executa a query
		$dados_formando = mysql_fetch_array($lista_formando); 
		
		$FormandoNome = $dados_formando["nome"];
		
		//Monta a descrição a exibir
		$desc_filtragem = "<b>Exibindo Cheques do Evento: </b><span style='color: 990000'>$EventoNome</span>
							<br/>
							<b>E do Formando: </b><span style='color: 990000'>$FormandoNome</span>" . $TextoFiltraData;

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
				che.id,
				che.numero_cheque,
				che.pre_datado,
				che.banco_id,
				che.conta_corrente_id,
				che.bom_para,
				che.data_vencimento,
				che.valor,
				che.favorecido,
				che.status,
				che.observacoes,
				che.agencia,
				che.conta,
				che.origem,
				che.disposicao,
				che.evento_id,
				che.formando_id,
				ban.codigo AS banco_codigo,
				ban.nome AS banco_nome
				FROM cheques che
				LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
				WHERE che.empresa_id = '$empresaId' 
				AND che.evento_id = $EventoId 
				AND che.formando_id = $FormandoId $TextoSQLData
				ORDER BY che.data_vencimento";

		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há cheques do evento e do formando para o periodo especificado";
		
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
						<?php echo $desc_filtragem ?>
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
					<td>
						<input name="Button" type="button" class="button" id="Submit" accesskey='N' title="Novo Cheque [Alt+N]" value='Novo Cheque' onclick="window.location='sistema.php?ModuloNome=ChequeCadastra';" />
					</td>
					<td align="right">
						<input class="button" title="Retorna ao Módulo de Cheques" name='btnVoltar' type='button' id='btnVoltar' value='Retornar a Cheques' onclick="wdCarregarFormulario('ModuloCheques.php','conteudo')" />	
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
					
					
					echo "
						<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
							<td width='90' align='center'>Número</td>
							<td width='170'>Banco</td> 
							<td width='65' align='center'>Agência</td>
							<td width='75' align='center'>Conta</td>         
							<td>Titular</td>
							<td width='90' align='right'>Valor</td>
							<td width='100' align='center'>Status</td>         
						</tr>";
						
				}
	  
				//Caso não houverem registros
				if ($tot_regs == 0) 
				{ 

					//Exibe uma linha dizendo que nao há registros
					echo "
						<tr height='24'>
							<td colspan='7' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
								<slot><font color='#33485C'><strong>$texto_vazio</strong></font></slot>
							</td>
						</tr>";	  
	  
				} 
    
				else 
    
				{

					//Cria o array e o percorre para montar a listagem dinamicamente
					while ($dados_rec = mysql_fetch_array($query))
					{

						//Efetua o switch para a descrição do status
						switch ($dados_rec["status"]) 
						{
		
							//Se for em aberto
							case 1: 
								$desc_status = "Recebido";
								$total_recebido = $total_recebido + $dados_rec[valor];								
							break;
							//Se for compensado
							case 2: 
								$desc_status = "Compensado"; 
								$total_compensado = $total_compensado + $dados_rec[valor];
							break;
							//Caso cheque voltar
							case 3: 
								$desc_status = "<span style='color: #990000'>Devolvido</span>";	
								$total_devolvido = $total_devolvido + $dados_rec[valor];
							break;			
						
						}
    
						//Efetua o switch para a origem
						switch ($dados_rec["origem"]) 
						{
		
							//Se for 1 = avulso
							case 1: $desc_origem = "Cheque Avulso";	break;
							//Se for 2 = contas a receber
							case 2: $desc_origem = "Contas a Receber"; break;
      			
						}
						
						//Efetua o switch da disposicao informada
						switch ($dados_rec["disposicao"]) 
						{
						
							//Se for 1 
							case 1:
								$texto_situacao = "Primeiro Contato";				
							break;		
							//Se for 2
							case 2:
								$texto_situacao = "Em Negociação";
							break;	
							//Se for 3
							case 3:
								$texto_situacao = "Reapresentado";
							break;		
							//Se for 4
							case 4:
								$texto_situacao = "Pago";
							break;
							//Se for 5
							case 5:
								$texto_situacao = "Para Registrar";			
							break;
							//Se for 6 
							case 6:
								$texto_situacao = "No SPC";			
							break;
							//Se for 7 
							case 7:
								$texto_situacao = "Não Pode SPC";			
							break;
							//Se for 8 
							case 8:
								$texto_situacao = "SPC Pago";			
							break;
							//Se for 9 
							case 9:
								$texto_situacao = "Dev. ao Titular";			
							break;
							//Se for 10
							case 10:
								$texto_situacao = "Cobr. Judicial";			
							break;
						
						}

					//Fecha o php, mas o while continua
					?>

				<tr height="16" onclick="wdCarregarFormulario('ChequeTerceiroExibe.php?ChequeId=<?php echo $dados_rec[id] ?>','conteudo')" style="cursor: pointer" >
					<td align="center" height="20">
						<font color="#CC3300" size="2" face="Tahoma"><a title="Clique para exibir este cheque" href="#">&nbsp;<?php echo $dados_rec[numero_cheque]; ?></a></font>      
					</td>			
					<td>
						<?php echo "[" . $dados_rec[banco_codigo] . "] - " . $dados_rec[banco_nome] ?>				
					</td>
					<td align="center">
						<?php echo $dados_rec[agencia] ?>				
					</td>
					<td align="center">
						<?php echo $dados_rec[conta] ?>				
					</td>
					<td >
						<?php echo $dados_rec[favorecido] ?>				
					</td>			
					<td valign="middle" bgcolor="#fdfdfd" align="right">
						<?php 
							
							echo "R$ " . number_format($dados_rec[valor], 2, ",", ".");
							$total_geral = $total_geral + $dados_rec[valor]; 
						
						?>
					</td>	
					<td align="center">
						<b><?php echo $desc_status ?></b>
					</td>						
				</tr>
				<tr>
					<td colspan="1" style="border-bottom: #aaa 1px dotted;">&nbsp;</span>
					<td colspan="5" style="border-bottom: #aaa 1px dotted;"><span style="font-family: Trebuchet MS, Lucida Sans Unicode, Arial, sans-serif; color: #6666CC; font-weight: normal">Origem: <?php echo $desc_origem ?></span></td>
					<td align="center" style="border-bottom: #aaa 1px dotted;">
						<span style="">
						<?php
						
							if ($dados_rec["status"] == 3)
							{
							
								echo $texto_situacao;
								
							}
							
							else
							
							{
							
								echo "&nbsp;";
								
							}
							
						?>
						</span>
					</td>
				</tr>
				<?php
					//Fecha o WHILE
					};
	
					//Fecha o if de se tem registros
					}

					//Verifica se precisa imprimir o rodapé
					if ($tot_regs > 0) 
					{ 
				?>

				<tr height="16">
					<td colspan="5" height="20" align="right">Total Recebido:&nbsp;&nbsp;<span style="color: #990000"><b><?php echo "R$ " . number_format($total_recebido, 2, ",", ".") ?></b></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Compensado:&nbsp;&nbsp;<span style="color: #990000"><b><?php echo "R$ " . number_format($total_compensado, 2, ",", ".") ?></b></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Devolvido:&nbsp;&nbsp;<span style="color: #990000"><b><?php echo "R$ " . number_format($total_devolvido, 2, ",", ".") ?></b></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Geral:</b></span></td>
					<td height="20" valign="middle" bgcolor="#fdfdfd" align="right">
						<span style="color: #990000"><b><?php echo "R$ " . number_format($total_geral, 2, ",", ".") ?></b></span>
					</td>
					<td>&nbsp;</td>					
				</tr>	
	
				<?php
				
					//Fecha o IF
					};
				
				?>
		
			</table>	
		</td>
	</tr>  
</table>
