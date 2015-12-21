<?php 
###########
## Módulo para cadastro de brindes de evento
## Criado: 01/10/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
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

//Recupera o id do evento
if($_POST) {
  $EventoId = $_POST["EventoId"]; 
} else {
  $EventoId = $_GET["EventoId"]; 
}

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA){
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $HORA = "00:00:00";
  $data_array = split("[- ]",$DATA);
  if ($DATA <> ""){
    $ANO = $data_array[0];
    $MES = $data_array[1];
    $DIA = $data_array[2];
		$HORA = $data_array[3];
    return $DIA."/".$MES."/".$ANO. " - " . $HORA;
  }else {
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
													eve.brindes_timestamp,
													eve.brindes_operador_id,
													concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
													cli.nome as cliente_nome,
													gru.nome as grupo_nome
													FROM eventos eve 
													INNER JOIN clientes cli ON cli.id = eve.cliente_id
													LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
													LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.brindes_operador_id
													WHERE eve.id = '$EventoId'");

//Cria o array dos dados
$dados_evento = mysql_fetch_array($sql_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) {
  case 0: $desc_status = "Em orçamento"; break;
  case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Realizado"; break;
  case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;
} 

?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}
</script>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Brindes do Evento</span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
			</table>

      <table id='2' width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td class='text'>

          <?php
					//Recupera os valores vindos do formulário e armazena nas variaveis
          if($_POST['Submit']){

						$edtTotalChk = $_POST['edtTotalChk'];
	          $edtEventoId = $_POST["EventoId"];
	
					  //Primeiro apaga todos os itens que já existem na base de brindes do evento
					  $sql_exclui_brinde = "DELETE FROM eventos_brinde WHERE evento_id = '$EventoId'";
	
					  //Executa a query
					  $query_exclui_brinde = mysql_query($sql_exclui_brinde);
	
	
						//Define o valor inicial para efetuar o FOR
						for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++){
	   				
							//Monta a variável com o nome dos campos
							$texto_qtde = "edtQtde" . $contador_for;
							$texto_obs = "edtObs" . $contador_for;																											
							
							//Enquanto não chegar ao final do contador total de itens
							if ($_POST[$contador_for] != 0) {
																	
										$sql_insere_brinde = "INSERT INTO eventos_brinde(
													 						 evento_id, 
																			 brinde_id,
																			 quantidade,
																			 observacoes
																			 ) VALUES (
																			 '$EventoId',
																			 '$_POST[$contador_for]', 
																			 '$_POST[$texto_qtde]',
																			 '$_POST[$texto_obs]'
																			 )";																		
										//Insere os registros na tabela de eventos_brinde
		   							mysql_query($sql_insere_brinde);
								}								
															
						//Fecha o FOR
	  				}
						
						//Configura a assinatura digital
    	    	$sql = mysql_query("UPDATE eventos SET brindes_timestamp = now(), brindes_operador_id = $usuarioId WHERE id = $EventoId");
								
							//Exibe a mensagem de inclusão com sucesso
	        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Brindes Cadastrados com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
	        	
	        	
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
	                   <td width="300" height='20'>
	                     Nome:
	                   </td>
	                   <td width="260" height='20'>
	                     Observações:
	                   </td>
	                   <td height="20">
	                     Telefone:
	                   </td>
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
	             </TD>
	           </TR>
							<TR>
                <TD colspan="6" valign="middle" class=tabDetailViewDF>
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
                    		 <a title='Clique para gerenciar os participantes deste evento' href='#' onClick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_endereco_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os endereços deste evento' href='#' onClick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_item_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os produtos deste evento' href='#' onClick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a> 
                			</td>
                			<td width='30'>
                    		 <img src='./image/bt_servico_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os serviços deste evento' href='#' onClick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a> 
                			</td>                 			
                			<td width='30'>
                    		 <img src='./image/bt_terceiro_gd.gif'/> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os terceiros deste evento' href='#' onClick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
                			</td>
                			               			
              			</tr>
              			
										<tr>
              				<td colspan="2">
												&nbsp;
											</td>
											<td width='30'>
                    		 <img src='./image/bt_repertorio_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o repertório deste evento' href='#' onClick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
                			</td> 
											<td width='30'>
                    		 <img src='./image/bt_formando_gd.gif' /> 
                			</td>
											<td width='85'>
                    		 <a title='Clique para gerenciar os formandos deste evento' href='#' onClick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                			</td>
											<?php 
											
											//Verifica o nível de acesso do usuário
											if ($nivelAcesso >= 4) {
											
											?>	
																 
                			<td width='30'>
                    		 <img src='./image/bt_fotovideo_gd.gif' /> 
                			</td>
											<td>
                    		 <a title='Clique para gerenciar o foto e vídeo deste evento' href='#' onClick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
                			</td>
                			<?php
                			
                			}else{
                				
                			?>
                			
											<td width='30'>
                    		<img src='./image/bt_fotovideo_gd_off.gif' title='Opção não habilitada para seu nível de acesso !'/>  
                			</td>
											<td>
                    		 &nbsp;
                			</td>
                			
                			<?php
                			
                			}
                			
                			?>
                      <td width="30">
                    		 <img src="./image/bt_documentos_gd.gif" /> 
                			</td>
											<td colspan="4">
                         <a title="Clique para gerenciar os documentos deste evento" href="#" onclick="wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Documentos</a>
                      </td> 										
              			</tr>              			
          				</table>
				  			</TD>              
          	</TABLE>
								
						<br/>
						<span class="TituloModulo">Assinatura Digital:</span>
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
		        	<tr>
		          <td valign="top" width="120" class="dataLabel">Última Alteração:</td>
		          <td class="tabDetailViewDF">
								<?php 
									//Exibe o timestamp do cadastro da conta
									echo TimestampMySQLRetornar($dados_evento[brindes_timestamp]) 
								?>					
							</td>
		          <td class="dataLabel">Operador:</td>
		          <td class="tabDetailViewDF" width="200">
								<?php echo $dados_evento[operador_nome]	?>					
							</td>
		        </tr>                 
		  	  </table>								
													           
          </br>

          <TABLE cellSpacing='0' cellPadding='0' border='0'>
	          <tr>
	            <td>
	              <FORM id='form' name='cadastro' action='sistema.php?ModuloNome=BrindeEventoCadastra' method='post'>
				  		</td>
	          </TR>
	          <tr>
		        	<TD style="PADDING-BOTTOM: 2px">
		        		<INPUT name='Submit' type='submit' class=button title="Salva os Brindes do Evento" value='Salvar Brindes do Evento'>		        		
	            	<INPUT class=button title="Limpa o conteúdo dos campos digitados [Alt+L]" accessKey='L' name='Reset' type='reset' id='Reset' value='Limpar Campos'>
								<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
	          	</TD>
	          <TD width="36" align=right>	  </TD>
		       </TR>
         </TABLE>

			   <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  			 <tr>
	    			 <td COLSPAN="15" align="right">
	      			 <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        			 <tr>
	          			 <td colspan="2" align="left" class="listViewPaginationTdS1" style="PADDING-BOTTOM: 2px"><span class='pageNumbers'><strong>Selecione os brindes a incluir no evento</strong></span></td>
	        			 </tr>
	      			 </table>
	    			 </td>
	  			 </tr>
	  			 
					 <tr height="20">
      			 <td width='30' class="listViewThS1">
        		   <div align="center">Inc.</div>
      			 </td>
						 <td width="52" class="listViewThS1">
						 	 Qtde
						 </td>
		  			 <td width="340" class="listViewThS1">
						 	 &nbsp;&nbsp;Descrição do Brinde
						 </td>
						 </td>						 
						 <td class="listViewThS1">
						   Observações
						 </td>
	  			 </tr>					 						   
						   
						 <?php
						 
							 //Monta a query de filtragem dos itens
							 $filtra_item = "SELECT * from brindes WHERE ativo = '1' AND empresa_id = $empresaId ORDER BY nome";
							
							 //Executa a query
							 $lista_item = mysql_query($filtra_item);
							 
							 //Cria um contador com o número de contar que a query retornou
							 $nro_item = mysql_num_rows($lista_item);						   
						  
						   //Percorre o array
						   while ($dados_item = mysql_fetch_array($lista_item)){
							 	
								 //Efetua a pesquisa na base de itens do evento para ver se o item consta como selecionado para o evento
								 $sql_procura_item = "SELECT
																		 quantidade,
																		 observacoes
																		 FROM eventos_brinde
																		 WHERE evento_id = '$EventoId'
																		 AND brinde_id = '$dados_item[id]'";
			
								//Executa a query
								$query_procura_item = mysql_query($sql_procura_item);
								
								//Monta um array com o item de retorno
								$dados_procura_item = mysql_fetch_array($query_procura_item);
								
								//Conta se retornou algum registro
								$conta_retorno = mysql_num_rows($query_procura_item);
								
								//Caso encontrou o item para ser incluso no orçamento
								if ($conta_retorno == 1) {
									//Seta para marcar o checkbox
									$chkItem = "checked";
								} else {
									//Seta para o chekbox não ser marcado
									$chkItem = "";
								}							 
							 							 				
					 ?>

					 <tr height='16'>
    				 <td valign="top">
		  				 <div align="center">
      				 <input name="<?php echo ++$edtItemChk ?>" type="checkbox" value="<?php echo $dados_item[id] ?>" style="border: 0px" title="Clique para marcar ou desmarcar a inclusão deste brinde no evento" <?php echo $chkItem ?>/>
      				 </div>
    				 </td>
    				 <td valign='top' bgcolor='#fdfdfd' class='currentTabList'>
							 <input name="edtQtde<?php echo $edtItemChk ?>" type="text" class='datafield' style="width: 46px" maxlength="10" title="Informe a quantidade do brinde" value="<?php echo $dados_procura_item[quantidade] ?>" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
						 </td>
						 <td valign='top' bgcolor='#fdfdfd' class='oddListRowS1'>
      				 <a title="Clique para editar este brinde" href="#" onClick="wdCarregarFormulario('BrindeAltera.php?Id=<?php echo $dados_item[id] ?>&headers=1','conteudo')"><?php echo $dados_item[nome] ?></a>
    				 </td>
    				 <td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style="padding-right: 0px">
							 <textarea name="edtObs<?php echo $edtItemChk ?>" wrap="virtual" class="datafield" id="edtObs<?php echo $edtItemChk ?>" style="width: 360px; height: 40px; font-size: 11px"><?php echo $dados_procura_item[observacoes] ?></textarea>
						 </td>						 
					 </tr>

						 <?php
						 	//Fecha o while
						 	} 

					   	//Envia com o formulario o total final do contador para efetuar o for depois
						 	?>	
						 <input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>"></input>
						 <input name="EventoId" type="hidden" value="<?php echo $EventoId ?>"></input>		
					 </table>           

     </td>
   </tr>
</FORM>
</table>  	 

</tr>
</table>
