<?php 
###########
## Módulo para cadastramento de compromissos
## Criado: 16/04/2007 - Maycon Edinger
## Alterado: 13/06/2007 - Maycon Edinger
## Alterações: 
## 13/06/2007 - Implementado rotina para poder cadstrar para qualquer usuário o compromisso
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
//header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Monta o lookup da tabela de usuários
//Monta o SQL
$lista_usuario = "SELECT * FROM usuarios WHERE empresa_id = $empresaId AND ativo = 1 AND usuario_id <> $usuarioId ORDER BY nome";
//Executa a query
$dados_usuario = mysql_query($lista_usuario);	

?>

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdExibir() {

	 //Captura o valor referente ao radio button selecionado
   var edtAgendarValor = document.getElementsByName('edtAgendar');
   
	 for (var i=0; i < edtAgendarValor.length; i++) {
     if (edtAgendarValor[i].checked == true) {
       edtAgendarValor = edtAgendarValor[i].value;
       break;
     }
   }

	if (edtAgendarValor == 2) {
		IDUsuario = document.getElementById(20);
	  IDUsuario.style.display = "inline";
	} else {
		IDUsuario = document.getElementById(20);
	  IDUsuario.style.display = "none";
	}

}

function valida_form() {
     var Form;
     Form = document.cadastro;
     
		 //Captura o valor referente ao radio button selecionado
	   var edtAgendarValor = document.getElementsByName('edtAgendar');
	   
		 for (var i=0; i < edtAgendarValor.length; i++) {
	     if (edtAgendarValor[i].checked == true) {
	       edtAgendarValor = edtAgendarValor[i].value;
	       break;
	     }
	   }
   
	 	 if (edtAgendarValor == 2) {
	 		 if (Form.cmbUsuarioVinculaId.value == 0) {
	       alert("É necessário selecionar um usuário !");
	       Form.cmbUsuarioVinculaId.focus();
	       return false;
   	 	 }
		 }
		 
     if (Form.edtHora.value.length == 0) {
        alert("É necessário informar a Hora !");
        Form.edtHora.focus();
        return false;
     }
	 
	   //Função que checa se a hora informada é válida
	 	 hrs = (Form.edtHora.value.substring(0,2)); 
     min = (Form.edtHora.value.substring(3,5)); 
               
     situacao = ""; 
     // verifica data e hora 
     if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){ 
        situacao = "falsa"; 
     }        
     if (situacao == "falsa") { 
        alert("A hora informada no campo Hora ("+ hrs + ":" + min + ") é inválida !"); 
        Form.edtHora.focus();
		 return false; 
     } 	 
	 
     if (Form.edtAssunto.value.length == 0) {
        alert("O campo de Assunto não pode ser vazio !");
        Form.edtAssunto.focus();
        return false;
     }
     if (Form.cmbCategoria.value == 0) {
        alert("É necessário selecionar uma Categoria !");
        Form.cmbCategoria.focus();
        return false;
     }
     if (Form.edtDescricao.value.length == 0) {
        alert("O campo Descrição não pode ser vazio !");
        Form.edtDescricao.focus();
        return false;
     }     

     return true;
}  
</script>

<form id="form" name="cadastro" action="sistema.php?ModuloNome=CompromissoCadastra" method="post" onsubmit="return valida_form()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440">
					  <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Agendamento de Compromissos</span>
					</td>
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
          	//Caso venha de uma postagem
            if($_POST["Submit"]){
						//Preenche as variáveis com os valores dos formulários
            $edtAgendar = $_POST["edtAgendar"];
            $cmbUsuarioVinculaId = $_POST["cmbUsuarioVinculaId"];
						$cmbDia = $_POST["cmbDia"];
            $cmbMes = $_POST["cmbMes"];
            $cmbAno = $_POST["cmbAno"];
            $data = $cmbAno . "-" . $cmbMes . "-" . $cmbDia;
            $edtHora = $_POST["edtHora"];
            $edtDuracao = $_POST["edtDuracao"];
            $edtAtividade = $_POST["edtAtividade"];
            $edtAssunto = $_POST["edtAssunto"];
            $edtPrioridade = $_POST["edtPrioridade"];
            $cmbCategoria = $_POST["cmbCategoria"];
            $edtLocal = $_POST["edtLocal"];
            $edtDescricao = $_POST["edtDescricao"];
							
							//Verifica o modo de agendamento
							if ($edtAgendar == 1) {
								//Monta o sql para inserir para si mesmo
								$sql = "INSERT INTO compromissos (
												usuario_id, 
												dia, 
												mes, 
												ano,
                        data, 
												hora, 
												duracao, 
												atividade, 
												assunto, 
												prioridade, 
												categoria, 
												local, 
												descricao
												
												) VALUES (							
												
												'$usuarioId',
												'$cmbDia',
												'$cmbMes',
												'$cmbAno',                       
                        '$data',
												'$edtHora',
												'$edtDuracao',
												'$edtAtividade',
												'$edtAssunto',
												'$edtPrioridade',
												'$cmbCategoria',
												'$edtLocal',
												'$edtDescricao'
												);";
							}	else {
								//Monta o sql para inserir para o usuario selecionado
								$sql = "INSERT INTO compromissos (
												usuario_id, 
												dia, 
												mes, 
												ano,
                        data, 
												hora, 
												duracao, 
												atividade, 
												assunto, 
												prioridade, 
												categoria, 
												local, 
												descricao
												
												) VALUES (							
												
												'$cmbUsuarioVinculaId',
												'$cmbDia',
												'$cmbMes',
												'$cmbAno',
                        '$data',
												'$edtHora',
												'$edtDuracao',
												'$edtAtividade',
												'$edtAssunto',
												'$edtPrioridade',
												'$cmbCategoria',
												'$edtLocal',
												'$edtDescricao'
												);";									
							}	
						
					//Executa a query
    	    $query = mysql_query($sql);
					
					//Exibe a mensagem de inclusão com sucesso
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Compromisso cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500)</script>";
        }
        ?>

        <table cellspacing="0" cellpadding="0" width="520" border="0">
           <tr>
	        	 <td style="PADDING-BOTTOM: 2px">
	        		 <input name="Submit" type="submit" class="button" id="Submit" title="Salva o registro atual" value="Salvar Compromisso">
            	 <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos">
             </td>
	       	 </tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do compromisso e clique em [Salvar Compromisso] </td>
			     			 </tr>
		       		 </table>             
						</td>
	       	</tr>
       
           <tr>
             <td valign="top" class="dataLabel">Agendar:</td>
             <td colspan="5" valign="middle" class="tabDetailViewDF">
               <table cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="111" height="20">
                     <input name="edtAgendar" type="radio" value="1" checked onclick="wdExibir()">
                       Para mim 
                   </td>
                   <td width="130" height="20">
                     <input type="radio" name="edtAgendar" value="2" onclick="wdExibir()">
                       Para outro usuário 
                   </td>
                   <td>
										 <select id="20" name="cmbUsuarioVinculaId" id="cmbUsuarioVinculaId" style="width:150px; display: none">
			                 <option value="0">Selecione uma Opção</option>
							 				 <?php 
												 //Monta o while para gerar o combo de escolha
												 while ($lookup_usuario = mysql_fetch_object($dados_usuario)) { 
											 ?>
			                 <option value="<?php echo $lookup_usuario->usuario_id ?>"><?php echo $lookup_usuario->nome ?> </option>
			                 <?php } ?>
			               </select>
                   </td>
                 </tr>
               </table>
             </td>
           </tr>
  
					 <tr>
             <td class="dataLabel" width="15%"><span class="dataLabel">Data:</span></td>
             <td colspan="3" class="tabDetailViewDF"><select class="dataField" name="cmbDia" id="cmbDia">
              <?php 
              
              //Efetua o for para montar o combo do dia              
              for ($i=1;$i<=31;$i++) {
							
								//Caso o dia for menor que 10
								if ($i < 10){
									
									$dd = "0" . $i;
									
								} else {
									
									$dd = $i;
									
								}
								
								//Caso o dia for igual a data atual
								if ($i == date("d", mktime())) {
									
									//Alimenta a variável com o valor selected
									$seleciona_dia = "selected";
								
								//Caso nao for	
								} else {
									
									//Alimenta a variável com o valor vazio
									$seleciona_dia = "";
								
								}
							
							//Gera o combo do dia	
							echo "<option value='$dd' $seleciona_dia>$i</option>";
              
							}
						?>
	            </select>&nbsp; de &nbsp;
	              <select class="dataField" name="cmbMes" id="cmbMes">
	              <?php
								//Efetua o for para montar o combo do dia              
	              for ($m=1;$m<=12;$m++) {
	
									//Cria o switch com a descrição do mes
								  switch ($m) {
								    case 1:  $month_name = "Janeiro";	break;
								    case 2:  $month_name = "Fevereiro";	break;
								    case 3:  $month_name = "Março";	break;
								    case 4:  $month_name = "Abril";	break;
								    case 5:  $month_name = "Maio";	break;
								    case 6:  $month_name = "Junho";	break;
								    case 7:  $month_name = "Julho";	break;
								    case 8:  $month_name = "Agosto";	break;
								    case 9:  $month_name = "Setembro";	break;
								    case 10: $month_name = "Outubro";	break;
								    case 11: $month_name = "Novembro";	break;
								    case 12: $month_name = "Dezembro";	break;
								  }
								
									//Caso o mes for menor que 10
									if ($m < 10){
										
										$mm = "0" . $m;
										
									} else {
										
										$mm = $m;
										
									}
									
	
									//Caso o dia for igual a data atual
									if ($m == date("m", mktime())) {
										
										//Alimenta a variável com o valor selected
										$seleciona_mes = "selected";
									
									//Caso nao for	
									} else {
										
										//Alimenta a variável com o valor vazio
										$seleciona_mes = "";
									
									}
								
								//Gera o combo do dia	
								echo "<option value='$mm' $seleciona_mes>$month_name</option>";
	              
								}
							?>
            		</select>&nbsp; de &nbsp;
		            <select class="datafield" name="cmbAno" id="cmbAno">
			            <?php 
			              
			              //Efetua o for para montar o combo do ano              
			              for ($a=9;$a<=20;$a++) {
											
											if ($a < 10) {
												
												$monta_ano = "200" . $a;
											
											} else {
												
												$monta_ano = "20" . $a;
												
											}
										
											//Caso o ano for igual ao ano atual
											if ($monta_ano == date("Y", mktime())) {
												
												//Alimenta a variável com o valor selected
												$seleciona_ano = "selected";
											
											//Caso nao for	
											} else {
												
												//Alimenta a variável com o valor vazio
												$seleciona_ano = "";
											
											}
										
										//Gera o combo do ano	
										echo "<option value='$monta_ano' $seleciona_ano>$monta_ano</option>";
			              
										}
									?>
		            </select>
			       </td>
           </tr>
           <tr>
             <td class="dataLabel">Hora:</td>
             <td valign="middle" class="tabDetailViewDF">
               <input name="edtHora" type="text" class="requerido" id="edtHora" size="6" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtHora', '99:99', event);"> 
               (hh:mm) 
             </td>
             <td valign="middle" class="dataLabel">Dura&ccedil;&atilde;o:</td>
             <td valign="middle" class="tabDetailViewDF">
               <input name="edtDuracao" type="text" class="datafield" id="edtDuracao" size="6" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtDuracao', '99:99', event);">
               (hh:mm)
			 			 </td>
           </tr>
           <tr>
             <td class="dataLabel">Atividade:</td>
             <td colspan="3" valign="middle" class="tabDetailViewDF">
			   				<table width="400" cellpadding="0" cellspacing="0">
               		<tr valign="middle">
                 		<td width="111" height="20">				   
				   						<input name="edtAtividade" type="radio" value="1" checked> 
				   						<img src="image/bt_reuniao.gif" alt="Reuni&atilde;o" align="middle"> Reuni&atilde;o        
                 		</td>
                 		<td width="112" height="20"><label>
				   						<input type="radio" name="edtAtividade" value="2"> 
				   						<img src="image/bt_ligacao.gif" alt="Liga&ccedil;&atilde;o" align="middle"> Telefonema</label>				 
								 		</td>
                 		<td width="175" height="20"><label>
                   		<input type="radio" name="edtAtividade" value="3"> 
                   		<img src="image/bt_compromisso.gif" alt="Compromisso" width="13" height="14" align="middle"> Compromisso</label>				 
										</td>
               		</tr>
               </table>
             </td>
           </tr>
          <tr>
            <td class="dataLabel">
		          <span class="dataLabel">Assunto:</span>
						</td>
            <td colspan="3" class="tabDetailViewDF">
		    		  <input name="edtAssunto" type="text" class="requerido" id="edtAssunto" style="width: 300" size="84" maxlength="50">
						</td>
          </tr>
          <tr>
            <td class="dataLabel">Prioridade:</td>
            <td colspan="3" class="tabDetailViewDF">
						  <table width="343" cellpadding="0" cellspacing="0">
	              <tr>
	                <td width="111" height="15" valign="middle"><label>
	                  <input name="edtPrioridade" type="radio" value="1" checked>
	                  <img src="image/bt_prior_alta.gif" alt="Alta Prioridade" width="14" height="14" align="middle"> Alta </label>                </td>
	                <td width="112" valign="middle"><label>
	                  <input type="radio" name="edtPrioridade" value="2">
	                  <img src="image/bt_prior_media.gif" alt="M&eacute;dia Prioridade" width="14" height="14" align="middle"> M&eacute;dia </label></td>
	                <td width="118" valign="middle"><label>
	                  <input type="radio" name="edtPrioridade" value="3">
	                  <img src="image/bt_prior_baixa.gif" alt="Baixa Prioridade" width="14" height="14" align="middle"> Baixa </label>                
									</td>
	              </tr>
              </table>
					  </td>
          </tr>
          <tr>
            <td class="dataLabel">
		    			Categoria: 
						</td>
            <td width="29%" class="tabDetailViewDF">
							<select name="cmbCategoria" class="datafield" id="cmbCategoria">
				  		  <option value="0" style="font-weight: bold; color:#000000">Selecione uma Categoria</option>
				  		  <option value="1" style="font-weight: bold; color:#666666">Nenhuma</option>
			    		  <option value="2" style="font-weight: bold; color:#CC3300">Importante</option>
				  		  <option value="3" style="font-weight: bold; color:#6666CC">Negócios</option>
				  		  <option value="4" style="font-weight: bold; color:#669900">Pessoal</option>
				  		  <option value="5" style="font-weight: bold; color:#999900">Folga</option>
				  		  <option value="6" style="font-weight: bold; color:#FF9900">Deve ser atendido</option>
				  		  <option value="7" style="font-weight: bold; color:#FF00FF">Aniversário</option>
				  		  <option value="8" style="font-weight: bold; color:#FF3300">Ligação Telefônica</option>
	          	</select>
						</td>
            <td width="12%" class="dataLabel">Local:</td>
            <td width="44%" valign="top" class="tabDetailViewDF">
							<input name="edtLocal" type="text" class="datafield" id="edtLocal" size="39" maxlength="35" >
						</td>
		   		</tr>
          <tr>
            <td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
            <td colspan="3" class="tabDetailViewDF">
							<textarea name="edtDescricao" wrap="virtual" class="requerido" id="edtDescricao" style="width: 100%; height: 130px"></textarea>
						</td>
          </tr>
	    </table>
    </td>
  </tr>
</table>  	 
</form>

</tr>
</table>
