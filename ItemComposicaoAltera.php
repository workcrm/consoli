<?php 
###########
## Módulo para alteraçao de composição de produto
## Criado: 21/06/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workeventos
* @abstract Módulo Para alteracao de composicao de produto
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

if ($_GET["headers"] == 1) {
	//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Monta o lookup da tabela de materiais
//Monta o SQL
$lista_material = "SELECT * FROM item_evento WHERE empresa_id = $empresaId AND ativo = '1' AND tipo_material = '1' ORDER BY nome";
//Executa a query
$dados_material = mysql_query($lista_material);
?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitItem() {
	 var Form;
   Form = document.Item;
   if (Form.cmbMaterialId.value == 0) {
      alert("É necessário selecionar um Material !");
      Form.cmbMaterialId.focus();
      return false;
   }

   if (Form.edtQuantidade.value.length == 0) {
      alert("É necessário informar a Quantidade do Material !");
      Form.edtQuantidade.focus();
      return false;
   }
     		
   return true;
}
</script>

<FORM name='Item' action='sistema.php?ModuloNome=ItemComposicaoAltera' method='post' onSubmit='return wdSubmitItem()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Composicao de Produto </span></td>
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
							$cmbMaterialId = $_POST["cmbMaterialId"];
							$edtQuantidade = $_POST["edtQuantidade"];

							//Monta e executa a query
    	    		$sql = mysql_query("
               									UPDATE item_evento_composicao SET 
																material_id = '$cmbMaterialId',
																quantidade = '$edtQuantidade'
																WHERE id = '$id' ");			 
							
							//Exibe a mensagem de inclusão com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Composição alterada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
            }

		//Recebe os valores passados do form anterior para edição do registro
		if($_POST) {
		  $ComposicaoId = $_POST["Id"]; 
		  $ItemId = $_POST["ItemId"]; 
		} else {
		  $ComposicaoId = $_GET["Id"]; 
		  $ItemId = $_GET["ItemId"]; 
		}
		//Monta o sql
    $sql = "SELECT * FROM item_evento_composicao WHERE id = $ComposicaoId";
		//Executa a query
    $resultado = mysql_query($sql);
		//Monta o array dos dados
    $campos = mysql_fetch_array($resultado);
		?>

        <TABLE cellSpacing='0' cellPadding='0' width='100%' border='0'>
          <tr>
            <td width="484"></td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        <input name="Id" type="hidden" value="<?php echo $ComposicaoId ?>" />
					<input name="ItemId" type="hidden" value="<?php echo $ItemId ?>" />
            <INPUT name='Alterar' type='submit' class='button' id="Alterar" accessKey='S' title="Salva o registro atual [Alt+S]" value='Salvar Registro' >
            <INPUT class=button title="Cancela as alterações efetuadas no registro [Alt+C]" accessKey='C' name='Reset' type='reset' id='Reset' value='Cancela Alterações'>
             </TD>
             <TD align="right">
							 <input class="button" title="Retorna ao formulário de cadastro" name='btnVoltar' type='button' id='btnVoltar' value='Voltar' style="width:70px" onclick="window.location='sistema.php?ModuloNome=ItemComposicaoCadastra&ItemId=<?php echo $ItemId ?>';" />						 
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
               <select name="cmbMaterialId" id="cmbMaterialId" style="width:350px">
                 	<?php while ($lookup_material = mysql_fetch_object($dados_material)) { ?>
                 <option <?php if ($lookup_material->id == $campos[material_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_material->id ?>"><?php echo $lookup_material->nome ?>				 </option>
        	      <?php } ?>
               </select>
						 </TD>
           </TR>
           <TR>
             <TD class='dataLabel' width='15%'>
               <span class="dataLabel">Quantidade:</span>
             </TD>
             <TD width="85%" colspan='3' class=tabDetailViewDF>
								<input name="edtQuantidade" type="text" class="datafield" id="edtQuantidade" size="16" maxlength="14" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos[quantidade] ?>">
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
