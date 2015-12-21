<?php 
###########
## Módulo para Cadastro de arvore de materiais
## Criado: 19/06/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
## 
###########
/**
* @package workeventos
* @abstract Módulo Para cadastro da arvore de materiais
* @author Maycon Edinger - edinger@bol.com.br
* @copyright 2007 - Maycon Edinger
*/

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

//Captura o id do produto a efetuar a consulta/composição
if ($_GET["ItemId"]) {
	$ItemId = $_GET["ItemId"];
} else {
  $ItemId = $_POST["ItemId"];
}


//Pesquisa o item na base
$query_pesquisa = mysql_query("SELECT * FROM item_evento WHERE id = $ItemId");

//Monta o array do item
$dados_pesquisa = mysql_fetch_array($query_pesquisa);

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

function wdCarregarPreco() {
	var Form;
	Form = document.cadastro;	 
	
	 //Captura o valor referente ao radio button selecionado
   var edtPrecoValor = document.getElementsByName('edtPreco');
   
	 for (var i=0; i < edtPrecoValor.length; i++) {
     if (edtPrecoValor[i].checked == true) {
       edtPrecoValor = edtPrecoValor[i].value;
       break;
     }
   }

	if (edtPrecoValor == 1) {
		url = "ItemPrecoProcessa.php?ItemId=<?php echo $ItemId ?>&Tipo=1&Preco=" + Form.Total.value;
		wdCarregarFormulario(url,'conteudo');
	}
	
	if (edtPrecoValor == 2) {		
		url = "ItemPrecoProcessa.php?ItemId=<?php echo $ItemId ?>&Tipo=2&Preco=" + Form.Total.value;
		wdCarregarFormulario(url,'conteudo');
	}
	
	if (edtPrecoValor == 3) {
		if (Form.edtMargem.value == 0) {
			alert('É necessário informar o valor da margem de lucro a aplicar !');
			Form.edtMargem.focus();
      return false;
   	}		
		url = "ItemPrecoProcessa.php?ItemId=<?php echo $ItemId ?>&Tipo=3&Preco=" + Form.Total.value + "&Margem=" + Form.edtMargem.value;
		wdCarregarFormulario(url,'conteudo');
	}
	
	if (edtPrecoValor == 4) {
		if (Form.edtValorVenda.value == 0) {
			alert('É necessário informar o valor de venda do produto a aplicar !');
			Form.edtValorVenda.focus();
      return false;
   	}		
		url = "ItemPrecoProcessa.php?ItemId=<?php echo $ItemId ?>&Tipo=4&Preco=" + Form.Total.value + "&Valor=" + Form.edtValorVenda.value;
		wdCarregarFormulario(url,'conteudo');		
	}		
}
</script>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<FORM name='Item' action='sistema.php?ModuloNome=ItemComposicaoCadastra' method='post' onSubmit='return wdSubmitItem()'>
<input name="ItemId" type="hidden" value="<?php echo $ItemId ?>" />

<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
  <tr>
    <td class="text" valign="top">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td>
				<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Composição do Produto: <span style="color: #990000"><?php echo $dados_pesquisa[nome] ?></span>
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
		    		//Verifica se a página está abrindo vindo de uma postagem
            if($_POST['Submit']) {
				  	
						//Recupera os valores vindo do formulário e atribui as variáveis
		        $cmbMaterialId = $_POST["cmbMaterialId"];
		        $edtQuantidade = $_POST["edtQuantidade"];
		        
						//Monta e executa a query
    	    	$sql = mysql_query("INSERT INTO item_evento_composicao (
																item_id, 
																material_id,
																quantidade
																) values (				
																'$ItemId',
																'$cmbMaterialId',
																'$edtQuantidade'
																);");
	
						//Exibe a mensagem de inclusão com sucesso
        		echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Material agregado a composição do produto !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
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
	        	<INPUT name="Submit" type="submit" class="button" accessKey="S" title="Agrega o material selecionado à composição do produto [Alt+S]" value="Agregar Material">
            <INPUT class="button" title="Limpa o conteúdo dos campos digitados [Alt+L]" accessKey='L' name='Reset' type='reset' id='Reset' value='Limpar Campos'>
             </TD>
             <TD align="right">
						 		<?php /*
								 <input class="button" title="Emite o relatório da composição do produto" name='btnRelatorio' type='button' id='btnRelatorio' value='Emitir Relatório' style="width:100px" onclick="abreJanela('./relatorios/ItemComposicaoRelatorioPDF.php?UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&ItemId=<?php echo $ItemId ?>')" />
								 */ ?>
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
                   <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do Material e clique em [Agregar Material] </TD>
			     			 </TR>
		       		 </TBODY>
		       		 </TABLE>				 		 
							</TD>
	       	 </TR>
           <TR>
             <TD class='dataLabel' width='15%'>
               <span class="dataLabel">Descri&ccedil;&atilde;o:</span>             </TD>
             <TD colspan='3' class=tabDetailViewDF>
               <select name="cmbMaterialId" id="cmbMaterialId" style="width:350px">
               	<option value="0">Selecione uma Opção</option>
			 				 	<?php 
								 	//Monta o while para gerar o combo de escolha
								 	while ($lookup_material = mysql_fetch_object($dados_material)) { 
							 	?>
               	<option value="<?php echo $lookup_material->id ?>"><?php echo $lookup_material->nome . " (" . $lookup_material->unidade . ")"?> </option>
              	<?php } ?>
              </select>                        
						 </TD>
           </TR>
           <TR>
             <TD class='dataLabel'>Quantidade:</TD>
             <TD colspan='3' class=tabDetailViewDF>
							 <input name="edtQuantidade" type="text" class="datafield" id="edtQuantidade" size="16" maxlength="14" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
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
	          <td colspan="2" nowrap align="left"  class="listViewPaginationTdS1"><span class='pageNumbers'>O produto <b><?php echo $dados_pesquisa[nome] ?></b> é composto pelos seguintes materiais:</span></td>
	        </tr>
	      </table>
	    </td>
	  </tr>

	<?php
	  //Monta a variável do valor total do custo do produto
  	$total_arvore = 0;
  	  
		//Monta a tabela de consulta dos itens acadastrados
	  //Cria a SQL
	  $consulta = "SELECT 
								 comp.id as composicao_id,
								 comp.item_id,
								 comp.material_id,
								 comp.quantidade,
								 pro.id,
								 pro.nome,
								 pro.unidade,
								 pro.valor_custo								 
								 FROM item_evento_composicao comp
								 INNER JOIN item_evento pro ON comp.material_id = pro.id
								 WHERE comp.item_id = $ItemId";
								 
		//Executa a query
	  $listagem = mysql_query($consulta);
		
		//Conta o numero de contas que a query retornou
		$registros = mysql_num_rows($listagem);
		
		if ($registros == 0){
		?>
			  <tr height='24'>
		      <td colspan='6' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
				  	<font color='#33485C'><strong>Não há materiais agregados à composição deste produto !</strong></font>
					</td>
			  </tr>	
		<?php			
		} else {
		?>
	  <tr height="20">
        <td width='36' class="listViewThS1">
          <div align="center">A&ccedil;&atilde;o</div>
        </td>
				<td width="400" class="listViewThS1">&nbsp;&nbsp;Descrição do Material</td>
				<td align="right" width="70" class="listViewThS1">Quantidade</td>
				<td align="center" width="20" class="listViewThS1">Un</td>
				<td align="right" width="70" class="listViewThS1">Custo Unit.</td>
				<td align="right" width="70" class="listViewThS1">Custo Tot.</td>				        
	  </tr>
	  <?php
	  //Fecha o if dos registros
	  }
	  
	  //Monta e percorre o array com os dados da consulta
	  while ($dados = mysql_fetch_array($listagem)){
      //Efetua o switch para exibir a imagem para quando o cadastro estiver ativo
  	  switch ($dados[ativo]) {
       	case 00: $ativo_figura = "";	break;
			  case 01: $ativo_figura = "<img src='./image/grid_ativo.gif' alt='Cadastro Ativo' />";	break;       	
  	  }
  	  
  	  //Monta a variável do valor do material
  	  $total_item = $dados[quantidade] * $dados[valor_custo];
  	  
  	  //Monta a variável do valor total do custo do produto
  	  $total_arvore = $total_arvore + $total_item;
  	  
  	//Fecha o php, mas o while continua
	?>

	  <tr height='16'>
        <td>
		  	  <div align="center">
            <img src="image/grid_exclui.gif" alt="Remover da composição do material" width="12" height="12" border="0" onClick="if(confirm('Confirma a exclusão do registro ?\nA exclusão de registros desta tabela não é recomendada.\nRecomendamos a utilização da caixa [Cadastro Ativo] caso desejar desativar um registro.')) {wdCarregarFormulario('ItemComposicaoExclui.php?ItemId=<?php echo $dados[item_id] ?>&ComposicaoId=<?php echo $dados[composicao_id] ?>','conteudo')}" style="cursor: pointer"></a>
												          
            <img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('ItemComposicaoAltera.php?ItemId=<?php echo $dados[item_id] ?>&Id=<?php echo $dados[composicao_id] ?>&headers=1','conteudo')" style="cursor: pointer">   
          </div>
        </td>
	    <td valign='middle' nowrap='nowrap' bgcolor='#fdfdfd' class='oddListRowS1' onclick="wdCarregarFormulario('ItemComposicaoAltera.php?ItemId=<?php echo $dados[item_id] ?>&Id=<?php echo $dados[composicao_id] ?>&headers=1','conteudo')">
			  <a title="Clique para editar este registro" href="#"><?php echo $dados[nome] ?></a>
      </td>

      <td align='right' bgcolor='#fdfdfd' class='currentTabList' style="padding-right: 8px">
			  <?php echo $dados["quantidade"] ?>
			</td>

      <td align='center' bgcolor='#fdfdfd' class='currentTabList' >
			  <?php echo $dados["unidade"] ?>
			</td>

      <td align='right' bgcolor='#fdfdfd' class='currentTabList' style="padding-right: 8px">
			  <?php echo "R$ " . number_format($dados["valor_custo"], 2, ",", ".") ?>
			</td>
			
      <td align='right' bgcolor='#fdfdfd' class='currentTabList' style="padding-right: 8px">
			  <?php echo "R$ " . number_format($total_item, 2, ",", ".") ?>
			</td>
						
	<?php
	//Fecha o while
	}	
	?>
	</table>

	</br>
	
	<?php 
	
		//Exibe o total do custo do produto
		echo "O Custo de composição do produto <b>$dados_pesquisa[nome]</b> é de R$ <b>" . number_format($total_arvore, 2, ",", ".") . "</b>.";	
	?>
	<br>
	<br>	
	O Work | Eventos permite efetuar o cálculo do preço de venda do produto com base na composição do custo dos materiais. <br>Para tanto, selecione uma das opções abaixo:<br><br>
  
	<FORM id='form' name='cadastro' method='post'>
	<input name="Total" type="hidden" value="<?php echo $total_arvore ?>" />
	<table width="100%" cellpadding="0" cellspacing="0">
    <tr valign="middle" style="padding: 1px">
		  <td height='20'>
        <input name="edtPreco" type="radio" value="1" checked/> Sair sem alterar o preço de venda do produto.
      </td>
    </tr>
    <tr valign="middle" style="padding: 1px">
      <td height='20'>
        <input type="radio" name="edtPreco" value="2" /> Aplicar o valor de custo de composição (R$ <?php echo number_format($total_arvore, 2, ",", ".") ?>) como preço de venda do produto.
			</td>
    </tr>
    <tr valign="middle" style="padding: 1px">
      <td height='20'>
        <input type="radio" name="edtPreco" value="3" /> Aplicar a margem de lucro de 
				<?php
					//Cria um objeto do tipo WDEdit 
					$objWDComponente = new WDEditReal();
					
					//Define nome do componente
					$objWDComponente->strNome = "edtMargem";
					//Define o tamanho do componente
					$objWDComponente->intSize = 5;
					//Busca valor definido no XML para o componente
					$objWDComponente->strValor = "";
					//Busca a descrição do XML para o componente
					$objWDComponente->strLabel = "";
					//Determina um ou mais eventos para o componente
					$objWDComponente->strEvento = "";
					//Define numero de caracteres no componente
					$objWDComponente->intMaxLength = 5;
					
					//Cria o componente edit
					$objWDComponente->Criar();  
				?>
				% sobre o custo de composição.        
			</td>
    </tr>
    <tr valign="middle" style="padding: 1px">
      <td height='20'>
        <input type="radio" name="edtPreco" value="4" /> Aplicar um preço de venda fixo de R$&nbsp;
				<?php
					//Cria um objeto do tipo WDEdit 
					$objWDComponente = new WDEditReal();
					
					//Define nome do componente
					$objWDComponente->strNome = "edtValorVenda";
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
			</td>
    </tr>									          
  </table>
	<input class="button" title="Aplicar o preço de venda do produto com base na opção selecionada e retorna ao cadastro de itens." name='btnRelatorio' type='button' id='btnRelatorio' value='Atualizar Preço de Venda e Sair' style="width:180px" onclick="wdCarregarPreco()" />			
	</form>
</table>
