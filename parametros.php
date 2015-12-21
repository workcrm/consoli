<?php 
###########
## Módulo para alteraçao de Parametros
## Criado: 22/08/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Monta o lookup da tabela de subgrupos
//Monta o SQL
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = '1' AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);


//Monta o lookup da tabela de condição de pagamento
//Monta o SQL
$lista_condicao = "SELECT id, nome FROM condicao_pgto WHERE empresa_id = '1' AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_condicao = mysql_query($lista_condicao);


//Monta o lookup da tabela de categorias
//Monta o SQL
$lista_categoria = "SELECT * FROM categoria_conta WHERE empresa_id = '1' AND ativo = '1' AND tipo = '1' ORDER BY nome";
//Executa a query
$dados_categoria = mysql_query($lista_categoria);
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="JavaScript">

//document.frmCategoriaAltera.edtNome.focus();

//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitCategoriaAltera() {
	 var Form;
   Form = document.frmCategoriaAltera;
   
	 if (Form.edtSubgrupo.value.length == 0) {
      alert("É necessário informar o Subgrupo !");
      Form.edtSubgrupo.focus();
      return false;
   }
	 if (Form.edtCategoria.value.length == 0) {
      alert("É necessário informar a Categoria !");
      Form.edtCategoria.focus();
      return false;
   }   
	 if (Form.edtCondpagto.value.length == 0) {
      alert("É necessário informar o Subgrupo !");
      Form.edtCondpagto.focus();
      return false;
   }
   
   return true;
}
</script>

<FORM name='frmCategoriaAltera' action='parametros.php' method='post' onSubmit='return wdSubmitCategoriaAltera()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td width='440'></br>&nbsp;&nbsp;<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Parâmetros do Sistema </span></td>
			  </tr>
			  <tr>
			    <td colspan='5'>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				</td>
			  </tr>
			</table>

</br>

      <table align="center" id='2' width='750' align='left' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td width='750' class='text'>

          <?php
		    		//Verifica se a página está abrindo vindo de uma postagem
	          if($_POST['Alterar']) {
							//Recupera os valores vindo do formulário e atribui as variáveis
							$edtSubgrupo = $_POST["edtSubgrupo"];
							$edtCategoria = $_POST["edtCategoria"];
							$edtCondpagto = $_POST["edtCondpagto"];

							//Monta e executa a query
    	    		$sql = mysql_query("
               									UPDATE parametros_sistema SET 
																sub_grupo_id = '$edtSubgrupo',
																categoria_conta_id = '$edtCategoria',
																condicao_pgto_id = '$edtCondpagto'");			 
							
							//Exibe a mensagem de inclusão com sucesso
        			echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Parâmetros alterados com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
            
            die();
						}

		//Monta o sql
    $sql = "SELECT * FROM parametros_sistema";
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
	        <input name="Id" type="hidden" value="<?php echo $CategoriaId ?>" />
            <INPUT name='Alterar' type='submit' class='button' id="Alterar" accessKey='S' title="Salva o registro atual [Alt+S]" value='Salvar Registro'>
            <INPUT class=button title="Cancela as alterações efetuadas no registro [Alt+C]" accessKey='C' name='Reset' type='reset' id='Reset' value='Cancela Alterações'>
             </TD>
             <TD align="right">
							 &nbsp; 
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
               <span class="dataLabel">Sub-grupo:</span>
             </TD>
             <TD width="85%" colspan='3' class=tabDetailViewDF>
                <select name="edtSubgrupo" id="edtSubgrupo" style="width:350px">
                 	<?php while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo)) { ?>
                 <option <?php if ($lookup_subgrupo->id == $campos[sub_grupo_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_subgrupo->id ?>"><?php echo $lookup_subgrupo->nome ?>				 </option>
        	      <?php } ?>
               </select>
						 </TD>
           </TR>
           <TR>
             <TD class='dataLabel' width='15%'>
               <span class="dataLabel">Categoria:</span>
             </TD>
             <TD width="85%" colspan='3' class=tabDetailViewDF>
                 <select name="edtCategoria" id="edtCategoria" style="width:350px">
                 	<?php while ($lookup_categoria = mysql_fetch_object($dados_categoria)) { ?>
                 <option <?php if ($lookup_categoria->id == $campos[categoria_conta_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_categoria->id ?>"><?php echo $lookup_categoria->nome ?>				 </option>
        	      <?php } ?>
               </select>
						 </TD>
           </TR>
           <TR>
             <TD class='dataLabel' width='15%'>
               <span class="dataLabel">Condição Pgto:</span>
             </TD>
             <TD width="85%" colspan='3' class=tabDetailViewDF>
                 <select name="edtCondicao" id="edtCondicao" style="width:350px">
                 	<?php while ($lookup_condicao = mysql_fetch_object($dados_condicao)) { ?>
                 <option <?php if ($lookup_condicao->id == $campos[condicao_pgto_id]) {
                        echo " selected ";
                      } ?>
                     value="<?php echo $lookup_condicao->id ?>"><?php echo $lookup_condicao->nome ?>				 </option>
        	      <?php } ?>
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
