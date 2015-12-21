<?php 
###########
## Módulo para cadastro de foto e vídeo de evento
## Criado: 17/10/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//http://localhost/consoli/sistema.php?ModuloNome=EnderecoEventoCadastra&EventoId=1

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
													eve.foto_video_timestamp,
													eve.foto_video_operador_id,
													concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
													eve.desconto_comissao,
													eve.comissao_avista,
													eve.comissao_30,
													eve.comissao_60,
													eve.vendedor_id,
													cli.nome as cliente_nome,
													gru.nome as grupo_nome
													FROM eventos eve 
													INNER JOIN clientes cli ON cli.id = eve.cliente_id
													LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
													LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.foto_video_operador_id
													WHERE eve.id = $EventoId");

//Cria o array dos dados
$dados_evento = mysql_fetch_array($sql_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) {
  case 0: $desc_status = "Em orçamento"; break;
  case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Realizado"; break;
  case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;
} 

//Monta o lookup da tabela de colaboradores (para a pessoa_id)
//Monta o SQL
$lista_colaborador = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";
//Executa a query
$dados_colaborador = mysql_query($lista_colaborador);
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

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Gerenciamento de Foto e Vídeo do Evento</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
			</table>

      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="text">

          <?php
					//Recupera os valores vindos do formulário e armazena nas variaveis
          if($_POST["Submit"]){

						$edtTotalChk = $_POST["edtTotalChk"];
	          $edtEventoId = $_POST["EventoId"];      
	          
	          $edtTipoAcao = $_POST["TipoAcao"];	          	          
	          
						$edtDescontoComissao = MoneyMySQLInserir($_POST["edtDescontoComissao"]);
	          $edtComissaoAVista = MoneyMySQLInserir($_POST["edtComissaoAVista"]);
	          $edtComissao30 = MoneyMySQLInserir($_POST["edtComissao30"]);
	          $edtComissao60 = MoneyMySQLInserir($_POST["edtComissao60"]);
	          $cmbColaboradorId = $_POST["cmbColaboradorId"];
	
					  //Primeiro apaga todos os itens que já existem na base de itens do evento
					  $sql_exclui_item = "DELETE FROM eventos_fotovideo WHERE evento_id = '$EventoId'";
	
					  //Executa a query
					  $query_exclui_item = mysql_query($sql_exclui_item);
	
	
						//Define o valor inicial para efetuar o FOR
						for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++){
	   				
							//Monta a variável com o nome dos campos
							$texto_formando = "edtFormando" . $contador_for;
							$obs_formando = "edtObsFormando" . $_POST["$texto_formando"];
							$texto_qtde_disp = "edtQtdeDisp" . $contador_for;
							$texto_preco = "edtValor" . $contador_for;
							$texto_qtde_venda = "edtQtdeVenda" . $contador_for;
							$texto_desconto = "edtDesconto" . $contador_for;
							$texto_comissao = "edtComissao" . $contador_for;
							$bonus_comissao = "edtBonus" . $contador_for;		
							$texto_qtde_brinde = "edtQtdeBrinde" . $contador_for;
							$valor_preco = MoneyMySQLInserir($_POST[$texto_preco]);
							$valor_desconto = MoneyMySQLInserir($_POST[$texto_desconto]);
							$valor_bonus = MoneyMySQLInserir($_POST[$bonus_comissao]);
							
							$data_congela = date("Y-m-d",mktime());
							$hora_congela = date("H:i",mktime());
							
							//Enquanto não chegar ao final do contador total de itens
							if ($_POST[$contador_for] != 0) {
																	
										$sql_insere_item = "INSERT INTO eventos_fotovideo (
													 						 evento_id, 
																			 formando_id,
																			 item_id,
																			 quantidade_disponivel,
																			 valor_venda,
																			 comissao,
																			 valor_desconto,
																			 bonus_comissao, 
																			 quantidade_venda,
																			 quantidade_brinde
																			 ) VALUES (
																			 '$EventoId',
																			 '$_POST[$texto_formando]',
																			 '$_POST[$contador_for]', 
																			 '$_POST[$texto_qtde_disp]',
																			 '$valor_preco',
																			 '$_POST[$texto_comissao]',
																			 '$valor_desconto',
																			 '$valor_bonus',
																			 '$_POST[$texto_qtde_venda]',
																			 '$_POST[$texto_qtde_brinde]'
																			 )";																		
										
										//Insere os registros na tabela de eventos_itens
		   							mysql_query($sql_insere_item);
										 
										//Verifica se a ação foi congelar, daí gera o congelamento
										if ($edtTipoAcao == 2){
											
											$sql_insere_congela = "INSERT INTO eventos_fotovideo_congela (
																 						 evento_id,
																						 data_congela,
																						 hora_congela,
																						 operador_congela_id, 
																						 formando_id,
																						 item_id,
																						 quantidade_disponivel,
																						 valor_venda,
																						 comissao,
																						 valor_desconto,
																						 bonus_comissao, 
																						 quantidade_venda,
																						 quantidade_brinde
																						 ) VALUES (
																						 $EventoId,
																						 '$data_congela',
																						 '$hora_congela',
																						 $usuarioId,
																						 '$_POST[$texto_formando]',
																						 '$_POST[$contador_for]', 
																						 '$_POST[$texto_qtde_disp]',
																						 '$valor_preco',
																						 '$_POST[$texto_comissao]',
																						 '$valor_desconto',
																						 '$valor_bonus',
																						 '$_POST[$texto_qtde_venda]',
																						 '$_POST[$texto_qtde_brinde]'
																						 )";																		
										
										//Insere os registros na tabela de eventos_itens
		   							mysql_query($sql_insere_congela);
											
										}
		   							
								}
								
								//Insere a observação do formando
   							$sql_insere_obs = "UPDATE eventos_formando SET obs_compra = '$_POST[$obs_formando]' WHERE id = '$_POST[$texto_formando]'";		   							
   							
   							//Insere os registros na tabela
   							mysql_query($sql_insere_obs);								
															
						//Fecha o FOR
	  				}
						
						
						
						//Altera os valores das comissões no evento
						$sql_insere_comissao = "UPDATE eventos SET 
																		desconto_comissao = '$edtDescontoComissao',
																		comissao_avista = '$edtComissaoAVista',
																		comissao_30 = '$edtComissao30',
																		comissao_60 = '$edtComissao60',
																		vendedor_id = '$cmbColaboradorId'
						 												WHERE id = $edtEventoId";
						
						mysql_query($sql_insere_comissao);
						
						//Verifica se a ação foi congelar, daí gera o congelamento
						if ($edtTipoAcao == 2){
							
							//Altera os valores das comissões no evento
							$sql_insere_comissao_congela = "INSERT INTO eventos_fotovideo_congela_fechamento ( 
																							evento_id,
																							data_congela,
																							hora_congela,
																							desconto_comissao,
																							comissao_avista,
																							comissao_30,
																							comissao_60,
																							vendedor_id
																							) VALUES (
																							'$EventoId',
																						 	'$data_congela',
																						 	'$hora_congela',
																							'$edtDescontoComissao',
																							'$edtComissaoAVista',
																							'$edtComissao30',
																							'$edtComissao60',
																							'$cmbColaboradorId'
																							)";
							
							mysql_query($sql_insere_comissao_congela);
							
						}	
						
						//Recupera dos dados do evento novamente para processar o lançamento dos valores das comissões
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
																			eve.desconto_comissao,
																			eve.comissao_avista,
																			eve.comissao_30,
																			eve.comissao_60,
																			eve.vendedor_id,
																			eve.foto_video_timestamp,
																			eve.foto_video_operador_id,
																			concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
																			cli.nome as cliente_nome,
																			gru.nome as grupo_nome
																			FROM eventos eve 
																			INNER JOIN clientes cli ON cli.id = eve.cliente_id
																			LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
																			LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.foto_video_operador_id
																			WHERE eve.id = $edtEventoId");
						
						//Cria o array dos dados
						$dados_evento = mysql_fetch_array($sql_evento);
						
						//Configura a assinatura digital
    	    	$sql = mysql_query("UPDATE eventos SET foto_video_timestamp = now(), foto_video_operador_id = $usuarioId WHERE id = $edtEventoId");
    	    
						
						if($_POST["Submit"]){
							
							//Verifica se a ação foi congelar, daí gera o congelamento
							if ($edtTipoAcao == 2){
							
								//Exibe a mensagem de inclusão com sucesso
		        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Foto e Vídeo Cadastrado com sucesso e posição atual foi congelada !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
		        		
      				} else {
      					
      					//Exibe a mensagem de inclusão com sucesso
		        		echo "<div id='98'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Foto e Vídeo do Evento Cadastrados com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
      					
     					}
	        	
	        	}
        
				}
        ?>
          <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="dataLabel" width="15%"> Nome do Evento : </td>
                <td colspan="5" class="tabDetailViewDF">
									<span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
				  			</td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["descricao"] ?>
				  			</td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Status:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
								  <?php echo $desc_status ?>
				  			</td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Data:</td>
                <td valign="middle" class="tabDetailViewDF">
									<?php echo DataMySQLRetornar($dados_evento["data_realizacao"]) ?>
				  			</td>
                <td valign="middle" class="dataLabel">Hora:</td>
                <td width="19%" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["hora_realizacao"] ?>								
				  			</td>
                <td width="12%" valign="middle" class="dataLabel">Dura&ccedil;&atilde;o:</td>
                <td width="20%" valign="middle" class="tabDetailViewDF">
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
									<?php echo $dados_evento["grupo_nome"] ?>
				  			</td>
              </tr> 
              <tr>
                <td class="dataLabel">Respons&aacute;vel:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
									<?php echo $dados_evento["responsavel"] ?>								
				  			</td>
              </tr>
	           <tr>
	             <td valign="top" class="dataLabel">Contatos:</td>
	             <td colspan="5" valign="middle" class="tabDetailViewDF">
	               <table width="100%" cellpadding="0" cellspacing="0">
								   <tr valign="middle">
	                   <td width="300" height="20">
	                     Nome:
	                   </td>
	                   <td width="260" height="20">
	                     Observações:
	                   </td>
	                   <td height="20">
	                     Telefone:
	                   </td>
	                 </tr>
	                 <tr valign="middle">
	                   <td height="20">
	                     <span style="font-size: 12px"><?php echo $dados_evento[contato1] ?></span>
	                   </td>
	                   <td height="20">
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
							<tr>
                <td colspan="6" valign="middle" class="tabDetailViewDF">
									<table width="100%" cellpadding="0" cellspacing="0" border="0" >
              			<tr valign="middle">
											<td colspan="8" style="padding-bottom: 4px">
                    		 <span style="font-size: 11px">Tarefas Adicionais:</span>
                			</td>
                		</tr>
              			<tr valign="middle">
											<td width="30">
                    		 <img src="./image/bt_evento_gd.gif"/> 
                			</td>
											<td width="85">
                    		 <a title="Clique para exibir o detalhamento deste evento" href="#" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Detalhamento</a> 
                			</td>
											<td width="30">
                    		 <img src="./image/bt_data_evento_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar as datas deste evento" href="#" onclick="wdCarregarFormulario('DataEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Datas</a>
                			</td>
											<td width="30">
                    		 <img src="./image/bt_participante_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os participantes deste evento" href="#" onclick="wdCarregarFormulario('ParticipanteEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Participantes</a>
                			</td>
                			<td width="30">
                    		 <img src="./image/bt_endereco_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os endereços deste evento" href="#" onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
                			</td>
                			<td width="30">
                    		 <img src="./image/bt_item_gd.gif"/> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os produtos deste evento" href="#" onclick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Produtos</a> 
                			</td>                			
											<td width="30">
                    		 <img src="./image/bt_servico_gd.gif"/> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os serviços deste evento" href="#" onclick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a> 
                			</td>                 			
                			<td width="30">
                    		 <img src="./image/bt_terceiro_gd.gif"/> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os terceiros deste evento" href="#" onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
                			</td>
                			               			
              			</tr>

										<tr>
              				<td colspan="2">
												&nbsp;
											</td>
											<td width="30">
                    		 <img src="./image/bt_brinde_gd.gif"/> 
                			</td>
											<td>
                    		 <a title="Clique para gerenciar os brindes deste evento" href="#" onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
                			</td>
                			<td width="30">
                    		 <img src="./image/bt_repertorio_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar o repertório deste evento" href="#" onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
                			</td>
											<td width="30">
                    		 <img src="./image/bt_formando_gd.gif" /> 
                			</td>
											<td width="85">
                    		 <a title="Clique para gerenciar os formandos deste evento" href="#" onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                			</td>											
              			</tr>              			
          				</table>
				  			</td>              
          </table>
				<br/>
				<span class="TituloModulo">Assinatura Digital:</span>
				<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
        	<tr>
          <td valign="top" width="120" class="dataLabel">Última Alteração:</td>
          <td class="tabDetailViewDF">
						<?php 
							//Exibe o timestamp do cadastro da conta
							echo TimestampMySQLRetornar($dados_evento[foto_video_timestamp]) 
						?>					
					</td>
          <td class="dataLabel">Operador:</td>
          <td class="tabDetailViewDF" width="200">
						<?php echo $dados_evento[operador_nome]	?>					
					</td>
        </tr>                 
  	  </table>
          
<?php 
//verifica os congelamentos deste evento
$sql_consulta_congela = mysql_query("SELECT 
																		cong.data_congela,
																		cong.hora_congela,
																		concat(usu.nome, ' ',usu.sobrenome) as operador_congela_nome
																		FROM eventos_fotovideo_congela cong
																		LEFT OUTER JOIN usuarios usu ON usu.usuario_id = cong.operador_congela_id
																		WHERE cong.evento_id = $EventoId
																		GROUP BY cong.data_congela, cong.hora_congela
																		ORDER by cong.data_congela DESC, cong.hora_congela DESC
																		");

$registros_congela = mysql_num_rows($sql_consulta_congela); 

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>  
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Congelamentos de foto e vídeo para deste Evento:</span></td>
			  </tr>
			</table>
  	</td>
  </tr>
  <tr>
    <td>
      <table id="4" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">

  		<?php
      //Caso houverem registros
    	if ($registros_congela > 0) { 

      	//Exibe o cabeçalho da tabela
				echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
					<td width='75%'>&nbsp;Data do Congelamento</td>
 		      <td width='25%'>Operador</td>
        </tr>
    	";}
    	
		  //Caso não houverem registros
		  if ($registros_congela == 0) { 
	
		  //Exibe uma linha dizendo que nao registros
		  echo "
		  <tr height='24'>
	      <td colspan='2' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
			  	<font color='#33485C'><strong>Não há congelamentos para o foto e vídeo deste evento</strong></font>
				</td>
		  </tr>	
		  ";	  
		  }     	
		
		//Cria o array e o percorre para montar a listagem dinamicamente
    while ($dados_consulta_congela = mysql_fetch_array($sql_consulta_congela)){
    				    
?>
      <tr height="20" valign="middle">
				<td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="padding-bottom: 1px">
          &nbsp;<font color="#CC3300" size="2" face="Tahoma"><a title="Clique para exibir os detalhes deste congelamento de foto e vídeo do evento" href="#" onclick="wdCarregarFormulario('FotoVideoEventoCongela.php?EventoId=<?php echo $EventoId ?>&DataCongela=<?php echo $dados_consulta_congela['data_congela'] ?>&HoraCongela=<?php echo $dados_consulta_congela['hora_congela'] ?>&headers=1','conteudo')"><?php echo DataMySQLRetornar($dados_consulta_congela['data_congela']) . " - " . $dados_consulta_congela['hora_congela'] ; ?></a></font>        
				</td>
        <td>
          <?php echo $dados_consulta_congela["operador_congela_nome"] ?>
				</td>
  	  </tr>

		  <?php		  		    
		  
		  //Fecha o WHILE
		  }
		  ?>
		</table>
	</td>
	</tr>
</table> 
        
				<br/>  

					<?php
						 //Monta a query para capturar os formandos
						 $sql_formandos = mysql_query("SELECT * FROM eventos_formando WHERE evento_id = $EventoId ORDER BY nome"); 
							
						 $registros_formandos = mysql_num_rows($sql_formandos);
						 
						 if ($registros_formandos == 0){
						
						 ?>
						 
						 <table id="4" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">
							  <tr height="24">
						      <td colspan="6" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
								  	<span style="color: #990000"><b>Atenção:</span></b><br/>
										<span style="color: #33485C"><strong>Para gerenciar os itens de foto e vídeo do evento, deve-se primeiro cadastrar os formandos do evento</strong></span>
									</td>
							  </tr>	
						 </table>
						
						<?php
						 
						 //Fecha o if de se não tiver formandos cadastrados
						 } else { 
					?>
					
          <table cellspacing="0" cellpadding="0" border="0">
	          <tr>
	            <td>
	              <form id="form" name="cadastro" action="sistema.php?ModuloNome=FotoVideoEventoCadastra" method="post">
				  		</td>
	          </tr>
	          <tr>
		        	<td style="PADDING-BOTTOM: 2px">
		        		<input name="TipoAcao" type="hidden" value="1" />
								<input name="Submit" type="submit" class="button" title="Salva os itens de Foto e Vídeo do Evento" value="Salvar Foto e Vídeo do Evento" />
		        		<input name="Submit" type="submit" class="button" title="Congela a posição do cadastro do foto e vídeo para a data de hoje" value="Salvar e Congelar Posição Atual" style="width: 200px" onclick="document.cadastro.TipoAcao.value = 2;" />
	            	<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
								<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
	          	</td>
		       </tr>
         </table>
				
			   <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  			 <tr>
	    			 <td colspan="15" align="right">
	      			 <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        			 <tr>
	          			 <td colspan="2" align="left" class="listViewPaginationTdS1" style="PADDING-BOTTOM: 2px"><span class="pageNumbers"><strong>Informe as quantidades e os valores dos produtos de foto e vídeo do evento</strong></span></td>
	        			 </tr>
	      			 </table>
	    			 </td>
	  			 </tr>
	  			 
					 <tr height="20">
      			 <td width="30" class="listViewThS1">
        		   <div align="center">Inc.</div>
      			 </td>
		  			 <td width="218" class="listViewThS1">
						 	 &nbsp;&nbsp;Descrição do Produto
						 </td>
						 <td width="40" align="right" class="listViewThS1">
						 	 Quant Dispon.
						 </td>
						 <td width="80" align="center" class="listViewThS1">
						 	 Valor<br/>Unit.
						 </td>
						 <td width="70" align="center" class="listViewThS1">
						 	 Quant Venda
						 </td>
						 <td width="70" align="center" class="listViewThS1">
						 	 Quant Brinde
						 </td>
						 <td width="90" align="center" class="listViewThS1">
						 	 Descontos<br/>R$
						 </td>
						 <td width="90" align="center" class="listViewThS1">
						 	Valor<br/>Total
						 </td>						 
						 <td width="40" align="center" class="listViewThS1">
						 	 	Estoque
						 </td>
						 <td width="70" align="center" class="listViewThS1">
						   %<br/> Comis.
						 </td>
						 <td width="50" align="center" class="listViewThS1">
						   Bonus Comissão
						 </td>
						 <td width="80" align="center" class="listViewThS1">
						   Valor Comissão
						 </td>
	  			 </tr>

					 <?php
						 
						 //Cria a variavel zerada para o contador de checkboxes
						 $edtItemChk = 0;
						 $total_geral_comissao_formando = 0;
						 $formandos_atendidos = 0;
						  
						 
						 //Percorre o array
					   while ($dados_formandos = mysql_fetch_array($sql_formandos)){
					   	
					   	//Zera a variável do total de compra do formando
						  $total_venda_formando = 0;
						  $total_comissao_formando = 0;
						 
					 ?>
						   
					 <tr height="24">
    				 <td colspan="7" valign="bottom" style="padding-left: 8px">    				 	 
		  				 <span style="font-size: 14px"><b>							 
							 		<b><?php echo $dados_formandos["nome"] ?></b>
							 </span>
						 </td>						 
					 </tr>
					 						   
						   <?php
						 
							 //Monta a query de filtragem dos itens
							 $filtra_item = "SELECT * FROM categoria_fotovideo WHERE empresa_id = $empresaId ORDER BY nome";
							
							 //Executa a query
							 $lista_item = mysql_query($filtra_item);
							 
							 //Cria um contador com o número de contar que a query retornou
							 $nro_item = mysql_num_rows($lista_item);						   
						  	
							 $marca_formando = 0;
						   
							 //Percorre o array
						   while ($dados_item = mysql_fetch_array($lista_item)){
						   	
						   		
						   	
						   	 //Efetua a pesquisa para ver se o item já se encontra cadastrado no evento
						   	 $filtra_item_evento = "SELECT 
								 											quantidade_disponivel,
																			valor_venda,
																			comissao,
																			bonus_comissao,
																			quantidade_venda,
																			valor_desconto,
																			quantidade_brinde
																			FROM eventos_fotovideo 
																			WHERE evento_id = $EventoId 
																			AND formando_id = $dados_formandos[id]
																			AND item_id = '$dados_item[id]'";
							
								 	//Executa a query
								 	$query_procura_item_evento = mysql_query($filtra_item_evento);
								 
								 								
									//Monta um array com o item de retorno
									$dados_procura_item_evento = mysql_fetch_array($query_procura_item_evento);
									
									//Conta se retornou algum registro
									$conta_retorno = mysql_num_rows($query_procura_item_evento);
									
									//Caso encontrou o item para ser incluso no orçamento
									if ($conta_retorno == 1) {
										//Seta para marcar o checkbox
										$chkItem = "checked='checked'";
										
										//marca o formando como atendido
										$marca_formando = 1;
									} else {
										//Seta para o chekbox não ser marcado
										$chkItem = "";
									}	
							 						 
							 							 				
					 ?>

					 <tr height="16">
    				 <td valign="top">
		  				 <div align="center">
      				 <input name="<?php echo ++$edtItemChk ?>" type="checkbox" value="<?php echo $dados_item[id] ?>" style="border: 0px" title="Clique para marcar ou desmarcar a aquisição deste produto pelo formando" <?php echo $chkItem ?>/>
      				 <input name="edtFormando<?php echo $edtItemChk ?>" type="hidden" value="<?php echo $dados_formandos[id] ?>" />
      				 </div>
    				 </td>
						 <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
      				 <span style="color: #33485C"><b><?php echo $dados_item[nome] ?></b></span>
    				 </td>    				 
						 <td align="left" valign="top" bgcolor="#fdfdfd" class="currentTabList">
							 <input name="edtQtdeDisp<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade disponível do produto para o formando" value="<?php echo $dados_procura_item_evento[quantidade_disponivel] ?>" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
						 </td>
						 
    				 <td  align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
							<?php
								
									//Verifica se já existe um preço de venda cadastrado para o item
									if ($dados_procura_item_evento[valor_venda] > 0) {
										//Caso tenha valor de venda cadastrado mostra o valor do item para este evento
										$preco_venda = str_replace(".",",",$dados_procura_item_evento[valor_venda]);
									} else {
										//Caso não, pega o valor de venda padrão do item no cadastro normal
										$preco_venda = str_replace(".",",",$dados_item[valor_venda]);
									}
									
									//Cria um objeto do tipo WDEdit 
									$objWDComponente = new WDEditReal();
									
									//Define nome do componente
									$objWDComponente->strNome = "edtValor$edtItemChk";
									//Define o tamanho do componente
									$objWDComponente->intSize = 8;
									//Busca valor definido no XML para o componente
									$objWDComponente->strValor = "$preco_venda";
									//Busca a descrição do XML para o componente
									$objWDComponente->strLabel = "";
									//Determina um ou mais eventos para o componente
									$objWDComponente->strEvento = "";
									//Define numero de caracteres no componente
									$objWDComponente->intMaxLength = 12;
									
									//Cria o componente edit
									$objWDComponente->Criar();
							?>							 
						 </td>
    				 <td align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
						 		<input name="edtQtdeVenda<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px" maxlength="10" title="Informe a quantidade vendida do produto ao formando" value="<?php echo $dados_procura_item_evento[quantidade_venda] ?>" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
						 </td>
						 <td  align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
						 		<input name="edtQtdeBrinde<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px" maxlength="10" title="Informe a quantidade do produto distribuida como brinde ao formando" value="<?php echo $dados_procura_item_evento[quantidade_brinde] ?>" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" />
						 </td>
						 <td  align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
							<?php
								
									
									//Caso tenha valor de desconto cadastrado mostra o valor
									$preco_desconto = str_replace(".",",",$dados_procura_item_evento[valor_desconto]);
									
									//Cria um objeto do tipo WDEdit 
									$objWDComponente = new WDEditReal();
									
									//Define nome do componente
									$objWDComponente->strNome = "edtDesconto$edtItemChk";
									//Define o tamanho do componente
									$objWDComponente->intSize = 8;
									//Busca valor definido no XML para o componente
									$objWDComponente->strValor = "$preco_desconto";
									//Busca a descrição do XML para o componente
									$objWDComponente->strLabel = "";
									//Determina um ou mais eventos para o componente
									$objWDComponente->strEvento = "";
									//Define numero de caracteres no componente
									$objWDComponente->intMaxLength = 12;
									
									//Cria o componente edit
									$objWDComponente->Criar();
							?>							 
						 </td>						 
    				 <td align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 0px">
							 <input name="edtTotal<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" value="<?php echo number_format(($dados_procura_item_evento[valor_venda] * $dados_procura_item_evento[quantidade_venda]) - $dados_procura_item_evento[valor_desconto], 2, ',', '.') ?>" />
							</td>
							<td align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
								<input name="edtSaldo<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px; color: #000000; background-color:#E6E6E6" maxlength="10" readonly="readonly" title="Saldo do produto ao formando" value="<?php echo $dados_procura_item_evento[quantidade_disponivel] - $dados_procura_item_evento[quantidade_venda]  - $dados_procura_item_evento[quantidade_brinde] ?>" />
						 	</td>
						  <td  align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
						 		<input name="edtComissao<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px" maxlength="10" title="Informe percentual de comissão do produto" value="<?php echo $dados_procura_item_evento[comissao] ?>" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" />
						 </td>
						 <td  align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
							<?php
								
									//Verifica se já existe um preço de venda cadastrado para o item
									if ($dados_procura_item_evento[bonus_comissao] > 0) {
										//Caso tenha valor de venda cadastrado mostra o valor do item para este evento
										$bonus_comissao = str_replace(".",",",$dados_procura_item_evento[bonus_comissao]);
									} else {
										//Caso não, pega o valor de venda padrão do item no cadastro normal
										$bonus_comissao = str_replace(".",",",$dados_item[bonus_comissao]);
									}
									
									//Cria um objeto do tipo WDEdit 
									$objWDComponente = new WDEditReal();
									
									//Define nome do componente
									$objWDComponente->strNome = "edtBonus$edtItemChk";
									//Define o tamanho do componente
									$objWDComponente->intSize = 8;
									//Busca valor definido no XML para o componente
									$objWDComponente->strValor = "$bonus_comissao";
									//Busca a descrição do XML para o componente
									$objWDComponente->strLabel = "";
									//Determina um ou mais eventos para o componente
									$objWDComponente->strEvento = "";
									//Define numero de caracteres no componente
									$objWDComponente->intMaxLength = 12;
									
									//Cria o componente edit
									$objWDComponente->Criar();
							?>							 
						 </td>
						 <td align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 0px">
							 <input name="edtTotalComissao<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" title="Valor da comissão do item" value="<?php echo number_format((((($dados_procura_item_evento[valor_venda] * $dados_procura_item_evento[quantidade_venda]) - $dados_procura_item_evento[valor_desconto]) * $dados_procura_item_evento[comissao] / 100)) + $dados_procura_item_evento[bonus_comissao], 2, ',', '.') ?>" />
							</td>							 
					 </tr>

						 <?php
						 	//Cria a variável para o total da venda para o formando
						 	$total_venda_formando = $total_venda_formando +  (($dados_procura_item_evento[valor_venda] * $dados_procura_item_evento[quantidade_venda]) - $dados_procura_item_evento[valor_desconto]);
						 	
						 	$total_comissao_formando = $total_comissao_formando + (((($dados_procura_item_evento[valor_venda] * $dados_procura_item_evento[quantidade_venda]) - $dados_procura_item_evento[valor_desconto]) * $dados_procura_item_evento[comissao] / 100)) + $dados_procura_item_evento[bonus_comissao];						 							 	
							
							//Fecha o while
						 	}
							
							if ($marca_formando == 1){
						 		
						 		$formandos_atendidos++;
						 		$texto_pendente = "";
						 	} else {
						 		
						 		$texto_pendente = "<br/><span style='font-size: 14px; color: #990000'>FORMANDO PENDENTE</span>";
						 	}
							
							//Monta a linha com a totalização do formando
							$formata_total_formando = number_format($total_venda_formando, 2, ',', '.');
							$formata_comissao_formando = number_format($total_comissao_formando, 2, ',', '.');
							
							$total_geral_comissao_formando = $total_geral_comissao_formando + $total_comissao_formando;
							
							echo "<tr>
											<td valign='top'>
												<span style='font-size: 12px' style='padding-left: 4px; padding-right: 0px'><b>Obs</span>
											</td>
											<td colspan='2' style='padding-left: 4px; padding-right: 0px'>											 
												<textarea name='edtObsFormando$dados_formandos[id]' wrap='virtual' class='datafield' id='edtObsFormando$dados_formandos[id]' style='width: 250px; height: 40px; font-size: 11px'>$dados_formandos[obs_compra]</textarea>
											</td>
											<td colspan='4' align='right' valign='top' style='padding-right: 0px'>
												<span style='font-size: 14px'><b>Total do Formando:</span>$texto_pendente
											</td>
											<td align='right' valign='top' style='padding-right: 4px'>
												<span style='font-size: 14px; color: #990000'>
													<b>$formata_total_formando<b>
												</span>
											</td>
                      <td>
                        &nbsp;
                      </td>
											<td colspan='2' align='right' valign='top'>
												<span style='font-size: 14px'><b>Comissão:</span>
											</td>
											<td align='right' valign='top' style='padding-right: 3px'>
												<span style='font-size: 14px; color: #990000'>
													<b>$formata_comissao_formando<b>
												</span>
											</td>
										</tr>";						 	
						 	
						 	
						//Fecha o while da categoria
						}
					   	//Envia com o formulario o total final do contador para efetuar o for depois
						 	?>	
						 <input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>"></input>
						 <input name="EventoId" type="hidden" value="<?php echo $EventoId ?>"></input>		
					 </table>           

     </td>
   </tr>
   <tr>
   	<td style="padding-top: 4px">
   		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
				<tr>
					<td height="26">
						<span style="font-size: 12px">
							<span style="font-size: 14px"><b>&nbsp;&nbsp;Total de Formandos no Evento:&nbsp;<span style="color: #990000"><?php echo $registros_formandos ?></span>&nbsp;&nbsp;&nbsp;&nbsp;Atendidos:&nbsp;<span style="color: #990000"><?php echo $formandos_atendidos ?></span>&nbsp;&nbsp;&nbsp;&nbsp;Pendentes:&nbsp;<span style="color: #990000"><?php echo $registros_formandos - $formandos_atendidos ?></span></span>
						</span>
					</td>
				</tr>
			</table>			 
   	</td>
   </tr>   
   <tr>
   	<td>
   	<br/>
   		<table cellspacing="0" cellpadding="0" border="0">
        <tr>
        	<td colspan="2" style="PADDING-BOTTOM: 2px">
        		<input name="Submit" type="submit" class="button" title="Salva os itens de Foto e Vídeo do Evento" value="Salvar Foto e Vídeo do Evento">
        		<input name="Submit" type="submit" class="button" title="Congela a posição do cadastro do foto e vídeo para a data de hoje" value="Salvar e Congelar Posição Atual" style="width: 200px" onclick="document.cadastro.TipoAcao.value = 2;" />
          	<input class="button" title="Limpa o conteúdo dos campos digitados [Alt+L]" accesskey="L" name="Reset" type="reset" id="Reset" value="Limpar Campos">
						<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
        	</td>
       </tr>
     </table>
		 <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  	<tr>
	       <td colspan="15" align="right">
	      	 <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        			 <tr>
	          			 <td colspan="2" align="left" class="listViewPaginationTdS1" style="PADDING-BOTTOM: 2px"><span class="pageNumbers"><b>Acompanhamento de estoques de Foto e Vídeo do evento</b></span></td>
	        			 </tr>
	      			 </table>
	    			 </td>
	  			 </tr>
	  			 
					 <tr height="20">
		  			 <td width="310" class="listViewThS1">
						 	 &nbsp;&nbsp;Descrição do Produto
						 </td>
						 <td width="100" align="center" class="listViewThS1">
						 	 Qtde Total<br/>Disponível
						 </td>
						 <td width="100" align="center" class="listViewThS1">
						 	 Qtde Total<br/> Vendida
						 </td>
						 <td width="100" align="center" class="listViewThS1">
						 	 Qtde Total<br/> Brindes
						 </td>
						 <td width="100" align="center" class="listViewThS1">
						 	 Valor Total<br/> Descontos
						 </td>
						 <td width="100" align="center" class="listViewThS1">
						 	 Valor Total<br/>Vendido
						 </td>
						 <td width="100" align="center" class="listViewThS1">
						 	 Estoque Total
						 </td>
						 <td width="100" align="center" class="listViewThS1">
						 	 % Venda
						 </td>
	  			 </tr>

					 <?php
						 //Monta a query totalização dos itens
						 $sql_total = mysql_query("SELECT 
																			sum( ite.quantidade_disponivel ) AS total_disponivel, 
																			sum( ite.quantidade_venda ) AS total_venda,
																			sum( ite.quantidade_brinde ) AS total_brinde,
																			sum( ite.valor_desconto ) AS total_desconto,
																			sum( (ite.quantidade_venda * ite.valor_venda) - ite.valor_desconto) AS total_item, 
																			prod.nome AS item_nome
																			FROM eventos_fotovideo ite
																			LEFT OUTER JOIN categoria_fotovideo prod ON prod.id = ite.item_id
																			WHERE ite.evento_id = '$EventoId'
																			GROUP BY ite.item_id
																			ORDER by prod.nome");						 
						 
						 //Cria a variável do total geral
						 $total_geral = 0;
						 
						 //Percorre o array
					   while ($dados_total = mysql_fetch_array($sql_total)){
						 						 
					 ?>
						   
					 <tr height="20">
    				 <td valign="middle">		  				 
      				 <span style="font-size: 12px"><b>&nbsp;&nbsp;<?php echo $dados_total[item_nome] ?></b></span>
    				 </td>
						 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
								<input name="edtTotalDisp" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6" maxlength="10" readonly="readonly" title="Quantidade Total Disponível do Produto no Evento" value="<?php echo $dados_total[total_disponivel] ?>" />								
    				 </td>    				 
						 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
								<input name="edtTotalVenda" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6" maxlength="10" readonly="readonly" title="Quantidade Total Vendida do Produto no Evento" value="<?php echo $dados_total[total_venda] ?>" />
						 </td>
						 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
								<input name="edtTotalBrinde" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6" maxlength="10" readonly="readonly" title="Quantidade Total de Brinde do Produto no Evento" value="<?php echo $dados_total[total_brinde] ?>" />
						 </td>
						 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
								<input name="edtTotalDesconto" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" title="Quantidade Total de Descontos do Produto no Evento" value="<?php echo number_format($dados_total[total_desconto], 2, ',', '.') ?>" />
						 </td>
						 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
								<input name="edtTotalVenda" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" title="Quantidade Total Vendida do Produto no Evento" value="<?php echo number_format($dados_total[total_item], 2, ',', '.') ?>" />
						 </td>						     				 
						 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
								<input name="edtSaldoDisp" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6" maxlength="10" readonly="readonly" title="Saldo Total do Produto no Evento" value="<?php echo $dados_total[total_disponivel] - $dados_total[total_venda]- $dados_total[total_brinde] ?>" />
						 </td>
						 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
								<input name="edtPercVenda" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6" maxlength="10" readonly="readonly" title="Percentual de Venda do Produto no Evento" value="<?php echo round(($dados_total[total_venda] / $dados_total[total_disponivel]) * 100) ?>&nbsp;%" />
						 </td>						 
					 </tr>

						<?php 
							
							$total_geral = $total_geral + $dados_total[total_item];
							
							
							//Fecha o while da categoria
							}							
					 	?>
						 
						 <tr>
						 		<td colspan="5" align="right" style="padding-right: 22px">
							 		<span style="font-size: 14px"><b>Total Geral do Evento:</span>
								</td>
								<td align="right" style="padding-right: 10px">
									<span style="font-size: 14px; color: #990000"><b><?php echo number_format($total_geral, 2, ',', '.') ?><b></span>
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td colspan="5" align="right" style="padding-top: 10px; padding-right: 22px">
							 		<span style="font-size: 14px"><b>Total de Comissões:</span>
								</td>
								<td align="right" style="padding-top: 10px; padding-right: 10px">
									<span style="font-size: 14px; color: #990000"><b><?php echo number_format($total_geral_comissao_formando, 2, ',', '.') ?><b></span>
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td colspan="5" align="right" style="padding-right: 22px">
							 		<span style="font-size: 14px"><b>Desconto de Comissões:</span>
								</td>
								<td align="right" style="padding-right: 10px">
									<?php
								
									//Caso tenha valor de venda cadastrado mostra o valor do item para este evento
									$desconto_comissao = str_replace(".",",",$dados_evento[desconto_comissao]);
									
									//Cria um objeto do tipo WDEdit 
									$objWDComponente = new WDEditReal();
									
									//Define nome do componente
									$objWDComponente->strNome = "edtDescontoComissao";
									//Define o tamanho do componente
									$objWDComponente->intSize = 8;
									//Busca valor definido no XML para o componente
									$objWDComponente->strValor = "$desconto_comissao";
									//Busca a descrição do XML para o componente
									$objWDComponente->strLabel = "";
									//Determina um ou mais eventos para o componente
									$objWDComponente->strEvento = "";
									//Define numero de caracteres no componente
									$objWDComponente->intMaxLength = 12;
									
									//Cria o componente edit
									$objWDComponente->Criar();
								?>									
							 </td>
						   <td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td colspan="5" align="right" style="padding-right: 22px">
							 		<span style="font-size: 14px"><b>Total Geral de Comissões:</span>
								</td>
								<td align="right" style="padding-right: 10px">
									<span style="font-size: 14px; color: #990000"><b><?php echo number_format($total_geral_comissao_formando - $dados_evento[desconto_comissao], 2, ',', '.') ?><b></span>
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td align="left" valign="bottom" style="padding-top: 10px; padding-left: 10px">
							 		<span style="font-size: 14px"><b>Selecione o Vendedor:</span>
								</td>								
								 <td colspan="4" align="right" style="padding-top: 10px; padding-right: 22px">
							 		<span style="font-size: 14px"><b>Valor pago a Vista:</span>
								</td>
								<td align="right" style="padding-top: 10px; padding-right: 10px">
									<?php
								
										//Caso tenha valor de venda cadastrado mostra o valor do item para este evento
										$comissao_avista = str_replace(".",",",$dados_evento[comissao_avista]);
										
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtComissaoAVista";
										//Define o tamanho do componente
										$objWDComponente->intSize = 8;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "$comissao_avista";
										//Busca a descrição do XML para o componente
										$objWDComponente->strLabel = "";
										//Determina um ou mais eventos para o componente
										$objWDComponente->strEvento = "";
										//Define numero de caracteres no componente
										$objWDComponente->intMaxLength = 12;
										
										//Cria o componente edit
										$objWDComponente->Criar();
									
									?>
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td align="left" style="padding-top: 10px; padding-left: 8px">
							 		<select name="cmbColaboradorId" id="cmbColaboradorId" style="width:280px">
		                 <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_colaborador = mysql_fetch_object($dados_colaborador)) { 
										 ?>
		                 <option <?php if ($lookup_colaborador->id == $dados_evento[vendedor_id]) {
                        echo " selected ";
                      } ?>
											value="<?php echo $lookup_colaborador->id ?>"><?php echo $lookup_colaborador->nome ?> </option>
		                 <?php } ?>
		               </select>
								</td>
						 		<td colspan="4" align="right" style="padding-right: 22px">
							 		<span style="font-size: 14px"><b>Valor pago 30 dias:</span>
								</td>
								<td align="right" style="padding-right: 10px">
									<?php
								
										//Caso tenha valor de venda cadastrado mostra o valor do item para este evento
										$comissao_30 = str_replace(".",",",$dados_evento[comissao_30]);
										
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtComissao30";
										//Define o tamanho do componente
										$objWDComponente->intSize = 8;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "$comissao_30";
										//Busca a descrição do XML para o componente
										$objWDComponente->strLabel = "";
										//Determina um ou mais eventos para o componente
										$objWDComponente->strEvento = "";
										//Define numero de caracteres no componente
										$objWDComponente->intMaxLength = 12;
										
										//Cria o componente edit
										$objWDComponente->Criar();
									
									?>									
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td colspan="5" align="right" style="padding-right: 22px">
							 		<span style="font-size: 14px"><b>Valor pago 60 dias:</span>
								</td>
								<td align="right" style="padding-right: 10px">
									<?php
								
										//Caso tenha valor de venda cadastrado mostra o valor do item para este evento
										$comissao_60 = str_replace(".",",",$dados_evento[comissao_60]);
										
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtComissao60";
										//Define o tamanho do componente
										$objWDComponente->intSize = 8;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "$comissao_60";
										//Busca a descrição do XML para o componente
										$objWDComponente->strLabel = "";
										//Determina um ou mais eventos para o componente
										$objWDComponente->strEvento = "";
										//Define numero de caracteres no componente
										$objWDComponente->intMaxLength = 12;
										
										//Cria o componente edit
										$objWDComponente->Criar();
									
									?>									
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>			
				</table>
				
				<?php
				
				//Fecha o if de se tiver formandos cadastrados
				}
				
				?>
   	</td>
   </tr>
</form>
</table>  	 

</tr>
</table>
