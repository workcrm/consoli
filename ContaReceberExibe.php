<?php 
###########
## Módulo para Exibição dos dados das contas a receber
## Criado: 17/05/2007 - Maycon Edinger
## Alterado: 11/07/2007 - Maycon Edinger
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

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

//Recupera o id da conta a exeibir
$ContaId = $_GET['ContaId'];

//Monta o sql para recuperar os dados da conta
$sql = "SELECT 
        con.id,
        con.data,
        con.tipo_pessoa,
        con.pessoa_id,
        con.grupo_conta_id,
        con.subgrupo_conta_id,
        con.evento_id,
        con.formando_id,
        con.descricao,
        con.nro_documento,
        con.condicao_pgto_id,
        con.valor_original,
        con.valor,
        con.valor_boleto,
        con.taxa_multa,
        con.taxa_juros,
        con.data_vencimento,
        con.situacao,
        con.data_recebimento,
        con.tipo_recebimento,
        con.cheque_id,
        con.valor_recebido,
        con.observacoes,
        con.cadastro_timestamp,
        con.cadastro_operador_id,
        con.alteracao_timestamp,
        con.alteracao_operador_id,
        con.boleto_id,
        usu_cad.nome as operador_cadastro_nome, 
        usu_cad.sobrenome as operador_cadastro_sobrenome,
        usu_alt.nome as operador_alteracao_nome, 
        usu_alt.sobrenome as operador_alteracao_sobrenome,
        cat.nome as categoria_nome,
        gru.nome as grupo_nome,
        sub.nome as subgrupo_nome,
        cond.nome as condicao_pgto_nome,
        evento.nome as evento_nome,
        formando.nome as formando_nome

        FROM contas_receber con
        LEFT OUTER JOIN usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
        LEFT OUTER JOIN usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
        LEFT OUTER JOIN categoria_conta cat ON con.categoria_id = cat.id 
        LEFT OUTER JOIN grupo_conta gru ON con.grupo_conta_id = gru.id 							 
        LEFT OUTER JOIN subgrupo_conta sub ON con.subgrupo_conta_id = sub.id 							 
        LEFT OUTER JOIN condicao_pgto cond ON con.condicao_pgto_id = cond.id 							 
        LEFT OUTER JOIN eventos evento ON con.evento_id = evento.id 
        LEFT OUTER JOIN eventos_formando formando ON con.formando_id = formando.id							 
        WHERE con.id = $ContaId";	
				
			   
//Executa a query
$resultado = mysql_query($sql);

//Monta o array dos dados
$campos = mysql_fetch_array($resultado);

//Efetua o switch para o campo de situacao
switch ($campos['situacao']) 
{
  case 1: $desc_situacao = "<span style='color: #990000'><strong>Em aberto</strong></span>"; break;
  case 2: $desc_situacao = "<span style='color: blue'><strong>Recebida</strong></span>"; break;
}


//Caso a conta já tenha um valor recebido mas ainda está em aberta, então ela possui um recebimento parcial
if ($campos['valor_recebido'] > 0 AND $campos['situacao'] == 1)
{
 
  $desc_situacao = "<span style='color: #018B0F'><strong>Recebimento Parcial</strong></span>"; 
  
}

//Efetua o switch para o campo tipo de pessoa
switch ($campos[tipo_pessoa]) 
{
  case 1: 
    $desc_pessoa = 'Cliente:'; 
    $busca_pessoa = mysql_query("SELECT id, nome FROM clientes WHERE id = '$campos[pessoa_id]'");
    $dados_pessoa = mysql_fetch_array($busca_pessoa);
    $id_pessoa = $dados_pessoa[id];
    $nome_pessoa = $dados_pessoa[nome];
  break;
  case 2: 
    $desc_pessoa = 'Fornecedor:'; 
    $busca_pessoa = mysql_query("SELECT id, nome FROM fornecedores WHERE id = '$campos[pessoa_id]'");
    $dados_pessoa = mysql_fetch_array($busca_pessoa);
    $id_pessoa = $dados_pessoa[id];
    $nome_pessoa = $dados_pessoa[nome];	
  break;
  case 3: 
    $desc_pessoa = 'Colaborador:'; 
    $busca_pessoa = mysql_query("SELECT id, nome FROM colaboradores WHERE id = '$campos[pessoa_id]'");
    $dados_pessoa = mysql_fetch_array($busca_pessoa);
    $id_pessoa = $dados_pessoa[id];
    $nome_pessoa = $dados_pessoa[nome];	
  break;
  case 4: 
    $desc_pessoa = 'Formando:'; 
    $busca_pessoa = mysql_query("SELECT id, nome FROM eventos_formando WHERE id = '$campos[pessoa_id]'");
    $dados_pessoa = mysql_fetch_array($busca_pessoa);
    $id_pessoa = $dados_pessoa[id];
    $nome_pessoa = $dados_pessoa[nome];	
  break;	
}    

//Efetua o switch para o campo tipo de pagamento
switch ($campos[tipo_pagamento]) 
{
  case 1: $desc_pago = 'Dinheiro'; break;
  case 2: $desc_pago = 'Cheque - Nº: ' . $campos[cheque_numero]; break;
}
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Visualização da Conta a Receber</span></td>
        </tr>
        <tr>
          <td colspan="5">
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>

      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="88" style="PADDING-BOTTOM: 2px">
                  <input name="btnEditarConta" type="button" class="button" title="Edita esta Conta" value="Editar Conta" onclick="wdCarregarFormulario('ContaReceberAltera.php?Id=<?php echo $campos[id] ?>&headers=1','conteudo')" />
                </td>
                <td style="PADDING-BOTTOM: 2px">
                  <input class="button" title="Exclui esta conta a Receber" value="Excluir esta Conta a Receber" type="button" name="btExcluir" onclick="if(confirm('Confirma a exclusão desta Conta a Receber ?\n\nCaso tenha algum serviço tercerizado vinculado a esta conta a receber, o mesmo será excluída automaticamento do evento.')) {wdCarregarFormulario('ContaReceberExclui.php?ContaId=<?php echo $campos[id] ?>','conteudo')}" style="width: 150px" />
                </td>
                <td width="90" align="right">	  </td>
              </tr>
            </table>	

            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colSpan="22">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td width="450" class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Caso desejar alterar esta conta a receber, clique em [Editar Conta] - [<?php echo $campos[id] ?>]</td>
                      <td align="right" style="padding-right: 10px;" class="tabDetailViewDL">
                        <span style="font-size: 16px; color: #990000"><strong><?php echo $desc_situacao ?></strong></span>
                      </td>
                    </tr>
                  </table>             
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">
                  <span class="dataLabel">Data:</span>             
                </td>
                <td colspan="2" class="tabDetailViewDF">
                  <?php echo DataMySQLRetornar($campos['data']) ?>
                </td>
                <td class="dataLabel">
                  <span class="dataLabel">ID:</span>             
                </td>
                <td class="tabDetailViewDF" align="center">
                  <span style="color: #990000"><b><?php echo $campos['id'] ?></b></span>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Descrição:</td>
                <td colspan="4" valign="middle" class="tabDetailViewDF">
                  <b><?php echo $campos['descricao'] ?></b>
                </td>
              </tr>          
              <tr>
                <td width="140" class="dataLabel">Conta-caixa:</td>
                <td colspan="4" valign="middle" class="tabDetailViewDF">
                  <?php echo $campos['subgrupo_nome'] ?>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Centro de Custo:</td>
                <td colspan="4" valign="middle" class="tabDetailViewDF">
                  <?php echo $campos['grupo_nome'] ?>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Tipo Pessoa/Sacado:</td>
                <td colspan="4" valign="middle" class="tabDetailViewDF">
                  <?php echo $desc_pessoa ?><br/>
                  <b><?php echo $nome_pessoa ?></b>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Evento:</td>
                <td colspan="4" valign="middle" class="tabDetailViewDF">
                  <?php 

                    //Verifica se há algum formando associado a conta
                    if ($campos['evento_id'] != 0)
                    {

                      //Imprime os dados do evento
                      echo "[" . $campos['evento_id'] . "] - " . $campos['evento_nome']; 

                    } 

                    else 

                    {

                      echo 'Nenhum evento associado a esta conta';

                    }

                  ?>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Formando:</td>
                <td colspan="4" valign="middle" class="tabDetailViewDF">
                  <?php 

                    //Verifica se há algum formando associado a conta
                    if ($campos['formando_id'] != 0)
                    {

                            //Imprime os dados do formando
                            echo "[" . $campos['formando_id'] . "] - " . $campos['formando_nome']; 

                    } 

                            else 

                    {

                            echo 'Nenhum formando associado a esta conta';

                    }

                  ?>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Nº do Documento:</td>
                <td colspan="4" valign="middle" class="tabDetailViewDF">
                  <b><?php echo substr($campos[nro_documento],0,3) ?><span style='color: #990000'><?php echo substr($campos[nro_documento],3,5) ?></span><?php echo substr($campos[nro_documento],8,2) ?></b>
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Valor:</td>
                <td colspan="4" width="173" class="tabDetailViewDF">
                  <?php echo 'R$ ' . number_format($campos['valor_original'], 2, ',', '.') ?>
                </td>        
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Custo do Boleto:</td>
                <td colspan="4" width="173" class="tabDetailViewDF">
                  <?php echo 'R$ ' . number_format($campos['valor_boleto'], 2, ',', '.') ?>
                </td>        
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Taxa Multa Atraso:</td>
                <td width="173" class="tabDetailViewDF">
                  <?php echo $campos[taxa_multa] ?>&nbsp;%
                </td>
                <td width="146" class="dataLabel">Taxa de Juros ao Mês:</td>
                <td colspan="2" class="tabDetailViewDF">
                  <?php echo $campos[taxa_juros] ?>&nbsp;%
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel"><strong>Valor a Receber:</strong></td>
                <td width="173" class="tabDetailViewDF">
                  <?php echo 'R$ ' . number_format($campos['valor'], 2, ',', '.') ?>
                </td>
                <td width="146" class="dataLabel">Data Vencimento:</td>
                <td colspan="2" class="tabDetailViewDF">
                  <strong>
                    <?php echo DataMySQLRetornar($campos[data_vencimento]) ?>
                  </strong>
                </td>
              </tr>         
              <tr>
                <td width="140" valign="top" class="dataLabel">Situação:</td>
                <td colspan="4" class="tabDetailViewDF">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td colspan="2">
                        <span style="font-size: 12px"><?php echo $desc_situacao ?></span>									
                          <?php 
								
                            //Verifica se a conta está em aberto
                            if ($campos['situacao'] == 1) 
                            {

                              //Verifica se não possui um boleto associado a esta conta
                              if ($campos['boleto_id'] == 0)
                              {
				  
                                ?>
                                <br/><br/>
                                <input name="btnReceber" type="button" class="button" id="btnReceber" title="Receber a Conta" value="Receber Conta" style="width: 90px" onclick="wdCarregarFormulario('ContaReceberQuita.php?ContaId=<?php echo $campos['id'] ?>&headers=1','conteudo')" />											
                                <?php

                              //Fecha o if de se há um boleto vinculado
                              } 

                              else 

                              {
				  
                                echo "<br/><br/><span style='color: #990000'><b>[Esta conta possui um boleto vinculado]&nbsp;&nbsp;&nbsp;</b></span>";

                                ?>

                                <input name="btnReceber" type="button" class="button" id="btnReceber" value="Baixar Boleto" style="width: 90px" title="Baixar o recebimento deste boleto" onclick="wdCarregarFormulario('BoletoQuita.php?BoletoId=<?php echo $campos['boleto_id'] ?>&headers=1','conteudo')" />

                                <?php

                              }

                            //Fecha o if de se a conta está em aberto
                            }

                          ?>
                        </td>
                      </tr>
                    </table>
                  </td>            
                </tr>
                <tr>
                  <td width="140" valign="top" class="dataLabel">Valor Recebido:</td>
                  <td width="173" class="tabDetailViewDF">
                    <?php echo 'R$ ' . number_format($campos['valor_recebido'], 2, ',', '.') ?>
                  </td>
                  <td width="146" class="dataLabel">Saldo a Receber:</td>
                  <td colspan="2" class="tabDetailViewDF">
                    <?php echo 'R$ ' . number_format($campos['valor'] - $campos['valor_recebido'], 2, ',', '.') ?>
                  </td>
                </tr>          
                <tr>
                  <td width="140" valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
                  <td colspan="4" class="tabDetailViewDF">
                    <?php echo nl2br($campos[observacoes]) ?>
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
                  <td valign="top" class="dataLabel">Data de Altera&ccedil;&atilde;o:</td>
                  <td class="tabDetailViewDF">
                    <?php 
                      //Verifica se este registro já foi alterado
                      if ($campos['alteracao_operador_id'] <> 0) 
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
                      if ($campos['alteracao_operador_id'] <> 0) 
                      {
                        //Exibe o nome do operador da alteração da conta
                        echo $campos['operador_alteracao_nome'] . ' ' . $campos['operador_alteracao_sobrenome'];
                      }
                    ?>			 		 
                  </td>
                </tr>           	
              </table>  
              <br/>   
              <?php 
						
                if ($campos['boleto_id'] > 0) 
                {

                  ?>
                  <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
                    <tr>
                      <td height="22" width="20" valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px">
                        <img src="image/bt_boleto.png" title="Visualizar Boleto" onclick="abreJanelaBoleto('./boletos/boleto_bb.php?TipoBol=1&BoletoId=<?php echo $campos[boleto_id] ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" style="cursor: pointer" />
                      </td>
                      <td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px">
                        <strong>Esta conta possui um boleto associado ! </strong><a href="#" onclick="abreJanelaBoleto('./boletos/boleto_bb.php?TipoBol=1&BoletoId=<?php echo $campos[boleto_id] ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')">Clique aqui</a> para visualizar o boleto. (ou clique na imagem ao lado)
                      </td>
                    </tr>
                  </table>
                  <br />
                </td>
              </tr>
              <tr>
                <td>
                  <?php
                     }
                   ?>


<?php 
//Monta um sql para pesquisar se há algum pagamento lançado para esta conta
$sql_consulta = mysql_query("SELECT * FROM contas_receber_recebimento WHERE conta_receber_id = $ContaId ORDER BY data_recebimento");
														 
$registros = mysql_num_rows($sql_consulta); 
												
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>  
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Recebimentos efetuados para esta conta:</span></td>
			  </tr>
			</table>
  	</td>
  </tr>
  <tr>
    <td>
      <table id="4" width="100%" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">

  		<?php
    	
			//Caso não houverem registros
			if ($registros == 0) 
			{ 
	
				//Exibe uma linha dizendo que nao registros
				echo "<tr height='24'>
						<td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
							<font color='#33485C'><b>Não há recebimentos efetuados para esta conta a receber</b></font>
						</td>
					</tr>";	  
			} 
			
			else 
			
			{		  			   

				//Exibe o cabeçalho da tabela
				echo "
					<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
						<td width='22' style='padding-left: 12px'>&nbsp;A</td>
						<td width='70' style='padding-left: 12px'>&nbsp;Data</td>
						<td width='110'>Tipo Pgto</td>
						<td width='110' align='center'>Nº do Cheque</td>
						<td width='100' align='right'>Valor Recebido</td> 		      
						<td>&nbsp;&nbsp;Observações</td>
					</tr>";
		   	
		$Linha = 1;
		
		//Cria o array e o percorre para montar a listagem dinamicamente
		while ($dados_consulta = mysql_fetch_array($sql_consulta))
		{
    
			if ($Linha < $registros)
			{
			
				$linha_display = "border-bottom: 1px dotted #aaa;";
				
			}
			
			else
			
			{
			
				$linha_display = "";
				
			}
					
			//Efetua o switch do tipo de pagamento
			switch ($dados_consulta['tipo_recebimento']) 
			{
				case 1: $nome_tipo = 'Dinheiro';			break;
				case 2: $nome_tipo = 'Cheque';				break;       	
				case 3: $nome_tipo = 'Cheque de Terceiro';	break;
				case 4: $nome_tipo = 'Boleto Bancário';		break;
			}
    
?>
		<tr valign="middle">
			<td height="24" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>padding-bottom: 1px; padding-left: 12px">
				<?php
          
					//Verifica se o pagamento foi por boleto
					if ($dados_consulta['tipo_recebimento'] == 4)
					{
            
						$pgto_boleto = "&ComBoleto=1&BoletoId=" . $campos['boleto_id'];
            
					}
          
				?>
				<img src="image/grid_exclui.gif" alt="Clique para estornar este recebimento" onclick="if(confirm('Confirma o estorno deste recebimento ?')) {wdCarregarFormulario('ContaReceberQuitaExclui.php?RecebimentoId=<?php echo $dados_consulta[id] ?>&ContaId=<?php echo $dados_consulta[conta_receber_id] . $pgto_boleto ?>','conteudo')}" style="cursor: pointer" />
			</td>	
			<td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>">
				&nbsp;<?php echo DataMySQLRetornar($dados_consulta[data_recebimento]) ?>
				</font>        
			</td>
			<td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>">
				<?php echo $nome_tipo ?>
			</td>
			<td align="center" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>">
				<?php echo $dados_consulta['cheque_id'] ?>
			</td>
			<td align="right" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" style="<?php echo $linha_display ?>">
				<?php echo 'R$ ' . number_format($dados_consulta['total_recebido'], 2, ",", ".") ?>
			</td>				
			<td valign="middle" bgcolor="#fdfdfd" style="padding-left: 4px;" style="<?php echo $linha_display ?>">
				<?php 
          
					echo $dados_consulta['obs'];
          
					if ($dados_consulta['tipo_recebimento'] == 4)
					{
					  
					?>
					&nbsp;<input name="btnReceber" type="button" class="button" id="btnReceber" value="Detalhes" style="width: 90px" title="Mostrar detalhes da baixa deste boleto" onclick="wdCarregarFormulario('BoletoQuita.php?BoletoId=<?php echo $campos['boleto_id'] ?>&headers=1','conteudo')" />
					<?php
					
                              }
                              
                            ?>
			</td>						
                      </tr>
                    <?php
                    //Fecha o WHILE
                    }
		  
		  //Fecha o if de se tiver pagamentos
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
  </tr>
</table>
