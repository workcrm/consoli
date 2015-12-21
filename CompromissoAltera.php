<?php 
###########
## Módulo para Alteração dos compromissos
## Criado: 16/04/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
?>

<script language="JavaScript">
function valida_form() {
     var Form;

     //Função que checa se a data informada é válida
	 hrs = (Form.hora.value.substring(0,2)); 
     min = (Form.hora.value.substring(3,5)); 
               
     situacao = ""; 
     // verifica data e hora 
     if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){ 
        situacao = "falsa"; 
     }        
     if (Form.hora.value == "") { 
        situacao = "falsa"; 
     } 
     if (situacao == "falsa") { 
        alert("A hora informada no campo Hora ("+ hrs + ":" + min + ") é inválida !"); 
        Form.hora.focus();
		return false; 
     } 
	 
	 if (Form.assunto.value.length == 0) {
        alert("O campo de Assunto não pode ser vazio !");
        Form.assunto.focus();
        return false;
     }
     if (Form.categoria.value.length == 0) {
        alert("É necessário selecionar uma Categoria !");
        Form.categoria.focus();
        return false;
     }
     if (Form.descricao.value.length == 0) {
        alert("O campo Descrição não pode ser vazio !");
        Form.descricao.focus();
        return false;
     }

     return true;
}

</script>

<form id="form" name="cadastro" action="sistema.php?ModuloNome=CompromissoAltera" method="post" onsubmit="return valida_form()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Compromisso</span></td>
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
      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">

          <?php
          //include "ConexaoMySQL.php";

            if($_POST["Alterar"]){

            $id = $_POST["Id"];
            $dia = $_POST["dia"];
            $mes = $_POST["mes"];
            $ano = $_POST["ano"];
						$edtHora = $_POST["edtHora"];
						$edtDuracao = $_POST["edtDuracao"];
						$atividade = $_POST["atividade"];
            $assunto = $_POST["assunto"];
						$prioridade = $_POST["prioridade"];
            $categoria = $_POST["categoria"];
            $local = $_POST["local"];
            $descricao = $_POST["descricao"];

	    	    $sql = mysql_query("
                   UPDATE compromissos SET
                   dia = '$dia', 
								   mes = '$mes', 
				  				 ano = '$ano',
								   hora = '$edtHora', 
								   duracao = '$edtDuracao',
								   atividade = '$atividade', 
								   assunto = '$assunto', 
								   prioridade = '$prioridade',
								   categoria = '$categoria',
								   local = '$local', 
								   descricao = '$descricao'
                   WHERE id = '$id' "
				   );
	
        	echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Compromisso cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500); setTimeout('wdCarregarFormulario(\'CompromissoExibe.php?CompromissoId=$id\',\'conteudo\')', 2500)</script>";
        	
        }

	    //**** RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO

        $CompromissoId = $_POST["Id"];
        $sql = "SELECT * FROM compromissos WHERE id = $CompromissoId";
        $resultado = mysql_query($sql);
        $campos = mysql_fetch_array($resultado);

        switch ($campos[atividade]) {
          case 01: $ativ_1 = "checked";	$ativ_2 = ""; 		 $ativ_3 = ""; break;
          case 02: $ativ_1 = "";		$ativ_2 = "checked"; $ativ_3 = ""; break;
          case 03: $ativ_1 = "";		$ativ_2 = ""; 		 $ativ_3 = "checked"; break;
		  }

        switch ($campos[prioridade]) {
          case 01: $prior_1 = "checked"; $prior_2 = ""; 		$prior_3 = ""; break;
          case 02: $prior_1 = ""; 		 $prior_2 = "checked";  $prior_3 = ""; break;
          case 03: $prior_1 = ""; 		 $prior_2 = ""; 		$prior_3 = "checked"; break;
		  }
		  		  
        switch ($campos[mes]) {
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

        switch ($campos[categoria]) {
          case 1:  $cat_name = "<font color=#666666><strong>   Nenhuma</strong></font>";	break;
          case 2:  $cat_name = "<font color=#CC3300><strong>   Importante</strong></font>";	break;
          case 3:  $cat_name = "<font color=#6666CC><strong>   Negócios</strong></font>";	break;
          case 4:  $cat_name = "<font color=#669900><strong>   Pessoal</strong></font>";	break;
          case 5:  $cat_name = "<font color=#999900><strong>   Folga</strong></font>";	break;
          case 6:  $cat_name = "<font color=#FF9900><strong>   Deve ser atendido</strong></font>";	break;
          case 7:  $cat_name = "<font color=#FF00FF><strong>   Aniversário</strong></font>";	break;
          case 8:  $cat_name = "<font color=#FF3300><strong>   Ligação Telefônica</strong></font>";	break;
   	   }

?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
	        	<td width="500" style="PADDING-BOTTOM: 2px">
	            <input name="Id" type="hidden" value="<?php echo $CompromissoId ?>" />
	            <input name="Alterar" type="submit" class="button" id="Alterar" title="Salva o registro atual" value="Salvar Compromisso">
	            <input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações">
            </td>
            <td align="right">
						 <input class="button" title="Retorna a exibição do compromisso" name="btnVoltar" type="button" id="btnVoltar" value="Voltar" style="width:70px" onclick="wdCarregarFormulario('CompromissoExibe.php?CompromissoId=<?php echo $CompromissoId ?>&headers=1','conteudo')" />						 
					 </td>
	      	</tr>
        </table>
           
        <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
          <tr>
            <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
              <table cellspacing=0 cellpadding="0" width="100%" border="0">
                <tr>
                  <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do compromisso e clique em [Salvar] </td>
		        		</tr>
		      		</table>            
						</td>
	      	</tr>
          <tr>
            <td class="dataLabel" width="15%"><span class="dataLabel">Data:</span></td>
            <td colspan="3" class="tabDetailViewDF">
	            <select class="dataField" name="dia" id="dia">
	              <option selected value="<?php echo $campos[dia] ?>"><?php echo $campos[dia] ?></option>
	              <option value="01">01</option>
	              <option value="02">02</option>
	              <option value="03">03</option>
	              <option value="04">04</option>
	              <option value="05">05</option>
	              <option value="06">06</option>
	              <option value="07">07</option>
	              <option value="08">08</option>
	              <option value="09">09</option>
	              <option value="10">10</option>
	              <option value="11">11</option>
	              <option value="12">12</option>
	              <option value="13">13</option>
	              <option value="14">14</option>
	              <option value="15">15</option>
	              <option value="16">16</option>
	              <option value="17">17</option>
	              <option value="18">18</option>
	              <option value="19">19</option>
	              <option value="20">20</option>
	              <option value="21">21</option>
	              <option value="22">22</option>
	              <option value="23">23</option>
	              <option value="24">24</option>
	              <option value="25">25</option>
	              <option value="26">26</option>
	              <option value="27">27</option>
	              <option value="28">28</option>
	              <option value="29">29</option>
	              <option value="30">30</option>
	              <option value="31">31</option>
	            </select>&nbsp; de &nbsp;
	              <select class="dataField" name="mes" id="mes">
	              <option selected value="<?php echo $campos[mes] ?>"><?php echo $mes_nome ?></option>
	              <option value="01">Janeiro</option>
	              <option value="02">Fevereiro</option>
	              <option value="03">Março</option>
	              <option value="04">Abril</option>
	              <option value="05">Maio</option>
	              <option value="06">Junho</option>
	              <option value="07">Julho</option>
	              <option value="08">Agosto</option>
	              <option value="09">Setembro</option>
	              <option value="10">Outubro</option>
	              <option value="11">Novembro</option>
	              <option value="12">Dezembro</option>
	            </select>&nbsp; de &nbsp;
	            <select class="datafield" name="ano" id="ano">
	              <option selected value="<?php echo $campos[ano] ?>"><?php echo $campos[ano] ?></option>
	              <option value="2006">2006</option>
	              <option value="2007">2007</option>
	              <option value="2008">2008</option>
	              <option value="2009">2009</option>
	              <option value="2010">2010</option>
	            </select>  
						</td>
          </tr>
          <tr>
            <td class="dataLabel">Hora:</td>
            <td class="tabDetailViewDF">
               <input name="edtHora" type="text" class="requerido" id="edtHora" size="6" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtHora', '99:99', event);" value="<?php echo substr($campos[hora],0,5) ?>">
              (hh:mm) 
						</td>
            <td class='dataLabel'>Dura&ccedil;&atilde;o:</td>
            <td class="tabDetailViewDF">
						  <input name="edtDuracao" type="text" class="datafield" id="edtDuracao" size="6" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtDuracao', '99:99', event);"	value="<?php echo substr($campos[duracao],0,5) ?>" >
            (hh:mm) 
						</td>
          </tr>
          <tr>
            <td class="dataLabel">Atividade:</td>
            <td colspan="3" class="tabDetailViewDF"><table width="400" cellpadding="0" cellspacing="0">
              <table width="400" cellpadding="0" cellspacing="0">
								<tr valign="middle">
	                <td width="111" height="20"><label>
	                  <input name="atividade" type="radio" value="1" <?php echo $ativ_1 ?>>
	                  <img src="image/bt_reuniao.gif" alt="Reuni&atilde;o" align="middle"> Reuni&atilde;o </label>
	                </td>
	                <td width="112" height="20"><label>
	                  <input type="radio" name="atividade" value="2" <?php echo $ativ_2 ?>>
	                  <img src="image/bt_ligacao.gif" alt="Liga&ccedil;&atilde;o" align="middle"> Liga&ccedil;&atilde;o </label>
	                </td>
	                <td width="175" height="20"><label>
	                  <input type="radio" name="atividade" value="3" <?php echo $ativ_3 ?>>
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
		     			<input name="assunto" type="text" class="requerido" id="assunto" style="width: 300" size="82" maxlength="50" value="<?php echo $campos[assunto] ?>">
						</td>
          </tr>
          <tr>
            <td class="dataLabel">Prioridade:</td>
            <td colspan="3" class="tabDetailViewDF"><table width="343" cellpadding="0" cellspacing="0">
	            <table width="400" cellpadding="0" cellspacing="0">  
								<tr>
	                <td width="111" height="15" valign="middle"><label>
	                  <input name="prioridade" type="radio" value="1" <?php echo $prior_1 ?>>
	                  <img src="image/bt_prior_alta.gif" alt="Alta Prioridade" width="14" height="14" align="middle"> Alta </label>
	                </td>
	                <td width="112" valign="middle"><label>
	                  <input type="radio" name="prioridade" value="2" <?php echo $prior_2 ?>>
	                  <img src="image/bt_prior_media.gif" alt="M&eacute;dia Prioridade" width="14" height="14" align="middle"> M&eacute;dia </label></td>
	                <td width="118" valign="middle"><label>
	                  <input type="radio" name="prioridade" value="3" <?php echo $prior_3 ?>>
	                  <img src="image/bt_prior_baixa.gif" alt="Baixa Prioridade" width="14" height="14" align="middle"> Baixa </label>
	                </td>
	              </tr>
	            </table>
					 </td>
         </tr>
         <tr>
           <td class="dataLabel">Categoria:</td>
           <td width="29%" class="tabDetailViewDF">
							<select name="categoria" class="datafield" id="categoria">
	              <option selected value="<?php echo $campos[categoria] ?>"><?php echo $cat_name ?></option>
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
						 <input name="local" type="text" class="datafield" id="local" size="39" maxlength="35" value="<?php echo $campos[local] ?>">
			 		 </td>
		   	 </tr>
         <tr>
           <td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
           <td colspan="3" class="tabDetailViewDF">
						 <textarea name="descricao" wrap="virtual" class="requerido" id="descricao" style="width: 100%; height: 130px"><?php echo $campos[descricao] ?></textarea>
					 </td>
         </tr>
	   	 </table>
     </td>
   </tr>
	</table>  	 
</form>
</tr>
</table>
