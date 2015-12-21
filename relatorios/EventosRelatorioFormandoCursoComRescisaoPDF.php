<?php
###########
## Módulo para montagem do relatório formandos por evento e curso
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
		$this->Ln();	    
		$this->SetFont('Arial','',10);
		$this->Cell(0,4,'Consoli Eventos Ltda',0,0,'L');
		$this->SetFont('Arial','',9);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();	    
		$this->SetFont('Arial','',10);
		$this->Cell(0,4,'Relação de Formandos por Evento e Curso',0,0,'L');
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');
		$this->Ln();	    
		$this->SetFont('Arial','B',10);
		$this->Cell(0,4,'Evento: ' . $dados_evento["nome"],0,0,'L');	  
	
		//Line break
		$this->Ln();
	
	}

}

//Instancia a classe gerador de pdf
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos - CopyRight(c) - Work Labs Tecnologia Ltda - www.worklabs.com.br');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Relação de Formandos por Evento e Curso');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();


//verifica os formandos já cadastrados para este evento
$sql_formando = mysql_query("SELECT 
															form.nome,
															form.curso_id,
															form.chk_culto,
															form.chk_colacao,
															form.chk_jantar,
															form.chk_baile,
															form.status,
															cur.nome AS curso_nome
														FROM 
															eventos_formando form
														LEFT OUTER JOIN 
															cursos cur ON cur.id = form.curso_id
														WHERE 
															form.evento_id = '$EventoId'
														ORDER BY 
															curso_nome, form.nome");

//Verifica o numero de registros retornados
$registros = mysql_num_rows($sql_formando);

//Verifica a quantidade de registros
if ($registros == 0 ) 
{
	
	//Exibe a mensagem que não foram encontrados registros
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(0,6, 'Não há formandos cadastrados para este evento !',1,0,'L');

//Caso tiver
} 

else 

{
	
	$curso = 'a';
	$total_curso = 0;
	$total_formando = 0;
	
	//Percorre o array 
	while ($dados_formando = mysql_fetch_array($sql_formando))
	{

		//Efetua o switch para o campo de status
		switch ($dados_formando["status"]) 
		{
			case 1: $desc_status = "A se formar"; break;
			case 2: $desc_status = "Formado"; break;
			case 3: $desc_status = "Desistente"; break;
			case 4: $desc_status = "Aguar. Resc."; break;
		}
		
		$desc_participante = "";
							
		if ($dados_formando["chk_culto"] == 1)
		{
		
			$desc_participante .= "MISSA ";
			
			if ($dados_formando["status"] < 3) $total_culto++; 
			
		}
		
		if ($dados_formando["chk_colacao"] == 1)
		{
		
			$desc_participante .= "COLACAO ";
			if ($dados_formando["status"] < 3) $total_colacao++; 
			
		}
		
		if ($dados_formando["chk_jantar"] == 1)
		{
		
			$desc_participante .= "JANTAR ";
			if ($dados_formando["status"] < 3) $total_jantar++; 
			
		}
		
		if ($dados_formando["chk_baile"] == 1)
		{
		
			$desc_participante .= "BAILE";
			if ($dados_formando["status"] < 3) $total_baile++; 
			
		}
		
		if ($curso != $dados_formando["curso_nome"])
		{
		
			if ($curso != 'a')
			{
			
				//Imprime o total de formandos do curso
				$pdf->ln();
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(0,5, "Total de Formandos no Curso: " . $total_curso, "B");
				
			}
			
			$total_curso = 0;
			
			//Imprime os dados do formando	  
			$pdf->ln(10);
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,5, "Curso: [" . $dados_formando['curso_id'] . '] - ' . $dados_formando['curso_nome'], "B");
		
		}	
		
		//Imprime os dados do formando	  
		$pdf->ln();
		$pdf->SetFont('Arial','',10);
		$pdf->SetX(10);
		$pdf->Cell(30,5, '(' . $desc_status . ')');	
		$pdf->Cell(0,5, $dados_formando['nome'] . ' (' . $desc_participante . ')');	
		
		$curso = $dados_formando["curso_nome"];
		
		$total_formando++;
		$total_curso++;
		
	}
	
	//Imprime o total de formandos do curso
	$pdf->ln();
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(0,5, "Total de Formandos no Curso: " . $total_curso, "B");
				
	$pdf->ln(10);
	$pdf->Cell(00,6, "Total de Formandos no evento: " . $registros, "B",0,'L');
	
//Fecha o If
}

$pdf->ln();	

//Gera o PDF
$pdf->Output();

?>