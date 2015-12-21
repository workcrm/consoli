<?php
###########
## Módulo para Pedidos do Foto e Vídeo
## Criado: 01/09/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########


//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET[Headers] == 1)
{
  header('Content-Type: text/html;  charset=ISO-8859-1',true);
}

//Desativar o CSS redundante
//<link rel='stylesheet' type='text/css' href='include/workStyle.css'>

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="javascript">

function wdCarregarPedidos(id){
     
  var id;

	if (id == 1) {
		wdCarregarFormulario('PedidoFVRelatorioData.php?headers=1','filtragem','1');
	}
	
	if (id == 2) {
		wdCarregarFormulario('PedidoFVRelatorioEvento.php?headers=1','filtragem','1');
	}

	if (id == 3) {
		wdCarregarFormulario('PedidoFVRelatorioEventoFormando.php?headers=1','filtragem','1');
	}		
	
	if (id == 4) {
		wdCarregarFormulario('PedidoFVRelatorioEventoFornecedor.php?headers=1','filtragem','1');
	}
	
	if (id == 5) {
		wdCarregarFormulario('PedidoFVRelatorioFornecedor.php?headers=1','filtragem','1');
	}
  
  if (id == 6) {
		wdCarregarFormulario('PedidoFVRelatorioDataEntrega.php?headers=1','filtragem','1');
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
  
	//Caso for para exibir por data de emissão
	if (edtAgruparValor == 1) 
  {
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
		var urlCarrega = 'PedidoFVLista.php?TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

		//Acessa a listagem das contas
		wdCarregarFormulario(urlCarrega,'conteudo');		
	}



	//Caso for por evento
	if (edtAgruparValor == 2) 
  {
	 
	 
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
	  var urlCarrega = 'PedidoFVLista.php?TipoListagem=2&EventoId='+ cmbEventoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

  	  //Acessa a listagem das contas
  	  wdCarregarFormulario(urlCarrega,'conteudo');		
  	}
    
    	
    //Caso for por evento e formando
  	if (edtAgruparValor == 3) 
    {
     
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
  		
  		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) {
  				
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
  	  var urlCarrega = 'PedidoFVLista.php?TipoListagem=3&EventoId='+ cmbEventoIdValor + '&FormandoId='+ cmbFormandoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;
  
  	  //Acessa a listagem das contas
  	  wdCarregarFormulario(urlCarrega,'conteudo');		
  	} 


    //Caso for por evento, formando e fornecedor
  	if (edtAgruparValor == 4) 
    {
     
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
      
      //Caso não especificou fornecedor
      if (Form.cmbFornecedorId.value == 0) 
      {
        alert("É necessário selecionar um fornecedor !");      
        return false;
      }
        
     
  	  //Recebe o valor do combo de evento
  	  var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
  	  var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
      
      //Recebe o valor do combo de formando
  	  var cmbFormandoIdIndice = Form.cmbFormandoId.selectedIndex;
  	  var cmbFormandoIdValor = Form.cmbFormandoId.value
      
      //Recebe o valor do combo de fornecedor
  	  var cmbFornecedorIdIndice = Form.cmbFornecedorId.selectedIndex;
  	  var cmbFornecedorIdValor = Form.cmbFornecedorId.value
      
      	
  
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
  		
  		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) {
  				
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
  	  var urlCarrega = 'PedidoFVLista.php?TipoListagem=4&EventoId='+ cmbEventoIdValor + '&FormandoId='+ cmbFormandoIdValor + '&FornecedorId='+ cmbFornecedorIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;
  
  	  //Acessa a listagem das contas
  	  wdCarregarFormulario(urlCarrega,'conteudo');		
  }   
  
	
	//Caso for por fornecedor
	if (edtAgruparValor == 5) 
  {
	 
	 
   //Caso não especificou um evento
    if (Form.cmbFornecedorId.value == 0) 
    {
      
      alert("É necessário selecionar um fornecedor !");      
      return false;
    
    } 
	 
	  //Recebe o valor do combo de fornecedor
	  var cmbFornecedorIdIndice = Form.cmbFornecedorId.selectedIndex;
	  var cmbFornecedorIdValor = Form.cmbFornecedorId.options[cmbFornecedorIdIndice].value	

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

	  //Monta a url a acessar	 
	  var urlCarrega = 'PedidoFVLista.php?TipoListagem=5&FornecedorId='+ cmbFornecedorIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

  	  //Acessa a listagem das contas
  	  wdCarregarFormulario(urlCarrega,'conteudo');		
  	}  
  
  //Caso for para exibir por data a receber
	if (edtAgruparValor == 6) 
  {
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
		var urlCarrega = 'PedidoFVLista.php?TipoListagem=6&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;

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
   
	for (var i=0; i < edtAgruparValor.length; i++) 
  {
    if (edtAgruparValor[i].checked == true) 
    {
      edtAgruparValor = edtAgruparValor[i].value;
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
		var urlRelatorio = './relatorios/PedidoFVRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=1&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;
	}


	//Caso for por evento
	if (edtAgruparValor == 2) 
  {
	 
	 
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
		var urlRelatorio = './relatorios/PedidoFVRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=2&EventoId='+ cmbEventoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;		
  	
    }
    
    	
    //Caso for por evento e formando
  	if (edtAgruparValor == 3) 
    {
     
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
  		
  		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) {
  				
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
		  var urlRelatorio = './relatorios/PedidoFVRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=3&EventoId='+ cmbEventoIdValor + '&FormandoId='+ cmbFormandoIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;		
  			
  	} 


    //Caso for por evento, formando e fornecedor
  	if (edtAgruparValor == 4) 
    {
     
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
      
      //Caso não especificou fornecedor
      if (Form.cmbFornecedorId.value == 0) 
      {
        alert("É necessário selecionar um fornecedor !");      
        return false;
      }
        
     
  	  //Recebe o valor do combo de evento
  	  var cmbEventoIdIndice = Form.cmbEventoId.selectedIndex;
  	  var cmbEventoIdValor = Form.cmbEventoId.options[cmbEventoIdIndice].value
      
      //Recebe o valor do combo de formando
  	  var cmbFormandoIdIndice = Form.cmbFormandoId.selectedIndex;
  	  var cmbFormandoIdValor = Form.cmbFormandoId.value
      
      //Recebe o valor do combo de fornecedor
  	  var cmbFornecedorIdIndice = Form.cmbFornecedorId.selectedIndex;
  	  var cmbFornecedorIdValor = Form.cmbFornecedorId.value
      
      	
  
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
  		
  		if (Form.edtDataIni.value != 0 && Form.edtDataFim.value != 0) {
  				
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
		  var urlRelatorio = './relatorios/PedidoFVRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=4&EventoId='+ cmbEventoIdValor + '&FormandoId='+ cmbFormandoIdValor + '&FornecedorId='+ cmbFornecedorIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;		
  				
  }   
  
	
	//Caso for por fornecedor
	if (edtAgruparValor == 5) 
  {
	 
	 
   //Caso não especificou um evento
    if (Form.cmbFornecedorId.value == 0) 
    {
      
      alert("É necessário selecionar um fornecedor !");      
      return false;
    
    } 
	 
	  //Recebe o valor do combo de fornecedor
	  var cmbFornecedorIdIndice = Form.cmbFornecedorId.selectedIndex;
	  var cmbFornecedorIdValor = Form.cmbFornecedorId.options[cmbFornecedorIdIndice].value	

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

  	//Monta a url do relatório		
		var urlRelatorio = './relatorios/PedidoFVRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=5&FornecedorId='+ cmbFornecedorIdValor + '&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;		
  				
  	}  
  
  //Caso for para exibir por data a receber
	if (edtAgruparValor == 6) 
  {
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
		var urlRelatorio = './relatorios/PedidoFVRelatorioPDF.php?EmpresaId=<?php echo $empresaId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaNome=<?php echo $empresaNome ?>&TipoListagem=6&DataIni='+ Form.edtDataIni.value + '&DataFim=' + Form.edtDataFim.value;		
  			
	} 

//Executa o relatório
abreJanela(urlRelatorio);

}

</script>

<form id="form" name="cadastro" method="post">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
		    <tr>
		      <td width="750">
			    <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Consulta Pedidos do Foto e Vídeo</span></td>
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
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440">					
						<br/>
						Selecione uma das consultas abaixo:
						<br/>
						<br/>
				    <table width="100%" cellpadding="0" cellspacing="0">
		          <tr valign="middle" style="padding: 1px">
							  <td height="20">
		              <input name="edtAgrupar" type="radio" value="1" checked="checked" onclick="wdCarregarPedidos(1)"/> Pedidos por Data de Emissão
		            </td>
		          </tr>
		          <tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="2" onclick="wdCarregarPedidos(2)"/> Pedidos por Evento
								</td>
		          </tr>
              <tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="3" onclick="wdCarregarPedidos(3)"/> Pedidos por Evento e Formando
								</td>
		          </tr>
              <tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="4" onclick="wdCarregarPedidos(4)"/> Pedidos por Evento, Formando e Fornecedor
								</td>
		          </tr>
							<tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="5" onclick="wdCarregarPedidos(5)"/> Pedidos por Fornecedor
								</td>
		          </tr>
		          <?php
              /*
              <tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input type="radio" name="edtAgrupar" value="6" onclick="wdCarregarPedidos(6)"/> Pedidos por Data a Receber
								</td>
		          </tr>
              */
              ?>									          
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
					    include "PedidoFVRelatorioData.php";
					  ?>
					</div>
          </form>
				</td>
			</tr>	    
      
      <tr>
		    <td>
		      <br/>
						<input class="button" title="Visualiza os pedidos com as opções informadas" name="btnVisualizar" type="button" id="btnVisualizar" value="Visualizar na Tela" style="width:100px" onclick="wdVisualizarRelatorio()">
						<input class="button" title="Emite o relatório dos pedidos com as opções informadas" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()">
		      	<br />
		      	<br />	   	   		   		
		 		</td>   
	    </tr>  
</table>
</form>