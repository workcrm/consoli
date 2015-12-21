<?php
###########
## M�dulo para Cheques de Treceiros
## Criado: 11/09/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipula��o de datas
include "./include/ManipulaDatas.php";

//Armazena o m�s atual na vari�vel
$mes = date("m",mktime());
$dataHoje = date("Y-m-d", mktime());

//Efetua o switch para determinar o nome do mes atual
switch ($mes) 
{
	case 1: $mes_nome = "Janeiro";	break;
	case 2: $mes_nome = "Fevereiro";	break;
	case 3: $mes_nome = "Mar�o";	break;
	case 4: $mes_nome = "Abril";	break;
	case 5: $mes_nome = "Maio";	break;
	case 6: $mes_nome = "Junho";	break;
	case 7: $mes_nome = "Julho";	break;
	case 8: $mes_nome = "Agosto";	break;
	case 9: $mes_nome = "Setembro";	break;
	case 10: $mes_nome = "Outubro";	break;
	case 11: $mes_nome = "Novembro";	break;
	case 12: $mes_nome = "Dezembro";	break;
}
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="javascript">
function wdCarregarFiltragem() 
{
   
	//Captura o valor referente ao radio button selecionado
	var edtAgruparValor = document.getElementsByName('edtAgrupar');
   
	for (var i=0; i < edtAgruparValor.length; i++) 
	{
     
		if (edtAgruparValor[i].checked == true) 
		{
			edtAgruparValor = edtAgruparValor[i].value;
			break;
		}
   
	}

	if (edtAgruparValor == 1) 
	{
		wdCarregarFormulario('ChequeRelatorioSituacao.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 2) 
	{
		wdCarregarFormulario('ChequeRelatorioPre.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 3) 
	{
		wdCarregarFormulario('ChequeRelatorioBanco.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 4) 
	{
		wdCarregarFormulario('ChequeRelatorioEvento.php?headers=1','filtragem','1');
	}		
	
	if (edtAgruparValor == 5) 
	{
		wdCarregarFormulario('ChequeRelatorioEventoFormando.php?headers=1','filtragem','1');
	}
		
}

function wdVisualizarRelatorio() 
{
	
	var Form;
	Form = document.cadastro;
	
	//Captura o valor referente ao radio button selecionado
	var edtAgruparValor = document.getElementsByName('edtAgrupar');
   
	for (var i=0; i < edtAgruparValor.length; i++) 
	{
	 
		if (edtAgruparValor[i].checked == true) 
		{
			edtAgruparValor = edtAgruparValor[i].value;
			break;
		}
  
	}

	//Caso for por situa��o
	if (edtAgruparValor == 1) 
	{
	 
		//Captura o valor referente ao radio button selecionado da situacao
		var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) 
		{
	    
			if (edtSituacaoValor[i].checked == true) 
			{
				edtSituacaoValor = edtSituacaoValor[i].value;
				break;
			}
  	
		}
		
		//Captura o valor referente ao radio button selecionado da disposi��o
		var edtDisposicaoValor = document.getElementsByName('edtDisposicao');
   
		for (var i=0; i < edtDisposicaoValor.length; i++) 
		{
	    
			if (edtDisposicaoValor[i].checked == true) 
			{
				edtDisposicaoValor = edtDisposicaoValor[i].value;
				break;
			}
  	
		}

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('� necess�rio informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
   		
			}			
   	
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('� necess�rio informar a data final !');
				Form.edtDataFim.focus();
				return false;
   		
			}			
   	
		}
		
		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) 
		{
				
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
		}

		//Monta a url a acessar	 
		var urlCarrega = 'ChequeLista.php?TipoListagem=1&TipoSituacao='+ edtSituacaoValor  + '&TipoDisposicao='+ edtDisposicaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}
	
	
	//Caso for por pre datado
	if (edtAgruparValor == 2) 
	{

		//Captura o valor referente ao radio button selecionado da situacao
		var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) 
		{
	    
			if (edtSituacaoValor[i].checked == true) 
			{
				edtSituacaoValor = edtSituacaoValor[i].value;
				break;
			}
  	
		}
		
		//Captura o valor referente ao radio button selecionado da disposi��o
		var edtDisposicaoValor = document.getElementsByName('edtDisposicao');
   
		for (var i=0; i < edtDisposicaoValor.length; i++) 
		{
	    
			if (edtDisposicaoValor[i].checked == true) 
			{
				edtDisposicaoValor = edtDisposicaoValor[i].value;
				break;
			}
  	
		}
		
		if (Form.edtDataIni.value == 0) 
		{
				
			if (Form.edtDataFim.value != 0) 
			{
					
				alert('� necess�rio informar a data inicial !');
				Form.edtDataIni.focus();      	
				return false;
			
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
				
			if (Form.edtDataIni.value != 0) 
			{
					
				alert('� necess�rio informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
		}
		
		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) 
		{
					
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
	}

		//Monta a url a acessar	 
		var urlCarrega = 'ChequeLista.php?TipoListagem=2&TipoSituacao='+ edtSituacaoValor  + '&TipoDisposicao='+ edtDisposicaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}
	
	//Caso for por banco
	if (edtAgruparValor == 3) 
	{

		//Captura o valor referente ao radio button selecionado da situacao
		var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) 
		{
	    
			if (edtSituacaoValor[i].checked == true) 
			{
				edtSituacaoValor = edtSituacaoValor[i].value;
				break;
			}
  	
		}
		
		//Captura o valor referente ao radio button selecionado da disposi��o
		var edtDisposicaoValor = document.getElementsByName('edtDisposicao');
   
		for (var i=0; i < edtDisposicaoValor.length; i++) 
		{
	    
			if (edtDisposicaoValor[i].checked == true) 
			{
				edtDisposicaoValor = edtDisposicaoValor[i].value;
				break;
			}
  	
		}
		
		if (Form.edtDataIni.value == 0) 
		{
				
			if (Form.edtDataFim.value != 0) 
			{
					
				alert('� necess�rio informar a data inicial !');
				Form.edtDataIni.focus();      	
				return false;
			
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
				
			if (Form.edtDataIni.value != 0) 
			{
					
				alert('� necess�rio informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
		}
		
		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) 
		{
					
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
		}

		//Monta a url a acessar	 
		var urlCarrega = 'ChequeLista.php?TipoListagem=3&TipoSituacao='+ edtSituacaoValor  + '&TipoDisposicao='+ edtDisposicaoValor  + '&BancoId=' + Form.cmbBancoId.value + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}
	
	//Caso for por evento
	if (edtAgruparValor == 4) 
	{

		//Captura o valor referente ao radio button selecionado da situacao
		var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) 
		{
	    
			if (edtSituacaoValor[i].checked == true) 
			{
				edtSituacaoValor = edtSituacaoValor[i].value;
				break;
			}
  	
		}
		
		//Captura o valor referente ao radio button selecionado da disposi��o
		var edtDisposicaoValor = document.getElementsByName('edtDisposicao');
   
		for (var i=0; i < edtDisposicaoValor.length; i++) 
		{
	    
			if (edtDisposicaoValor[i].checked == true) 
			{
				edtDisposicaoValor = edtDisposicaoValor[i].value;
				break;
			}
  	
		}
		
		if (Form.edtDataIni.value == 0) 
		{
				
			if (Form.edtDataFim.value != 0) 
			{
					
				alert('� necess�rio informar a data inicial !');
				Form.edtDataIni.focus();      	
				return false;
			
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
				
			if (Form.edtDataIni.value != 0) 
			{
					
				alert('� necess�rio informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
		}
		
		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) 
		{
					
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
		}

		//Monta a url a acessar	 
		var urlCarrega = 'ChequeLista.php?TipoListagem=4&TipoSituacao='+ edtSituacaoValor  + '&TipoDisposicao='+ edtDisposicaoValor  + '&EventoId=' + Form.cmbEventoId.value + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}
	
	//Caso for por evento e formando
	if (edtAgruparValor == 5) 
	{

		//Captura o valor referente ao radio button selecionado da situacao
		var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) 
		{
	    
			if (edtSituacaoValor[i].checked == true) 
			{
				edtSituacaoValor = edtSituacaoValor[i].value;
				break;
			}
  	
		}
		
		//Captura o valor referente ao radio button selecionado da disposi��o
		var edtDisposicaoValor = document.getElementsByName('edtDisposicao');
   
		for (var i=0; i < edtDisposicaoValor.length; i++) 
		{
	    
			if (edtDisposicaoValor[i].checked == true) 
			{
				edtDisposicaoValor = edtDisposicaoValor[i].value;
				break;
			}
  	
		}
		
		if (Form.edtDataIni.value == 0) 
		{
				
			if (Form.edtDataFim.value != 0) 
			{
					
				alert('� necess�rio informar a data inicial !');
				Form.edtDataIni.focus();      	
				return false;
			
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
				
			if (Form.edtDataIni.value != 0) 
			{
					
				alert('� necess�rio informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
		}
		
		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) 
		{
					
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
		}

		//Monta a url a acessar	 
		var urlCarrega = 'ChequeLista.php?TipoListagem=5&TipoSituacao='+ edtSituacaoValor  + '&TipoDisposicao='+ edtDisposicaoValor  + '&EventoId=' + Form.cmbEventoId.value + '&FormandoId=' + Form.cmbFormandoId.value + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}
	

}



//*** SE FOR IMPRESS�O
function wdCarregarRelatorio() 
{

	var Form;
	Form = document.cadastro;

	//Captura o valor referente ao radio button selecionado
	var edtAgruparValor = document.getElementsByName('edtAgrupar');
   
	for (var i=0; i < edtAgruparValor.length; i++) 
	{
		if (edtAgruparValor[i].checked == true) 
		{
		  edtAgruparValor = edtAgruparValor[i].value;
		  break;
		}
	}
	
	//Caso for por situa��o
	if (edtAgruparValor == 1) 
	{
	 
		//Captura o valor referente ao radio button selecionado da situacao
		var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) 
		{
			if (edtSituacaoValor[i].checked == true) {
			  edtSituacaoValor = edtSituacaoValor[i].value;
			  break;
			}
		}

		if (Form.edtDataIni.value == 0) 
		{
			if (Form.edtDataFim.value != 0) 
			{
				alert('� necess�rio informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			}			
		}

		if (Form.edtDataFim.value == 0) 
		{
			if (Form.edtDataIni.value != 0) 
			{
				alert('� necess�rio informar a data final !');
				Form.edtDataFim.focus();
				return false;
			}			
		}
		
		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) 
		{
				
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
		}
		
		//Monta a url do relat�rio		
		var urlRelatorio = './relatorios/ChequeRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=1&TipoSituacao='+ edtSituacaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;
	}

	//Executa o relat�rio
	abreJanela(urlRelatorio);
}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="750">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Consulta Cheques de Terceiro</span>			  	
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
		<td style="padding-bottom: 2px">
			<span><input name="Button" type="button" class="button" id="Submit" title="Novo Cheque" value="Novo Cheque" onclick="window.location='sistema.php?ModuloNome=ChequeCadastra';" /></span>
		</td>
	</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='440'>
						<br/>
						Selecione um dos relat�rios abaixo:
						<br/>
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="1" checked="checked" onclick="wdCarregarFiltragem()" /> Cheques por Situa��o
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="2" onclick="wdCarregarFiltragem()" /> Cheques Pre-datados
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="3" onclick="wdCarregarFiltragem()" /> Cheques por Banco
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="4" onclick="wdCarregarFiltragem()" /> Cheques por Evento
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="5" onclick="wdCarregarFiltragem()" /> Cheques por Evento e Formando
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
			<div id="filtragem">
				<?php
				//Inclui o arquivo php vazio mas setando o input
				include "ChequeRelatorioSituacao.php";
				?>
			</div>
		</td>
	</tr>
  
	<tr>
		<td>
			<br/>
 			<input class="button" title="Visualiza os cheques com as op��es informadas" name='btnVisualizar' type='button' id='btnVisualizar' value='Visualizar na Tela' style="width:100px" onclick="wdVisualizarRelatorio()">
  			<input class="button" title="Emite o relat�rio dos cheques com as op��es informadas" name='btnRelatorio' type='button' id='btnRelatorio' value='Emitir Relat�rio' style="width:100px" onclick="wdCarregarRelatorio()">
			<br />
			<br />	   	   		   		
		</td>   
	</tr> 
</table>
