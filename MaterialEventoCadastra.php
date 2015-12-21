<?php 
###########
## Módulo para cadastro de materiais dos eventos
## Criado: 01/10/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
};

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

//Captura as variáveis
//Recupera o id do evento
if($_POST) {
  $EventoId = $_POST["EventoId"]; 
} else {
  $EventoId = $_GET["EventoId"]; 
}

//Recupera o id do item da árvore
if($_POST) {
  $ItemId = $_POST["ItemId"]; 
} else {
  $ItemId = $_GET["ItemId"]; 
}

//Recupera dos dados do evento
$sql_evento = "SELECT 
							eve.id,
							eve.nome,
							eve.descricao,
							eve.status,
							eve.cliente_id,
							eve.responsavel,
							eve.data_realizacao,
							eve.hora_realizacao,
							eve.duracao,
							eve.observacoes,
							cli.id as cliente_id,
							cli.nome as cliente_nome,
							cli.endereco as cliente_endereco,
							cli.complemento as cliente_complemento,
							cli.bairro as cliente_bairro,
							cli.cidade_id,
							cli.cep as cliente_cep,
							cli.uf as cliente_uf,
							cli.telefone as cliente_telefone,
							cli.fax as cliente_fax,
							cli.celular as cliente_celular,
							cli.email as cliente_email,
							cid.nome as cliente_cidade,
							gru.nome as grupo_nome
							FROM eventos eve 
							INNER JOIN clientes cli ON cli.id = eve.cliente_id
							LEFT OUTER JOIN cidades cid ON cid.id = cli.cidade_id
							LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id							
							WHERE eve.id = '$EventoId'";
  
//Executa a query
$resultado = mysql_query($sql_evento);

//Monta o array dos campos
$dados_evento = mysql_fetch_array($resultado);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) {
  case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Realizado"; break;
}    

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Árvore de Materiais dos Itens Evento</span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
				</table>
	
<table id="2" width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='100%' class="text">
      
      <?php
					//Recupera os valores vindos do formulário e armazena nas variaveis
          if($_POST['Submit']){

						$edtTotalChk = $_POST['edtTotalChk'];
	          $edtEventoId = $_POST["EventoId"];
	          $edtItemId = $_POST["ItemId"];
							
					  //Primeiro apaga todos os materiais que já existem na base de itens do evento
					  $sql_exclui_item = "DELETE FROM eventos_item_composicao WHERE evento_id = '$edtEventoId' AND item_id = '$edtItemId'";
	
					  //Executa a query
					  $query_exclui_item = mysql_query($sql_exclui_item);
	
	
						//Define o valor inicial para efetuar o FOR
						for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++){
	   				
							//Monta a variável com o nome dos campos
							$texto_material = "edtMaterial" . $contador_for;
							$texto_qtde = "edtQtde" . $contador_for;							
																	
							$sql_insere_item = "INSERT INTO eventos_item_composicao (
										 						 evento_id, 
																 item_id,
																 material_id,
																 quantidade
																 ) VALUES (
																 '$EventoId',
																 '$ItemId', 
																 '$_POST[$texto_material]',
																 '$_POST[$texto_qtde]'
																 )";																		
							
							//Insere os registros na tabela de eventos_itens
 							mysql_query($sql_insere_item);						
															
						//Fecha o FOR
	  				}
								
						//Exibe a mensagem de inclusão com sucesso
        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Arvore de Materiais do Item Cadastrada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";	
	        	
        }
        ?>
      
      
			
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
      <TBODY>
        <tr>
          <td width="95" style="PADDING-BOTTOM: 2px">
						<?php
							//Verifica o nível de acesso do usuário
							if ($nivelAcesso > 1) {
						?> 
 	        		<input name='btnEditarConta' type='button' class=button accessKey='E' title="Retorna aos itens do Evento [Alt+E]" value='Retornar aos Itens' onClick="wdCarregarFormulario('MaterialEventoGerencia.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">
 	        	<?php
 	        		}
 	        	?>
          </td>

	      	<td width="90" style="PADDING-BOTTOM: 2px">
						&nbsp;
          </TD>
          <TD align="right" style="PADDING-BOTTOM: 2px">

				 </TD>
	  		</TR>
    	</TBODY>
    </TABLE>
           
    	<TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
           <TR>
             <TD class='dataLabel' width='15%'>
               <span class="dataLabel">Nome do Evento :</span>             
						 </TD>
             <TD colspan='5' class=tabDetailViewDF>
							 <strong><?php echo $dados_evento["nome"] ?></strong>
						 </TD>
           </TR>
           <TR>
             <TD valign="top" class='dataLabel'>Descri&ccedil;&atilde;o:</TD>
             <TD colspan="5" valign="middle" class=tabDetailViewDF><?php echo $dados_evento["descricao"] ?></TD>
           </TR>
           <TR>
             <TD valign="top" class='dataLabel'>Status:</TD>
             <TD colspan="5" valign="middle" class=tabDetailViewDF><?php echo $desc_status ?></TD>
           </TR>
           <TR>
             <TD valign="top" class='dataLabel'>Cliente:</TD>
             <TD colspan="5" valign="middle" class=tabDetailViewDF>
						 		<a href="#" onClick="wdCarregarFormulario('ClienteExibe.php?ClienteId=<?php echo $dados_evento[cliente_id] ?>','conteudo')" title="Clique para exibir os detalhes deste Cliente"><?php echo $dados_evento["cliente_nome"] ?></a>
								</br>
						  	<span style="font-size: 9px">
									<?php echo $dados_evento[cliente_endereco] . " - " . $dados_evento[cliente_complemento] ?>
						  		<br>
						  		<?php echo $dados_evento[cliente_bairro] . " - " . $dados_evento[cliente_cep] . " - " . $dados_evento[cliente_cidade] . "/" . $dados_evento[cliente_uf]?>
						  		<br>
						  		<?php echo "Fone: " . $dados_evento[cliente_telefone] . " - Fax: " . $dados_evento[cliente_fax] . " - Celular: " . $dados_evento[cliente_celular] ?>
									<br>
									<?php echo "email: <a href='mailto:" . $dados_evento[cliente_email] . "' title='Clique para enviar um email para o endereço'>$dados_evento[cliente_email]</a>" ?>
						  	</span>						 
						 </TD>
           </TR>

           <TR>
             <TD class='dataLabel'>Grupo:</TD>
             <TD colspan="5" valign="middle" class=tabDetailViewDF><?php echo $dados_evento["grupo_nome"] ?></TD>
           </TR>
           
           <TR>
             <TD class='dataLabel'>Responsável:</TD>
             <TD colspan="5" valign="middle" class=tabDetailViewDF><?php echo $dados_evento["responsavel"] ?></TD>
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
             <TD valign="top" class=dataLabel>Informa&ccedil;&otilde;es Complementares :</TD>
             <TD colspan="5" class=tabDetailViewDF><?php echo $dados_evento["observacoes"] ?></TD>
           </TR>
	   	  </TABLE>
			</td>
		</tr>

		<tr>
		  <td>
			<br>			
		
		
			<?php					
			//Monta a query de filtragem dos item da árvore
		  $filtra_item_arvore = mysql_query("SELECT
													  nome
													  FROM item_evento
													  WHERE id = '$ItemId'");
										  
			$dados_item_arvore = mysql_fetch_array($filtra_item_arvore);
			
										  
			//Monta a query de filtragem para verificar se o item possui alguma árvore de material cadastrada
		  $filtra_arvore = "SELECT
										  	comp.material_id,
										  	mat.nome as material_nome,
										  	mat.unidade
										  	FROM item_evento_composicao comp
										  	INNER JOIN item_evento mat ON mat.id = comp.material_id
										  	WHERE comp.item_id = '$ItemId'
												ORDER BY material_nome";
		
		  //Executa a query
		  $lista_arvore = mysql_query($filtra_arvore);
		 
		  //Cria um contador com o número de registros que a query retornou
		  $registros = mysql_num_rows($lista_arvore);
		   
		  ?>

			<span class='TituloModulo'>Materiais que compõe o item: <?php echo $dados_item_arvore[nome] ?></span>
			<FORM id='form' name='cadastro' action='sistema.php?ModuloNome=MaterialEventoCadastra' method='post' onSubmit='return valida_form()'>		
		  <TABLE cellSpacing='0' cellPadding='0' border='0'>
        <tr>
        	<TD style="PADDING-BOTTOM: 2px">
						<?php
							//Se tiver arvore, então mostra o botão de salvar árvore
        			if ($registros > 0) {
        		?>
						<INPUT name='Submit' type='submit' class=button accessKey='S' title="Salva os Itens do Evento [Alt+S]" value='Salvar Árvore de Materiais'>
						<?php
							}
						?>
						<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
        	</TD>
        <TD width="36" align=right>	  </TD>
       </TR>
     </TABLE>
			   
			<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
		
			<?php

			  if ($registros == 0) { //Caso não houverem registros
		
			  //Exibe uma linha dizendo que nao há regitros
			  echo "
			  <tr height='24'>
		      <td colspan='5' scope='row' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
				  	<font color='#33485C'><strong>Não há árvore de composição de produto cadastrada para este item !</strong></font>
					</td>
			  </tr>	
			  ";	  
			  } else {
						
			  echo "
			  <tr height='20'>
		        <td width='52' class='listViewThS1' style='padding-left: 10px'>Qtde</td>
		        <td width='20' class='listViewThS1'>Un</td>
		        <td width='710' class='listViewThS1'>&nbsp;&nbsp;Descrição do Material</td>		      
			  </tr>"; 
			  
			 	//Cria a variavel zerada para o contador de checkboxes
			 	$edtItemChk = 0; 
																						   
				//Percorre o array
				while ($dados_arvore = mysql_fetch_array($lista_arvore)){				  			
				
				//Verifica se o material já tinha um valor cadastrado no banco de materiais do evento (está alterando)
				$sql_material_cadastrado = "SELECT quantidade 
																		FROM eventos_item_composicao 
																		WHERE evento_id = '$EventoId'
																		AND item_id = '$ItemId'
																		AND material_id = '$dados_arvore[material_id]'";
				
				//Executa a query
				$query_material_cadastrado = mysql_query($sql_material_cadastrado);
				
				//Cria a variável com o numero de registros para ver se tem o material no evento
				$tem_material = mysql_num_rows($query_material_cadastrado);
				
				//Caso tenha o material já cadastrado para o evento
				if ($tem_material > 0){
					
					//Monta o array dos dados
					$dados_material_evento = mysql_fetch_array($query_material_cadastrado);
					
					//Busca o valor e armazena na variável
					$valor_material = $dados_material_evento[quantidade];

				//Caso não tenha o material no evento
				} else {
				
					//Busca a quantidade básica do material do cadastro de árvore de material
					$sql_material_arvore = "SELECT quantidade 
																	FROM item_evento_composicao 
																	WHERE item_id = '$ItemId'
																	AND material_id = '$dados_arvore[material_id]'"; 
					
					//Executa a query												
					$query_material_arvore = mysql_query($sql_material_arvore);

					//Monta o array dos dados
					$dados_material_arvore = mysql_fetch_array($query_material_arvore);
					
					//Busca o valor e armazena na variável
					$valor_material = $dados_material_arvore[quantidade];					
					
				}
				
																			
				?>
				 <input name="edtMaterial<?php echo ++$edtItemChk ?>" type="hidden" value="<?php echo $dados_arvore[material_id] ?>"></input>
				 
				 <tr valign='middle'>    			 
					 <td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-left:10px; padding-right: 5px'>
	  				 <input name="edtQtde<?php echo $edtItemChk ?>" type="text" class='datafield' style="width: 46px" maxlength="10" title="Informe a quantidade do material" value="<?php echo $valor_material ?>" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
					 </td>
					 <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
	  				 <?php echo $dados_arvore[unidade] ?>
					 </td>					 					 
					 <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
						 <?php echo $dados_arvore[material_nome] ?>
					 </td>
				 </tr>			 	
			
			<?php
			//Fecha o while dos materiais
			}
			
			?>
			<input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>"></input>
			<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>"></input>
			<input name="ItemId" type="hidden" value="<?php echo $ItemId ?>"></input>

			<?php
			//Fecha o if de registros
			}
						
			?>
			</table>
			
			</form>
			</td>
		</tr>


	</table>  	 
</td>
</tr>



</table>
