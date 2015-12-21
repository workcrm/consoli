<?php
###########
## Módulo para Listagem da vales emitidos por período
## Criado: 19/03/2010 - Maycon Edinger
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

//Captura o evento informado
$edtDataIni = DataMySQLInserir($_GET["DataIni"]);
$edtDataFim = DataMySQLInserir($_GET["DataFim"]);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css" />

    	<?php
		
			//verifica os vales deste colaborador e exibe na tela
			$sql_consulta = mysql_query("SELECT 
                                  vale.data,
                                  vale.id,
                                  vale.valor,
								  vale.colaborador_id,
                                  vale.observacoes,
                                  vale.data_devolucao,
                                  col.nome as colaborador_nome
                                  FROM vales vale
                                  LEFT OUTER JOIN colaboradores col ON col.id = vale.colaborador_id																	
		                              WHERE vale.data >= '$edtDataIni' AND vale.data <= '$edtDataFim'
									  AND vale.colaborador_id <> 0
		                              ORDER by vale.data, colaborador_nome");                                   
			
			//Verifica o numero de registros retornados
			$registros = mysql_num_rows($sql_consulta); 
		   
		  ?>
			<div id="88">   
			<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
		
			<?php
		
			  if ($registros > 0) { //Caso houverem registros
      	//Exibe o cabeçalho da tabela
				echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>					
          <td width='80' align='center'>Data Emissão</td>
          <td width='480' align='left'>Colaborador</td>
 		      <td width='120' align='right' style='padding-right: 10px' >Valor</td> 		      
          <td width='80' align='right' style='padding-right: 8px'>Devolução</td>	      
        </tr>
	    	";}
	    	
			  //Caso não houverem registros
			  if ($registros == 0) { 
		
			  //Exibe uma linha dizendo que nao registros
			  echo "
			  <tr height='24'>
		      <td colspan='4' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
				  	<font color='#33485C'><b>Não há vales emitidos para o período informado !</b></font>
					</td>
			  </tr>	
			  ";	  
			  }     	
	
				//Cria a variável do total de vales
				$total_vales = 0;
				
				//Cria o array e o percorre para montar a listagem dinamicamente
		    while ($dados_consulta = mysql_fetch_array($sql_consulta)){
		    	 
		    	
			?>
		
      <tr height="24" valign="middle">				
        <td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="padding-bottom: 1px">
          &nbsp;<font color="#CC3300" size="2" face="Tahoma"><a title="Clique para alterar os dados deste vale" href="#" onclick="wdCarregarFormulario('ValeAltera.php?ValeId=<?php echo $dados_consulta[id] ?>&headers=1','conteudo')"><?php echo DataMySQLRetornar($dados_consulta["data"]); ?></a></font>        
				</td>
        <td valign="middle" bgcolor="#fdfdfd">
          <span style="color: #990000; font-size: 12px"><b><?php echo '(' . $dados_consulta["colaborador_id"] . ') - ' . $dados_consulta["colaborador_nome"] ?></b></span>
				</td>
        <td align="right" style="padding-right: 10px">
          <?php echo "R$ " . number_format($dados_consulta[valor], 2, ",", ".") ?>
				</td>        
				<td valign="middle" bgcolor="#fdfdfd" align="right" style="padding-right: 8px">
          <?php 
          
            if ($dados_consulta["data_devolucao"] != "0000-00-00"){
            
              echo DataMySQLRetornar($dados_consulta["data_devolucao"]);
               
            }
              
          ?>
				</td>											
  	  </tr>
			
			<?php
      
      $total_vales = $total_vales + $dados_consulta[valor];
			
			//Fecha o while
			}
			?>
			</table>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-top: 0px">
				<tr>
					<td height="26">
						<span style="font-size: 12px">
						<?php 

								echo "&nbsp;&nbsp;Valor total de vales:    <b>R$ " . number_format($total_vales, 2, ",", ".") . "</b>"; 
							
							?>
						</span>
					</td>
				</tr>
			</table>
			</div>	
			</td>
		</tr>

	</table> 	
	 </td>
	</tr>
</table>