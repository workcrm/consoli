<?php
###########
## Módulo para montagem do relatório endereçamento dos formandos
## Criado: 18/11/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
##
###########

//Acesso as rotinas do PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conexão com o servidor
include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "../include/ManipulaDatas.php";

//Recupera os valores para filtragem
$EventoId = $_GET["EventoId"]; 

//Chama a classe para gerar o PDF
class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{    
	
	//Recupera os valores para filtragem
	$EventoId = $_GET["EventoId"];
	
	//Recupera dos dados do evento
	$sql_evento = "SELECT nome FROM eventos WHERE id = '$EventoId'"; 							
	
	//Executa a query de consulta
	$query_evento = mysql_query($sql_evento);
	
	//Monta a matriz com os dados
	$dados_evento = mysql_fetch_array($query_evento); 

	//Recupera o nome da empresa
	$empresaNome = $_GET["EmpresaNome"];
  //Ajusta a fonte
  $this->SetFont('Arial','',9);
	$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
	$this->Ln();
	$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');
	$this->Ln();	    
	$this->SetFont('Arial','',10);
  $this->Cell(0,4,'Relação de Edereços dos Formandos do Evento',0,0,'L');
	$this->Ln();	    
	$this->SetFont('Arial','B',10);
  $this->Cell(0,4,'Evento: ' . $dados_evento["nome"],0,0,'L');	  
	
  //Line break
  $this->Ln(6);
}

}

//Instancia a classe gerador de pdf
$pdf=new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos - CopyRight(c) - Desenvolvido por Maycon Edinger - edinger@bol.com.br');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Relação de Endereços dos Formandos Evento');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();


//verifica os formandos já cadastrados para este evento
$sql_formando = mysql_query("SELECT 
														form.nome,
														form.cpf,
														form.endereco,
														form.complemento,
														form.bairro,
														form.uf,
														form.cep,
														form.observacoes,
														cid.nome as cidade_nome
														FROM eventos_formando form
														LEFT OUTER JOIN cidades cid ON cid.id = form.cidade_id
														WHERE form.evento_id = '$EventoId' ORDER by form.nome");

//Verifica o numero de registros retornados
$registros = mysql_num_rows($sql_formando);

//Verifica a quantidade de registros
if ($registros == 0 ) {
	//Exibe a mensagem que não foram encontrados registros
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(0,6, 'Não há formandos cadastrados para este evento !',1,0,'L');

//Caso tiver
} else {
	
	//Percorre o array 
	while ($dados_formando = mysql_fetch_array($sql_formando)){

		$total_formando = 0;
		
	  //Imprime os dados do formando	  
	  $pdf->SetFont('Arial','B',9);
		$pdf->MultiCell(90,4, $dados_formando['nome'] . "\nCPF: $dados_formando[cpf]\n\nEndereço: $dados_formando[endereco]\n$dados_formando[complemento]\n$dados_formando[bairro]\n$dados_formando[cidade_nome] - $dados_formando[uf] - CEP: $dados_formando[cep]\n$dados_formando[observacoes]",1);
		$pdf->ln(7);
		
		
		}
		
		$pdf->Cell(90,6, "Total de formandos no evento: " . $registros,1,0,'L');
	
//Fecha o If
}


$pdf->ln();	

//Gera o PDF
$pdf->Output();

?>                                                      