<?php 
###########
## Módulo para Cadastro de Músicas
## Criado: 06/10/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";
?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id){
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitMaterial() {
	 var Form;
   Form = document.Musica;
   if (Form.edtNome.value.length == 0) {
      alert("É necessário informar o Nome da Música !");
      Form.edtNome.focus();
      return false;
   }
   		
   return true;
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="Musica" action="sistema.php?ModuloNome=MusicaCadastra" method="post" onsubmit="return wdSubmitMusica()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width="440">
				<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastro de Músicas</span>
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
		    		//Verifica se a página está abrindo vindo de uma postagem
            if($_POST["Submit"]) {
				  	
						//Recupera os valores vindo do formulário e atribui as variáveis
				  	$edtEmpresaId = $empresaId;
		        $edtNome = $_POST["edtNome"];
		        $chkAtivo = $_POST["chkAtivo"];
		        $edtInterprete = $_POST["edtInterprete"];
		        $edtLetra = $_POST["edtLetra"];		      
		        
						//Monta e executa a query
    	    	$sql = mysql_query("INSERT INTO musicas (
																empresa_id, 
																nome,
																interprete,
																letra,
																ativo
																) values (				
																'$edtEmpresaId',
																'$edtNome',
																'$edtInterprete',
																'$edtLetra',
																'$chkAtivo'
																);");
	
						//Exibe a mensagem de inclusão com sucesso
        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Música cadastrada com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
         		}
        ?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
	        	 <td style="PADDING-BOTTOM: 2px">
	        		 <input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Música">
            	 <input class="button" title="Limpa o conteúdo dos campos digitados"  name="Reset" type="reset" id="Reset" value="Limpar Campos">
             </td>
             <td align="right">
						 		<input class="button" title="Emite o relatório das músicas cadastradas" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="abreJanela('./relatorios/MusicaRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" />
						 </td>
	       	</tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="18">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados da Música e clique em [Salvar Música] </td>
			     			 </tr>
		       		 </table>							
						 </td>
	       	 </tr>
           <tr>
             <td class="dataLabel" width="15%">
               <span class="dataLabel">Nome da Música:</span>             
						 </td>
             <td class="tabDetailViewDF">
               <table width="100%" cellpadding="0" cellspacing="0">
                 <tr valign="middle">
                   <td height="20">
                     <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 350px" size="60" maxlength="70">									 
									 </td>
                   <td>
                     <div align="right">Cadastro Ativo
                       <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" checked>
                     </div>									 
									 </td>
                 </tr>
               </table>						 
						 </td>
           </tr>
           <tr>
             <td class="dataLabel">Intérprete:</td>
             <td class="tabDetailViewDF">
								<input name="edtInterprete" type="text" class="datafield" id="edtInterprete" style="width: 350px" size="60" maxlength="70">
						 </td>		
        	 </tr>
           <tr>
             <td class="dataLabel" valign="top">Letra:</td>
             <td colspan="3" class="tabDetailViewDF">
								<textarea name="edtLetra" wrap="virtual" class="datafield" id="edtLetra" style="width: 100%; height: 400px"></textarea>
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
<br/>

<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
	  <tr>
	    <td colspan="15" align="right">
	      <table border="0" cellpadding="0" cellspacing="0" width="100%">
	        <tr>
	          <td colspan="2" nowrap align="left"  class="listViewPaginationTdS1"><span class="pageNumbers">Músicas Cadastradas</span></td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	  <tr height="20">
        <td width="36" class="listViewThS1">
          <div align="center">A&ccedil;&atilde;o</div>
        </td>
				<td width="410" class="listViewThS1">&nbsp;&nbsp;Nome da Música</td>
				<td width="250" class="listViewThS1">Intérprete</td>
        <td class="listViewThS1" style="padding-right: 5px"><div align="center">Ativo</div></td>
	  </tr>

	<?php
	  //Monta a tabela de consulta das Músicas acadastrados
	  //Cria a SQL
	  $consulta = "SELECT * FROM musicas WHERE empresa_id = $empresaId ORDER BY nome";
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
        <td style="padding-left: 3px">
          <img src="image/grid_exclui.gif" alt="Excluir Registro" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclusão do registro ?\nA exclusão de registros desta tabela não é recomendada.\nRecomendamos a utilização da caixa [Cadastro Ativo] caso desejar desativar um registro.')) {wdCarregarFormulario('ProcessaExclusaoGet.php?Id=<?php echo $dados[id] ?>&Modulo=musicas&Retorno=MusicaCadastra','conteudo')}" style="cursor: pointer"></a>
												          
          <img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('MusicaAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">
					
					<img src="image/grid_imprime.gif" alt="Imprimir Letra da Música" width="12" height="12" border="0" onclick="abreJanela('./relatorios/MusicaImprimePDF.php?MusicaId=<?php echo $dados[id] ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" style="cursor: pointer">					
        </td>
	    	<td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" class="oddListRowS1" onclick="wdCarregarFormulario('MusicaAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')">
			  <a title="Clique para editar este registro" href="#"><?php echo $dados[nome] ?></a>
      	</td>

      	<td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
			  &nbsp;<?php echo $dados["interprete"] ?>
				</td>
						
      	<td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
			  	<div align="center"><?php echo $ativo_figura ?></div>
				</td>

	<?php
	//Fecha o while
	}
	?>
	</table>
	
</table>
