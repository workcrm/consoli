<?php 
###########
## M�dulo para altera�ao de musicas	
## Criado: 08/10/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

if ($_GET["headers"] == 1) {
	//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
require("Diretivas.php");
//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";
?>

<script language="JavaScript">

document.frmCategoriaAltera.edtNome.focus();

//Fun��o que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitMusicaAltera() {
	 var Form;
   Form = document.frmMusicaAltera;
   if (Form.edtNome.value.length == 0) {
      alert("� necess�rio informar o Nome da M�sica !");
      Form.edtNome.focus();
      return false;
   }
   
   return true;
}
</script>

<form name="frmMusicaAltera" action="sistema.php?ModuloNome=MusicaAltera" method="post" onsubmit="return wdSubmitMusicaAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Altera��o de M�sica</span></td>
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
		    		//Verifica se a p�gina est� abrindo vindo de uma postagem
	          if($_POST["Alterar"]) {
							//Recupera os valores vindo do formul�rio e atribui as vari�veis
							$id = $_POST["Id"];			
		        	$edtNome = $_POST["edtNome"];
		        	$chkAtivo = $_POST["chkAtivo"];
		        	$edtInterprete = $_POST["edtInterprete"];
		        	$edtLetra = $_POST["edtLetra"];;

							//Monta e executa a query
    	    		$sql = mysql_query("
               									UPDATE musicas SET 
																ativo = '$chkAtivo',
																nome = '$edtNome',
																interprete = '$edtInterprete',
																letra = '$edtLetra'
																WHERE id = '$id' ");			 
							
							//Exibe a mensagem de inclus�o com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>M�sica alterada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
            }

		//Recebe os valores passados do form anterior para edi��o do registro
		if($_POST) {
		  $MusicaId = $_POST["Id"]; 
		} else {
		  $MusicaId = $_GET["Id"]; 
		}
		//Monta o sql
    $sql = "SELECT * FROM musicas WHERE id = $MusicaId";
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

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
	        	 <td style="PADDING-BOTTOM: 2px">
	        	 	 <input name="Id" type="hidden" value="<?php echo $MusicaId ?>" />
            	 <input name="Alterar" type="submit" class="button" id="Alterar" title="Salva o registro atual" value="Salvar Registro">
            	 <input class="button" title="Cancela as altera��es efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Altera��es">
             </td>
             <td align="right">
							 <input class="button" title="Retorna ao formul�rio de cadastro" name="btnVoltar" type="button" id="btnVoltar" value="Voltar" style="width:70px" onclick="window.location='sistema.php?ModuloNome=MusicaCadastra';" />						 
						 </td>
	       	 </tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
		           <table cellspacing="0" cellpadding="0" width="100%" border="0">
		             <tr>
		               <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os novos dados do registro e clique em [Salvar Registro] </td>
					     	 </tr>
				       </table>
				   	 </td>
	       	 </tr>
           <tr>
             <td class="dataLabel" width="15%">
               <span class="dataLabel">Nome da M�sica:</span>
             </td>
             <td width="85%" colspan="3" class="tabDetailViewDF">
               <table width="100%" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="333" height="20">                    
                     <label></label>
                     <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 300" size="60" maxlength="50" value="<?php echo $campos[nome] ?>">
                   </td>
                   <td width="110">
                     <div align="right">Cadastro Ativo
                       <input name="chkAtivo" type="checkbox" id="chkAtivo" <?php echo $ativo_status ?>>
                     </div>
                   </td>
                 </tr>
               </table>
						 </td>
           </tr>
					 <tr>
             <td class="dataLabel">Int�rprete:</td>
             <td class="tabDetailViewDF">
								<input name="edtInterprete" type="text" class="datafield" id="edtInterprete" style="width: 350px" size="60" maxlength="70" value="<?php echo $campos[interprete] ?>">		
           </tr>
           <tr>
             <td class="dataLabel" valign="top">Letra:</td>
             <td colspan="3" class="tabDetailViewDF">
								<textarea name="edtLetra" wrap="virtual" class="datafield" id="edtLetra" style="width: 100%; height: 400px"><?php echo $campos[letra] ?></textarea>
						 </td>
           </tr>           
	   		 </table>
     	 </td>
   	 </tr>
	</table>
	</form>  	 

  </tr>
</table>
