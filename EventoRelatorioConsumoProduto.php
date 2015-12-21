<?php 
###########
## Módulo para relatório de alocação de produtos em eventos
## Criado: 15/08/2012 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Rotina para verificar se necessita ou não montar o header para o ajax
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{

  //Inclui o arquivo para manipulação de datas
  include "./include/ManipulaDatas.php";

}

//Efetua o lookup na tabela de produtos
//Monta o sql de pesquisa
$lista_produtos = "SELECT id, nome FROM item_evento WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_produtos = mysql_query($lista_produtos);


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


//Monta o lookup da tabela de regiões
//Monta o SQL
$lista_regiao = "SELECT id, nome FROM regioes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY id";

//Executa a query
$dados_regiao = mysql_query($lista_regiao);


//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";
//Executa a query
$dados_eventos = mysql_query($lista_eventos);


?>

<script language="JavaScript">
function ExecutaConsulta() 
{

  var Form = document.consulta_data;

  if (Form.edtDataIni.value == 0) 
  {
    alert("É necessário Informar a Data Inicial !");
    Form.edtDataIni.focus();
    return false;
  }

  if (Form.edtDataFim.value == 0) 
  {
    alert("É necessário Informar a Data Final !");
    Form.edtDataFim.focus();
    return false;
  }
  
  // Verifica se data final é maior que a data inicial
  var data_inicial = Form.edtDataIni;
  var data_final = Form.edtDataFim;

  //Aplica a validação das datas informadas	
  dia_inicial      = data_inicial.value.substr(0,2);
  dia_final        = data_final.value.substr(0,2);
  mes_inicial      = data_inicial.value.substr(3,2);
  mes_final        = data_final.value.substr(3,2);
  ano_inicial      = data_inicial.value.substr(6,4);
  ano_final        = data_final.value.substr(6,4);

  if (ano_inicial > ano_final)
  {
    alert("A data inicial deve ser menor que a data final."); 
    data_inicial.focus();
    return false
  } 

  else 

  {
		
    if (ano_inicial == ano_final)
    {

      if (mes_inicial > mes_final)
      {

        alert("A data inicial deve ser menor que a data final.");
        data_final.focus();
        return false

      } 

      else 

      {
					
        if (mes_inicial == mes_final)
        {

          if (dia_inicial > dia_final)
          {
            alert("A data inicial deve ser menor que a data final.");
            data_final.focus();
            return false
          }

        }

      }

    }

  }
  
  valor = 0;
  if (Form.chkValores.checked == true) valor = 1;

  //Monta url que do relatório que será carregado	
  url = "./relatorios/EventoRelatorioConsumoProdutoPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&Valores=" + valor + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&Produto=" + Form.cmbItemId.value + "&Categoria=" + Form.cmbCategoriaId.value + "&Departamento=" + Form.cmbDepartamentoId.value + "&Regiao=" + Form.cmbRegiaoId.value + "&Evento=" + Form.cmbEventoId.value;

  //Executa o relatório selecionado
  abreJanela(url);
	
}

</script>

<form id="consulta_data" name="consulta_data" method="post">

  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td>
              <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alocação de Produtos em Eventos por Data</span>
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
      <td style="PADDING-BOTTOM: 2px">
        <input name="Button" type="button" class="button" id="consulta" title="Emite o relatório" value='Emitir Relatório' onclick="ExecutaConsulta()" />
      </td>
    </tr>
    <tr>
      <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="middle"> 
              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
                <tr>
                  <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
                    <table cellspacing="0" cellpadding="0" width="100%" border="0">
                      <tr>
                        <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe o per&iacute;odo a pesquisar</td>
                      </tr>
                    </table>
                  </td>
                </tr>			  
                <tr>
                  <td class="dataLabel" width="100">In&iacute;cio:</td>
                  <td width="107" class="tabDetailViewDF">
                    <?php
                    
                      //Adiciona o acesso a entidade de criação do componente data
                      include_once("CalendarioPopUp.php");
                      //Cria um objeto do componente data
                      $objData = new tipData();
                      //Define que não deve exibir a hora no calendario
                      $objData->bolExibirHora = false;
                      //Monta javaScript do calendario uma unica vez para todos os campos do tipo data
                      $objData->MontarJavaScript();

                      //Define a data do formulário
                      $objData->strFormulario = "consulta_data";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtDataIni";
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = "";
                      //Define o tamanho do campo 
                      //$objData->intTamanho = 15;
                      //Define o número maximo de caracteres
                      //$objData->intMaximoCaracter = 20;
                      //define o tamanho da tela do calendario
                      //$objData->intTamanhoCalendario = 200;
                      //Cria o componente com seu calendario para escolha da data
                      $objData->CriarData();
                      
                    ?>
                  </td>
                  <td width="61" class="dataLabel">T&eacute;rmino:</td>
                  <td class="tabDetailViewDF">
                    <?php
                    
                      //Define a data do formulário
                      $objData->strFormulario = "consulta_data";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtDataFim";
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = "";
                      //Define o tamanho do campo 
                      //$objData->intTamanho = 15;
                      //Define o número maximo de caracteres
                      //$objData->intMaximoCaracter = 20;
                      //define o tamanho da tela do calendario
                      //$objData->intTamanhoCalendario = 200;
                      //Cria o componente com seu calendario para escolha da data
                      $objData->CriarData();
                      
                    ?>
                  </td>                
                </tr>
                <tr>
                  <td class="dataLabel">Produto:</td>
                  <td colspan="3" class="tabDetailViewDF">
                    <select name="cmbItemId" id="cmbItemId" style="width:360px">
                      <option value="0">--- Todos os Produtos ---</option>
                      <?php

                        //Monta o while para gerar o combo de escolha
                        while ($lookup_produtos = mysql_fetch_object($dados_produtos))
                        {

                          ?>
                          <option value="<?php echo $lookup_produtos->id ?>"><?php echo $lookup_produtos->id . " - " . $lookup_produtos->nome ?></option>
                          <?php

                        }
                      
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel">Categoria:</td>
                  <td colspan="3" class="tabDetailViewDF">
                    <select name="cmbCategoriaId" id="cmbCategoriaId" style="width:360px">
                      <option value="0">--- Todas as Categorias ---</option>
                      <?php

                        //Monta o while para gerar o combo de escolha
                        while ($lookup_categoria = mysql_fetch_object($dados_categoria))
                        {

                          ?>
                          <option value="<?php echo $lookup_categoria->id ?>"><?php echo $lookup_categoria->id . " - " . $lookup_categoria->nome ?></option>
                          <?php

                        }
                      
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel">Departamento:</td>
                  <td colspan="3" class="tabDetailViewDF">
                    <select name="cmbDepartamentoId" id="cmbDepartamentoId" style="width:360px">
                      <option value="0">--- Todos os Departamentos ---</option>
                      <?php

                        //Monta o while para gerar o combo de escolha
                        while ($lookup_departamento = mysql_fetch_object($dados_departamento))
                        {

                          ?>
                          <option value="<?php echo $lookup_departamento->id ?>"><?php echo $lookup_departamento->id . " - " . $lookup_departamento->nome ?></option>
                          <?php

                        }
                      
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Região:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <select name="cmbRegiaoId" id="cmbRegiaoId" style="width:360px">
                      <option value="0">--- Todas as Regiões ---</option>
                      <?php
                      
                      //Monta o while para gerar o combo de escolha de regiao
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
                  <td valign="top" class="dataLabel">Evento:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <select name="cmbEventoId" id="cmbEventoId" style="width:560px">
                      <option value="0">--- Todos os Eventos ---</option>
                      <?php
                      
                      //Monta o while para gerar o combo de escolha de evento
                      while ($lookup_evento = mysql_fetch_object($dados_eventos))
                      {
                        
                        ?>
                        <option value="<?php echo $lookup_evento->id ?>"><?php echo $lookup_evento->id . " - " . $lookup_evento->nome ?></option>
                        <?php
                        
                      }
                      
                      ?>
                    </select> 
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Opções:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <input id="chkValores" name="chkValores" type="checkbox" value="1" checked>&nbsp;Incluir Valores no Relatório
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>