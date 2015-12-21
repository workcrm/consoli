<?php 
###########
## Módulo para Exibição dos pedidos do foto e video
## Criado: 18/11/2013 - Maycon Edinger
## Alterado: 
## Alterações:
###########

header('Content-Type: text/html;  charset=ISO-8859-1',true);
  
//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA)
{
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $HORA = '00:00:00';
  $data_array = split('[- ]',$DATA);
  if ($DATA <> '')
  {
    $ANO = $data_array[0];
    $MES = $data_array[1];
    $DIA = $data_array[2];
    $HORA = $data_array[3];
    return $DIA.'/'.$MES.'/'.$ANO. ' - ' . $HORA;
  }

  else 

  {
    $ANO = 0000;
    $MES = 00;
    $DIA = 00;
    return $DIA.'/'.$MES.'/'.$ANO;
  }

}

//Definicao das variaveis
$PedidoId = $_GET['PedidoId'];

//Recupera dos dados do pedido
$sql_pedido  = "SELECT 
                ped.id, 
                ped.data, 
                ped.hora, 
                ped.data_venda,
                ped.formando_id, 
                ped.usuario_cadastro_id,
                ped.cadastro_timestamp,
                ped.usuario_alteracao_id,
                ped.alteracao_timestamp,
                ped.observacoes,
                eve.id AS evento_id, 
                eve.nome AS evento_nome, 
                formando.nome AS formando_nome, 
                CONCAT(usu_cad.nome, ' ', usu_cad.sobrenome) AS usuario_cadastro_nome,
                CONCAT(usu_alt.nome, ' ', usu_alt.sobrenome) AS usuario_alteracao_nome
                FROM fotovideo_pedido ped 
                LEFT OUTER JOIN eventos_formando formando ON formando.id = ped.formando_id 
                LEFT OUTER JOIN eventos eve ON eve.id = formando.evento_id 
                LEFT OUTER JOIN usuarios usu_cad ON usu_cad.usuario_id = ped.usuario_cadastro_id
                LEFT OUTER JOIN usuarios usu_alt ON usu_alt.usuario_id = ped.usuario_alteracao_id
                WHERE ped.id = $PedidoId";
  
//Executa a query
$resultado = mysql_query($sql_pedido);

//Monta o array dos campos
$dados_pedido = mysql_fetch_array($resultado);

$EventoId = $dados_pedido['evento_id'];

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização do Pedido do Foto e Vídeo</span></td>
        </tr>
        <tr>
          <td>
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>
      <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="200" style="PADDING-BOTTOM: 2px">
                  <input name="btnVoltar" type="button" class="button" title="Retorna para o Gerenciamento de Pedidos do Foto e Vídeo" value="Voltar" onclick="wdCarregarFormulario('ModuloFotoVideo.php?EventoId=<?php echo $EventoId ?>','conteudo')" style="width: 80px" />
                </td>
                <td align="right" style="PADDING-BOTTOM: 2px">
                  <input id="btnRelatorio" name="btnRelatorio" type="button" class="button" title="Emite o relatório dos detalhes do evento" value="Imprimir Pedido" style="width: 100px" onclick="abreJanela('./relatorios/FotoVideoPedidoRelatorioPDF.php?PedidoId=<?php echo $PedidoId ?>');" />
                </td>
              </tr>
            </table>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="130" valign="top" class="dataLabel">Número:</td>
                <td valign="middle" class="tabDetailViewDF"><span style="color: #990000;"><b><?php echo $dados_pedido["id"] ?></b></span></td>
              </tr>
              <tr>
                <td class="dataLabel" width="130">
                  <span class="dataLabel">Formando:</span>             
                </td>
                <td class="tabDetailViewDF">
                  <span style="font-size: 16px; color: #990000"><b><?php echo '(' . $dados_pedido['formando_id'] . ') - ' . $dados_pedido['formando_nome'] ?></b></span>
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="18%">
                  <span class="dataLabel">Evento:</span>             
                </td>
                <td class="tabDetailViewDF">
                  <span style="font-size: 12px;"><b><?php echo '(' . $dados_pedido['evento_id'] . ') - ' . $dados_pedido['evento_nome'] ?></b></span>
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="18%">
                  <span class="dataLabel">Data da Venda:</span>             
                </td>
                <td class="tabDetailViewDF">
                  <span style="font-size: 12px;"><b><?php echo DataMySQLRetornar($dados_pedido['data_venda']) ?></b></span>
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Observacões:</td>
                <td valign="middle" class="tabDetailViewDF"><?php echo nl2br($dados_pedido['observacoes']) ?></td>
              </tr>
            </table>
            <br/>
            <span class="TituloModulo">Assinatura Digital:</span>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">	
              <tr>
                <td valign="top" width="130" class="dataLabel">Data de Cadastro: </td>
                <td class="tabDetailViewDF">
                  <?php 
                    //Exibe o timestamp do cadastro da conta
                    echo TimestampMySQLRetornar($dados_pedido[cadastro_timestamp]) 
                  ?>					
                </td>
                <td class="dataLabel">Usuário:</td>
                <td class="tabDetailViewDF" colspan="3">
                  <?php 
                  
                    //Exibe o nome do operador do cadastro da conta
                    echo $dados_pedido['usuario_cadastro_nome'] 
                  
                  ?>					
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Data de Altera&ccedil;&atilde;o: </td>
                <td class="tabDetailViewDF">
                  <?php 

                    //Verifica se este registro já foi alterado
                    if ($dados_pedido['alteracao_operador_id'] <> 0) 
                    {
                      //Exibe o timestamp da alteração da conta
                      echo TimestampMySQLRetornar($dados_pedido['alteracao_timestamp']);
                    }

                  ?>			 		
                </td>
                <td class="dataLabel">Operador:</td>
                <td class="tabDetailViewDF" colspan="3">
                  <?php 

                    //Verifica se este registro já foi alterado
                    if ($dados_pedido['alteracao_operador_id'] <> 0) 
                    {

                      //Exibe o nome do operador da alteração da conta
                      echo $dados_pedido['usuario_alteracao_nome'];

                    }

                  ?>			 		
                </td>
              </tr>           
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <br/>
            <span class="TituloModulo">Produtos do Pedido:</span>
            <br/>
            <?php
		
              //verifica os produtos
              $sql_consulta = mysql_query("SELECT 
                                          prod.id,
                                          prod.pedido_id,
                                          prod.produto_id,
                                          prod.quantidade_venda,
                                          prod.chk_brinde,
                                          prod.valor_unitario,
                                          prod.obs_cadastro,
                                          produto.nome as produto_nome                          
                                          FROM fotovideo_pedido_produto prod
                                          LEFT OUTER JOIN categoria_fotovideo produto ON produto.id = prod.produto_id                          
                                          WHERE prod.pedido_id = $PedidoId
                                          ORDER BY produto_nome
                                          ");

              //Verifica o numero de registros retornados
              $registros = mysql_num_rows($sql_consulta); 

            ?>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
              <?php
		
                if ($registros > 0) 
                {
                  
                  //Exibe o cabeçalho da tabela
                  echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                          <td style='padding-left: 6px'>Descrição do Produto</td>
                          <td width='50' align='center'>Quant.</td>
                          <td width='80' align='right' style='padding-right: 6px'>Unitário</td>
                          <td width='80' align='right' style='padding-right: 6px'>Total</td>
                        </tr>";
                  
                }
	    	
                //Caso não houverem registros
                else if ($registros == 0) 
                { 

                  //Exibe uma linha dizendo que nao registros
                  echo "<tr height='24'>
                          <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                            <font color='#33485C'><b>Não há produtos associados a este pedido</b></font>
                          </td>
                        </tr>";	
                  
                }     	
	
                //Cria o array e o percorre para montar a listagem dinamicamente
                while ($dados_consulta = mysql_fetch_array($sql_consulta))
                {
		    	
                  ?>
                  <tr valign="middle" height="22">
                    <td valign="middle" bgcolor="#fdfdfd" style="border-top: 1px dotted; padding-left: 6px; border-right: 1px dotted;">
                      <span style="font-size: 12px"><b>
                      <?php 
                        
                        echo $dados_consulta['produto_nome']; 
                      
                        if ($dados_consulta['obs_cadastro'] !='') echo '<br/><i>' . nl2br($dados_consulta['observacoes']) . '</i>';
                        
                      ?>
                      </b></span>
                    </td>
                    <td valign="middle" align="center" bgcolor="#fdfdfd" style="border-top: 1px dotted; border-right: 1px dotted;">
                      <?php echo $dados_consulta['quantidade_venda'] ?>
                    </td>
                    <td valign="middle" align="right" bgcolor="#fdfdfd" style="border-top: 1px dotted; border-right: 1px dotted; padding-right: 4px;">
                      <?php echo number_format($dados_consulta['valor_unitario'],2,',','.') ?>
                    </td>
                    <td valign="middle" align="right" bgcolor="#fdfdfd" style="border-top: 1px dotted; padding-right: 4px;">
                      <?php 
                      
                        $total_produto = $dados_consulta['quantidade_venda'] * $dados_consulta['valor_unitario'];
                        $total_geral = $total_geral + $total_produto;
                        
                        echo number_format($total_produto,2,',','.');
                      
                      ?>
                    </td>
                  </tr>
                  <?php
			
                //Fecha o while
		}
                
              ?>
              <tr height="22">
                <td colspan="3" valign="middle" align="right" bgcolor="#fdfdfd" style="border-top: 1px solid;  border-right: 1px dotted; padding-right: 4px">
                  <b>TOTAL:</b>
                </td>
                <td valign="middle" align="right" bgcolor="#fdfdfd" style="border-top: 1px solid; padding-right: 4px">
                  <b><?php echo number_format($total_geral,2,',','.') ?></b>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <br/>
            <span class="TituloModulo">Vendedores e Comissões:</span>
            <br/>
            <?php
            
              //verifica os vendedores
              $sql_consulta = mysql_query("SELECT 
                                          com.id,
                                          com.pedido_id,
                                          com.vendedor_id,
                                          com.comissao,
                                          com.rateio,
                                          vend.nome as vendedor_nome                        
                                          FROM fotovideo_pedido_comissoes com
                                          LEFT OUTER JOIN fotovideo_vendedores vend ON vend.id = com.vendedor_id                          
                                          WHERE com.pedido_id = $PedidoId
                                          ORDER BY vend.nome
                                          ");

              //Verifica o numero de registros retornados
              $registros = mysql_num_rows($sql_consulta); 
              
            ?>
            <br/>
            <input id="btnGeraContaPagar" name="btnGeraContaPagar" type="button" class="button" title="Gera as contas a pagar das comissões e a receber do formando" value="Gerar Contas a Pagar de Comissões e Receber do Formando" style="width: 320px;" onclick="if (confirm('Confirma a geração das contas a pagar das comissões e receber do formando ?')){wdCarregarFormulario('FotoVideoPedidoGeraContas.php?PedidoId=<?php echo $PedidoId ?>&headers=1','gera_contas')}" />
            <div id="gera_contas"></div>
            <br/>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="margin-top: 4px;">
              <?php
		
                if ($registros > 0) 
                {
                  
                  //Exibe o cabeçalho da tabela
                  echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                          <td style='padding-left: 6px'>Vendedor</td>
                          <td width='50' align='center'>Comissão</td>
                          <td width='80' align='right' style='padding-right: 6px'>Rateio</td>
                          <td width='80' align='right' style='padding-right: 6px'>Valor</td>
                        </tr>";
                  
                }
	    	
                //Caso não houverem registros
                else if ($registros == 0) 
                { 

                  //Exibe uma linha dizendo que nao registros
                  echo "<tr height='24'>
                          <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                            <font color='#33485C'><b>Não há vendedores associados a este pedido</b></font>
                          </td>
                        </tr>";	
                  
                }     	
	
                //Cria o array e o percorre para montar a listagem dinamicamente
                while ($dados_consulta = mysql_fetch_array($sql_consulta))
                {
		    	
                  ?>
                  <tr valign="middle" height="22">
                    <td valign="middle" bgcolor="#fdfdfd" style="border-top: 1px dotted; padding-left: 6px; border-right: 1px dotted;">
                      <span style="font-size: 12px"><b>
                      <?php echo $dados_consulta['vendedor_nome'] ?>
                      </b></span>
                    </td>
                    <td valign="middle" align="center" bgcolor="#fdfdfd" style="border-top: 1px dotted; border-right: 1px dotted;">
                      <?php echo number_format($dados_consulta['comissao'],0) ?>%
                    </td>
                    <td valign="middle" align="right" bgcolor="#fdfdfd" style="border-top: 1px dotted; border-right: 1px dotted; padding-right: 4px;">
                      <?php echo $dados_consulta['rateio'] ?>
                    </td>
                    <td valign="middle" align="right" bgcolor="#fdfdfd" style="border-top: 1px dotted; padding-right: 4px;">
                      <b>
                      <?php 
                      
                        @$total_comissao = ($total_geral * ($dados_consulta['comissao'] / $dados_consulta['rateio'])) / 100;
                        $total_geral_comissao = $total_geral_comissao + $total_comissao;
                        
                        echo number_format($total_comissao,2,',','.');
                      
                      ?>
                      </b>
                    </td>
                  </tr>
                  <?php
			
                //Fecha o while
		}
                
              ?>
              <tr height="22">
                <td colspan="3" valign="middle" align="right" bgcolor="#fdfdfd" style="border-top: 1px solid;  border-right: 1px dotted; padding-right: 4px">
                  <b>TOTAL DE COMISSÕES:</b>
                </td>
                <td valign="middle" align="right" bgcolor="#fdfdfd" style="border-top: 1px solid; padding-right: 4px">
                  <b><?php echo number_format($total_geral_comissao,2,',','.') ?></b>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>