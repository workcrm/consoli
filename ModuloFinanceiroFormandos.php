<?php
###########
## Módulo para estatísticas financeiras dos formandos
## Criado: 05/03/2010 - Maycon Edinger
## Alterado: 
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

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Adiciona o acesso a entidade de criação do componente data
include_once("CalendarioPopUp.php");

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";
//Executa a query
$dados_eventos = mysql_query($lista_eventos);

//Efetua o lookup na tabela de subgrupos
//Monta o SQL de pesquisa
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = 1 AND tipo = 1 ORDER BY nome";
	
//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);

$dados_subgrupo2 = mysql_query($lista_subgrupo);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="javascript">

function wdVisualizarRelatorio() 
{
	
  var Form;
  Form = document.cadastro;

  if (Form.cmbEventoId.value == 0) 
  {
    alert("É necessário selecionar um Evento !");
    Form.cmbEventoId.focus();
    return false;
  }

  //Recebe o valor do combo de evento
  var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
  var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value

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

  if (edtTipoConsulta == 1)
  {

    //Monta a url a acessar	 
    var urlCarrega = 'ModuloFinanceiroFormandosListaContas.php?&EventoId=' + cmbEventoIdValor + '&ContaCaixaId=' + Form.cmbSubgrupoId.value + '&ContaCaixaId2=' + Form.cmbSubgrupoId2.value;

  }

  if (edtTipoConsulta == 2)

  {

    //Monta a url a acessar	 
    var urlCarrega = 'ModuloFinanceiroFormandosListaConsolidado.php?&EventoId=' + cmbEventoIdValor + '&ContaCaixaId=' + Form.cmbSubgrupoId.value + '&ContaCaixaId2=' + Form.cmbSubgrupoId2.value;

  }

  else

  {

    //Monta a url a acessar	 
    var urlCarrega = 'ModuloFinanceiroFormandosListaConsolidadoSemJuros.php?&EventoId=' + cmbEventoIdValor + '&ContaCaixaId=' + Form.cmbSubgrupoId.value + '&ContaCaixaId2=' + Form.cmbSubgrupoId2.value;

  }


  //Acessa a listagem das contas
  wdCarregarFormulario(urlCarrega,'resultado');		

}


//*** SE FOR IMPRESSÃO
function wdCarregarRelatorio() 
{
	
  var Form;
  Form = document.cadastro;

  if (Form.cmbEventoId.value == 0) 
  {
    alert("É necessário selecionar um Evento !");
    Form.cmbEventoId.focus();
    return false;
  }

  //Recebe o valor do combo de evento
  var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
  var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value

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

  //Captura o valor referente ao radio button do tipo de ripo de conta
  var edtTipoConta = document.getElementsByName('edtTipoConta');

  for (var i=0; i < edtTipoConta.length; i++) 
  {
    if (edtTipoConta[i].checked == true) 
    {
      edtTipoConta = edtTipoConta[i].value;
      break;
    }
  }

  //Captura o valor referente ao radio button do tipo de situacao
  var edtTipoSituacao = document.getElementsByName('edtSituacao');

  for (var i=0; i < edtTipoSituacao.length; i++) 
  {
    if (edtTipoSituacao[i].checked == true) 
    {
      edtTipoSituacao = edtTipoSituacao[i].value;
      break;
    }
  }
	
	
  if (edtTipoConsulta == 1)
  {

    //Monta a url do relatório		
    var urlRelatorio = './relatorios/FinanceiroFormandoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&EventoId=' + cmbEventoIdValor + '&TipoConsulta=' + edtTipoConsulta + '&ContaCaixaId=' + Form.cmbSubgrupoId.value + '&ContaCaixaId2=' + Form.cmbSubgrupoId2.value;

  } 

  if (edtTipoConsulta == 2)
  {
		
    //Monta a url do relatório		
    var urlRelatorio = './relatorios/FinanceiroFormandoConsolidadoJurosRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&EventoId=' + cmbEventoIdValor + '&TipoConsulta=' + edtTipoConsulta + '&TipoConta=' + edtTipoConta + '&ContaCaixaId=' + Form.cmbSubgrupoId.value + '&ContaCaixaId2=' + Form.cmbSubgrupoId2.value;

  }

  else
  {

    //Monta a url do relatório		
    var urlRelatorio = './relatorios/FinanceiroFormandoConsolidadoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&EventoId=' + cmbEventoIdValor + '&TipoConsulta=' + edtTipoConsulta + '&TipoConta=' + edtTipoConta + '&DataIni=' + data_inicial.value + '&DataFim=' + data_final.value + '&Situacao=' + edtTipoSituacao + '&ContaCaixaId=' + Form.cmbSubgrupoId.value + '&ContaCaixaId2=' + Form.cmbSubgrupoId2.value;

  }
  //Executa o relatório
  abreJanela(urlRelatorio);

}
</script>

<form id="form" name="cadastro" action="#" method="post">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="100%">
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Posição Financeira dos Formandos por Evento</span>			  	
          </td>
        </tr>
        <tr>
          <td colspan="5">
            <img src="image/bt_espacohoriz.gif" width="100%" height="12" />
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">	
  <tr>
    <td>
      <input class="button" title="Visualiza a posição financeira " name="btnVisualizar" type="button" id="btnVisualizar" value="Visualizar na Tela" style="width:100px" onclick="wdVisualizarRelatorio()" />
      <input class="button" title="Emite o relatório da posição financeira dos formandos" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()" />   	   		   		
    </td>   
  </tr> 
</table>
<br/>
<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
  <tr>
    <td class="listViewPaginationTdS1" style="padding-right: 0px; padding-left: 0px; font-weight: normal; padding-bottom: 0px; padding-top: 0px; border-bottom: 0px" colspan="4">
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
          <td class="tabDetailViewDL" style="text-align: left">
            <img src="image/bt_cadastro.gif" width="16" height="15"> Selecione o evento para exibir a posição financeira dos formandos:
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="dataLabel">Evento:</td>
    <td colspan="3" class="tabDetailViewDF">
      <select name="cmbEventoId" id="cmbEventoId" style="width: 400px">                  
        <option value="0">Selecione uma Opção</option>
        <?php 

          //Cria o componente de lookup de eventos
          while ($lookup_eventos = mysql_fetch_object($dados_eventos)) 
          { 
            ?>
            <option value="<?php echo $lookup_eventos->id ?>"><?php echo $lookup_eventos->id . " - " . $lookup_eventos->nome ?></option>
            <?php 

          //Fecha o while
          } 
        ?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="dataLabel">Conta-caixa</td>
    <td colspan="3" class="tabDetailViewDF">
      <select name="cmbSubgrupoId" id="cmbSubgrupoId" style="width: 360px"> 
        <option value="0">--- Todas as Contas-caixa ---</option>			
        <?php 
					
          while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo)) 
          {

            ?>
            <option value="<?php echo $lookup_subgrupo->id ?>"><?php echo $lookup_subgrupo->id . ' - ' . $lookup_subgrupo->nome ?></option>
            <?php 

          }

        ?>
      </select>			  				
    </td>
  </tr>
  <tr>
    <td class="dataLabel">Conta-caixa 2 (Experimental)</td>
    <td colspan="3" class="tabDetailViewDF">
      <select name="cmbSubgrupoId2" id="cmbSubgrupoId2" style="width: 360px"> 
        <option value="0">--- Todas as Contas-caixa ---</option>			
        <?php 

          while ($lookup_subgrupo2 = mysql_fetch_object($dados_subgrupo2)) 
          {

            ?>
            <option value="<?php echo $lookup_subgrupo2->id ?>"><?php echo $lookup_subgrupo2->id . ' - ' . $lookup_subgrupo2->nome ?></option>
            <?php 

          }

        ?>
      </select>			  				
    </td>
  </tr>
  <tr>
    <td class="dataLabel">Agrupamento:</td>
    <td colspan="3" class="tabDetailViewDF">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr valign="middle">
          <td width="160" height="20">
            <input name="edtTipoAgrupamento" type="radio" value="1" checked="checked" />&nbsp;&nbsp;Sintético
          </td>
          <td height="20">
            <input name="edtTipoAgrupamento" type="radio" value="2" />&nbsp;&nbsp;Analítico
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="dataLabel">Formato:</td>
    <td colspan="3" class="tabDetailViewDF">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr valign="middle">
          <td width="160" height="20">
            <input name="edtTipoConsulta" type="radio" value="1" checked="checked" />&nbsp;&nbsp;Consultar Boletos
          </td>
          <td width="210" height="20">
            <input name="edtTipoConsulta" type="radio" value="2" />&nbsp;&nbsp;Posição Financeira dos Formandos
          </td>
          <td height="20">
            <input name="edtTipoConsulta" type="radio" value="3" />&nbsp;&nbsp;Posição Financeira dos Formandos (Sem Juros)
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="dataLabel">Filtragem:</td>
    <td colspan="3" class="tabDetailViewDF">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr valign="middle">
          <td width="160" height="20">
            <input name="edtTipoConta" type="radio" value="1" checked="checked" />&nbsp;&nbsp;Todas as Contas
          </td>
          <td width="210" height="20">
            <input name="edtTipoConta" type="radio" value="2" />&nbsp;&nbsp;Contas dos Formandos
          </td>
          <td height="20">
            <input name="edtTipoConta" type="radio" value="3" />&nbsp;&nbsp;Contas da Comissão
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="dataLabel" width="130">In&iacute;cio:</td>
    <td width="107" class="tabDetailViewDF">
      <?php
        //Define a data do formulário
        $objData->strFormulario = "cadastro";  
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
        $objData->strFormulario = "cadastro";  
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
    <td class="dataLabel">Situação</td>
    <td colspan="3" class=tabDetailViewDF>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="110">
            <input type="radio" name="edtSituacao" value="0" checked/> Todas
          </td>
          <td width="110">
            <input type="radio" name="edtSituacao" value="1" /> A Vencer
          </td>
          <td width="110">
            <input type="radio" name="edtSituacao" value="2" /> Recebidas
          </td>
          <td>
            <input type="radio" name="edtSituacao" value="3" /> Vencidas
          </td>						
        </tr>
      </table>		  				
    </td>
  </tr>                		
</table>
<br/>
<table width="100%" border="0" cellspacing="0" cellpadding="0">	
  <tr>
    <td>
      <div id="resultado"></div>
    </td>
  </tr> 
</table>
</form>