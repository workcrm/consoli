<?php 
###########
## Módulo para alteraçao de Cidades
## Criado: 18/05/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo Para alteracao de Cidades
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

if ($_GET["headers"] == 1) {
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";
?>

<script language="JavaScript">

//document.frmCidadeAltera.edtNome.focus();

//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitCidadeAltera() {
	 var Form;
   Form = document.frmCidadeAltera;
   if (Form.edtNome.value.length == 0) {
      alert("É necessário informar a descrição da Cidade !");
      Form.edtNome.focus();
      return false;
   }
   
   //Verifica se o checkbox de ativo está marcado
   if (Form.chkAtivo.checked) {
   	 var chkAtivoValor = 1;
   } else {
   	 var chkAtivoValor = 0;
 	 }
   		
   return true;
}
</script>

<FORM name='frmCidadeAltera' action='sistema.php?ModuloNome=CidadeAltera' method='post' onSubmit='return wdSubmitCidadeAltera()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Cidade </span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
			</table>

      <table id='2' width='750' align='left' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td width='750' class='text'>

          <?php
		    		//Verifica se a página está abrindo vindo de uma postagem
	          if($_POST['Alterar']) {
							//Recupera os valores vindo do formulário e atribui as variáveis
							$id = $_POST["Id"];			
							$chkAtivo = $_POST["chkAtivo"];
							$edtNome = $_POST["edtNome"];
							$edtUf = $_POST["edtUf"];

							//Monta e executa a query
    	    		$sql = mysql_query("
               									UPDATE cidades SET 
																ativo = '$chkAtivo',
																nome = '$edtNome',
																uf = '$edtUf'
																WHERE id = '$id' ");			 
							
							//Exibe a mensagem de inclusão com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Cidade alterada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
            }

		//Recebe os valores passados do form anterior para edição do registro
		if($_POST) {
		  $CidadeId = $_POST["Id"]; 
		} else {
		  $CidadeId = $_GET["Id"]; 
		}
		//Monta o sql
    $sql = "SELECT * FROM cidades WHERE id = $CidadeId";
		//Executa a query
    $resultado = mysql_query($sql);
		//Monta o array dos dados
    $campos = mysql_fetch_array($resultado);
		//Efetua o switch para a figura de status ativo
		switch ($campos[ativo]) {
          case 00: $ativo_status = "value='1'";	  break;
          case 01: $ativo_status = "value='1' checked";  break;
		}
				
		?>

        <TABLE cellSpacing='0' cellPadding='0' width='100%' border='0'>
          <tr>
            <td width="484"></td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        <input name="Id" type="hidden" value="<?php echo $CidadeId ?>" />
            <INPUT name='Alterar' type='submit' class='button' id="Alterar" accessKey='S' title="Salva o registro atual [Alt+S]" value='Salvar Registro'>
            <INPUT class=button title="Cancela as alterações efetuadas no registro [Alt+C]" accessKey='C' name='Reset' type='reset' id='Reset' value='Cancela Alterações'>
             </TD>
             <TD align="right">
							 <input class="button" title="Retorna ao formulário de cadastro" name='btnVoltar' type='button' id='btnVoltar' value='Voltar' style="width:70px" onclick="window.location='sistema.php?ModuloNome=CidadeCadastra';" />						 
						 </TD>
	       </TR>
         </TBODY>
         </TABLE>
           
         <TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
         <TBODY>
           <TR>
             <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='20'>
               <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
               <TBODY>
                 <TR>
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os novos dados do registro e clique em [Salvar Registro] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             
				 </TD>
	       </TR>
           <TR>
             <TD class='dataLabel' width='15%'>
               <span class="dataLabel">Descri&ccedil;&atilde;o:</span>
             </TD>
             <TD width="85%" colspan='3' class=tabDetailViewDF>
               <table width="100%" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="333" height='20'>                    
                     <label></label>
                     <input name="edtNome" type="text" class='datafield' id="edtNome" style="width: 300" size="60" maxlength="50" value="<?php echo $campos[nome] ?>">
                   </td>
                   <td width="110">
                     <div align="right">Cadastro Ativo
                       <input name="chkAtivo" type="checkbox" id="chkAtivo" <?php echo $ativo_status ?>>
                     </div>
                   </td>
                 </tr>
               </table>
						 </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>Uf:</TD>
             <TD colspan='3' class=tabDetailViewDF>
        		   <select class="datafield"name="edtUf" id="edtUf">
      		  	   <option selected value='<?php echo $campos[uf] ?>'><?php echo $campos[uf] ?></option>
				 			   <option value="AC">AC</option>
					       <option value="AL">AL</option>
					       <option value="AM">AM</option>
					       <option value="BA">BA</option>
					       <option value="CE">CE</option>
					       <option value="DF">DF</option>
					       <option value="ES">ES</option>
					       <option value="GO">GO</option>
					       <option value="MA">MA</option>
					       <option value="MG">MG</option>
					       <option value="MS">MS</option>
					       <option value="MT">MT</option>
					       <option value="PA">PA</option>
					       <option value="PB">PB</option>
					       <option value="PE">PE</option>
					       <option value="PI">PI</option>
					       <option value="PR">PR</option>
					       <option value="RJ">RJ</option>
					       <option value="RN">RN</option>
					       <option value="RO">RO</option>
					       <option value="RR">RR</option>
					       <option value="RS">RS</option>
				    	   <option value="SC">SC</option>
					       <option value="SE">SE</option>
					       <option value="SP">SP</option>
					       <option value="TO">TO</option>
      			   </select>		 		 
			 				</TD>
           </TR>           
         </TBODY>
	   		 </TABLE>
     	 </td>
   	 </tr>
		</FORM>
	</table>  	 

  </tr>
</table>
