<?php 
###########
## Módulo para alteraçao de Categorias de repertorio
## Criado: 06/10/2007 - Maycon Edinger
## Alterado: 03/09/2008 - Maycon Edinger
## Alterações: 
## 03/09/2008 - Alterado o nome para Momentos de Repertório
###########

if ($_GET["headers"] == 1) {
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";
?>

<script language="JavaScript">

document.frmCategoriaAltera.edtNome.focus();

//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitCategoriaAltera() {
	 var Form;
   Form = document.frmCategoriaAltera;
   if (Form.edtNome.value.length == 0) {
      alert("É necessário informar a descrição do Momento de Repertório !");
      Form.edtNome.focus();
      return false;
   }
   
   return true;
}
</script>

<form name="frmCategoriaAltera" action="sistema.php?ModuloNome=CategoriaRepertorioAltera" method="post" onsubmit="return wdSubmitCategoriaAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Momento do Repertorio </span></td>
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
		    		//Verifica se a página está abrindo vindo de uma postagem
	          if($_POST["Alterar"]) {
							//Recupera os valores vindo do formulário e atribui as variáveis
							$id = $_POST["Id"];			
							$chkAtivo = $_POST["chkAtivo"];
							$edtNome = $_POST["edtNome"];

							//Monta e executa a query
    	    		$sql = mysql_query("
               									UPDATE categoria_repertorio SET 
																ativo = '$chkAtivo',
																nome = '$edtNome'
																WHERE id = '$id' ");			 
							
							//Exibe a mensagem de inclusão com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Momento do Repertório alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
            }

		//Recebe os valores passados do form anterior para edição do registro
		if($_POST) {
		  $CategoriaId = $_POST["Id"]; 
		} else {
		  $CategoriaId = $_GET["Id"]; 
		}
		//Monta o sql
    $sql = "SELECT * FROM categoria_repertorio WHERE id = $CategoriaId";
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
	        		 <input name="Id" type="hidden" value="<?php echo $CategoriaId ?>" />
            	 <input name="Alterar" type="submit" class="button" id="Alterar" title="Salva o registro atual" value="Salvar Registro" />
            	 <input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações" />
             </td>
             <td align="right">
							 <input class="button" title="Retorna ao formulário de cadastro" name="btnVoltar" type="button" id="btnVoltar" value="Voltar" style="width:70px" onclick="window.location='sistema.php?ModuloNome=CategoriaRepertorioCadastra';" />						 
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
               <span class="dataLabel">Descri&ccedil;&atilde;o:</span>
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
	   		  </table>
     	  </td>
   	  </tr>
		</table>  	 
		</form>
  </tr>
</table>
