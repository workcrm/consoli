<?php 
###########
## M�dulo para cadastro de servi�os de evento
## Criado: 25/11/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

// Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipula��o de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipula��o de valor monet�rio
include "./include/ManipulaMoney.php";

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

//Monitora o evento escolhido para o bactracking do usu�rio
$sql_backtracking = mysql_query("UPDATE usuarios SET evento_id = '$EventoId' WHERE usuario_id = '$usuarioId'");

echo "<script>wdCarregarFormulario('UltimoEvento.php','ultimo_evento',2)</script>";


//Recupera dos dados do evento
$sql_evento = mysql_query("SELECT 
							eve.id,
							eve.nome,
							eve.descricao,
							eve.status,
							eve.cliente_id,
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
							eve.data_realizacao,
							eve.hora_realizacao,
							eve.duracao,
							eve.servicos_timestamp,
							eve.servicos_operador_id,
							concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
							cli.nome as cliente_nome,
							gru.nome as grupo_nome
							FROM eventos eve 
							INNER JOIN clientes cli ON cli.id = eve.cliente_id
							LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
							LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.servicos_operador_id
							WHERE eve.id = $EventoId");

//Cria o array dos dados
$dados_evento = mysql_fetch_array($sql_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) 
{
	
	case 0: $desc_status = "Em or�amento"; break;
	case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Realizado"; break;
	case 3: $desc_status = "<span style='color: red'>N�o-Realizado</span>"; break;

} 

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Servi�os do Evento</span>
					</td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table id='2' width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
				<tr>
					<td class='text'>

						<?php
					
							//Recupera os valores vindos do formul�rio e armazena nas variaveis
							if($_POST['Submit'])
							{

								$edtTotalChk = $_POST['edtTotalChk'];
								$edtEventoId = $_POST["EventoId"];
	
								//Primeiro apaga todos os servicos que j� existem na base
								$sql_exclui_servico = "DELETE FROM eventos_servico WHERE evento_id = $EventoId";
								
								//Executa a query
								$query_exclui_servico = mysql_query($sql_exclui_servico);
		
								//Define o valor inicial para efetuar o FOR
								for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++)
								{
							
									//Monta a vari�vel com o nome dos campos
									$texto_qtde = "edtQtde" . $contador_for;
									$texto_preco = "edtValor" . $contador_for;
									$texto_obs = "edtObs" . $contador_for;																											
									$valor_preco = MoneyMySQLInserir($_POST[$texto_preco]);
									
									$texto_culto = "chkCulto" . $contador_for;
									$texto_colacao = "chkColacao" . $contador_for;
									$texto_jantar = "chkJantar" . $contador_for;
									$texto_baile = "chkBaile" . $contador_for;
									
									//Enquanto n�o chegar ao final do contador total de itens
									if ($_POST[$contador_for] != 0) 
									{
																			
										$sql_insere_servico = "INSERT INTO eventos_servico (
																	 evento_id, 
																	 servico_id,
																	 quantidade,
																	 valor_venda,
																	 chk_culto,
																	 chk_colacao,
																	 chk_jantar,
																	 chk_baile,																	 
																	 observacoes
																	 ) VALUES (
																	 '$EventoId',
																	 '$_POST[$contador_for]', 
																	 '$_POST[$texto_qtde]',
																	 '$valor_preco',
																	 '$_POST[$texto_culto]',
																	 '$_POST[$texto_colacao]',
																	 '$_POST[$texto_jantar]',
																	 '$_POST[$texto_baile]',
																	 '$_POST[$texto_obs]'
																	 )";																		
										
										//Insere os registros na tabela de eventos_servico
										mysql_query($sql_insere_servico);
									}								
															
								//Fecha o FOR
								}
						
								//Configura a assinatura digital
								$sql = mysql_query("UPDATE eventos SET servicos_timestamp = now(), servicos_operador_id = $usuarioId WHERE id = $EventoId");
								
								//Exibe a mensagem de inclus�o com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Servi�os cadastrados com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
	        	
							}
						?>
          
						<TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
							<TR>
								<TD class='dataLabel' width='15%'> Nome do Evento : </TD>
								<TD colspan='5' class=tabDetailViewDF>
									<span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
								</TD>
							</TR>
							<TR>
								<TD valign="top" class='dataLabel'>Descri&ccedil;&atilde;o:</TD>
								<TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["descricao"] ?>
								</TD>
							</TR>
							<TR>
								<TD valign="top" class='dataLabel'>Status:</TD>
								<TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $desc_status ?>
								</TD>
							</TR>
							<TR>
								<TD valign="top" class='dataLabel'>Data:</TD>
								<TD valign="middle" class=tabDetailViewDF>
									<?php echo DataMySQLRetornar($dados_evento["data_realizacao"]) ?>
								</TD>
								<TD valign="middle" class=dataLabel>Hora:</TD>
								<TD width="19%" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["hora_realizacao"] ?>								
								</TD>
								<TD width="12%" valign="middle" class=dataLabel>Dura&ccedil;&atilde;o:</TD>
								<TD width="20%" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["duracao"] ?>								
								</TD>
							</TR>
							<TR>
								<TD valign="top" class='dataLabel'>Cliente:</TD>
								<TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["cliente_nome"] ?>
								</TD>
							</TR>
							<TR>
								<TD valign="top" class='dataLabel'>Grupo:</TD>
								<TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["grupo_nome"] ?>
								</TD>
							</TR> 
							<TR>
								<TD class='dataLabel'>Respons&aacute;vel:</TD>
								<TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_evento["responsavel"] ?>								
								</TD>
							</TR>
							<TR>
								<TD valign="top" class='dataLabel'>Contatos:</TD>
								<TD colspan="5" valign="middle" class=tabDetailViewDF>
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="300" height='20'>Nome:</td>
											<td width="260" height='20'>Observa��es:</td>
											<td height="20">Telefone:</td>
										</tr>
										<tr valign="middle">
											<td height='20'>
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
											<td height='20'>
												<span style="font-size: 12px"><?php echo $dados_evento[contato2] ?></span>                   
											</td>
											<td height='20'>
												<span style="font-size: 12px"><?php echo $dados_evento[contato_obs2] ?></span>
											</td>
											<td height="20">
												<span style="font-size: 12px"><?php echo $dados_evento[contato_fone2] ?></span>
											</td>
										</tr>
										<tr valign="middle">
											<td height='20'>
												<span style="font-size: 12px"><?php echo $dados_evento[contato3] ?></span>
											</td>
											<td height='20'>
												<span style="font-size: 12px"><?php echo $dados_evento[contato_obs3] ?></span>
											</td>
											<td height="20">
												<span style="font-size: 12px"><?php echo $dados_evento[contato_fone3] ?></span>
											</td>
										</tr>                 
									</table>               							 
								</td>
							</tr>
							<tr>
								<td colspan="6" valign="middle" class="tabDetailViewDF">
									<table width='100%' cellpadding='0' cellspacing='0' border='0' >
										<tr valign='middle'>
											<td colspan="8" style="padding-bottom: 4px">
												<span style="font-size: 11px">Tarefas Adicionais:</span>
											</td>
										</tr>
										<tr valign='middle'>
											<td width='30'>
                    		 <img src='./image/bt_evento_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para exibir o detalhamento deste evento' href='#' onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Detalhamento</a> 
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_data_evento_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar as datas deste evento' href='#' onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Datas</a>
                			</td>
											<td width='30'>
                    		 <img src='./image/bt_participante_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os participantes deste evento' href='#' onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_endereco_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os endere�os deste evento' href='#' onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endere�os</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_item_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os produtos deste evento' href='#' onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a> 
                			</td>                 			
                			<td width='30'>
                    		 <img src='./image/bt_terceiro_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os terceiros deste evento' href='#' onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_brinde_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os brindes deste evento' href='#' onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
                			</td>
                			                			
              			</tr>
              			
										<tr>
              				<td colspan="2">
												&nbsp;
											</td>
											<td width="30">
                    		 <img src="./image/bt_repertorio_gd.gif" /> 
                			</td>
											<td>
                    		 <a title="Clique para gerenciar o repert�rio deste evento" href="#" onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repert�rio</a>
                			</td>
											<td width="30">
                    		 <img src="./image/bt_formando_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os formandos deste evento" href="#" onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                			</td>				 
                			<td width="30">
                    		 <img src="./image/bt_fotovideo_gd.gif" /> 
                			</td>
											<td>
                    		 <a title="Clique para gerenciar o foto e v�deo deste evento" href="#" onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e V�deo</a>
                			</td>
                      <td width="30">
                    		 <img src="./image/bt_documentos_gd.gif" /> 
                			</td>
											<td colspan="4">
                         <a title="Clique para gerenciar os documentos deste evento" href="#" onclick="wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Documentos</a>
                      </td> 									
              			</tr>              			
          				</table>
				  			</td>              
          </table>
          
          <br/>
				<span class="TituloModulo">Assinatura Digital:</span>
				<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
        	<tr>
          <td valign="top" width="120" class="dataLabel">�ltima Altera��o:</td>
          <td class="tabDetailViewDF">
						<?php 
							//Exibe o timestamp do cadastro da conta
							echo TimestampMySQLRetornar($dados_evento[servicos_timestamp]) 
						?>					
					</td>
          <td class="dataLabel">Operador:</td>
          <td class="tabDetailViewDF" width="200">
						<?php echo $dados_evento[operador_nome]	?>					
					</td>
        </tr>                 
  	  </table>
													           
          <br />

          <table cellspacing="0" cellpadding="0" border="0">
	          <tr>
	            <td>
	              <form id="form" name="cadastro" action="sistema.php?ModuloNome=ServicoEventoCadastra" method="post">
				  		</td>
	          </tr>
	          <tr>
		        	<td style="PADDING-BOTTOM: 2px">
		        		<input name="Submit" type="submit" class="button" title="Salva os Servi�os do Evento" value="Salvar Servi�os do Evento" />
	            	<input class="button" title="Limpa o conte�do dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
								<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
	          	</td>
		       </tr>
         </table>

			   <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  			 <tr>
	    			 <td colspan="15" align="right">
	      			 <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        			 <tr>
	          			 <td width="300" align="left" class="listViewPaginationTdS1" style="PADDING-BOTTOM: 2px"><span class="pageNumbers"><b>Selecione os servi�os a incluir no evento</b></span></td>
	          			 <td align="right" class="listViewPaginationTdS1" style="PADDING-BOTTOM: 2px"><span class="pageNumbers" style="color: #990000"><b>A quantidade de cada servi�o � UNIT�RIA de acordo com sua respectiva unidade de medida.&nbsp;</b></span></td>
	        			 </tr>
	      			 </table>
	    			 </td>
	  			 </tr>
	  			 
					 <tr height="20">
      			 <td width="30" class="listViewThS1">
        		   <div align="center">Inc.</div>
      			 </td>
						 <td width="52" class="listViewThS1">
						 	 Qtde
						 </td>
		  			 <td width="310" class="listViewThS1">
						 	 &nbsp;&nbsp;Descri��o do Servi�o
						 </td>
						 <td width="67" class="listViewThS1">
								Pre�o Un.
						 </td>
						 <td width="67" class="listViewThS1">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total
						 </td>						 
						 <td class="listViewThS1">
						   Observa��es
						 </td>
	  			 </tr>

					 <?php
						 //Monta a query para capturar as categorias que existem cadastrados servi�os
						 $sql_categoria = mysql_query("
						 									SELECT serv.id, 
															serv.nome, 
															serv.categoria_id, 
															cat.nome as categoria_nome
															FROM servico_evento serv
															LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
															WHERE serv.ativo = '1' 
															AND serv.empresa_id = $empresaId
															GROUP BY cat.nome
															ORDER BY cat.nome, serv.nome"); 
						 
						 //Cria a variavel zerada para o contador de checkboxes
						 $edtServicoChk = 0; 
						 
						 //Percorre o array
					   while ($dados_categoria = mysql_fetch_array($sql_categoria)){
						 
						 ?>
						   
					 <tr height="24">
    				 <td colspan="7" valign="bottom" style="padding-left: 8px">    				 	 
		  				 <span style="font-size: 14px"><b>
							 <?php 
							   if ($dados_categoria["categoria_id"] == 0) {
							   	 echo "Sem categoria definida";
							   } else {
							 		 echo $dados_categoria["categoria_nome"];
								 }			
							 ?>
							 </b></span>
						 </td>						 
					 </tr>
					 						   
						   <?php
						 
							 //Monta a query de filtragem dos servicos
							 $filtra_servico = "SELECT 
												serv.id,
												serv.nome,
												serv.valor_venda,
												cat.nome as categoria_nome
												FROM servico_evento serv
												LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
												WHERE serv.ativo = '1' 
												AND serv.empresa_id = $empresaId
												AND serv.categoria_id = $dados_categoria[categoria_id]
												ORDER BY cat.nome, serv.nome";
							
							 //Executa a query
							 $lista_servico = mysql_query($filtra_servico);
							 
							 //Cria um contador com o n�mero de contar que a query retornou
							 $nro_servico = mysql_num_rows($lista_servico);
						  
						   //Percorre o array
						   while ($dados_servico = mysql_fetch_array($lista_servico))
						   {
							 	
								 if ($dados_servico["categoria_id"] == 0) {}
								 
								 //Efetua a pesquisa na base de servico do evento para ver se o servico consta como selecionado para o evento
								 $sql_procura_servico = "SELECT
														quantidade,
														valor_venda,
														chk_culto,
														chk_colacao,
														chk_jantar,
														chk_baile,
														observacoes
														FROM eventos_servico
														WHERE evento_id = $EventoId
														AND servico_id = '$dados_servico[id]'";
			
								//Executa a query
								$query_procura_servico = mysql_query($sql_procura_servico);
								
								//Monta um array com o servico de retorno
								$dados_procura_servico = mysql_fetch_array($query_procura_servico);
								
								//Conta se retornou algum registro
								$conta_retorno = mysql_num_rows($query_procura_servico);
								
								//Caso encontrou o servi�o para ser incluso no or�amento
								if ($conta_retorno == 1) 
								{
									//Seta para marcar o checkbox
									$chkServico = "checked";
									
									$QtdeRead = '';
									$QtdeCor = '#FFFFFF';
									
									if ($dados_procura_servico[chk_culto] == 1) 
									{
										$marca_culto = 'checked';
									}
									
									else
									
									{
									
										$marca_culto = '';
										
									}
									
									if ($dados_procura_servico[chk_colacao] == 1) 
									{
									
										$marca_colacao = 'checked';
										
									}
									
									else
									
									{
									
										$marca_colacao = '';
										
									}
									
									if ($dados_procura_servico[chk_jantar] == 1) 
									{
										
										$marca_jantar = 'checked';
										
									}
									
									else
									
									{
									
										$marca_jantar = '';
										
									}
									
									if ($dados_procura_servico[chk_baile] == 1) 
									{
									
										$marca_baile = 'checked';
										
									}
									
									else
									
									{
									
										$marca_baile = '';
										
									}
									
								} 
								
								else 
								
								{
									
									//Seta para o chekbox n�o ser marcado
									$chkServico = "";
									
									$QtdeRead = "disabled='disabled'";
									$QtdeCor = '#E6E6E6';
									
									$marca_culto = '';
									$marca_colacao = '';
									$marca_jantar = '';
									$marca_baile = '';
								
								}							 
							 							 				
							?>

							<tr height="16">
								<td valign="top" style="border-bottom: 1px dotted;">
									<div align="center">
										<input name="<?php echo ++$edtServicoChk ?>" type="checkbox" value="<?php echo $dados_servico[id] ?>" style="border: 0px" title="Clique para marcar ou desmarcar a inclus�o deste servi�o no evento" onclick="if (this.checked == true){document.cadastro.edtQtde<?php echo $edtServicoChk ?>.disabled = false; document.cadastro.edtQtde<?php echo $edtServicoChk ?>.style.backgroundColor = '#FFFFFF'; document.cadastro.chkCulto<?php echo $edtServicoChk ?>.checked = true; document.cadastro.chkColacao<?php echo $edtServicoChk ?>.checked = true; document.cadastro.chkJantar<?php echo $edtServicoChk ?>.checked = true; document.cadastro.chkBaile<?php echo $edtServicoChk ?>.checked = true;} else {document.cadastro.edtQtde<?php echo $edtServicoChk ?>.disabled = true; document.cadastro.edtQtde<?php echo $edtServicoChk ?>.style.backgroundColor = '#E6E6E6'; document.cadastro.chkCulto<?php echo $edtServicoChk ?>.checked = false; document.cadastro.chkColacao<?php echo $edtServicoChk ?>.checked = false; document.cadastro.chkJantar<?php echo $edtServicoChk ?>.checked = false; document.cadastro.chkBaile<?php echo $edtServicoChk ?>.checked = false;}" <?php echo $chkServico ?>/>
									</div>
								</td>
								<td valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 2px; border-bottom: 1px dotted;">
									<input name="edtQtde<?php echo $edtServicoChk ?>" <?php echo $QtdeRead ?> type="text" class="datafield" style="background-color: <?php echo $QtdeCor ?>; width: 35px" maxlength="10" title="Informe a quantidade do servi�o" value="<?php echo $dados_procura_servico[quantidade] ?>" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
								</td>
								<td valign="top" bgcolor="#fdfdfd" class="oddListRowS1" style="border-bottom: 1px dotted;">
									<span style="color: #666; padding-bottom: 4px;"><b><?php echo $dados_servico[nome] ?></b></span><br/>
									<input name="chkCulto<?php echo $edtServicoChk ?>" id="chkCulto<?php echo $edtServicoChk ?>" type="checkbox" value="1" style="border: 0px" <?php echo $marca_culto  ?> ><span style="font-size: 11px">Culto</span>&nbsp;&nbsp;&nbsp;&nbsp;<input name="chkColacao<?php echo $edtServicoChk ?>" id="chkColacao<?php echo $edtServicoChk ?>" type="checkbox"  value="1" style="border: 0px" <?php echo $marca_colacao  ?> ><span style="font-size: 11px">Cola��o</span>&nbsp;&nbsp;&nbsp;&nbsp;<input name="chkJantar<?php echo $edtServicoChk ?>" id="chkJantar<?php echo $edtServicoChk ?>" type="checkbox" value="1" style="border: 0px" <?php echo $marca_jantar  ?> ><span style="font-size: 11px">Jantar<span>&nbsp;&nbsp;&nbsp;&nbsp;<input name="chkBaile<?php echo $edtServicoChk ?>" id="chkBaile<?php echo $edtServicoChk ?>" type="checkbox" value="1" style="border: 0px" <?php echo $marca_baile ?> ><span style="font-size: 11px">Baile</span>
								</td>
								<td valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 2px; border-bottom: 1px dotted;">
									<?php							
									
										//Verifica se j� existe um pre�o de venda cadastrado para o servi�o
										if ($dados_procura_servico[valor_venda] > 0) 
										{
										
											//Caso tenha valor de venda cadastrado mostra o valor do servico para este evento
											$preco_venda = str_replace(".",",",$dados_procura_servico[valor_venda]);
										
										} 
										
										else 
										
										{
										
											//Caso n�o, pega o valor de venda padr�o do servi�o no cadastro normal
											$preco_venda = str_replace(".",",",$dados_servico[valor_venda]);
										
										}
									
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtValor$edtServicoChk";
										//Define o tamanho do componente
										$objWDComponente->intSize = 8;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "$preco_venda";
										//Busca a descri��o do XML para o componente
										$objWDComponente->strLabel = "";
										//Determina um ou mais eventos para o componente
										$objWDComponente->strEvento = "";
										//Define numero de caracteres no componente
										$objWDComponente->intMaxLength = 12;
										
										//Cria o componente edit
										$objWDComponente->Criar();

									?>							 
								</td>
								<td valign="top" bgcolor="#fdfdfd" class="currentTabList">
									<input name="edtTotal<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" value="<?php echo number_format($dados_procura_servico[valor_venda] * $dados_procura_servico[quantidade], 2, ',', '.') ?>" />
								</td>
								<td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 0px">
									<textarea name="edtObs<?php echo $edtServicoChk ?>" wrap="virtual" class="datafield" id="edtObs<?php echo $edtServicoChk ?>" style="width: 320px; height: 40px; font-size: 11px"><?php echo $dados_procura_servico[observacoes] ?></textarea>
								</td>						 
							</tr>

							<?php
						
						//Fecha o while
						} 
					
					//Fecha o while da categoria
					}
					   	
				//Envia com o formulario o total final do contador para efetuar o for depois		 	
				?>	
				</table>
				<input name="edtTotalChk" type="hidden" value="<?php echo $edtServicoChk ?>" />
				<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />		
			</table>           
		</td>
	</tr>
</table>  	 
</form>

</tr>
</table>
