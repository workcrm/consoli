<?php
###########
## Módulo para controle do envio do foto e vídeo
## Criado: 05/05/2011 - Maycon Edinger
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

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');  

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

//Efetua o lookup na tabela de Origens
//Monta o SQL de pesquisa
$lista_evento = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_evento = mysql_query($lista_evento);

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="javascript">

function wdVisualizarRelatorio() 
{
	
	var Form;
	Form = document.cadastro;
   
	//Recebe o valor do combo de evento
	var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
	var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
	
	//Recebe o valor do combo de fornecedor
	var cmbFornecedorIdIndice = Form.cmbFornecedorId.selectedIndex;
	var cmbFornecedorIdValor = Form.cmbFornecedorId.options[cmbFornecedorIdIndice].value
	
	if (Form.edtDataIni.value == 0) 
	{
		
		alert('É necessário informar a data inicial !');
		Form.edtDataIni.focus();
		return false;			

	}

	if (Form.edtDataIni.value == 0) 
	{
		
		alert('É necessário informar a data final !');
		Form.edtDataFim.focus();
		return false;

	}			
		
	if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) 
	{
			
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
	}
	
	//Captura o valor referente ao radio button selecionado
	var edtEstagioValor = document.getElementsByName('edtEstagio');
   
	for (var i=0; i < edtEstagioValor.length; i++) 
	{
     
		if (edtEstagioValor[i].checked == true) 
		{
			edtEstagioValor = edtEstagioValor[i].value;
			break;
		}
   
	}
	
	//Captura o valor referente ao radio button selecionado
	var edtAtrasoValor = document.getElementsByName('edtAtraso');
   
	for (var i=0; i < edtAtrasoValor.length; i++) 
	{
     
		if (edtAtrasoValor[i].checked == true) 
		{
			edtAtrasoValor = edtAtrasoValor[i].value;
			break;
		}
   
	}
  
	//Monta a url a acessar	 
	var urlCarrega = 'ModuloControleFotoVideoLista.php?EventoId=' + cmbEventoIdValor + '&Estagio=' + edtEstagioValor + '&Atraso=' + edtAtrasoValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&FornecedorId=' + cmbFornecedorIdValor;
	
	//Acessa a listagem das contas
	wdCarregarFormulario(urlCarrega,'resultado');		

}


//*** SE FOR IMPRESSÃO
function wdCarregarRelatorio() 
{

	var Form;
	Form = document.cadastro;
   
	//Recebe o valor do combo de evento
	var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
	var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
	
	//Recebe o valor do combo de fornecedor
	var cmbFornecedorIdIndice = Form.cmbFornecedorId.selectedIndex;
	var cmbFornecedorIdValor = Form.cmbFornecedorId.options[cmbFornecedorIdIndice].value
	
	if (Form.edtDataIni.value == 0) 
	{
		
		alert('É necessário informar a data inicial !');
		Form.edtDataIni.focus();
		return false;			

	}

	if (Form.edtDataIni.value == 0) 
	{
		
		alert('É necessário informar a data final !');
		Form.edtDataFim.focus();
		return false;

	}			
		
	if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) 
	{
			
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
	}
	
	//Captura o valor referente ao radio button selecionado
	var edtEstagioValor = document.getElementsByName('edtEstagio');
   
	for (var i=0; i < edtEstagioValor.length; i++) 
	{
     
		if (edtEstagioValor[i].checked == true) 
		{
			edtEstagioValor = edtEstagioValor[i].value;
			break;
		}
   
	}

	//Captura o valor referente ao radio button selecionado
	var edtAtrasoValor = document.getElementsByName('edtAtraso');
   
	for (var i=0; i < edtAtrasoValor.length; i++) 
	{
     
		if (edtAtrasoValor[i].checked == true) 
		{
			edtAtrasoValor = edtAtrasoValor[i].value;
			break;
		}
   
	}	
		
	//Monta a url do relatório		
	var urlRelatorio = './relatorios/ControleFotoVideoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&EventoId=' + cmbEventoIdValor + '&Estagio=' + edtEstagioValor + '&Atraso=' + edtAtrasoValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&FornecedorId=' + cmbFornecedorIdValor;;
	
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
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Controle de Envio do Foto e Vídeo</span>			  	
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
					<td class="dataLabel" width="130">
						Entrega Cliente In&iacute;cio:
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
				<tr>
					<td class="dataLabel">
						Evento
					</td>
					<td colspan="3" class="tabDetailViewDF">
						<select name="cmbEventoId" id="cmbEventoId" style="width: 450px">
							<option value="0">-- Selecione uma opção --</option>    
							<?php 
								while ($lookup_evento = mysql_fetch_object($dados_evento)) 
								{ 
							?>
							<option value="<?php echo $lookup_evento->id ?>"><?php echo $lookup_evento->id . " - " . $lookup_evento->nome ?></option>
							<?php 
								} 
							?>
						</select>		  				
					</td>
				</tr>
				<tr valign="middle">
					<td class="dataLabel">
						Fornecedor
					</td>
					<td colspan="3" class="tabDetailViewDF">                  
						<select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
							<option value="0">--- Selecione uma Opção ---</option>
							<?php 
								//Monta o while para gerar o combo de escolha
								while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) 
								{ 
							?>
							<option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . ' - ' . $lookup_fornecedor->nome ?></option>
							<?php 
							
								} 
								
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dataLabel">
						Estágio
					</td>
					<td colspan="3" class="tabDetailViewDF">
						<table width="530" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="110">
									<input type="radio" name="edtEstagio" value="0" checked="checked" /> Todos
								</td>
								<td width="110">
									<input type="radio" name="edtEstagio" value="1" /> Em Atraso
								</td>
								<td width="110">
									<input type="radio" name="edtEstagio" value="2" /> Enviado
								</td>
								<td>
									<input type="radio" name="edtEstagio" value="3" /> Aguardando
								</td>						
							</tr>
						</table>		  				
					</td>
				</tr>   
				<tr>
					<td class="dataLabel">
						Tipo de Atraso
					</td>
					<td colspan="3" class="tabDetailViewDF">
						<table width="530" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="220">
									<input type="radio" name="edtAtraso" value="1" checked="checked" /> Envio ao Cliente
								</td>
								<td>
									<input type="radio" name="edtAtraso" value="2" /> Retorno do Laboratório
								</td>						
							</tr>
						</table>		  				
					</td>
				</tr>
			</table>
		<td>
	</tr>
</table>
<br/>
<input class="button" title="Visualiza na tela" name="btnVisualizar" type="button" id="btnVisualizar" value="Visualizar na Tela" style="width:100px" onclick="wdVisualizarRelatorio()" />
<input class="button" title="Emite o relatório da posição financeira dos formandos" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()" />
<br />
<br />	   	   		   		

<div id="resultado"></div>
		
</form>