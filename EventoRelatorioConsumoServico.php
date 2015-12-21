<?php 
###########
## Módulo para relatório de alocação de servicos em eventos
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

//Efetua o lookup na tabela de serviços
//Monta o sql de pesquisa
$lista_servicos = "SELECT id, nome FROM servico_evento WHERE empresa_id = $empresaId ORDER BY nome";

//Executa a query
$dados_servicos = mysql_query($lista_servicos);

//Efetua o lookup na tabela de categorias
//Monta o SQL de pesquisa
$lista_categoria = "SELECT * FROM categoria_servico WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_categoria = mysql_query($lista_categoria);

?>

<script language="JavaScript">
function ExecutaConsulta() 
{

  var Form;
  Form = document.consulta_data;

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
  
  if (Form.cmbItemId.value == 0)
  {
    alert("É necessário selecionar um serviço !");
    return false
  }

  //Monta url que do relatório que será carregado	
  url = "./relatorios/EventoRelatorioConsumoServicoPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&Servico=" + Form.cmbServicoId.value;

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
              <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alocação de Servicos em Eventos por Data</span>
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
                  <td class="dataLabel">Serviço:</td>
                  <td colspan="3" class="tabDetailViewDF">
                    <select name="cmbServicoId" id="cmbItemId" style="width:360px">
                      <option value="0">--- Todos os Serviços ---</option>
                      <?php

                        //Monta o while para gerar o combo de escolha
                        while ($lookup_servicos = mysql_fetch_object($dados_servicos))
                        {

                          ?>
                          <option value="<?php echo $lookup_servicos->id ?>"><?php echo $lookup_servicos->id . " - " . $lookup_servicos->nome ?></option>
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
</form>