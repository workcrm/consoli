<?php 
###########
## Módulo para Cadastro de Itens do evento
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

//Monta o lookup da tabela de categorias de item
//Monta o SQL
$lista_categoria = "SELECT * FROM categoria_item WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";

//Executa a query
$dados_categoria = mysql_query($lista_categoria);

//Monta o lookup da tabela de departamentos de item
//Monta o SQL
$lista_departamento = "SELECT * FROM departamentos WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";

//Executa a query
$dados_departamento = mysql_query($lista_departamento);

?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitItem() 
{
  
  var Form;
  Form = document.Item;
  if (Form.edtNome.value.length == 0) 
  {
    alert("É necessário informar a descrição do Produto !");
    Form.edtNome.focus();
    return false;
  }

  if (Form.cmbCategoriaId.value == 0) 
  {
    alert("É necessário selecionar um Centro de Custo !");
    Form.cmbCategoriaId.focus();
    return false;
  }

  //Verifica se o checkbox de ativo está marcado
  if (Form.chkAtivo.checked) 
  {
    var chkAtivoValor = 1;
  } 

  else 
  {
    var chkAtivoValor = 0;
  }

  return true;
	
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="Item" action="sistema.php?ModuloNome=ItemCadastra" method="post" onsubmit="return wdSubmitItem()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440">
            <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastro de Produtos</span>
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
            <?php
            
            //Verifica se a página está abrindo vindo de uma postagem
            if ($_POST["Submit"])
            {

              //Recupera os valores vindo do formulário e atribui as variáveis
              $edtEmpresaId = $empresaId;
              $edtNome = $_POST["edtNome"];
              $edtClassificacao = $_POST["edtClassificacao"];
              $edtEstoqueAtual = $_POST["edtEstoqueAtual"];
              $edtEstoqueMinimo = $_POST["edtEstoqueMinimo"];
              $cmbCategoriaId = $_POST["cmbCategoriaId"];
              $cmbDepartamentoId = $_POST["cmbDepartamentoId"];
              $chkAtivo = $_POST["chkAtivo"];
              $chkMaterial = $_POST["chkMaterial"];
              $chkEvento = $_POST["chkEvento"];
              $chkOrcamento = $_POST["chkOrcamento"];
              $edtUnidade = $_POST["edtUnidade"];
              $edtLocalizacao1 = $_POST["edtLocalizacao1"];
              $edtLocalizacao2 = $_POST["edtLocalizacao2"];
              $edtLocalizacao3 = $_POST["edtLocalizacao3"];
              $edtValorCusto = MoneyMySQLInserir($_POST["edtValorCusto"]);
              //$edtValorVenda = MoneyMySQLInserir($_POST["edtValorVenda"]);
              //$edtValorLocacao = MoneyMySQLInserir($_POST["edtValorLocacao"]);

              $edtTotalChk = $_POST["edtTotalChk"];

              //Monta e executa a query
              $sql = mysql_query("INSERT INTO item_evento (
                                  empresa_id, 
                                  nome,
                                  classificacao,
                                  estoque_atual,
                                  estoque_minimo,
                                  categoria_id,
                                  departamento_id,
                                  unidade,
                                  tipo_produto,
                                  tipo_material,																
                                  valor_custo,
                                  exibir_evento,
                                  exibir_orcamento,
                                  ativo,
                                  localizacao_1,
                                  localizacao_2,
                                  localizacao_3
                                  ) values (				
                                  '$edtEmpresaId',
                                  '$edtNome',
                                  '$edtClassificacao',
                                  '$edtEstoqueAtual',
                                  '$edtEstoqueMinimo',
                                  '$cmbCategoriaId',
                                  '$cmbDepartamentoId',
                                  '$edtUnidade',
                                  '1',
                                  '$chkMaterial',
                                  '$edtValorCusto',
                                  '$chkEvento',
                                  '$chkOrcamento',
                                  '$chkAtivo',
                                  '$edtLocalizacao1',
                                  '$edtLocalizacao2',
                                  '$edtLocalizacao3'
                                  );");

              $edtItemIdBanco = mysql_insert_id();

              //Define o valor inicial para efetuar o FOR
              for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++)
              {

                //Monta a variável com o nome dos campos
                $regiao = "edtRegiao" . $contador_for;
                $regiao_form = $_POST["$regiao"];

                $valor_venda = "edtPrecoVenda" . $contador_for;
                $valor_venda_form = MoneyMySQLInserir($_POST["$valor_venda"]);

                $valor_locacao = "edtPrecoLocacao" . $contador_for;
                $valor_locacao_form = MoneyMySQLInserir($_POST["$valor_locacao"]);

                mysql_query("INSERT INTO item_valor_venda (item_id, regiao_id, valor_venda, valor_locacao) VALUES ($edtItemIdBanco, $regiao_form, $valor_venda_form, $valor_locacao_form);");
                
              }

              //Exibe a mensagem de inclusão com sucesso
              echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Produto cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
            }
            ?>

            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td style="PADDING-BOTTOM: 2px">
                  <?php
                  
                    if ($usuarioNome == "Maycon" OR $usuarioNome == "Janaina")
                    {

                      ?>
                      <input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Produto">
                      <?php

                    }
                  
                  ?>
                  <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos">
                </td>
                <td align="right">
                  <input class="button" title="Emite o relatório dos produtos cadastrados" name='btnRelatorio' type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="abreJanela('./relatorios/ItemRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" />
                </td>
              </tr>
            </table>

            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Produto e clique em [Salvar Produto] </td>
                    </tr>
                  </table>				 		 
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="15%">
                  <span class="dataLabel">Descri&ccedil;&atilde;o:</span>             
                </td>
                <td colspan="3" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td height="20">
                        <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 460px" maxlength="75">                   
                      </td>
                      <td>
                        <div align="right">Cadastro Ativo
                          <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" checked>
                        </div>                   
                      </td>
                    </tr>
                  </table>             
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Classificação:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="130" height="22">
                        <input name="edtClassificacao" type="radio" value="1" checked>&nbsp;Produto
                      </td>
                      <td height="22">
                        <input name="edtClassificacao" type="radio" value="2">&nbsp;Serviço
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Centro de Custo:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <select name="cmbCategoriaId" id="cmbCategoriaId" style="width:350px">
                    <option value="0">Selecione uma Opção</option>
                      <?php

                        //Monta o while para gerar o combo de escolha de funcao
                        while ($lookup_categoria = mysql_fetch_object($dados_categoria))
                        {

                          ?>
                          <option value="<?php echo $lookup_categoria->id ?>"><?php echo $lookup_categoria->nome ?> </option>
                          <?php 
                          
                        }
                        
                      ?>
                  </select>						 						 
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Responsável:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <select name="cmbDepartamentoId" id="cmbDepartamentoId" style="width:350px">
                    <option value="0">Selecione uma Opção</option>
                      <?php

                        //Monta o while para gerar o combo de escolha de departamento
                        while ($lookup_departamento = mysql_fetch_object($dados_departamento))
                        {

                          ?>
                          <option value="<?php echo $lookup_departamento->id ?>"><?php echo $lookup_departamento->nome ?> </option>
                          <?php 
                          
                        }
                        
                      ?>
                  </select>						 						 
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Unidade:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <select class="datafield"name="edtUnidade" id="edtUnidade">
                    <option value="PC">PC - Peça</option>
                    <option value="UN">UN - Unidade</option>
                    <option value="GR">GR - Grama</option>
                    <option value="KG">KG - Kilo</option>
                    <option value="LT">LT - Litro</option>
                    <option value="PT">PT - Pacote</option>
                    <option value="VD">VD - Vidro</option>
                    <option value="LT">LT - Lata</option>
                    <option value="BD">BD - Balde</option>
                    <option value="CX">CX - Caixa</option>
                    <option value="GL">GL - Galão</option>
                    <option value="MT">MT - Metro</option>
                    <option value="M2">M2 - Metro Quadrado</option>
                    <option value="M3">M3 - Metro Cúbico</option>
                  </select>							 						 
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Estoque Atual:</td>
                <td width="250" class="tabDetailViewDF">
                  <input name="edtEstoqueAtual" type="text" class="campo" id="edtEstoqueAtual" style="width: 50px; background-color: #ddd" >							 						 
                </td>
                <td width="120" class="dataLabel">Estoque Minimo:</td>
                <td class="tabDetailViewDF">
                  <input name="edtEstoqueMinimo" type="text" class="campo" id="edtEstoqueMinimo" style="width: 50px" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Pre&ccedil;o de Custo:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <?php

                    //Cria um objeto do tipo WDEdit 
                    $objWDComponente = new WDEditReal();

                    //Define nome do componente
                    $objWDComponente->strNome = "edtValorCusto";
                    //Define o tamanho do componente
                    $objWDComponente->intSize = 16;
                    //Busca valor definido no XML para o componente
                    $objWDComponente->strValor = "";
                    //Busca a descrição do XML para o componente
                    $objWDComponente->strLabel = "";
                    //Determina um ou mais eventos para o componente
                    $objWDComponente->strEvento = "";
                    //Define numero de caracteres no componente
                    $objWDComponente->intMaxLength = 14;

                    //Cria o componente edit
                    $objWDComponente->Criar();

                  ?>								
                </td>						 
              </tr>
              <tr>
                <td class="dataLabel">Localização:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <input name="edtLocalizacao1" type="text" class="campo" id="edtLocalizacao1" style="width: 40px" maxlength="5">&nbsp;<b>/</b>&nbsp;<input name="edtLocalizacao2" type="text" class="campo" id="edtLocalizacao2" style="width: 40px" maxlength="5">&nbsp;<b>/</b>&nbsp;<input name="edtLocalizacao3" type="text" class="campo" id="edtLocalizacao3" style="width: 40px" maxlength="5">							
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
                        <input name="edtEntradaData" type="text" class="campo" id="edtEntradaData" style="width: 62px; background-color: #ddd" readonly="readonly">
                      </td>
                      <td height="22">
                        <input name="edtEntradaOc" type="text" class="campo" id="edtEntradaOc" style="width: 52px; background-color: #ddd" readonly="readonly">
                      </td>
                      <td height="22">
                        <input name="edtEntradaNf" type="text" class="campo" id="edtEntradaNF" style="width: 52px; background-color: #ddd" readonly="readonly">
                      </td>
                      <td height="22">
                        <input name="edtEntradaFornecedor" type="text" class="campo" id="edtEntradaFornecedor" style="width: 290px; background-color: #ddd" readonly="readonly">
                      </td>
                      <td height="22">
                        <input name="edtEntradaQuantidade" type="text" class="campo" id="edtEntradaQuantidade" style="width: 62px; background-color: #ddd" readonly="readonly">
                      </td>
                      <td height="22" align="right" style="padding-right: 4px">
                        <input name="edtEntradaValor" type="text" class="campo" id="edtEntradaValor" style="text-align: right; width: 62px; background-color: #ddd" readonly="readonly">
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
                        Valor Unit:
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td height="22" style="padding-left: 4px">
                        <input name="edtSaidaData" type="text" class="campo" id="edtSaidaData" style="width: 62px; background-color: #ddd" readonly="readonly">
                      </td>
                      <td height="22">
                        <input name="edtSaidaEvento" type="text" class="campo" id="edtSaidaEvento" style="width: 420px; background-color: #ddd" readonly="readonly">
                      </td>
                      <td height="22">
                        <input name="edtSaidaQuantidade" type="text" class="campo" id="edtSaidaQuantidade" style="width: 62px; background-color: #ddd" readonly="readonly">
                      </td>
                      <td height="22" align="right" style="padding-right: 4px">
                        <input name="edtSaidaValor" type="text" class="campo" id="edtSaidaValor" style="text-align: right; width: 62px; background-color: #ddd" readonly="readonly">
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="dataLabel" valign="top">Opções:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <input name="chkMaterial" type="checkbox" value="1" style="border: 0px">
                  <span style="font-size: 11px">Este produto também pode ser usado como um material.</span>
                  <br>
                  <input name="chkEvento" type="checkbox" value="1" style="border: 0px" checked>
                  <span style="font-size: 11px">Exibir o produto nos itens disponíveis para um evento.</span>
                  <br>
                  <input name="chkOrcamento" type="checkbox" value="1" style="border: 0px" checked>
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
    <td class="text" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>
            <br/>
            <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Preços para Comercialização</span>
          </td>
        </tr>
        <tr>
          <td>
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="text" valign="top">		
      <table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
        <tr height="20">
          <td class="listViewThS1">&nbsp;&nbsp;Região</td>
          <td align="center" width="100" class="listViewThS1" style="padding-right: 6px">P. Venda Evento</td>
          <td align="center" width="100" class="listViewThS1" style="padding-right: 6px">P. Venda Locação</td>
        </tr>
        <?php

          //Monta a tabela de consulta dos itens acadastrados
          //Cria a SQL
          $consulta = "SELECT * FROM regioes ORDER BY id";

          //Executa a query
          $listagem = mysql_query($consulta);

          $registros = mysql_num_rows($listagem);

          $categoria_lista = 0;

          $linha = 1;

          $edtItemChk = 0;

          //Monta e percorre o array com os dados da consulta
          while ($dados = mysql_fetch_array($listagem))
          {

            $edtItemChk++;

            if ($linha < $registros)
            {

              $borda = "border-bottom: 1px #aaa dashed;";
              
            }
            
            else
            {

              $borda = '';
              
            }

            //Fecha o php, mas o while continua
            ?>
              <tr height="16">
                <td style="<?php echo $borda ?>" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1">
                  <b><?php echo $dados["nome"] ?></b><input name="edtRegiao<?php echo $edtItemChk ?>" type="hidden" value="<?php echo $dados["id"] ?>" />
                </td>
                <td width="90" style="<?php echo $borda ?>padding-right: 6px" align="right" bgcolor="#fdfdfd" class="currentTabList">
                  <?php

                    //Cria um objeto do tipo WDEdit 
                    $objWDComponente = new WDEditReal();

                    //Define nome do componente
                    $objWDComponente->strNome = "edtPrecoVenda$edtItemChk";
                    //Define o tamanho do componente
                    $objWDComponente->intSize = 16;
                    //Busca valor definido no XML para o componente
                    $objWDComponente->strValor = "";
                    //Busca a descrição do XML para o componente
                    $objWDComponente->strLabel = "";
                    //Determina um ou mais eventos para o componente
                    $objWDComponente->strEvento = "";
                    //Define numero de caracteres no componente
                    $objWDComponente->intMaxLength = 14;

                    //Cria o componente edit
                    $objWDComponente->Criar();

                  ?>
                </td>
                <td width="90" style="<?php echo $borda ?>padding-right: 6px" align="right" bgcolor="#fdfdfd" class="currentTabList">
                  <?php
                  
                    //Cria um objeto do tipo WDEdit 
                    $objWDComponente = new WDEditReal();

                    //Define nome do componente
                    $objWDComponente->strNome = "edtPrecoLocacao$edtItemChk";
                    //Define o tamanho do componente
                    $objWDComponente->intSize = 16;
                    //Busca valor definido no XML para o componente
                    $objWDComponente->strValor = "";
                    //Busca a descrição do XML para o componente
                    $objWDComponente->strLabel = "";
                    //Determina um ou mais eventos para o componente
                    $objWDComponente->strEvento = "";
                    //Define numero de caracteres no componente
                    $objWDComponente->intMaxLength = 14;

                    //Cria o componente edit
                    $objWDComponente->Criar();
                  
                  ?>
                </td>
              </tr>
              <?php
              
            $linha++;

          //Fecha o while
          }

        ?>
      </table>	
    </td>
  </tr>
</table>
<input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>" />
</form>