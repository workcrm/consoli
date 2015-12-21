<?php
###########
## Módulo para Contas a Receber
## Criado: 19/02/2009 - Maycon Edinger
## Alterado: 
## Alterações: 
###########


//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Desativar o CSS redundante
//<link rel='stylesheet' type='text/css' href='include/workStyle.css'>

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';

//Armazena o mês atual na variável
$mes = date('m',mktime());
$dataHoje = date('Y-m-d', mktime());

//Efetua o switch para determinar o nome do mes atual
switch ($mes) 
{
	case 1: $mes_nome = 'Janeiro';	break;
	case 2: $mes_nome = 'Fevereiro';	break;
	case 3: $mes_nome = 'Março';	break;
	case 4: $mes_nome = 'Abril';	break;
	case 5: $mes_nome = 'Maio';	break;
	case 6: $mes_nome = 'Junho';	break;
	case 7: $mes_nome = 'Julho';	break;
	case 8: $mes_nome = 'Agosto';	break;
	case 9: $mes_nome = 'Setembro';	break;
	case 10: $mes_nome = 'Outubro';	break;
	case 11: $mes_nome = 'Novembro';	break;
	case 12: $mes_nome = 'Dezembro';	break;
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
		wdCarregarFormulario('ContaReceberRelatorioData.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 2) 
	{
		wdCarregarFormulario('ContaReceberRelatorioGrupo.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 3) 
	{
		wdCarregarFormulario('ContaReceberRelatorioEvento.php?headers=1','filtragem','1');
	}

	if (edtAgruparValor == 4) 
	{
		wdCarregarFormulario('ContaReceberRelatorioSituacao.php?headers=1','filtragem','1');
	}		
	
	if (edtAgruparValor == 5) 
	{
		wdCarregarFormulario('ContaReceberRelatorioSacado.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 6) 
	{
		wdCarregarFormulario('ContaReceberRelatorioContaCaixa.php?headers=1&Tipo=E','filtragem','1');
	}	
	
	if (edtAgruparValor == 7) 
	{
		wdCarregarFormulario('ContaReceberRelatorioAgrupado.php?headers=1','filtragem','1');
	}	
	
	//Caso for para agrupar por evento e formando
	if (edtAgruparValor == 8) 
	{		
		//Carrega a tela de filtragem
		wdCarregarFormulario('ContaRelatorioEventoFormando.php?headers=1','filtragem','1');
	}
  
	//Caso for para agrupar por evento e conta-caixa
	if (edtAgruparValor == 9) 
	{		
		//Carrega a tela de filtragem
		wdCarregarFormulario('ContaRelatorioEventoContaCaixa.php?headers=1','filtragem','1');
	}
	
	//Caso for para agrupar por evento e curso	
	if (edtAgruparValor == 10) 
	{		
		//Carrega a tela de filtragem
		wdCarregarFormulario('ContaRelatorioEventoFormandoCurso.php?headers=1','filtragem','1');
	}
	
	//Caso for para agrupar por evento, formando e conta-caixa
	if (edtAgruparValor == 11) 
	{		
		//Carrega a tela de filtragem
		wdCarregarFormulario('ContaRelatorioEventoFormandoContaCaixa.php?headers=1','filtragem','1');
	}

}


function wdVisualizarRelatorio() 
{
	var Form;
	Form = document.cadastro;
  
	if (Form.chkParticipante.checked) 
	{
  
		var chkParticipanteValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkParticipanteValor = 0;
 	
	}
  
  
	if (Form.chkDataVencimento.checked) 
	{
  
		var chkDataVencimentoValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkDataVencimentoValor = 0;
 	
	}
  
  
	if (Form.chkValorContrato.checked) 
	{
  
		var chkValorContratoValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorContratoValor = 0;
 	
	}
  
	if (Form.chkValorBoleto.checked) 
	{
  
		var chkValorBoletoValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorBoletoValor = 0;
 	
	}
  
	if (Form.chkValorMultaJuros.checked) 
	{
  
		var chkValorMultaJurosValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorMultaJurosValor = 0;
 	
	}
  
	if (Form.chkValorReceber.checked) 
	{
  
		var chkValorReceberValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorReceberValor = 0;
 	
	}
  
	if (Form.chkValorRecebido.checked) 
	{
  
		var chkValorRecebidoValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorRecebidoValor = 0;
 	
	}
  
	if (Form.chkSaldoReceber.checked) 
	{
  
		var chkSaldoReceberValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkSaldoReceberValor = 0;
 	
	}
	
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

		//Monta a url a acessar
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	}


	//Caso for por grupo
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

		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=2&GrupoId='+ cmbGrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	}


	//Caso for por evento
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
		 
		//Caso não especificou um evento
		if (Form.cmbEventoId.value == 0) 
		{
		  
			alert("É necessário selecionar um evento !");      
			return false;
		
		} 
		 
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

		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=3&EventoId='+ cmbEventoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

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

		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=4&TipoSituacao='+ edtSituacaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
		
	}
		
	//Caso for por Sacado
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
			  
		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=5&TipoPessoa=' + edtTipoPessoaValor + '&PessoaId='+ edtPessoaId + '&DataIni=' + Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		

	}


	//Caso for por subgrupo
	if (edtAgruparValor == 6) 
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

		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=6&SubgrupoId='+ cmbSubgrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
		
	}
		
	//Caso for por agrupado por grupo, subgrupo e categoria
	if (edtAgruparValor == 7) 
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
		
		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=7&GrupoId='+ cmbGrupoIdValor + '&SubgrupoId='+ cmbSubgrupoIdValor +  '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		

	}	
		
		
	//Caso for por evento e formando
	if (edtAgruparValor == 8) 
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
	   
		//Caso não especificou um evento
		if (Form.cmbEventoId.value == 0) 
		{
			alert("É necessário selecionar um evento !");      
			return false;
		} 
		  
		//Caso especificou um evento, porém não um formando
		if (Form.cmbFormandoId.value == 0) 
		{
			alert("É necessário selecionar um formando !");      
			return false;
		} 
		  
		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
		
		//Recebe o valor do combo de formando
		var cmbFormandoIdIndice = Form.cmbFormandoId.selectedIndex;
		var cmbFormandoIdValor = Form.cmbFormandoId.value

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
			
			//Verifica se data final é maior que a data inicial
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
		
		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=8&EventoId='+ cmbEventoIdValor + '&FormandoId='+ cmbFormandoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		

	} 
	  
	  
	//Caso for por evento e conta-caixa
	if (edtAgruparValor == 9) 
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
	  

		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value

		//Recebe o valor do combo de formando
		var cmbContaCaixaIdIndice = Form.cmbContaCaixaId.selectedIndex;
		var cmbContaCaixaIdValor = Form.cmbContaCaixaId.value

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
				
			//Verifica se data final é maior que a data inicial
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

		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=9&EventoId='+ cmbEventoIdValor + '&ContaCaixaId='+ cmbContaCaixaIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	} 

	//Caso for por evento e curso
	if (edtAgruparValor == 10) 
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
   
		//Caso não especificou um evento
		if (Form.cmbEventoId.value == 0) 
		{
			alert("É necessário selecionar um evento !");      
			return false;
		} 
	  
		//Caso especificou um evento, porém não um curso
		if (Form.cmbCursoId.value == 0) 
		{
			alert("É necessário selecionar um curso !");      
			return false;
		} 
	  
		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
	
		//Recebe o valor do combo de curso
		var cmbCursoIdIndice = Form.cmbCursoId.selectedIndex;
		var cmbCursoIdValor = Form.cmbCursoId.value

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
				
			//Verifica se data final é maior que a data inicial
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
	
		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=10&EventoId='+ cmbEventoIdValor + '&CursoId='+ cmbCursoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	}

	//Caso for por evento, formando e conta-caixa
	if (edtAgruparValor == 11) 
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
	  
   
		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
	
		//Recebe o valor do combo de foramndo
		var cmbFormandoIdIndice = Form.cmbFormandoId.selectedIndex;
		var cmbFormandoIdValor = Form.cmbFormandoId.value
		
		//Recebe o valor do combo de conta-caixa
		var cmbContaCaixaIdIndice = Form.cmbContaCaixaId.selectedIndex;
		var cmbContaCaixaIdValor = Form.cmbContaCaixaId.value
	
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
				
			//Verifica se data final é maior que a data inicial
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

		//Monta a url a acessar	 
		var urlCarrega = 'ContaReceberLista.php?TipoListagem=11&EventoId='+ cmbEventoIdValor + '&FormandoId='+ cmbFormandoIdValor + '&ContaCaixaId='+ cmbContaCaixaIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	}	

}



//*** SE FOR IMPRESSÃO
function wdCarregarRelatorio() 
{

	var Form;
	Form = document.cadastro;

  
	if (Form.chkParticipante.checked) 
	{
  
		var chkParticipanteValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkParticipanteValor = 0;
	
	}
  
  
	if (Form.chkDataVencimento.checked) 
	{
  
		var chkDataVencimentoValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkDataVencimentoValor = 0;
 	
	}
  
  
	if (Form.chkValorContrato.checked) 
	{
  
		var chkValorContratoValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorContratoValor = 0;
 	
	}
  
	if (Form.chkValorBoleto.checked) 
	{
  
		var chkValorBoletoValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorBoletoValor = 0;
 	
	}
  
	if (Form.chkValorMultaJuros.checked) 
	{
  
		var chkValorMultaJurosValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorMultaJurosValor = 0;
 	
	}
  
	if (Form.chkValorReceber.checked) 
	{
  
		var chkValorReceberValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorReceberValor = 0;
 	
	}
  
	if (Form.chkValorRecebido.checked) 
	{
  
		var chkValorRecebidoValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkValorRecebidoValor = 0;
 	
	}
  
	if (Form.chkSaldoReceber.checked) 
	{
  
		var chkSaldoReceberValor = 1;
  
	} 
	
	else 
	
	{
  
		var chkSaldoReceberValor = 0;
 	
	}

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
			var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;
		}

	
	
		//Caso for por grupo
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
			var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=2&GrupoId='+ cmbGrupoIdValor   + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;		
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
			var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=4&TipoSituacao='+ edtSituacaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;
		}
	

	//Caso for por Sacado
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
		var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=5&TipoPessoa=' + edtTipoPessoaValor + '&PessoaId='+ edtPessoaId  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;
	}



	//************
	//Caso for por evento
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
   
		//Caso não especificou um evento
		if (Form.cmbEventoId.value == 0) 
		{
			alert("É necessário selecionar um evento !");      
			return false;
		} 
   
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
		
		//Verifica se o checkbox de obs está marcado
		if (Form.chkObs.checked) 
		{
			
			var chkObsValor = 1;
		
		} 
		
		else 
		
		{
			
			var chkObsValor = 0;
		}

		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=3&EventoId='+ cmbEventoIdValor   + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor + '&Observacoes=' + chkObsValor;		
	}
	
		
	//Caso for por conta-caixa
	if (edtAgruparValor == 6) 
	{
	 
   //Captura o valor referente ao radio button selecionado da situacao
  	var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) {
	    if (edtSituacaoValor[i].checked == true) {
	      edtSituacaoValor = edtSituacaoValor[i].value;
	      break;
	    }
  	}
   
	  //Recebe o valor do combo de subgrupo
	  var cmbSubgrupoIdIndice = Form.cmbSubgrupoId.selectedIndex;
	  var cmbSubgrupoIdValor = Form.cmbSubgrupoId.options[cmbSubgrupoIdIndice].value	

		if (Form.edtDataIni.value == 0) {
			if (Form.edtDataFim.value != 0) {
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
      	return false;
   		}			
   	}

		if (Form.edtDataFim.value == 0) {
			if (Form.edtDataIni.value != 0) {
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
      	return false;
   		}			
   	}
		
		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) {
				
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
		}

		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=6&SubgrupoId='+ cmbSubgrupoIdValor   + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;		
	}
	
	//******************************	
	
	//Caso for por agrupamento (centro de custo e conta-caixa)
	if (edtAgruparValor == 7) {
	 
   //Captura o valor referente ao radio button selecionado da situacao
  	var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) {
	    if (edtSituacaoValor[i].checked == true) {
	      edtSituacaoValor = edtSituacaoValor[i].value;
	      break;
	    }
  	}
	 
		//Recebe o valor do combo de grupo
	  var cmbGrupoIdIndice = Form.cmbGrupoId.selectedIndex;
	  var cmbGrupoIdValor = Form.cmbGrupoId.options[cmbGrupoIdIndice].value	

		//Recebe o valor do combo de subgrupo
	  var cmbSubgrupoIdIndice = Form.cmbSubgrupoId.selectedIndex;
	  var cmbSubgrupoIdValor = Form.cmbSubgrupoId.options[cmbSubgrupoIdIndice].value	
	

		if (Form.edtDataIni.value == 0) {
			if (Form.edtDataFim.value != 0) {
				alert('É necessário informar a data inicial !');
				Form.edtDataIni.focus();
      	return false;
   		}			
   	}

		if (Form.edtDataFim.value == 0) {
			if (Form.edtDataIni.value != 0) {
				alert('É necessário informar a data final !');
				Form.edtDataFim.focus();
      	return false;
   		}			
   	}
		
		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) {
				
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
		}

		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=7&GrupoId='+ cmbGrupoIdValor   + '&SubgrupoId='+ cmbSubgrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;		
	}	
  
	
	//Caso for por evento e formando
	if (edtAgruparValor == 8) 
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
   
		//Caso não especificou um evento
		if (Form.cmbEventoId.value == 0) 
		{
			
			alert("É necessário selecionar um evento !");      
			return false;
		
		} 
      
		//Caso especificou um evento, porém não um formando
		if (Form.cmbFormandoId.value == 0) 
		{
      
			alert("É necessário selecionar um formando !");      
			return false;
		} 
      
   
		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
    
		//Recebe o valor do combo de formando
		var cmbFormandoIdIndice = Form.cmbFormandoId.selectedIndex;
		var cmbFormandoIdValor = Form.cmbFormandoId.value	

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
		
		//Verifica se o checkbox de obs está marcado
		if (Form.chkObs.checked) 
		{
			
			var chkObsValor = 1;
		
		} 
		
		else 
		
		{
			
			var chkObsValor = 0;
		}

		//Monta a url do relatório		
		var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=8&EventoId='+ cmbEventoIdValor   + '&FormandoId='+ cmbFormandoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor + '&Observacoes=' + chkObsValor;	;		
	}  
  
  
	//Caso for por evento e conta-caixa
	if (edtAgruparValor == 9) 
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
   
		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
    
		//Recebe o valor do combo de formando
		var cmbContaCaixaIdIndice = Form.cmbContaCaixaId.selectedIndex;
		var cmbContaCaixaIdValor = Form.cmbContaCaixaId.value	

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
		var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=9&EventoId='+ cmbEventoIdValor   + '&ContaCaixaId='+ cmbContaCaixaIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;		
	 
	}

	//Caso for por evento e curso
	if (edtAgruparValor == 10) 
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
   
		//Caso não especificou um evento
		if (Form.cmbEventoId.value == 0) 
		{
			
			alert("É necessário selecionar um evento !");      
			return false;
		
		} 
      
		//Caso especificou um evento, porém não um curso
		if (Form.cmbCursoId.value == 0) 
		{
      
			alert("É necessário selecionar um curso !");      
			return false;
		} 
      
   
		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
    
		//Recebe o valor do combo de curso
		var cmbCursoIdIndice = Form.cmbCursoId.selectedIndex;
		var cmbCursoIdValor = Form.cmbCursoId.value	

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
		var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=10&EventoId='+ cmbEventoIdValor   + '&CursoId='+ cmbCursoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;		
	}

	//Caso for por evento, formando e conta-caixa
	if (edtAgruparValor == 11) 
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
   
		//Caso não especificou um evento
		if (Form.cmbEventoId.value == 0) 
		{
			
			alert("É necessário selecionar um evento !");      
			return false;
		
		} 
      
		//Caso especificou um evento, porém não um formando
		if (Form.cmbFormandoId.value == 0) 
		{
      
			alert("É necessário selecionar um formando !");      
			return false;
		} 
      
   
		//Recebe o valor do combo de evento
		var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
		var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
    
		//Recebe o valor do combo de formando
		var cmbFormandoIdIndice = Form.cmbFormandoId.selectedIndex;
		var cmbFormandoIdValor = Form.cmbFormandoId.value

		//Recebe o valor do combo de formando
		var cmbContaCaixaIdIndice = Form.cmbContaCaixaId.selectedIndex;
		var cmbContaCaixaIdValor = Form.cmbContaCaixaId.value		

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
		var urlRelatorio = './relatorios/ContaReceberRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=11&EventoId='+ cmbEventoIdValor + '&FormandoId='+ cmbFormandoIdValor + '&ContaCaixaId='+ cmbContaCaixaIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor + '&Participante=' + chkParticipanteValor + '&DataVencimento=' + chkDataVencimentoValor + '&ValorContrato=' + chkValorContratoValor + '&ValorBoleto=' + chkValorBoletoValor + '&ValorMultaJuros=' + chkValorMultaJurosValor + '&ValorReceber=' + chkValorReceberValor + '&ValorRecebido=' + chkValorRecebidoValor + '&SaldoReceber=' + chkSaldoReceberValor;		
	}

	//Executa o relatório
	abreJanela(urlRelatorio);
	
}
</script>


<?php
	/*EXIBE AS CONTAS A RECEBER VENCENDO NO DIA*/
		
	//Monta a paginação dos resultados
	$sql = "SELECT 
			rec.id,
			rec.data,
			rec.valor_original,
			rec.valor,
			rec.valor_boleto,
			rec.valor_multa_juros,
			rec.valor_recebido,
			rec.tipo_pessoa,
			rec.pessoa_id,
			rec.data_vencimento,
			rec.descricao,
			rec.situacao,
			rec.restricao,
			rec.origem_conta,
			rec.valor_recebido,
			rec.boleto_id,
			eve.nome as evento_nome
			FROM contas_receber rec
			LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
			WHERE rec.empresa_id = '$empresaId' AND rec.data_vencimento = '$dataHoje' 
			ORDER BY rec.descricao";
			   
			//Executa a Query
			$query = mysql_query($sql);
	  
			//verifica o número total de registros
			$tot_regs = mysql_num_rows($query); 	    
  
			//Gera a variável com o total de contas a pagar
			$total_pagar = 0;
?>
      
<form id="form" name="muda" action="sistema.php?ModuloNome=ContaReceberListaAltera" method="post">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="750">
						<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Contas a Receber com vencimento em <?php echo date("d",mktime()) . " de " . $mes_nome . " de " . date("Y",mktime()) ?></span>			  	
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
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>	   
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">

				<?php
				  
					//Caso houverem registros
					if ($tot_regs > 0) 
					{ 
					
						echo "
						<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
							<td style='border-right: 1px dotted'>&nbsp;Sacado/Descrição da Conta a Receber</td>
						  <td style='border-right: 1px dotted' width='20' align='center'>P</td>
						  <td style='border-right: 1px dotted' width='82' align='center'>Data de<br/>Vencimento</td>          
						  <td style='border-right: 1px dotted' width='62' align='center'>Valor do<br/>Contrato</td>
						  <td style='border-right: 1px dotted' width='60' align='center'>Custo do<br/>Boleto</td>
						  <td style='border-right: 1px dotted' width='60' align='center'>Multa<br/>e Juros</td>
						  <td style='border-right: 1px dotted' width='66' align='center'>Valor a<br/>Receber</td>
						  <td style='border-right: 1px dotted' width='62' align='center'>Valor<br/>Recebido</td>
						  <td width='62' align='center'>Saldo a<br/>Receber</td>                    
						</tr>";}
				  
						//Caso não houverem registros
						if ($tot_regs == 0) 
						{ 

							//Exibe uma linha dizendo que nao há registros
							echo "
								<tr height='24'>
									<td colspan='9' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
										<slot><font color='#33485C'><strong>Não há contas a receber a vencer com vencimento para hoje</strong></font></slot>
									</td>
								</tr>";	  
						} 
    
						else
    
						{	
      
							//Adiciona o acesso a entidade de criação do componente data
							include_once("CalendarioPopUp.php");
							
							//Cria um objeto do componente data
							$objData = new tipData();
							
							//Define que não deve exibir a hora no calendario
							$objData->bolExibirHora = false;
							
							//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
							$objData->MontarJavaScript();
							  
							//Cria a variavel zerada para o contador de checkboxes
							$edtItemChk = 0;
    
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
										$desc_participante = "&nbsp;";
										$edtValorCapital = "0,00";
										$edtValorCapitalSoma = 0;				
									break;
									case 2: //Se for fornecedor
										$sql = mysql_query("SELECT nome FROM fornecedores WHERE id = '$dados_rec[pessoa_id]'");
										$desc_pessoa = "Fornecedor";
										$dados_pessoa = mysql_fetch_array($sql);      
										$desc_participante = "&nbsp;";
										$edtValorCapital = "0,00";
										$edtValorCapitalSoma = 0;				
									break;
									case 3: //Se for colaborador
										$sql = mysql_query("SELECT nome FROM colaboradores WHERE id = '$dados_rec[pessoa_id]'");
										$desc_pessoa = "Colaborador";
										$dados_pessoa = mysql_fetch_array($sql);       
										$desc_participante = "&nbsp;";
										$edtValorCapital = "0,00";
										$edtValorCapitalSoma = 0;				
									break;
									case 4: //Se for formando
										$sql = mysql_query("SELECT nome, participante, chk_culto, chk_colacao, chk_jantar, chk_baile, valor_capital FROM eventos_formando WHERE id = '$dados_rec[pessoa_id]'");
										$desc_pessoa = "Formando";
										$dados_pessoa = mysql_fetch_array($sql);
        
										$desc_participante = "";
					
										if ($dados_pessoa["chk_culto"] == 1)
										{
										
											$desc_participante .= "<span title='Formando Participa do Culto'>M</span>&nbsp;";
											
										}
										
										if ($dados_pessoa["chk_colacao"] == 1)
										{
										
											$desc_participante .= "<span title='Formando Participa da Colação'>C</span>&nbsp;";
											
										}
										
										if ($dados_pessoa["chk_jantar"] == 1)
										{
										
											$desc_participante .= "<span title='Formando Participa do Jantar'>J</span>&nbsp;";
											
										}
										
										if ($dados_pessoa["chk_baile"] == 1)
										{
										
											$desc_participante .= "<span title='Formando Participa do Baile'>B</span>";
											
										}
        
										$edtValorCapital = number_format($dados_pessoa[valor_capital], 2, ",", ".");
										$edtValorCapitalSoma = $dados_pessoa[valor_capital];
        				
									break;
									case 5: //Se for por evento
										$sql = mysql_query("SELECT nome FROM eventos WHERE id = '$dados_rec[pessoa_id]'");
										$desc_pessoa = "Evento";
										$dados_pessoa = mysql_fetch_array($sql);      
										$desc_participante = "&nbsp;";
										$edtValorCapital = "0,00";
										$edtValorCapitalSoma = 0;				
									break;			
								}		

								//Efetua o switch para o campo de situacao
								switch ($dados_rec[situacao]) 
								{
									
									case 1: $desc_situacao = "<span style='color: #990000'>[A Vencer]</span>"; break;
									case 2: $desc_situacao = "<span style='color: blue'>[Recebida]</span>"; break;
								}
								
								//Se o formando estiver com restricoes financeiras, muda a cor da celula
								if ($dados_rec["restricao"] == 2)
								{
								
									$cor_celula = "#F0D9D9";
									
								}
								
								else
								
								{
								
									$cor_celula = "#FFFFFF";
									
								}

								//Fecha o php, mas o while continua
	  ?>

			<tr height="16">      
				<td bgcolor="<?php echo $cor_celula ?>" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px" height="20">        
					<font color="#CC3300" size="2" face="Tahoma">
					<a title="Clique para exibir esta conta a Receber" href="#" onclick="wdCarregarFormulario('ContaReceberExibe.php?ContaId=<?php echo $dados_rec[id] ?>','conteudo')">&nbsp;<?php echo $dados_pessoa[nome]; ?></a>
					</font>				
					<br/>
					<span style="font-size: 9px">&nbsp;<?php echo $dados_rec["descricao"] ?>
					<br/>
					<?php 
				
						if ($dados_rec["origem_conta"] == 2)
						{
						
							echo "<span style='color: #990000'>&nbsp;<b>$dados_rec[evento_nome]</b></span>&nbsp;&nbsp;EM " . DataMySQLRetornar($dados_rec[data]);
					
						} 
						else 
						{
					
							if ($dados_rec[boleto_id] > 0) 
							{
					
								echo "&nbsp;<b>Gerada pelo Contas a Receber&nbsp;<span style='color: #990000'>(VIA BOLETO)</span></b>&nbsp;&nbsp;EM " . DataMySQLRetornar($dados_rec[data]);
              
							}
							else
							{
              
								echo "&nbsp;<b>Gerada pelo Contas a Receber</b>&nbsp;&nbsp;EM " . DataMySQLRetornar($dados_rec[data]);
              
							}
            
						}
				
					?>
					</span>
					<br/>
					&nbsp;<strong><?php echo $desc_situacao ?></strong>
					<?php 
						
						if ($dados_rec[boleto_id] > 0) 
						{
					 
							echo "<input class='button' style='width: 80px; height: 16px' title='Visualizar Boleto' name='btnBoleto' type='button' id='btnBoleto' value='Exibir Boleto' onclick='abreJanelaBoleto(\"./boletos/boleto_bb.php?TipoBol=1&BoletoId=$dados_rec[boleto_id]&EmpresaId=$empresaId&EmpresaNome=$empresaNome\")' style='cursor: pointer' />&nbsp;";					
	
							if ($dados_rec[situacao] == 1) 
							{
  
								echo "<input class='button' style='width: 90px; height: 16px' title='Receber esta boleto e quita a conta' name='btnRecebeBoleto' type='button' id='btnRecebeBoleto' value='Baixar Boleto' onclick='wdCarregarFormulario(\"BoletoQuita.php?BoletoId=$dados_rec[boleto_id]&headers=1\",\"conteudo\")' style='cursor: pointer' />";					
  
							}

						}
						else
						{

							if ($dados_rec[situacao] == 1) 
							{
  
								echo "<input class='button' style='width: 80px; height: 16px' title='Receber esta conta' name='btnRecebe' type='button' id='btnRecebe' value='Receber Conta' onclick='wdCarregarFormulario(\"ContaReceberQuita.php?ContaId=$dados_rec[id]&headers=1\",\"conteudo\")' style='cursor: pointer' />";					
  
							}
          
						}
					?>      
				</td>
				<td bgcolor="<?php echo $cor_celula ?>" style="border-bottom: 1px solid; border-right: 1px dotted" align="center">
					<?php echo $desc_participante ?>				
				</td>
				<td bgcolor="<?php echo $cor_celula ?>" style="border-bottom: 1px solid; border-right: 1px dotted" align="center">
					<?php
					           
						echo DataMySQLRetornar($dados_rec[data_vencimento]);
				
					?>       				
				</td>
				<td bgcolor="#F0D9D9" style="border-bottom: 1px solid; border-right: 1px dotted" align="center">
					<?php 
					               
						echo "<strong><span style='color: blue'>" . number_format($dados_rec[valor_original], 2, ",", ".") . "</span></strong>";

						$total_original = $total_original + $dados_rec[valor_original];
        
					?>
				</td>      
				<td bgcolor="<?php echo $cor_celula ?>" style="border-bottom: 1px solid; border-right: 1px dotted" align="center">
					<?php 
            
						echo "<strong>" . number_format($dados_rec[valor_boleto], 2, ",", ".") . "</strong>";

						$total_boletos = $total_boletos + $dados_rec[valor_boleto];

					?>
				</td>        
				<td bgcolor="<?php echo $cor_celula ?>" style="border-bottom: 1px solid; border-right: 1px dotted" align="center">
					<?php 					
            
						echo "<strong>" . number_format($dados_rec[valor_multa_juros], 2, ",", ".") . "</strong>";
            							
						$total_multa_juros = $total_multa_juros + $dados_rec[valor_multa_juros];     
 
					?>
				</td>          			                  
				<td bgcolor="#FFFFCD" style="border-bottom: 1px solid; border-right: 1px dotted" align="center">
					<?php
						
						echo "<strong>" . number_format($dados_rec[valor], 2, ",", ".") . "</strong>"; 
			  
						$total_receber = $total_receber + $dados_rec[valor];          
					?>
				</td>
				<td bgcolor="<?php echo $cor_celula ?>" style="border-bottom: 1px solid; border-right: 1px dotted" align="center">
					<strong><span style="color: #031C98;">
					<?php 
        
						echo number_format($dados_rec[valor_recebido], 2, ",", ".");
          
						$total_recebido = $total_recebido + $dados_rec[valor_recebido];
          				  
					?>
					</span></strong>
				</td>
				<td bgcolor="<?php echo $cor_celula ?>" style="border-bottom: 1px solid;" align="center">
					<strong><span style="color: #990000;">
					<?php 
					
						echo number_format($dados_rec[valor] - $dados_rec[valor_recebido], 2, ",", ".");
                   
						$saldo_receber = $saldo_receber + ($dados_rec[valor] - $dados_rec[valor_recebido]);				  
					?>
					</span></strong>				
				</td>						
			</tr>
		<?php
		//Fecha o WHILE
		};

		//Envia com o formulario o total final do contador para efetuar o for depois
 	?>
  
  <input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>" />
	
  <?php
	//Fecha o if de se tem registros
	}
  
	//Verifica se precisa imprimir o rodapé
	if ($tot_regs > 0) 
	{ 
	?>

	<tr height="16">
    <td colspan="3" height="20" align="right" style="border-right: 1px dotted"><strong>Total:&nbsp;&nbsp;</strong></td>
    <td bgcolor="#F0D9D9" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_original, 2, ",", ".") ?></span>
		</td>
    <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_boletos, 2, ",", ".") ?></span>
		</td>
    <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_multa_juros, 2, ",", ".") ?></span>
		</td>
    <td bgcolor="#FFFFCD" height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_receber, 2, ",", ".") ?></span>
		</td>
    <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center" style="border-right: 1px dotted">
      <span style="color: #990000"><?php echo number_format($total_recebido, 2, ",", ".") ?></span>
		</td>
		<td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="center">
      <span style="color: #990000"><?php echo number_format($saldo_receber, 2, ",", ".") ?></span>
		</td>					
	</tr>
	
	<?php
	//Fecha o IF
	};
	?>
		
				</table>
			</form>	
		</td>
	</tr>  

	<tr>
		<td> 
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440">
						<br/>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Consulta Contas a Receber</span>
						<br/>
						Selecione uma das consultas abaixo:
						<br/>
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input name="edtAgrupar" type="radio" value="1" checked="checked" onclick="wdCarregarFiltragem()"/> Contas a Receber por Data de Vencimento
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="3" onclick="wdCarregarFiltragem()"/> Contas a Receber por Evento
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="8" onclick="wdCarregarFiltragem()"/> Contas a Receber por Evento e Formando
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="11" onclick="wdCarregarFiltragem()"/> Contas a Receber por Evento, Formando e Conta-Caixa
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="10" onclick="wdCarregarFiltragem()"/> Contas a Receber por Evento e Curso
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="9" onclick="wdCarregarFiltragem()"/> Contas a Receber por Evento e Conta-caixa
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="5" onclick="wdCarregarFiltragem()"/> Contas a Receber por Sacado
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="6" onclick="wdCarregarFiltragem()"/> Contas a Receber por Conta-caixa
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="2" onclick="wdCarregarFiltragem()"/> Contas a Receber por Centro de Custo
								</td>
							</tr>		          		          
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="7" onclick="wdCarregarFiltragem()"/> Contas a Receber por Conta-caixa e Centro de Custo
								</td>
							</tr>
							<tr valign="middle" style="padding: 1px">
								<td height="20">
									<input type="radio" name="edtAgrupar" value="4" onclick="wdCarregarFiltragem()"/> Contas a Receber por Situação
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
					    include "ContaReceberRelatorioData.php";
					?>
				</div>
		</td>
	</tr>
	<tr>
			<td>
				<br/>          
				<span class="TituloModulo">Selecione as colunas a incluir na consulta:</span>
				<table width="626" cellpadding="0" cellspacing="0" border="0" class="listView">
					<tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">              
						<td style="border-right: 1px dotted" align="center">Participante</td>
						<td style="border-right: 1px dotted" width="86" align="center">Data de<br/>Vencimento</td>          
						<td style="border-right: 1px dotted" width="86" align="center">Valor do<br/>Contrato</td>
						<td style="border-right: 1px dotted" width="86" align="center">Custo do<br/>Boleto</td>
						<td style="border-right: 1px dotted" width="76" align="center">Multa<br/>e Juros</td>
						<td style="border-right: 1px dotted" width="76" align="center">Valor a<br/>Receber</td>
						<td style="border-right: 1px dotted" width="76" align="center">Valor<br/>Recebido</td>
						<td width="62" align="center">Saldo a<br/>Receber</td>                    
					</tr> 
					<tr height="16">              
						<td style="border-right: 1px dotted" align="center">
							<input name="chkParticipante" type="checkbox" value="1" style="border: 0px" title="Clique para marcar ou desmarcar a coluna de participante" checked="checked"/>				
						</td>
						<td style="border-right: 1px dotted" align="center">
							<input name="chkDataVencimento" type="checkbox" value="1" style="border: 0px" title="Clique para marcar ou desmarcar a coluna de data do vencimento" checked="checked"/>       				
						</td>			
						<td bgcolor="#F0D9D9" style="border-right: 1px dotted" align="center">
							<input name="chkValorContrato" type="checkbox" value="1" style="border: 0px" title="Clique para marcar ou desmarcar a coluna de valor do contrato" checked="checked"/>
						</td>      
						<td style="border-right: 1px dotted" align="center">
							<input name="chkValorBoleto" type="checkbox" value="1" style="border: 0px" title="Clique para marcar ou desmarcar a coluna de custo do boleto" checked="checked"/>
						</td>        
						<td style="border-right: 1px dotted" align="center">
							<input name="chkValorMultaJuros" type="checkbox" value="1" style="border: 0px" title="Clique para marcar ou desmarcar a coluna de multa e juros" checked="checked"/>
						</td>          			                  
						<td bgcolor="#FFFFCD" style="border-right: 1px dotted" align="center">
							<input name="chkValorReceber" type="checkbox" value="1" style="border: 0px" title="Clique para marcar ou desmarcar a coluna de valor a receber" checked="checked"/>
						</td>
						<td style="border-right: 1px dotted" align="center">
							<input name="chkValorRecebido" type="checkbox" value="1" style="border: 0px" title="Clique para marcar ou desmarcar a coluna de valor recebido" checked="checked"/>
						</td>
						<td align="center">
							<input name="chkSaldoReceber" type="checkbox" value="1" style="border: 0px" title="Clique para marcar ou desmarcar a coluna de saldo a receber" checked="checked"/>				
						</td>						
					</tr>
				</table>
			</form>  	   		   		
		</td>   
	</tr>     
    <tr>
		<td>
		    <br/>
			<input class="button" title="Visualiza as contas com as opções informadas" name="btnVisualizar" type="button" id="btnVisualizar" value="Visualizar na Tela" style="width:100px" onclick="wdVisualizarRelatorio()">
			<input class="button" title="Emite o relatório das contas com as opções informadas" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()">
		    <br />
		    <br />	   	   		   		
		</td>   
	</tr>   
</table>