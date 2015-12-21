<?php 
###########
## Módulo para cadastro de itens da locação
## Criado: 30/08/2007 - Maycon Edinger
## Alterado: 26/11/2007 - Maycon Edinger
## Alterações: 
## 26/11/2007 - Alterado para exibição do valor de locação como valor default do item da locação
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
  $LocacaoId = $_POST["LocacaoId"]; 
} else {
  $LocacaoId = $_GET["LocacaoId"]; 
}

//Recupera dos dados do evento
$sql_locacao = mysql_query("SELECT 
													loc.id,
													loc.data,
													loc.tipo_pessoa,
													loc.pessoa_id,
													loc.descricao,
													loc.situacao,
													loc.devolucao_prevista,
													loc.devolucao_realizada,
													loc.observacoes,
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
													WHERE loc.id = $LocacaoId");

//Cria o array dos dados
$dados_locacao = mysql_fetch_array($sql_locacao);

//Efetua o switch para o campo de status
switch ($dados_locacao[situacao]) {
  case 1: $desc_status = "Pendente"; break;
	case 2: $desc_status = "Recebida"; break;
} 

//Efetua o switch para o campo de pessoa
switch ($dados_locacao[tipo_pessoa]) {
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
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Itens da Locação</span></td>
			  </tr>
			  <tr>
			    <td colspan="5">
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
        <tr>
    			<td style="padding-bottom: 2px">
						<input class="button" title="Retorna a exibição do detalhamento da locação" name="btnVoltar" type="button" id="btnRelatorio" value="Retornar a Locação" style="width:120px" onclick="wdCarregarFormulario('LocacaoExibe.php?LocacaoId=<?php echo $LocacaoId ?>&headers=1','conteudo')"/>						
		      	<br />	   	   		   		
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
          $edtLocacaoId = $_POST["LocacaoId"];

				  //Primeiro apaga todos os itens que já existem na base de itens da locação
				  $sql_exclui_item = "DELETE FROM locacao_item WHERE locacao_id = '$LocacaoId'";

				  //Executa a query
				  $query_exclui_item = mysql_query($sql_exclui_item);


					//Define o valor inicial para efetuar o FOR
					for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++){
   				
						//Monta a variável com o nome dos campos
						$texto_qtde = "edtQtde" . $contador_for;
						$texto_preco = "edtValor" . $contador_for;
						$texto_obs = "edtObs" . $contador_for;																											
						$valor_preco = MoneyMySQLInserir($_POST[$texto_preco]);
						
						//Enquanto não chegar ao final do contador total de itens
						if ($_POST[$contador_for] != 0) {
																
									$sql_insere_item = "INSERT INTO locacao_item (
												 						 locacao_id, 
																		 item_id,
																		 quantidade,
																		 valor_venda, 
																		 observacoes
																		 ) VALUES (
																		 '$LocacaoId',
																		 '$_POST[$contador_for]', 
																		 '$_POST[$texto_qtde]',
																		 '$valor_preco',
																		 '$_POST[$texto_obs]'
																		 )";																		
									
									//Insere os registros na tabela
	   							mysql_query($sql_insere_item);
							}								
														
					//Fecha o FOR
  				}
	
					//Exibe a mensagem de inclusão com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Itens Cadastrados com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
        }
        ?>
          <TABLE class="tabDetailView" cellSpacing="0" cellPadding="0" width="100%" border="0">
              <TR>
                <TD class="dataLabel" width="15%"> Data: </TD>
                <TD colspan="5" class=tabDetailViewDF>
									<strong><?php echo DataMySQLRetornar($dados_locacao["data"]) ?></strong>
				  			</TD>
              </TR>
              <TR>
                <TD valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</TD>
                <TD colspan="5" valign="middle" class=tabDetailViewDF>
									<?php echo $dados_locacao["descricao"] ?>
				  			</TD>
              </TR>
<TR>
			             <TD valign="top" class="dataLabel">Tipo de Pessoa:</TD>
			             <TD colspan="5" valign="middle" class=tabDetailViewDF><?php echo $pessoa_tipo ?></TD>
			           </TR>
			           <TR>
			             <TD valign="top" class="dataLabel">Locador:</TD>
			             <TD colspan="5" valign="middle" class=tabDetailViewDF><?php echo $pessoa_nome ?></TD>
			           </TR>
			           <TR>
			             <TD valign="top" class="dataLabel">Descrição:</TD>
			             <TD colspan="5" valign="middle" class=tabDetailViewDF><b><?php echo $dados_locacao["descricao"] ?></b></TD>
			           </TR>
			
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
			             <td width="38%" valign="middle" class="tabDetailViewDF">
									 	 <?php echo DataMySQLRetornar($dados_locacao["devolucao_realizada"]) ?>
									 </td>
			           </tr>
			           <tr>
			             <td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares :</td>
			             <td colspan="5" class="tabDetailViewDF"><?php echo $dados_locacao["observacoes"] ?></td>
			           </tr>			                       
          </table>
													           
          <br/>

          <table cellspacing="0" cellpadding="0" width="520" border="0">
	          <tr>
	            <td width="484">
	              <form id="form" name="cadastro" action="sistema.php?ModuloNome=ItemLocacaoCadastra" method="post" onsubmit="return valida_form()">
				  		</td>
	          </tr>
	          <tr>
		        	<td style="PADDING-BOTTOM: 2px">
		        		<input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Listagem de Itens" />
	            	<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
								<input name="LocacaoId" type="hidden" value="<?php echo $LocacaoId ?>" />
	          	</td>
	          <td width="36" align="right">	  </td>
		       </tr>
         </table>

			   <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  			 <tr>
	    			 <td colspan="15" align="right">
	      			 <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        			 <tr>
	          			 <td colspan="2" align="left" class="listViewPaginationTdS1" style="PADDING-BOTTOM: 2px"><span class="pageNumbers"><strong>Selecione os itens a incluir na locação</strong></span></td>
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
						 <td width="20" class="listViewThS1">
						 	 Un
						 </td>
		  			 <td width="310" class="listViewThS1">
						 	 &nbsp;&nbsp;Descrição do Item
						 </td>
						 <td width="67" class="listViewThS1">						 	 	
								Preço Un.								
						 </td>						 
						 <td class="listViewThS1">
						   Observações
						 </td>
	  			 </tr>

					 <?php
						 //Monta a query para capturar as categorias que existem cadastrados itens
						 $sql_categoria = mysql_query("SELECT ite.id, ite.nome, ite.categoria_id, cat.nome as categoria_nome
															FROM item_evento ite
															LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
															WHERE ite.tipo_produto = '1'
															AND ite.exibir_evento = '1'
															AND ite.ativo = '1' 
															AND ite.empresa_id = $empresaId
															GROUP BY cat.nome
															ORDER BY cat.nome, ite.nome"); 
						 
						 //Cria a variavel zerada para o contador de checkboxes
						 $edtItemChk = 0; 
						 
						 //Percorre o array
					   while ($dados_categoria = mysql_fetch_array($sql_categoria)){
						 
						 ?>
						   
					 <tr height='24'>
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
																ite.valor_locacao,
																cat.nome as categoria_nome
																FROM item_evento ite
																LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
																WHERE ite.tipo_produto = '1'
																AND ite.exibir_evento = '1'
																AND ite.ativo = '1' 
																AND ite.empresa_id = $empresaId
																AND ite.categoria_id = $dados_categoria[categoria_id]
																ORDER BY cat.nome, ite.nome";
							
							 //Executa a query
							 $lista_item = mysql_query($filtra_item);
							 
							 //Cria um contador com o número de contar que a query retornou
							 $nro_item = mysql_num_rows($lista_item);						   
						  
						   //Percorre o array
						   while ($dados_item = mysql_fetch_array($lista_item)){
							 	
								 if ($dados_item["categoria_id"] == 0) {
								 	
								 }
								 //Efetua a pesquisa na base de itens do evento para ver se o item consta como selecionado para o evento
								 $sql_procura_item = "SELECT
																		 quantidade,
																		 valor_venda,
																		 observacoes
																		 FROM locacao_item
																		 WHERE locacao_id = '$LocacaoId'
																		 AND item_id = '$dados_item[id]'";
			
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

					 <tr height="16">
    				 <td>
		  				 <div align="center">
      				 <input name="<?php echo ++$edtItemChk ?>" type="checkbox" value="<?php echo $dados_item[id] ?>" style="border: 0px" title="Clique para marcar ou desmarcar a inclusão deste item na locação" <?php echo $chkItem ?>/>
      				 </div>
    				 </td>
    				 <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
							 <input name="edtQtde<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 46px" maxlength="10" title="Informe a quantidade do item" value="<?php echo $dados_procura_item[quantidade] ?>">
						 </td>
    				 <td bgcolor="#fdfdfd" class="currentTabList">
							 <?php echo $dados_item[unidade] ?>
						 </td>
						 <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
      				 <a title="Clique para editar este item" href="#" onclick="wdCarregarFormulario('ItemAltera.php?Id=<?php echo $dados_item[id] ?>&headers=1','conteudo')"><?php echo $dados_item[nome] ?></a>
    				 </td>
    				 <td bgcolor="#fdfdfd" class="currentTabList">
							<?php
																
									
									//Verifica se já existe um preço de venda cadastrado para o item
									if ($dados_procura_item[valor_venda] > 0) {
										//Caso tenha valor de venda cadastrado mostra o valor do item para este evento
										$preco_venda = str_replace(".",",",$dados_procura_item[valor_venda]);
									} else {
										//Caso não, pega o valor de venda padrão do item no cadastro normal
										$preco_venda = str_replace(".",",",$dados_item[valor_locacao]);
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
    				 <td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="padding-right: 0px">
							 <input name="edtObs<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 300px" maxlength="80" title="Informe as observações sobre o item" value="<?php echo $dados_procura_item[observacoes] ?>">
						 </td>						 
					 </tr>

						 <?php
						 	//Fecha o while
						 	} 
						//Fecha o while da categoria
						}
					   	//Envia com o formulario o total final do contador para efetuar o for depois
						 	?>	
						 <input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>"/>
						 <input name="LocacaoId" type="hidden" value="<?php echo $LocacaoId ?>"/>		
					 </table>           

     </td>
   </tr>
</form>
</table>  	 

</tr>
</table>
