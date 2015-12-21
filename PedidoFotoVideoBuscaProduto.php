<?php 
###########
## Módulo para busca de produtos comprados pelo formando para utilização no pedido do foto e vídeo
## Criado: 26/08/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

// Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';


//Captura o evento para filtragem dos formandos
$EventoId = $_GET['EventoId'];

//Camptura o id original do formando, para caso for uma alteração da conta
$FormandoId = $_GET['FormandoId'];

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//Efetua o lookup na tabela de formandos
//Monta o sql de pesquisa
$lista_formandos = "SELECT * FROM eventos_formando WHERE id = $FormandoId AND evento_id = $EventoId";

//Executa a query
$sql_formandos = mysql_query($lista_formandos);

//Conta o total de formandos que existem no evento
$total_formandos = mysql_num_rows($sql_formandos);

//Caso o total de formandos for zero
if ($total_formandos == 0) {
 
  //Exibe a mensagem que não há formandos para este evento
  echo "<span style='color: #990000'><b>[ Não há formandos cadastrados para o evento escolhido ! ]</b></span>
        <input type='hidden' name='cmbFormandoId' id='cmbFormandoId' value='0'>
 "; 
  
} else {
  
?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="440">
			<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Produtos Adquiridos pelo Formando</span>
		</td>
  </tr>
  <tr>
    <td colspan="5"><img src="image/bt_espacohoriz.gif" width="100%" height="12" /></td>
  </tr>
</table>
<table cellspacing="0" cellpadding="0" border="0">
  <tr>
    <td>
      
		</td>
  </tr>
  <tr>
  	<td style="PADDING-BOTTOM: 2px">  		
			<input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
  	</td>
 </tr>
</table>

<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">  
 <tr height="20">
	 <td width="30" class="listViewThS1">
	   <div align="center">Inc.</div>
	 </td>
	 <td width="300" class="listViewThS1">
	 	 &nbsp;&nbsp;Descrição do Produto
	 </td>
	 <td width="80" align="center" class="listViewThS1">
	 	 Quantidade
	 </td>
	 <td width="350" align="left" class="listViewThS1">
	 	 Observações
	 </td>
 </tr>

 <?php
	 
	 //Cria a variavel zerada para o contador de checkboxes
	 $edtItemChk = 0;
	  
	 
	 //Percorre o array
   while ($dados_formandos = mysql_fetch_array($sql_formandos))
   {
   	
	 
 ?>
	   
 <tr height="24">
	 <td colspan="7" valign="bottom" style="padding-left: 8px; padding-bottom: 4px; border-bottom: 1px dashed;">    				 	 
		 <span style="font-size: 14px"><b>							 
		 		<b><?php echo $dados_formandos["nome"] ?></b>
		 </span>
	 </td>						 
 </tr>
 						   
	   <?php
	 
		 //Monta a query de filtragem dos itens
		 $filtra_item = "SELECT * FROM categoria_fotovideo WHERE empresa_id = $empresaId ORDER BY nome";
		
		 //Executa a query
		 $lista_item = mysql_query($filtra_item);
		 
		 //Cria um contador com o número de contar que a query retornou
		 $nro_item = mysql_num_rows($lista_item);						   
	  	
		 $marca_formando = 0;
	   
		 //Percorre o array
	   while ($dados_item = mysql_fetch_array($lista_item)){
	   	
	   			   	
	   	 //Efetua a pesquisa para ver se o item já se encontra cadastrado no evento
	   	 $filtra_item_evento = "SELECT 
			 											quantidade_disponivel,
														valor_venda,
														comissao,
														bonus_comissao,
														quantidade_venda,
														valor_desconto,
														quantidade_brinde
														FROM eventos_fotovideo 
														WHERE evento_id = $EventoId 
														AND formando_id = $dados_formandos[id]
														AND item_id = '$dados_item[id]'";
		
			 	//Executa a query
			 	$query_procura_item_evento = mysql_query($filtra_item_evento);
			 			 								
				//Monta um array com o item de retorno
				$dados_procura_item_evento = mysql_fetch_array($query_procura_item_evento);
				
				//Conta se retornou algum registro
				$conta_retorno = mysql_num_rows($query_procura_item_evento);
				
				//Caso encontrou o item para ser incluso no orçamento
				if ($conta_retorno == 1) 
        {
					
          //Seta para marcar o checkbox
					$chkItem = "checked='checked'";
					
					//marca o formando como atendido
					$marca_formando = 1;
				
        } 
        
        else 
        {
					
          //Seta para o chekbox não ser marcado
					$chkItem = "";
				
        }	
		 						 
		 							 				
 ?>

 <tr height="16">
	 <td valign="middle" style="border-bottom: 1px dashed;">
		 <div align="center">
		 <input name="<?php echo ++$edtItemChk ?>" type="checkbox" value="<?php echo $dados_item[id] ?>" style="border: 0px" title="Clique para marcar ou desmarcar a aquisição deste produto pelo formando" <?php echo $chkItem ?>/>
		 <input name="edtFormando<?php echo $edtItemChk ?>" type="hidden" value="<?php echo $dados_formandos[id] ?>" />
		 </div>
	 </td>
	 <td valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" style="border-bottom: 1px dashed;">
		 <span style="color: #33485C"><b><?php echo $dados_item[nome] ?></b></span>
	 </td>    				 	 
	 <td align="center" valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="border-bottom: 1px dashed;">
	 		<input name="edtQuantidade<?php echo $edtItemChk ?>" type="text" class="datafield" style="width: 36px" maxlength="10" title="Informe a quantidade vendida do produto ao formando" value="<?php echo $dados_procura_item_evento[quantidade_venda] ?>" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
	 </td>
	 <td valign="middle" bgcolor="#fdfdfd" class="currentTabList" style="padding-top:2px; padding-bottom: 2px; border-bottom: 1px dashed;">
		  <textarea name="edtObs<?php echo $edtItemChk ?>" wrap="virtual" class="datafield" id="edtObs<?php echo $edtItemChk ?>" style="width: 350px; height: 26px; font-size: 11px"></textarea>							 
	 </td>						 	 
		 
 </tr>

	 <?php
	 	//Cria a variável para o total da venda para o formando
	 	$total_venda_formando = $total_venda_formando +  (($dados_procura_item_evento[valor_venda] * $dados_procura_item_evento[quantidade_venda]) - $dados_procura_item_evento[valor_desconto]);
	 		 							 							 	
		
		//Fecha o while
	 	}
		
		if ($marca_formando != 1)
    {	 		
	 		
	 		echo "<tr>
						<td height='28' colspan='4' align='center' valign='middle' style='padding-right: 0px; padding-bottom: 4px'>
							<br/>
              <b><span style='font-size: 14px; color: #990000'>ESTE FORMANDO NÃO POSSUI NENHUMA COMPRA DE FOTO E VÍDEO !</span></b>
						</td>						
					</tr>";	
       
	 	}
				
				
		
							 	
	 	
	 	
	//Fecha o while da categoria
	}
   	//Envia com o formulario o total final do contador para efetuar o for depois
	 	?>	
	 <input name="edtTotalChk" type="hidden" value="<?php echo $edtItemChk ?>"></input>
	 <input name="EventoId" type="hidden" value="<?php echo $EventoId ?>"></input>		
 </table>
 
 <br/>
 
 <table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="440">
			<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Selecione o Fornecedor para o Pedido</span>
		</td>
  </tr>
  <tr>
    <td colspan="5"><img src="image/bt_espacohoriz.gif" width="100%" height="12" /></td>
  </tr>
</table>

<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
  <tr>
    <td width="140" class="dataLabel">
      <span class="dataLabel">Fornecedor:</span>             
    </td>
    <td class="tabDetailViewDF">
      <select name="cmbFornecedorId" id="cmbFornecedorId" style="width:400px">
         <option value="0">Selecione uma Opção</option>
         <?php 
        	 //Monta o while para gerar o combo de escolha
        	 while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) { 
         ?>
         <option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . " - " . $lookup_fornecedor->nome ?></option>
         <?php } ?>
      </select>
    </td>
  </tr>
  <tr>
    <td width="140" class="dataLabel" valign="top">
      <span class="dataLabel">Observações:</span>             
    </td>
    <td class="tabDetailViewDF">
      <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 100px"></textarea> 
    </td>
  </tr>  
</table>           

<?php
  
}

?>