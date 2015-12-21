<link rel="stylesheet" type="text/css" href="include/workStyle.css">
<script src="include/workFuncoes.js" type="text/javascript"></script>

<?php 
###########
## Módulo para Cadastro de Ordens de Compra
## Criado: 28/02/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
	
}

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Recupera os valores vindos do formulário e armazena nas variaveis
if($_POST["Submit"])
{

	//Variaveis
	$numero_oc = $_POST['edtProdutoOc'];

	$cmbProdutoId = $_POST['cmbProdutoId'];

	$edtProdutoQuantidade = $_POST['edtProdutoQuantidade'];

	$edtProdutoValor = MoneyMySQLInserir($_POST['edtProdutoValor']);
	
	//Monta o sql e executa a query de inserção
	$sql = mysql_query("INSERT INTO ordem_compra_produto (
						ordem_compra_id,
						produto_id, 
						quantidade, 
						valor_unitario
		
						) VALUES (
		
						'$numero_oc',
						'$cmbProdutoId',
						'$edtProdutoQuantidade',
						'$edtProdutoValor'		
						);");
	
	?>
	
	<table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'>
		<tr>
			<td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'>
				<img src='./image/bt_informacao.gif' border='0' />
			</td>
			<td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'>
				<strong>Produto incluído com sucesso !</strong>
			</td>
		</tr>
	</table>
	<br/>
	<br/>
	<input name="ImprimeOc" type="button" class="button" style="width:120px" id="ImprimeOc" title="Imprime esta OC" value="Imprimir OC" onclick="abreJanela('./relatorios/OrdemCompraRelatorioPDF.php?OrdemId=<?php echo $numero_oc ?>');" />
	<br/>
	<br/>
	
	<?php
	
	//Consulta todos os produtos da OC
	$consulta = "SELECT 
				prod.id,
				produto.id AS produto_id,
				produto.nome AS produto_nome,
				produto.unidade,
				prod.quantidade,
				prod.valor_unitario 
				FROM ordem_compra_produto prod 
				LEFT OUTER JOIN item_evento produto ON produto.id = prod.produto_id
				WHERE ordem_compra_id = $numero_oc				
				ORDER BY produto.nome";

	//Executa a query
	$listagem = mysql_query($consulta);
	
	$registros = mysql_num_rows($listagem);
	
	//Caso tenha produtos associados
	if ($registros > 0)
	{
	
		?>
		
		<table width="100%" id="4" cellpadding="0" cellspacing="0" border="0" class="listView">
		<tr>
			<td colspan="8" align="right">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td height="20" nowrap align="left"  class="listViewPaginationTdS1"><span class="pageNumbers">Produtos da Ordem de Compra</span></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr height="20">
			<td width="50" class="listViewThS1">
				<div align="center">A&ccedil;&atilde;o</div>
			</td>
			<td width="60" align="center" class="listViewThS1">Código</td>
			<td class="listViewThS1">&nbsp;&nbsp;Descrição do Produto</td>
			<td align="right" width="80" class="listViewThS1">Quantid.</td>
			<td align="center" width="26" class="listViewThS1">Un</td>
			<td align="right" width="66" class="listViewThS1">Unitario</td>				        
			<td align="right" width="66" class="listViewThS1" style="padding-right: 6px">Total</td>
		</tr>
		
		<?php	
	
		$categoria_lista = 0;
			
		$linha = 1; 

		//Monta e percorre o array com os dados da consulta
		while ($dados = mysql_fetch_array($listagem))
		{

			if ($linha < $registros)
			{
			
				$borda = "border-bottom: 1px #aaa dashed;";
				
			}
			
			else
			
			{
			
				$borda = '';
				
			}
		
			//Caso seja uma outra categoria de produtos.
			if ($categoria_lista != $dados["categoria_id"])
			{
			
				echo "<tr height='16'>
						<td colspan='6' style='padding-left: 6px; padding-top: 5px; padding-bottom: 5px'><span class='TituloModulo'>$dados[categoria_nome]</span></td>
						</tr>";
			
			}

			//Fecha o php, mas o while continua
			?>

			<tr height="16">
				<td height="24" style="<?php echo $borda ?>">
					<div align="center">
						<img src="image/grid_exclui.gif" alt="Excluir Registro" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclusão do registro ?\nA exclusão de registros desta tabela não é recomendada.\nRecomendamos a utilização da caixa [Cadastro Ativo] caso desejar desativar um registro.')) {wdCarregarFormulario('ProcessaExclusaoGet.php?Id=<?php echo $dados[id] ?>&Modulo=item_evento&Retorno=ItemCadastra','conteudo')}" style="cursor: pointer"></a>
						<img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('OrdemCompraProdutoAvulsoAltera.php?OrdemId=<?php echo $numero_oc ?>&Id=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">   
					</div>
				</td>
				<td style="<?php echo $borda ?>" valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
					<?php echo $dados["produto_id"] ?>
				</td>
				<td style="<?php echo $borda ?>" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" onclick="wdCarregarFormulario('OrdemCompraProdutoAvulsoAltera.php?OrdemId=<?php echo $numero_oc ?>&Id=<?php echo $dados[id] ?>&headers=1','conteudo')">
					<a title="Clique para exibir este registro" href="#"><?php echo $dados[produto_nome] ?></a>
				</td>
				<td style="<?php echo $borda ?>" valign="middle" align="right" bgcolor="#fdfdfd" class="currentTabList">
					<?php echo $dados["quantidade"] ?>
				</td>
				<td style="<?php echo $borda ?>" valign="middle" align="center" bgcolor="#fdfdfd" class="currentTabList">
					<?php echo $dados["unidade"] ?>
				</td>
				<td style="<?php echo $borda ?>" align="right" bgcolor="#fdfdfd" class="currentTabList">
					<?php echo number_format($dados["valor_unitario"], 2, ",", ".") ?>
				</td>
				<td style="padding-right: 6px; <?php echo $borda ?>" align="right" bgcolor="#fdfdfd" class="currentTabList">
					<?php 
					
						$total_item = $dados["quantidade"] * $dados["valor_unitario"];
						
						$total_geral = $total_geral + $total_item;
						
						echo number_format($total_item, 2, ",", "."); 
						
					?>
				</td>
			</tr>
			
			<?php
				
				$categoria_lista = $dados["categoria_id"];
				
				$linha++;
				
		//Fecha o while
		}
		
		
	?>
	<tr height="16">
		<td height="24" colspan="6" align="right" style="border-top: 1px #aaa solid;">
			<b>TOTAL GERAL:</b>&nbsp;
		</td>
		<td height="24" style="border-top: 1px #aaa solid; padding-right: 6px" align="right">
			<b><?php echo number_format($total_geral, 2, ",", ".") ?></b>
		</td>
	</table>
	<?php
	
	}
	
}

?>