<?php 
###########
## Módulo para gerenciamento e listagem de materiais dos eventos
## Criado: 26/09/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

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

//Pega o valor da cliente a exibir
$EventoId = $_GET["EventoId"];


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
			    <td width='440'><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização do Evento</span></td>
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
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
      <TBODY>
        <tr>
          <td width="95" style="PADDING-BOTTOM: 2px">
						<?php
							//Verifica o nível de acesso do usuário
							if ($nivelAcesso > 1) {
						?> 
 	        		<input name='btnEditarConta' type='button' class=button accessKey='E' title="Retorna aos itens do Evento [Alt+E]" value='Retornar aos Itens' onClick="wdCarregarFormulario('ItemEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">
 	        	<?php
 	        		}
 	        	?>
          </td>

	      	<td width="90" style="PADDING-BOTTOM: 2px">
						&nbsp;
          </TD>
          <TD align="right" style="PADDING-BOTTOM: 2px">
						<?php
							//Verifica o nível de acesso do usuário
							if ($nivelAcesso >= 3) {
						?> 
						<input class="button" title="Emite o relatório dos detalhes do evento" name='btnRelatorio' type='button' id='btnRelatorio' value='Emitir Relatório' style="width:100px" onclick="wdCarregarFormulario('EventoRelatorio.php?EventoId=<?php echo $dados_evento[id] ?>','conteudo')">
						<?php
						  //Fecha o if
						  }
						?>
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
			<?php /*EXIBE OS ITENS CADASTRADOS PARA ESTE EVENTO*/ ?>
			
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Materiais do Evento</span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="5">
				</td>
			</table>

			<?php
				//Verifica o nível de acesso do usuário
				if ($nivelAcesso > 1) {
			?> 						
			<table width='100%' cellpadding='0' cellspacing='0' border='0' >
  			<tr valign='middle'>
					<td style="padding-bottom: 5px">
             Selecione o item do evento para exibir/alterar a quantidade de materiais utilizados clicando no botão <img src='./image/grid_composicao.gif'>
          </td>
    		</tr>
    	</table>		
			<?php
			//Fecha o if do nivel de acesso
			}
		
			//verifica todos os itens cadastrados na base para montar o primeiro array (para comparar com os que estão inclusos no evento
			//Monta a query de filtragem dos itens
		  $filtra_item = "SELECT
										  evento_id														 
										  FROM eventos_item
										  WHERE evento_id = '$EventoId'";
		
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
		        <td width='52' align='right' class='listViewThS1' style='padding-right: 5px'>Qtde</td>
		        <td width='15' class='listViewThS1'>Un</td>
		        <td width='15' class='listViewThS1'>&nbsp;</td>
		        <td width='355' class='listViewThS1'>&nbsp;&nbsp;Descrição do Item</td>
		        ";
						//Verifica o nível de acesso do usuário
						if ($nivelAcesso >= 5) {
		         echo "<td width='70' align='right' class='listViewThS1' style='padding-right: 8px'>Preço Un.</td>";
		        }

		    echo "<td width='285' class='listViewThS1'>Observações</td>
		        <td class='listViewThS1'>&nbsp;</td> 
			  </tr>"; 
			  }
		
			  if ($registros == 0) { //Caso não houverem registros
		
			  //Exibe uma linha dizendo que nao há regitros
			  echo "
			  <tr height='24'>
		      <td colspan='5' scope='row' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
				  	<font color='#33485C'><strong>Não há itens cadastrados para este evento</strong></font>
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
														eve.valor_venda
														FROM item_evento ite
														LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
														INNER JOIN eventos_item eve ON eve.item_id = ite.id
														WHERE eve.evento_id = '$EventoId'											
														GROUP BY cat.nome
														ORDER BY cat.nome");
			
				
				//Percorre o array das funcoes
				while ($dados_categoria = mysql_fetch_array($sql_categoria)){				
				
				//Fecha o php para imprimir o texto da categoria
				?>
						   
					 <tr height='22'>
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
														eve.quantidade,
														eve.valor_venda,
														eve.observacoes
														FROM item_evento ite
														LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
														INNER JOIN eventos_item eve ON eve.item_id = ite.id
														WHERE eve.evento_id = '$EventoId'
														AND ite.categoria_id = '$dados_categoria[categoria_id]'
														ORDER BY cat.nome, ite.nome";
					
					//Executa a query
					$lista_item = mysql_query($filtra_item);
			   
					//Percorre o array
					while ($dados_item = mysql_fetch_array($lista_item)){
				  
							//Define a variável do valor total do item
							$total_item = $dados_item[quantidade] * $dados_item[valor_venda];
							
							//Ajusta o total do evento
							$total_evento = $total_evento + $total_item;
																			
					?>
		
				 <tr valign='middle'>
					 <td valign='middle' align='right' bgcolor='#fdfdfd' class='currentTabList' style='padding-right: 5px'>
	  				 <?php echo $dados_item[quantidade]	?>
					 </td>
					 <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
	  				 <?php echo $dados_item[unidade] ?>
					 </td>
					 <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-top: 4px'>
	  				 <img src='./image/grid_composicao.gif' alt='Clique para gerenciar os materiais deste item do evento' style="cursor: pointer" onClick="wdCarregarFormulario('MaterialEventoCadastra.php?ItemId=<?php echo $dados_item[id] ?>&EventoId=<?php echo $EventoId ?>&headers=1','conteudo')">
					 </td>					 					 
					 <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-top: 1px; padding-bottom: 2px'>
	  				 <?php echo $dados_item[nome] ?>
					 </td>
					 <?php
					 	 //Verifica o nível de acesso
						 if ($nivelAcesso >= 5) {
					 ?>
					 <td valign='middle' align='right' bgcolor='#fdfdfd' class='currentTabList' style='padding-right: 8px'>
	  				 <?php echo "R$ " . number_format($dados_item[valor_venda], 2, ",", ".") ?>
					 </td>					 
					 <?php
					 	 //fecha o if do nivel de acesso
						 }
					 ?>
					 <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
						 <?php echo $dados_item[observacoes] ?>
					 </td>

					 <td valign='middle' style="padding-right: 6px">
						&nbsp;
           </td>
				 </tr>			 	
			
			<?php
			//Fecha o while dos itens
			}
			
			//Fecha o while das categorias
			}
			?>
			</table>
			</td>
		</tr>


	</table>  	 
</td>
</tr>



</table>
</td>
