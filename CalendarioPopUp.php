<?php
/*
Construdor: Robson Romeu Rizzieri
Data: 23 de agosto de 2006
Alteração: Maycon Edinger
Data: 27 de Agosto de 2006
Descrição: Cria um componente com um calendario para a seleção de uma data
*/

class tipData {
  //define o tamanho da tela do calendario
  public $intTamanhoCalendario;
  //Permite exibir ou não a data
  public $bolExibirHora;
  //Valor que define o conteúdo do campo em caso de alteração
  //Implementado por Maycon
  public $strValor;
  
  public $strRequerido;


  //Metodo construtor da classe
  function tipData(){
		$this->strFormulario = "";
		$this->strRequerido = false;
		$this->strNome = "";  
		$this->strValor = "";
		$this->intTamanho = 10;
		$this->intMaximoCaracter = 10;
		$this->intTamanhoCalendario = 150;
		$this->bolExibirHora = true;
		$this->strOnblur = '';
		$this->strOnfocus = '';
  }
	
	public function MontarJavaScript(){         
?>
    <script language='Javascript'>
  
		function verifica_data(objCampo) { 
		  dia = (objCampo.value.substring(0,2)); 
		  mes = (objCampo.value.substring(3,5)); 
		  ano = (objCampo.value.substring(6,10)); 
		
		  situacao = ""; 
		  // verifica o dia valido para cada mes 
		  if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) { 
		    situacao = "falsa"; 
		  } 
		
		  // verifica se o mes e valido 
		  if (mes < 01 || mes > 12 ) { 
		    situacao = "falsa"; 
		  } 
		
		  // verifica se e ano bissexto 
		  if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) { 
		    situacao = "falsa"; 
		  } 
		    
		  if (objCampo.value == "") { 
		    situacao = "true"; 
		  } 
		    
		  if (situacao == "falsa") { 
		    alert("A data informada ("+ objCampo.value +") é inválida !"); 
		    objCampo.focus(); 
		    return false;
		  } else {
			return true; 
		  }	 
		}

		function popdate(obj,div,tam,ddd)
		{
		    if (ddd) 
		    {
		        day = ""
		        mmonth = ""
		        ano = ""
		        c = 1
		        char = ""
		        for (s=0;s<parseInt(ddd.length);s++)
		        {
		            char = ddd.substr(s,1)
		            if (char == "/") 
		            {
		                c++; 
		                s++; 
		                char = ddd.substr(s,1);
		            }
		            if (c==1) day    += char
		            if (c==2) mmonth += char
		            if (c==3) ano    += char
		        }
		        ddd = mmonth + "/" + day + "/" + ano
		    }
		  
		    if(!ddd) {
				  today = new Date()
				} else {
				  today = new Date(ddd)
				}
		    date_Form = eval (obj)
		    
				if (date_Form.value == "") { 
				  date_Form = new Date()
				} else {
				  date_Form = new Date(date_Form.value)
				}
		  
		    ano = today.getFullYear();
		    mmonth = today.getMonth ();
		    day = today.toString ().substr (8,2) 
		    umonth = new Array ("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro")
		    days_Feb = (!(ano % 4) ? 29 : 28)
		    days = new Array (31, days_Feb, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31)
		
		    if ((mmonth < 0) || (mmonth > 11))  
				  alert(mmonth)
		    if ((mmonth - 1) == -1) {
				  month_prior = 11; 
					year_prior = ano - 1;
				} else {
				  month_prior = mmonth - 1; 
					year_prior = ano;
				}
		    if ((mmonth + 1) == 12) {
				  month_next  = 0;  
					year_next  = ano + 1;
				} else {
				  month_next  = mmonth + 1; 
					year_next  = ano;
				}
		    txt  = "<table class='listView' cellspacing='0' cellpadding='0' border='0' width='"+tam+"' height='16'>"
		    
			txt += "<tr class='Cabecalho_Calendario' ><td colspan='7' align='center'><table border='0' cellpadding='0' width='100%' class='Cabecalho_Calendario'><tr>"
		    
			txt += "<td width=100% align=left height='18'>&nbsp;<a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+((mmonth+1).toString() +"/01/"+(ano-1).toString())+"') class='Cabecalho_Calendario'><img src='image/bt_primeiro.gif' alt='Exibe o calendário do Ano Anterior' border='0' align='middle'/></a>&nbsp;&nbsp;"
		    
			txt += "<a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+( "01/" + (month_prior+1).toString() + "/" + year_prior.toString())+"') class='Cabecalho_Calendario'><img src='image/bt_anterior.gif' alt='Exibe o calendário do M&ecirc;s Anterior' border='0' align='middle'/></a>&nbsp;&nbsp;"
		    
			txt += "<a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+( "01/" + (month_next+1).toString()  + "/" + year_next.toString())+"') class='Cabecalho_Calendario'><img src='image/bt_proximo.gif' alt='Exibe o calendário para o Próximo Mês' border='0' align='middle'/></a>&nbsp;&nbsp;"
		    
			txt += "<a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+((mmonth+1).toString() +"/01/"+(ano+1).toString())+"') class='Cabecalho_Calendario'><img src='image/bt_ultimo.gif' alt='Exibe o calendário para o Próximo Ano' border='0' align='middle'/></a></td>"
		    
			txt += "<td width=20% align='center'><a href=javascript:force_close('"+div+"') class='Cabecalho_Calendario'><img src='image/bt_fechar.gif' alt='Fecha a Janela do Calendário' border='0' align='middle'/></a></td></tr></table></td></tr>"
		    
			txt += "<tr><td colspan='7' align='center' class='mes' background='image/fundo_tabela.gif' height='18'><a href='#' class='mes'><?php 
				if ($this->bolExibirHora){
				  echo date('H:i', mktime())." de ";
				}	 				
				?></a>"
		    
			txt += " <a href=javascript:pop_month('"+obj+"','"+div+"','"+tam+"','" + ano + "') class='mes' title='Clique para selecionar o Mês'><font size='2' face='Tahoma'>" + umonth[mmonth] + " de " +"</font></a><a href=javascript:pop_year('"+obj+"','"+div+"','"+tam+"','" + (mmonth+1) + "') class='mes' title='Clique para selecionar o Ano'><font size='2' face='Tahoma'>" + ano.toString() + "</font></a> <div id='popd' style='position:absolute'></div></td></tr>"
		    
			txt += "<tr ><td width='22' class='dia' align=center height='16' style='border-left-style:none'><font color=\"#990000\">D</font></td><td width='22' class='dia' align=center style='border-left-style:none'>S</td><td width='22' class='dia' align=center style='border-left-style:none'>T</td><td width='22' class='dia' align=center style='border-left-style:none'>Q</td><td width='22' class='dia' align=center style='border-left-style:none'>Q</td><td width='22' class='dia' align=center style='border-left-style:none'>S</td><td width='22' class='dia' align=center style='border-left-style:none; border-right-style:none;'>S</td></tr>"
		    today1 = new Date((mmonth+1).toString() +"/01/"+ano.toString());
		    diainicio = today1.getDay () + 1;
		    week = d = 1
		    start = false;
		
		    for (n=1;n<= 42;n++) 
		    {
		        if (week == 1)  txt += "<tr align=center>"
		        if (week==diainicio) {start = true}
		        if (d > days[mmonth]) {start=false}
		        if (start) 
		        {
						/*Adiciona Zero para dias menores que 10*/	
					    if (d <=9){
							  dd = "0"+d;
							}else{
							  dd = d;
							}
							/*Adiciona zero para meses menores que 10*/
							if ((mmonth+1).toString() <= 9){
					      mm = "0"+(mmonth+1).toString(); 		 
							}else{
  					    mm = (mmonth+1).toString(); 		 
	  					}
	  					if (date_Form.value == ""){
			          dat = new Date(mm + "/" + dd + "/" + ano.toString());
			          day_dat   = dat.toString().substr(0,10);
			          day_today  = date_Form.toString().substr(0,10);
			          year_dat  = dat.getFullYear();
			          year_today = date_Form.getFullYear();
			          if  ((day_dat == day_today) && (year_dat == year_today)){
			            txt += "<td class='data_hoje' align=center height='16' style='border-left-style:none; border-top-style:none;'><a href=javascript:block('"+  dd + "/" + mm + "/" + ano.toString() +"','"+ obj +"','" + div +"')>"+ d.toString() + "</a></td>"		  
			    		  }else { 
			            txt += "<td class='data' align=center height='16' style='border-left-style:none; border-top-style:none;'><a href=javascript:block('"+  dd + "/" + mm + "/" + ano.toString() +"','"+ obj +"','" + div +"')>"+d.toString() + "</a></td>"
			    		  }	
		            d ++ 			          
			        }else{
                dat = new Date();
			          if  ((dat.getDate() == dd) && (dat.getMonth()+1 == mm)){
			            txt += "<td class='data_hoje' align=center height='16' style='border-left-style:none; border-top-style:none;'><a href=javascript:block('"+  dd + "/" + mm + "/" + ano.toString() +"','"+ obj +"','" + div +"')>"+ d.toString() + "</a></td>"		  
			    		  }else { 
			            txt += "<td class='data' align=center height='16' style='border-left-style:none; border-top-style:none;'><a href=javascript:block('"+  dd + "/" + mm + "/" + ano.toString() +"','"+ obj +"','" + div +"')>"+d.toString() + "</a></td>"
			    		  }	
		            d ++ 

							}	  
		        } else { 
	               if (n <= 35){
				      txt += "<td class='data' align=center height='16' style='border-left-style:none; border-top-style:none'>&nbsp;</td>";
				   }  
		        }
		        week ++
		        if (week == 8) 
		        { 
	            week = 1; txt += "</tr>"} 
		        }
		        txt += "</table>"
		        div2 = eval (div)
		        div2.innerHTML = txt 
		}
				  
		// função para exibir a janela com os meses
		function pop_month(obj, div, tam, ano)
		{
		  txt  = "<table width='130' class='listView' border='0' cellpadding='0' cellspacing='0'><tr><td valign='middle' height='20' background='image/fundo_consulta.gif'>&nbsp;&nbsp;&nbsp;Selecione o Mês&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=javascript:fechar() class='Cabecalho_Calendario'><img src='image/bt_fechar.gif' alt='Fecha a Janela de seleção de meses' border='0' align='top'/></a></td></tr>"
		  for (n = 0; n < 12; n++) { txt += "<tr><td height='18' align=center bgcolor='#EEEEEE'><a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+("01/" + (n+1).toString() + "/" + ano.toString())+"')>" + umonth[n] +"</a></td></tr>" }
		  txt += "</table>"
		  popd.innerHTML = txt
		}
		
		//Função para fechar as janelas secundárias
		function fechar() {
		  popd.innerHTML = "";
		}  
		
		// função para exibir a janela com os anos
		function pop_year(obj, div, tam, umonth)
		{
		  txt  = "<table width='150' class='listView' border='0' cellpadding='0' cellspacing='0'><tr><td colspan='3' height='18' background='image/fundo_consulta.gif'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Selecione o Ano&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=javascript:fechar() class='Cabecalho_Calendario'><img src='image/bt_fechar.gif' alt='Fecha a Janela de seleção de anos' border='0' align='top'/></a></td></tr> "
		  l = 1
		  for (n=1991; n<2012; n++)
		  {  if (l == 1) txt += "<tr>"
		     txt += "<td height='18' align=center bgcolor='#EEEEEE'><a href=javascript:popdate('"+obj+"','"+div+"','"+tam+"','"+(umonth.toString () +"/01/" + n) +"')>" + n + "</a></td>"
		     l++
		     if (l == 4) 
		        {txt += "</tr>"; l = 1 } 
		  }
		  txt += "</tr></table>"
		  popd.innerHTML = txt 
		}		
		// função para fechar o calendário
		function force_close(div) 
		    { div2 = eval (div); div2.innerHTML = ''}
		    
		// função para fechar o calendário e setar a data no campo de data associado
		function block(data, obj, div)
		{ 
		    force_close (div)
		    obj2 = eval(obj)
		    obj2.value = data 
		}		
		</script>

<?php	  
	}	  
	//Cria o componente com o botão de acesso ao calendario
	public function CriarData(){
		
		//Verifica se deve aplicar a classe de campo requerido
		if ($this->strRequerido == true){
			
			//Caso for verdadeiro, então aplica a classe de campo requerido
			$campo_classe = "requerido";
		
		//Caso contrário
		} else {
			
			//Aplica a classe de campo normal
			$campo_classe = "datafield";
		}
		
	  //Define o formulario e campo que deve receber a data
	  $strComponente = "document.".$this->strFormulario.".".$this->strNome;
	  //Define o formulário o campo que deve receber a data e propriedade do mesmo
	  $strComponenteValor = "document.".$this->strFormulario.".".$this->strNome."value";
?>    
		<input name="<?php echo $this->strNome?>" class="<?php echo $campo_classe ?>" style="width: 60px" MAXLENGTH="<?php echo $this->intMaximoCaracter?>" onfocus="<?php echo $this->strOnfocus ?>" onblur="<?php echo $this->strOnblur ?>" onkeypress="return FormataCampo(document.<?php echo $this->strFormulario?>, '<?php echo $this->strNome?>', '99/99/9999', event);" onblur="verifica_data(document.<?php echo $this->strFormulario?>.<?php echo $this->strNome?>);" value="<?php echo $this->strValor?>"> 
    <img src="image/bt_calendario.gif" alt="Clique para selecionar a data a partir do calend&aacute;rio" align="middle" style="cursor: hand" onclick="javascript:popdate('<?php echo $strComponente?>','pop<?php echo $this->strNome?>','<?php echo $this->intTamanhoCalendario?>',<?php echo $strComponenteValor?>)">
<!-- na span abaixo aparece o segundo calendario -->
    <span id="pop<?php echo $this->strNome?>" style="position:absolute "></span>

<?php	  
	}  
}  

?>