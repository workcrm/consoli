<?php
###########
## Módulo para montagem do relatório de compras do Foto e Vídeo 
## Criado: 20/06/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
##
###########

//Acesso as rotinas do PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conexão com o servidor
include '../conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include '../include/ManipulaDatas.php';

//Recupera os valores para filtragem
$EventoId = $_GET['EventoId'];
$EmpresaId = $_GET['EmpresaId'];
$TipoRel = $_GET["TipoRel"];
$OrigemRel = $_GET["OrigemRel"];

$DataIni = DataMySQLInserir($_GET['DataIni']);
$DataFim = DataMySQLInserir($_GET['DataFim']);

$DataIniFormata = $_GET['DataIni'];
$DataFimFormata = $_GET['DataFim'];

if ($DataIni != '0-0-0')
{
	
	$desc_periodo = "Periodo: " . $DataIniFormata . " a " . $DataFimFormata;
	$where_datas = "AND eve.data_realizacao BETWEEN '$DataIni' AND '$DataFim'";
	
}

//Caso for informado um evento
if ($EventoId > 0)
{

	//Recupera dos dados do evento
	$sql_evento = "SELECT nome FROM eventos WHERE id = $EventoId";

	//Executa a query de consulta
	$query_evento = mysql_query($sql_evento);

	//Monta a matriz com os dados
	$dados_evento = mysql_fetch_array($query_evento);

	$query_evento = "AND evento_id = '$EventoId'";
	
}

//Chama a classe para gerar o PDF
class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{    
		
		switch ($_GET["TipoRel"]) 
		{
			
			case 1: $texto_rel = 'Resumido'; break;
			case 2: $texto_rel = 'Detalhado'; break;
			
		}
		
		global $desc_periodo;
		
		//Recupera o nome da empresa
		$empresaNome = $_GET['EmpresaNome'];
		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');
		$this->Ln();	    
		$this->SetFont('Arial','B',15);
		$this->Cell(0,6,'Compras do Foto e Vídeo',0,0,'R');
		$this->SetFont('Arial','B',12);		
		$this->Ln();
		$this->Cell(0,6,$desc_periodo,0,0,'R');
		$this->Ln();
		$this->Cell(0,6,$texto_rel,0,0,'R');
		$this->SetFont('Arial','',9);
  
		//Imprime o logotipo da empresa
		$this->Image('../image/logo_consoli2.jpg',10,10,42);
		//$this->Image('../image/logo_consoli2.jpg',10,10,59);
		$this->SetY(25);
		$this->SetFont('Arial', 'B', 10);
		$this->SetFillColor(178,178,178);
	
		//Line break
		$this->Ln(14);
	}

	//Rodapé do Relatório
	function Footer()
	{
   
		$usuarioNome = $_GET['UsuarioNome'];
		//Posiciona a 1.5 cm do final
		$this->SetY(-15);    
		//Arial italico 8
		$this->SetFont('Arial','I',7);
		$this->Line(10,281,200,281);
		$this->Cell(100,3,'Emitido por: ' . $usuarioNome);
		$this->SetFont('Arial','',7);
		$this->Cell(0,3,'[Powered by work | eventos]',0,0,'R');
	}
}

//Instancia a classe gerador de pdf
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos - CopyRight(c) - Desenvolvido por Work Labs Tecnologia - www.worklabs.com.br');
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle('Planilha de Compras do Foto e Vídeo do Evento');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();

$pdf->SetFillColor(178,178,178);
	
//verifica os formandos já cadastrados para este evento
$sql_formando  = mysql_query("SELECT 
																form.id, 
																form.evento_id,
																form.nome,
																form.contato,
																form.operadora,
																form.telefone_comercial,
																form.telefone_residencial,
																form.endereco,
																form.complemento,
																form.bairro,
																form.cep,
																form.cpf,
																form.uf,
																form.email,
																form.observacoes,
																eve.nome AS evento_nome,
																cid.nome AS cidade_nome
															FROM 
																eventos_formando form
															LEFT OUTER JOIN 
																eventos eve ON eve.id = form.evento_id
															LEFT OUTER JOIN 
																cidades cid ON cid.id = form.cidade_id
															WHERE 
																form.status = 2 
															AND 
																form.status_fotovideo = 1
																$query_evento
																$where_datas
															ORDER BY 
																eve.id,form.nome");

//Verifica o numero de registros retornados
$registros = mysql_num_rows($sql_formando);
	
//Verifica a quantidade de registros
if ($registros == 0 ) 
{
	
	//Exibe a mensagem que não foram encontrados registros
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(0,6, 'Não há formandos para o evento informado !',1,0,'L');

//Caso tiver
} 

else 

{
	
	$evento_quebra = 0;
	
	//Percorre o array 
	while ($dados_formando = mysql_fetch_array($sql_formando))
	{

		//Caso tenha quebra
		if ($evento_quebra != $dados_formando["evento_id"])
		{
		
			if ($evento_quebra > 0)
			{
			
				$pdf->ln();
				$pdf->SetFont('Arial', '', 10);
				$pdf->Cell(40,6, "Formandos: " . $registros_formando, 1, 0, 'L');
				$pdf->Cell(40,6, "Com Compra: " . $compra, 1, 0, 'L');
				$pdf->Cell(70,6, "Total Compra: " . number_format($compra_formando,2,',','.'), 1, 0, 'L');
				$pdf->Cell(0,6, "Pendentes: " . $pendente, 1, 0, 'L');
				$pdf->ln();
		
			}
			
			//Exibe a mensagem que não foram encontrados registros
			$pdf->ln();
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(0,6, "(" . $dados_formando["evento_id"] . ") - " . $dados_formando["evento_nome"], 1, 0, 'L',1);
			
			$registros_formando = 0;
			$compra_formando = 0;
			$pendente = 0;
			$compra = 0;
			
		}
		
		$registros_formando++;
		$total_formando++;
		
		//Captura o id do formando
		$FormandoId = $dados_formando["id"];

		//Caso for do modulo NOVO
		if ($OrigemRel == 1)
		{

			//verifica se o formando comprou algo de foto e video
			$sql_compra = mysql_query("SELECT 
																	id
																FROM 
																	fotovideo_pedido
																WHERE 
																	formando_id = $FormandoId");

			//Verifica o numero de registros retornados
			$registros = mysql_num_rows($sql_compra);
				
			//Caso não tenha compra
			if ($registros == 0 ) 
			{			
				
				$pendente++;
				$total_pendente++;

				//Caso for formato detalhado, mostra o nome do formando
				if ($TipoRel == 2)
				{
				
					$pdf->ln();
					$pdf->SetFont('Arial', 'B', 10);
					$pdf->SetX(15);
					$pdf->Cell(0,5, "[PENDENTE] - " . $dados_formando["nome"], 'T');
					$pdf->ln();
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetX(15);
					$pdf->Cell(0,4, $dados_formando["cidade_nome"] . ' - ' . $dados_formando["contato"] . ' - ' . $dados_formando["email"]);
				
				}

			}

			//Caso tenha compra
			else
			
			{
			
				$compra++;
				$total_compra++;

				//Percorre o array 
				while ($dados_compra = mysql_fetch_array($sql_compra))
				{

					$edtPedidoId = $dados_compra['id'];

					//Busca as compras do formando no modulo novo
					$sql_total = mysql_query("SELECT 
																			SUM(quantidade_venda * valor_unitario) AS total 
																		FROM 
																			fotovideo_pedido_produto 
																		WHERE 
																			pedido_id = $edtPedidoId
																		AND 
																			chk_brinde = 0");
					
					//Percorre o array 
					while ($dados_total = mysql_fetch_array($sql_total))
					{

						$compra_formando += $dados_total["total"];
						$compra_individual += $dados_total["total"];
						$total_compra_formando += $dados_total["total"];
					
					}

				}
				
				if ($TipoRel == 2)
				{
				
					$pdf->ln();
					$pdf->SetFont('Arial', 'B', 10);
					$pdf->SetX(15);
					$pdf->Cell(0,5, "[COMPRA] - " . $dados_formando["nome"] . "   -   R$: " . number_format($compra_individual,2,'.',',') , 'T');
					$compra_individual = 0;
					
				}

			}

		}

		//Caso for do módulo antigo
		else
		{
		
			//verifica se o formando comprou algo de foto e video
			$sql_compra = mysql_query("SELECT 
																	item_id,
																	valor_venda,
																	valor_desconto,
																	quantidade_venda
																FROM 
																	eventos_fotovideo 
																WHERE 
																	formando_id = $FormandoId");

			//Verifica o numero de registros retornados
			$registros = mysql_num_rows($sql_compra);
				
			//Verifica a quantidade de registros
			if ($registros == 0 ) 
			{			
				
				$pendente++;
				$total_pendente++;
				
				if ($TipoRel == 2)
				{
				
					$pdf->ln();
					$pdf->SetFont('Arial', 'B', 10);
					$pdf->SetX(15);
					$pdf->Cell(0,5, "[PENDENTE] - " . $dados_formando["nome"], 'T');
					$pdf->ln();
					$pdf->SetFont('Arial', '', 9);
					$pdf->SetX(15);
					$pdf->Cell(0,4, $dados_formando["cidade_nome"] . ' - ' . $dados_formando["contato"] . ' - ' . $dados_formando["email"]);
				
				}
				
			}
			
			else
			
			{
			
				$compra++;
				$total_compra++;
				
				//Percorre o array 
				while ($dados_compra = mysql_fetch_array($sql_compra))
				{
				
					$compra_formando = $compra_formando + (($dados_compra["valor_venda"] * $dados_compra["quantidade_venda"]) - $dados_compra["valor_desconto"]);
					$compra_individual = $compra_individual + (($dados_compra["valor_venda"] * $dados_compra["quantidade_venda"]) - $dados_compra["valor_desconto"]);
					$total_compra_formando = $total_compra_formando + (($dados_compra["valor_venda"] * $dados_compra["quantidade_venda"]) - $dados_compra["valor_desconto"]);
				
				}
				
				if ($TipoRel == 2)
				{
				
					$pdf->ln();
					$pdf->SetFont('Arial', 'B', 10);
					$pdf->SetX(15);
					$pdf->Cell(0,5, "[COMPRA] - " . $dados_formando["nome"] . "   -   R$: " . number_format($compra_individual,2,'.',',') , 'T');
					$compra_individual = 0;
					
				}
				
			}

		}
		
		$evento_quebra = $dados_formando["evento_id"];
	
	}
	
	$pdf->ln();
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(40,6, "Formandos: " . $registros_formando, 1, 0, 'L');
	$pdf->Cell(40,6, "Com Compra: " . $compra, 1, 0, 'L');
	$pdf->Cell(70,6, "Total Compra: " . number_format($compra_formando,2,',','.'), 1, 0, 'L');
	$pdf->Cell(0,6, "Pendentes: " . $pendente, 1, 0, 'L');
	
	//Imprime os dados do formando	  
	$pdf->ln(15);
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Cell(0,6, "TOTAL GERAL" , 1, 0, 'C',1);
	$pdf->ln();		
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(40,6, "Formandos: " . $total_formando, 1, 0, 'L');
	$pdf->Cell(40,6, "Com Compra: " . $total_compra, 1, 0, 'L');
	$pdf->Cell(70,6, "Total Compra: " . number_format($total_compra_formando,2,',','.'), 1, 0, 'L');
	$pdf->Cell(0,6, "Pendentes: " . $total_pendente, 1, 0, 'L');
	
}

$pdf->ln();	

//Gera o PDF
$pdf->Output();

?>                                                      