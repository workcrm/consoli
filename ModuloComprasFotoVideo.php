<?php
###########
## Módulo para Relatorio das Compras de Foto e video
## Criado: 20/06/2012 - Maycon Edinger
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

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="javascript">

//*** SE FOR IMPRESSÃO
function wdCarregarRelatorio() 
{

	var Form;
	Form = document.cadastro;
   
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
	var edtTipoRel = document.getElementsByName('edtTipoRel');
   
	for (var i=0; i < edtTipoRel.length; i++) 
	{
		if (edtTipoRel[i].checked == true) 
		{
			edtTipoRel = edtTipoRel[i].value;
			break;
		}
	}	
	
	//Captura o valor referente a origem dos dados (formato antigo ou novo)
	var edtOrigemRel = document.getElementsByName('edtOrigemRel');
   
	for (var i=0; i < edtOrigemRel.length; i++) 
	{
		if (edtOrigemRel[i].checked == true) 
		{
			edtOrigemRel = edtOrigemRel[i].value;
			break;
		}
	}	
	
	//Monta a url do relatório		
	var urlRelatorio = './relatorios/CompraFotoVideoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&EventoId=' + cmbEventoIdValor + '&DataIni=' + data_inicial.value + '&DataFim=' + data_final.value + '&TipoRel=' + edtTipoRel + '&OrigemRel=' + edtOrigemRel;

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
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Compras do Foto e Vídeo</span>			  	
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
			<input class="button" title="Emite o relatório da posição de compra do foto e vídeo" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()" />   	   		   		
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
						<img src="image/bt_cadastro.gif" width="16" height="15"> Caso desejar, selecione um evento para exibir a posição de compra do foto e vídeo:
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="dataLabel">Evento:</td>
    <td colspan="3" class="tabDetailViewDF">
			<select name="cmbEventoId" id="cmbEventoId" style="width: 400px">                  
				<option value="0">--- Todos os Eventos ---</option>
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
		<td class="dataLabel">Tipo de Relatorio</td>
		<td colspan="3" class=tabDetailViewDF>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="110">
						<input type="radio" name="edtTipoRel" value="1" checked/> Resumido
					</td>
					<td>
						<input type="radio" name="edtTipoRel" value="2" /> Detalhado
					</td>					
				</tr>
			</table>		  				
		</td>
	</tr>
	<tr>
		<td class="dataLabel">Origem dos Dados</td>
		<td colspan="3" class=tabDetailViewDF>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="110">
						<input type="radio" name="edtOrigemRel" value="1" checked/> Modulo Novo
					</td>
					<td>
						<input type="radio" name="edtOrigemRel" value="2" /> Modulo Antigo
					</td>					
				</tr>
			</table>		  				
		</td>
	</tr>
</table>
</form>