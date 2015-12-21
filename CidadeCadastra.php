<?php 
###########
## M�dulo para Cadastro de Cidades
## Criado: 18/05/2007 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########
/**
* @package workeventos
* @abstract M�dulo Para cadastro de Cidades
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

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

//document.Cidade.edtNome.focus();
//Fun��o que alterna a visibilidade do painel especificado.

function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitCidade() {
	 var Form;
   Form = document.Cidade;
   if (Form.edtNome.value.length == 0) {
      alert("� necess�rio informar a descri��o da Cidade !");
      Form.edtNome.focus();
      return false;
   }
   
   return true;
}
</script>

<FORM name='Cidade' action='sistema.php?ModuloNome=CidadeCadastra' method='post' onSubmit='return wdSubmitCidade()'>

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width='440'>
				<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastro de Cidades</span>
			</td>
	  </tr>
	  <tr>
	    <td colspan='5'>
		    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
		</td>
	  </tr>
	</table>

      <table id='2' width='750' align='left' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td width='750' class="text">

          <?php
		    		//Verifica se a p�gina est� abrindo vindo de uma postagem
            if($_POST['Submit']) {
				  	//Recupera os valores vindo do formul�rio e atribui as vari�veis
				  	$edtEmpresaId = $empresaId;
		        $edtNome = $_POST["edtNome"];
		        $edtUf = $_POST["edtUf"];
		        $chkAtivo = $_POST["chkAtivo"];
						//Monta e executa a query
    	    	$sql = mysql_query("INSERT INTO cidades (
																empresa_id, 
																nome,
																uf, 
																ativo
																) values (				
																'$edtEmpresaId',
																'$edtNome',
																'$edtUf',
																'$chkAtivo'
																);");
	
						//Exibe a mensagem de inclus�o com sucesso
        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Cidade cadastrada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
         		}
        ?>

        <TABLE cellSpacing='0' cellPadding='0' width='100%' border='0'>
        <TBODY>
          <tr>
            <td width="484">
				</td>
          </TR>
          <tr>
	        <TD style="PADDING-BOTTOM: 2px">
	        	<INPUT name="Submit" type="submit" class="button" accessKey="S" title="Salva o registro atual [Alt+S]" value="Salvar Cidade" >
            <INPUT class="button" title="Limpa o conte�do dos campos digitados [Alt+L]" accessKey='L' name='Reset' type='reset' id='Reset' value='Limpar Campos'>
             </TD>
             <TD align="right">
						 		<input class="button" title="Emite o relat�rio das Cidades cadastradas" name='btnRelatorio' type='button' id='btnRelatorio' value='Emitir Relat�rio' style="width:100px" onclick="abreJanela('./relatorios/CidadeRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" />
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
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da Cidade e clique em [Salvar Cidade] </TD>
			     			 </TR>
		       		 </TBODY>
		       		 </TABLE>             
				 		 </TD>
	       	 </TR>
           <TR>
             <TD class='dataLabel' width='15%'>
               <span class="dataLabel">Descri&ccedil;&atilde;o:</span>             </TD>
             <TD width="85%" colspan='3' class=tabDetailViewDF>
               <table width="100%" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td width="333" height='20'>
                     <input name="edtNome" type="text" class='datafield' id="edtNome" style="width: 300" size="60" maxlength="50">
                   </td>
                   <td width="110">
                     <div align="right">Cadastro Ativo
                       <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" checked>
                     </div>
                   </td>
                 </tr>
               </table>
             </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>UF:</TD>
             <TD colspan='3' class=tabDetailViewDF>
					  <select class="datafield"name="edtUf" id="edtUf">
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
</td>
</tr>

<tr>
<td>
<br>

<table width="750" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  <tr>
	    <td COLSPAN="15" align="right">
	      <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        <tr>
	          <td colspan="2" nowrap align="left"  class="listViewPaginationTdS1"><span class='pageNumbers'>Cidades Cadastradas</span></td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	  <tr height="20">
        <td width='42' class="listViewThS1">
          <div align="center">A&ccedil;&atilde;o</div>
        </td>
				<td width="575" class="listViewThS1">
          &nbsp;&nbsp;Descri��o da Cidade
        </td>
        <td nowrap="nowrap" class="listViewThS1">
	        <div align="center">Ativo</div>
        </td>
        <td nowrap="nowrap" class="listViewThS1">
	        <div align="center">Uf</div>
        </td>
	  </tr>

	<?php
	  //Monta a tabela de consulta das cidades acadastradas
	  //Cria a SQL
	  $consulta = "SELECT * FROM cidades WHERE empresa_id = $empresaId ORDER BY nome";
		//Executa a query
	  $listagem = mysql_query($consulta);
	  //Monta e percorre o array com os dados da consulta
	  while ($dados = mysql_fetch_array($listagem)){

      //Efetua o switch para exibir a imagem para quando o cadastro estiver ativo
  	  switch ($dados[ativo]) {
       	case 00: $ativo_figura = "";	break;
			  case 01: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Cadastro Ativo' />";	break;       	
			}
			
	?>

	  <tr height='16'>
        <td width='42'>
		  	  <div align="center">
            <img src="image/grid_exclui.gif" alt="Excluir Registro" width="12" height="12" border="0" onClick="if(confirm('Confirma a exclus�o do registro ?\nA exclus�o de registros desta tabela n�o � recomendada.\nRecomendamos a utiliza��o da caixa [Cadastro Ativo] caso desejar desativar um registro.')) {wdCarregarFormulario('ProcessaExclusaoGet.php?Id=<?php echo $dados[id] ?>&Modulo=cidades&Retorno=CidadeCadastra','conteudo')}" style="cursor: pointer"></a>
												          
            <img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('CidadeAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">          
          </div>
        </td>
	    <td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' class='oddListRowS1' onclick="wdCarregarFormulario('CidadeAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')">
			  <a title="Clique para editar este registro" href="#"><?php echo $dados[nome] ?></a>
      </td>

      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
			  <div align="center"><?php echo $ativo_figura ?></div>
			</td>

      <td valign='middle' bgcolor='#fdfdfd' class='currentTabList'>
			  <div align="center"><strong><?php echo $dados[uf] ?></strong></div>
			</td>

	<?php
	//Fecha o while
	}
	?>
	</table>
	
</table>
