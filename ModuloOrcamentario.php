<?php
###########
## Módulo para Pesquisa de Planejamento Orçamentário
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

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{
	
  //Inclui o arquivo para manipulação de datas
  include "./include/ManipulaDatas.php";

}

?>
<script language="JavaScript">
  
function EfetuaConsulta() 
{
	
  var Form;
  Form = document.frmOrcamentarioPesquisa;

  urlPesquisa = "ModuloOrcamentarioLista.php?cmbAno=" + Form.cmbAno.value + '&cmbRegional=' + Form.cmbRegiaoId.value + '&cmbCCU=' + Form.cmbCCUId.value + '&cmbCCX=' + Form.cmbCCXId.value;

  wdCarregarFormulario(urlPesquisa,'retornopesquisa','1');

  return true;
  
}

</script>

<form id="form" name="frmOrcamentarioPesquisa" action="#">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440">
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Pesquisar Planejamento Orçamentário</span>
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
      <input class="button" title="Inicia a pesquisa da(s) ordem(ns) de compra com base nos valores informados" name="btnPesquisa" type="button" id="btnPesquisa" value="Ver Orçamento" onclick="EfetuaConsulta()"></span>
      <br/>
      <br/>
      <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
          <td class="dataLabel" width="130"><span class="dataLabel">Ano:</span></td>
          <td class="tabDetailViewDF">
            <select name="cmbAno" id="cmbAno" style="width: 50px">
              <option value="2014" selected>2014</option>
              <option value="2013">2013</option>
            </select>
          </td>
        </tr>
        <tr>
          <td class="dataLabel"><span class="dataLabel">Regional:</span></td>
          <td class="tabDetailViewDF">
            <?php
            
              //Monta o lookup da tabela
              //Monta o SQL
              $lista_regiao = "SELECT * FROM regioes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

              //Executa a query
              $dados_regiao = mysql_query($lista_regiao);
              
            ?>
            <select name="cmbRegiaoId" id="cmbRegiaoId" style="width:250px">
              <option value="0">--- Todas as Regionais ---</option>
              <?php

                //Monta o while para gerar o combo de escolha
                while ($lookup_regiao = mysql_fetch_object($dados_regiao))
                {

                  ?>
                  <option value="<?php echo $lookup_regiao->id ?>"><?php echo $lookup_regiao->id . " - " . $lookup_regiao->nome ?></option>
                  <?php
                  
                }
                
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td class="dataLabel"><span class="dataLabel">Centro de Custo:</span></td>
          <td class="tabDetailViewDF">
          <?php

            //Monta o lookup da tabela de grupos
            //Monta o SQL
            $lista_grupo = "SELECT * FROM grupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
            //Executa a query
            $dados_grupo = mysql_query($lista_grupo);

            ?>
            <select name="cmbCCUId" id="cmbCCUId" style="width:250px">
              <option value="0">--- Todos os Centros de Custo ---</option>
              <?php
              
                //Monta o while para gerar o combo de escolha
                while ($lookup_grupo = mysql_fetch_object($dados_grupo))
                {

                  ?>
                  <option value="<?php echo $lookup_grupo->id ?>"><?php echo $lookup_grupo->id . " - " . $lookup_grupo->nome ?></option>
                  <?php

                }
              
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td class="dataLabel"><span class="dataLabel">Conta-caixa:</span></td>
          <td class="tabDetailViewDF">
            <?php
              
              //Monta o lookup da tabela de subgrupos (CONTA_CAIXA) filtrando tipo 2 que é saída (débito)
              //Monta o SQL
              $lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '2' ORDER BY nome";
              //Executa a query
              $dados_subgrupo = mysql_query($lista_subgrupo);
              
              ?>
              <select name="cmbCCXId" id="cmbCCXId" style="width:250px">
                <option value="0">--- Todas as Contas-caixa ---</option>
                <?php

                  //Monta o while para gerar o combo de escolha
                  while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo))
                  {

                    ?>
                    <option value="<?php echo $lookup_subgrupo->id ?>"><?php echo $lookup_subgrupo->id . " - " . $lookup_subgrupo->nome ?></option>
                    <?php
                    
                  }
                  
                ?>
              </select>
            </td>
          </tr>
        </table>
      <br/>
    </td>
  </tr>
  <tr>
    <td>
      <div id="retornopesquisa"></div>
    </td>
  </tr>
</table>

</form>