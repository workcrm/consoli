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
include "./include/ManipulaDatas.php";

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

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

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="javascript">

function wdVisualizarRelatorio() {
	
  var Form;
	Form = document.cadastro;
	
	if (Form.edtDataIni.value == 0) {
		alert('É necessário informar a data inicial !');
		Form.edtDataIni.focus();
    return false;
 	}
	if (Form.edtDataFim.value == 0) {
		alert('É necessário informar a data final !');
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

	if (ano_inicial > ano_final){
		alert("A data inicial deve ser menor que a data final."); 
		data_inicial.focus();
		return false
	} else {
		if (ano_inicial == ano_final){
	 	if (mes_inicial > mes_final){
	  	alert("A data inicial deve ser menor que a data final.");
				data_final.focus();
				return false
			} else {
				if (mes_inicial == mes_final){
					if (dia_inicial > dia_final){
						alert("A data inicial deve ser menor que a data final.");
						data_final.focus();
						return false
					}
				}
			}
		}
	}
  
  //Monta a url a acessar	 
  var urlCarrega = 'ValeRelatorioPeriodoLista.php?DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

  //Acessa a listagem das contas
  wdCarregarFormulario(urlCarrega,'resultado');		

}


//*** SE FOR IMPRESSÃO
function wdCarregarRelatorio() {

  var Form;
	Form = document.cadastro;
	
	if (Form.edtDataIni.value == 0) {
		alert('É necessário informar a data inicial !');
		Form.edtDataIni.focus();
    return false;
 	}
	if (Form.edtDataFim.value == 0) {
		alert('É necessário informar a data final !');
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

	if (ano_inicial > ano_final){
		alert("A data inicial deve ser menor que a data final."); 
		data_inicial.focus();
		return false
	} else {
		if (ano_inicial == ano_final){
	 	if (mes_inicial > mes_final){
	  	alert("A data inicial deve ser menor que a data final.");
				data_final.focus();
				return false
			} else {
				if (mes_inicial == mes_final){
					if (dia_inicial > dia_final){
						alert("A data inicial deve ser menor que a data final.");
						data_final.focus();
						return false
					}
				}
			}
		}
	}
		
  //Monta a url do relatório		
  var urlRelatorio = './relatorios/ValePeriodoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

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
		      <td width="750">
			    <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Relação de Vales emitidos por período</span></td>
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
			<span >
      <input name="Button" type="button" class="button" id="Submit" title="Novo Vale" value="Novo Vale" onclick="wdCarregarFormulario('ValeCadastra.php?headers=1','conteudo')" />
    	</span>
		</td>
  </tr>
</table>

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
    <td class="dataLabel" width="65">
      In&iacute;cio:
    </td>
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
<table cellspacing="0" cellpadding="0" width="100%" border="0">
  <tr>
    <td>
      <br/>
				<input class="button" title="Visualizar na tela" name="btnVisualizar" type="button" id="btnVisualizar" value="Visualizar na Tela" style="width:100px" onclick="wdVisualizarRelatorio()" />
				<input class="button" title="Emite o relatório" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()" />
      	<br />
      	<br />	   	   		   		
 		</td>   
  </tr>
  <tr>
    <td>
      <div id="resultado"></div>
    </td>
  </tr> 
</table>
</form>