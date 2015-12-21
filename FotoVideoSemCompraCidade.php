<?php
###########
## Módulo para controle do envio do foto e vídeo sem compra por cidade
## Criado: 09/11/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Efetua o lookup na tabela de cidades
//Monta o SQL de pesquisa
$lista_cidades = "SELECT id, nome FROM cidades WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_cidades = mysql_query($lista_cidades);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="javascript">

//*** SE FOR IMPRESSÃO
function wdCarregarRelatorio() 
{

  var Form = document.cadastro;

  //Recebe o valor do combo de Cidade
  var cmbCidadeIdIndice = Form.cmbCidadeId.selectedIndex;
  var cmbCidadeIdValor = Form.cmbCidadeId.options[cmbCidadeIdIndice].value

  //Recebe o valor do combo de UF
  var cmbUFIdIndice = Form.cmbUFId.selectedIndex;
  var cmbUFIdValor = Form.cmbUFId.options[cmbUFIdIndice].value

  if (cmbCidadeIdValor == 0 && cmbUFIdValor == 0)
  {

    alert('É necessário selecionar uma Cidade ou UF !');
    return false();

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

  //Monta a url do relatório		
  var urlRelatorio = './relatorios/FotoVideoSemCompraCidadeRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&CidadeId=' + cmbCidadeIdValor + '&UFId=' + cmbUFIdValor + '&DataIni=' + Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

  //Executa o relatório
  abreJanela(urlRelatorio);

  return true;

}

function wdGerarRelacao()
{
  
  var Form = document.cadastro;

  //Recebe o valor do combo de Cidade
  var cmbCidadeIdIndice = Form.cmbCidadeId.selectedIndex;
  var cmbCidadeIdValor = Form.cmbCidadeId.options[cmbCidadeIdIndice].value

  //Recebe o valor do combo de UF
  var cmbUFIdIndice = Form.cmbUFId.selectedIndex;
  var cmbUFIdValor = Form.cmbUFId.options[cmbUFIdIndice].value

  if (cmbCidadeIdValor == 0 && cmbUFIdValor == 0)
  {

    alert('É necessário selecionar uma Cidade ou UF !');
    return false();

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
  
  //Carrega a listagem
  wdCarregarFormulario('FotoVideoSemCompraCidadeProcessa.php?CidadeId=' + cmbCidadeIdValor + '&UFId=' + cmbUFIdValor + "&DataIni=" + Form.edtDataIni.value + "&DataFim=" + Form.edtDataFim.value,'exibe_lista',2);
  
}
</script>

<form id="form" name="cadastro" action="#" method="post">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="100%">
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Foto e Vídeo - Formando Sem Compra por Cidade</span>			  	
          </td>
        </tr>
        <tr>
          <td>
            <img src="image/bt_espacohoriz.gif" width="100%" height="12" />
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td valign="middle"> 
      <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">				
        <tr>
          <td width="100" class="dataLabel">
            Cidade
          </td>
          <td colspan="3" class="tabDetailViewDF">
            <select name="cmbCidadeId" id="cmbCidadeId" style="width: 450px">   
              <option value="0">-- Todas as Cidades --</option>
                <?php
                
                while ($lookup_cidades = mysql_fetch_object($dados_cidades))
                {
                  
                  ?>
                  <option value="<?php echo $lookup_cidades->id ?>"><?php echo $lookup_cidades->nome ?></option>
                  <?php
                  
                }
                
                ?>
            </select>		  				
          </td>
        </tr>
        <tr>
          <td class="dataLabel">UF:</td>
          <td colspan="3" class="tabDetailViewDF">
            <select class="datafield"name="cmbUFId" id="cmbUFId">
              <option value="0">-- Todos --</option>
              <option value="AC">AC</option>
              <option value="AL">AL</option>
              <option value="AM">AM</option>
              <option value="BA">BA</option>
              <option value="CE">CE</option>
              <option value="DF">DF</option>
              <option value="ES">ES</option>
              <option value="GO">GO</option>
              <option value="MA">MA</option>
              <option value="MG">MG</option>
              <option value="MS">MS</option>
              <option value="MT">MT</option>
              <option value="PA">PA</option>
              <option value="PB">PB</option>
              <option value="PE">PE</option>
              <option value="PI">PI</option>
              <option value="PR">PR</option>
              <option value="RJ">RJ</option>
              <option value="RN">RN</option>
              <option value="RO">RO</option>
              <option value="RR">RR</option>
              <option value="RS">RS</option>
              <option value="SC">SC</option>
              <option value="SE">SE</option>
              <option value="SP">SP</option>
              <option value="TO">TO</option>
            </select>		 	 
          </td>
        </tr>
        <tr>
          <td class="dataLabel" width="65">In&iacute;cio:</td>
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
          <td width="100" class="tabDetailViewDF">
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
      </table>
    <td>
  </tr>
</table>
<br/>
<input class="button" title="Emite o relatório" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="return wdCarregarRelatorio()" />&nbsp;<input class="button" title="Gera a relação de Emails" name="btnRelacao" type="button" id="btnRelacao" value="Relação de Emails" style="width:100px" onclick="return wdGerarRelacao()" />
<br />
<br />	   	   		   		

<div id="resultado"></div>
<div id="exibe_lista"></div>
		
</form>