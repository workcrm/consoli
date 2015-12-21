<?php 
###########
## Módulo para exibição dos detalhes do congelamento do foto e vídeo
## Criado: 17/08/2009 - Maycon Edinger
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

$data_congela = $_GET["DataCongela"];
$hora_congela = $_GET["HoraCongela"];

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

//Monta o lookup da tabela de colaboradores (para a pessoa_id)
//Monta o SQL
$lista_colaborador = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_colaborador = mysql_query($lista_colaborador);
?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdCarregarRelatorio() {
   
	var Form;
  Form = document.cadastro;
		
	//Captura o valor referente ao radio button da posição 
  var chkPosicao= document.getElementsByName('chkPosicaoAtualTipo');
   
	for (var i=0; i < chkPosicao.length; i++) {
    if (chkPosicao[i].checked == true) {
      chkPosicao = chkPosicao[i].value;
      break;
    }
  }
		

	//Monta url que do relatório que será carregado		
	url = "./relatorios/EventoFotoVideoCongelaRelatorioPDF.php?EventoId=<?php echo $EventoId ?>&UsuarioNome=<?php echo $usuarioNome . ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&DataCongela=<?php echo $data_congela ?>&HoraCongela=<?php echo $hora_congela ?>&TipoRelatorio=" + chkPosicao;

  //Executa o relatório selecionado
	abreJanela(url);	

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
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Exibição do Congelamento de Foto e Vídeo do Evento</span></td>
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
          </table>
				<br/>
				<span class="TituloModulo">Assinatura Digital:</span>
				<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
        	<tr>
          	<td valign="top" width="160" class="dataLabel">Data do Congelamento:</td>
          	<td class="tabDetailViewDF">
							<?php 
								//Exibe o timestamp do cadastro da conta
								echo DataMySQLRetornar($data_congela) . " - " . $hora_congela 
							?>					
						</td>
        	</tr>                 
  	  	</table>
       
				<br/> 
				
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
  			  <tr>
  			 		<td width="200" style="padding-bottom: 4px">
  			 			<input class="button" title="Retorna a exibição do foto e vídeo do evento" name="btnVoltar" type="button" id="btnRelatorio" value="Retornar ao Foto e Vídeo" style="width:180px" onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" />
  			 		</td>
  			 		<td align="right" style="padding-bottom: 4px">
  						 <input class="button" title="Imprime a planilha de detalhamento deste congelamento de foto e vídeo" name="btnVoltar" type="button" id="btnRelatorio" value="Imprimir Planilha do Congelamento" style="width:220px" onclick="wdCarregarRelatorio()" />&nbsp;&nbsp;&nbsp;<input name="chkPosicaoAtualTipo" type="radio" value="1" checked="checked" />&nbsp;Relatório Detalhado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="chkPosicaoAtualTipo" type="radio" value="2" />&nbsp;Somente Totais<br/>	
  			 		</td>
  			  </tr>
			 	</table>
				
			   <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  			 <tr>
	    			 <td colspan="15" align="right">
	      			 <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        			 <tr>
	          			 <td colspan="2" align="left" class="listViewPaginationTdS1" style="PADDING-BOTTOM: 2px"><span class="pageNumbers"><strong>Quantidades e valores do congelamento destes produtos de foto e vídeo do evento</strong></span></td>
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
						 
						 //Monta a query para capturar os formandos
						 $sql_formandos = mysql_query("SELECT * FROM eventos_formando WHERE evento_id = $EventoId ORDER BY nome"); 
							
						 $registros_formandos = mysql_num_rows($sql_formandos);																 
						 
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
																			FROM eventos_fotovideo_congela 
																			WHERE evento_id = $EventoId 
																			AND data_congela = '$data_congela'
																			AND hora_congela = '$hora_congela'
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
      				 <input name="<?php echo ++$edtItemChk ?>" type="checkbox" value="<?php echo $dados_item[id] ?>" style="border: 0px; background-color:#E6E6E6;" <?php echo $chkItem ?> disabled="true" />
      				 <input name="edtFormando<?php echo $edtItemChk ?>" type="hidden" value="<?php echo $dados_formandos[id] ?>" />
      				 </div>
    				 </td>
						 <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
      				 <span style="color: #33485C"><b><?php echo $dados_item[nome] ?></b></span>
    				 </td>    				 
						 <td align="left" valign="top" bgcolor="#fdfdfd" class="currentTabList">
							 <input name="edtQtdeDisp<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px" maxlength="5" title="Quantidade disponível do produto para o formando" value="<?php echo $dados_procura_item_evento[quantidade_disponivel] ?>" style="background-color:#E6E6E6" readonly="readonly" />
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
							
							?>
							
							<input name="edtValor<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 56px" title="Valor de venda do produto" value="<?php echo $preco_venda ?>" style="background-color:#E6E6E6" readonly="readonly" />
																 
						 </td>
    				 <td align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
						 		<input name="edtQtdeVenda<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px" maxlength="10" title="Quantidade vendida do produto ao formando" value="<?php echo $dados_procura_item_evento[quantidade_venda] ?>" style="background-color:#E6E6E6" readonly="readonly" />
						 </td>
						 <td  align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
						 		<input name="edtQtdeBrinde<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px" maxlength="10" title="Quantidade do produto distribuida como brinde ao formando" value="<?php echo $dados_procura_item_evento[quantidade_brinde] ?>" style="background-color:#E6E6E6" readonly="readonly" />
						 </td>
						 <td  align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
							<?php
								
									
									//Caso tenha valor de desconto cadastrado mostra o valor
									$preco_desconto = str_replace(".",",",$dados_procura_item_evento[valor_desconto]);
							?>
								
								<input name="edtDesconto<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 56px" title="Valor de desconto do produto" value="<?php echo $preco_desconto ?>" style="background-color:#E6E6E6" readonly="readonly" />
																 
						 </td>						 
    				 <td align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 0px">
							 <input name="edtTotal<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" value="<?php echo number_format(($dados_procura_item_evento[valor_venda] * $dados_procura_item_evento[quantidade_venda]) - $dados_procura_item_evento[valor_desconto], 2, ',', '.') ?>" />
							</td>
							<td align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
								<input name="edtSaldo<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px; color: #000000; background-color:#E6E6E6" maxlength="10" readonly="readonly" title="Saldo do produto ao formando" value="<?php echo $dados_procura_item_evento[quantidade_disponivel] - $dados_procura_item_evento[quantidade_venda]  - $dados_procura_item_evento[quantidade_brinde] ?>" />
						 	</td>
						  <td  align="center" valign="top" bgcolor="#fdfdfd" class="currentTabList">
						 		<input name="edtComissao<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px" maxlength="10" title="Percentual de comissão do produto" value="<?php echo $dados_procura_item_evento[comissao] ?>" style="background-color:#E6E6E6" readonly="readonly" />
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
							?>
							
							<input name="edtBonus<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 56px" title="Bonus de comissão do produto" value="<?php echo $bonus_comissao ?>" style="background-color:#E6E6E6" readonly="readonly" />
																								 
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
												<textarea name='edtObsFormando$dados_formandos[id]' wrap='virtual' class='datafield' id='edtObsFormando$dados_formandos[id]' style='width: 250px; height: 40px; font-size: 11px; background-color:#E6E6E6;' readonly='readonly'>$dados_formandos[obs_compra]</textarea>
											</td>
											<td colspan='4' align='right' valign='top' style='padding-right: 0px'>
												<span style='font-size: 14px'><b>Total do Formando:</span>$texto_pendente
											</td>
											<td align='right' valign='top' style='padding-right: 0px'>
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
						 <input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>" />
						 <input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />		
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
				
					<input class="button" title="Retorna a exibição do foto e vídeo do evento" name="btnVoltar" type="button" id="btnRelatorio" value="Retornar ao Foto e Vídeo" style="width:180px" onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" />
					<br/> 
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
																			FROM eventos_fotovideo_congela ite
																			LEFT OUTER JOIN categoria_fotovideo prod ON prod.id = ite.item_id
																			WHERE ite.evento_id = $EventoId
																			AND ite.data_congela = '$data_congela'
																			AND ite.hora_congela = '$hora_congela'
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

							//Pega os dados do fechamento do congelamento							
							$sql_fechamento = mysql_query("SELECT 													
																				eve.desconto_comissao,
																				eve.comissao_avista,
																				eve.comissao_30,
																				eve.comissao_60,
																				eve.vendedor_id,
																				vend.nome as vendedor_nome
																				FROM eventos_fotovideo_congela_fechamento eve 
																				LEFT OUTER JOIN colaboradores vend ON vend.id = eve.vendedor_id
																				WHERE eve.evento_id = $EventoId
																				AND eve.data_congela = '$data_congela'
																				AND eve.hora_congela = '$hora_congela'																				
																				");


							//Cria o array dos dados
							$dados_fechamento = mysql_fetch_array($sql_fechamento);
						
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
									<input name="edtDescontoComissao" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" title="Desconto de comissões" value="<?php echo number_format($dados_fechamento[desconto_comissao], 2, ',', '.') ?>" />
							 </td>
						   <td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td colspan="5" align="right" style="padding-right: 22px">
							 		<span style="font-size: 14px"><b>Total Geral de Comissões:</span>
								</td>
								<td align="right" style="padding-right: 10px">
									<span style="font-size: 14px; color: #990000"><b><?php echo number_format($total_geral_comissao_formando - $dados_fechamento[desconto_comissao], 2, ',', '.') ?><b></span>
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td align="left" valign="bottom" style="padding-top: 10px; padding-left: 10px">
							 		<span style="font-size: 14px"><b>Vendedor:</span>
								</td>								
								 <td colspan="4" align="right" style="padding-top: 10px; padding-right: 22px">
							 		<span style="font-size: 14px"><b>Valor pago a Vista:</span>
								</td>
								<td align="right" style="padding-top: 10px; padding-right: 10px">
									<input name="edtComissaoAVista" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" title="Valor da comissão pago a vista" value="<?php echo number_format($dados_fechamento[comissao_avista], 2, ',', '.') ?>" />
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td align="left" style="padding-top: 10px; padding-left: 10px">
							 		<b><?php echo $dados_fechamento["vendedor_nome"] ?></b>
								</td>
						 		<td colspan="4" align="right" style="padding-right: 22px">
							 		<span style="font-size: 14px"><b>Valor pago 30 dias:</span>
								</td>
								<td align="right" style="padding-right: 10px">
									<input name="edtComissao30" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" title="Valor da comissão pago 30 dias" value="<?php echo number_format($dados_fechamento[comissao_30], 2, ',', '.') ?>" />
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>
						 <tr>
						 		<td colspan="5" align="right" style="padding-right: 22px">
							 		<span style="font-size: 14px"><b>Valor pago 60 dias:</span>
								</td>
								<td align="right" style="padding-right: 10px">
									<input name="edtComissao60" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" title="Valor da comissão pago 60 dias" value="<?php echo number_format($dados_fechamento[comissao_60], 2, ',', '.') ?>" />									
								</td>
								<td colspan="2">&nbsp;</td>
						 </tr>			
				</table>
   	</td>
   </tr>
</form>
</table>  	 

</tr>
</table>
