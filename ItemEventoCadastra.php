<?php 
###########
## Módulo para cadastro de itens de evento
## Criado: 22/05/2007 - Maycon Edinger
## Alterado: 22/11/2007 - Maycon Edinger
## Alterações: 
## 21/06/2007 - Incluído campo para quantidade do item
## 22/08/2007 - Implementado a exibição do grupo do evento
## 23/11/2007 - Incluído link para gerenciamento de serviços do evento
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
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
if($_POST) 
{
  
  $EventoId = $_POST["EventoId"]; 

} 

else 

{
  
  $EventoId = $_GET["EventoId"]; 

}

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
                          eve.produtos_timestamp,
                          eve.produtos_operador_id,
                          concat(usu.nome , ' ', usu.sobrenome) as operador_nome,
                          cli.nome as cliente_nome,
                          gru.nome as grupo_nome
                          FROM eventos eve 
                          INNER JOIN clientes cli ON cli.id = eve.cliente_id
                          LEFT OUTER JOIN grupo_conta gru ON gru.id = eve.grupo_id
                          LEFT OUTER JOIN usuarios usu ON usu.usuario_id = eve.produtos_operador_id
                          WHERE eve.id = $EventoId");

//Cria o array dos dados
$dados_evento = mysql_fetch_array($sql_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) 
{
	
  case 0: $desc_status = "Em orçamento"; break;
  case 1: $desc_status = "Em aberto"; break;
  case 2: $desc_status = "Realizado"; break;
  case 3: $desc_status = "<span style='color: red'>Não-Realizado</span>"; break;

} 

?>

<script language="JavaScript">
  //Função que alterna a visibilidade do painel especificado.
  function oculta(id)
  {

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
          <td width="440">
            <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Produtos do Evento</span>
          </td>
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
              if($_POST["Submit"] || $_POST["Materiais"])
              {

                $edtTotalChk = $_POST["edtTotalChk"];
                $edtEventoId = $_POST["EventoId"];

                //Primeiro apaga todos os itens que já existem na base de itens do evento
                $sql_exclui_item = "DELETE FROM eventos_item WHERE evento_id = $EventoId";

                //Executa a query
                $query_exclui_item = mysql_query($sql_exclui_item);
	
	
                //Define o valor inicial para efetuar o FOR
                for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++)
                {

                  //Monta a variável com o nome dos campos
                  $texto_qtde = "edtQtde" . $contador_for;
                  $texto_qtde_alocada = "edtQtdeAlocada" . $contador_for;
                  $texto_preco = "edtValor" . $contador_for;
                  $texto_obs = "edtObs" . $contador_for;

                  $texto_culto = "chkCulto" . $contador_for;
                  $texto_colacao = "chkColacao" . $contador_for;
                  $texto_jantar = "chkJantar" . $contador_for;
                  $texto_baile = "chkBaile" . $contador_for;

                  $valor_preco = MoneyMySQLInserir($_POST[$texto_preco]);

                  //Enquanto não chegar ao final do contador total de itens
                  if ($_POST[$contador_for] != 0) 
                  {
																	
                    $sql_insere_item = "INSERT INTO eventos_item (
                                        evento_id, 
                                        item_id,
                                        quantidade_alocada,
                                        quantidade,
                                        valor_venda,
                                        chk_culto,
                                        chk_colacao,
                                        chk_jantar,
                                        chk_baile,																			 
                                        observacoes
                                        ) VALUES (
                                        '$EventoId',
                                        '$_POST[$contador_for]', 
                                        '$_POST[$texto_qtde_alocada]',
                                        '$_POST[$texto_qtde]',
                                        '$valor_preco',
                                        '$_POST[$texto_culto]',
                                        '$_POST[$texto_colacao]',
                                        '$_POST[$texto_jantar]',
                                        '$_POST[$texto_baile]',
                                        '$_POST[$texto_obs]'
                                        )";																		
										
                    //Insere os registros na tabela de eventos_itens
                    mysql_query($sql_insere_item);
									
                  }								
															
                //Fecha o FOR
                }
						
                //Configura a assinatura digital
                $sql = mysql_query("UPDATE eventos SET produtos_timestamp = now(), produtos_operador_id = $usuarioId WHERE id = $EventoId");

                if($_POST['Submit'])
                {

                  //Exibe a mensagem de inclusão com sucesso
                  echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Produtos Cadastrados com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";

                } 
								
                else 

                {

                  echo "<script language='javascript'>window.location='sistema.php?EventoId=$EventoId&ModuloNome=MaterialEventoGerencia'</script>";	

                }

              }

            ?>       
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="dataLabel" width="15%">Nome do Evento:</td>
                <td colspan="5" class="tabDetailViewDF">
                  <span style="font-size: 16px; color: #990000"><b><?php echo $dados_evento["nome"] ?></b></span>
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <?php echo nl2br($dados_evento["descricao"]) ?>
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
                      <td width="300" height="20">Nome:</td>
                      <td width="260" height="20">Observações:</td>
                      <td height="20">Telefone:</td>
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
                      <td width="30"><img src="./image/bt_evento_gd.gif"/></td>
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
                      <td width="30"><img src="./image/bt_endereco_gd.gif" /></td>
                      <td width="85">
                        <a title="Clique para gerenciar os endereços deste evento" href="#" onclick="wdCarregarFormulario('EnderecoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Endereços</a>
                      </td>
                      <td width="30"><img src="./image/bt_servico_gd.gif"/></td>
                      <td width="85">
                        <a title="Clique para gerenciar os serviços deste evento" href="#" onclick="wdCarregarFormulario('ServicoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Serviços</a> 
                      </td>                 			
                      <td width="30"><img src="./image/bt_terceiro_gd.gif"/></td>
                      <td width="85">
                        <a title="Clique para gerenciar os terceiros deste evento" href="#" onclick="wdCarregarFormulario('TerceiroEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Terceiros</a> 
                      </td>
                      <td width="30"><img src="./image/bt_brinde_gd.gif"/></td>
                      <td width="85">
                        <a title="Clique para gerenciar os brindes deste evento" href="#" onclick="wdCarregarFormulario('BrindeEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Brindes</a> 
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">&nbsp;</td>
                      <td width="30"><img src="./image/bt_repertorio_gd.gif" /></td>
                      <td>
                        <a title="Clique para gerenciar o repertório deste evento" href="#" onclick="wdCarregarFormulario('RepertorioEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Repertório</a>
                      </td>
                      <td width="30">
                        <img src="./image/bt_formando_gd.gif" /> 
                      </td>
                      <td width="85">
                        <a title="Clique para gerenciar os formandos deste evento" href="#" onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Formandos</a>
                      </td>				 
                      <td width="30"><img src="./image/bt_fotovideo_gd.gif" /></td>
                      <td>
                        <a title="Clique para gerenciar o foto e vídeo deste evento" href="#" onclick="wdCarregarFormulario('FotoVideoEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Foto e Vídeo</a>
                      </td>
                      <td width="30"><img src="./image/bt_documentos_gd.gif" /></td>
                      <td colspan="4">
                        <a title="Clique para gerenciar os documentos deste evento" href="#" onclick="wdCarregarFormulario('DocumentosEventoCadastra.php?EventoId=<?php echo $dados_evento[id] ?>&headers=1','conteudo')">Documentos</a>
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
                <td valign="top" width="120" class="dataLabel">Última Alteração:</td>
                <td class="tabDetailViewDF">
                  <?php 
                    //Exibe o timestamp do cadastro da conta
                    echo TimestampMySQLRetornar($dados_evento[produtos_timestamp]) 
                  ?>					
                </td>
                <td class="dataLabel">Operador:</td>
                <td class="tabDetailViewDF" width="200">
                  <?php echo $dados_evento[operador_nome]	?>					
                </td>
              </tr>                 
            </table>
            <br/>
            <table cellspacing="0" cellpadding="0" border="0">
              <tr>
                <td>
                  <form id="form" name="cadastro" action="sistema.php?ModuloNome=ItemEventoCadastra" method="post">
                </td>
              </tr>
              <tr>
                <td style="padding-bottom: 2px">
                  <input name="Submit" type="submit" class="button" title="Salva os Produtos do Evento" value="Salvar Produtos do Evento">
                  <input name="Materiais" type="submit" class="button" title="Salva os produtos do Evento e Vai para o Gerenciamento de Materiais" value="Salvar Produtos e Gerenciar Materiais">
                  <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos">
                  <input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
                </td>
                <td width="36" align="right">&nbsp;</td>
              </tr>
            </table>
            <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
            <tr>
              <td colspan="7" align="right">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td width="300" align="left" class="listViewPaginationTdS1" style="PADDING-BOTTOM: 2px"><span class="pageNumbers"><b>Selecione os produtos a incluir no evento</b></span></td>
                    <td align="right" class="listViewPaginationTdS1" style="PADDING-BOTTOM: 2px"><span class="pageNumbers" style="color: #990000"><b>A quantidade de cada produto é UNITÁRIA de acordo com sua respectiva unidade de medida.&nbsp;</b></span></td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr height="20">
              <td width="28" class="listViewThS1"><div align="center">Inc.</div></td>
              <td width="76" class="listViewThS1">Qtde Venda</td>
              <td width="76" class="listViewThS1">Qtde Entrega</td>
              <td width="200" class="listViewThS1">&nbsp;&nbsp;Descrição do Produto</td>
              <td width="67" class="listViewThS1">Preço Un.</td>
              <td width="67" class="listViewThS1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total</td>						 
              <td width="100" class="listViewThS1">Utilização</td>
            </tr>
            <?php

              //Monta a query para capturar as categorias que existem cadastrados itens
              $sql_categoria = mysql_query("SELECT 
                                            ite.id, 
                                            ite.nome, 
                                            ite.categoria_id, 
                                            cat.nome AS categoria_nome
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
              while ($dados_categoria = mysql_fetch_array($sql_categoria))
              {

                ?>						   
                <tr height="24">
                  <td colspan="7" valign="bottom" style="padding-left: 8px">    				 	 
                    <span style="font-size: 14px">
                    <b>
                    <?php 

                      if ($dados_categoria["categoria_id"] == 0) 
                      {

                        echo "Produtos sem Centro de Custo definido";

                      } 

                      else 

                      {
									
                        echo $dados_categoria["categoria_nome"];

                      }			

                    ?>
                    </b>
                    </span>
                  </td>						 
                </tr>											   
                <?php

                  //Monta a query de filtragem dos itens
                  $filtra_item = "SELECT 
                                  ite.id,
                                  ite.nome,
                                  ite.unidade,
                                  ite.valor_venda,
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
                  while ($dados_item = mysql_fetch_array($lista_item))
                  {
									
                    //if ($dados_item["categoria_id"] == 0) {}

                    //Efetua a pesquisa na base de itens do evento para ver se o item consta como selecionado para o evento
                    $sql_procura_item = "SELECT
                                        quantidade_alocada,
                                        quantidade,
                                        valor_venda,
                                        chk_culto,
                                        chk_colacao,
                                        chk_jantar,
                                        chk_baile,
                                        observacoes
                                        FROM eventos_item
                                        WHERE evento_id = $EventoId
                                        AND item_id = '$dados_item[id]'";
				
                    //Executa a query
                    $query_procura_item = mysql_query($sql_procura_item);

                    //Monta um array com o item de retorno
                    $dados_procura_item = mysql_fetch_array($query_procura_item);

                    //Conta se retornou algum registro
                    $conta_retorno = mysql_num_rows($query_procura_item);

                    //Caso encontrou o item para ser incluso no orçamento
                    if ($conta_retorno == 1) 
                    {
							
                      //Seta para marcar o checkbox
                      $chkItem = "checked";

                      $QtdeRead = '';
                      $QtdeCor = '#FFFFFF';

                      if ($dados_procura_item[chk_culto] == 1) 
                      {
                        $marca_culto = 'checked';
                      }

                      else

                      {

                        $marca_culto = '';

                      }
							
                      if ($dados_procura_item[chk_colacao] == 1) 
                      {

                        $marca_colacao = 'checked';

                      }

                      else

                      {

                        $marca_colacao = '';

                      }

                      if ($dados_procura_item[chk_jantar] == 1) 
                      {

                        $marca_jantar = 'checked';

                      }
							
                      else

                      {

                        $marca_jantar = '';

                      }

                      if ($dados_procura_item[chk_baile] == 1) 
                      {

                        $marca_baile = 'checked';

                      }

                      else

                      {

                        $marca_baile = '';

                      }

                    } 

                    else 
						
                    {

                      //Seta para o chekbox não ser marcado
                      $chkItem = "";

                      $QtdeRead = "disabled='disabled'";
                      $QtdeCor = '#E6E6E6';

                      $marca_culto = '';
                      $marca_colacao = '';
                      $marca_jantar = '';
                      $marca_baile = '';

                    }							 
						
                  ?>
                  <tr height="16">
                    <td valign="top">
                      <div align="center">
                        <input name="<?php echo ++$edtItemChk ?>" type="checkbox" value="<?php echo $dados_item[id] ?>" style="border: 0px" title="Clique para marcar ou desmarcar a inclusão deste produto no evento" onclick="var qtde1 = document.getElementById('edtQtde<?php echo $edtItemChk ?>'); var qtde2 = document.getElementById('edtQtdeAlocada<?php echo $edtItemChk ?>'); if (this.checked == true){qtde1.disabled = false; qtde2.disabled = false; qtde1.style.backgroundColor = '#FFFFFF'; qtde2.style.backgroundColor = '#FFFFFF';} else {qtde1.disabled = true; qtde2.disabled = true; qtde1.style.backgroundColor = '#E6E6E6'; qtde2.style.backgroundColor = '#E6E6E6'; document.cadastro.chkCulto<?php echo $edtItemChk ?>.checked = false; document.cadastro.chkColacao<?php echo $edtItemChk ?>.checked = false; document.cadastro.chkJantar<?php echo $edtItemChk ?>.checked = false; document.cadastro.chkBaile<?php echo $edtItemChk ?>.checked = false;}" <?php echo $chkItem ?> />
                      </div>
                    </td>
                    <td valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 2px;" >
                      <input name="edtQtde<?php echo $edtItemChk ?>" <?php echo $QtdeRead ?> type="text" class="datafield" style="background-color: <?php echo $QtdeCor ?>; width: 50px" maxlength="10" title="Informe a quantidade vendida do produto" value="<?php echo $dados_procura_item[quantidade] ?>" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                    </td>
                    <td valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 2px;" >
                      <input name="edtQtdeAlocada<?php echo $edtItemChk ?>" <?php echo $QtdeRead ?> type="text" class="datafield" style="background-color: <?php echo $QtdeCor ?>; width: 50px" maxlength="10" title="Informe a quantidade alocada do produto" value="<?php echo $dados_procura_item[quantidade_alocada] ?>" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                    </td>
                    <td valign="top" bgcolor="#fdfdfd" class="oddListRowS1">
                      <span style="color: #666; padding-bottom: 4px;">
                        <b><?php echo $dados_item[nome] ?></b>
                      </span>
                    </td>
                    <td valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 2px;">
                      <?php

                        //Verifica se já existe um preço de venda cadastrado para o item
                        if ($dados_procura_item[valor_venda] > 0) 
                        {

                          //Caso tenha valor de venda cadastrado mostra o valor do item para este evento
                          $preco_venda = str_replace(".",",",$dados_procura_item[valor_venda]);

                        } 

                        else 
                        {

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
                    <td valign="top" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 2px;">
                      <input name="edtTotal<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 60px; color: #000000; background-color:#E6E6E6; text-align:right" maxlength="10" readonly="readonly" value="<?php echo number_format($dados_procura_item[valor_venda] * $dados_procura_item[quantidade], 2, ',', '.') ?>" />
                    </td>
                    <td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="padding-top: 2px; padding-right: 0px">
                      <input name="chkCulto<?php echo $edtItemChk ?>" id="chkCulto<?php echo $edtItemChk ?>" type="checkbox" value="1" style="border: 0px" <?php echo $marca_culto  ?> ><span style="font-size: 11px">Culto</span>&nbsp;&nbsp;&nbsp;&nbsp;
                      <input name="chkColacao<?php echo $edtItemChk ?>" id="chkColacao<?php echo $edtItemChk ?>" type="checkbox"  value="1" style="border: 0px" <?php echo $marca_colacao  ?> ><span style="font-size: 11px">Colação</span>
                    </td>						 
                  </tr>
                  <tr>
                    <td colspan="3" style="border-bottom: 1px dotted">&nbsp;</td>
                    <td colspan="3" style="border-bottom: 1px dotted">
                      <textarea name="edtObs<?php echo $edtItemChk ?>" wrap="virtual" class="datafield" id="edtObs<?php echo $edtItemChk ?>" style="width: 420px; height: 50px; font-size: 11px"><?php echo $dados_procura_item[observacoes] ?></textarea>
                    </td>
                    <td valign="top" style="border-bottom: 1px dotted">
                      <input name="chkJantar<?php echo $edtItemChk ?>" id="chkJantar<?php echo $edtItemChk ?>" type="checkbox" value="1" style="border: 0px" <?php echo $marca_jantar  ?> ><span style="font-size: 11px">Jantar<span>&nbsp;&nbsp;&nbsp;&nbsp;
                      <input name="chkBaile<?php echo $edtItemChk ?>" id="chkBaile<?php echo $edtItemChk ?>" type="checkbox" value="1" style="border: 0px" <?php echo $marca_baile ?> ><span style="font-size: 11px">Baile</span>
                    </td>
                  </tr>
                  <?php
							 
                  //Fecha o while
                  } 

                //Fecha o while da categoria
                }

              //Envia com o formulario o total final do contador para efetuar o for depois
              ?>					
            </table>
            <input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>" />
            <input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />           
          </td>
	</tr>
      </table>
    </td>
  </tr>
</table>
</form> 