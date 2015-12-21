<?php 
###########
## M�dulo para altera�ao de Grupos de Conta
## Criado: 04/06/2007 - Maycon Edinger
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

//document.frmCategoriaAltera.edtNome.focus();

//Fun��o que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitGrupoAltera() {
	 var Form;
   Form = document.frmGrupoAltera;
   if (Form.edtNome.value.length == 0) {
      alert("� necess�rio informar a descri��o do Centro de Custo !");
      Form.edtNome.focus();
      return false;
   }
   
   return true;
}
</script>

<form name="frmGrupoAltera" action="sistema.php?ModuloNome=GrupoContaAltera" method="post" onSubmit="return wdSubmitGrupoAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Altera��o de Centro de Custo de Contas </span></td>
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
							$chkAtivo = $_POST["chkAtivo"];
							$edtNome = $_POST["edtNome"];

							//Monta e executa a query
    	    		$sql = mysql_query("
               									UPDATE grupo_conta SET 
																ativo = '$chkAtivo',
																nome = '$edtNome'
																WHERE id = '$id' ");			 
							
							//Exibe a mensagem de inclus�o com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Centro de Custo alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
            }

		//Recebe os valores passados do form anterior para edi��o do registro
		if($_POST) {
		  $GrupoId = $_POST["Id"]; 
		} else {
		  $GrupoId = $_GET["Id"]; 
		}
		//Monta o sql
    $sql = "SELECT * FROM grupo_conta WHERE id = $GrupoId";
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

        <TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
          <tr>
            <td width="484"></td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        <input name="Id" type="hidden" value="<?php echo $GrupoId ?>" />
            <INPUT name="Alterar" type="submit" class="button" id="Alterar" title="Salva o centro de custo atual" value="Salvar Registro">
            <INPUT class=button title="Cancela as altera��es efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Altera��es">
             </TD>
             <TD align="right">
							 <input class="button" title="Retorna ao formul�rio de cadastro" name="btnVoltar" type="button" id="btnVoltar" value="Voltar" style="width:70px" onclick="window.location='sistema.php?ModuloNome=GrupoContaCadastra';" />						 
						 </TD>
	       </TR>
         </TBODY>
         </TABLE>
           
         <TABLE class="tabDetailView" cellSpacing="0" cellPadding="0" width="100%" border="0">
         <TBODY>
           <TR>
             <TD class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colSpan="20">
               <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
               <TBODY>
                 <TR>
                   <TD class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os novos dados do registro e clique em [Salvar Registro] </TD>
			     </TR>
		       </TBODY>
		       </TABLE>             
				 </TD>
	       </TR>
           <TR>
             <TD class="dataLabel" width="15%">
               <span class="dataLabel">Descri&ccedil;&atilde;o:</span>
             </TD>
             <TD width="85%" colspan="3" class=tabDetailViewDF>
               <table width="100%" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="333" height="20">                    
                     <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 300" size="60" maxlength="50" value="<?php echo $campos[nome] ?>">
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
         </TBODY>
	   		 </TABLE>
     	 </td>
   	 </tr>
		</FORM>
	</table>  	 

  </tr>
</table>
