<?php
###########
## Módulo para Exibição das Ordens de Compra
## Criado: 13/03/2012 - Maycon Edinger
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

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{
	
	//Inclui o arquivo para manipulação de datas
	include "./include/ManipulaDatas.php";

}

//Captura as variaveis
$edtNumeroOc = $_GET["edtPesquisa"];
$edtDepartamentoId = $_GET["DepartamentoId"];
$edtFornecedorId = $_GET["FornecedorId"];
$edtEventoId = $_GET["EventoId"];
$edtDataIni = $_GET["edtDataIni"];
$edtDataFim = $_GET["edtDataFim"];
$edtDataEntregaIni = $_GET["edtDataEntregaIni"];
$edtDataEntregaFim = $_GET["edtDataEntregaFim"];

//Verifica se o usuário digitou um argumento para pesquisar	  
if ($edtNumeroOc > 0) 
{
	
	$where_oc = "AND oc.id = '$edtNumeroOc'";
	
}

//Verifica se o usuário digitou um argumento para pesquisar	  
if ($edtDepartamentoId > 0) 
{
	
	$where_departamento = "AND oc.departamento_id = '$edtDepartamentoId'";
	
}

//Verifica se o usuário digitou um argumento para pesquisar	  
if ($edtFornecedorId > 0) 
{
	
	$where_fornecedor = "AND oc.fornecedor_id = '$edtFornecedorId'";
	
}

//Verifica se o usuário digitou um argumento para pesquisar	  
if ($edtEventoId > 0) 
{
	
	$where_evento = "AND oc.evento_id = '$edtEventoId'";
	
}

//Verifica se o usuário digitou um argumento para pesquisar	  
if ($edtDataIni != '') 
{
	$data_ini_banco = DataMySQLInserir($edtDataIni);
	$data_fim_banco = DataMySQLInserir($edtDataFim);
	
	$where_datas = "AND oc.data BETWEEN '$data_ini_banco' AND '$data_fim_banco'";
	
}

//Verifica se o usuário digitou um argumento para pesquisar	  
if ($edtDataEntregaIni != '') 
{
	$data_entrega_ini_banco = DataMySQLInserir($edtDataEntregaIni);
	$data_entrega_fim_banco = DataMySQLInserir($edtDataEntregaFim);
	
	$where_prazo = "AND oc.prazo_entrega BETWEEN '$data_entrega_ini_banco' AND '$data_entrega_fim_banco'";
	
}

$consulta = "SELECT 
			oc.id,
			oc.data,
			oc.prazo_compra,
			oc.solicitante,
			oc.departamento_id,
			oc.evento_id,
			oc.fornecedor_id,
			oc.transportadora,
			oc.cond_pgto,
			oc.prazo_entrega,
			dep.nome AS departamento_nome,
			forn.nome AS fornecedor_nome,
			eve.nome AS evento_nome
			FROM ordem_compra oc
			LEFT OUTER JOIN departamentos dep ON dep.id = oc.departamento_id
			LEFT OUTER JOIN fornecedores forn ON forn.id = oc.fornecedor_id
			LEFT OUTER JOIN eventos eve ON eve.id = oc.evento_id
			WHERE 1 = 1
			$where_oc $where_fornecedor $where_departamento $where_evento $where_datas $where_prazo
			ORDER BY oc.data";
			
//Executa a query
$listagem = mysql_query($consulta);

//Conta o numero de contas que a query retornou
$registros = mysql_num_rows($listagem);

//************
//Determina a Quantidade de registros por página para cartão de visita
$regs_pagina_rec = "30"; 

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
      		
					echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
							<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
								<td  scope='col'>&nbsp;&nbsp;Não foram encontradas Ordens de Compra para a pesquisa !</td>
							</tr>
						</table>";
						
				} 
				
				else 
				
				{

					//Monta os botões de paginação
					$anterior_rec = $pc_rec -1;
					$proximo_rec = $pc_rec +1;

					//Exibe a tabela com os dados de controle de paginação
					echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
							<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
								<td width='20'>";
									
							//Verifica se necessita exibir o botão de anterior
							if ($pc_rec > 1) 
							{				
								
								//Exibe o botão
								echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloOrdemCompraLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>&nbsp;<img src='./image/bt_anterior.gif' alt='Exibe a Página Anterior' border='0' align='middle'></a></td><td width='50'><a href='#' onClick=\"wdCarregarFormulario('ModuloOrdemCompraLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>Anterior</a></td>";
							
							}	 
				?>
				
				
						</td>
						<td align="center">
							<span class='pageNumbers'>(Exibindo Ordens de Compra: <?php echo $conta_inicial_rec . " - " . $conta_final_rec . " de " . $tot_regs_rec ?>)</span>
						</td>
						<td valign="middle" width="50" align="right">
				
				<?php 
				  
					//Verifica se necessita exibir o botão de próximo
					if ($pc_rec < $tot_pags_rec) 
					{				
						
						//Exibe o botão
						echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloOrdemCompraLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'>Próximo&nbsp;</a></td><td width='20' align='right'><a href='#' onClick=\"wdCarregarFormulario('ModuloOrdemCompraLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'><img src='./image/bt_proximo.gif' alt='Exibe a Próxima Página' border='0' align='middle'>&nbsp;</a>";
					
					}	 
				
					//Fecha a tabela com os dados de controle da paginação
					echo "</td></tr></table></br>";
				
					//Monta e percorre o array dos dados
					while ($dados_rec = mysql_fetch_array($limite_rec))
					{

						$data_emissao = DataMySQLRetornar($dados_rec[data]);
						
						if ($dados_rec[evento_id] > 0) $evento_id = "($dados_rec[evento_id]) -";
						$prazo_entrega = '';
						if ($dados_rec[prazo_entrega] != '0000-00-00') $prazo_entrega = DataMySQLRetornar($dados_rec[prazo_entrega]);
						
						echo "
						<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
							<tr height='20'>
								<td class='listViewThS1'>
									&nbsp;&nbsp;<font size='3'><span style='color: #990000'>[O.C: $dados_rec[id]]</span> - <a title='Clique para exibir os detalhes desta OC' href='#' onClick=\"wdCarregarFormulario('OrdemCompraExibe.php?OrdemId=$dados_rec[id]','conteudo')\">($dados_rec[fornecedor_id]) - $dados_rec[fornecedor_nome]</font></a>
								</td>
							</tr>
							<tr height='16'>
								<td valign='top' bgcolor='#fdfdfd' class='oddListRowS1' style='padding: 0px' >
									<span class='oddListRowS1' style='padding: 5px'><strong>Emissão:</strong> $data_emissao</span>
								</td>
							</tr>
							<tr height='16'>
								<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-right: 5px'>
									<span class='oddListRowS1' style='padding: 5px'><strong>Evento:</strong> $evento_id $dados_rec[evento_nome]</span>
								</td>					
							</tr>
							<tr height='16'>
								<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-left: 5px'>
									Solicitante: $dados_rec[solicitante]
								</td>
							</tr>
							<tr height='16'>
								<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-left: 5px'>
									Departamento: $dados_rec[departamento_nome]
								</td>
							</tr>	  		  
							<tr height='16'>
								<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-left: 5px'>
									Prazo de Entrega: $prazo_entrega
								</td>
							</tr>	
						</table>";
        
			?>
            
			</br>

			<?php
		   
				//Fecha o while
				}
			 
				//Fecha o if de se conter registros na consulta
				}
		   
			?>
		
		</td>
	</tr>
</table>	
