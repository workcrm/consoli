<?php
###########
## Módulo para Exibição das locacoes pesquisados
## Criado: 29/08/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Efetua o switch para a forma de visualização
switch ($_GET["edtVisualizar"]) 
{
	
	case 1: $where_vis = ""; break;
	case 2: $where_vis = "AND loc.situacao = 1"; break;
	case 3: $where_vis = "AND loc.situacao = 2"; break;

}

//Efetua o switch para a forma de financeiro
switch ($_GET["edtFinanceiro"]) 
{
	
	case 0: 
		$where_fin = ""; 
		$nome_fin = "Posição Financeira: <span style='color: #990000'><b>Qualquer</b></span>";	
	break;
	case 1: 
		$where_fin = "AND loc.posicao_financeira = 1";
		$nome_fin = "Posição Financeira: <span style='color: #990000'><b>A Receber</b></span>";
	break;
	case 2: 
		$where_fin = "AND loc.posicao_financeira = 2";
		$nome_fin = "Posição Financeira: <span style='color: #990000'><b>Recebido</b></span>"; 
	break;
	case 3: 
		$where_fin = "AND loc.posicao_financeira = 3";
		$nome_fin = "Posição Financeira: <span style='color: #990000'><b>Cortesia</b></span>"; 
	break;

}

$edtDataIni = DataMySQLInserir($_GET[edtDataIni]);
$edtDataFim = DataMySQLInserir($_GET[edtDataFim]);

if ($_GET['edtDataIni'] != "")
{
	
	$where_data = "AND loc.data >= '$edtDataIni' AND loc.data <= '$edtDataFim'";
	$data_classifica = "No período de <span style='color: #990000'><b>$_GET[edtDataIni]</b></span> a <span style='color: #990000'><b>$_GET[edtDataFim]</b></span>";

}

//Verifica se o usuário digitou um argumento para pesquisar	  
if ($_GET['edtPesquisa'] != "") 
{
	
	$pesquisa = $_GET["edtPesquisa"];
	
	//Monta a query
	$consulta = "SELECT * FROM locacao loc WHERE loc.descricao LIKE '%$pesquisa%' AND loc.empresa_id = $empresaId $where_vis $where_fin ORDER BY data";

} 

else 

{	  		  
	
	$filtragem = $_GET['ChaveFiltragem'];
	
	//Caso clicar no botáo para exibir todas as clientes
	if ($filtragem == "todos") 
	{
	  
		$consulta = "SELECT 
					loc.id,
					loc.data,
					loc.tipo_pessoa,
					loc.pessoa_id,
					loc.descricao,
					loc.situacao,
					loc.devolucao_prevista,
					loc.devolucao_realizada,
					loc.observacoes,
					loc.recebido_por,
					loc.posicao_financeira,
					cli.id as cliente_id,
					cli.nome as cliente_nome,
					forn.id as fornecedor_id,
					forn.nome as fornecedor_nome,
					col.id as colaborador_id,
					col.nome as colaborador_nome
					FROM locacao loc 
					LEFT OUTER JOIN clientes cli ON cli.id = loc.pessoa_id
					LEFT OUTER JOIN fornecedores forn ON forn.id = loc.pessoa_id
					LEFT OUTER JOIN colaboradores col ON col.id = loc.pessoa_id
					WHERE loc.empresa_id = $empresaId  $where_vis $where_fin $where_data
					ORDER BY loc.data";	  
	} 
	
	else 
	
	{
		
		//Monta a query pesquisando por empresa e por inicial 
		$consulta = "SELECT 
					loc.id,
					loc.data,
					loc.tipo_pessoa,
					loc.pessoa_id,
					loc.descricao,
					loc.situacao,
					loc.devolucao_prevista,
					loc.devolucao_realizada,
					loc.observacoes,
					loc.recebido_por,
					cli.id as cliente_id,
					cli.nome as cliente_nome,
					forn.id as fornecedor_id,
					forn.nome as fornecedor_nome,
					col.id as colaborador_id,
					col.nome as colaborador_nome
					FROM locacao loc 
					LEFT OUTER JOIN clientes cli ON cli.id = loc.pessoa_id
					LEFT OUTER JOIN fornecedores forn ON forn.id = loc.pessoa_id
					LEFT OUTER JOIN colaboradores col ON col.id = loc.pessoa_id
					WHERE loc.descricao LIKE '$filtragem%' AND loc.empresa_id = $empresaId  $where_vis $where_data
					ORDER BY loc.data";
	}
  
}

//Executa a query
$listagem = mysql_query($consulta);

//Conta o numero de contas que a query retornou
$registros = mysql_num_rows($listagem);

//Determina a Quantidade de registros por página para cartão de visita
$regs_pagina_rec = "15"; 

//Recebe o numero da pagina para nevegar	
$pagina_rec = $_GET['PaginaRec']; 

//Caso não seja especificado uma página
if (!$pagina_rec) 
{
	
	//Define como a página 1
	$pc_rec = "1";

//Caso for especificado a pagina
} 

else 

{
	
	//Alimenta a variável com o numero da página
	$pc_rec = $pagina_rec;

}
	
$inicio_rec = $pc_rec - 1;
$inicio_rec = $inicio_rec * $regs_pagina_rec;
	  
//Monta e executa o sql limitando e fazendo a paginação
$limite_rec = mysql_query("$consulta LIMIT $inicio_rec, $regs_pagina_rec");

//Descomentar para visualizar o código sql que está sendo gerado
//echo "$consulta LIMIT $inicio_rec, $regs_pagina_rec</br></br>";
	  
//Monta e executa o sql contendo todos os registros
$todos_rec = mysql_query("$consulta");
  	  
//Verifica o número total de registros
$tot_regs_rec = mysql_num_rows($todos_rec);
     
//cria o contador inicial do numero do registro pra exibir na tela
if ($inicio_rec == 0) 
{ 
	
	//Determina o contador inicial como 1
	$conta_inicial_rec = 1;
	
	//Verifica quantos registros está exibindo
	$conta_final_rec = mysql_num_rows($limite_rec); 

} 

else 

{
	
	$conta_inicial_rec = $inicio_rec + 1;
	//Workaround
	$conta_final_rec = (mysql_num_rows($limite_rec) + $conta_inicial_rec) - 1; 

}

// verifica o número total de páginas
$tot_pags_rec = $tot_regs_rec / $regs_pagina_rec; 

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<?php
				
				//Caso não tenha compromissos então não exibe a linha de cabeçalho.
				if ($registros == 0) 
				{ 
					
					echo "
						<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
							<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
								<td align='center'>&nbsp;&nbsp;Não foram encontrados Locações para a pesquisa !</td>
							</tr>
						</table>";
				
				} 
				
				else 
				
				{

					//Monta os botões de paginação
					$anterior_rec = $pc_rec -1;
					$proximo_rec = $pc_rec +1;

					//Exibe a tabela com os dados de controle de paginação
					echo "
						<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
							<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
								<td width='20'>";
									
									//Verifica se necessita exibir o botão de anterior
									if ($pc_rec > 1) 
									{				
										
										//Exibe o botão
										echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloLocacoesLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>&nbsp;<img src='./image/bt_anterior.gif' alt='Exibe a Página Anterior' border='0' align='middle'></a></td><td width='50'><a href='#' onClick=\"wdCarregarFormulario('ModuloLocacoesLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>Anterior</a></td>";
									
									}	 
				?>
				
				
						</td>
						<td align="center">
							<span class='pageNumbers'>(Exibindo Locações: <?php echo $conta_inicial_rec . " - " . $conta_final_rec . " de " . $tot_regs_rec ?>)<br/><?php echo $data_classifica ?></span>
						</td>
						<td valign="middle" width="50" align="right">
				
				<?php 
				  
					//Verifica se necessita exibir o botão de próximo
					if ($pc_rec < $tot_pags_rec) 
					{				
				    
						//Exibe o botão
						echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloLocacoesLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'>Próximo&nbsp;</a></td><td width='20' align='right'><a href='#' onClick=\"wdCarregarFormulario('ModuloLocacoesLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'><img src='./image/bt_proximo.gif' alt='Exibe a Próxima Página' border='0' align='middle'>&nbsp;</a>";
				  
					}	 
				
					//Fecha a tabela com os dados de controle da paginação
					echo "</td></tr></table></br>";
				
					//Monta e percorre o array dos dados
					while ($dados_rec = mysql_fetch_array($limite_rec))
					{

						//Efetua o switch para o campo de status
						switch ($dados_rec[situacao]) 
						{
							case 1: $desc_status = "Pendente"; break;
							case 2: $desc_status = "Recebida"; break;
						}
					
						//Efetua o switch para o campo de pessoa
						switch ($dados_rec[tipo_pessoa]) 
						{
						
							//Se for cliente
							case 1: 
								$pessoa_tipo = "Cliente";
								$pessoa_nome = $dados_rec[cliente_nome]; 
							break;
							//Se for fornecedor
							case 2: 
								$pessoa_tipo = "Fornecedor"; 
								$pessoa_nome = $dados_rec[fornecedor_nome];
							break;
							//Se for colaborador
							case 3: 
								$pessoa_tipo = "Colaborador"; 
								$pessoa_nome = $dados_rec[colaborador_nome];							
							break;
						}
          
						//Efetua o switch para o campo de posição financeira
						switch ($dados_rec[posicao_financeira]) 
						{
							
							case 1: $desc_financeiro = "A Receber"; break;
							case 2: $desc_financeiro = "Recebido"; break;
							case 3: $desc_financeiro = "Cortesia"; break;	
						
						} 

						$data = DataMySQLRetornar($dados_rec[data]);
						$data_prevista = DataMySQLRetornar($dados_rec[devolucao_prevista]);
						$data_realizada = DataMySQLRetornar($dados_rec[devolucao_realizada]);

						echo "
						<table width='100%' border='0' cellspacing='0' cellpadding='0' class='listView'>
							<tr height='12'>
								<td height='12' colspan='4' class='listViewPaginationTdS1'>      
									<font color='#444444'>Clique no título da locação para exibir os detalhes</font>
								</td>
							</tr>
						</table>
						<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
							<tr height='20'>
								<td class='listViewThS1'>
									&nbsp;&nbsp;<a title='Clique para exibir os detalhes desta locação' href='#' onClick=\"wdCarregarFormulario('LocacaoExibe.php?LocacaoId=$dados_rec[id]&headers=1','conteudo')\"><font size='3'><span style='color: #990000'>[$desc_status]</span> - $dados_rec[descricao]</font></a>
								</td>
							</tr>";
			  		
					//Verifica o nível de acesso do usuário
					if ($nivelAcesso >= 3) 
					{
						
						echo "
						<tr height='16'>
							<td valign='top' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 5px; padding-left: 5px; border-top: 1px solid' width='100%'>
								<table width='100%' cellpadding='0' cellspacing='0' border='0' >
									<tr valign='middle'>
										<td width='5%'>
											<img src='./image/bt_item_gd.gif'/> 
										</td>
										<td>
											<a title='Clique para gerenciar os itens/produtos desta locação' href='#' onClick=\"wdCarregarFormulario('ItemLocacaoCadastra.php?LocacaoId=$dados_rec[id]&headers=1','conteudo')\">Gerenciar Itens/Produtos</a> 
										</td>
										<td align='right'>
											<input class='button' title='Gerenciar a devolução dos itens da locação' name='btnRetorno' type='button' id='btnRetorno' value='Gerenciar Retorno dos Itens' style='width:160px' onclick=\"wdCarregarFormulario('LocacaoRetorno.php?LocacaoId=$dados_rec[id]&headers=1','conteudo')\">
										</td>
									</tr>
								</table>
							</td>
						</tr>";
	  		  	
					}
	  		  	
					echo "
						<tr height='16'>
							<td valign='top' bgcolor='#fdfdfd' class='oddListRowS1' style='padding-left: 5px; padding-left: 5px; border-top: 1px solid'>
								<b>$pessoa_tipo:</br>$pessoa_nome</b>
							</td>
						</tr>
						<tr height='40'>
							<td valign='top' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px solid'>
								<table width='100%' cellpadding='0' cellspacing='0' border='0'>
									<tr>
										<td colspan='3' style='padding-left: 5px; padding-top: 6px; border: 1px solid'>
											Data: <b>$data</b>
										</td>
									</tr>
									<tr>		
										<td width='33%' style='padding-left: 5px; border: 1px solid'>											
											Devolução Prevista: <b>$data_prevista</b>
										</td>
										<td width='33%' style='padding-left: 5px; border: 1px solid'>	      		  				
											Devolução Realizada: <b>$data_realizada</b>
										</td>
										<td style='padding-left: 5px; border: 1px solid'>
											Recebido por: <b>$dados_rec[recebido_por]</b>
										</td>
									</tr>
									<tr>
										<td colspan='3' style='padding-left: 5px'>
											Posição Financeira: <b>$desc_financeiro</b>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
	    		</br>";
			}
				
			?>   
			</br>
			<?php
		   
				//Fecha o if de se conter registros na consulta
				}
			?>
		</td>
	</tr>
</table>	
