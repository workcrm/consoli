/* Fun��o para criar o objeto wd_ajax usando o componente XMLHttpRequest */
wd_Ajax = function (){   
    try{   
        ajax = new XMLHttpRequest();   
    }catch(ee){   
        try{   
            ajax = new ActiveXObject("Msxml2.XMLHTTP");   
        }catch(e){   
            try{   
                ajax = new ActiveXObject("Microsoft.XMLHTTP");   
            }catch(E){   
                ajax = false;   
            }   
        }   
    }   
}         

/*FUN��O PARA PROCURAR TAGS DE SCRIPT NO AJAX E EXECUTAR SEU CONTEUDO */
function wdExtraiScript(texto){
    // inicializa o inicio ><
    var ini = 0;
    // loop enquanto achar um script
    while (ini!=-1){
        // procura uma tag de script
        ini = texto.indexOf('<script', ini);
        // se encontrar
        if (ini >=0){
            // define o inicio para depois do fechamento dessa tag
            ini = texto.indexOf('>', ini) + 1;
            // procura o final do script
            var fim = texto.indexOf('</script>', ini);
            // extrai apenas o script
            codigo = texto.substring(ini,fim);
            // executa o script
            eval(codigo);
            novo = document.createElement("script")
            novo.text = codigo;
            document.body.appendChild(novo);
        }
    }
}

function wdCarregarFormulario(CaminhoURL,Retornar,Cabecalho,Metodo,processa_js)
{
  
	var Conteudo = document.getElementById(Retornar);   
	
	/* Rotina para gerar um par�metro de n�mero rand�mico para o navegador n�o guardar a pagina ajax no cache */
	/* Cria a variavel que ser� pesquisara (nesse caso o caractere de igual =) */
	
	var limitador = /=/;
	
	/* Caso N�O ache o sinal de igual, � porque n�o foi passado nenhum parametro GET na URL pro ajax */
	if(CaminhoURL.search(limitador) == -1)
	{
	
		/* Da� cria o link ajax adicionando uma vari�vel GET UNICA e adicionando a semente rand�mica */
		var LinkAjax = CaminhoURL + "?TimeStamp=" + new Date() .getTime();
		
	}
	
	/* Caso ACHE o sinal de igual � porque tem um par�metro sendo passado via GET */
	else
		{
    /* Da� monta o link ajax adicionando MAIS UMA VARI�VEL na URL (para a semente rand�mica) */
	  var LinkAjax = CaminhoURL + "&TimeStamp=" + new Date() .getTime();
		}
  
	/* Verifica se deve usar cabe�alho na exibi��o da tela de carregando */

	if(Cabecalho == 1)
	  {
		/* Cria a mensagem que a p�gina est� sendo carregada mas sem o cabe�alho */
  	Conteudo.innerHTML = "<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_ajax_loading2.gif' border='0' /><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>A opera&ccedil;&atilde;o solicitada est&aacute; sendo processada...</strong></td></tr><tr><td>&nbsp</td></tr></table>";	  	
	  }
	else if (Cabecalho == 2)
	{
		
		Conteudo.innerHTML = "<span style='color: #990000'><b>Aguarde...</b></span>";
		
	}
	
	else
	
	{
		
		/* Cria a mensagem que a p�gina est� sendo carregada COM cabe�alho*/
		Conteudo.innerHTML = "<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr>    <td colspan='2' valign='top' class='text'><img src='image/lat_cadastro.gif' />&nbsp;<span class='TituloModulo'>Aguarde...</span></td></tr><tr><td colspan='2'><img src='image/bt_espacohoriz.gif' width='100%' height='12'></td></tr><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_ajax_loading2.gif' border='0' /><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>A opera&ccedil;&atilde;o solicitada est&aacute; sendo processada...</strong></td></tr><tr><td>&nbsp</td></tr></table>";
  	
	}

	/* Instancia o objeto AJAX */
	wd_Ajax();
	ajax.abort();   
	
	/* Verifica o m�todo selecionado */
	if(Metodo == "POST")
	{
			
		/* Abre a p�gina solicitada via ajax METODO POST*/
		ajax.open("POST",LinkAjax,true);
	
	}
	
	else
	
	{	
		
		/* Abre a p�gina solicitada via ajax METODO GET*/
		ajax.open("POST",LinkAjax,true);
		
	}
	
	/* Cria os cabe�alhos da p�gina html para instru��es de chache ao navegador */
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
	ajax.setRequestHeader("Cache-Control", "no-store, no-cache, must-revalidate");
	ajax.setRequestHeader("Cache-Control", "post-check=0, pre-check=0");
	ajax.setRequestHeader("Pragma", "no-cache");1

	ajax.onreadystatechange = function() 
	{   
		
		if( ajax.readyState == 4 )
		{   
			
			var valorRetorno = ajax.responseText;                  
			Conteudo.innerHTML = valorRetorno;
			
			if (processa_js != '1')
			{
				
				wdExtraiScript(valorRetorno); 
			}
			
        }
		
	}   
	
	ajax.send( null );
}  
