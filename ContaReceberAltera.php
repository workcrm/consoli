<?php 
###########
## Módulo para alteração de conta a receber
## Criado: 17/05/2007 - Maycon Edinger
## Alterado: 11/07/2007 - Maycon Edinger
## Alterações:
## 06/06/2007 - Implementado todos os novos campos solicitados
## 19/06/2007 - Aplicado objeto para campo money
## 03/07/2007 - Implementado campo para condição de pagamento
## 05/07/2007 - Implementado para incluir o cheque na conta
## 11/07/2007 - Implementado campo para cadastro de subgrupos e nro do documento
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{

	header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

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

//Efetua o lookup na tabela de eventos de pregao
//Monta o sql de pesquisa
$lista_eventos_pregao = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND tipo = 3 ORDER BY nome";
//Executa a query
$dados_eventos_pregao = mysql_query($lista_eventos_pregao);

//Monta o lookup aa tabela de categorias
//Monta o SQL
$lista_categoria = "SELECT * FROM categoria_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '2' ORDER BY nome";
//Executa a query
$dados_categoria = mysql_query($lista_categoria);

//Monta o lookup da tabela de grupos
//Monta o SQL
$lista_grupo = "SELECT * FROM grupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_grupo = mysql_query($lista_grupo);

//Monta o lookup da tabela de clientes (para a pessoa_id)
//Monta o SQL
$lista_cliente = "SELECT id, nome FROM clientes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_cliente = mysql_query($lista_cliente);

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//Monta o lookup da tabela de colaboradores (para a pessoa_id)
//Monta o SQL
$lista_colaborador = "SELECT id, nome FROM colaboradores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_colaborador = mysql_query($lista_colaborador);

//Monta o lookup da tabela de cheques
//Monta o SQL
$lista_cheque = "SELECT id, numero FROM cheques WHERE empresa_id = $empresaId ORDER BY data_vencimento DESC";
//Executa a query
$dados_cheque = mysql_query($lista_cheque);

//Monta o lookup da tabela de subgrupos
//Monta o SQL
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
	ID = document.getElementById(id);
	ID.style.display = "none";
}


function wdExibir() 
{

	//Captura o valor referente ao radio button selecionado
	var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
   
	for (var i=0; i < edtTipoPessoaValor.length; i++) 
	{
		if (edtTipoPessoaValor[i].checked == true) 
		{
			edtTipoPessoaValor = edtTipoPessoaValor[i].value;
			break;
		}
	
	}

	if (edtTipoPessoaValor == 1) 
	{
		IDCli = document.getElementById(20);
		IDFor = document.getElementById(30);
		IDCol = document.getElementById(40);
		IDFor.style.display = "none";
		IDCol.style.display = "none";
		IDCli.style.display = "inline";
	}
	
	if (edtTipoPessoaValor == 2) 
	{
		IDCli = document.getElementById(20);
		IDFor = document.getElementById(30);
		IDCol = document.getElementById(40);
		IDFor.style.display = "inline";
		IDCol.style.display = "none";
		IDCli.style.display = "none";		
	}
	
	if (edtTipoPessoaValor == 3) 
	{
		IDCli = document.getElementById(20);
		IDFor = document.getElementById(30);
		IDCol = document.getElementById(40);
		IDFor.style.display = "none";
		IDCol.style.display = "inline";
		IDCli.style.display = "none";		
	
	}

}

//Função para postagem das alterações
function wdSubmitContaAltera() 
{
   
	var Form;
	Form = document.frmContaAltera;
   
	//Verifica se foi informada a data da conta a receber
	if (Form.edtData.value.length == 0) 
	{
		alert("É necessário Informar a Data !");
		Form.edtData.focus();
		return false;
	}
  
	//Verifica se foi informado a descrição da conta 
	if (Form.edtDescricao.value.length == 0) 
	{
		alert("É necessário Informar a Descrição da conta a receber!");
		Form.edtDescricao.focus();
		return false;
	}
   
	//Caso não informou a conta-caixa
	if (Form.cmbSubgrupoId.value == 0) 
	{
		alert("É necessário selecionar uma Conta-caixa !");
		Form.cmbSubgrupoId.focus();
		return false;
	}
  
	//Verifica se foi informado o centro de custo  
	if (Form.cmbGrupoId.value == 0) 
	{
		alert("É necessário selecionar um Centro de Custo para a Conta !");
		Form.cmbGrupoId.focus();
		return false;
	}
  
	//Captura o valor referente ao radio button do tipo de pessoa
	var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
  
	for (var i=0; i < edtTipoPessoaValor.length; i++) 
	{
		
		if (edtTipoPessoaValor[i].checked == true) 
		{
		
			edtTipoPessoaValor = edtTipoPessoaValor[i].value;
			break;
		}
	
	}

	//verifica se nao foi escolhido um evento
	if (Form.cmbEventoId.value == 0) 
	{
  
		if (edtTipoPessoaValor == 1) 
		{
			if (Form.cmbClienteId.value == 0) 
			{
			
				alert("É necessário selecionar um Cliente !");
				Form.cmbClienteId.focus();
				return false;
			}
		}
  	
		if (edtTipoPessoaValor == 2) 
		{
			
			if (Form.cmbFornecedorId.value == 0) 
			{
				
				alert("É necessário selecionar um Fornecedor !");
				Form.cmbFornecedorId.focus();
				return false;
			}
		
		}
  	
		if (edtTipoPessoaValor == 3) 
		{
		  
			if (Form.cmbColaboradorId.value == 0) 
			{
			
				alert("É necessário selecionar um Colaborador !");
				Form.cmbColaboradorId.focus();
				return false;
			
			}
		}
  
	}   
	
	else   
	
	{
    
		//Captura o valor referente ao radio button do tipo de conta
		var edtTipoContaValor = document.getElementsByName('edtTipoConta');
    
		for (var i=0; i < edtTipoContaValor.length; i++) 
		{
		
			if (edtTipoContaValor[i].checked == true) 
			{
				
				edtTipoContaValor = edtTipoContaValor[i].value;
				break;
			
			}
		
		}
    
		//Caso tenha escolhido o tipo de conta de formatura
		if (edtTipoContaValor == 3) 
		{
    
			//Caso especificou um evento, porém não um formando
			if (Form.cmbFormandoId.value == 0) 
			{
			
				alert("É necessário selecionar um formando !");      
				return false;
			} 
    
		}
    
	}

	//Verifica se foi informado um numero de documento para o boleto
	if (Form.edtNroDocumento.value.length == 0) 
	{

		alert("É necessário Informar o Número do Documento (Será utilizado para o boleto) !");
		Form.edtNroDocumento.focus();
		return false;
	}
  
	//Verifica se foi informado o valor original da conta a receber
	if (Form.edtValorOriginal.value.length == 0) 
	{

		alert("É necessário Informar o Valor Original da conta a receber!");
		Form.edtValorOriginal.focus();
		return false;
  
	}

	//Verifica se foi informado o valor da conta a receber
	if (Form.edtValor.value.length == 0) 
	{
		alert("É necessário Informar o Valor da conta a receber!");
		Form.edtValor.focus();
		return false;
	}
  
	//Verifica se foi informado a data do vencimento   
	if (Form.edtDataVencimento.value.length == 0) 
	{
		alert("É necessário Informar a Data do Vencimento !");
		Form.edtDataVencimento.focus();
		return false;
	}
   
	return true;
}

function busca_formandos()
{
  
	var Form;
	Form = document.frmContaAltera;   
  
	if (Form.cmbEventoId.value != 0)
	{
  
		Form.edtTipoPessoa[0].disabled = true;
		Form.edtTipoPessoa[1].disabled = true;
		Form.edtTipoPessoa[2].disabled = true;
		
		Form.cmbClienteId.disabled = true;
		Form.cmbFornecedorId.disabled = true;
		Form.cmbColaboradorId.disabled = true;
    
		//Captura o valor referente ao radio button do tipo de conta
		var edtTipoContaValor = document.getElementsByName('edtTipoConta');
    
		for (var i=0; i < edtTipoContaValor.length; i++) 
		{

			if (edtTipoContaValor[i].checked == true) 
			{
			   edtTipoContaValor = edtTipoContaValor[i].value;
			   break;
			}
		}
    
		//Caso tenha escolhido o tipo por formatura
		if (edtTipoContaValor == 3) 
		{    
      
			eventoId = Form.cmbEventoId.value;

			wdCarregarFormulario('ContaReceberBuscaFormando.php?EventoId=' + eventoId,'recebe_formandos');
     
		}
   
	}	 
	
	else 
	
	{
    
		Form.edtTipoPessoa[0].disabled = false;
		Form.edtTipoPessoa[1].disabled = false;
		Form.edtTipoPessoa[2].disabled = false;

		Form.cmbClienteId.disabled = false;
		Form.cmbFornecedorId.disabled = false;
		Form.cmbColaboradorId.disabled = false;

		document.getElementById("recebe_formandos").innerHTML = "[ Selecione um evento ]";
      
	}
     
}

//Função para escolha do tipo de conta a receber
function wdTipoConta()
{
  
	var Form;
	Form = document.frmContaAltera; 

	//Captura o valor referente ao radio button do tipo de conta
	var edtTipoContaValor = document.getElementsByName('edtTipoConta');

	for (var i=0; i < edtTipoContaValor.length; i++) 
	{

		if (edtTipoContaValor[i].checked == true) 
		{ 

			edtTipoContaValor = edtTipoContaValor[i].value;
			break;

		}
	}
  
	//Caso tenha escolhido o tipo por sacado normal
	if (edtTipoContaValor == 1) 
	{
	  
		//Desativa o combo de evento
		Form.cmbEventoId.disabled = true;

		//Ativa os combos de sacado
		Form.edtTipoPessoa[0].disabled = false;
		Form.edtTipoPessoa[1].disabled = false;
		Form.edtTipoPessoa[2].disabled = false;

		Form.cmbClienteId.disabled = false;
		Form.cmbFornecedorId.disabled = false;
		Form.cmbColaboradorId.disabled = false;

		Form.edtTipoPessoa[0].checked = true; 
		IDCli = document.getElementById(20);
		IDFor = document.getElementById(30);
		IDCol = document.getElementById(40);
		IDFor.style.display = "none";
		IDCol.style.display = "none";
		IDCli.style.display = "inline";
	
	}

	//Caso tenha escolhido outro tipo de sacado
	if (edtTipoContaValor > 1) 
	{
	  
		//Ativa o combo de evento
		Form.cmbEventoId.disabled = false;
		
		//Desativa os combos de sacado
		Form.edtTipoPessoa[0].disabled = true;
		Form.edtTipoPessoa[1].disabled = true;
		Form.edtTipoPessoa[2].disabled = true;
		
		Form.cmbClienteId.disabled = true;
		Form.cmbFornecedorId.disabled = true;
		Form.cmbColaboradorId.disabled = true;
	
	}
	
	//Caso tenha escolhido o tipo por pregao
	if (edtTipoContaValor == 4) 
	{
	  
		//Desativa o combo de evento
		Form.cmbEventoId.disabled = false;

		//Ativa os combos de sacado
		Form.edtTipoPessoa[0].disabled = false;
		Form.edtTipoPessoa[1].disabled = false;
		Form.edtTipoPessoa[2].disabled = false;

		Form.cmbClienteId.disabled = false;
		Form.cmbFornecedorId.disabled = false;
		Form.cmbColaboradorId.disabled = false;

		Form.edtTipoPessoa[0].checked = true; 
		IDCli = document.getElementById(20);
		IDFor = document.getElementById(30);
		IDCol = document.getElementById(40);
		IDFor.style.display = "none";
		IDCol.style.display = "none";
		IDCli.style.display = "inline";
	
	}
  
}
</script>

<form name="frmContaAltera" action="sistema.php?ModuloNome=ContaReceberAltera" method="post" onsubmit="return wdSubmitContaAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Conta a Receber</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">

						<?php
						
							//Verifica se a flag está vindo de uma postagem para liberar a alteração
							if($_POST["Submit"])
							{

								//Recupera os valores do formulario e alimenta as variáveis
								$id = $_POST["Id"];
								$edtData = DataMySQLInserir($_POST["edtData"]);
            
								$edtTipoConta = $_POST["edtTipoConta"];
            
								//Verifica o tipo da conta
								//Se for evento social normal
								if ($edtTipoConta == 2)
								{
              
									//Define o sacado como sendo o evento
									$edtTipoPessoa = 5;
              
								}
            
								else if ($edtTipoConta == 3)
								
								{
            
									//Define o sacado como sendo o formando
									$edtTipoPessoa = 4;
            
								} 
								
								//Verifica o tipo da conta
								//Se for evento pregao
								//if ($edtTipoConta == 4)
								//{
              
									//Define o sacado como sendo o evento
									//$edtTipoPessoa = 1;
									//A pessoa ID é pega do combo de CLIENTE
									//$cmbPessoaId = 	$_POST["cmbClienteId"];
              
								//}
							
								else
								
								{
              
									//O tipo de pessoa pega do radio escolhido
									$edtTipoPessoa = $_POST["edtTipoPessoa"];
              
								}
						
								//Se o tipo de pessoa for 1 é CLIENTE 
								if ($edtTipoPessoa == 1) 
								{
							
									//A pessoa ID é pega do combo de CLIENTE
									$cmbPessoaId = 	$_POST["cmbClienteId"];
								
								}
            
								//Se o tipo de pessoa for 2 é FORNECEDOR
								if ($edtTipoPessoa == 2) 
								{
								
									//A pessoa ID é pega do combo de FORNECEDOR
									$cmbPessoaId = 	$_POST["cmbFornecedorId"];
								}

								//Se o tipo de pessoa for 3 é COLABORADOR
								if ($edtTipoPessoa == 3) 
								{
								
									//A pessoa ID é pega do combo de COLABORADOR
									$cmbPessoaId = 	$_POST["cmbColaboradorId"];
								}
            
								//Se o tipo de pessoa for 4 é FORMANDO
								if ($edtTipoPessoa == 4) 
								{
							
									//A pessoa ID é pega do combo de FORMANDO
									$cmbPessoaId = 	$_POST["cmbFormandoId"];
              
								}
            
								//Se o tipo de pessoa for 5 é EVENTO
								if ($edtTipoPessoa == 5) 
								{	
							
									//A pessoa ID é pega do combo de FORMANDO
									$cmbPessoaId = 	$_POST["cmbEventoId"];
              
								}
											          
								$cmbGrupoId = $_POST["cmbGrupoId"];
								$cmbSubgrupoId = $_POST["cmbSubgrupoId"];
								$cmbEventoId = $_POST["cmbEventoId"];
								$cmbFormandoId = $_POST["cmbFormandoId"];
								$cmbCategoriaId = $_POST["cmbCategoriaId"];
								$edtDescricao = $_POST["edtDescricao"];
								$edtNroDocumento = $_POST["edtNroDocumento"];
								$edtValorOriginal = MoneyMySQLInserir($_POST["edtValorOriginal"]);
								$edtValor = MoneyMySQLInserir($_POST["edtValor"]);
								$edtValorMultaJuro = MoneyMySQLInserir($_POST["edtValorMultaJuro"]);
								$edtDataVencimento = DataMySQLInserir($_POST["edtDataVencimento"]);
								$edtObservacoes = $_POST["edtObservacoes"];	
								$edtOperadorId = $usuarioId;

								$edtValorBoleto = MoneyMySQLInserir($_POST["edtValorBoleto"]);
								$edtTaxaMulta = MoneyMySQLInserir($_POST["edtValorMulta"]);
								$edtTaxaJuros = MoneyMySQLInserir($_POST["edtValorJuros"]);
								
								//Parte financeira
								$edtRestricao = $_POST["edtRestricao"];	
								$edtObsFinanceiro = $_POST['edtObsFinanceiro'];
								

								//Executa a query de alteração da conta
								$sql = mysql_query("UPDATE 
																			contas_receber 
																		SET 
																			tipo_pessoa = '$edtTipoPessoa',
																			pessoa_id = '$cmbPessoaId', 
																			grupo_conta_id = '$cmbGrupoId',
																			subgrupo_conta_id = '$cmbSubgrupoId',
																			evento_id = '$cmbEventoId',
																			formando_id = '$cmbFormandoId', 
																			descricao = '$edtDescricao',
																			nro_documento = '$edtNroDocumento',
																			valor_original = '$edtValorOriginal',
																			valor = '$edtValor',
																			valor_boleto = '$edtValorBoleto',
																			valor_multa_juros = '$edtValorMultaJuro',
																			taxa_multa = '$edtTaxaMulta',
																			taxa_juros  = '$edtTaxaJuros',
																			data_vencimento = '$edtDataVencimento',  
																			observacoes = '$edtObservacoes',
																			alteracao_timestamp = now(),
																			alteracao_operador_id = '$edtOperadorId',
																			restricao = '$edtRestricao',
																			obs_financeiro = '$edtObsFinanceiro'
																		WHERE 
																			id = '$id' ");	
                                
								$data_atualizacao = date("Y-m-d", mktime());
								
								//Monta o SQL para alteração do boleto de acordo com as modificações da conta
								$sql_boleto = mysql_query(" UPDATE 
																							boleto 
																						SET 
																							valor_boleto = '$edtValor',
																							data_vencimento = '$edtDataVencimento',
																							reajustado = 0,
																							data_atualizacao = '$data_atualizacao'
																						WHERE 
																							conta_receber_id = '$id' ");		
                                                                                                 

								//Exibe a mensagem de alteração com sucesso
								echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Conta a Receber alterada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
							}
         	
			//RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
			//Captura o id a alterar
			if ($_GET["Id"]) 
			{
				$ContaId = $_GET["Id"];
          
			} 
		  
			else 
		  
			{
          
				$ContaId = $_POST["Id"];
			}
          
			//Monta o sql para busca da conta
			$sql = "SELECT * FROM contas_receber WHERE id = $ContaId";
          
			//Executa a query
			$resultado = mysql_query($sql);
          
			//Monta o array dos dados
			$campos = mysql_fetch_array($resultado);
          
			//Efetua o switch para o campo de tipo de pessoa
			switch ($campos[tipo_pessoa]) 
			{
				case 01: 
					$pess_1 = "checked";	
					$pess_2 = ""; 
					$pess_3 = "";		  
					$tbcli = "inline";
					$tbfor = "none";
					$tbcol = "none";
				break;
				case 02: 
					$pess_1 = "";		
					$pess_2 = "checked";	
					$pess_3 = "";  
					$tbcli = "none";
					$tbfor = "inline";
					$tbcol = "none";						
					break;
				case 03: 
					$pess_1 = "";		
					$pess_2 = "";	
					$pess_3 = "checked";  
					$tbcli = "none";
					$tbfor = "none";
					$tbcol = "inline";
				break;
			}
          								      
          
			//Efetua o switch para o campo de tipo de recebimento
			switch ($campos[tipo_recebimento]) 
			{
				case 01: 
				  $pag_1 = "checked";	
				  $pag_2 = ""; 		  
				break;
				case 02: 
				  $pag_1 = "";		
				  $pag_2 = "checked";  
				break;
			}

			//Efetua o switch para o campo de restricao
			switch ($campos["restricao"]) 
			{
				case 0: 
					$restricao_1 = "checked='checked'";	
					$restricao_2 = ""; 
				break;
				case 1: 
					$restricao_1 = "checked='checked'";	
					$restricao_2 = ""; 
				break;
				case 2: 
					$restricao_1 = "";		
					$restricao_2 = "checked='checked'";  
				break;
			}

		?>
    <table cellspacing="0" cellpadding="0" width="100%" border="0">
			<tr>
				<td width="100%"> </td>
			</tr>
			<tr>
      	<td style="PADDING-BOTTOM: 2px">
      		<input name="Id" type="hidden" value="<?php echo $ContaId ?>" />
					<input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Conta a Receber" />
					<input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações" />
				</td>
				<td width="36" align="right">
					<input class="button" title="Retorna a exibição do registro" name="btnVoltar" type="button" id="btnVoltar" value="Retornar a Conta a Receber" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $ContaId ?>','conteudo')" />						
				</td>
     	</tr>
    </table>
           
    <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
			<tr>
				<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
					<table cellspacing="0" cellpadding="0" width="100%" border="0">
						<tr>
							<td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Informe os dados da Conta a Receber e clique em [Salvar Conta a Receber] <br />
								<br />
								<span class="style1">Aten&ccedil;&atilde;o:</span> Esta transa&ccedil;&atilde;o ser&aacute; monitorada pelo sistema e ser&aacute; gerado um log da atividade para fins de auditoria.
							</td>
		     		</tr>
       		</table>						 
				</td>
     	</tr>
			<tr>
				<td width="140" class="dataLabel">
					<span class="dataLabel">Data:</span>             
				</td>
				<td colspan="4" class="tabDetailViewDF">
					<?php

						//Define a data do formulário
						// $objData->strFormulario = "frmContaAltera";  
						// $objData->strNome = "edtData";
						// $objData->strRequerido = true;
						// $objData->strValor = DataMySQLRetornar($campos[data]);
						// $objData->CriarData();

					?>
					<input id="edtData" name="edtData" type="text" value="<?php echo DataMySQLRetornar($campos['data']) ?>" readonly="readonly" style="width: 70px; background-color: #ddd" />
				</td>
			</tr>           

			<tr>
				<td width="140" class="dataLabel">Descrição:</td>
				<td colspan="4" valign="middle" class="tabDetailViewDF">
					<input name="edtDescricao" type="text" class="requerido" id="edtDescricao" style="width: 400px" size="84" maxlength="80" value="<?php echo $campos[descricao] ?>" />
				</td>
			</tr>
			<tr>
				<td width="140" class="dataLabel">Conta-caixa:</td>
				<td colspan="4" valign="middle" class="tabDetailViewDF">
					<select name="cmbSubgrupoId" id="cmbSubgrupoId" style="width:350px">
						<?php while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo)) { ?>
						<option <?php if ($lookup_subgrupo->id == $campos[subgrupo_conta_id]) {
                        echo " selected ";
						} ?>
						value="<?php echo $lookup_subgrupo->id ?>"><?php echo $lookup_subgrupo->id . " - " . $lookup_subgrupo->nome ?></option>
						<?php } ?>
					</select>								 						 
				</td>
			</tr>
			<tr>
				<td width="140" class="dataLabel">Centro de Custo:</td>
				<td colspan="4" valign="middle" class="tabDetailViewDF">
					<select name="cmbGrupoId" id="cmbGrupoId" style="width:350px">
						<?php while ($lookup_grupo = mysql_fetch_object($dados_grupo)) { ?>
						<option <?php if ($lookup_grupo->id == $campos[grupo_conta_id]) {
                        echo " selected ";
						} ?>
						value="<?php echo $lookup_grupo->id ?>"><?php echo $lookup_grupo->id . " - " . $lookup_grupo->nome ?></option>
						<?php } ?>
					</select>								 						 
				</td>
			</tr>
			<tr>
				<td width="140" valign="top" class="dataLabel">Tipo de Conta:</td>
				<td colspan="4" class="tabDetailViewDF">
				<?php
              
					//Verifica o tipo de conta
					if ($campos["tipo_pessoa"] >0 AND $campos["tipo_pessoa"] < 4)
					{

						$tipo_1 = "checked='checked'";
						$tipo_2 = "";
						$tipo_3 = ""; 

					}

					//Verifica o tipo de conta se é de evento social
					if ($campos["tipo_pessoa"] == 5)
					{

						$tipo_1 = "";
						$tipo_2 = "checked='checked'";
						$tipo_3 = "";

					?>
                 
					<script language="javascript">
						var Form;
						Form = document.frmContaAltera;
						IDCli = document.getElementById(20);
						IDFor = document.getElementById(30);
						IDCol = document.getElementById(40);
						IDFor.style.display = "none";
						IDCol.style.display = "none";
						IDCli.style.display = "inline";

						//Desativa os combos de sacado
						Form.edtTipoPessoa[0].disabled = true;
						Form.edtTipoPessoa[1].disabled = true;
						Form.edtTipoPessoa[2].disabled = true;

						Form.cmbClienteId.disabled = true;
						Form.cmbFornecedorId.disabled = true;
						Form.cmbColaboradorId.disabled = true;
					</script>
                 
                 <?php
                  
                //Fecha o if de se é evento social  
                }
                
                //Verifica o tipo de conta
                if ($campos["tipo_pessoa"] == 4)
                {
                  
                 $tipo_1 = "";
                 $tipo_2 = "";
                 $tipo_3 = "checked='checked'"; 
                 
                 ?>
                 <script language="javascript">
                   var Form;
                   Form = document.frmContaAltera;
  
                   IDCli = document.getElementById(20);
              		 IDFor = document.getElementById(30);
              	   IDCol = document.getElementById(40);
              	   IDFor.style.display = "none";
              	   IDCol.style.display = "none";
              	   IDCli.style.display = "inline";
                   
                   //Desativa os combos de sacado
                   Form.edtTipoPessoa[0].disabled = true;
                   Form.edtTipoPessoa[1].disabled = true;
                   Form.edtTipoPessoa[2].disabled = true;
                   
                   Form.cmbClienteId.disabled = true;
                   Form.cmbFornecedorId.disabled = true;
                   Form.cmbColaboradorId.disabled = true;
                 </script>
                 
                 <?php
                  
                }
              
              ?>
              <table cellpadding="0" cellspacing="0" width="100%">
                <tr valign="middle">
                  <td width="180" height="20">
                    <input type="radio" name="edtTipoConta" value="1" <?php echo $tipo_1 ?> onclick="wdTipoConta()" />
                      Cliente/Fornecedor/Colaborador
                  </td>
                  <td width="120" height="20">
                    <input type="radio" name="edtTipoConta" value="2" <?php echo $tipo_2 ?> onclick="wdTipoConta()" />
                      Evento Social
                  </td>
                  <td width="120" height="20">
                    <input type="radio" name="edtTipoConta" value="3" <?php echo $tipo_3 ?> onclick="wdTipoConta()" />
                      Evento Formatura
                  </td>
				  <td height="20">
                    <input type="radio" name="edtTipoConta" value="4" <?php echo $tipo_4 ?> onclick="wdTipoConta()" />
                      Pregão/Edital
                  </td>
                </tr>
              </table>
            </td>
          </tr> 
          
          <tr>
            <td width="140" valign="top" class="dataLabel">Tipo Pessoa:</td>
            <td colspan="4" class="tabDetailViewDF">          
              <table cellpadding="0" cellspacing="0">
                <tr valign="middle">
                  <td width="117" height="20">
                    <input type="radio" name="edtTipoPessoa" value="1" <?php echo $pess_1 ?> onclick="wdExibir()" />
                      Cliente
                  </td>
                  <td width="120" height="20">
                    <input type="radio" name="edtTipoPessoa" value="2" <?php echo $pess_2 ?> onclick="wdExibir()" />
                      Fornecedor
                  </td>
                  <td width="120" height="20">
                    <input type="radio" name="edtTipoPessoa" value="3" <?php echo $pess_3 ?> onclick="wdExibir()" />
                      Colaborador
                  </td>
                </tr>
              </table>
              
              
			<table id="20" cellpadding="0" cellspacing="0" style="display: <?php echo $tbcli ?>">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Cliente:<br/>
						<select name="cmbClienteId" id="cmbClienteId" style="width:350px">
							<option value="0">Selecione uma Opção</option>
							<?php 
								
								//Monta o while para gerar o combo de escolha
								while ($lookup_cliente = mysql_fetch_object($dados_cliente)) 
								{ 
							
							?>
							<option <?php if ($campos[tipo_pessoa] == 1) {
										 		if ($lookup_cliente->id == $campos[pessoa_id]) {
                        echo " selected ";
                      } 
											}
											?>
											value="<?php echo $lookup_cliente->id ?>"><?php echo $lookup_cliente->id . " - " . $lookup_cliente->nome ?> </option>
		                 <?php } ?>
		               </select>
                  </td>
                </tr>
              </table>

              <table id="30" cellpadding="0" cellspacing="0" style="display: <?php echo $tbfor ?>">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Fornecedor:</br>                  
		               <select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
		                 <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) { 
										 ?>
		                 <option <?php if ($campos[tipo_pessoa] == 2) {
										 		if ($lookup_fornecedor->id == $campos[pessoa_id]) {
                        echo " selected ";
                      } 
											}
											?>
											value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . " - " . $lookup_fornecedor->nome ?> </option>
		                 <?php } ?>
		               </select>
                  </td>
                </tr>
              </table>

              <table id="40" cellpadding="0" cellspacing="0" style="display: <?php echo $tbcol ?>">
                <tr valign="middle">
                  <td style="padding-top: 7px">
		               Colaborador:</br>                  
		               <select name="cmbColaboradorId" id="cmbColaboradorId" style="width:350px">
		                 <option value="0">Selecione uma Opção</option>
						 				 <?php 
											 //Monta o while para gerar o combo de escolha
											 while ($lookup_colaborador = mysql_fetch_object($dados_colaborador)) { 
										 ?>
		                 <option <?php if ($campos[tipo_pessoa] == 3) {
										 		if ($lookup_colaborador->id == $campos[pessoa_id]) {
                        echo " selected ";
                      } 
											}
											?>
											value="<?php echo $lookup_colaborador->id ?>"><?php echo $lookup_colaborador->id . " - " . $lookup_colaborador->nome ?> </option>
		                 <?php } ?>
		               </select>
                  </td>
                </tr>
              </table>              
				  	</td>
          </tr>
           
			  		<tr>
              <td class="dataLabel" width="50">Evento:</td>
              <td colspan="4" width="490" class="tabDetailViewDF">
								<select name="cmbEventoId" id="cmbEventoId" style="width: 400px" onchange="busca_formandos()">                  
								  <option value="0">Selecione uma Opção</option>
									<?php 
								    //Cria o componente de lookup de eventos
								    while ($lookup_eventos = mysql_fetch_object($dados_eventos)) { 
								  ?>
                   <option <?php if ($lookup_eventos->id == $campos[evento_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_eventos->id ?>"><?php echo $lookup_eventos->id . " - " . $lookup_eventos->nome ?></option>
        	      <?php } ?>
                </select>
              </td>
            </tr>
            
            <tr>
              <td class="dataLabel" width="50">Formando:</td>
              <td colspan="4" width="490" class="tabDetailViewDF">
								<div id="recebe_formandos">
                  [ Selecione um evento ] <input type="hidden" name="cmbFormandoId" id="cmbFormandoId" value="0">
                </div>
              </td>
            </tr>

           <tr>
             <td width="140" class="dataLabel">Nº do Documento:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
               <input name="edtNroDocumento" type="text" class="requerido" id="edtNroDocumento" style="width: 80px" maxlength="10" value="<?php echo $campos[nro_documento] ?>" />             
						 </td>
          </tr> 
          
          <tr>
            <td width="140" valign="top" class="dataLabel">Valor Original:</td>
            <td colspan="4" width="173" class="tabDetailViewDF">
							<?php
							//Acerta a variável com o valor a alterar
							$valor_alterar = str_replace(".",",",$campos[valor_original]);							
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValorOriginal";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = "$valor_alterar";
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();  
							?>														
						</td>
          </tr>
          
          <tr>
            <td width="140" valign="top" class="dataLabel">Valor a Receber: </td>
            <td width="173" class="tabDetailViewDF">
							<?php
							//Acerta a variável com o valor a alterar
							$valor_alterar = str_replace(".",",",$campos[valor]);							
							
							//Cria um objeto do tipo WDEdit 
							$objWDComponente = new WDEditReal();
							
							//Define nome do componente
							$objWDComponente->strNome = "edtValor";
							//Define o tamanho do componente
							$objWDComponente->intSize = 16;
							//Busca valor definido no XML para o componente
							$objWDComponente->strValor = "$valor_alterar";
							//Busca a descrição do XML para o componente
							$objWDComponente->strLabel = "";
							//Determina um ou mais eventos para o componente
							$objWDComponente->strEvento = "";
							//Define numero de caracteres no componente
							$objWDComponente->intMaxLength = 14;
							
							//Cria o componente edit
							$objWDComponente->Criar();  
							?>														
						</td>
            <td width="146" class="dataLabel">Data Vencimento:</td>
            <td colspan="2" class="tabDetailViewDF">
							<?php
							    //Define a data do formulário
							    $objData->strFormulario = "frmContaAltera";  
							    //Nome do campo que deve ser criado
							    $objData->strNome = "edtDataVencimento";
                  $objData->strRequerido = true;
							    //Valor a constar dentro do campo (p/ alteração)
							    $objData->strValor = DataMySQLRetornar($campos[data_vencimento]);
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
             <td width="140" class="dataLabel">Valor Juro e Multa:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
                <?php
					//Acerta a variável com o valor a alterar
					$valor_alterar = str_replace(".",",",$campos[valor_multa_juros]);							
					
					//Cria um objeto do tipo WDEdit 
					$objWDComponente = new WDEditReal();
					
					//Define nome do componente
					$objWDComponente->strNome = "edtValorMultaJuro";
					//Define o tamanho do componente
					$objWDComponente->intSize = 9;
					//Busca valor definido no XML para o componente
					$objWDComponente->strValor = "$valor_alterar";
					//Busca a descrição do XML para o componente
					$objWDComponente->strLabel = "";
					//Determina um ou mais eventos para o componente
					$objWDComponente->strEvento = "";
					//Define numero de caracteres no componente
					$objWDComponente->intMaxLength = 14;
					
					//Cria o componente edit
					$objWDComponente->Criar();
                ?>            
						 </td>       
          </tr>
          
          <tr>
             <td width="140" class="dataLabel">Custo do Boleto:</td>
             <td colspan="4" valign="middle" class="tabDetailViewDF">
                <?php
                  //Acerta a variável com o valor a alterar
    							$valor_alterar = str_replace(".",",",$campos[valor_boleto]);							
    							
    							//Cria um objeto do tipo WDEdit 
    							$objWDComponente = new WDEditReal();
    							
    							//Define nome do componente
    							$objWDComponente->strNome = "edtValorBoleto";
    							//Define o tamanho do componente
    							$objWDComponente->intSize = 9;
    							//Busca valor definido no XML para o componente
    							$objWDComponente->strValor = "$valor_alterar";
    							//Busca a descrição do XML para o componente
    							$objWDComponente->strLabel = "";
    							//Determina um ou mais eventos para o componente
    							$objWDComponente->strEvento = "";
    							//Define numero de caracteres no componente
    							$objWDComponente->intMaxLength = 14;
    							
    							//Cria o componente edit
    							$objWDComponente->Criar();
                ?>             
						 </td>       
          </tr>
          
          <tr>
            <td width="140" valign="top" class="dataLabel">Taxa Multa Atraso:</td>
            <td width="173" class="tabDetailViewDF">
							<?php 
  							//Acerta a variável com o valor a alterar
  							$valor_alterar = str_replace(".",",",$campos[taxa_multa]);							
  							
  							//Cria um objeto do tipo WDEdit 
  							$objWDComponente = new WDEditReal();
  							
  							//Define nome do componente
  							$objWDComponente->strNome = "edtValorMulta";
  							//Define o tamanho do componente
  							$objWDComponente->intSize = 9;
  							//Busca valor definido no XML para o componente
  							$objWDComponente->strValor = "$valor_alterar";
  							//Busca a descrição do XML para o componente
  							$objWDComponente->strLabel = "";
  							//Determina um ou mais eventos para o componente
  							$objWDComponente->strEvento = "";
  							//Define numero de caracteres no componente
  							$objWDComponente->intMaxLength = 14;
  							
  							//Cria o componente edit
  							$objWDComponente->Criar();  
							?>
              &nbsp;%																				
						</td>
            <td width="146" class="dataLabel">Taxa de Juros ao Mês:</td>
            <td colspan="2" class="tabDetailViewDF">
							<?php
							    //Acerta a variável com o valor a alterar
  							$valor_alterar = str_replace(".",",",$campos[taxa_juros]);							
  							
  							//Cria um objeto do tipo WDEdit 
  							$objWDComponente = new WDEditReal();
  							
  							//Define nome do componente
  							$objWDComponente->strNome = "edtValorJuros";
  							//Define o tamanho do componente
  							$objWDComponente->intSize = 9;
  							//Busca valor definido no XML para o componente
  							$objWDComponente->strValor = "$valor_alterar";
  							//Busca a descrição do XML para o componente
  							$objWDComponente->strLabel = "";
  							//Determina um ou mais eventos para o componente
  							$objWDComponente->strEvento = "";
  							//Define numero de caracteres no componente
  							$objWDComponente->intMaxLength = 14;
  							
  							//Cria o componente edit
  							$objWDComponente->Criar();
							?>
              &nbsp;%						
					</td>
				</tr> 
				<tr>
					<td width="140" valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
					<td colspan="4" class="tabDetailViewDF">
						<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"><?php echo $campos[observacoes] ?></textarea>    							
					</td>
				</tr>
				
			</table>
			<br/>
				<span class="TituloModulo">Informações Financeiras:</span>
        <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td width="140" valign="top" class="dataLabel">Situação:</td>
								<td colspan="3" valign="middle" class="tabDetailViewDF">
									<table width="500" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="130" height="20">
												<input name="edtRestricao" type="radio" value="1" <?php echo $restricao_1 ?> />&nbsp;&nbsp;<img src="image/bt_receber.gif" alt="Em Dia" />&nbsp;Em Dia
											</td>
											<td height="20">
												<input name="edtRestricao" type="radio" value="2" <?php echo $restricao_2 ?> />&nbsp;&nbsp;<img src="image/bt_pendente.gif" alt="Restrições Financeiras" />&nbsp;Restrições Financeiras
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="dataLabel">
									<span class="dataLabel">Observações:</span>						
								</td>
								<td colspan="3" class="tabDetailViewDF">
									<input name="edtObsFinanceiro" type="text" class="datafield" id="edtObsFinanceiro" style="width: 600px" maxlength="150" value="<?php echo $campos[obs_financeiro] ?>"/>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>  	 
			</form>
		</td>
	</tr>
</table>
<?php

//Verifica o tipo de conta
if ($campos["tipo_pessoa"] == 4)
{
  
 ?>
 <script language="javascript">
    var Form;
    Form = document.frmContaAltera;
    
    IDCli = document.getElementById(20);
    IDFor = document.getElementById(30);
    IDCol = document.getElementById(40);
    IDFor.style.display = "none";
    IDCol.style.display = "none";
    IDCli.style.display = "inline";
   
    //Desativa os combos de sacado
    Form.edtTipoPessoa[0].disabled = true;
    Form.edtTipoPessoa[1].disabled = true;
    Form.edtTipoPessoa[2].disabled = true;
    
    Form.cmbClienteId.disabled = true;
    Form.cmbFornecedorId.disabled = true;
    Form.cmbColaboradorId.disabled = true;
   
    //Captura o valor referente ao radio button do tipo de conta
    var edtTipoContaValor = document.getElementsByName('edtTipoConta');
    
    for (var i=0; i < edtTipoContaValor.length; i++) 
    {
      if (edtTipoContaValor[i].checked == true) 
      {
       edtTipoContaValor = edtTipoContaValor[i].value;
       break;
      }
    }  
      
    eventoId = Form.cmbEventoId.value;
    formandoId = <?php echo $campos["formando_id"] ?>;
     
    wdCarregarFormulario('ContaReceberBuscaFormando.php?EventoId=' + eventoId + '&FormandoId=' + formandoId,'recebe_formandos');
 </script>
 
<?php
  
}
              
//Verifica o tipo de conta se é de evento social
if ($campos["tipo_pessoa"] == 5)
{
  
?>
 
 <script language="javascript">
		var Form;
		Form = document.frmContaAltera;
		IDCli = document.getElementById(20);
		IDFor = document.getElementById(30);
		IDCol = document.getElementById(40);
		IDFor.style.display = "none";
		IDCol.style.display = "none";
		IDCli.style.display = "inline";
   
		//Desativa os combos de sacado
		Form.edtTipoPessoa[0].disabled = true;
		Form.edtTipoPessoa[1].disabled = true;
		Form.edtTipoPessoa[2].disabled = true;
   
		Form.cmbClienteId.disabled = true;
		Form.cmbFornecedorId.disabled = true;
		Form.cmbColaboradorId.disabled = true;
	</script>
 
	<?php
  
//Fecha o if de se é evento social  
}
?>