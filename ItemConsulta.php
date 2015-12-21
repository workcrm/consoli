<?php 
###########
## Módulo para Consulta de Itens do evento
## Criado: 06/12/2011 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Efetua o lookup na tabela de categorias
//Monta o SQL de pesquisa
$lista_categoria = "SELECT * FROM categoria_item WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_categoria = mysql_query($lista_categoria);


//Efetua o lookup na tabela de departamentos
//Monta o SQL de pesquisa
$lista_departamento = "SELECT * FROM departamentos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_departamento = mysql_query($lista_departamento);

?>

<script language="JavaScript">
function EfetuaConsulta() 
{
	
  var Form;
  Form = document.frmProdutosPesquisa;

  if (Form.edtPesquisa.value.length == 0) 
  {

    alert("É necessário informar um argumento de pesquisa !");
    Form.edtPesquisa.focus();
    return false;

  }

  urlPesquisa = "ItemConsultaLista.php?edtPesquisa=" + Form.edtPesquisa.value + "&headers=1";

  wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');

  return true;
  
}

function EfetuaConsultaCategoria() 
{
	
  var Form = document.frmProdutosPesquisa;

  urlPesquisa = "ItemConsultaLista.php?edtCategoria=" + Form.cmbCategoriaId.value + "&headers=1";

  wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');

  return true;
  
}

function EfetuaConsultaDepartamento() 
{
	
  var Form = document.frmProdutosPesquisa;

  urlPesquisa = "ItemConsultaLista.php?edtDepartamento=" + Form.cmbDepartamentoId.value + "&headers=1";

  wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');

  return true;
  
}

function EfetuaPesquisa(Letra) 
{
	
  var Form;
  Form = document.frmEventosPesquisa;

  urlPesquisa = "ItemConsultaLista.php?ChaveFiltragem=" + Letra  + "&headers=1";

  wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');
  return true;

}
</script>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form id="form" name="frmProdutosPesquisa" action="#">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440">
            <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Consulta de Produtos</span>
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>	 
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_pesquisar.gif"> Informe o nome ou codigo do produtos a pesquisar e clique em [Pesquisar Produtos]</td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="70">Pesquisar:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="505" height="20">
                        <input name="edtPesquisa" type="text" class="datafield" id="edtPesquisa"  size="65" maxlength="50" />
                      </td>
                      <td>
                        <span style="PADDING-BOTTOM: 2px">
                          <input class="button" title="Inicia a pesquisa do(s) produtos(s) com base nos valores informados" name="btnPesquisa" type="button" id="btnPesquisa" value="Pesquisar Produtos" onclick="EfetuaConsulta()" style="width: 150px" >
                        </span>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="70">Categoria:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="505" height="20">
                        <select name="cmbCategoriaId" id="cmbCategoriaId" style="width: 360px">    
                          <option value="0">--- Todas as Categorias ---</option>
                          <?php
                          
                            while ($lookup_categoria = mysql_fetch_object($dados_categoria))
                            {

                              ?>
                              <option value="<?php echo $lookup_categoria->id ?>"><?php echo $lookup_categoria->nome ?>				 </option>
                              <?php

                            }
                          
                          ?>
                        </select>
                      </td>
                      <td>
                        <span style="PADDING-BOTTOM: 2px">
                          <input class="button" title="Inicia a pesquisa do(s) produtos(s) com base na categoria selecionada" name="btnPesquisa" type="button" id="btnPesquisa" value="Pesquisar Categorias" onclick="EfetuaConsultaCategoria()" style="width: 150px" >
                        </span>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="70">Responsável:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="505" height="20">
                        <select name="cmbDepartamentoId" id="cmbDepartamentoId" style="width: 360px">    
                          <option value="0">--- Todos os Departamentos ---</option>
                          <?php
                          
                            while ($lookup_departamento = mysql_fetch_object($dados_departamento))
                            {

                              ?>
                              <option value="<?php echo $lookup_departamento->id ?>"><?php echo $lookup_departamento->nome ?>				 </option>
                              <?php

                            }
                          
                          ?>
                        </select>
                      </td>
                      <td>
                        <span style="PADDING-BOTTOM: 2px">
                          <input class="button" title="Inicia a pesquisa do(s) produtos(s) com base no departamento selecionado" name="btnPesquisa" type="button" id="btnPesquisa" value="Pesquisar Departamentos" onclick="EfetuaConsultaDepartamento()" style="width: 150px" >
                        </span>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Iniciar em:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <span style="PADDING-BOTTOM: 2px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnA" type="button" id="btnA" value="A" onClick="EfetuaPesquisa('A')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnB" type="button" id="btnB" value="B" onClick="EfetuaPesquisa('B')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnC" type="button" id="btnC" value="C" onClick="EfetuaPesquisa('C')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnD" type="button" id="btnD" value="D" onClick="EfetuaPesquisa('D')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnE" type="button" id="btnE" value="E" onClick="EfetuaPesquisa('E')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnF" type="button" id="btnF" value="F" onClick="EfetuaPesquisa('F')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnG" type="button" id="btnG" value="G" onClick="EfetuaPesquisa('G')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnH" type="button" id="btnH" value="H" onClick="EfetuaPesquisa('H')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnI" type="button" id="btnI" value="I" onClick="EfetuaPesquisa('I')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnJ" type="button" id="btnJ" value="J" onClick="EfetuaPesquisa('J')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnK" type="button" id="btnK" value="K" onClick="EfetuaPesquisa('K')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnL" type="button" id="btnL" value="L" onClick="EfetuaPesquisa('L')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnM" type="button" id="btnM" value="M" onClick="EfetuaPesquisa('M')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnN" type="button" id="btnN" value="N" onClick="EfetuaPesquisa('N')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnO" type="button" id="btnO" value="O" onClick="EfetuaPesquisa('O')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnP" type="button" id="btnP" value="P" onClick="EfetuaPesquisa('P')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnQ" type="button" id="btnQ" value="Q" onClick="EfetuaPesquisa('Q')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnR" type="button" id="btnR" value="R" onClick="EfetuaPesquisa('R')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnS" type="button" id="btnS" value="S" onClick="EfetuaPesquisa('S')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnT" type="button" id="btnT" value="T" onClick="EfetuaPesquisa('T')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnU" type="button" id="btnU" value="U" onClick="EfetuaPesquisa('U')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnV" type="button" id="btnV" value="V" onClick="EfetuaPesquisa('V')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnW" type="button" id="btnW" value="W" onClick="EfetuaPesquisa('W')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnX" type="button" id="btnX" value="X" onClick="EfetuaPesquisa('X')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnY" type="button" id="btnY" value="Y" onClick="EfetuaPesquisa('Y')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos iniciando com a letra selecionada" name="btnZ" type="button" id="btnZ" value="Z" onClick="EfetuaPesquisa('Z')" style="width:18px">
                    <input class="button" title="Exibe todos os Produtos" name="btnTodos" type="button" id="btnTodos" value="Exibir Todos" onClick="EfetuaPesquisa('todos')" style="width:85px">
                  </span>
                </td>
              </tr>
            </table>		 
            <br/>
          </td>
        </tr> 	
        <tr>
          <td>
            <div id="retornopesquisa">
<?php include "ItemConsultaLista.php"; ?>
            </div>
          </td>
        </tr>
      </table>	
    </td>
  </tr>
</table>

</form>