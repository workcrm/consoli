<?php 
###########
## Módulo para Exibição dos dados do pedido do foto e vídeo
## Criado: 14/10/2010 - Maycon Edinger
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
$PedidoId = $_GET["PedidoId"];

//Monta o SQL
$sql = "SELECT 
				ped.id,
				ped.data,
        ped.evento_id,
				ped.formando_id,
        ped.data_entrega,
        ped.fornecedor_id,
        ped.observacoes,
	  	  ped.cadastro_timestamp,
  	  	ped.cadastro_operador_id,
  	  	ped.alteracao_timestamp,
  	  	ped.alteracao_operador_id,
				eve.nome AS evento_nome,
				form.nome AS formando_nome,
				forn.nome AS fornecedor_nome,
        usu_cad.nome as operador_cadastro_nome, 
  	  	usu_cad.sobrenome as operador_cadastro_sobrenome,
  	  	usu_alt.nome as operador_alteracao_nome, 
  	  	usu_alt.sobrenome as operador_alteracao_sobrenome
				FROM pedido_fv ped
				LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
        LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
        LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
        LEFT OUTER JOIN usuarios usu_cad ON ped.cadastro_operador_id = usu_cad.usuario_id 
	  	  LEFT OUTER JOIN usuarios usu_alt ON ped.alteracao_operador_id = usu_alt.usuario_id		  	
		  	WHERE ped.id = '$PedidoId'";
  
//Executa a query
$resultado = mysql_query($sql);

//Monta o array dos campos
$campos = mysql_fetch_array($resultado);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css" />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td>
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Detalhamento do Pedido de Foto e Vídeo</span>
          </td>
			  </tr>
			  <tr>
			    <td>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				  </td>
			  </tr>
			</table>
	
<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td  class="text">
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
          <td width="100" style="PADDING-BOTTOM: 2px"> 												
        		<input name="btnEditar" type="button" class="button" title="Altera este pedido" value="Alterar Pedido" onclick="wdCarregarFormulario('PedidoFVAltera.php?PedidoId=<?php echo $campos[id] ?>&headers=1','conteudo')" />
					</td>
          <td width="330" style="PADDING-BOTTOM: 2px">
            <input class="button" title="Exclui este pedido" value="Excluir Pedido" type="button" name="btExcluir" onclick="if(confirm('Confirma a exclusão deste Pedido do Foto e Vídeo ?')) {wdCarregarFormulario('PedidoFVExclui.php?PedidoId=<?php echo $campos[id] ?>','conteudo')}" style="width: 120px" />
          </td>
          <td align="right" style="PADDING-BOTTOM: 2px">					
						<input class="button" title="Imprime o pedido" name="btnRelatorio" type="button" id="btnRelatorio" value="Imprimir Pedido" style="width:100px" onclick="abreJanela('./relatorios/PedidoDetalheRelatorio.php?PedidoId=<?php echo $campos[id] ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&Ta=<?php echo $nivelAcesso ?>')">
				  </td>
	  		</tr>
    </table>
           
    <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" style="border-bottom: none;">
	      <tr>
	        <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
	          <table cellspacing="0" cellpadding="0" width="100%" border="0">
	            <tr>
	              <td class="tabDetailViewDL" style="TEXT-ALIGN: left">
									<img src="image/bt_cadastro.gif" width="16" height="15"/> Caso desejar alterar este Pedido, clique em [Alterar Pedido]. Para excluir, clique em [Excluir Pedido]								
								</td>
		     			</tr>
		        </table>					
					</td>
	      </tr>
        </table>
       <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" >

         <tr>
           <td width="140" class="dataLabel" >
             <span class="dataLabel">Número:</span>					 
					 </td>
           <td class="tabDetailViewDF">               
             <span class="TituloModulo" style="color: #990000;"><?php echo $campos[id] ?></span>														 					
           </td>
         </tr>
         <tr>
           <td class="dataLabel">Data de Emissão:</td>
           <td valign="middle" class="tabDetailViewDF">
							<b><?php echo DataMySQLRetornar($campos[data]) ?></b>				 
					 </td>					 
         </tr>
       </table>
       <br/>
       <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
         <tr>
           <td width="140" class="dataLabel">Fornecedor:</td>
           <td valign="middle" class="tabDetailViewDF">
							<?php echo $campos[fornecedor_nome] . " (" . $campos[fornecedor_id] . ")" ?>				  
					 </td>           
         </tr>         
      </table>
      <br/>
      <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
         <tr>
           <td width="140" class="dataLabel">Evento:</td>
           <td valign="middle" class="tabDetailViewDF">
							<?php echo $campos[evento_nome] . " (" . $campos[evento_id] . ")" ?>					 
					 </td>
        </tr>
        <tr>
          <td width="140" class="dataLabel">
						<span class="dataLabel">Formando:</span>					
					</td>
          <td class="tabDetailViewDF">
						<?php echo $campos[formando_nome] . " (" . $campos[formando_id] . ")" ?>						
					</td>
        </tr>          
        </table>        
  
  
  
        <?php 
        
        //******** PRODUTOS DO PEDIDO ************
        ?>
        <br/>
  			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td height="30">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						  <tr>   
								<td><span class="TituloModulo">Produtos do Pedido</span></td>
						  </tr>
              <tr>
			          <td>
				          <img src="image/bt_espacohoriz.gif" width="100%" height="12" />
				        </td>
			        </tr>
						</table>
					</td>
				</tr>
			</table>

    	<?php
	         
			//verifica os vales deste colaborador e exibe na tela
			$sql_consulta = mysql_query("SELECT 
                                  prod.id,
                                  prod.quantidade,
                                  prod.observacoes,
                                  produto.nome as produto_nome                          
                                  FROM pedido_fv_produtos prod
                                  LEFT OUTER JOIN categoria_fotovideo produto ON produto.id = prod.produto_id                          
                                  WHERE prod.pedido_id = $PedidoId
                                  ORDER BY produto_nome
																	");
			
			//Verifica o numero de registros retornados
			$registros = mysql_num_rows($sql_consulta); 
		   
		  ?> 
			<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
		
			<?php
		
			  if ($registros > 0) 
        { 
          
          //Caso houverem registros
        	//Exibe o cabeçalho da tabela
  				echo "
          <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
  					<td align='center' width='50'>Quant.</td>
            <td>Descrição do Produto</td>   		      
   		      <td width='350'>Observações</td>	      
          </tr>
  	    	";
        
        }
	    	
			  //Caso não houverem registros
			  if ($registros == 0) 
        { 
		
  			  //Exibe uma linha dizendo que nao registros
  			  echo "
  			  <tr height='24'>
  		      <td colspan='4' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
  				  	<font color='#33485C'><b>Não há produtos cadastrados neste pedido !</b></font>
  					</td>
  			  </tr>";	  
			  
        }     		
				
				//Cria o array e o percorre para montar a listagem dinamicamente
		    while ($dados_consulta = mysql_fetch_array($sql_consulta)){
		    	 
		    	
			?>
		
      <tr height="24" valign="middle">
				<td align="center" style="border-top: 1px dotted;">
          <span style="font-size: 12px;"><?php echo $dados_consulta[quantidade] ?></span>
        </td>
        <td valign="middle" bgcolor="#fdfdfd" style="border-top: 1px dotted; border-left: 1px dotted; padding-left: 4px;">
          <span style="font-size: 12px;"><?php echo $dados_consulta[produto_nome] ?></span>       
				</td>
        <td valign="middle" bgcolor="#fdfdfd" style="border-top: 1px dotted; border-left: 1px dotted; padding-left: 4px;">
          <?php echo nl2br($dados_consulta["observacoes"]) ?>&nbsp;
				</td>														
  	  </tr>
			
			<?php      
			
  			//Fecha o while
  			}
			?>
			</table>      
      <br/>
        <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
	        <tr>
	          <td width="140" valign="top" class="dataLabel">Observações:</td>
	          <td class="tabDetailViewDF">
							<?php 
								//Exibe o timestamp do cadastro da conta
								echo nl2br($campos[observacoes]) 
							?>					
						</td>	          
					</tr>	        
	  		</table>
        
        <br/>
        <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
	        <tr>
	          <td width="140" valign="top" class="dataLabel">Data de Cadastro: </td>
	          <td class="tabDetailViewDF">
							<?php 
								//Exibe o timestamp do cadastro da conta
								echo TimestampMySQLRetornar($campos[cadastro_timestamp]) 
							?>					
						</td>
	          <td class="dataLabel">Operador:</td>
	          <td colspan="2" class="tabDetailViewDF">
							<?php 
								//Exibe o nome do operador do cadastro da conta
								echo $campos[operador_cadastro_nome] . " " . $campos[operador_cadastro_sobrenome] 
							?>					 
						</td>
					</tr>
	        <tr>
	          <td valign="top" class="dataLabel">Data de Altera&ccedil;&atilde;o: </td>
	          <td class="tabDetailViewDF">
			  	 		<?php 
					 			//Verifica se este registro já foi alterado
					 			if ($campos[alteracao_operador_id] <> 0) 
                 {
									//Exibe o timestamp da alteração da conta
					   			echo TimestampMySQLRetornar($campos[alteracao_timestamp]);
					 			}
					 		?>			 		
						</td>
	          <td class="dataLabel">Operador:</td>
	          <td colspan="2" class="tabDetailViewDF">
					 		<?php 
					 			//Verifica se este registro já foi alterado
					 			if ($campos[alteracao_operador_id] <> 0) 
                 {
									//Exibe o nome do operador da alteração da conta
					   			echo $campos[operador_alteracao_nome] . " " . $campos[operador_alteracao_sobrenome];
					 			}
					 		?>			 		 
						</td>
					</tr>
	  		</table>
        <br/>	
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
