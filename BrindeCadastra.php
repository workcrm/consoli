<?php 
###########
## M�dulo para Cadastro de tipos de brindes
## Criado: 25-09-2008 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
// Processa as diretivas de seguran�a 
require("Diretivas.php");
//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";
?>

<script language="JavaScript">

//document.Tipo.edtNome.focus();
//Fun��o que alterna a visibilidade do painel especificado.

function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitBrinde() {
	 var Form;
   Form = document.brinde;
   if (Form.edtNome.value.length == 0) {
      alert("� necess�rio informar a descri��o do Brinde !");
      Form.edtNome.focus();
      return false;
   }
   
   //Verifica se o checkbox de ativo est� marcado
   if (Form.chkAtivo.checked) {
   	 var chkAtivoValor = 1;
   } else {
   	 var chkAtivoValor = 0;
 	 }
   		
   return true;
}
</script>

<form name="brinde" action="sistema.php?ModuloNome=BrindeCadastra" method="post" onsubmit="return wdSubmitBrinde()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width="440">
				<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastro de Brindes</span>
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
		    		//Verifica se a p�gina est� abrindo vindo de uma postagem
            if($_POST["Submit"]) {
				  	//Recupera os valores vindo do formul�rio e atribui as vari�veis
				  	$edtEmpresaId = $empresaId;
		        $edtNome = $_POST["edtNome"];
		        $chkAtivo = $_POST["chkAtivo"];
						//Monta e executa a query
    	    	$sql = mysql_query("INSERT INTO brindes (
																empresa_id, 
																nome, 
																ativo
																) values (				
																'$edtEmpresaId',
																'$edtNome',
																'$chkAtivo'
																);");
	
						//Exibe a mensagem de inclus�o com sucesso
        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><b>Brinde cadastrado com sucesso !</b></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
         		}
        ?>

        <TABLE cellSpacing='0' cellPadding="0" width="100%" border="0">
        <TBODY>
          <tr>
            <td width="484">
						</td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        	<INPUT name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Brinde" onclick="wdSubmitBrinde()">
            <INPUT class="button" title="Limpa o conte�do dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos">
             </TD>
             <TD align="right">
						 		<input class="button" title="Emite o relat�rio dos dados cadastrados" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relat�rio" style="width:100px" onclick="abreJanela('./relatorios/BrindeRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" />
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
                   <TD class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do brinde e clique em [Salvar Brinde] </TD>
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
                     <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 300" size="60" maxlength="50">
                   </td>
                   <td width="110">
                     <div align="right">Cadastro Ativo
                       <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" checked>
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
</td>
</tr>

<tr>
<td>
<br>

<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  <tr>
	    <td COLSPAN="15" align="right">
	      <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        <tr>
	          <td colspan="2" nowrap align="left"  class="listViewPaginationTdS1"><span class="pageNumbers">Brindes Cadastrados</span></td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	  <tr height="20">
        <td width="42" class="listViewThS1">
          <div align="center">A&ccedil;&atilde;o</div>
        </td>
				<td width="505" class="listViewThS1" scope="col">
          &nbsp;&nbsp;Descri��o do Brinde
        </td>
        <td nowrap="nowrap" class="listViewThS1" scope="col">
	        <div align="center">Ativo</div>
        </td>
	  </tr>

	<?php
	  //Monta a tabela de consulta dos tipos acadastrados
	  //Cria a SQL
	  $consulta = "SELECT * FROM brindes WHERE empresa_id = $empresaId ORDER BY nome";
		//Executa a query
	  $listagem = mysql_query($consulta);
	  //Monta e percorre o array com os dados da consulta
	  while ($dados = mysql_fetch_array($listagem)){
      //Efetua o switch para exibir a imagem para quando o cadastro estiver ativo
  	  switch ($dados[ativo]) {
       	case 00: $ativo_figura = "";	break;
			  case 01: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Cadastro Ativo' />";	break;       	
  	  }
  	//Fecha o php, mas o while continua
	?>

	  <tr height="16">
        <td width="42">
		  	  <div align="center">
            <img src="image/grid_exclui.gif" alt="Excluir Registro" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclus�o do registro ?\nA exclus�o de registros desta tabela n�o � recomendada.\nRecomendamos a utiliza��o da caixa [Cadastro Ativo] caso desejar desativar um registro.')) {wdCarregarFormulario('ProcessaExclusaoGet.php?Id=<?php echo $dados[id] ?>&Modulo=brindes&Retorno=BrindeCadastra','conteudo')}" style="cursor: pointer"></a>
												          
            <img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('BrindeAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">          
          </div>
        </td>
	    <td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" class="oddListRowS1" scope="row" onclick="wdCarregarFormulario('BrindeAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')">
			  <a title="Clique para editar este registro" href="#"><?php echo $dados[nome] ?></a>
      </td>

      <td scope="row" valign="middle" bgcolor="#fdfdfd" class="currentTabList">
			  <div align="center"><?php echo $ativo_figura ?></div>
			</td>

	<?php
	//Fecha o while
	}
	?>
	</table>
	
</table>
