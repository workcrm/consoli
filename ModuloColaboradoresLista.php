<?php
###########
## Módulo para Exibição dos colaboradores pesquisados
## Criado: 20/04/2007 - Maycon Edinger
## Alterado: 28/05/2007 - Maycon Edinger
## Alterações: 
## 24/04/2007 - Colocado o campo telefone e celular mais visível nas consultas
## 28/05/2007 - Implementado o campo ClienteID para a tabela
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
  
	$tipo = $_GET["edtTipo"];
	$modo_vis = $_GET["edtVisualizar"];
	$pesquisa = $_GET["edtPesquisa"];
	
	//Efetua o switch para o campo de ativo
	switch ($tipo) {
	  case 0: $where_tipo = ""; break;
		case 1: $where_tipo = "AND con.tipo = 1 "; break;
		case 2: $where_tipo = "AND con.tipo = 2 "; break;
	}
	
	//Monta a query
	//$consulta = "SELECT * FROM colaboradores WHERE nome LIKE '$pesquisa%' AND empresa_id = $empresaId ORDER BY nome";
	
	$consulta =	"SELECT 
			  		  con.id,
					  	con.ativo,
					  	con.empresa_id,
					  	con.nome,
					  	con.tipo,
					  	con.endereco,
					  	con.complemento,
					  	con.bairro,
					  	con.cidade_id,
					  	con.uf,
					  	con.cep,
					  	con.rg,
					  	con.cpf,
					  	con.telefone,
					  	con.fax,
					  	con.celular,
					  	con.email,
					  	con.contato,
					  	con.observacoes,
					  	con.foto,
							cid.nome as cidade_nome,
							fun.nome as funcao_nome
				  
					  	FROM colaboradores con
					  	LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id
					  	LEFT OUTER JOIN funcoes fun ON fun.id = con.funcao_id
					  	WHERE con.nome LIKE '$pesquisa%' AND con.empresa_id = $empresaId 
							$where_tipo
							ORDER BY con.nome";
	

//Se não, o usuário quer pesquisar por inicial da letra
} else {	  
	
	$tipo = $_GET["edtTipo"];	  
	$filtragem = $_GET['ChaveFiltragem'];
  $modo_vis = $_GET['Modo_vis'];
  
  //Efetua o switch para o campo de ativo
	switch ($tipo) {
	  case 0: 
			$where_tipo = ""; 
			$desc_tipo = "";
		break;
		case 1: 
			$where_tipo = "AND con.tipo = 1 "; 
			$desc_tipo = "(<span style='color: #990000'>Somente Freelances</span>)";
		break;
		case 2: 
			$where_tipo = "AND con.tipo = 2 "; 
			$desc_tipo = "(<span style='color: #990000'>Somente Funcionários</span>)";
		break;
	}
  
	//Caso clicar no botáo para exibir todas os colaboradores
	if ($filtragem == "todos") {
		//Monta a query pesquisando por empresa
	  //$consulta = "SELECT * FROM colaboradores WHERE empresa_id = $empresaId ORDER BY nome";
	$consulta =	"SELECT 
			  		  con.id,
					  	con.ativo,
					  	con.empresa_id,
					  	con.nome,
					  	con.tipo,
					  	con.endereco,
					  	con.complemento,
					  	con.bairro,
					  	con.cidade_id,
					  	con.uf,
					  	con.cep,
					  	con.rg,
					  	con.cpf,
					  	con.telefone,
					  	con.fax,
					  	con.celular,
					  	con.email,
					  	con.contato,
					  	con.observacoes,
					  	con.foto,
							cid.nome as cidade_nome,
							fun.nome as funcao_nome
				  
					  	FROM colaboradores con
					  	LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id
					  	LEFT OUTER JOIN funcoes fun ON fun.id = con.funcao_id
					  	WHERE con.empresa_id = $empresaId 
							$where_tipo
							ORDER BY con.nome";

	} else {
		//Monta a query pesquisando por empresa e por inicial
	  //$consulta = "SELECT * FROM colaboradores WHERE nome LIKE '$filtragem%' AND empresa_id = $empresaId ORDER BY nome";		 
	
	$consulta =	"SELECT 
			  		  con.id,
					  	con.ativo,
					  	con.empresa_id,
					  	con.nome,
					  	con.tipo,
					  	con.endereco,
					  	con.complemento,
					  	con.bairro,
					  	con.cidade_id,
					  	con.uf,
					  	con.cep,
					  	con.rg,
					  	con.cpf,
					  	con.telefone,
					  	con.fax,
					  	con.celular,
					  	con.email,
					  	con.contato,
					  	con.observacoes,
					  	con.foto,
							cid.nome as cidade_nome,
							fun.nome as funcao_nome
				  
					  	FROM colaboradores con
					  	LEFT OUTER JOIN cidades cid ON cid.id = con.cidade_id
					  	LEFT OUTER JOIN funcoes fun ON fun.id = con.funcao_id
							WHERE con.nome LIKE '$filtragem%' AND con.empresa_id = $empresaId 
							$where_tipo
							ORDER BY con.nome";	

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
	$regs_pagina_rec = "15"; 
} else {
	//Determina a quantidade de registros por página para modo listagem
	$regs_pagina_rec = "30";
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
					<table width='500' cellpadding='0' cellspacing='0' border='0' class='listView'>
        		<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          		<td  scope='col'>&nbsp;&nbsp;Não foram encontrados colaboradores para a pesquisa !</td>
        		</tr>
        	</table>	
    			";
				} else {

				//Monta os botões de paginação
				$anterior_rec = $pc_rec -1;
				$proximo_rec = $pc_rec +1;

				//Exibe a tabela com os dados de controle de paginação
      	echo "
				<table width='500' cellpadding='0' cellspacing='0' border='0' class='listView'>
        	<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
          	<td width='20'>";
									
							//Verifica se necessita exibir o botão de anterior
							if ($pc_rec > 1) {				
				  			//Exibe o botão
							echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloColaboradoresLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>&nbsp;<img src='./image/bt_anterior.gif' alt='Exibe a Página Anterior' border='0' align='middle'></a></td><td width='50'><a href='#' onClick=\"wdCarregarFormulario('ModuloColaboradoresLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$anterior_rec','retornopesquisa','1')\" title='Exibe a Página Anterior'>Anterior</a></td>";
				  		}	 
				?>
				
				
						</td>
						<td align="center">
							<span class='pageNumbers'>(Exibindo Colaboradores: <?php echo $conta_inicial_rec . " - " . $conta_final_rec . " de " . $tot_regs_rec ?>) <?php echo $desc_tipo ?></span>
						</td>
						<td valign="middle" width="50" align="right">
				
				<?php 
				  //Verifica se necessita exibir o botão de próximo
				  if ($pc_rec < $tot_pags_rec) {				
				    //Exibe o botão
						echo "<a href='#' onClick=\"wdCarregarFormulario('ModuloColaboradoresLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'>Próximo&nbsp;</a></td><td width='20' align='right'><a href='#' onClick=\"wdCarregarFormulario('ModuloColaboradoresLista.php?edtPesquisa=$_GET[edtPesquisa]&edtVisualizar=$_GET[edtVisualizar]&ChaveFiltragem=$_GET[ChaveFiltragem]&Modo_vis=$_GET[Modo_vis]&PaginaRec=$proximo_rec','retornopesquisa','1')\" title='Exibe a próxima página'><img src='./image/bt_proximo.gif' alt='Exibe a Próxima Página' border='0' align='middle'>&nbsp;</a>";
				  }	 
				
					//Fecha a tabela com os dados de controle da paginação
					echo "</td></tr></table></br>";
				
					//Monta e percorre o array dos dados
		    	while ($dados_rec = mysql_fetch_array($limite_rec)){


				//Caso estiver visualizando em cartão de visita
        if ($modo_vis == 1) {
       	
       	//Verifica se há uma foto definida para o colaborador
        if ($dados_rec["foto"] != ""){
        	
        	$caminho_foto = "<img src='imagem_colaborador/$dados_rec[foto]' width='100' height='133' />";
        	
       	} else {
        	
					$caminho_foto = "Colaborador<br/>sem<br/>foto";
					
				}
				
				//Efetua o switch para o campo de ativo
				switch ($dados_rec["tipo"]) {
				  case 1: $desc_tipo = "FREELANCE"; break;
					case 2: $desc_tipo = "FUNCIONÁRIO"; break;
				}

				echo "
          <table width='500' cellpadding='0' cellspacing='0' border='0' class='listView'>
	  		    <tr height='20'>
        		  <td class='listViewThS1'>
								&nbsp;&nbsp;<font size='3'><span style='color: #990000'>[$dados_rec[id]]</span> - <a title='Clique para exibir os detalhes deste colaborador' href='#' onClick=\"wdCarregarFormulario('ColaboradorExibe.php?ColaboradorId=$dados_rec[id]','conteudo')\"><font size='3'>$dados_rec[nome]</font></a>
							</td>
							<td rowspan='7' width='100' style='border-left: 1px solid; padding-bottom: 0px' valign='middle' align='center'>
							  $caminho_foto
					  	</td>
			  		</tr>

			  		<tr height='14'>
        	    <td	valign='top' bgcolor='#fdfdfd' class='oddListRowS1'>
								<span class='oddListRowS1' style='padding: 0px'><strong>Tipo: <span style='color: #990000'>$desc_tipo</span></strong></span>
						  </td>
	  		  	</tr>
	  		  	<tr height='14'>
	  		  		<td	valign='top' bgcolor='#fdfdfd' class='oddListRowS1'>
	    	    		<span class='oddListRowS1' style='padding: 0px'><strong>Função: <span style='color: #990000'>$dados_rec[funcao_nome]</span></strong></span>
	    	    	</td>
	    	  	</tr>						
						<tr height='14'>
        	    <td	valign='top' bgcolor='#fdfdfd' class='oddListRowS1'>
								<span class='oddListRowS1' style='padding: 0px'><strong>Fone: $dados_rec[telefone] - Celular: $dados_rec[celular]</strong></span>
						  </td>
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
	    				<td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='padding-right: 5px'>
	      		  	<div align='right'><a title='Clique para enviar um email para o colaborador' href='mailto:$dados_rec[email]'>$dados_rec[email]</a></div>
	    				</td>
	    	  	</tr>	
	    		</table>
				";
        }
				
				//Caso for visualizar em listagem
  		  if ($modo_vis == 2) { 
			  echo "
		      <table border='0' cellpadding='0' cellspacing='0' width='500' class='listView'>
	          <tr>
	        	  <td align='left' class='listViewPaginationTdS1'><a title='Clique para exibir os detalhes deste colaborador' href='#' onClick=\"wdCarregarFormulario('ColaboradorExibe.php?ColaboradorId=$dados_rec[id]','conteudo')\"><font size='2'>$dados_rec[nome]</font></a></td>
    		      <td nowrap align='center' class='listViewPaginationTdS1' valign='middle'><div align='right'>$conta_figura &nbsp;</div></td>
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
