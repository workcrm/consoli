<?php 
###########
## Módulo para Exibição da locação
## Criado: 29/08/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
	header("Content-Type: text/html;  charset=ISO-8859-1", true);

}

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
$LocacaoId = $_GET["LocacaoId"];

//pesquisa as diretivas do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";													  													  
							  
//Executa a query
$resultado_usuario = mysql_query($sql_usuario);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_usuario);


//Recupera dos dados da locação
$sql_evento = "SELECT 
			loc.id,
			loc.data,
			loc.tipo_pessoa,
			loc.pessoa_id,
			loc.descricao,
			loc.situacao,
			loc.devolucao_prevista,
			loc.devolucao_realizada,
			loc.recebido_por,
			loc.observacoes,
			loc.cadastro_timestamp,
			loc.cadastro_operador_id,
			loc.alteracao_timestamp,
			loc.alteracao_operador_id,
			loc.obs_financeira,
			loc.posicao_financeira,
			loc.numero_nf,
			usu_cad.nome as operador_cadastro_nome, 
			usu_cad.sobrenome as operador_cadastro_sobrenome,
			usu_alt.nome as operador_alteracao_nome, 
			usu_alt.sobrenome as operador_alteracao_sobrenome,
			cli.id as cliente_id,
			cli.nome as cliente_nome,
			forn.id as fornecedor_id,
			forn.nome as fornecedor_nome,
			col.id as colaborador_id,
			col.nome as colaborador_nome
			FROM locacao loc 
			LEFT OUTER JOIN clientes cli ON cli.id = loc.pessoa_id
			LEFT OUTER JOIN fornecedores forn ON forn.id = loc.pessoa_id
			LEFT OUTER JOIN colaboradores col ON col.id = loc.pessoa_id	
			LEFT OUTER JOIN usuarios usu_cad ON loc.cadastro_operador_id = usu_cad.usuario_id 
			LEFT OUTER JOIN usuarios usu_alt ON loc.alteracao_operador_id = usu_alt.usuario_id						
			WHERE loc.id = $LocacaoId";
  
//Executa a query
$resultado = mysql_query($sql_evento);

//Monta o array dos campos
$dados_locacao = mysql_fetch_array($resultado);

//Efetua o switch para o campo de status
switch ($dados_locacao['situacao']) 
{
	case 1: $desc_status = "Pendente"; break;
	case 2: $desc_status = "Recebida"; break;
}    

//Efetua o switch para o campo de pessoa
switch ($dados_locacao[tipo_pessoa]) 
{
	
	//Se for cliente
	case 1: 
		$pessoa_tipo = "Cliente";
		$pessoa_nome = $dados_locacao[cliente_nome]; 
	break;
	//Se for fornecedor
	case 2: 
		$pessoa_tipo = "Fornecedor"; 
		$pessoa_nome = $dados_locacao[fornecedor_nome];
	break;
	//Se for colaborador
	case 3: 
		$pessoa_tipo = "Colaborador"; 
		$pessoa_nome = $dados_locacao[colaborador_nome];							
	break;
	
}

//Efetua o switch para o campo de posição financeira
switch ($dados_locacao['posicao_financeira']) 
{
  
	case 1: $desc_financeiro = "A Receber"; break;
	case 2: $desc_financeiro = "Recebido"; break;
	case 3: $desc_financeiro = "Cortesia"; break;	
  
} 

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização da Locação</span></td>
				</tr>
				<tr>
					<td>
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>
	
			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
				    <td width="100%" class="text">
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td width="108" style="PADDING-BOTTOM: 2px">
									<?php
										
										//Verifica o nível de acesso do usuário
										//if ($nivelAcesso > 1) {
									?> 
									<input name="btnEditarConta" type="button" class="button" title="Edita esta Locação" value="Editar Locação" onclick="wdCarregarFormulario('LocacaoAltera.php?Id=<?php echo $dados_locacao[id] ?>&headers=1','conteudo')" />
									<?php
				 	        		
										//}
									
									?>
								</td>
								<?php
							
							/*
							<td width="90" style="PADDING-BOTTOM: 2px">
									  <?php
									    //Verifica o nível de acesso do usuário
									    if ($nivelAcesso >= 3) {
									    	//Exibe o botão de excluir
									    	echo "<form id='exclui' name='exclui' action='LocacaoExclui.php' method='post'><input class=button title='Exclui esta Locação' onClick='return confirm(\"Confirma a exclusão desta Locação ?\")' type='submit' value='Excluir' name='Delete'><input name='LocacaoId' type='hidden' value=$dados_locacao[id] /></form>";
									    }
									  ?>
				          </td>
						  */
						  ?>
				          <td align="right" style="PADDING-BOTTOM: 2px">
										<?php
											//Verifica o nível de acesso do usuário
											//if ($nivelAcesso >= 3) {
										?> 
										<input class="button" title="Emite o relatório dos detalhes da locação" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarFormulario('LocacaoRelatorio.php?LocacaoId=<?php echo $dados_locacao[id] ?>','conteudo')" />
										<?php
										  //Fecha o if
										  //}
										?>
								 </td>
					  	 </tr>
				    </table>
           
			    	<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
	           <tr>
	             <td class="dataLabel" width="15%">
	               <span class="dataLabel">Data:</span>             
							 </td>
	             <td colspan="5" class="tabDetailViewDF">
								 <b><?php echo DataMySQLRetornar($dados_locacao["data"]) ?></b>
							 </td>
	           </tr>
	           <tr>
	             <td valign="top" class="dataLabel">Tipo de Pessoa:</td>
	             <td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo $pessoa_tipo ?></td>
	           </tr>
	           <tr>
	             <td valign="top" class="dataLabel">Locador:</td>
	             <td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo $pessoa_nome ?></td>
	           </tr>
	           <tr>
	             <td valign="top" class="dataLabel">Descrição:</td>
	             <td colspan="5" valign="middle" class="tabDetailViewDF"><b><?php echo $dados_locacao["descricao"] ?></b></td>
	           </tr>
	
	           <tr>
	             <td class="dataLabel">Devolução Prevista:</td>
	             <td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo DataMySQLRetornar($dados_locacao["devolucao_prevista"]) ?></td>
	           </tr>			          
	
	           <tr>
	             <td valign="top" class="dataLabel">Situação:</td>
	             <td valign="middle" class="tabDetailViewDF">
							 	 <span style="font-size: 12px; color: #990000"><b><?php echo $desc_status ?></b></span>
							 </td>
	             <td width="130" valign="middle" class="dataLabel">Devolução Realizada:</td>
	             <td width="28%" valign="middle" class="tabDetailViewDF">
							 	 <?php echo DataMySQLRetornar($dados_locacao["devolucao_realizada"]) ?>
							 </td>
	           </tr>
	          <tr>
	             <td valign="top" class="dataLabel">Recebido Por:</td>
	             <td colspan="5" valign="middle" class="tabDetailViewDF"><?php echo $dados_locacao["recebido_por"] ?></td>
	           </tr>
	           <tr>
	             <td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares :</td>
	             <td colspan="5" class="tabDetailViewDF"><?php echo $dados_locacao["observacoes"] ?></td>
	           </tr>			           
		   	  </table>
               		<br/>
			<?php
						//verifica a exibição
						if ($dados_usuario["evento_financeiro"] == 1 || $usuarioNome == 'Zulaine')
						{
					?>
	   	  <span class="TituloModulo">Informações Financeiras:</span>
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
				   <tr>
             <td width="110" valign="top" class="dataLabel">Posição Financeira:</td>
             <td valign="middle" class="tabDetailViewDF">
							 <b><?php echo $desc_financeiro ?></b>						 
						 </td>
       		 </tr>
       		 <tr>
						 <td valign="middle" width="110" class="dataLabel">Número da NF:</td>
             <td valign="middle" class="tabDetailViewDF">
							 <?php echo $dados_locacao["numero_nf"] ?>						 
						 </td>             
           </tr>
					 <tr>
             <td valign="top" class="dataLabel">Obs Financeiras:</td>
             <td class="tabDetailViewDF">
						   <?php echo nl2br($dados_locacao["obs_financeira"]) ?>
				  	 </td>
           </tr>
	   	  </table>
		  <?php
			
			}
			
		?>
		   	  <br/>
				<span class="TituloModulo">Assinatura Digital:</span>
				<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
        	<tr>
          <td valign="top" width="110" class="dataLabel">Data de Cadastro: </td>
          <td class="tabDetailViewDF">
						<?php 
							//Exibe o timestamp do cadastro da conta
							echo TimestampMySQLRetornar($dados_locacao[cadastro_timestamp]) 
						?>					
					</td>
          <td class="dataLabel">Operador:</td>
          <td class="tabDetailViewDF" colspan="3">
						<?php 
							//Exibe o nome do operador do cadastro da conta
							echo $dados_locacao[operador_cadastro_nome] . " " . $dados_locacao[operador_cadastro_sobrenome] 
						?>					
					</td>
        </tr>
        <tr>
          <td valign="top" class="dataLabel">Data de Altera&ccedil;&atilde;o: </td>
          <td class="tabDetailViewDF">
		  	 		<?php 
				 			//Verifica se este registro já foi alterado
				 			if ($dados_locacao[alteracao_operador_id] <> 0) {
								//Exibe o timestamp da alteração da conta
				   			echo TimestampMySQLRetornar($dados_locacao[alteracao_timestamp]);
				 			}
				 		?>			 		
				  </td>
          <td class="dataLabel">Operador:</td>
          <td class="tabDetailViewDF" colspan="3">
				 		<?php 
				 			//Verifica se este registro já foi alterado
				 			if ($dados_locacao[alteracao_operador_id] <> 0) {
								//Exibe o nome do operador da alteração da conta
				   			echo $dados_locacao[operador_alteracao_nome] . " " . $dados_locacao[operador_alteracao_sobrenome];
				 			}
				 		?>			 		
						</td>
        	</tr>           
	   	  </table>
        
				</td>
			</tr>

<?php 
//EXIBE OS ITENS CADASTRADOS PARA ESTA LOCAÇÃO
?>
					<tr>
					  <td>
						<br>			
			
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						  <tr>
						    <td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Itens da Locação</span></td>
						  </tr>
						  <tr>
						    <td colspan="5">
							    <img src="image/bt_espacohoriz.gif" width="100%" height="5">
							</td>
						</table>

						<?php
							//Verifica o nível de acesso do usuário
							//if ($nivelAcesso > 1) {
						?> 						
						<table width="100%" cellpadding="0" cellspacing="0" border="0" >
			  			<tr valign="middle">
								<td width="30">
			            <img src="./image/bt_item_gd.gif"/> 
			         	</td>
								<td>
			            <a title="Clique para gerenciar os itens/produtos desta locação" href="#" onclick="wdCarregarFormulario('ItemLocacaoCadastra.php?LocacaoId=<?php echo $dados_locacao[id] ?>&headers=1','conteudo')">Gerenciar Itens/Produtos</a> 
			          </td>
			          <td align="right">
			          	<input class="button" title="Gerenciar a devolução dos itens da locação" name="btnRetorno" type="button" id="btnRetorno" value="Gerenciar Retorno dos Itens" style="width:160px" onclick="wdCarregarFormulario('LocacaoRetorno.php?LocacaoId=<?php echo $dados_locacao[id] ?>&headers=1','conteudo')">
			          </td>
			    		</tr>
			    	</table>		
						<?php
						//Fecha o if do nivel de acesso
						//}
					
						//verifica todos os itens cadastrados na base para montar o primeiro array (para comparar com os que estão inclusos no evento
						//Monta a query de filtragem dos itens
					  $filtra_item = "SELECT
													  locacao_id														 
													  FROM locacao_item
													  WHERE locacao_id = $LocacaoId";
					
					  //Executa a query
					  $lista_item = mysql_query($filtra_item);
					 
					  //Cria um contador com o número de contar que a query retornou
					  $registros = mysql_num_rows($lista_item);
					   
					  ?>
			   
					<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
		
			<?php
		
			  if ($registros > 0) { //Caso houverem registros
			  echo "
			  <tr height='20'>
		        <td width='60' align='center' class='listViewThS1' style='padding-right: 5px'>Qtde Locada</td>
		        <td width='25' class='listViewThS1'>Un</td>
		        <td width='60' align='center' class='listViewThS1' style='padding-right: 5px'>Qtde Retornada</td>
		        <td width='60' align='center' class='listViewThS1' style='padding-right: 5px'>Qtde Pendente</td>
		        <td class='listViewThS1'>&nbsp;&nbsp;Descrição do Item</td>
		        <td width='70' align='right' class='listViewThS1' style='padding-right: 4px'>Preço Un.</td>
            <td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Total Item</td>
            <td class='listViewThS1'>Observações</td>
		        <td width='20'class='listViewThS1'>&nbsp;</td> 
			  </tr>"; 
			  }
		
			  if ($registros == 0) { //Caso não houverem registros
		
			  //Exibe uma linha dizendo que nao há regitros
			  echo "
			  <tr height='24'>
		      <td colspan='8' scope='row' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
				  	<font color='#33485C'><strong>Não há itens cadastrados para esta locação</strong></font>
					</td>
			  </tr>	
			  ";	  
			  } 	  
				//Monta a variável de total do evento
				$total_evento = 0;
				
				
				//Monta a query para capturar as categorias que existem cadastrados itens
				$sql_categoria = mysql_query("SELECT 
														ite.id,
														ite.categoria_id,											
														cat.nome as categoria_nome,
														loc.valor_venda
														FROM item_evento ite
														LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
														INNER JOIN locacao_item loc ON loc.item_id = ite.id
														WHERE loc.locacao_id = $LocacaoId											
														GROUP BY cat.nome
														ORDER BY cat.nome");
			
				
				//Percorre o array das funcoes
				while ($dados_categoria = mysql_fetch_array($sql_categoria)){				
				
				//Fecha o php para imprimir o texto da categoria
				?>
						   
					 <tr height="22">
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
					
					
					//Monta a query de filtragem dos itens
					$filtra_item = "SELECT 
														ite.id,
														ite.nome,
														ite.unidade,											
														cat.nome as categoria_nome,
														loc.quantidade,
														loc.quantidade_retorno,
														loc.valor_venda,
														loc.observacoes
														FROM item_evento ite
														LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
														INNER JOIN locacao_item loc ON loc.item_id = ite.id
														WHERE loc.locacao_id = $LocacaoId
														AND ite.categoria_id = '$dados_categoria[categoria_id]'
														ORDER BY cat.nome, ite.nome";
					
					//Executa a query
					$lista_item = mysql_query($filtra_item);
			   
					//Percorre o array
					while ($dados_item = mysql_fetch_array($lista_item)){
				  
            //Efetua o switch do campo de unidade de medida
        	  switch ($dados_item[unidade]) {
        	     case "PC": $texto_unidade = "PC - Peça"; break;
               case "UN": $texto_unidade = "UN - Unidade"; break;
               case "GR": $texto_unidade = "GR - Grama"; break;
      				 case "KG": $texto_unidade = "KG - Kilo"; break;	    
      				 case "LT": $texto_unidade = "LT - Litro"; break;
      			   case "PT": $texto_unidade = "PT - Pacote"; break;
               case "VD": $texto_unidade = "VD - Vidro"; break;
               case "LT": $texto_unidade = "LT - Lata"; break;
               case "BD": $texto_unidade = "BD - Balde"; break;
               case "CX": $texto_unidade = "CX - Caixa"; break;
               case "GL": $texto_unidade = "GL - Galão"; break;
               case "MT": $texto_unidade = "MT - Metro"; break;
               case "M2": $texto_unidade = "M2 - Metro Quadrado"; break;
               case "M3": $texto_unidade = "M3 - Metro Cúbico"; break;      	
        	  }
						
							//Define o botão de exclusão do item
							$botão_exclui_item = "<img src='image/grid_exclui.gif' alt='Clique para remover este item da locação' width='12' height='12' border='0' onClick=\"if(confirm('Confirma a remoção deste item da locação ?')) {wdCarregarFormulario('ItemLocacaoExclui.php?ItemId=$dados_item[id]&LocacaoId=$LocacaoId','conteudo')}\" style='cursor: pointer'>";
						
							//Define a variável do valor total do item
							$total_item = $dados_item[quantidade] * $dados_item[valor_venda];
							
							//Ajusta o total do evento
							$total_evento = $total_evento + $total_item;
              
              //Cria a variável com o total pendente do item
              $total_pendente = $dados_item[quantidade] - $dados_item[quantidade_retorno];
              
              //Verifica se o item está pendente
              if ($total_pendente > 0){
                
                $cor_status = "#990000";
                
              } else if ($total_pendente == 0) {
                
                $cor_status = "#000000";
                
              }
																			
					?>
		
				 <tr valign="middle">
					 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 5px">
	  				 <span style="color: <?php echo $cor_status ?>"><?php echo $dados_item[quantidade]	?></span>
					 </td>
					 <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
	  				 <span style="color: <?php echo $cor_status ?>"><span title="<?php echo $texto_unidade ?>"><?php echo $dados_item["unidade"] ?></span></span>
					 </td>					 
					 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 5px">
	  				<span style="color: <?php echo $cor_status ?>"><?php echo $dados_item[quantidade_retorno]	?></span>
					 </td>
					 <td valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 5px">
	  				 <span style="color: <?php echo $cor_status ?>"><?php echo $total_pendente	?></span>
					 </td>
					 <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="padding-top: 1px; padding-bottom: 2px">
	  				 <span style="color: <?php echo $cor_status ?>"><?php echo $dados_item[nome] ?></span>
					 </td>
					 <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 4px">
	  				 <span style="color: <?php echo $cor_status ?>"><?php echo "R$ " . number_format($dados_item[valor_venda], 2, ",", ".") ?></span>
					 </td>					 
           <td valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 8px">
	  				 <span style="color: <?php echo $cor_status ?>"><?php echo "R$ " . number_format($total_item, 2, ",", ".") ?></span>
					 </td>
					 <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
						 <span style="color: <?php echo $cor_status ?>"><?php echo $dados_item[observacoes] ?></span>
					 </td>

					 <td valign="middle" style="padding-right: 6px">
		  	  	 <div align="center">
							<?php
								//Exibe o botão de excluir o item
								echo $botão_exclui_item; 
							?>            	 
             </div>
           </td>
				 </tr>	
			
			<?php
			//Fecha o while dos itens
			}
			
			//Fecha o while das categorias
			}
      
      ?>
      </table>
      
      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
				<tr>
					<td height="26" width="60%">
						<span style="font-size: 12px">
							<?php echo "&nbsp;&nbsp;&nbsp;Valor geral dos itens da locação: <b>R$ " . number_format($total_evento, 2, ",", ".") . "</b>" ?>
						</span>
					</td>
          <td align="right">
            Itens em <span style="color: #990000"><b>vermelho</b></span> ainda estão pendentes de devolução.
          </td>
				</tr>
			</table>
       
        </td>
      </tr>
			
			</table>
			</td>
		</tr>

	</table>  	 
</td>
</tr>



</table>
</td>
