<?php 
###########
## Módulo para cadastro de Contas a Receber
## Criado: 19/02/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
###########


//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET['headers'] == 1) 
{
	header('Content-Type: text/html;  charset=ISO-8859-1',true);
}

//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';

//Recupera dos dados do ultimo evento trabalhado
$sql_boleto = "SELECT * FROM parametros_sistema";													  													  
							  
//Executa a query
$resultado_boleto = mysql_query($sql_boleto);

//Monta o array dos campos
$dados_boleto = mysql_fetch_array($resultado_boleto);

?>

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

function valida_form() 
{
  
	var Form;
	Form = document.cadastro;   
  
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

	//verifica se o tipo de conta é para sacado normal
	if (edtTipoContaValor == 1)
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

	//verifica se o tipo de conta é para pregao
	else if (edtTipoContaValor == 4)
	{
  
		if (Form.cmbClienteId.value == 0) 
		{
	
			alert("É necessário selecionar um Cliente !");
			Form.cmbClienteId.focus();
			return false;
		}	
	
	}
	
	else   
	
	{
       
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
  
	//Verifica se o numero de parcelas informado é válido  
	if (Form.edtParcelas.value.length == 0 || Form.edtParcelas.value == "0") 
	{
		
		alert("É necessário Informar o número de parcelas desta conta !");
		Form.edtParcelas.value = 1;
		Form.edtParcelas.focus();
		return false;
	
	}
  
	//Verifica se o numero de parcelas informado é maior que 1, daí precisa informar o numero de dias
	if (Form.edtParcelas.value > 1 && Form.edtDias.value == "0") 
	{
		
		alert("É necessário Informar o dia de vencimento das parcelas subsequentes desta conta !");
		Form.edtDias.focus();
		return false;
	
	} 
  
	//Verifica se o numero de parcelas informado é maior que 1, e se o numero de dias informado for superior a 31
	if (Form.edtParcelas.value > 1 && Form.edtDias.value > 31) 
	{
    
		alert("O dia para o vencimento das parcelas não pode ser superior a 31 !");
		Form.edtDias.focus();
		return false;
	
	} 
  
	return true;

}

//Pesquisa os formandos
function busca_formandos()
{
  
	var Form;
	Form = document.cadastro; 

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
	
	//So antiva caso for conta do tipo de formando
	if (edtTipoContaValor == 3)
	{
  
		eventoId = Form.cmbEventoFormaturaId.value;
     
		wdCarregarFormulario('ContaReceberBuscaFormando.php?EventoId=' + eventoId,'recebe_formandos');
   
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


//Função para preenchimento dos dados do boleto com base na conta a receber
function wdGeraBoleto()
{
  
	var Form;
	Form = document.cadastro; 

	//Verifica se foi informada a data da conta a receber
	if (Form.edtData.value.length == 0) 
	{
		
		alert("É necessário Informar a Data !");
		Form.edtData.focus();
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

	//verifica se o tipo de conta é para sacado normal
	if (edtTipoContaValor == 1)
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

	Form.chkUsaBoleto.checked = true;
  
	//Monta as variáveis pegando os atributos dos comboboxes do form
	var EventoId = document.getElementById("cmbEventoFormaturaId");
	var FormandoId = document.getElementById("cmbFormandoId");
	var ClienteId = document.getElementById("cmbClienteId");
	var FornecedorId = document.getElementById("cmbFornecedorId");
	var ColaboradorId = document.getElementById("cmbColaboradorId");
  

	//Caso não tenha sido informado um evento, então o boleto é para um sacado  
	if (Form.cmbEventoSocialId.value == 0 || Form.cmbEventoFormaturaId.value == 0)
	{
    
		ParticipaEvento = "";
		EventoNome = "";
    
		//Caso tenha escolhido o sacado por cliente
		if (edtTipoPessoaValor == 1) 
		{
	    
			//Monta o nome do sacado com o nome do cliente
			SacadoNome = "CLIENTE: " + ClienteId.options[ClienteId.selectedIndex].text;
  	
		}
  	
		//Caso tenha escolhido o sacado por fornecedor
		if (edtTipoPessoaValor == 2) 
		{
  	  
			//Monta o nome do sacado com o nome do fornecedor
			SacadoNome = "FORNECEDOR: " + FornecedorId.options[FornecedorId.selectedIndex].text;
  	
		}
  	
    
		//Caso tenha escolhido o sacado por colaborador
		if (edtTipoPessoaValor == 3) 
		{
		  
			//Monta o nome do sacado com o nome do colaborador
			SacadoNome = "COLABORADOR: " + ColaboradorId.options[ColaboradorId.selectedIndex].text;
	  
		}
    
	//Caso tenha um evento selecionado  
	} 
	
	else 
	
	{
    
		//Monta o texto da participação no evento
		ParticipaEvento = "Pagamento referente a participação no evento:";

		//Monta o nome do evento
		EventoNome = EventoId.options[EventoId.selectedIndex].text;

		//Monta o nome do sacado para o formando escolhido
		SacadoNome = "FORMANDO: " + FormandoId.options[FormandoId.selectedIndex].text;
    
	}
  
	//Verificação dos dados do sacado
	document.getElementById('nro_doc').innerHTML = Form.edtNroDocumento.value;
	document.getElementById('nosso_nro').innerHTML = "<?php echo $dados_boleto[boleto_convenio] ?>" + Form.edtNroDocumento.value;
	document.getElementById('sacado').innerHTML = SacadoNome;

	Form.edtBoletoVencimento.value = Form.edtDataVencimento.value;

	Form.edtBoletoDemonstrativo1.value = ParticipaEvento;
	Form.edtBoletoDemonstrativo2.value = EventoNome;

	Form.edtBoletoInstrucoes1.value = "<?php echo $dados_boleto[instrucoes1] ?>";
	Form.edtBoletoInstrucoes2.value = "<?php echo $dados_boleto[instrucoes2] ?>";
	Form.edtBoletoInstrucoes3.value = "<?php echo $dados_boleto[instrucoes3] ?>";
	Form.edtBoletoInstrucoes4.value = "<?php echo $dados_boleto[instrucoes4] ?>";
  
	var ValorBoletoOriginal =  Form.edtValor.value;
	var ValorBoletoPonto = ValorBoletoOriginal.replace(".","");
	var ValorBoletoFinal = parseFloat(ValorBoletoPonto.replace(",",".")); 

	var TaxaBoletoOriginal = Form.edtValorBoleto.value;
	var TaxaBoletoPonto = TaxaBoletoOriginal.replace(".","");
	var TaxaBoletoFinal = parseFloat(TaxaBoletoPonto.replace(",","."));

	var TotalBoleto = ValorBoletoFinal + TaxaBoletoFinal;
	var TotalBoleto2casas = TotalBoleto.toFixed(2);

	Form.edtTotalBoleto.value = TotalBoleto2casas;

	alert("Boleto gerado com sucesso !\n\nCaso desejar alterar algum dado do boleto, informe o valor manualmente dentro do local que desejar alterar");
	return true; 

}


//Função para adicionar zeros a esquerda de uma variável
function strPad(palavra, casas, carac, dir) 
{ 
  
	//dir = 'R' => Right; dir = 'L' => Left; 
	if(palavra == null || palavra == '') palavra = 0; 
	var ret = ''; 
	var nro = casas - (palavra.length); 
	for(var i = 0; i < nro; i++) ret += carac; 
	if(dir == 'R') 
	ret = palavra + ret; 
	else if(dir == 'L') 
	ret += palavra; 
	return ret; 

} 


//Função para gerar automaticamente o número do boleto
function wdGeraNumero()
{

	var Form;
	Form = document.cadastro; 

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
  
	if (edtTipoContaValor == 1)
	{
    
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
  
		//Caso tenha escolhido o sacado por cliente
		if (edtTipoPessoaValor == 1) 
		{
	    
			//Verifica se foi escolhido o cliente
			if (Form.cmbClienteId.value == 0) 
			{
			
				alert("Para utilizar esta função, é necessário selecionar um Cliente !");
				Form.cmbClienteId.focus();
				return false;
			
			}

			//Monta as variáveis do nosso numero adicionando zeros conforme necessário    
			$numero_sacado = strPad(Form.cmbClienteId.value,5,'0','L'); 
  	
		}
  	
		//Caso tenha escolhido o sacado por fornecedor
		if (edtTipoPessoaValor == 2) 
		{
  	  
			//Verifica se foi escolhido o cliente
			if (Form.cmbFornecedorId.value == 0) 
			{
        
				alert("Para utilizar esta função, é necessário selecionar um Fornecedor !");
				Form.cmbFornecedorId.focus();
				return false;
			
			}
      
			//Monta as variáveis do nosso numero adicionando zeros conforme necessário    
			$numero_sacado = strPad(Form.cmbFornecedorId.value,5,'0','L'); 
  	
		}
  	
    
		//Caso tenha escolhido o sacado por colaborador
		if (edtTipoPessoaValor == 3) 
		{
  	  
			//Verifica se foi escolhido o colaborador
			if (Form.cmbColaboradorId.value == 0) 
			{
        
				alert("Para utilizar esta função, é necessário selecionar um Colaborador !");
				Form.cmbColaboradorId.focus();
				return false;
			
			}
      
			//Monta as variáveis do nosso numero adicionando zeros conforme necessário    
			$numero_sacado = strPad(Form.cmbColaboradorId.value,5,'0','L');       
  	
		}
            
		//Alimenta o numero do documento com base nas variáveis montadas
		Form.edtNroDocumento.value = $numero_sacado;     
    
	}
  
	//Se for para gerar o nosso numero por um evento
	if (edtTipoContaValor == 2)
	{  
  
		//Verifica se foi escolhido o evento
		if (Form.cmbEventoSocialId.value == 0)
		{
      
			alert("Para utilizar esta função, é necessário selecionar um Evento !");
			return false;
    
		}
    
		//Monta as variáveis do nosso numero adicionando zeros conforme necessário    
		$numero_sacado = strPad(Form.cmbEventoSocialId.value,5,'0','L');
    
		//Alimenta o numero do documento com base nas variáveis montadas
		Form.edtNroDocumento.value = $numero_sacado;
  
	}
  
	//Se for para gerar o nosso numero por um evento de formatura
	if (edtTipoContaValor == 3)
	{  
  
		//Verifica se foi escolhido o evento
		if (Form.cmbEventoFormaturaId.value == 0)
		{
      
			alert("Para utilizar esta função, é necessário selecionar um Evento de Formatura!");
			return false;
    
		}
    
		//Verifica se foi escolhido um formando
		if (Form.cmbFormandoId.value == 0) 
		{
      
			
			alert("Para utilizar esta função, é necessário selecionar um Formando !");
			Form.cmbColaboradorId.focus();
			return false;
    
		}
		
		//Monta as variáveis do nosso numero adicionando zeros conforme necessário    
		//$numero_evento = strPad(Form.cmbEventoFormaturaId.value,3,'0','L'); 
		
		//Gera um numero aleatorio entre 1 e 1000
		numero_evento = Math.floor(Math.random() * 1001);
		
		numero_formando = strPad(Form.cmbFormandoId.value, 5, '0', 'L');
		
		numero_documento = numero_evento + numero_formando;
		
		//Alimenta o numero do documento com base nas variáveis montadas
		Form.edtNroDocumento.value = numero_documento;
		
		if (Form.edtNroDocumento.value.length == 7)
		{
		
			Form.edtNroDocumento.value = "0" + numero_documento;
			
		}
  
	}
	
	//Se for para gerar o nosso numero por um pregao
	if (edtTipoContaValor == 4)
	{  
  
		//Verifica se foi escolhido o evento
		if (Form.cmbEventoPregaoId.value == 0)
		{
      
			alert("Para utilizar esta função, é necessário selecionar um Evento !");
			return false;
    
		}
    
		//Monta as variáveis do nosso numero adicionando zeros conforme necessário    
		$numero_sacado = strPad(Form.cmbEventoPregaoId.value,3,'0','L');
    
		//Alimenta o numero do documento com base nas variáveis montadas
		Form.edtNroDocumento.value = $numero_sacado;
  
	}
	
	//Se for para gerar o nosso numero por um evento de formatura
	if (edtTipoContaValor == 5)
	{  
  
		//Verifica se foi escolhido o evento
		if (Form.cmbClienteId.value == 0)
		{
      
			alert("Para utilizar esta função, é necessário selecionar um Cliente !");
			return false;
    
		}
		
		//Verifica se foi escolhido o evento
		if (Form.cmbEventoFormaturaId.value == 0)
		{
      
			alert("Para utilizar esta função, é necessário selecionar um Evento de Formatura !");
			return false;
    
		}
    
		//Monta as variáveis do nosso numero adicionando zeros conforme necessário    
		$numero_evento = strPad(Form.cmbEventoFormaturaId.value,3,'0','L'); 
		$numero_cliente = strPad(Form.cmbClienteId.value,5,'0','L');
	
		//Alimenta o numero do documento com base nas variáveis montadas
		Form.edtNroDocumento.value = $numero_evento + $numero_cliente;
  
	}
   
	return true;
  
}

//Função para escolha do tipo de conta a receber
function wdTipoConta()
{
  
	var Form;
	Form = document.cadastro; 
  
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
		Form.cmbEventoSocialId.disabled = true;
		Form.cmbEventoFormaturaId.disabled = true;
		Form.cmbEventoPregaoId.disabled = false;
		
		ID = document.getElementById(800);
		ID.style.display = "";
		ID = document.getElementById(900);
		ID.style.display = "none";
		ID = document.getElementById(920);
		ID.style.display = "none";
		
		//Ativa os combos de sacado
		Form.edtTipoPessoa[0].disabled = false;
		Form.edtTipoPessoa[1].disabled = false;
		Form.edtTipoPessoa[2].disabled = false;
    
		Form.cmbClienteId.disabled = false;
		Form.cmbFornecedorId.disabled = false;
		Form.cmbColaboradorId.disabled = false;
		
		document.getElementById("recebe_formandos").innerHTML = "[ Selecione um evento ]";
	
	}

	//Caso tenha escolhido outro tipo de sacado
	if (edtTipoContaValor == 2) 
	{
	  
		//Ativa o combo de evento
		Form.cmbEventoSocialId.disabled = false;
		Form.cmbEventoFormaturaId.disabled = false;
		Form.cmbEventoPregaoId.disabled = false;
		
		ID = document.getElementById(800);
		ID.style.display = "";
		ID = document.getElementById(900);
		ID.style.display = "none";
		ID = document.getElementById(920);
		ID.style.display = "none";
		
		//Desativa os combos de sacado
		Form.edtTipoPessoa[0].disabled = true;
		Form.edtTipoPessoa[1].disabled = true;
		Form.edtTipoPessoa[2].disabled = true;
		
		Form.cmbClienteId.disabled = true;
		Form.cmbFornecedorId.disabled = true;
		Form.cmbColaboradorId.disabled = true;
		
		document.getElementById("recebe_formandos").innerHTML = "[ Selecione um evento ]";
	
	}
  
	//Caso tenha escolhido evento tipo de formando
	if (edtTipoContaValor == 3) 
	{
	  
		//Ativa o combo de evento
		Form.cmbEventoSocialId.disabled = false;
		Form.cmbEventoFormaturaId.disabled = false;
		Form.cmbEventoPregaoId.disabled = false;
		
		ID = document.getElementById(800);
		ID.style.display = "none";
		ID = document.getElementById(900);
		ID.style.display = "";
		ID = document.getElementById(920);
		ID.style.display = "none";
		
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
		Form.cmbEventoSocialId.disabled = true;
		Form.cmbEventoFormaturaId.disabled = true;
		Form.cmbEventoPregaoId.disabled = false;
		
		ID = document.getElementById(800);
		ID.style.display = "none";
		ID = document.getElementById(900);
		ID.style.display = "none";
		ID = document.getElementById(920);
		ID.style.display = "";
		
		//Ativa os combos de sacado
		Form.edtTipoPessoa[0].disabled = false;
		Form.edtTipoPessoa[1].disabled = false;
		Form.edtTipoPessoa[2].disabled = false;
    
		Form.cmbClienteId.disabled = false;
		Form.cmbFornecedorId.disabled = false;
		Form.cmbColaboradorId.disabled = false;
		
		document.getElementById("recebe_formandos").innerHTML = "[ Selecione um evento ]";
	
	}

	//Caso tenha escolhido o tipo por comissao de formatura
	if (edtTipoContaValor == 5) 
	{
	  
		//Desativa o combo de evento
		Form.cmbEventoSocialId.disabled = true;
		Form.cmbEventoFormaturaId.disabled = false;
		Form.cmbEventoPregaoId.disabled = true;
		
		ID = document.getElementById(800);
		ID.style.display = "none";
		ID = document.getElementById(900);
		ID.style.display = "";
		ID = document.getElementById(920);
		ID.style.display = "none";
		
		//Ativa os combos de sacado
		Form.edtTipoPessoa[0].disabled = false;
		Form.edtTipoPessoa[1].disabled = false;
		Form.edtTipoPessoa[2].disabled = false;
    
		Form.cmbClienteId.disabled = false;
		Form.cmbFornecedorId.disabled = false;
		Form.cmbColaboradorId.disabled = false;
		
		document.getElementById("recebe_formandos").innerHTML = "[ Selecione um evento ]";
	
	}
  
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<?php 

//Efetua o lookup na tabela de eventos sociais
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND tipo = 1 ORDER BY nome";
//Executa a query
$dados_eventos = mysql_query($lista_eventos);

//Efetua o lookup na tabela de eventos de formatura
//Monta o sql de pesquisa
$lista_eventos_formatura = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND tipo = 2 ORDER BY nome";
//Executa a query
$dados_eventos_formatura = mysql_query($lista_eventos_formatura);

//Efetua o lookup na tabela de eventos de pregao
//Monta o sql de pesquisa
$lista_eventos_pregao = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND tipo = 3 ORDER BY nome";
//Executa a query
$dados_eventos_pregao = mysql_query($lista_eventos_pregao);

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

//Monta o lookup da tabela de subgrupos (CONTA_CAIXA) filtrando tipo 2 que é saída (débito)
//Monta o SQL
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '1' ORDER BY nome";
//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

?>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Conta a Receber</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12" />
					</td>
				</tr>
			</table>

			<table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="text">
						<?php
							
							//Recupera os valores vindos do formulário e armazena nas variaveis
							if($_POST["Submit"])
							{

								$edtEmpresaId = $empresaId;
								$edtData = DataMySQLInserir($_POST["edtData"]);

								$chkUsaBoleto = $_POST["chkUsaBoleto"];

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
												
									//A pessoa ID é pega do combo de EVENTO SOCIAL
									$cmbPessoaId = 	$_POST["cmbEventoSocialId"];
              
								}
											
								$cmbGrupoId = $_POST["cmbGrupoId"];
								$cmbSubgrupoId = $_POST["cmbSubgrupoId"];
								$cmbCategoriaId = $_POST["cmbCategoriaId"];
								$edtDescricao = $_POST["edtDescricao"];

								//Armazena o numero normal do documento
								$edtNroDocumentoOriginal = $_POST["edtNroDocumento"];

								$edtDataVencimento = DataMySQLInserir($_POST["edtDataVencimento"]);
								$edtDataVencimentoCalcula = $_POST["edtDataVencimento"];
								
								$edtContaComissao = 0;

								//Verifica se o tipo de conta é de evento social
								if ($edtTipoConta == 2)
								{
								
									$cmbEventoId = $_POST["cmbEventoSocialId"];
								
								} 
								
								else if ($edtTipoConta == 3)
								
								{              
									
									$cmbEventoId = $_POST["cmbEventoFormaturaId"];
								
								}
								
								else if ($edtTipoConta == 4)
								
								{              
									
									$cmbEventoId = $_POST["cmbEventoPregaoId"];
								
								}
								
								else if ($edtTipoConta == 5)
								
								{              
									
									$cmbEventoId = $_POST["cmbEventoFormaturaId"];
									$edtContaComissao = 1;
								
								}

								$cmbFormandoId = $_POST["cmbFormandoId"];
								$edtObservacoes = $_POST["edtObservacoes"];
								$edtOperadorId = $usuarioId;
            
								//Cria a variável do valor original da conta (nova implementação)
								$edtValorOriginal = MoneyMySQLInserir($_POST["edtValor"]);                                                  


								$edtTaxaMulta = MoneyMySQLInserir($_POST["edtValorMulta"]);
								$edtTaxaJuros = MoneyMySQLInserir($_POST["edtValorJuros"]);

								//Verifica se a conta usa boleto
								if ($chkUsaBoleto == 1)
								{

									//Caso usa boleto, o valor da conta a receber deve incluir o custo do boleto
									$edtValor = $_POST["edtTotalBoleto"];
									$edtValorBoleto = MoneyMySQLInserir($_POST["edtValorBoleto"]);

								} 
								
								else 
								
								{

									//Caso não utiliza boleto, pega o valor normal da conta
									$edtValor = MoneyMySQLInserir($_POST["edtValor"]);
									$edtValorBoleto = 0;

								}
            
								$edtValorMulta = (($edtTaxaMulta / 100) * $edtValor);                        

								//Referente aos boletos

								$edtBoletoNumero = $dados_boleto[boleto_convenio] . $_POST["edtNroDocumento"];
								$edtBoletoVencimento = DataMySQLInserir($_POST["edtBoletoVencimento"]);
								$edtBoletoVencimentoCalcula = $_POST["edtBoletoVencimento"];

								$edtTotalBoleto = $_POST["edtTotalBoleto"];
								$edtBoletoDemonstrativo1 = $_POST["edtBoletoDemonstrativo1"];
								$edtBoletoDemonstrativo2 = $_POST["edtBoletoDemonstrativo2"];            
								$edtBoletoInstrucoes1 = $_POST["edtBoletoInstrucoes1"];
								$edtBoletoInstrucoes2 = $_POST["edtBoletoInstrucoes2"];
								$edtBoletoInstrucoes3 = $_POST["edtBoletoInstrucoes3"];
								$edtBoletoInstrucoes4 = $_POST["edtBoletoInstrucoes4"];

								$edtDataProcessaBoleto = DataMySQLInserir($_POST["edtData"]);

								//Captura o numero de desmembramento da parcela
								$Parcelas = $_POST["edtParcelas"];
								$ParcelasOriginal = $_POST["edtParcelas"];
								
								//Captura o numero de dias entre os vencimentos
								$Dias_original = $_POST["edtDias"];
								$Dias = $_POST["edtDias"];
										
								//Pega o mês da data original do vencimento
								$mes_original = substr($edtDataVencimento, 5, 2);
								$ano_original = substr($edtDataVencimento, 0, 4);

								//echo "Vct original: " . $edtDataVencimento . "<br/>";
								//echo "Mes original: " . $mes_original . "<br/>";
            
								$edtMostraVencimento .= $edtDataVencimentoCalcula . "<br/>";
								
								//Monta o numero do documento adicionando o 01 ao sequencial
								$edtNroDocumento = $_POST["edtNroDocumento"] . '01';
								$fim_nosso_numero = '01';
								
								
								
								if ($cmbFormandoId > 0)
								{
								
									//Pesquisa a numeracao sequencial do formando
									//Monta o SQL
									$procura_sequencia = mysql_query("SELECT sequencia_conta FROM eventos_formando WHERE id = $cmbFormandoId");
									//Executa a query
									$dados_sequencia = mysql_fetch_array($procura_sequencia);
									
									$Sequencia = $dados_sequencia["sequencia_conta"];
									
									if ($Sequencia == 0) $Sequencia = 1; 
									if ($Sequencia < 10) $Sequencia = '0' . $Sequencia;
									
									//Monta o numero do documento adicionando o 01 ao sequencial
									$edtNroDocumento = $_POST["edtNroDocumento"] . $Sequencia;
									
									$fim_nosso_numero = $Sequencia;
									
								}
								
								$edtMostraDocumento .= $edtNroDocumento . "<br/>";
								
								//Cria o for para lanças quantas contas forem necessárias
								for($i = 01; $i <= $Parcelas; $i ++ )
								{                                                        
                                    								
									//Cria o texto da linha de demonstrativo 3 informando as parcelas
									$edtBoletoDemonstrativo3 = "Parcela " . $i . " de " . $Parcelas;

									//Cria a variável que mostrará a parte das parcelas
									$edtMostraParcelas .= "Parcela <strong>" . $i . "</strong> de <strong>" . $Parcelas . "</strong><br/>";

									$edtMostraValor .= "R$: " . number_format($edtValor, 2, ",", ".") . "<br/>";

									//Cria a nova data de vencimento
									//Somente após a primeira parcela
              
									if ($i > 1)
									{

										//Verifica se a parcela é menor do que 10
										if ($Sequencia < 10)
										{
										  
											$edtNroDocumento = $edtNroDocumentoOriginal . "0" . $Sequencia;
											$fim_nosso_numero = '0' . $Sequencia;
										  
										}
										else
										{
										  
											$edtNroDocumento = $edtNroDocumentoOriginal . $Sequencia;
											$fim_nosso_numero = $Sequencia;
										  
										}

										$edtMostraDocumento .= $edtNroDocumento . "<br/>";
										  
										$mes_original = ++$mes_original;

										//Verifica se o mês não é inferior a 10
										if ($mes_original < 10)
										{
										  
											$mes_original = "0" . $mes_original;
										  
										}
                
										//Verifica se o mês não é superior a 12
										if ($mes_original > 12)
										{
										  
											$mes_original = "01";
											$ano_original = ++$ano_original;
										  
										}
																		 
										//Verifica se é um mês com 30 dias
										if (($mes_original == 4) || ($mes_original == 6) || ($mes_original == 9) || ($mes_original == 11))
										{

											//Verifica se o dia do vencimento informado for = a 31
											if ($Dias == 31)
											{
										  
												$Dias = "30";

											}    
									
										}
										
										else
										
										{
											
											$Dias = $Dias_original;               
										
										}
                
										//Verifica se é um mês de fevereiro
										if ($mes_original == 2)
										{

											//Verifica se o dia do vencimento informado for > que 28
											if ($Dias_original > 28)
											{

												$Dias = "28";

											} 
											
											else
											
											{
												
												$Dias = $Dias_original;               
											
											}                 
								
										}
                
                
										$data_calculada = $ano_original . "-" . $mes_original . "-" . $Dias;

										$edtMostraVencimento .= DataMySQLRetornar($data_calculada) . "<br/>";                
													  
										$edtBoletoVencimentoCalculaFormata = $data_calculada;              
										$edtDataVencimentoCalculaFormata = $data_calculada;	
                
									}

									else

									{
								
										$edtBoletoVencimentoCalcula = $edtBoletoVencimentoCalcula;
										$edtBoletoVencimentoCalculaFormata = DataMySQLInserir($edtBoletoVencimentoCalcula);
										$edtDataVencimentoCalcula = $edtDataVencimentoCalcula;
										$edtDataVencimentoCalculaFormata = DataMySQLInserir($edtDataVencimentoCalcula);

									}
            																				
									//Monta o sql e executa a query de inserção da conta
									$sql = mysql_query("
														INSERT INTO contas_receber (
														empresa_id, 
														data,
														tipo_pessoa,
														pessoa_id,
														grupo_conta_id,
														subgrupo_conta_id,
														evento_id, 
														formando_id, 
														descricao,
														origem_conta,
														nro_documento,
														valor_original,
														valor,
														valor_boleto,
														valor_multa,
														taxa_multa,
														taxa_juros,
														data_vencimento,
														situacao,
														observacoes,
														cadastro_timestamp,
														cadastro_operador_id,
														conta_de_comissao
										
														) VALUES (
										
														'$edtEmpresaId',
														'$edtData',
														'$edtTipoPessoa',
														'$cmbPessoaId',
														'$cmbGrupoId',
														'$cmbSubgrupoId',
														'$cmbEventoId',
														'$cmbFormandoId',
														'$edtDescricao',
														1,
														'$edtNroDocumento',
														'$edtValorOriginal',
														'$edtValor',
														'$edtValorBoleto',
														'$edtValorMulta',
														'$edtTaxaMulta',
														'$edtTaxaJuros',
														'$edtDataVencimentoCalculaFormata',
														1,										
														'$edtObservacoes',
														now(),
														'$edtOperadorId',
														$edtContaComissao
														);"); 
                    
                    
									$ID_ContaReceber = mysql_insert_id();
                
									//Verifica se deve utilizar boletos bancários    
									if ($chkUsaBoleto == 1)
									{
                  
										//Verifica os dados do sacado se for um formando
										if ($cmbFormandoId > 0)
										{
                    
											//Busca os dados do formando
											$sql_busca_formando = mysql_query("SELECT 
																			  form.nome,
																			  form.cpf,
																			  form.endereco,
																			  form.complemento,
																			  form.bairro,
																			  form.cep,
																			  form.uf,
																			  cid.nome as cidade_nome
																			  FROM eventos_formando form
																			  LEFT OUTER JOIN cidades cid ON form.cidade_id = cid.id
																			  WHERE form.id = $cmbFormandoId");

                    
											//Monta o array com os dados
											$dados_busca_formando = mysql_fetch_array($sql_busca_formando);
                    
											$SacadoNome = $dados_busca_formando["nome"] . " - CPF: " . $dados_busca_formando["cpf"];
											$SacadoEndereco1 = $dados_busca_formando["endereco"] . " - " . $dados_busca_formando["complemento"] . " - " . $dados_busca_formando["bairro"];
											$SacadoEndereco2 = $dados_busca_formando["cep"] . " - " . $dados_busca_formando["cidade_nome"] . " - " . $dados_busca_formando["uf"];
                    
										} 
										
										else 
										
										{
                    
											$SacadoNome = "Dados do formando não informados";
											$SacadoEndereco1 = "";
											$SacadoEndereco2 = "";
                    
										}
                
										//Cria o numero do boleto sequenciado
										$edtBoletoNumero = substr($edtBoletoNumero,0,15) . $fim_nosso_numero;
                
										$data_atualizacao = date("Y-m-d", mktime());
										
										$sql_boleto = mysql_query("
                        		                INSERT INTO boleto (
                        						prazo_pagamento,
												taxa_boleto,
												valor_cobrado,
												valor_boleto,
												nosso_numero,
												numero_documento,
												data_vencimento,
												data_documento,
												data_processamento,
												sacado,
												endereco1,
												endereco2,
												demonstrativo1,
												demonstrativo2,
												demonstrativo3,
												instrucoes1,
												instrucoes2,
												instrucoes3,
												instrucoes4,
												quantidade,
												valor_unitario,
												aceite,
												especie,
												especie_doc,
												boleto_recebido,
												evento_id,
												formando_id,
												data_atualizacao
                        						
												) VALUES (

												'1',
												'1',
												'1',
												'$edtTotalBoleto',
												'$edtBoletoNumero',
												'$edtBoletoNumero',
												'$edtBoletoVencimentoCalculaFormata',
												'$edtDataProcessaBoleto',
												'$edtDataProcessaBoleto',
												'$SacadoNome',
												'$SacadoEndereco1',
												'$SacadoEndereco2',
												'$edtBoletoDemonstrativo1',
												'$edtBoletoDemonstrativo2',
												'$edtBoletoDemonstrativo3',
												'$edtBoletoInstrucoes1',										
												'$edtBoletoInstrucoes2',
												'$edtBoletoInstrucoes3',
												'$edtBoletoInstrucoes4',
												'0',
												'0',
												'S',
												'R$',
												'',
												0,
												'$cmbEventoId',
												'$cmbFormandoId',
												'$data_atualizacao');"); 
                                            
                                        $ID_Boleto = mysql_insert_id();
                                          
										//Insere o id do boleto na conta a receber
										$sql_boleto = mysql_query("UPDATE contas_receber SET boleto_id = $ID_Boleto WHERE id = $ID_ContaReceber");

										$boleto_hash = md5($ID_Boleto);

										$sql_hash = mysql_query("UPDATE boleto SET 
															  id_hash = '$boleto_hash',  
															  conta_receber_id = $ID_ContaReceber
															  WHERE id = $ID_Boleto");
                
									//Fecha o if de se deve gerar o boleto                
								}
								
								$Sequencia++;
               
							//Fecha o FOR 
							}
							
							$valor_atualiza = $Sequencia;
							
							$atualiza_sequencia = mysql_query("UPDATE eventos_formando SET sequencia_conta = $valor_atualiza WHERE id = $cmbFormandoId");
							
										
						?>                       
						<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
							<tr>
								<td height="22" width="20" valign="top" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 3px; border-right: 0px">
									<img src="./image/bt_informacao.gif" border="0" />
								</td>
								<td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px; padding-top: 3px; padding-bottom: 4px">
									<strong>Conta a Receber cadastrada com sucesso !</strong>
									<br/>
									<table width="500" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
										<tr>
											<td colspan="4" style="padding-top: 5px;">
												<span style="color: #990000"><strong>Desdobramento das Parcelas:</strong></span>
											</td>
										</tr>
										<tr>
											<td width="120">
												<strong>Número da Parcela</strong>
											</td>
											<td width="90">
												<strong>Documento</strong>
											</td>
											<td width="90">
												<strong>Valor</strong>
											</td>
											<td>
												<strong>Vencimento</strong>
											<td>
										</tr>
										<tr>
											<td>
												<?php echo $edtMostraParcelas ?>
											</td>
											<td>
												<?php echo $edtMostraDocumento ?>
											</td>
											<td>
												<strong><?php echo $edtMostraValor ?></strong>
											</td>
											<td>
												<span style="color: #990000">
												<strong>
													<?php echo $edtMostraVencimento ?>
												</strong>
												</span>
											</td>
										</tr>
									</table>                    
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>      
						<?php 
        
							//Fecha o if de postagem
							}
						
						?>
						<table cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td width="484">
									<form id="form" name="cadastro" action="sistema.php?ModuloNome=ContaReceberCadastra" method="post" onsubmit="return valida_form()">
								</td>
							</tr>
							<tr>
								<td style="PADDING-BOTTOM: 2px">
									<input name="Submit" type="submit" class="button" id="Submit" title="Salva a conta a receber" value="Salvar Conta a Receber" />
									<input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
								</td>
								<td width="36" align="right">	  </td>
							</tr>
						</table>   
						<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
							<tr>
								<td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
									<table cellspacing="0" cellpadding="0" width="100%" border="0">
										<tr>
											<td class="tabDetailViewDL" style="TEXT-ALIGN: left">
												<img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da conta a receber e clique em [Salvar Conta a Receber]									 </td>
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
										$objData->strFormulario = "cadastro";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtData";
										$objData->strRequerido = true;
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = Date("d/m/Y", mktime());
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
								<td width="140" class="dataLabel">Descrição:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<input name="edtDescricao" type="text" class="requerido" id="edtDescricao" style="width: 400px" size="84" maxlength="80" />             
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Conta-caixa:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="cmbSubgrupoId" id="cmbSubgrupoId" style="width:350px">
									<option value="0">--- Selecione uma Opção ---</option>
									<?php 
								 	
										//Monta o while para gerar o combo de escolha
										while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo)) 
										{ 
									
									?>
									<option value="<?php echo $lookup_subgrupo->id ?>"><?php echo $lookup_subgrupo->id . " - " . $lookup_subgrupo->nome ?></option>
									<?php } ?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td width="140" class="dataLabel">Centro de Custo:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<select name="cmbGrupoId" id="cmbGrupoId" style="width:350px">
									<option value="0">--- Selecione uma Opção ---</option>
									<?php 
									 
										//Monta o while para gerar o combo de escolha
										while ($lookup_grupo = mysql_fetch_object($dados_grupo)) 
										{ 
									
									?>
									<option value="<?php echo $lookup_grupo->id ?>"><?php echo $lookup_grupo->id . " - " . $lookup_grupo->nome ?></option>
									<?php } ?>
									</select>						 						 
								</td>
							</tr>
							<tr>
								<td width="140" valign="top" class="dataLabel">Tipo de Conta:</td>
								<td colspan="4" class="tabDetailViewDF">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr valign="middle">
											<td width="200" height="20">
												<input type="radio" name="edtTipoConta" value="1" checked="checked" onclick="wdTipoConta()" />
												Cliente/Fornecedor/Colaborador
											</td>
											<td width="120" height="20">
												<input type="radio" name="edtTipoConta" value="2"  onclick="wdTipoConta()" />
												Evento Social
											</td>
											<td width="110" height="20">
												<input type="radio" name="edtTipoConta" id="radio_edtTipoContaFormatura" value="3" onclick="wdTipoConta()" />
												Formando
											</td>
											<td width="100" height="20">
												<input type="radio" name="edtTipoConta" id="radio_edtTipoContaComissao" value="5" onclick="wdTipoConta()" />
												Comissão
											</td>
											<td height="20">
												<input type="radio" name="edtTipoConta" id="radio_edtTipoContaPregao" value="4" onclick="wdTipoConta()" />
												Pregão/Edital
											</td>
										</tr>
									</table>
								</td>
							</tr>           
							<tr>
								<td width="140" valign="top" class="dataLabel">Tipo de Pessoa:<br/><br/>Sacado:</td>
								<td colspan="4" class="tabDetailViewDF">
									<table cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td width="117" height="20">
												<input type="radio" name="edtTipoPessoa" value="1" checked="checked" onclick="wdExibir()" />
												Cliente
											</td>
											<td width="120" height="20">
												<input type="radio" name="edtTipoPessoa" value="2"  onclick="wdExibir()" />
												Fornecedor
											</td>
											<td width="120" height="20">
												<input type="radio" name="edtTipoPessoa" value="3" onclick="wdExibir()" />
												Colaborador
											</td>
										</tr>
									</table>
									<table id="20" cellpadding="0" cellspacing="0">
										<tr valign="middle">
											<td style="padding-top: 7px">
												Cliente:<br/>
												<select name="cmbClienteId" id="cmbClienteId" style="width:350px" onchange="if (this.value > 0) {cmbFormandoId.disabled = true;}">
												<option value="0">--- Selecione uma Opção ---</option>
												<?php 
													
													//Monta o while para gerar o combo de escolha
													while ($lookup_cliente = mysql_fetch_object($dados_cliente)) 
													{ 
												
												?>
												<option value="<?php echo $lookup_cliente->id ?>"><?php echo $lookup_cliente->id . " - " . $lookup_cliente->nome ?> </option>
												<?php } ?>
												</select>
											</td>
										</tr>
									</table>
									<table id="30" cellpadding="0" cellspacing="0" style="display: none">
										<tr valign="middle">
											<td style="padding-top: 7px">
												Fornecedor:<br/>                  
												<select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
												<option value="0">--- Selecione uma Opção ---</option>
												<?php 
											 
													//Monta o while para gerar o combo de escolha
													while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) 
													{ 
												
												?>
												<option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . " - " . $lookup_fornecedor->nome ?></option>
												<?php } ?>
												</select>
											</td>
										</tr>
									</table>
									<table id="40" cellpadding="0" cellspacing="0" style="display: none">
										<tr valign="middle">
											<td style="padding-top: 7px">
												Colaborador:<br/>                  
												<select name="cmbColaboradorId" id="cmbColaboradorId" style="width:350px">
												<option value="0">--- Selecione uma Opção --- </option>
												<?php 
											 
													//Monta o while para gerar o combo de escolha
													while ($lookup_colaborador = mysql_fetch_object($dados_colaborador)) 
													{ 
												
												?>
												<option value="<?php echo $lookup_colaborador->id ?>"><?php echo $lookup_colaborador->id . " - " . $lookup_colaborador->nome ?></option>
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
									<div id="800">
										<select name="cmbEventoSocialId" id="cmbEventoSocialId" style="width: 400px" style="disabled: true">                  
											<option value="0">--- Selecione uma Opção ---</option>
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
									</div>
									<div id="900">
										<select name="cmbEventoFormaturaId" id="cmbEventoFormaturaId" style="width: 400px" onchange="busca_formandos()" style="disabled: true">                  
											<option value="0">--- Selecione uma Opção ---</option>
											<?php 
												
												//Cria o componente de lookup de eventos formatura
												while ($lookup_eventos_formatura = mysql_fetch_object($dados_eventos_formatura)) 
												{ 
											
											?>
											<option value="<?php echo $lookup_eventos_formatura->id ?>"><?php echo $lookup_eventos_formatura->id . " - " . $lookup_eventos_formatura->nome ?></option>
											<?php 
  									  
												//Fecha o while
												} 
											
											?>
										</select>
									</div>
									<div id="920">
										<select name="cmbEventoPregaoId" id="cmbEventoPregaoId" style="width: 400px" onchange="busca_formandos()" style="disabled: true">                  
											<option value="0">--- Selecione uma Opção ---</option>
											<?php 
												
												//Cria o componente de lookup de eventos formatura
												while ($lookup_eventos_pregao = mysql_fetch_object($dados_eventos_pregao)) 
												{ 
											
											?>
											<option value="<?php echo $lookup_eventos_pregao->id ?>"><?php echo $lookup_eventos_pregao->id . " - " . $lookup_eventos_pregao->nome ?></option>
											<?php 
  									  
												//Fecha o while
												} 
											
											?>
										</select>
									</div>
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
									<input name="edtNroDocumento" type="text" class="requerido" id="edtNroDocumento" style="width: 80px" maxlength="10" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" />
									<input class="button" title="Gera o número do documento automaticamente com base no evento e formando selecionado" onclick="wdGeraNumero();" type="button" value="Gerar Número" name="GeraNumero" />             
									&nbsp;<span style="font-size: 10px;">(Números sequenciais serão adicionados automaticamente ao salvar a conta)</span>
								</td>       
							</tr>
							<tr>
								<td width="140" valign="top" class="dataLabel">Valor:</td>
								<td width="173" class="tabDetailViewDF">
									<?php
										
										//Cria um objeto do tipo WDEdit 
										$objWDComponente = new WDEditReal();
										
										//Define nome do componente
										$objWDComponente->strNome = "edtValor";
										//Define o tamanho do componente
										$objWDComponente->intSize = 16;
										//Busca valor definido no XML para o componente
										$objWDComponente->strValor = "";
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
										$objData->strFormulario = "cadastro";  
										//Nome do campo que deve ser criado
										$objData->strNome = "edtDataVencimento";
										//Valor a constar dentro do campo (p/ alteração)
										$objData->strValor = "";
										//Define o tamanho do campo 
										//$objData->intTamanho = 15;
										$objData->strRequerido = true;
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
								<td width="140" class="dataLabel">Custo do Boleto:</td>
								<td colspan="4" valign="middle" class="tabDetailViewDF">
									<?php
									
										//Acerta a variável com o valor a alterar
										$valor_alterar = str_replace(".",",",$dados_boleto[boleto_taxa]);							
										
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
									&nbsp;R$             
								</td>       
							</tr>
							<tr>
								<td width="140" valign="top" class="dataLabel">Taxa Multa Atraso:</td>
								<td width="173" class="tabDetailViewDF">
									<?php 
  							
										//Acerta a variável com o valor a alterar
										$valor_alterar = str_replace(".",",",$dados_boleto[valor_multa]);							
										
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
										$valor_alterar = str_replace(".",",",$dados_boleto[valor_juros]);							
										
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
									<textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 130px"></textarea>    							
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="text" valign="top">
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td><span class="TituloModulo">Duplicar Conta</span></td>
							</tr>
							<tr>
								<td>
									<img src="image/bt_espacohoriz.gif" width="100%" height="12" />
								</td>
							</tr>
							<tr>
								<td>
									Caso desejar duplicar esta conta, informe o número de vezes que desejado (Será criado Parcela X de Y):
									<br/>
									Duplicar esta conta&nbsp;&nbsp;&nbsp;<input name="edtParcelas" type="text" class="requerido" id="edtParcelas" style="width: 20px" maxlength="2" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="1" />&nbsp;vez(es), com vencimentos para o dia&nbsp;&nbsp;<input name="edtDias" type="text" class="requerido" id="edtDias" style="width: 20px" maxlength="2" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="0" />&nbsp;&nbsp;de cada mês subsequente. 
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="text" valign="top">
						<br/>
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td><span class="TituloModulo">Pagamento via Boleto Bancário</span></td>
								</tr>
								<tr>
									<td>
										<img src="image/bt_espacohoriz.gif" width="100%" height="12" />
									</td>
								</tr>
								<tr>
									<td>
										<input name="btnGeraBoleto" type="button" class="button" id="btnGeraBoleto" title="Preenche o modelo de boleto com os parâmetros pré-configurados" value="Preencher Boleto" onclick="return wdGeraBoleto();" />&nbsp;&nbsp;<input name="chkUsaBoleto" type="checkbox" id="chkUsaBoleto" value="1" />&nbsp;Utilizar Boleto para Esta Conta a Receber
									</td>
								</tr>
								<tr>
									<td>
										<div id="777">
										<br/>
										<table width="100%" cellpadding="0" cellspacing="0" border="1">
											<tr>
												<td colspan="6" style="border: 0px;">
													<img src="image/topo_boleto.gif" width="100%" />
												</td>
											</tr>
											<tr>
												<td colspan="5" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Local de Pagamento:</span>
												</td>
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Vencimento:</span>
												</td>
											</tr>
											<tr>
												<td colspan="5" style="border-top: 0px;">
													<span style="color: #990000;">&nbsp;QUALQUER BANCO ATÉ O VENCIMENTO</div>
												</td>
												<td style="border-top: 0px;">
													<div align="right"><input name="edtBoletoVencimento" type="text" class="datafield" id="edtBoletoVencimento" style="color: #990000; width: 70px; text-align: right" maxlength="10" readonly="readonly" />&nbsp;</span></div>
												</td>
											</tr>
											<tr>
												<td colspan="5" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Cedente:</span>
												</td>
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Agência/Código Cedente:</span>
												</td>
											</tr>
											<tr>
												<td colspan="5" style="border-top: 0px;">
													<span style="color: #990000;"><b>&nbsp;<?php echo $empresaNome ?></b></div>
												</td>
												<td style="border-top: 0px;">
													<div align="right"><span style="color: #990000;"><?php echo $dados_boleto["boleto_agencia"] . " / " . $dados_boleto["boleto_conta"] ?></span>&nbsp;</div>
												</td>
											</tr>
											<tr>
												<td width="15%" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Data do Documento:</span>
												</td>
												<td width="15%" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Nº do Documento:</span>
												</td>
												<td width="13%" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Espécie Doc:</span>
												</td>
												<td width="15%" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Aceite:</span>
												</td>
												<td width="16%" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Data Proc.:</span>
												</td>
												<td style="border-bottom: 0px;">
														<span style="color: #6666CC;">&nbsp;Nosso Número:</span>
												</td>
											</tr>
											<tr>
												<td style="border-top: 0px;" align="center">
													<span style="color: #990000;">&nbsp;<?php echo date("d/m/Y", mktime()); ?></span>
												</td>
												<td style="border-top: 0px;">
													<span style="color: #990000;"><div align="center" id="nro_doc">&nbsp;</div></span>
												</td>
												<td style="border-top: 0px;">
													<span style="color: #990000;"><div align="center">DM</div></span>
												</td>
												<td style="border-top: 0px;">
													<span style="color: #990000;"><div align="center">N</div></span>
												</td>
												<td style="border-top: 0px;" align="center">
													<span style="color: #990000;"><?php echo date("d/m/Y", mktime()); ?></span>
												</td>
												<td style="border-top: 0px;">
													<span style="color: #990000;"><div align="right" id="nosso_nro">&nbsp;</div></span>
												</td>
											</tr>
											<tr>
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Uso do Banco:</span>
												</td>
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Carteira:</span>
												</td>
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Espécie:</span>
												</td>
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Quantidade:</span>
												</td>
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;x Valor:</span>
												</td>
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;(=) Valor do Documento:</span>
												</td>
											</tr>
											<tr>
												<td style="border-top: 0px;">&nbsp;</td>
												<td style="border-top: 0px;">
													<div align="center"><span style="color: #990000;"><?php echo $dados_boleto["boleto_carteira"] . $dados_boleto["boleto_var_carteira"] ?></span></div>
												</td>
												<td style="border-top: 0px;">
													<span style="color: #990000;"><div align="center">R$</div></span>
												</td>
												<td style="border-top: 0px;">
													<div align="center">&nbsp;</span>
												</td>
												<td style="border-top: 0px;">&nbsp;</td>
												<td style="border-top: 0px;">
													<div align="right"><input name="edtTotalBoleto" type="text" class="datafield" id="edtTotalBoleto" style="color: #990000; width: 70px; text-align: right" maxlength="15" readonly="readonly" />&nbsp;
												</td>
											</tr>
											<tr>
												<td colspan="5" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Instruções (Texto de responsabilidade do cedente):</span>
												</td>                      
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;(-) Descontos/Abatimentos:</span>
												</td>
											</tr>
											<tr>
												<td rowspan="9" colspan="5" style="border-bottom: 0px; border-top: 0px">
													<span style="color: #6666CC;">
													&nbsp;<input name="edtBoletoDemonstrativo1" type="text" class="datafield" id="edtBoletoDemonstrativo1" style="color: #990000; width: 570px" maxlength="100" /><br/>
													&nbsp;<input name="edtBoletoDemonstrativo2" type="text" class="datafield" id="edtBoletoDemonstrativo2" style="color: #990000; width: 570px" maxlength="100" /><br/>                        
													&nbsp;<input name="edtBoletoInstrucoes1" type="text" class="datafield" id="edtBoletoInstrucoes1" style="color: #990000; width: 570px" maxlength="100" style="margin-top: 4px" /><br/>
													&nbsp;<input name="edtBoletoInstrucoes2" type="text" class="datafield" id="edtBoletoInstrucoes2" style="color: #990000; width: 570px" maxlength="100" /><br/>
													&nbsp;<input name="edtBoletoInstrucoes3" type="text" class="datafield" id="edtBoletoInstrucoes3" style="color: #990000; width: 570px" maxlength="100" /><br/>
													&nbsp;<input name="edtBoletoInstrucoes4" type="text" class="datafield" id="edtBoletoInstrucoes4" style="color: #990000; width: 570px" maxlength="100" />
													</span>
												</td>
												<td  style="border-top: 0px;">
													<span style="color: #6666CC;">&nbsp;</span>
												</td>
											</tr>
											<tr>     			    
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;(-) Outras Deduções:</span>
												</td>
											</tr>
											<tr>            			
												<td style="border-top: 0px;">
													<span style="color: #6666CC;">&nbsp;</span>
												</td>
											</tr>
											<tr>     			    
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;(+) Mora / Multa:</span>
												</td>
											</tr>
											<tr>            			
												<td style="border-top: 0px;">
													<span style="color: #6666CC;">&nbsp;</span>
												</td>
											</tr>
											<tr>     			    
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;(+) Outros Acréscimos:</span>
												</td>
											</tr>
											<tr>            			
												<td style="border-top: 0px;">
													<span style="color: #6666CC;">&nbsp;</span>
												</td>
											</tr>
											<tr>     			    
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;(=) Valor Cobrado:</span>
												</td>
											</tr>
											<tr>            			
												<td style="border-top: 0px;">
													<span style="color: #6666CC;">&nbsp;</span>
												</td>
											</tr>
											<tr>            			
												<td colspan="6" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Sacado:</span>
												</td>
											</tr> 
											<tr>
												<td colspan="6" style="border-bottom: 0px; border-top: 0px; padding-top: 0px; padding-left: 4px">
													<span style="color: #990000;"><div id="sacado">&nbsp;</div></span>
												</td>               
											</tr>
											<tr>
												<td colspan="5" style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Sacador/Avalista:</span>
												</td>                      
												<td style="border-bottom: 0px;">
													<span style="color: #6666CC;">&nbsp;Cód. Baixa:</span>
												</td>
											</tr>
											<tr>
												<td colspan="5" style="border-bottom: 0px; border-top: 0px">
													<span style="color: #6666CC;">&nbsp;</span>
												</td>
												<td  style="border-top: 0px;">
													<span style="color: #6666CC;">&nbsp;</span>
												</td>
											</tr>
											<tr>
												<td colspan="6" style="padding-left: 15px;">
													<img src="image/rodape_boleto.gif" />
												</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
</form>
</table>  	 

</tr>
</table>

<script language="JavaScript">
	var Form;
	Form = document.cadastro; 
	Form.cmbEventoSocialId.disabled = true;
	Form.cmbEventoFormaturaId.disabled = true;
	Form.cmbEventoPregaoId.disabled = true;
	oculta(800);
	oculta(900);
</script>

<?php

//Verifica se vem o parâmetro que é do Foto e Vídeo
$edtOrigem = $_GET["Origem"];

if ($edtOrigem == "FV")
{
 
	$edtEventoFVId = $_GET["EventoFVId"];
	$edtFormandoFVId = $_GET["FormandoFVId"];

	//Busca o nome do evento
	//Efetua o lookup na tabela de eventos sociais
	//Monta o sql de pesquisa
	$lista_evento_fv = "SELECT id, nome FROM eventos WHERE id = $edtEventoFVId";

	//Executa a query
	$resultado_evento_fv = mysql_query($lista_evento_fv);

	//Monta o array dos campos
	$dados_evento_fv = mysql_fetch_array($resultado_evento_fv);

	$texto_evento_fv = $dados_evento_fv["nome"] . " / FOTOS ";

	echo "<script>

		  function timeMsg()
		  {
		  var t=setTimeout('alertMsg()',3000);
		  }
		  function alertMsg()
		  {
		  
			var Form;
			Form = document.cadastro;
			
			Form.cmbFormandoId.value = $edtFormandoFVId;
			Form.edtObservacoes.value = 'Gerada automaticamente pelo módulo de Foto e Vídeo';
			wdGeraNumero();
			Form.edtValorBoleto.value = 0;
			alert('Conta a receber gerada com sucesso ! - Informe a data do vencimento e o número de parcelas');
		  }

		  var Form;
		  Form = document.cadastro;
		  
		  Form.edtDescricao.value = '$texto_evento_fv';
		  Form.cmbSubgrupoId.value = 14;
		  Form.cmbGrupoId.value = 6;
		  document.all.radio_edtTipoContaFormatura.checked = true;
		  wdTipoConta();
		  Form.cmbEventoFormaturaId.value = $edtEventoFVId;
		  busca_formandos();
		  timeMsg();
		  
		</script>"; 

}

?>