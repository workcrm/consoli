<?php 
###########
## M�dulo para relat�rio dos eventos por data
## Criado: 11/02/2009 - Maycon Edinger
## Alterado: 
## Altera��es: 
## Exibir a listagem de compromissos com 7 dias de anteced�ncia
###########

//Rotina para verificar se necessita ou n�o montar o header para o ajax
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Verifica se a func�o j� foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{

  //Inclui o arquivo para manipula��o de datas
  include "./include/ManipulaDatas.php";

}

//Monta o lookup da tabela de regi�es
//Monta o SQL
$lista_regiao = "SELECT id, nome FROM regioes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY id";

//Executa a query
$dados_regiao = mysql_query($lista_regiao);

?>

<script language="JavaScript">
function ExecutaConsulta() 
{

  var Form;
  Form = document.consulta_data;

  if (Form.edtDataIni.value == 0) 
  {
    alert("� necess�rio Informar a Data Inicial !");
    Form.edtDataIni.focus();
    return false;
  }

  if (Form.edtDataFim.value == 0) 
  {
    alert("� necess�rio Informar a Data Final !");
    Form.edtDataFim.focus();
    return false;
  }

  // Verifica se data final � maior que a data inicial
  var data_inicial = Form.edtDataIni;
  var data_final = Form.edtDataFim;

  //Aplica a valida��o das datas informadas	
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
 
  //Captura o valor referente ao radio button do tipo de capa
  var edtTipoConsulta = document.getElementsByName('edtTipoConsulta');

  for (var i=0; i < edtTipoConsulta.length; i++) 
  {

    if (edtTipoConsulta[i].checked == true) 
    {

      edtTipoConsulta = edtTipoConsulta[i].value;
      break;

    }

  }

  //Captura o valor referente ao radio button do tipo de status
  var edtTipoStatus = document.getElementsByName('edtStatus');

  for (var i=0; i < edtTipoStatus.length; i++) 
  {

    if (edtTipoStatus[i].checked == true) 
    {

      edtTipoStatus = edtTipoStatus[i].value;
      break;

    }

  }
   
  //Monta url que do relat�rio que ser� carregado	
  url = "./relatorios/EventoRelatorioDataPDF.php?DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value + "&Regiao=" + Form.cmbRegiaoId.value + "&UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>" + "&TipoConsulta=" + edtTipoConsulta + "&TipoStatus=" + edtTipoStatus;

  //Executa o relat�rio selecionado
  abreJanela(url);
	
}

</script>

<form id="consulta_data" name="consulta_data" method="post">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440">
            <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relat�tio de Eventos por Data</span>
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
    <td style="PADDING-BOTTOM: 2px">
      <input name="Button" type="button" class="button" id="consulta" title="Emite o relat�rio de eventos pelas datas informadas" value='Emitir Relat�rio' onclick="ExecutaConsulta()" />
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
                <td class="dataLabel" width="65">In&iacute;cio:</td>
                <td width="107" class="tabDetailViewDF">
                  <?php
                  
                    //Adiciona o acesso a entidade de cria��o do componente data
                    include_once("CalendarioPopUp.php");
                    //Cria um objeto do componente data
                    $objData = new tipData();
                    //Define que n�o deve exibir a hora no calendario
                    $objData->bolExibirHora = false;
                    //Monta javaScript do calendario uma unica vez para todos os campos do tipo data
                    $objData->MontarJavaScript();

                    //Define a data do formul�rio
                    $objData->strFormulario = "consulta_data";
                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataIni";
                    //Valor a constar dentro do campo (p/ altera��o)
                    $objData->strValor = "";
                    //Define o tamanho do campo 
                    //$objData->intTamanho = 15;
                    //Define o n�mero maximo de caracteres
                    //$objData->intMaximoCaracter = 20;
                    //define o tamanho da tela do calendario
                    //$objData->intTamanhoCalendario = 200;
                    //Cria o componente com seu calendario para escolha da data
                    $objData->CriarData();
                  
                  ?>
                </td>
                <td width="61" class="dataLabel">T&eacute;rmino:</td>
                <td width="100" class="tabDetailViewDF">
                  <?php
                  
                    //Define a data do formul�rio
                    $objData->strFormulario = "consulta_data";
                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataFim";
                    //Valor a constar dentro do campo (p/ altera��o)
                    $objData->strValor = "";
                    //Define o tamanho do campo 
                    //$objData->intTamanho = 15;
                    //Define o n�mero maximo de caracteres
                    //$objData->intMaximoCaracter = 20;
                    //define o tamanho da tela do calendario
                    //$objData->intTamanhoCalendario = 200;
                    //Cria o componente com seu calendario para escolha da data
                    $objData->CriarData();
                  
                  ?>
                </td>                
              </tr>
              <tr>
                  <td valign="top" class="dataLabel">Regi�o:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <select name="cmbRegiaoId" id="cmbRegiaoId" style="width:350px">
                      <option value="0">--- Todas as Regi�es ---</option>
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
                <td class="dataLabel">Tipo de Evento:</td>
                <td colspan="3" style="padding-top: 8px;" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="130" height="20">
                        <input name="edtTipoConsulta" type="radio" value="0" checked="checked" />&nbsp;&nbsp;Todos
                      </td>
                      <td width="130" height="20">
                        <input name="edtTipoConsulta" type="radio" value="1" />&nbsp;&nbsp;Eventos Sociais
                      </td>
                      <td width="130" height="20">
                        <input name="edtTipoConsulta" type="radio" value="2" />&nbsp;&nbsp;Formaturas
                      </td>
                      <td height="20">
                        <input name="edtTipoConsulta" type="radio" value="3" />&nbsp;&nbsp;Preg�o/Edital
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Status:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="130" height="20">
                        <input name="edtStatus" type="radio" value="4" checked="checked" />&nbsp;Todos
                      </td>
                      <td width="130" height="20">
                        <input name="edtStatus" type="radio" value="0" />&nbsp;Em Or�amento
                      </td>
                      <td width="130" height="20">
                        <input name="edtStatus" type="radio" value="1" />&nbsp;Em Aberto
                      </td>
                      <td width="130" height="20">
                        <input name="edtStatus" type="radio" value="2" />&nbsp;Realizado
                      </td>
                      <td height="20">
                        <input name="edtStatus" type="radio" value="3" />&nbsp;N�o-Realizado
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
      <div id="resultado_consulta"></div>
    </td>
  </tr>
</table>
</form>