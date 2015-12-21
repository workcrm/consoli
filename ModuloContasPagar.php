<?php
###########
## Módulo para Contas a Pagar
## Criado: 10/05/2007 - Maycon Edinger
## Alterado: 14/08/2007 - Maycon Edinger
## Alterações: 
## 26/06/2007 - Alterado os javascripts para receber as datas de filtagem opcionais
## 17/07/2007 - Incluído a opção de filtragem por sacado
## 19/07/2007 - Incluído a opção de filtragem por sub-grupo
## 14/08/2007 - Incluído a opção de filtragem agrupada de grupo, subgrupo e categoria
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

//Armazena o mês atual na variável
$mes = date("m",mktime());
$dataHoje = date("Y-m-d", mktime());

//Efetua o switch para determinar o nome do mes atual
switch ($mes) 
{
	
	case 1: $mes_nome = "Janeiro";	break;
	case 2: $mes_nome = "Fevereiro";	break;
	case 3: $mes_nome = "Março";	break;
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
		wdCarregarFormulario('ContaPagarRelatorioData.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 2) 
	{
		wdCarregarFormulario('ContaPagarRelatorioGrupo.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 3) 
	{
		wdCarregarFormulario('ContaPagarRelatorioEvento.php?headers=1','filtragem','1');
	}

	if (edtAgruparValor == 4) 
	{
		wdCarregarFormulario('ContaPagarRelatorioSituacao.php?headers=1','filtragem','1');
	}		
	
	if (edtAgruparValor == 5) 
	{
		wdCarregarFormulario('ContaPagarRelatorioSacado.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 6) 
	{
		wdCarregarFormulario('ContaPagarRelatorioSubgrupo.php?headers=1&Tipo=S','filtragem','1');
	}	
	
	if (edtAgruparValor == 7) 
	{
		wdCarregarFormulario('ContaPagarRelatorioAgrupado.php?headers=1','filtragem','1');
	}	
}


function wdVisualizarRelatorio(parametro) 
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
  
	
	//Caso for para exibir por data de vencimento
	if (edtAgruparValor == 1) 
	{
			
		if (Form.edtDataIni.value == 0) 
		{
			
			alert('É necessário informar a data inicial !');
			Form.edtDataIni.focus();
			return false;
		
   	}
	
		if (Form.edtDataFim.value == 0) 
		{
			
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

		if (parametro == 1)
		{

			//Monta a url a acessar
			var urlCarrega = 'ContaPagarLista.php?TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}
		
		else if (parametro == 2)
		{

			//Monta a url a acessar
			var urlCarrega = 'ContaPagarListaLote.php?TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}


		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}
  
	
	//Caso for para exibir por data de vencimento
	if (edtAgruparValor == 1) 
	{
			
		if (Form.edtDataIni.value == 0) 
		{
			
			alert('É necessário informar a data inicial !');
			Form.edtDataIni.focus();
			return false;
		
   	}
	
		if (Form.edtDataFim.value == 0) 
		{
			
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


		if (parametro == 1)
		{

			//Monta a url a acessar
			var urlCarrega = 'ContaPagarLista.php?TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;
			
		}

		else if (parametro == 2)
		{

			//Monta a url a acessar
			var urlCarrega = 'ContaPagarListaLote.php?TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}


	//Caso for por grupo
	if (edtAgruparValor == 2) 
	{
	 
	  
		//Recebe o valor do combo de grupo
		var cmbGrupoIdIndice = Form.cmbGrupoId.selectedIndex;
		var cmbGrupoIdValor = Form.cmbGrupoId.options[cmbGrupoIdIndice].value	

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			}			
		
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
							
							if (dia_inicial > dia_final){
								alert("A data inicial deve ser menor que a data final.");
								data_final.focus();
								return false
							}
							
						}
					
					}
				}
			}
		}

		if (parametro == 1)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarLista.php?TipoListagem=2&GrupoId='+ cmbGrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;
		
		}

		else if (parametro == 2)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarListaLote.php?TipoListagem=2&GrupoId='+ cmbGrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}

	//Caso for por evento
	if (edtAgruparValor == 3) 
	{
	 
		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value	

		if (Form.edtDataIni.value == 0) 
		{
			if (Form.edtDataFim.value != 0) 
			{
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
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

		if (parametro == 1)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarLista.php?TipoListagem=3&EventoId='+ cmbEventoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		else if (parametro == 2)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarListaLote.php?TipoListagem=3&EventoId='+ cmbEventoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');
		
	}

	//Caso for por situação
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

		if (Form.edtDataIni.value == 0) 
		{
			if (Form.edtDataFim.value != 0) 
			{
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			}			
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			}			
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

		if (parametro == 1)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarLista.php?TipoListagem=4&TipoSituacao='+ edtSituacaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value  + '&Regiao='+ Form.cmbRegiaoId.value;
		
		}

		else if (parametro == 2)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarListaLote.php?TipoListagem=4&TipoSituacao='+ edtSituacaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}
	
	//Caso for por Sacado
	if (edtAgruparValor == 5) 
	{

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			}			
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
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

		//Captura o valor do combo do tipo de pessoa
		var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
	   
		for (var i=0; i < edtTipoPessoaValor.length; i++) 
		{
			
			if (edtTipoPessoaValor[i].checked == true) 
			{
				
				edtTipoPessoaValor = edtTipoPessoaValor[i].value;
				break;
			
			}
		
		}
	  
		//Se for por cliente
		if (edtTipoPessoaValor == 1) 
		{	  	
			
			if (Form.cmbClienteId.value == 0) 
			{
      	
				alert("É necessário selecionar um Cliente !");
				Form.cmbClienteId.focus();
				return false;
			}
			
			//Recebe o valor do combo de cliente
			var cmbClienteIdIndice = Form.cmbClienteId.selectedIndex;
			
			//Cria a variável edtPessoaId com o valor do combo de cliente
			var edtPessoaId = Form.cmbClienteId.options[cmbClienteIdIndice].value	
		
		}
		
		//Se for por fornecedor
		if (edtTipoPessoaValor == 2) 
		{
			
			if (Form.cmbFornecedorId.value == 0) 
			{
      	
				alert("É necessário selecionar um Fornecedor !");
				Form.cmbFornecedorId.focus();
				return false;
   		
			}
			
			//Recebe o valor do combo de fornecedor
			var cmbFornecedorIdIndice = Form.cmbFornecedorId.selectedIndex;
			
			//Cria a variável edtPessoaId com o valor do combo de fornecedor
			var edtPessoaId = Form.cmbFornecedorId.options[cmbFornecedorIdIndice].value		
		
		}
		
		//Se for por colaborador
		if (edtTipoPessoaValor == 3) 
		{
			
			if (Form.cmbColaboradorId.value == 0) 
			{
      	
				alert("É necessário selecionar um Colaborador !");
				Form.cmbColaboradorId.focus();
				return false;
			}
			
			//Recebe o valor do combo de colaborador
			var cmbColaboradorIdIndice = Form.cmbColaboradorId.selectedIndex;
			
			//Cria a variável edtPessoaId com o valor do combo de colaborador
			var edtPessoaId = Form.cmbColaboradorId.options[cmbColaboradorIdIndice].value		
		}


		if (parametro == 1)
		{
		  
			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarLista.php?TipoListagem=5&TipoPessoa=' + edtTipoPessoaValor + '&PessoaId='+ edtPessoaId + '&DataIni=' + Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;
 		}

 		else if (parametro == 2)
 		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarListaLote.php?TipoListagem=5&TipoPessoa=' + edtTipoPessoaValor + '&PessoaId='+ edtPessoaId + '&DataIni=' + Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}


	//Caso for por subgrupo
	if (edtAgruparValor == 6) 
	{
	 
	  
		//Recebe o valor do combo de subgrupo
		var cmbSubgrupoIdIndice = Form.cmbSubgrupoId.selectedIndex;
		var cmbSubgrupoIdValor = Form.cmbSubgrupoId.options[cmbSubgrupoIdIndice].value	

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
   		
			}			
   	
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
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


		if (parametro == 1)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarLista.php?TipoListagem=6&SubgrupoId='+ cmbSubgrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		else if (parametro == 2)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarListaLote.php?TipoListagem=6&SubgrupoId='+ cmbSubgrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}
	
	//Caso for por agrupado por grupo, subgrupo e categoria
	if (edtAgruparValor == 7)
	{
	 
		//Recebe o valor do combo de grupo
		var cmbGrupoIdIndice = Form.cmbGrupoId.selectedIndex;
		var cmbGrupoIdValor = Form.cmbGrupoId.options[cmbGrupoIdIndice].value	


		//Recebe o valor do combo de subgrupo
		var cmbSubgrupoIdIndice = Form.cmbSubgrupoId.selectedIndex;
		var cmbSubgrupoIdValor = Form.cmbSubgrupoId.options[cmbSubgrupoIdIndice].value	


		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
   		
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
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


		if (parametro == 1)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarLista.php?TipoListagem=7&GrupoId='+ cmbGrupoIdValor + '&SubgrupoId='+ cmbSubgrupoIdValor +  '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		else if (parametro == 2)
		{

			//Monta a url a acessar	 
			var urlCarrega = 'ContaPagarListaLote.php?TipoListagem=7&GrupoId='+ cmbGrupoIdValor + '&SubgrupoId='+ cmbSubgrupoIdValor +  '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;

		}

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	
	}	

}


//*** SE FOR IMPRESSÃO
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
	
	//Caso for para exibir por data de vencimento
	if (edtAgruparValor == 1) 
	{
		
		if (Form.edtDataIni.value == 0) 
		{
			
			alert('É necessário informar a data inicial !');
			Form.edtDataIni.focus();
			return false;
		
		}
		
		if (Form.edtDataFim.value == 0) 
		{
			
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
		var urlRelatorio = './relatorios/ContaPagarRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;
	
	}

	//Caso for por grupo
	if (edtAgruparValor == 2) 
	{
	 
		//Recebe o valor do combo de grupo
		var cmbGrupoIdIndice = Form.cmbGrupoId.selectedIndex;
		var cmbGrupoIdValor = Form.cmbGrupoId.options[cmbGrupoIdIndice].value	

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			}			
   	
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
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

		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaPagarRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=2&GrupoId='+ cmbGrupoIdValor   + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;		
	
	}



	//Caso for por situação
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

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
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
		
		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaPagarRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=4&TipoSituacao='+ edtSituacaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value  + '&Regiao='+ Form.cmbRegiaoId.value;
	
	}
	

	//Caso for por Sacado
	if (edtAgruparValor == 5) 
	{

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			
			}			
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
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

		//Captura o valor do combo do tipo de pessoa
		var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
	   
		for (var i=0; i < edtTipoPessoaValor.length; i++) 
		{
			
			if (edtTipoPessoaValor[i].checked == true) 
			{
			
				edtTipoPessoaValor = edtTipoPessoaValor[i].value;
				break;
			
			}
		
		}
	  
		//Se for por cliente
		if (edtTipoPessoaValor == 1) 
		{	  	
			
			if (Form.cmbClienteId.value == 0) 
			{
			
				alert("É necessário selecionar um Cliente !");
				Form.cmbClienteId.focus();
				return false;
   		
			}
			
			//Recebe o valor do combo de cliente
			var cmbClienteIdIndice = Form.cmbClienteId.selectedIndex;
			
			//Cria a variável edtPessoaId com o valor do combo de cliente
			var edtPessoaId = Form.cmbClienteId.options[cmbClienteIdIndice].value	
		
		}
		
		//Se for por fornecedor
		if (edtTipoPessoaValor == 2) 
		{
			
			if (Form.cmbFornecedorId.value == 0) 
			{
      	
				alert("É necessário selecionar um Fornecedor !");
				Form.cmbFornecedorId.focus();
				return false;
			
			}
			
			//Recebe o valor do combo de fornecedor
			var cmbFornecedorIdIndice = Form.cmbFornecedorId.selectedIndex;
			
			//Cria a variável edtPessoaId com o valor do combo de fornecedor
			var edtPessoaId = Form.cmbFornecedorId.options[cmbFornecedorIdIndice].value		
		
		}
		
		//Se for por colaborador
		if (edtTipoPessoaValor == 3) 
		{
			
			if (Form.cmbColaboradorId.value == 0) 
			{
      	
				alert("É necessário selecionar um Colaborador !");
				Form.cmbColaboradorId.focus();
				return false;
			
			}
			
			//Recebe o valor do combo de colaborador
			var cmbColaboradorIdIndice = Form.cmbColaboradorId.selectedIndex;
			
			//Cria a variável edtPessoaId com o valor do combo de colaborador
			var edtPessoaId = Form.cmbColaboradorId.options[cmbColaboradorIdIndice].value		
		
		}

		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaPagarRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=5&TipoPessoa=' + edtTipoPessoaValor + '&PessoaId='+ edtPessoaId  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;
	
	}



	//************
	//Caso for por subgrupo
	if (edtAgruparValor == 3) 
	{
	 
		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value	

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			}			
		
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

		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaPagarRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=3&EventoId='+ cmbEventoIdValor   + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;		
	
	}
	
	//Caso for por subgrupo
	if (edtAgruparValor == 6) 
	{
	 
		//Recebe o valor do combo de subgrupo
		var cmbSubgrupoIdIndice = Form.cmbSubgrupoId.selectedIndex;
		var cmbSubgrupoIdValor = Form.cmbSubgrupoId.options[cmbSubgrupoIdIndice].value	

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
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

		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaPagarRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=6&SubgrupoId='+ cmbSubgrupoIdValor   + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;		
	
	}
	
	//******************************	
	//Caso for por agrupamento (grupo, subgrupo e categoria)
	if (edtAgruparValor == 7) 
	{
	 
		//Recebe o valor do combo de grupo
		var cmbGrupoIdIndice = Form.cmbGrupoId.selectedIndex;
		var cmbGrupoIdValor = Form.cmbGrupoId.options[cmbGrupoIdIndice].value	

		//Recebe o valor do combo de subgrupo
		var cmbSubgrupoIdIndice = Form.cmbSubgrupoId.selectedIndex;
		var cmbSubgrupoIdValor = Form.cmbSubgrupoId.options[cmbSubgrupoIdIndice].value	
	

		if (Form.edtDataIni.value == 0) 
		{
			
			if (Form.edtDataFim.value != 0) 
			{
				
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
				return false;
			
			}			
		
		}

		if (Form.edtDataFim.value == 0) 
		{
			
			if (Form.edtDataIni.value != 0) 
			{
				
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
				return false;
			
			}			
		
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

		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaPagarRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=7&GrupoId='+ cmbGrupoIdValor   + '&SubgrupoId='+ cmbSubgrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor  + '&Regiao='+ Form.cmbRegiaoId.value;		
	
	}	

//Executa o relatório
abreJanela(urlRelatorio);

}
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="750">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Consulta Contas a Pagar</span>			  	
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
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440">
						<br/>
						<span style="color:#990000"><b>Selecione uma das consultas abaixo:</b></span>
						<br/>
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr valign="middle" style="padding: 1px">
							  <td height='20'>
									<input name="edtAgrupar" type="radio" value="1" checked="checked" onclick="wdCarregarFiltragem()"/> Contas a Pagar por Data de Vencimento
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="3" onclick="wdCarregarFiltragem()"/> Contas a Pagar por Evento
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="5" onclick="wdCarregarFiltragem()"/> Contas a Pagar por Sacado
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
									<td height="20">
									<input type="radio" name="edtAgrupar" value="6" onclick="wdCarregarFiltragem()"/> Contas a Pagar por Conta-caixa
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="2" onclick="wdCarregarFiltragem()"/> Contas a Pagar por Centro de Custo
								</td>
							</tr>		          		          
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="7" onclick="wdCarregarFiltragem()"/> Contas a Pagar por Conta-caixa e Centro de Custo
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="4" onclick="wdCarregarFiltragem()"/> Contas a Pagar por Situação
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
			<form id="form" name="cadastro" method="post">
			<div id="filtragem">
				<?php
					
					//Inclui o arquivo php vazio mas setando o input
					include "ContaPagarRelatorioData.php";
				
				?>
			</div>
		</td>
	</tr>
	<tr>
		<td>
	    <br/>
			<input id="btnVisualizar" name="btnVisualizar" type="button" class="button" title="Visualiza as contas com as opções informadas" value="Visualizar na Tela" style="width:110px" onclick="wdVisualizarRelatorio(1)">
			<input id="btnRelatorio" name="btnRelatorio" type="button" class="button" title="Emite o relatório das contas com as opções informadas" value="Emitir Relatório" style="width:110px" onclick="wdCarregarRelatorio()">
	    <input id="btnPagaLote" name="btnPagaLote" type="button" class="button" title="Permite o Pagamento em Lote de múltiplas contas" value="Pagamento em Lote" style="width:110px" onclick="wdVisualizarRelatorio(2)" />
			<br />
	    <br />	   	   		   		
		</td>   
	</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>		
			<span class="TituloModulo" style="color: #990000">Contas a Pagar com Vencimento em <?php echo date("d",mktime()) . " de " . $mes_nome . " de " . date("Y",mktime()) ?></span>
			<br/>
			<br/>
			<?php
			
				/*EXIBE AS CONTAS A PAGAR VENCENDO NO DIA*/
				
				//Monta a paginação dos resultados
				$sql = "SELECT 
						pag.id,
						pag.data,
						pag.valor,
						pag.tipo_pessoa,
						pag.pessoa_id,
						pag.data_vencimento,
						pag.descricao,
						pag.situacao,
						pag.origem_conta,
						pag.valor_pago,
						eve.nome as evento_nome
						FROM contas_pagar pag
						LEFT OUTER JOIN eventos eve ON eve.id = pag.evento_id
						WHERE pag.empresa_id = '$empresaId' AND pag.data_vencimento = '$dataHoje' 
						ORDER BY pag.descricao";
					   
				//Executa a Query
				$query = mysql_query($sql);
	  	  
				//verifica o número total de registros
				$tot_regs = mysql_num_rows($query); 	    
		  
				//Gera a variável com o total de contas a pagar
				$total_pagar = 0;
			
			?>
	   
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">

			<?php
	  
				//Caso houverem registros
				if ($tot_regs > 0) 
				{ 
				  
				  echo "<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
							<td width='416'>&nbsp;&nbsp;Dados do Sacado/Descrição da Conta a Pagar</td>
							<td width='66' align='center'>Emissão</td>
							<td width='66' align='center'>Vencto</td>
							<td width='80' align='right'>Valor</td>
							<td width='80' align='right'>A Pagar</td>
							<td width='65' align='center'>Situação</td>
							<td>&nbsp;</td>          
						</tr>";
						
				}
	  
				//Caso não houverem registros
				if ($tot_regs == 0) 
				{ 

					//Exibe uma linha dizendo que nao há registros
					echo "<tr height='24'>
							<td colspan='7' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
								<font color='#33485C'><strong>Não há contas a pagar para esta data</strong></font>
							</td>
						</tr>";	  
				
				}
				
				//Cria o array e o percorre para montar a listagem dinamicamente
				while ($dados_rec = mysql_fetch_array($query))
				{

					//Efetua o switch para recuperar o nome do sacado
					switch ($dados_rec[tipo_pessoa]) 
					{
		  
						case 1: //Se for cliente
							$sql = mysql_query("SELECT nome FROM clientes WHERE id = '$dados_rec[pessoa_id]'");
							$desc_pessoa = "Cliente";
							$dados_pessoa = mysql_fetch_array($sql);				
						break;
						case 2: //Se for fornecedor
							$sql = mysql_query("SELECT nome FROM fornecedores WHERE id = '$dados_rec[pessoa_id]'");
							$desc_pessoa = "Fornecedor";
							$dados_pessoa = mysql_fetch_array($sql);				
						break;
						case 3: //Se for colaborador
							$sql = mysql_query("SELECT nome FROM colaboradores WHERE id = '$dados_rec[pessoa_id]'");
							$desc_pessoa = "Colaborador";
							$dados_pessoa = mysql_fetch_array($sql);				
						break;			
					}		
		
					//Efetua o switch para o campo de situacao
					switch ($dados_rec[situacao]) 
					{
						
						case 1: $desc_situacao = "Em aberto"; break;
						case 2: $desc_situacao = "Pago"; break;
					}

					//Fecha o php, mas o while continua
				?>

				<tr height="16">
					<td height="20" style="border-top: 1px dotted">
						<font color="#CC3300" size="2" face="Tahoma">
						<a title="Clique para exibir esta conta a pagar" href="#" onclick="wdCarregarFormulario('ContaPagarExibe.php?ContaId=<?php echo $dados_rec[id] ?>','conteudo')">&nbsp;<?php echo $dados_pessoa[nome]; ?></a>
						</font>
						</br>
						<span style="font-size: 9px">&nbsp;<?php echo $dados_rec['descricao'] ?>
						<br/>
						<?php 
				
							if ($dados_rec["origem_conta"] == 2)
							{
						
								echo "<span style='color: #990000'>&nbsp;<b>$dados_rec[evento_nome]</b></span>";
					
							} 
							
							else if ($dados_rec["origem_conta"] == 1)
							{
					
								echo "<b>Gerada pelo Contas a Pagar</b>";	
					
							}
							
							else if ($dados_rec["origem_conta"] == 3)
							{
					
								echo "<b>Gerada por Pedido do Foto e Vídeo</b>";	
							}
				
						?>
						</span>      
					</td>
					<td align="center" style="border-top: 1px dotted">
						<?php echo DataMySQLRetornar($dados_rec[data]) ?>				
					</td>
					<td align="center" style="border-top: 1px dotted">
						<?php echo DataMySQLRetornar($dados_rec[data_vencimento]) ?>				
					</td>			
					<td align="right" style="border-top: 1px dotted">
						<?php 
							
							echo "R$ " . number_format($dados_rec[valor], 2, ",", ".");
							$total_pagar = $total_pagar + $dados_rec[valor]; 
						
						?>
					</td>
					<td align="right" style="border-top: 1px dotted">
						<?php 
							
							echo "R$ " . number_format($dados_rec[valor] - $dados_rec[valor_pago], 2, ",", ".");
							$saldo_pagar = $saldo_pagar + ($dados_rec[valor] - $dados_rec[valor_pago]);
						
						?>
					</td>
					<td align="center" style="border-top: 1px dotted">
						<?php echo $desc_situacao ?>				
					</td>
					<td style="border-top: 1px dotted" valign="middle">
						<?php 
					
							if ($desc_situacao == "Em aberto" && $nivelAcesso >= 5) 
							{
						
						?>
						<input name="btnPagar" type="button" class="button" id="btnPagar" title="Pagar a Conta" value="Pagar" style="width: 50px" onclick="wdCarregarFormulario('ContaPagarQuita.php?ContaId=<?php echo $dados_rec[id] ?>&headers=1','conteudo')" />
						<?php
							}
						?>
						&nbsp;						
					</td>			
				</tr>
			<?php
				//Fecha o WHILE
				};

				//Verifica se precisa imprimir o rodapé
	if ($tot_regs > 0) { 
	?>

	<tr height='16'>
    <td colspan="3" height="20" align="right"><strong>Total:&nbsp;&nbsp;</strong></td>
    <td height="20" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="border-top: 1px solid" align="right">
      <?php echo "R$ " . number_format($total_pagar, 2, ",", ".") ?>
		</td>
		<td height="20" valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' style="border-top: 1px solid" align="right">
      <?php echo "R$ " . number_format($saldo_pagar, 2, ",", ".") ?>
		</td>					
	</tr>	
	
	<?php
	//Fecha o IF
	};
	?>
		
	</table>	
	</td>
  </tr>  

   
</table>
