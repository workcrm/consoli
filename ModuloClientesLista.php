<?php
###########
## Módulo para Exibição dos clientes pesquisados
## Criado: 17/04/2007 - Maycon Edinger
## Alterado: 24/04/2007 - Maycon Edinger
## Alterações: 
## 24/04/2007 - Colocado o campo telefone e celular mais visível nas consultas
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

//Verifica se o usuário digitou um argumento para pesquisar	  
if ($_GET['edtPesquisa'] != "") {
  $modo_vis = $_GET["edtVisualizar"];
	$pesquisa = $_GET["edtPesquisa"];
	//Monta a query
	//$consulta = "SELECT * FROM clientes WHERE nome LIKE '$pesquisa%' AND empresa_id = $empresaId ORDER BY nome";

	$consulta = "SELECT 
	  		  con.id,
			  	con.ativo,
			  	con.empresa_id,
			  	con.nome,
			  	con.tipo_pessoa,
			  	con.endereco,
			  	con.complemento,
			  	con.bairro,
			  	con.cidade_id,
			  	con.uf,
			  	con.cep,
			  	con.inscricao,
			  	con.cnpj,
			  	con.rg,
			  	con.cpf,
			  	con.telefone,
			  	con.fax,
			  	con.celular,
			  	con.email,
			  	con.contato,
			  	con.observacoes,
					cid.nome as cidade_nome	  
	
			  	FROM clientes con
			  	LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id
					WHERE con.nome LIKE '$pesquisa%' AND con.empresa_id = $empresaId ORDER BY con.nome";
					
//Se não, o usuário quer pesquisar por inicial da letra
} else {	  
		  
	$filtragem = $_GET['ChaveFiltragem'];
  $modo_vis = $_GET['Modo_vis'];
	//Caso clicar no botáo para exibir todas as clientes
	if ($filtragem == "todos") {
		//Monta a query pesquisando por empresa
	  //$consulta = "SELECT * FROM clientes WHERE empresa_id = $empresaId ORDER BY nome";

	$consulta = "SELECT 
	  		  con.id,
			  	con.ativo,
			  	con.empresa_id,
			  	con.nome,
			  	con.tipo_pessoa,
			  	con.endereco,
			  	con.complemento,
			  	con.bairro,
			  	con.cidade_id,
			  	con.uf,
			  	con.cep,
			  	con.inscricao,
			  	con.cnpj,
			  	con.rg,
			  	con.cpf,
			  	con.telefone,
			  	con.fax,
			  	con.celular,
			  	con.email,
			  	con.contato,
			  	con.observacoes,
					cid.nome as cidade_nome	  
	
			  	FROM clientes con
			  	LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id
			  	WHERE con.empresa_id = $empresaId ORDER BY con.nome";
			  	
	} else {
		//Monta a query pesquisando por empresa e por inicial
	  //$consulta = "SELECT * FROM clientes WHERE nome LIKE '$filtragem%' AND empresa_id = $empresaId ORDER BY nome";		 

	$consulta = "SELECT 
	  		  con.id,
			  	con.ativo,
			  	con.empresa_id,
			  	con.nome,
			  	con.tipo_pessoa,
			  	con.endereco,
			  	con.complemento,
			  	con.bairro,
			  	con.cidade_id,
			  	con.uf,
			  	con.cep,
			  	con.inscricao,
			  	con.cnpj,
			  	con.rg,
			  	con.cpf,
			  	con.telefone,
			  	con.fax,
			  	con.celular,
			  	con.email,
			  	con.contato,
			  	con.observacoes,
					cid.nome as cidade_nome	  
	
			  	FROM clientes con
			  	LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id
					WHERE con.nome LIKE '$filtragem%' AND con.empresa_id = $empresaId ORDER BY nome";		 

	}
  
}

//Executa a query
$listagem = mysql_query($consulta);

//Conta o numero de contas que a query retornou
$registros = mysql_num_rows($listagem);

//************
//Caso estiver visualizando em cartão de visita
if ($modo_vis == 1) {
	//Determina a Quantidade de registros por página para cartão de visita
	$regs_pagina_rec = "30"; 
} else {
	//Determina a quantidade de registros por página para modo listagem
	$regs_pagina_rec = "60";
}

//Recebe o numero da pagina para nevegar	
$pagina_rec = $_GET['PaginaRec']; 

//Caso não seja especificado uma página
if (!$pagina_rec) {
	//Define como a página 1
	$pc_rec = "1";
//Caso for especificado a pagina
} else {
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
if ($inicio_rec == 0) { 
  //Determina o contador inicial como 1
	$conta_inicial_rec = 1;
  //Verifica quantos registros está exibindo
  $conta_final_rec = mysql_num_rows($limite_rec); 
} else {
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
    		if ($registros == 0) { 
      		echo "
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
        		<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          		<td  scope='col'>&nbsp;&nbsp;Não foram encontradas clientes para a pesquisa !</td>
        		</tr>
        	</table>	
    			";
				} else {

				//Monta os botões de paginação
				$anterior_rec = $pc_rec -1;
				$proximo_rec = $pc_rec +1;

				//Exibe a tabela com os dados de controle de paginação
      	echo "
				<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
        	<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          	<td width='20'>";
									
							//Verifica se necessita exibir o botão de anterior
							if ($pc_rec > 1) {				
				  			//Exibe o botão
							echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloClientesLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>&nbsp;<img src='./image/bt_anterior.gif' alt='Exibe a Página Anterior' border='0' align='middle'></a></td><td width='50'><a href='#' onClick=\"wdCarregarFormulario('ModuloClientesLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>Anterior</a></td>";
				  		}	 
				?>
				
				
						</td>
						<td align="center">
							<span class='pageNumbers'>(Exibindo Clientes: <?php echo $conta_inicial_rec . " - " . $conta_final_rec . " de " . $tot_regs_rec ?>)</span>
						</td>
						<td valign="middle" width="50" align="right">
				
				<?php 
				  //Verifica se necessita exibir o botão de próximo
				  if ($pc_rec < $tot_pags_rec) {				
				    //Exibe o botão
						echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloClientesLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'>Próximo&nbsp;</a></td><td width='20' align='right'><a href='#' onClick=\"wdCarregarFormulario('ModuloClientesLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'><img src='./image/bt_proximo.gif' alt='Exibe a Próxima Página' border='0' align='middle'>&nbsp;</a>";
				  }	 
				
					//Fecha a tabela com os dados de controle da paginação
					echo "</td></tr></table></br>";
				
					//Monta e percorre o array dos dados
		    	while ($dados_rec = mysql_fetch_array($limite_rec)){

					//Monta o switch para a imagem da categoria do cliente
	    		switch ($dados_rec[tipo_pessoa]) {
          	case 1: $conta_figura = "<img src='./image/bt_prospect.gif' alt='Física' />";	break;
          	case 2: $conta_figura = "<img src='./image/bt_cliente.gif' alt='Jurídica' />"; break;
        	}



				//Caso estiver visualizando em cartão de visita
        if ($modo_vis == 1) {

				echo "
          <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
	  		    <tr height='20'>
        		  <td class='listViewThS1'>
								&nbsp;&nbsp;<font size='3'><span style='color: #990000'>[$dados_rec[id]]</span> - <a title='Clique para exibir os detalhes deste cliente' href='#' onClick=\"wdCarregarFormulario('ClienteExibe.php?ClienteId=$dados_rec[id]','conteudo')\">$dados_rec[nome]</font></a>
							</td>
			  		</tr>

			  		<tr height='16'>
        	    <td valign='top' bgcolor='#fdfdfd' class='oddListRowS1' style='padding: 0px' >
								<span class='oddListRowS1' style='padding: 5px'><strong>Fone: $dados_rec[telefone] - Celular: $dados_rec[celular]</strong></span>
		  		  		<div align='right'>$conta_figura &nbsp;</div>
    					</td>
	  		  	</tr>
	  		  	<tr height='16'>
	    	    	<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-right: 5px'>&nbsp;</td>					
	    	  	</tr>
	  		  	<tr height='16'>
	    				<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-right: 5px'>
	      		  	<div align='right'>$dados_rec[endereco] - $dados_rec[bairro]</div>
	    				</td>
			  		</tr>
	  		  	<tr height='16'>
	    				<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-right: 5px'>
	      		  	<div align='right'>$dados_rec[cep] - $dados_rec[cidade_nome] - $dados_rec[uf] </div>
	    				</td>
	    	  	</tr>	  		  
	  		  	<tr height='16'>
	    				<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-right: 5px'>&nbsp;</td>
	    	  	</tr>
	  		  	<tr height='16'>
	    				<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-right: 5px'>
	      		  	<div align='right'><a title='Clique para enviar um email para o cliente' href='mailto:$dados_rec[email]'>$dados_rec[email]</a></div>
	    				</td>
	    	  	</tr>	
	    		</table>
				";
        }
				
				//Caso for visualizar em listagem
  		  if ($modo_vis == 2) { 
			  echo "
		      <table border='0' cellpadding='0' cellspacing='0' width='100%' class='listView'>
	          <tr>
	        	  <td align='left' class='listViewPaginationTdS1'><font size='2'><span style='color: #990000'><b>[$dados_rec[id]]</b></span> - <a title='Clique para exibir os detalhes deste cliente' href='#' onClick=\"wdCarregarFormulario('ClienteExibe.php?ClienteId=$dados_rec[id]','conteudo')\">$dados_rec[nome]</font></a></td>
    		      <td align='center' width='50' class='listViewPaginationTdS1' valign='middle'><div align='right'>$conta_figura &nbsp;</div></td>
	       		</tr>
	    	  </table>
			   ";
       }
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
