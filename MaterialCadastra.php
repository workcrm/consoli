<?php 
###########
## Módulo para Cadastro de Materiais
## Criado: 18/06/2007 - Maycon Edinger
## Alterado: 27/06/2007 - Maycon Edinger 
## Alterações: 
## 27/06/2007 - Implementado o cadastro de opções do item
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) {
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
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
   Form = document.Material;
   if (Form.edtNome.value.length == 0) {
      alert("É necessário informar a descrição do material !");
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
<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<form name="Material" action="sistema.php?ModuloNome=MaterialCadastra" method="post" onsubmit="return wdSubmitMaterial()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td width="440">
				<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastro de Materiais</span>
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
		        $chkProduto = $_POST["chkProduto"];
		        $edtUnidade = $_POST["edtUnidade"];
		        $edtValorCusto = MoneyMySQLInserir($_POST["edtValorCusto"]);
		        
		        //Verifica se é para ser um produto tb
		        if ($chkProduto == 1) {
		        	//Seta as variáveis para marcar o produto para exibir automaticamente no evento e orçamento
		        	$chkEvento = 1;
		        	$chkOrcamento = 1;
		        } else {
							//Seta as variáveis para não marcar o produto para exibir automaticamente no evento e orçamento
		        	$chkEvento = 0;
		        	$chkOrcamento = 0;		        	
		        }
		        
						//Monta e executa a query
    	    	$sql = mysql_query("INSERT INTO item_evento (
																empresa_id, 
																nome,
																unidade,
																tipo_produto,
																tipo_material,
																exibir_evento,
																exibir_orcamento,
																valor_custo,
																ativo
																) values (				
																'$edtEmpresaId',
																'$edtNome',
																'$edtUnidade',
																'$chkProduto',
																'1',
																'$chkEvento',
																'$chkOrcamento',
																'$edtValorCusto',
																'$chkAtivo'
																);");
	
						//Exibe a mensagem de inclusão com sucesso
        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Material cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
         		}
        ?>

        <table cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
	        	 <td style="PADDING-BOTTOM: 2px">
	        		 <input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Material">
            	 <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos">
             </td>
             <td align="right">
						 		<input class="button" title="Emite o relatório dos materiais cadastrados" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="abreJanela('./relatorios/MaterialRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>')" />
						 </td>
	       	</tr>
         </table>
           
         <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
           <tr>
             <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="18">
               <table cellspacing="0" cellpadding="0" width="100%" border="0">
                 <tr>
                   <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Material e clique em [Salvar Material] </td>
			     			 </tr>
		       		 </table>							
						 </td>
	       	 </tr>
           <tr>
             <td class="dataLabel" width="15%">
               <span class="dataLabel">Descri&ccedil;&atilde;o:</span>             
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
             <td class="dataLabel">Unidade:</td>
             <td class="tabDetailViewDF">
							  <select class="datafield"name="edtUnidade" id="edtUnidade">
						        <option value="PC">PC - Peça</option>
						        <option value="UN">UN - Unidade</option>
						        <option value="GR">GR - Grama</option>
						        <option value="KG">KG - Kilo</option>
						        <option value="LT">LT - Litro</option>
                    <option value="PT">PT - Pacote</option>
                    <option value="VD">VD - Vidro</option>
                    <option value="LT">LT - Lata</option>
						        <option value="BD">BD - Balde</option>
						        <option value="CX">CX - Caixa</option>
						        <option value="GL">GL - Galão</option>
						        <option value="MT">MT - Metro</option>
                    <option value="M2">M2 - Metro Quadrado</option>
						        <option value="M3">M3 - Metro Cúbico</option>						        
						    </select>						 
							</td>
           </tr>
           <tr>
             <td class="dataLabel">Pre&ccedil;o de Custo:</td>
             <td class="tabDetailViewDF">
							<?php
								//Cria um objeto do tipo WDEdit 
								$objWDComponente = new WDEditReal();
								
								//Define nome do componente
								$objWDComponente->strNome = "edtValorCusto";
								//Define o tamanho do componente
								$objWDComponente->intSize = 16;
								//Busca valor definido no XML para o componente
								$objWDComponente->strValor = "";
								//Busca a descrição do XML para o componente
								$objWDComponente->strLabel = "";
								//Determina um ou mais eventos para o componente
								$objWDComponente->strEvento = "";
								//Define numero de caracteres no componente
								$objWDComponente->intMaxLength = 14;
								
								//Cria o componente edit
								$objWDComponente->Criar();  
							?>								
           </tr>
           <tr>
             <td class="dataLabel" valign="top">Opções:</td>
             <td colspan="3" class="tabDetailViewDF">
								<input name="chkProduto" type="checkbox" value="1" style="border: 0px">
								<span style="font-size: 11px">Este material também pode ser usado como um produto.</span>
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
	          <td colspan="2" nowrap align="left"  class="listViewPaginationTdS1"><span class="pageNumbers">Materiais Cadastrados</span></td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	  <tr height="20">
        <td width="36" class="listViewThS1">
          <div align="center">A&ccedil;&atilde;o</div>
        </td>
				<td width="510" class="listViewThS1">&nbsp;&nbsp;Descrição do Material</td>
				<td align="center" width="20" class="listViewThS1">Un</td>
				<td align="right" width="70" class="listViewThS1">Pr. Custo</td>
        <td class="listViewThS1"><div align="center">Ativo</div></td>
	  </tr>

	<?php
	  //Monta a tabela de consulta dos itens acadastrados
	  //Cria a SQL
	  $consulta = "SELECT * FROM item_evento WHERE empresa_id = $empresaId AND tipo_material = '1' ORDER BY nome";
		//Executa a query
	  $listagem = mysql_query($consulta);
	  //Monta e percorre o array com os dados da consulta
	  while ($dados = mysql_fetch_array($listagem)){
      
      //Efetua o switch para exibir a imagem para quando o cadastro estiver ativo
  	  switch ($dados[ativo]) {
       	case 00: $ativo_figura = "";	break;
			  case 01: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Cadastro Ativo' />";	break;       	
  	  }
      
      
      //Efetua o switch do campo de unidade de medida
  	  switch ($dados[unidade]) {
  	     case "PC": $texto_unidade = "PC - Peça"; break;
         case "UN": $texto_unidade = "UN - Unidade"; break;
         case "GR": $texto_unidade = "GR - Grama"; break;
				 case "KG": $texto_unidade = "KG - Kilo"; break;	    
				 case "LT": $texto_unidade = "LT - Litro"; break;
			   case "PT": $texto_unidade = "PT - Pacote"; break;
         case "VD": $texto_unidade = "VD - Vidro"; break;
         case "LT": $texto_unidade = "LT - Lata"; break;
         case "BD": $texto_unidade = "BD - Balde"; break;
         case "CX": $texto_unidade = "CX - Caixa"; break;
         case "GL": $texto_unidade = "GL - Galão"; break;
         case "MT": $texto_unidade = "MT - Metro"; break;
         case "M2": $texto_unidade = "M2 - Metro Quadrado"; break;
         case "M3": $texto_unidade = "M3 - Metro Cúbico"; break;      	
  	  }
      
  	//Fecha o php, mas o while continua
	?>

	  <tr height="16">
        <td>
		  	  <div align="center">
            <img src="image/grid_exclui.gif" alt="Excluir Registro" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclusão do registro ?\nA exclusão de registros desta tabela não é recomendada.\nRecomendamos a utilização da caixa [Cadastro Ativo] caso desejar desativar um registro.')) {wdCarregarFormulario('ProcessaExclusaoGet.php?Id=<?php echo $dados[id] ?>&Modulo=item_evento&Retorno=MaterialCadastra','conteudo')}" style="cursor: pointer"></a>
												          
          <img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('MaterialAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer"></div>
        </td>
	    <td valign="middle" nowrap="nowrap" bgcolor="#fdfdfd" class="oddListRowS1" onclick="wdCarregarFormulario('MaterialAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')">
			  <a title="Clique para editar este registro" href="#"><?php echo $dados["nome"] ?></a>
      </td>

      <td valign="middle" bgcolor="#fdfdfd" class="currentTabList">
			  &nbsp;<span title="<?php echo $texto_unidade ?>"><?php echo $dados["unidade"] ?></span>
			</td>

      <td align="right" bgcolor="#fdfdfd" class="currentTabList">
			  <?php echo "R$ " . number_format($dados["valor_custo"], 2, ",", ".") ?>
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
