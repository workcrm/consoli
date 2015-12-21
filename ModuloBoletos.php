<?php
###########
## Módulo para Contas a Receber
## Criado: 19/02/2009 - Maycon Edinger
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

//Armazena o mês atual na variável
$mes = date("m",mktime());
$dataHoje = date("Y-m-d", mktime());

//Efetua o switch para determinar o nome do mes atual
switch ($mes) {
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
function wdCarregarFiltragem() {
   
	 //Captura o valor referente ao radio button selecionado
   var edtAgruparValor = document.getElementsByName('edtAgrupar');
   
	 for (var i=0; i < edtAgruparValor.length; i++) {
     if (edtAgruparValor[i].checked == true) {
       edtAgruparValor = edtAgruparValor[i].value;
       break;
     }
   }

	if (edtAgruparValor == 1) {
		wdCarregarFormulario('BoletoRelatorioData.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 2) {
		wdCarregarFormulario('BoletoRelatorioGrupo.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 3) {
		wdCarregarFormulario('BoletoRelatorioEvento.php?headers=1','filtragem','1');
	}

	if (edtAgruparValor == 4) {
		wdCarregarFormulario('BoletoRelatorioSituacao.php?headers=1','filtragem','1');
	}		
	
	if (edtAgruparValor == 5) {
		wdCarregarFormulario('BoletoRelatorioSacado.php?headers=1','filtragem','1');
	}
	
	if (edtAgruparValor == 6) {
		wdCarregarFormulario('BoletoRelatorioSubgrupo.php?headers=1&Tipo=E','filtragem','1');
	}	
	
	if (edtAgruparValor == 7) {
		wdCarregarFormulario('BoletoRelatorioAgrupado.php?headers=1','filtragem','1');
	}	
}

function wdVisualizarRelatorio() {
	var Form;
	Form = document.cadastro;
	
	//Captura o valor referente ao radio button selecionado
  var edtAgruparValor = document.getElementsByName('edtAgrupar');
   
	for (var i=0; i < edtAgruparValor.length; i++) {
    if (edtAgruparValor[i].checked == true) {
      edtAgruparValor = edtAgruparValor[i].value;
      break;
    }
  }
  
  	//Captura o valor referente ao radio button selecionado da situacao
  	var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) {
	    if (edtSituacaoValor[i].checked == true) {
	      edtSituacaoValor = edtSituacaoValor[i].value;
	      break;
	    }
  	}
  
	
	//Caso for para exibir por data de vencimento
	if (edtAgruparValor == 1) {
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
		var urlCarrega = 'BoletoLista.php?TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	}


	//Caso for por grupo
	if (edtAgruparValor == 2) {
	 
	  //Recebe o valor do combo de grupo
	  var cmbGrupoIdIndice = Form.cmbGrupoId.selectedIndex;
	  var cmbGrupoIdValor = Form.cmbGrupoId.options[cmbGrupoIdIndice].value	

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

	  //Monta a url a acessar	 
	  var urlCarrega = 'BoletoLista.php?TipoListagem=2&GrupoId='+ cmbGrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;

	  //Acessa a listagem das contas
	  wdCarregarFormulario(urlCarrega,'conteudo');		
	}


	//Caso for por evento
	if (edtAgruparValor == 3) {
	 
	  //Recebe o valor do combo de evento
	  var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
	  var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value	

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

	  //Monta a url a acessar	 
	  var urlCarrega = 'BoletoLista.php?TipoListagem=3&EventoId='+ cmbEventoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;

	  //Acessa a listagem das contas
	  wdCarregarFormulario(urlCarrega,'conteudo');		
	}

	//Caso for por situação
	if (edtAgruparValor == 4) {
	 
	  //Captura o valor referente ao radio button selecionado da situacao
  	var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) {
	    if (edtSituacaoValor[i].checked == true) {
	      edtSituacaoValor = edtSituacaoValor[i].value;
	      break;
	    }
  	}

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

	  //Monta a url a acessar	 
	  var urlCarrega = 'BoletoLista.php?TipoListagem=4&TipoSituacao='+ edtSituacaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

	  //Acessa a listagem das contas
	  wdCarregarFormulario(urlCarrega,'conteudo');		
	}
	
	//Caso for por Sacado
	if (edtAgruparValor == 5) {

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

	  //Captura o valor do combo do tipo de pessoa
		var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
	   
		for (var i=0; i < edtTipoPessoaValor.length; i++) {
	     if (edtTipoPessoaValor[i].checked == true) {
	       edtTipoPessoaValor = edtTipoPessoaValor[i].value;
	       break;
	     }
	  }
	  
	  //Se for por cliente
		if (edtTipoPessoaValor == 1) {	  	
			if (Form.cmbClienteId.value == 0) {
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
		if (edtTipoPessoaValor == 2) {
			if (Form.cmbFornecedorId.value == 0) {
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
		if (edtTipoPessoaValor == 3) {
			if (Form.cmbColaboradorId.value == 0) {
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
	  var urlCarrega = 'BoletoLista.php?TipoListagem=5&TipoPessoa=' + edtTipoPessoaValor + '&PessoaId='+ edtPessoaId + '&DataIni=' + Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;

	  //Acessa a listagem das contas
	  wdCarregarFormulario(urlCarrega,'conteudo');		
	}


	//Caso for por subgrupo
	if (edtAgruparValor == 6) {
	 
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

	  //Monta a url a acessar	 
	  var urlCarrega = 'BoletoLista.php?TipoListagem=6&SubgrupoId='+ cmbSubgrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;

	  //Acessa a listagem das contas
	  wdCarregarFormulario(urlCarrega,'conteudo');		
	}
	
	//Caso for por agrupado por grupo, subgrupo e categoria
	if (edtAgruparValor == 7) {
	 
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

	  //Monta a url a acessar	 
	  var urlCarrega = 'BoletoLista.php?TipoListagem=7&GrupoId='+ cmbGrupoIdValor + '&SubgrupoId='+ cmbSubgrupoIdValor +  '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;

	  //Acessa a listagem das contas
	  wdCarregarFormulario(urlCarrega,'conteudo');		
	}	
}



//*** SE FOR IMPRESSÃO
function wdCarregarRelatorio() {

var Form;
Form = document.cadastro;

	//Captura o valor referente ao radio button selecionado
  var edtAgruparValor = document.getElementsByName('edtAgrupar');
   
	for (var i=0; i < edtAgruparValor.length; i++) {
    if (edtAgruparValor[i].checked == true) {
      edtAgruparValor = edtAgruparValor[i].value;
      break;
    }
  }
  
  	//Captura o valor referente ao radio button selecionado da situacao
  	var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) {
	    if (edtSituacaoValor[i].checked == true) {
	      edtSituacaoValor = edtSituacaoValor[i].value;
	      break;
	    }
  	}  
	
	//Caso for para exibir por data de vencimento
	if (edtAgruparValor == 1) {
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
		var urlRelatorio = './relatorios/BoletoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;
	}

	
	
	//Caso for por grupo
	if (edtAgruparValor == 2) {
	 
	  //Recebe o valor do combo de grupo
	  var cmbGrupoIdIndice = Form.cmbGrupoId.selectedIndex;
	  var cmbGrupoIdValor = Form.cmbGrupoId.options[cmbGrupoIdIndice].value	

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
		var urlRelatorio = './relatorios/BoletoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=2&GrupoId='+ cmbGrupoIdValor   + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;		
	}



	//Caso for por situação
	if (edtAgruparValor == 4) {
	 
	  //Captura o valor referente ao radio button selecionado da situacao
  	var edtSituacaoValor = document.getElementsByName('edtSituacao');
   
		for (var i=0; i < edtSituacaoValor.length; i++) {
	    if (edtSituacaoValor[i].checked == true) {
	      edtSituacaoValor = edtSituacaoValor[i].value;
	      break;
	    }
  	}

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
		var urlRelatorio = './relatorios/BoletoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=4&TipoSituacao='+ edtSituacaoValor  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;
	}
	

	//Caso for por Sacado
	if (edtAgruparValor == 5) {

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

	  //Captura o valor do combo do tipo de pessoa
		var edtTipoPessoaValor = document.getElementsByName('edtTipoPessoa');
	   
		for (var i=0; i < edtTipoPessoaValor.length; i++) {
	     if (edtTipoPessoaValor[i].checked == true) {
	       edtTipoPessoaValor = edtTipoPessoaValor[i].value;
	       break;
	     }
	  }
	  
	  //Se for por cliente
		if (edtTipoPessoaValor == 1) {	  	
			if (Form.cmbClienteId.value == 0) {
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
		if (edtTipoPessoaValor == 2) {
			if (Form.cmbFornecedorId.value == 0) {
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
		if (edtTipoPessoaValor == 3) {
			if (Form.cmbColaboradorId.value == 0) {
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
		var urlRelatorio = './relatorios/BoletoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=5&TipoPessoa=' + edtTipoPessoaValor + '&PessoaId='+ edtPessoaId  + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;
	}



//************
	//Caso for por subgrupo
	if (edtAgruparValor == 3) {
	 
	  //Recebe o valor do combo de evento
	  var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
	  var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value	

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
		var urlRelatorio = './relatorios/BoletoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=3&EventoId='+ cmbEventoIdValor   + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;		
	}
	
	
	
	//Caso for por subgrupo
	if (edtAgruparValor == 6) {
	 
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
		var urlRelatorio = './relatorios/BoletoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=6&SubgrupoId='+ cmbSubgrupoIdValor   + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;		
	}
	
	//******************************	
	
	//Caso for por agrupamento (grupo, subgrupo e categoria)
	if (edtAgruparValor == 7) {
	 
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
		var urlRelatorio = './relatorios/BoletoRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=7&GrupoId='+ cmbGrupoIdValor   + '&SubgrupoId='+ cmbSubgrupoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value + '&TipoSituacao='+ edtSituacaoValor;		
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
			    <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Boletos com pagamento em atrazo para <?php echo date("d",mktime()) . " de " . $mes_nome . " de " . date("Y",mktime()) ?></span>			  	</td>
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
			<?php
			/*EXIBE OS BOLETOS EM ATRAZO*/
				
		  $sql = "SELECT * FROM boleto
							WHERE data_vencimento < '$dataHoje' 
              AND boleto_recebido = 0
							ORDER BY sacado";
              
					   
		  //Executa a Query
		  $query = mysql_query($sql);
	  	  
		  //verifica o número total de registros
		  $tot_regs = mysql_num_rows($query); 	    
		  
		  //Gera a variável com o total
			$total_receber = 0;
		  ?>
	   
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">

	<?php
	  //Caso houverem registros
	  if ($tot_regs > 0) { 
	  echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          <td width='116' align='center'>Nosso Número</td>
	      	<td>Dados do Sacado/Evento/Formando</td>
          <td width='60' align='center'>Emissão</td>
          <td width='60' align='center'>Vencto</td>
          <td width='80' align='center'>Valor</td>
          <td width='65' align='center'>Situação</td>
          <td colspan='2' width='90' align='center' style='padding-right: 0px'>Ação</td>          
        </tr>
	  ";}
	  
	  //Caso não houverem registros
	  if ($tot_regs == 0) { 

	  //Exibe uma linha dizendo que nao há registros
	  echo "
	  	<tr height='24'>
        <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
		  <slot><font color='#33485C'><strong>Não há boletos com pagamento em atrazo</strong></font></slot>
			</td>
	  </tr>	
	  ";	  
	  } 	  
	  //Cria o array e o percorre para montar a listagem dinamicamente
	  while ($dados_rec = mysql_fetch_array($query)){					
  		  
      $desc_situacao = "<span style='color: #990000'>Em Atrazo</span>";

		//Fecha o php, mas o while continua
	  ?>

	  <tr height="16">
			<td style="border-bottom: 1px solid" align="center">
				<span style="color: #6666CC"><?php echo substr($dados_rec["nosso_numero"], 0,7) ?></span><span style="color: #990000"><?php echo substr($dados_rec["nosso_numero"], 7,3) ?></span><span style="color: #59AA08"><?php echo substr($dados_rec["nosso_numero"], 10,5) ?></span><?php echo substr($dados_rec["nosso_numero"], 15,2) ?>				
			</td>      			
      <td style="border-bottom: 1px solid" height="20">
        <font color="#CC3300" size="2" face="Tahoma">
				  <a title="Clique para exibir este boleto" href="#" onclick="abreJanelaBoleto('./boletos/boleto_bb.php?TipoBol=1&BoletoId=<?php echo $dados_rec[id] ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')"><?php echo $dados_rec["sacado"]; ?></a>
				</font>
				<br/>
				<?php echo "<span style='color: #990000'><b>$dados_rec[demonstrativo2]</b></span><br/>$dados_rec[demonstrativo3]" ?>
				</span>      
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo DataMySQLRetornar($dados_rec["data_documento"]) ?>				
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<span style="color: #6666CC"><?php echo DataMySQLRetornar($dados_rec["data_vencimento"]) ?></span>			
			</td>			
      <td style="border-bottom: 1px solid" align="right">
        <?php 
					echo "R$ " . number_format($dados_rec["valor_boleto"], 2, ",", ".");
					$total_receber = $total_receber + $dados_rec["valor_boleto"]; 
				?>
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo $desc_situacao ?>				
			</td>
      <td width="32" style="border-bottom: 1px solid">
        <?php

					if ($dados_rec["boleto_recebido"] == 0) {
				?>
        	<img src="image/bt_receber_gd.gif" title="Baixar o recebimento deste boleto" onclick="wdCarregarFormulario('BoletoQuita.php?BoletoId=<?php echo $dados_rec[id] ?>&headers=1','conteudo')" style="cursor: pointer" />					
				<?php
					} else {
					 
           echo "&nbsp;";
           
         }
				?>			
			</td>
      <td style="border-bottom: 1px solid">
        <input class="button" title="Exclui este Boleto e sua conta a receber vinculada" onclick="if(confirm('Confirma a exclusão deste boleto e sua conta a receber vinculada ?')) {wdCarregarFormulario('BoletoExclui.php?BoletoId=<?php echo $dados_rec[id] ?>&ContaReceberId=<?php echo $dados_rec[conta_receber_id] ?>','conteudo')}" type="button" value="Excluir" name="Delete" />			
			</td>			
	  </tr>

  	<?php
  	//Fecha o WHILE
  	};
  
  	//Verifica se precisa imprimir o rodapé
  	if ($tot_regs > 0) { 
  	?>

      	<tr height="16">
          <td colspan="4" height="20" align="right"><strong>Total de boletos em atrazo:&nbsp;&nbsp;</strong></td>
          <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="right">
            <span style="color: #990000"><?php echo "R$ " . number_format($total_receber, 2, ",", ".") ?></span>
      		</td>
      		<td colspan="3">
            &nbsp;
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

<br/>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
		    <tr>
		      <td width="100%">
			      <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Boletos com vencimento em <?php echo date("d",mktime()) . " de " . $mes_nome . " de " . date("Y",mktime()) ?></span>			  	
          </td>
		    </tr>
		  </table>
    </td>
  </tr>
 </table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<?php
			/*EXIBE OS BOLETOS VENCENDO NO DIA*/
				
		  $sql = "SELECT * FROM boleto
							WHERE data_vencimento = '$dataHoje'
							ORDER BY sacado";
              
					   
		  //Executa a Query
		  $query = mysql_query($sql);
	  	  
		  //verifica o número total de registros
		  $tot_regs = mysql_num_rows($query); 	    
		  
		  //Gera a variável com o total
			$total_receber = 0;
		  ?>
	   
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">

	<?php
	  //Caso houverem registros
	  if ($tot_regs > 0) { 
	  echo "
        <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          <td width='116' align='center'>Nosso Número</td>
	      	<td>Dados do Sacado/Evento/Formando</td>
          <td width='60' align='center'>Emissão</td>
          <td width='60' align='center'>Vencto</td>
          <td width='80' align='center'>Valor</td>
          <td width='65' align='center'>Situação</td>
          <td colspan='2' width='90' align='center' style='padding-right: 0px'>Ação</td>          
        </tr>
	  ";}
	  
	  //Caso não houverem registros
	  if ($tot_regs == 0) { 

	  //Exibe uma linha dizendo que nao há registros
	  echo "
	  	<tr height='24'>
        <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' nowrap='nowrap'>
		  <slot><font color='#33485C'><strong>Não há boletos com vencimento para hoje</strong></font></slot>
			</td>
	  </tr>	
	  ";	  
	  } 	  
	  //Cria o array e o percorre para montar a listagem dinamicamente
	  while ($dados_rec = mysql_fetch_array($query)){	
		
  		//Verifica a situação do boleto
  		switch ($dados_rec["boleto_recebido"]) {
  		  
        case 0: $desc_situacao = "<span style='color: #990000'>Em Aberto</span>"; break;		  
        case 1: $desc_situacao = "<span style='color: #6666CC'>Recebido</span>"; break;
        
      }

		//Fecha o php, mas o while continua
	  ?>

	  <tr height="16">
			<td style="border-bottom: 1px solid" align="center">
				<span style="color: #6666CC"><?php echo substr($dados_rec["nosso_numero"], 0,7) ?></span><span style="color: #990000"><?php echo substr($dados_rec["nosso_numero"], 7,3) ?></span><span style="color: #59AA08"><?php echo substr($dados_rec["nosso_numero"], 10,5) ?></span><?php echo substr($dados_rec["nosso_numero"], 15,2) ?>			
			</td>      			
      <td style="border-bottom: 1px solid" height="20">
        <font color="#CC3300" size="2" face="Tahoma">
				  <a title="Clique para exibir este boleto e a conta a receber vinculada" href="#" onclick="abreJanelaBoleto('./boletos/boleto_bb.php?TipoBol=1&BoletoId=<?php echo $dados_rec[id] ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')"><?php echo $dados_rec["sacado"]; ?></a>
				</font>
				<br/>
				<?php echo "<span style='color: #990000'><b>$dados_rec[demonstrativo2]</b></span><br/>$dados_rec[demonstrativo3]" ?>
				</span>      
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo DataMySQLRetornar($dados_rec["data_documento"]) ?>				
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo DataMySQLRetornar($dados_rec["data_vencimento"]) ?>				
			</td>			
      <td style="border-bottom: 1px solid" align="right">
        <?php 
					echo "R$ " . number_format($dados_rec["valor_boleto"], 2, ",", ".");
					$total_receber = $total_receber + $dados_rec["valor_boleto"]; 
				?>
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo $desc_situacao ?>				
			</td>
      <td style="border-bottom: 1px solid">
        <?php

					if ($dados_rec["boleto_recebido"] == 0) {
				?>
        	<img src="image/bt_receber_gd.gif" title="Baixar o recebimento deste boleto" onclick="wdCarregarFormulario('BoletoQuita.php?BoletoId=<?php echo $dados_rec[id] ?>&headers=1','conteudo')" style="cursor: pointer" />					
				<?php
					} else {
					 
           echo "&nbsp;";
           
         }
				?>			
			</td>
      <td style="border-bottom: 1px solid">
        <input class="button" title="Exclui este Boleto e sua conta a receber vinculada" onclick="if(confirm('Confirma a exclusão deste boleto e sua conta a receber vinculada ?')) {wdCarregarFormulario('BoletoExclui.php?BoletoId=<?php echo $dados_rec[id] ?>&ContaReceberId=<?php echo $dados_rec[conta_receber_id] ?>','conteudo')}" type="button" value="Excluir" name="Delete" />			
			</td>			
	  </tr>
	<?php
	//Fecha o WHILE
	};

	//Verifica se precisa imprimir o rodapé
	if ($tot_regs > 0) { 
	?>

	<tr height="16">
    <td colspan="5" height="20" align="right"><strong>Total de boletos com vencimento para hoje:&nbsp;&nbsp;</strong></td>
    <td height="20" valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" align="right">
      <span style="color: #990000"><?php echo "R$ " . number_format($total_receber, 2, ",", ".") ?></span>
		</td>
		<td colspan="3">
      &nbsp;
		</td>					
	</tr>	
	
	<?php
	//Fecha o IF
	};
	?>
		
	</table>	
	</td>
  </tr> 

  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440">
						<br/>
						<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relatórios dos Boletos</span>
						<br/>
						Selecione um dos relatórios abaixo:
						<br/>
						<br/>
				    <table width="100%" cellpadding="0" cellspacing="0">
		          <tr valign="middle" style="padding: 1px">
							  <td height="20">
		              <input name="edtAgrupar" type="radio" value="1" checked="checked" onclick="wdCarregarFiltragem()"/> Boletos por Data de Vencimento
		            </td>
		          </tr>
		          <tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="3" onclick="wdCarregarFiltragem()"/> Boletos por Evento
								</td>
		          </tr>
							<tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="5" onclick="wdCarregarFiltragem()"/> Boletos por Sacado
								</td>
		          </tr>
		          <tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="6" onclick="wdCarregarFiltragem()"/> Boletos por Evento e Formando
								</td>
		          </tr>
							<tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="2" onclick="wdCarregarFiltragem()"/> Boletos por Centro de Custo
								</td>
		          </tr>		          		          
							<tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="7" onclick="wdCarregarFiltragem()"/> Boletos por Conta-caixa e Centro de Custo
								</td>
		          </tr>
		          <tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="4" onclick="wdCarregarFiltragem()"/> Boletos por Situação
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
					    include "BoletoRelatorioData.php";
					  ?>
					</div>
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
