<?php 
###########
## Módulo para Cadastro de Categorias de Foto e Vídeo
## Criado: 14/10/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
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

//Monta o lookup da tabela de fornecedores
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitCategoria() 
{
  var Form;
  Form = document.Categoria;
  
  if (Form.edtNome.value.length == 0) 
  {
     alert("É necessário informar a descrição do Produto do Foto e Vídeo !");
     Form.edtNome.focus();
     return false;
  }   

  return true;
  
}
</script>

<form name="Categoria" action="sistema.php?ModuloNome=CategoriaFotoVideoCadastra" method="post" onsubmit="return wdSubmitCategoria()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
        <tr>
          <td class="text" valign="top">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td width="440">
                  <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastro de Produtos do Foto e Vídeo</span>
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
                    if($_POST["Submit"]) 
                    {

                      //Recupera os valores vindo do formulário e atribui as variáveis
                      $edtEmpresaId = $empresaId;
                      $edtNome = $_POST["edtNome"];
                      $edtValorVenda = MoneyMySQLInserir($_POST["edtValorVenda"]);
                      $chkAtivo = $_POST["chkAtivo"];
                      $edtValorCompra = MoneyMySQLInserir($_POST["edtValorCompra"]);
                      $edtFornecedorId = $_POST["edtFornecedorId"];

                      //Monta e executa a query
                      $sql = mysql_query("INSERT INTO categoria_fotovideo (
                                          empresa_id, 
                                          nome,
                                          valor_venda, 
                                          ativo,
                                          valor_compra,
                                          fornecedor_id
                                          ) values (				
                                          '$edtEmpresaId',
                                          '$edtNome',
                                          '$edtValorVenda',
                                          '$chkAtivo',
                                          '$edtValorCompra',
                                          '$edtFornecedorId'
                                          );");

                      //Exibe a mensagem de inclusão com sucesso
                      echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Produto do Foto e Vídeo cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";

                    }

                  ?>
                <table cellspacing="0" cellpadding="0" width="100%" border="0">
                  <tr>
                    <td style="PADDING-BOTTOM: 2px">
                      <input name="Submit" type="submit" class="button" title="Salva o registro atua" value="Salvar Produto do Foto/Vídeo" />
                      <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
                    </td>
                    <td align="right">
                      <input class="button" title="Emite o relatório dos Produtos do Foto e Vídeo cadastrados" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="abreJanela('./relatorios/CategoriaFotoVideoRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" />
                    </td>
                  </tr>
                </table>

                <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
                  <tr>
                    <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
                      <table cellspacing="0" cellpadding="0" width="100%" border="0">
                        <tr>
                          <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Produto do Foto e Vídeo e clique em [Salvar Produto do Foto/Vídeo] </td>
                        </tr>
                      </table>             
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel" width="15%">
                      <span class="dataLabel">Descri&ccedil;&atilde;o:</span>             
                    </td>
                    <td width="85%" colspan="3" class="tabDetailViewDF">
                      <table width="100%" cellpadding="0" cellspacing="0">
                        <tr valign="middle">
                          <td width="333" height="20">
                            <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 300" size="60" maxlength="50">
                          </td>
                          <td width="110">
                            <div align="right">Cadastro Ativo
                              <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" checked>
                            </div>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td class="dataLabel">Pre&ccedil;o de Venda:</td>
                    <td class="tabDetailViewDF">
                      <?php

                        //Cria um objeto do tipo WDEdit 
                        $objWDComponente = new WDEditReal();

                        //Define nome do componente
                        $objWDComponente->strNome = "edtValorVenda";
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
                    <td class="dataLabel">Pre&ccedil;o de Custo:</td>
                    <td class="tabDetailViewDF">
                      <?php

                        //Cria um objeto do tipo WDEdit 
                        $objWDComponente = new WDEditReal();

                        //Define nome do componente
                        $objWDComponente->strNome = "edtValorCompra";
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
                    <td class="dataLabel">Fornecedor:</td>
                    <td valign="middle" class="tabDetailViewDF">
                      <select id="edtFornecedorId"t name="edtFornecedorId" style="width:450px">
                        <option value="0">Selecione um Fornecedor</option>
                        <?php 

                          //Monta o while para gerar o combo de escolha
                          while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) 
                          {

                            ?>
                            <option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->nome ?></option>
                            <?php 

                          }

                        ?>
                      </select>
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
  </tr>
  <tr>
    <td>
      <br/>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
        <tr height="18">
          <td colspan="15" align="right">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td colspan="2" nowrap align="left"  class="listViewPaginationTdS1"><span class="pageNumbers">Produtos do Foto e Vídeo Cadastrados</span></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr height="28">
          <td width="42" class="listViewThS1">
            <div align="center">A&ccedil;&atilde;o</div>
          </td>
          <td class="listViewThS1">
            &nbsp;&nbsp;Descrição do Produto do Foto/Vídeo
          </td>
          <td align="right" width="64" class="listViewThS1">Vlr. Venda</td>
          <td align="right" width="64" class="listViewThS1">Vlr. Custo</td>
          <td align="center" width="75" class="listViewThS1">Estoque</td>				        
          <td width="40" class="listViewThS1">
            <div align="center">Ativo</div>
          </td>
        </tr>
        <?php

          //Monta a tabela de consulta
          //Cria a SQL
          $consulta = "SELECT 
                      cat.id,
                      cat.nome,
                      cat.valor_venda,
                      cat.ativo,
                      cat.valor_compra,
                      cat.fornecedor_id,
                      forn.nome as fornecedor_nome
                      FROM categoria_fotovideo cat
                      LEFT OUTER JOIN fornecedores forn ON forn.id = cat.fornecedor_id
                      WHERE cat.empresa_id = $empresaId 
                      ORDER BY cat.nome";

          //Executa a query
          $listagem = mysql_query($consulta);
          
          //Monta e percorre o array com os dados da consulta
          while ($dados = mysql_fetch_array($listagem))
          {

            //Efetua o switch para exibir a imagem para quando o cadastro estiver ativo
            switch ($dados[ativo]) 
            {
              case 0: $ativo_figura = "";	break;
              case 1: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Cadastro Ativo' />";	break;       	
            }	

            $item_pesquisa = $dados[id];

            //Verifica o estoque do produto na tabela de foto e vídeo.
            $consulta_estoque = "SELECT 
                                item_id, 													 
                                (sum(quantidade_disponivel) - sum(quantidade_venda) - sum(quantidade_brinde)) as saldo_item 
                                FROM eventos_fotovideo 
                                WHERE item_id = $item_pesquisa
                                GROUP BY item_id";

            $listagem_estoque = mysql_query($consulta_estoque);

            //Conta o numero de compromissos que a query retornou
            $registros_estoque = mysql_num_rows($listagem_estoque);

            $dados_estoque = mysql_fetch_array($listagem_estoque)

            //Fecha o php, mas o while continua
            ?>

            <tr height="24">
              <td width="42" style="border-bottom: 1px dotted">
                <div align="center">
                  <img src="image/grid_exclui.gif" alt="Excluir Registro" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclusão do registro ?\nA exclusão de registros desta tabela não é recomendada.\nRecomendamos a utilização da caixa [Cadastro Ativo] caso desejar desativar um registro.')) {wdCarregarFormulario('ProcessaExclusaoGet.php?Id=<?php echo $dados[id] ?>&Modulo=categoria_fotovideo&Retorno=CategoriaFotoVideoCadastra','conteudo')}" style="cursor: pointer"></a>
                  <img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('CategoriaFotoVideoAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">          
                </div>
              </td>
              <td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' class='oddListRowS1' style="border-bottom: 1px dotted" onclick="wdCarregarFormulario('CategoriaFotoVideoAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')">
                <a title="Clique para editar este registro" href="#"><?php echo $dados[nome] ?></a>
                <?php
                
                  if ($dados['fornecedor_nome'] != '') echo "<br/><span style='font-size: 11px'><b>Fornecedor Padrão: </b>" . $dados['fornecedor_nome'] . '</span>';
                  
                ?>
              </td>
              <td align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted">
                <?php echo number_format($dados["valor_venda"], 2, ",", ".") ?>
              </td>
              <td align="right" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted">
                <?php echo number_format($dados["valor_compra"], 2, ",", ".") ?>
              </td>
              <td align="center" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted">
                <span style="font-size: 12px"><b>
                  <?php 

                    if ($registros_estoque > 0) 
                    {
                      echo $dados_estoque["saldo_item"];
                    } 

                    else 
                    {
                      echo "0";
                    }

                  ?>
                </b></span>
              </td>	
              <td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dotted">
                <div align="center"><?php echo $ativo_figura ?></div>
              </td>
            </tr>
          <?php

          //Fecha o while
          }

        ?>
      </table> 
    </td>
  </tr>
</table>
</form>
