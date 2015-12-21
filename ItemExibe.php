<?php 
###########
## Módulo para exibicao do produto
## Criado: 20/05/2007 - Maycon Edinger
## Alterado: 26/11/2007 - Maycon Edinger 
## Alterações: 
## 17/06/2007 - Implementado os novos campos para o cadastro de produtos
## 26/06/2007 - Implementado o cadastro de opções do item
## 30/07/2007 - Implementado campo para categoria dos itens
## 22/11/2007 - Corrigido bug que não exibia o valor de venda e valor de custo do item na consulta
## 26/11/2007 - Implementado campo para valor de locação do item
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
  header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

$edtProdutoId = $_GET["Id"];

//Monta o lookup da tabela de categorias de item
//Monta o SQL
$sql = "SELECT
        ite.id,
        ite.nome,
        ite.classificacao,
        ite.categoria_id,
        ite.departamento_id,
        ite.unidade,
        ite.estoque_atual,
        ite.estoque_minimo,
        ite.tipo_produto,
        ite.tipo_material,
        ite.valor_custo,
        ite.ativo,
        ite.localizacao_1,
        ite.localizacao_2,
        ite.localizacao_3,
        ite.exibir_evento,
        ite.exibir_orcamento,
        ite.ent_data,
        ite.ent_oc_id,
        ite.ent_nf,
        ite.ent_fornecedor_id,
        ite.ent_quantidade,
        ite.ent_valor_unitario,
        ite.sai_data,
        ite.sai_evento_id,
        ite.sai_cliente_id,
        ite.sai_documento,
        ite.sai_quantidade,
        eve.nome AS evento_nome,
        cat.nome AS categoria_nome,
        depto.nome AS departamento_nome,
        forn.nome AS fornecedor_nome
        FROM item_evento ite
        LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
        LEFT OUTER JOIN departamentos depto ON depto.id = ite.departamento_id
        LEFT OUTER JOIN fornecedores forn ON forn.id = ite.ent_fornecedor_id
        LEFT OUTER JOIN eventos eve ON eve.id = ite.sai_evento_id
        WHERE ite.id = $edtProdutoId";

//Executa a query
$resultado = mysql_query($sql);

//Monta o array dos campos
$dados = mysql_fetch_array($resultado);

//Efetua o switch para o campo de status
switch ($dados["status"]) 
{
  
  case 0: $desc_ativo = 'Inativo'; break;
  case 1: $desc_ativo = 'Ativo'; break;	
  
} 

//Efetua o switch para o campo de classificacao
switch ($dados["classificacao"]) 
{
  
  case 1: $desc_class = 'Produto'; break;
  case 2: $desc_class = 'Servico'; break;
  
} 

//Efetua o switch para o campo de material
switch ($dados["tipo_material"]) 
{
  
  case 0: $material_status = "<img src='image/grid_aberto.gif'/>";	  break;
  case 1: $material_status = "<img src='image/grid_ativo.gif'/>";  break;

}

//Efetua o switch para o campo de exibir no evento
switch ($dados["exibir_evento"]) 
{
  
  case 0: $evento_status = "<img src='image/grid_aberto.gif'/>";	  break;
  case 1: $evento_status = "<img src='image/grid_ativo.gif'/>";  break;

 }
 
//Efetua o switch para o campo de exibir no orçamento
switch ($dados["exibir_orcamento"]) 
{
	
  case 0: $orcamento_status = "<img src='image/grid_aberto.gif'/>";	  break;
  case 1: $orcamento_status = "<img src='image/grid_ativo.gif'/>";  break;

}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440">
            <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Detalhamento do Produto</span>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>

      <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">

            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="84" style="PADDING-BOTTOM: 2px">
                  <input name="btnVoltar" type="button" class="button" title="Voltar" value="Voltar" style="width:80px" onclick="wdCarregarFormulario('ItemConsulta.php','conteudo')">
                </td>
                <td style="PADDING-BOTTOM: 2px">
                  <input name="btnEditar" type="button" class="button" title="Edita este Produto" style="width:80px" value="Editar Produto" onclick="wdCarregarFormulario('ItemAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')" />
                </td>
              </tr>
            </table>

            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Caso desejar alterar o produto, clique em [Alterar Produto] </td>
                    </tr>
                  </table>				 		 
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="15%">
                  <span class="dataLabel">Descri&ccedil;&atilde;o:</span>             
                </td>
                <td colspan="3" class="tabDetailViewDF">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td>
                        <b><span style='font-size: 14px'><?php echo $dados["nome"] ?></span></b> 
                        <br/>
                        Código: <?php echo $dados["id"] ?></b>
                        <br/>
                        <br/>
                        <input name="btnEtiqueta" type="button" class="button" title="Emite a Etiqueta do Item" style="width:80px" value="Gerar Etiqueta" onclick="javascript:abreJanela('ItemGeraEtiqueta.php?ProdutoId=<?php echo $dados[id] ?>')" />
                      </td>
                      <td width='250' align='right'>
                        <img src='CodigoBarrasGera.php?CodigoId=<?php echo $dados["id"] ?>'>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Classificação:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <?php echo $desc_class ?>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Categoria:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <?php echo $dados["categoria_nome"] ?>				 						 
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Responsável:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <?php echo $dados["departamento_nome"] ?>				 						 
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Unidade:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <?php echo $dados["unidade"] ?>							 						 
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Estoque Atual:</td>
                <td width="250" class="tabDetailViewDF">
                  <?php echo number_format($dados["estoque_atual"], 3, ".", ",") ?>						 						 
                </td>
                <td width="120" class="dataLabel">Estoque Minimo:</td>
                <td class="tabDetailViewDF">
                  <?php echo number_format($dados["estoque_minimo"], 3, ".", ",") ?>
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Pre&ccedil;o de Custo:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <?php echo number_format($dados["valor_custo"], 2, ",", ".") ?>									
                </td>						 
              </tr>
              <tr>
                <td class="dataLabel">Localização:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <?php echo $dados["localizacao_1"] ?> / <?php echo $dados["localizacao_2"] ?> / <?php echo $dados["localizacao_3"] ?>							
                </td>						 
              </tr>
              <tr>
                <td class="dataLabel" valign="top">Movimentação:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <b>Última Entrada:</b>
                  <br/>
                  <table width="100%" cellpadding="0" cellspacing="0" class="listView">
                    <tr valign="middle">
                      <td width="74" height="22" class="listViewThS1" style="padding-left: 4px">
                        Data:
                      </td>
                      <td width="65" height="22" class="listViewThS1">
                        OC:
                      </td>
                      <td width="65" height="22" class="listViewThS1">
                        NF:
                      </td>
                      <td height="22" class="listViewThS1">
                        Fornecedor:
                      </td>
                      <td width="70" height="22" class="listViewThS1">
                        Quantidade:
                      </td>
                      <td width="70" height="22" class="listViewThS1" align="right" style="padding-right: 4px">
                        Valor Unit:
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="22" style="padding-left: 4px">
                        <input name="edtEntradaData" type="text" class="campo" id="edtEntradaData" style="width: 62px; background-color: #ddd" readonly="readonly" value="<?php echo DataMySQLRetornar($dados["ent_data"]) ?>" >
                      </td>
                      <td height="22">
                        <input name="edtEntradaOc" type="text" class="campo" id="edtEntradaOc" style="width: 52px; background-color: #ddd" readonly="readonly" value="<?php echo $dados["ent_oc_id"] ?>" >
                      </td>
                      <td height="22">
                        <input name="edtEntradaNf" type="text" class="campo" id="edtEntradaNF" style="width: 52px; background-color: #ddd" readonly="readonly" value="<?php echo $dados["ent_nf"] ?>">
                      </td>
                      <td height="22">
                        <input name="edtEntradaFornecedor" type="text" class="campo" id="edtEntradaFornecedor" style="width: 290px; background-color: #ddd" readonly="readonly" value="<?php echo "[" . $dados["ent_fornecedor_id"] . "] - " . $dados["fornecedor_nome"] ?>">
                      </td>
                      <td height="22">
                        <input name="edtEntradaQuantidade" type="text" class="campo" id="edtEntradaQuantidade" style="text-align: right;width: 62px; background-color: #ddd" readonly="readonly" value="<?php echo $dados["ent_quantidade"] ?>">
                      </td>
                      <td height="22" align="right" style="padding-right: 4px">
                        <input name="edtEntradaValor" type="text" class="campo" id="edtEntradaValor" style="text-align: right; width: 62px; background-color: #ddd" readonly="readonly" value="<?php echo $dados["ent_valor_unitario"] ?>">
                      </td>
                    </tr>
                  </table>
                  <br/>
                  <b>Última Saída:</b>
                  <br/>
                  <table width="100%" cellpadding="0" cellspacing="0" class="listView">
                    <tr valign="middle">
                      <td width="74" height="22" class="listViewThS1" style="padding-left: 4px">
                        Data:
                      </td>
                      <td height="22" class="listViewThS1">
                        Evento:
                      </td>
                      <td width="70" height="22" class="listViewThS1">
                        Quantidade:
                      </td>
                      <td width="70" height="22" class="listViewThS1" align="right" style="padding-right: 4px">
                        Documento:
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="22" style="padding-left: 4px">
                        <input name="edtSaidaData" type="text" class="campo" id="edtSaidaData" style="width: 62px; background-color: #ddd" readonly="readonly" value="<?php echo DataMySQLRetornar($dados["sai_data"]) ?>">
                      </td>
                      <td height="22">
                        <input name="edtSaidaEvento" type="text" class="campo" id="edtSaidaEvento" style="width: 420px; background-color: #ddd" readonly="readonly" value="<?php echo "[" . $dados["sai_evento_id"] . "] - " . $dados["evento_nome"] ?>">
                      </td>
                      <td height="22">
                        <input name="edtSaidaQuantidade" type="text" class="campo" id="edtSaidaQuantidade" style="text-align: right; width: 62px; background-color: #ddd" readonly="readonly" value="<?php echo $dados["sai_quantidade"] ?>">
                      </td>
                      <td height="22" align="right" style="padding-right: 4px">
                        <input name="edtSaidaDocto" type="text" class="campo" id="edtSaidaDocto" style="text-align: right; width: 62px; background-color: #ddd" readonly="readonly" value="<?php echo $dados["sai_documento"] ?>">
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="dataLabel" valign="top">Opções:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <?php echo $material_status ?>
                  <span style="font-size: 11px">Este produto também pode ser usado como um material.</span>
                  <br>
                  <?php echo $evento_status ?>
                  <span style="font-size: 11px">Exibir o produto nos itens disponíveis para um evento.</span>
                  <br>
                  <?php echo $orcamento_status ?>
                  <span style="font-size: 11px">Exibir o produto nos itens disponíveis para um orçamento.</span>
                </td>
              </tr>           
            </table>
          </td>
        </tr>  
      </table>  	 
    </td>
  </tr>
  <tr>
    <td>
      <div id="retorno_consulta"></div>
    </td>
  </tr>
</table>
