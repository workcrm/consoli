<?php
###########
## Módulo para Relatório de Listagem das Contas
## Criado: - 15/03/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########
/**
* @package workcrm
* @abstract Módulo para Relatórios de Listagem das Contas
* @author Maycon Edinger
* @copyright 2007 - Work CRM
*/

//localhost/crm/relatorios/ContasRelatorioPDF.php?UsuarioNome=Maycon Edinger&EmpresaId=1&EmpresaNome=Empresa Teste SA&usarBanco=base_crm

//Inclui os arquivos do relatório em PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conexão
include "../conexao/ConexaoMySQL.php";

//Recupera os valores para filtragem
$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$agrupar = $_GET["Agrupar"];
$filtrar = $_GET["Filtrar"];
$exibeInativos = $_GET["chkInativo"];

//Verifica se deve exibir os inativos
if ($exibeInativos == 0) {
	//Caso não incluir os inativos, criar uma filtragem extra pela variável
	$filtra_inativos = "AND con.ativo = '1'";
}

//Efetua o switch para montar as rotinas de agrupamento
switch ($agrupar) {
	//Caso não for agrupar
  case 0: 
		//Seta a variável para não imprimir e controlar as quebras
		$SemQuebra = 1;
		//Cria o agrupamento na query
		$Agrupamento = "ORDER BY nome";	
		//Filtra apenas pela empresa
		$Filtragem = "WHERE con.empresa_id = '$empresaId'";
	break;

	//Caso for agrupar por tipo de conta
	case 1: 
		//Seta a variável para não imprimir e controlar as quebras
		$SemQuebra = 2;
		//Cria o agrupamento na query
		$Agrupamento = "ORDER BY tipo_conta, nome";	
		//Verifica se precisa ou não efetuar filtragem
		if ($filtrar == "todos") {
			//Caso não efetuar filtragem, exibe todos os tipos de conta
			$Filtragem = "WHERE con.empresa_id = '$empresaId'";
		} else {		
			//Senão exibe somente o tipo de conta selecionado
			$Filtragem = "WHERE con.tipo_conta = $filtrar AND con.empresa_id = '$empresaId'";
		}
		//Define o campo a efetuar o agrupamento da quebra
		$Campo_pesquisa = "tipo_conta";
		//Define o nome do agrupamento
		$Agrupamento_nome = "Tipo de Conta: ";
	break;

	//Caso por agrupar por rating
	case 2: 
		//Seta a variável para não imprimir e controlar as quebras
		$SemQuebra = 2;
		//Cria o agrupamento na query
		$Agrupamento = "ORDER BY rating, nome"; 
		//Verifica se precisa ou não efetuar filtragem
		if ($filtrar == "todos") {
			//Caso não efetuar filtragem, exibe todos os ratings
			$Filtragem = "WHERE con.empresa_id = '$empresaId'";
		} else {		
			//Senão exibe somente o rating selecionado
			$Filtragem = "WHERE con.rating = $filtrar AND con.empresa_id = '$empresaId'";
		}
		//Define o campo a pesquisar
		$Campo_pesquisa = "rating";			
		//Define o nome do agrupamento
		$Agrupamento_nome = "Rating: ";
	break;

	//Caso for agrupar por Area de Interesse
	case 3: 
		//Seta a variável para não imprimir e controlar as quebras
		$SemQuebra = 2;
		//Cria o agrupamento na query
		$Agrupamento = "ORDER BY area_interesse_nome, nome"; 
		//Verifica se precisa ou não efetuar filtragem
		if ($filtrar == "todos") {
			//Caso não efetuar filtragem, exibe todos os ramos de atividade
			$Filtragem = "WHERE con.empresa_id = '$empresaId'";
		} else {		
			//Senão exibe somente o tipo de conta selecionado
			$Filtragem = "WHERE con.area_interesse_id = $filtrar AND con.empresa_id = '$empresaId'";
		}
		//Define o campo a efetuar a pesquisa da quebra
		$Campo_pesquisa = "area_interesse_nome";
		//Define o nome do agrupamento
		$Agrupamento_nome = "Área de Interesse: ";
	break;

	//Caso for agrupar por Ramo de Atividade
	case 4: 
		//Seta a variável para não imprimir e controlar as quebras
		$SemQuebra = 2;
		//Cria o agrupamento na query
		$Agrupamento = "ORDER BY ramo_atividade_nome, nome"; 
		//Verifica se precisa ou não efetuar filtragem
		if ($filtrar == "todos") {
			//Caso não efetuar filtragem, exibe todos os ramos de atividade
			$Filtragem = "WHERE con.empresa_id = '$empresaId'";
		} else {
			//Caso for filtrar, cria o where na query
			$Filtragem = "WHERE con.ramo_atividade_id = $filtrar AND con.empresa_id = '$empresaId'";
		}
		//Define o campo a efetuar a pesquisa da quebra
		$Campo_pesquisa = "ramo_atividade_nome";
		//Define o nome do agrupamento
		$Agrupamento_nome = "Ramo de Atividade: ";
	break;

	//Caso for agrupar por Origem de Conta
	case 5: 
		//Seta a variável para não imprimir e controlar as quebras
		$SemQuebra = 2;
		//Cria o agrupamento na query
		$Agrupamento = "ORDER BY origem_nome, nome"; 
		//Verifica se precisa ou não efetuar filtragem
		if ($filtrar == "todos") {
			//Caso não efetuar filtragem, exibe todos os ramos de atividade
			$Filtragem = "WHERE con.empresa_id = '$empresaId'";
		} else {
			//Caso for filtrar, cria o where na query
			$Filtragem = "WHERE con.origem_id = $filtrar AND con.empresa_id = '$empresaId'";
		}			
		//Define o campo a efetuar a pesquisa da quebra
		$Campo_pesquisa = "origem_nome";	
		//Define o nome do agrupamento
		$Agrupamento_nome = "Origem de Conta: ";
	break;
}
  
//Executa a Query de seleção das contas
$sql = mysql_query("SELECT 
						  		  con.id,
								  	con.ativo,
								  	con.empresa_id,
								  	con.nome,
								  	con.rating,
								  	con.tipo_conta,
								  	con.tipo_pessoa,
								  	con.cidade,
								  	con.uf,
								  	con.telefone,
								  	con.email_geral,
								  	con.area_interesse_id,
								  	con.ramo_atividade_id,
								  	con.origem_id,		  
								  	are.nome as area_interesse_nome,
								  	ati.nome as ramo_atividade_nome,
								  	ori.nome as origem_nome
						
								  	FROM contas con
						  		  INNER JOIN area_interesse are ON con.area_interesse_id = are.id 
						  		  INNER JOIN ramo_atividade ati ON con.ramo_atividade_id = ati.id 
						  		  INNER JOIN origem ori ON con.origem_id = ori.id 
								  	$Filtragem $filtra_inativos
								  	$Agrupamento");

//Verifica o numero de registros
$registros = mysql_num_rows($sql); 

//Caso for imprimir sem quebra (imprimir todas as contas)
if ($SemQuebra != 1) {

	//Verifica se o tipo de agrupamento for por tipo de conta
	if ($agrupar == 1) {
		//Monta e executa a query só com o valor e a primeira linha do campo a efetuar a quebra
		$sql_quebra = mysql_query("SELECT $Campo_pesquisa FROM contas con $Filtragem $Agrupamento LIMIT 0,1");
	}
	//Verifica se o tipo de agrupamento for por rating
	if ($agrupar == 2) {
		//Monta e executa a query só com o valor e a primeira linha do campo a efetuar a quebra
		$sql_quebra = mysql_query("SELECT con.rating FROM contas con $Filtragem $Agrupamento LIMIT 0,1");
	}
	//Verifica se o tipo de agrupamento for por area de interesse
	if ($agrupar == 3) {
		//Monta e executa a query só com o valor e a primeira linha do campo a efetuar a quebra
		$sql_quebra = mysql_query("SELECT con.area_interesse_id, are.nome as area_interesse_nome FROM contas con INNER JOIN area_interesse are ON con.area_interesse_id = are.id $Filtragem ORDER BY are.nome LIMIT 0,1");
	}
	
	//Verifica se o tipo de agrupamento for por ramo de atividade
	if ($agrupar == 4) {
		//Monta e executa a query só com o valor e a primeira linha do campo a efetuar a quebra
		$sql_quebra = mysql_query("SELECT con.ramo_atividade_id, ati.nome as ramo_atividade_nome FROM contas con INNER JOIN ramo_atividade ati ON con.ramo_atividade_id = ati.id $Filtragem ORDER BY ati.nome LIMIT 0,1");
	}
	
	//Verifica se o tipo de agrupamento for por origem de conta
	if ($agrupar == 5) {
		//Monta e executa a query só com o valor e a primeira linha do campo a efetuar a quebra
		$sql_quebra = mysql_query("SELECT con.origem_id, ori.nome as origem_nome FROM contas con INNER JOIN origem ori ON con.origem_id = ori.id $Filtragem ORDER BY ori.nome LIMIT 0,1");
	}
	
	//Monta o array com os dados da quebra
	$dados_quebra = mysql_fetch_array($sql_quebra); 
	
	//Pega o valor do campo para comparação de quebra
	$valor_quebra = $dados_quebra["$Campo_pesquisa"];
	
	//Monta o switch para exibir o cabeçalho da quebra
	if ($agrupar == 1) {
		//Efetua o switch para o campo de tipo de conta
		switch ($dados_quebra["$Campo_pesquisa"]) {
			case 1: $Nome_quebra = "Prospect"; break;
			case 2: $Nome_quebra = "Cliente"; break;
		}
	}
	if ($agrupar == 2) {
		//Efetua o switch para o campo de rating
		switch ($dados_quebra["$Campo_pesquisa"]) {
		  case 1: $Nome_quebra = "Rating A"; break;
		  case 2: $Nome_quebra = "Rating B"; break;
			case 3: $Nome_quebra = "Rating C"; break;
		  case 4: $Nome_quebra = "Rating D"; break;
		}	
	}
	if ($agrupar == 3) {
		$Nome_quebra = $dados_quebra["area_interesse_nome"];
	}
	if ($agrupar == 4) {
		$Nome_quebra = $dados_quebra["ramo_atividade_nome"];
	}
	if ($agrupar == 5) {
		$Nome_quebra = $dados_quebra["origem_nome"];
	}
	//Monta a variável do número do registro com valor zero
	$numero_registro = 0;
}
//Extende a classe em pdf
class PDF extends FPDF {
	//Cabeçalho do relatório
	function Header() {	    
		$empresaNome = $_GET["EmpresaNome"];
	  //Ajusta a fonte
	  $this->SetFont('Arial','',9);
	  //Titulo do relatório
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->SetFont('Arial','B',15);
	  $this->Cell(0,6,'Relação Geral de Contas');
	  $this->SetFont('Arial','',9);
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
	  //Line break
	  $this->Ln(10);
	  //Títulos das colunas
		$this->SetFont('Arial', 'B', 10);
		//Define a cor RGB do fundo da celula
		$this->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$this->Cell(0,6, '',1,0,'L',1);
		$this->SetX(12);
		$this->Cell(0,6, 'Nome/Razão Social:');
		$this->SetX(105);
		$this->Cell(0,6, 'Tipo:');
		$this->SetX(120);
		$this->Cell(0,6, 'Rating:');
		$this->SetX(135);
		$this->Cell(0,6, 'Cidade/UF:');
		$this->SetX(192);
		$this->Cell(0,6, 'Telefone:');
		$this->SetX(216);
		$this->Cell(0,6, 'Email Geral:');
		$this->SetX(275);
		$this->Cell(0,6, 'Ativo:');
	}
	
	//Rodapé do Relatório
	function Footer()	{
	   $usuarioNome = $_GET["UsuarioNome"];
	   //Position at 1.5 cm from bottom
	   $this->SetY(-15);    
	   //Arial italic 8
	   $this->SetFont('Arial','I',7);
	   //Page number
	   $this->Line(10,281,200,281);
	   $this->Cell(0,3,'Emitido por: ' . $usuarioNome);
	   //Logotipo
	   $this->Image('../image/workcrm_powered.jpg',175,282,24);
	}
//Fim do if de imprimir com quebra
}

//Instancia a classe gerador de pdf
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | crm - www.workcrm.com.br');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Relação de Contas');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a primeira página no modo landscape
$pdf->AddPage(L);

//Verifica se precisa imprimir o cabeçalho da quebra
if ($SemQuebra != 1) {
	//Imprime o cabeçalho da primeira quebra
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(0,5, $Agrupamento_nome . $Nome_quebra);     
	$pdf->SetFont('Arial','',10);
}

//Salta a primeira linha
$pdf->ln();

//Monta as linhas com os dados da query
$pdf->SetFont('Arial','',10);

//Percorre e monta o array com os dados
while ($dados = mysql_fetch_array($sql)){
	
	//Efetua o switch para o campo de tipo de conta
	switch ($dados["tipo_conta"]) {
		case 1: $tipo_conta_nome = "Prospect"; break;
		case 2: $tipo_conta_nome = "Cliente"; break;
	}	

	//Efetua o switch para o campo de rating
	switch ($dados["rating"]) {
	  case 1: $rating_nome = "Rating A"; break;
	  case 2: $rating_nome = "Rating B"; break;
		case 3: $rating_nome = "Rating C"; break;
	  case 4: $rating_nome = "Rating D"; break;
	}	

	//Efetua o switch para o campo de ativo
  switch ($dados["ativo"]) {
    case 0: $ativo = "Inativo";	break;
		case 1: $ativo = "Ativo";	break;     	
  }
	
	//Verifica se não é necessário efetuar a quebra
	if ($SemQuebra != 1) {
		if ($valor_quebra != $dados["$Campo_pesquisa"]) {
			//$pdf->ln();
			$pdf->SetFont('Arial','B', 8);
			$pdf->Cell(0,5,"Total de Registros listados: " . $numero_registro,'B');
			$pdf->SetFont('Arial','',10);
			$pdf->ln();
			//Zera o contador para a próxima quebra
			$numero_registro = 0;
			$pdf->SetFont('Arial','B',10);
			//Monta o cabeçalho das outras quebras com base no tipo de ordenação
			if ($agrupar == 1) {		
				$pdf->Cell(0,5, $Agrupamento_nome . $tipo_conta_nome);
			}
			if ($agrupar == 2) {
				$pdf->Cell(0,5, $Agrupamento_nome . $rating_nome);	
			}
			if ($agrupar == 3) {
				$pdf->Cell(0,5, $Agrupamento_nome . $dados["area_interesse_nome"]);		
			}
			if ($agrupar == 4) {
				$pdf->Cell(0,5, $Agrupamento_nome . $dados["ramo_atividade_nome"]);		
			}
			if ($agrupar == 5) {
				$pdf->Cell(0,5, $Agrupamento_nome . $dados["origem_nome"]);		
			}				
			$pdf->SetFont('Arial','',10);
			$pdf->ln();		
		}	
	}

	//Imprime os dados da conta
  //$pdf->Cell(0,5, $dados['id']);
  $pdf->SetX(12);
	$pdf->SetFont('Arial','B',10);
  $pdf->Cell(0,5, $dados['nome']);
	$pdf->SetFont('Arial','',9);  
	$pdf->SetX(105);
  $pdf->Cell(0,5, $tipo_conta_nome);
	$pdf->SetX(120);
  $pdf->Cell(0,5, $rating_nome);  
	$pdf->SetX(135);
  $pdf->Cell(0,5, $dados['cidade'] . "/" . $dados['uf']);  
	$pdf->SetX(192);
  $pdf->Cell(0,5, $dados['telefone']);  
	$pdf->SetX(216);
  $pdf->Cell(0,5, $dados['email_geral']);
	$pdf->SetX(276);
  $pdf->Cell(0,5, $ativo);
	$pdf->ln();
	
	//Incrementa o contador do número de registros
	$numero_registro ++;
	//Armazena o novo valor da variável que controla a quebra
	$valor_quebra = $dados["$Campo_pesquisa"];  		  
}

//Verifica se vai imprimir o rodapé dos registros da quebra
if ($SemQuebra != 1) {
	//Exibe o rodapé com a quantidade de registros da quebra
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(0,6, 'Total de registros listados: ' . $numero_registro,'');
	$pdf->ln();
}
//Imprime o total geral de registros listados
$pdf->Cell(0,5, 'Total Geral de Registros Listados: ' . $registros,'T');

//Gera o PDF
$pdf->Output();
?>