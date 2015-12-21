<?php
###########
## Módulo para montagem do relatório de Detalhamento da locação
## Criado: 30/08/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Acesso as rotinas do PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conexão com o servidor
include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "../include/ManipulaDatas.php";

//Recupera os valores para filtragem
$LocacaoId = $_GET["LocacaoId"];
$EmpresaId = $_GET["EmpresaId"];
$Iv = $_GET["Iv"];
$Id = $_GET["Id"];

//Recupera dos dados do evento
$sql_locacao = "SELECT 
							loc.id,
							loc.data,
							loc.tipo_pessoa,
							loc.pessoa_id,
							loc.descricao,
							loc.situacao,
							loc.devolucao_prevista,
							loc.devolucao_realizada,
							loc.recebido_por,
							loc.observacoes,
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
							WHERE loc.id = '$LocacaoId'";

//Executa a query de consulta
$query_locacao = mysql_query($sql_locacao);

//Monta a matriz com os dados
$dados_locacao = mysql_fetch_array($query_locacao);

//Efetua o switch para o campo de status
switch ($dados_locacao[situacao]) {
  case 1: $desc_status = "Em aberto"; break;
	case 2: $desc_status = "Devolvido"; break;
}    

//Efetua o switch para o campo de pessoa
switch ($dados_locacao[tipo_pessoa]) {
  //Se for cliente
	case 1: 
		$pessoa_tipo = "Cliente";
		$pessoa_nome = $dados_locacao[cliente_nome]; 
	break;
	//Se for fornecedor
	case 2: 
		$pessoa_tipo = "Fornecedor"; 
		$pessoa_nome = $dados_locacao[fornecedor_nome];
	break;
	//Se for colaborador
	case 3: 
		$pessoa_tipo = "Colaborador"; 
		$pessoa_nome = $dados_locacao[colaborador_nome];							
	break;
} 

//Chama a classe para gerar o PDF
class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{    
	//Recupera o nome da empresa
	$empresaNome = $_GET["EmpresaNome"];
  //Ajusta a fonte
  $this->SetFont("Arial","",9);
	$this->Cell(0,4, date("d/m/Y", mktime()),0,0,"R");
	$this->Ln();
	$this->Cell(0,4, "Pagina: ".$this->PageNo(),0,0,"R");
	$this->Ln();	    
	$this->SetFont("Arial","B",15);
  $this->Cell(0,6,"Detalhamento da Locação",0,0,"R");
  $this->SetFont("Arial","",9);
  
  //Imprime o logotipo da empresa
	$this->Image("../image/logo_consoli2.jpg",10,10,46);
	//$this->Image("../image/logo_consoli2.jpg",10,10,59);
	$this->SetY(25);
	$this->SetFont("Arial", "B", 10);
	$this->SetFillColor(178,178,178);
	
  //Line break
  $this->Ln(14);
}

//Rodapé do Relatório
function Footer()
{
   $usuarioNome = $_GET["UsuarioNome"];
   //Posiciona a 1.5 cm do final
   $this->SetY(-15);    
   //Arial italico 8
   $this->SetFont("Arial","I",7);
   $this->Line(10,281,200,281);
   $this->Cell(0,3,"Emitido por: " . $usuarioNome);
}
}

//Instancia a classe gerador de pdf
$pdf=new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator("work | eventos - CopyRight(c) - Desenvolvido por Maycon Edinger - edinger@bol.com.br");
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle("Detalhamento da Locação");
$pdf->SetSubject("Relatório gerado automaticamente pelo sistema");
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();

//Nova linha
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "Descrição da Locação",1,0,"C",1);
$pdf->SetFont("Arial", "BI", 9);
$pdf->ln();
$pdf->Cell(0,6, $dados_locacao["descricao"],1);

//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, "Data:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(35);
$pdf->Cell(0,6, DataMySQLRetornar($dados_locacao["data"]),0,0,"L");

//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, $pessoa_tipo . ":",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(35);
$pdf->Cell(0,6, $pessoa_nome,0,0,"L");

//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(60,6, "Dev. Prevista:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(35);
$pdf->Cell(0,6, DataMySQLRetornar($dados_locacao["devolucao_prevista"]),0,0,"L");
$pdf->SetX(70);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(60,6, "Situação:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(88);
$pdf->Cell(0,6, $desc_status,0,0,"L");
$pdf->SetX(130);
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(70,6, "Dev. Realizada:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(156);
$pdf->Cell(0,6, DataMySQLRetornar($dados_locacao["devolucao_realizada"]),0,0,"L");
$pdf->ln();
$pdf->SetFont("Arial", "B", 9);
$pdf->Cell(0,6, "Recebido Por:",1,0,"L");
$pdf->SetFont("Arial", "I", 9);
$pdf->SetX(35);
$pdf->Cell(0,6, $dados_locacao["recebido_por"],0,0,"L");

//Nova linha
$pdf->ln();
$pdf->SetFont("Arial", "B", 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, "Informações Complementares",1,0,"C",1);
$pdf->ln();
$pdf->SetFont("Arial", "I", 10);
$pdf->MultiCell(0,4, $dados_locacao["observacoes"],1);

//Verifica se deve imprimir os dados financeiros do evento
if ($Iv == 0) {

	//*** Exibe os itens do evento
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Itens da Locação",1,0,"C",1);
	$pdf->ln();
	
	//Efetua a pesquisa dos itens da base de dados geral
	//Monta a query de filtragem dos itens	
	$filtra_item = "SELECT 
								id,
								nome
								FROM item_evento 
								WHERE tipo_produto = 1
								AND exibir_evento = 1
								AND ativo = 1 
								AND empresa_id = $EmpresaId
								ORDER BY nome";
	
	//Executa a query
	$lista_item = mysql_query($filtra_item);
	
	//Cria um contador com o número de contar que a query retornou
	$registros = mysql_num_rows($lista_item);
	
	//Verifica a quantidade de registros
	if ($registros == 0 ) {
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Não há itens cadastrados para esta locação !",1,0,"L");
	
	//Caso tiver itens no banco
	} else {
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont("Arial", "B", 9);		

		$pdf->SetX(10);
		$pdf->Cell(0,6, "     Descrição do Item:","1");
		$pdf->SetX(106);
		$pdf->Cell(0,6, "Observações:");
		
		//Monta a query para capturar as categorias que existem cadastrados itens
		$sql_categoria = mysql_query("SELECT 
												ite.id,
												ite.categoria_id,											
												cat.nome as categoria_nome,
												loc.valor_venda
												FROM item_evento ite
												LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
												INNER JOIN locacao_item loc ON loc.item_id = ite.id
												WHERE loc.locacao_id = '$LocacaoId'											
												GROUP BY cat.nome
												ORDER BY cat.nome");
	
		
		//Percorre o array das funcoes
		while ($dados_categoria = mysql_fetch_array($sql_categoria)){
	
			//Imprime o nome da categoria
			$pdf->ln(8);
			$pdf->SetFont("Arial","B",10);
			
			//Verifica se a categoria não tem o id 0 (devido as versões antigas do sistema)
			if ($dados_categoria["categoria_id"] == 0) {
				$nome_categoria = "Sem categoria definida";
			} else {
				$nome_categoria = $dados_categoria["categoria_nome"];
			}
			$pdf->Cell(6,4, $nome_categoria);		
	
			//Monta a query de filtragem dos itens
			$filtra_item = "SELECT 
												ite.id,
												ite.nome,
												ite.unidade,											
												cat.nome as categoria_nome,
												loc.quantidade,
												loc.valor_venda,
												loc.observacoes
												FROM item_evento ite
												LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
												INNER JOIN locacao_item loc ON loc.item_id = ite.id
												WHERE loc.locacao_id = '$LocacaoId'
												AND ite.categoria_id = '$dados_categoria[categoria_id]'
												ORDER BY cat.nome, ite.nome";
			
			//Executa a query
			$lista_item = mysql_query($filtra_item);
			 
			//Cria um contador com o número de contar que a query retornou
			$nro_item = mysql_num_rows($lista_item);	
			
			//Zera a variável de total do custo
			$valor_categoria = 0;
			
			//Percorre o array 
			while ($dados_item = mysql_fetch_array($lista_item)){
			
				//Define a variável do valor total do item
				$total_item = $dados_item[quantidade] * $dados_item[valor_venda];
	
		  	//Imprime os dados dos contatos
			  $pdf->ln();	  
				$pdf->SetX(15);
				$pdf->SetFont("Arial","",9);
			  $pdf->Cell(100,4, $dados_item["nome"]);
			  $pdf->SetX(106);
				$pdf->SetFont("Arial","",8);
			  $pdf->Cell(0,4, $dados_item["observacoes"]);
	
				//Incrementa o contador do valor
				$valor_categoria = $valor_categoria + $total_item;
			
			//Fecha o while de itens
			}
		
		//Fecha o while das categorias
		}
		
	//Fecha o if se deve imprimir os valores financeiros
	}

//Fecha o if se NÃO deve imprimir os detalhes financeiros
} else {
	
	//*** Exibe os itens do evento
	$pdf->ln();
	$pdf->SetFont("Arial", "B", 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, "Itens da Locação",1,0,"C",1);
	$pdf->ln();
	
	//Efetua a pesquisa dos itens da base de dados geral
	//Monta a query de filtragem dos itens
	$filtra_item = "SELECT 
								id,
								nome
								FROM item_evento 
								WHERE tipo_produto = '1'
								AND exibir_evento = '1'
								AND ativo = '1' 
								AND empresa_id = $EmpresaId
								ORDER BY nome";
	
	//Executa a query
	$lista_item = mysql_query($filtra_item);
	
	//Cria um contador com o número de contar que a query retornou
	$registros = mysql_num_rows($lista_item);
	
	//Verifica a quantidade de registros
	if ($registros == 0 ) {
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "Não há itens cadastrados para esta locação !",1,0,"L");
	
	//Caso tiver itens no banco
	} else {
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(0,6, "    Quant",1,0,"L");
		$pdf->SetX(28);
		$pdf->Cell(0,6, "Un");
		$pdf->SetX(34);
		$pdf->Cell(0,6, "Descrição dos Itens");
		$pdf->SetX(110);
		$pdf->Cell(15,6, "Custo","0","0","R");		
		$pdf->SetX(126);
		$pdf->Cell(0,6, "Observações");
		
		//Monta a query para capturar as categorias que existem cadastrados itens
		$sql_categoria = mysql_query("SELECT 
												ite.id,
												ite.categoria_id,											
												cat.nome as categoria_nome,
												loc.valor_venda
												FROM item_evento ite
												LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
												INNER JOIN locacao_item loc ON loc.item_id = ite.id
												WHERE loc.locacao_id = $LocacaoId											
												GROUP BY cat.nome
												ORDER BY cat.nome");
	
		
		//Percorre o array das funcoes
		while ($dados_categoria = mysql_fetch_array($sql_categoria)){
			
			//Imprime o nome da categoria
			$pdf->ln(8);
			$pdf->SetFont("Arial","B",10);
			
			//Verifica se a categoria não tem o id 0 (devido as versões antigas do sistema)
			if ($dados_categoria["categoria_id"] == 0) {
				$nome_categoria = "Sem categoria definida";
			} else {
				$nome_categoria = $dados_categoria["categoria_nome"];
			}
			$pdf->Cell(6,4, $nome_categoria);		
	
			//Monta a query de filtragem dos itens
			$filtra_item = "SELECT 
												ite.id,
												ite.nome,
												ite.unidade,											
												cat.nome as categoria_nome,
												loc.quantidade,
												loc.valor_venda,
												loc.observacoes
												FROM item_evento ite
												LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
												INNER JOIN locacao_item loc ON loc.item_id = ite.id
												WHERE loc.locacao_id = '$LocacaoId'
												AND ite.categoria_id = '$dados_categoria[categoria_id]'
												ORDER BY cat.nome, ite.nome";
			
			//Executa a query
			$lista_item = mysql_query($filtra_item);
			 
			//Cria um contador com o número de contar que a query retornou
			$nro_item = mysql_num_rows($lista_item);	
			
			//Zera a variável de total do custo
			$valor_categoria = 0;			
			
			//Percorre o array 
			while ($dados_item = mysql_fetch_array($lista_item)){
								
				//Define a variável do valor total do item
				$total_item = $dados_item[quantidade] * $dados_item[valor_venda];
			
		  	//Imprime os dados dos itens
			  $pdf->ln();
			  $pdf->SetFont("Arial","B",9);
				$pdf->SetX(15);
				$pdf->Cell(13,4, $dados_item["quantidade"],"0","0","R");
			  $pdf->SetX(28);
				$pdf->SetFont("Arial","",8);
			  $pdf->Cell(70,4, $dados_item["unidade"]);		  
				$pdf->SetX(34);
				$pdf->SetFont("Arial","",9);
			  $pdf->Cell(100,4, $dados_item["nome"]);
			  $pdf->SetX(110);
				$pdf->SetFont("Arial","",9);
			  $pdf->Cell(16,4, number_format($total_item, 2, ",", "."),"0","0","R");		  
			  $pdf->SetX(126);
				$pdf->SetFont("Arial","",8);
			  $pdf->Cell(0,4, $dados_item["observacoes"]);
		
				//Incrementa o contador do valor
				$valor_categoria = $valor_categoria + $total_item;
						
			//Fecha o while dos itens da categoria
			}	

		//Imprime o total de itens da categoria
		$pdf->ln();
	  $pdf->SetFont("Arial","I",9);
		$pdf->SetX(66);
		$pdf->Cell(60,4, "Subtotal da categoria: R$ " . number_format($valor_categoria, 2, ",", "."),"T","0","R");		
		
		//Incrmenta o valor do total dos itens ao total geral
		$valor_geral = $valor_geral + $valor_categoria;
	
	//Fecha o while das categorias
	}
	
	//Imprime o total de itens do evento
	$pdf->ln(8);
	$pdf->SetFont("Arial","BI",11);
	$pdf->Cell(0,6, "Total Geral da Locação: R$ " . number_format($valor_geral, 2, ",", "."),"1");
	
	
	//Imprime o campo para assinaturas
	$pdf->ln(9);
	$pdf->SetFillColor(178,178,178);
	$pdf->SetFont("Arial", "B", 10);
	$pdf->Cell(100,6, "Assinaturas",1,0,"C",1);
	$pdf->ln();
	$pdf->SetFont("Arial","BI",10);
	$pdf->Cell(40,6, "Auxiliar de Depósito: ","1");
	$pdf->Cell(60,6, "","1");
	$pdf->ln();
	$pdf->Cell(40,6, "Entregue por: ","1");
	$pdf->Cell(60,6, "","1");
	$pdf->ln();
	
	//Verifica se deve imprimir a declaração de devolução
	if ($Id == 1){
		
		$pdf->ln();
		$pdf->SetFont("Arial","BI",14);	
		$pdf->Multicell(0,6, "Declaro estar ciente dos materiais locados e comprometo-me a devolvê-los, prezando a qualidade dos mesmos.");
		$pdf->SetFont("Arial","BI",10);
		$pdf->ln();
	
	}
	
	$pdf->Cell(40,6, "Cliente: ","1");
	$pdf->Cell(60,6, "","1");
	$pdf->ln();
	
	//Fecha o if de se tiver itens
	}		
	
//Fecha o if se deve imprimir os detalhes financeiros	
}

//Gera o PDF
$pdf->Output();

?>                               
                       