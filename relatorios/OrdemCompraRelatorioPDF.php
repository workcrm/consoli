<?php
###########
## Módulo para emissao da Ordem de Compra
## Criado: 06/03/2012 - Maycon Edinger
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
$OrdemId = $_GET["OrdemId"]; 

//Chama a classe para gerar o PDF
class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{    
	
		//Recupera os valores para filtragem
		$OrdemId = $_GET["OrdemId"];
		
		//Recupera dos dados do evento
		$sql_oc = "SELECT
					oc.id,
					oc.data,
					oc.prazo_compra,
					oc.prazo_entrega,
					oc.solicitante,
					oc.departamento_id,
					oc.evento_id,
					oc.transportadora,
					oc.cond_pgto,
					oc.obs,
					oc.desconto,
					dep.nome AS departamento_nome,
					forn.id AS fornecedor_id,
					forn.nome AS fornecedor_nome,
					forn.telefone AS fornecedor_telefone,
					forn.cnpj AS fornecedor_cnpj
					FROM ordem_compra oc
					LEFT OUTER JOIN departamentos dep ON dep.id = oc.departamento_id
					LEFT OUTER JOIN fornecedores forn ON forn.id = oc.fornecedor_id
					WHERE oc.id = '$OrdemId'"; 							
		
		//Executa a query de consulta
		$query_oc = mysql_query($sql_oc);
		
		//Monta a matriz com os dados
		$dados = mysql_fetch_array($query_oc); 
		
		
		//Logo da Empresa
		$this->Image("../image/logo_consoli2.jpg",13,13,30);
		
		//Cor do preenchimento
		$this->SetFillColor(178,178,178);
		
		//Ajusta a fonte
		$this->Ln();    
		$this->SetFont('Arial','B',10);
		$this->Cell(95,8,'','LTR',0,'R');
		$this->SetFont('Arial','B',16);
		$this->Cell(65,8,'ORDEM DE COMPRA ','TR',0,'C');
		$this->Cell(0,8,$OrdemId,'TR',0,'C');
		$this->Ln();
		$this->SetFont('Arial','B',12);
		$this->Cell(95,5,'CONSOLI EVENTOS LTDA ','LR',0,'R');
		$this->SetFont('Arial','B',10);
		$this->Cell(39,5,' Data: ','LTR',0,'L',1);
		$this->SetFont('Arial','',10);
		$this->Cell(0,5, DataMySQLRetornar($dados["data"]) ,'LTR',0,'C');
		$this->Ln();	    
		$this->SetFont('Arial','',10);
		$this->Cell(95,5,'Rua São Bento, 289 - Progresso ','LR',0,'R');	 
		$this->SetFont('Arial','B',10);
		$this->Cell(39,5,' Prazo para Compra: ' ,'LTR',0,'L',1);
		$this->SetFont('Arial','',10);
		
		if ($dados["prazo_compra"] != '0000-00-00')
		{
		
			$this->Cell(0,5, DataMySQLRetornar($dados["prazo_compra"]) ,'LTR',0,'C');
		
		}
		
		else
		
		{
	
			$this->Cell(0,5, " ", 'LTR',0,'C');
		
		}
		
		$this->Ln();	    
		$this->SetFont('Arial','',10);
		$this->Cell(95,5,'89.160-000 - Rio do Sul - SC ','LR',0,'R');	 
		$this->SetFont('Arial','B',10);
		$this->Cell(39,5,' Solicitante: ','LTR',0,'L',1);	
		$this->SetFont('Arial','',10);
		$this->Cell(0,5, $dados["solicitante"] ,'LTR',0,'C');	
		$this->Ln();	    
		$this->SetFont('Arial','',10);
		$this->Cell(95,5,'(47) 3522-1336 - compras@consolieventos.com.br ','LBR',0,'R');	 
		
		$this->SetFont('Arial','B',10);
		$this->Cell(39,5,' Departamento: ','LTBR',0,'L',1);	
		$this->SetFont('Arial','',10);
		$this->Cell(0,5, $dados["departamento_nome"] ,'LTBR',0,'C');	

		$this->Ln();
		$this->SetFont('Arial','B',10);
		$this->Cell(95,5,'Fornecedor','LR',0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(39,5,' Evento: ' ,'LTBR',0,'L',1);
		$this->SetFont('Arial','',10);	
		$this->Cell(0,5, $dados["evento_id"] ,'LTBR',0,'C');		
		
		$this->Ln();
		$this->SetFont('Arial','',8);
		$this->Cell(95,5, $dados["fornecedor_id"] . ' - ' . $dados["fornecedor_nome"],'LR',0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(39,5,' Cond. de Pagamento: ','LTR',0,'L',1);
		$this->SetFont('Arial','',10);
		$this->Cell(0,5, $dados["cond_pgto"] ,'LTR',0,'C');

		$this->Ln();
		$this->SetFont('Arial','',8);
		$this->Cell(95,5, $dados["fornecedor_cnpj"] . " - " . $dados["fornecedor_telefone"] . " - " . $dados["fornecedor_email"],'LBR',0,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(39,5,' Prazo de Entrega: ','LTBR',0,'L',1);
		$this->SetFont('Arial','',10);
		
		if ($dados["prazo_entrega"] != '0000-00-00')
		{
		
			$this->Cell(0,5, DataMySQLRetornar($dados["prazo_entrega"]) ,'LTBR',0,'C');
		
		}
		
		else
		
		{
	
			$this->Cell(0,5, " ", 'LTBR',0,'C');
		
		}
		
		$this->Ln();
		$this->SetFont('Arial','B',8);
		$this->Cell(18,7, "CÓDIGO",1,0,'C',1);
		$this->Cell(24,7, "QUANTIDADE",1,0,'C',1);
		$this->Cell(8,7, "UN",1,0,'C',1);
		$this->Cell(92,7, "DESCRIÇÃO DAS MERCADORIAS",1,0,'C',1);
		$this->Cell(24,7, "UNITÁRIO",1,0,'C',1);
		$this->Cell(24,7, "TOTAL",1,0,'C',1);
	
		//Line break
		$this->Ln(10);
	}

}

//Instancia a classe gerador de pdf
$pdf = new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos - CopyRight(c) - Work Labs Tecnologia - www.worklabs.com.br');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Ordem de Compra');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();


//verifica os itens da ordem de compra
$sql_produto = mysql_query("SELECT 
							ite.id,
							ite.produto_id,
							ite.quantidade,
							ite.valor_unitario,
							produto.nome AS produto_nome,
							produto.unidade
							FROM ordem_compra_produto ite
							LEFT OUTER JOIN item_evento produto ON produto.id = ite.produto_id
							WHERE ite.ordem_compra_id = '$OrdemId' 
							ORDER by produto.nome");

//Verifica o numero de registros retornados
$registros = mysql_num_rows($sql_produto);

//Verifica a quantidade de registros
if ($registros == 0 ) 
{
	
	//Exibe a mensagem que não foram encontrados registros
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->Cell(0,6, 'Não há produtos cadastrados para esta OC !',1,0,'L');

//Caso tiver
} 

else 

{
	
	//Percorre o array 
	while ($dados_produto = mysql_fetch_array($sql_produto))
	{
		
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(18,6, $dados_produto['produto_id'],0,0,'C');
		$pdf->Cell(24,6, $dados_produto['quantidade'],0,0,'C');
		$pdf->Cell(8,6, $dados_produto['unidade'],0,0,'C');
		$pdf->Cell(92,6, $dados_produto['produto_nome'],0,0,'L');
		$pdf->Cell(24,6, number_format($dados_produto['valor_unitario'],2,',','.'),0,0,'R');
		
		$total_item = $dados_produto['valor_unitario'] * $dados_produto['quantidade'];
		
		$total_geral = $total_geral + $total_item;
		
		$pdf->Cell(24,6, number_format($total_item,2,',','.'),0,0,'R');
		
		$pdf->ln();

	}
	
	//Recupera dos dados do evento
	$sql_oc = "SELECT oc.desconto FROM ordem_compra oc WHERE oc.id = '$OrdemId'"; 							
	
	//Executa a query de consulta
	$query_oc = mysql_query($sql_oc);
	
	//Monta a matriz com os dados
	$dados = mysql_fetch_array($query_oc); 
	
	$desconto = $dados['desconto'];
		
	$pdf->SetX(140);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(60,5, "Total dos Produtos: " . number_format($total_geral,2,',','.'),'LTR',0,'R');
	$pdf->ln();
	$pdf->SetX(140);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(60,4, "Desconto: " . number_format($desconto,2,',','.'),'LR',0,'R');
	$pdf->ln();
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(0,4, "Total de Itens: " . $registros);
	$pdf->SetX(140);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(60,4, "Total Geral: " . number_format($total_geral - $desconto,2,',','.'),'LR',0,'R');
	$pdf->ln();
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(0,4, "ATENÇÃO: 1 - O NÚMERO DESTA O.C. DEVE CONSTAR NA NOTA FISCAL","LTR",0,'L');
	
	$pdf->ln();
	$pdf->Cell(0,4, "                   2 - OBSERVAR O PRAZO DE ENTREGA RIGOROSAMENTE","LR",0,'L');
	
	$pdf->ln();
	$pdf->Cell(0,4, "                   3 - OBSERVAR COM ANTECEDÊNCIA QUALQUER ALTERAÇÃO EM RELACAO A O.C.","LBR",0,'L');
	
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(60,10, "COMPRADOR:","1",0,'L');
	$pdf->Cell(60,10, "VISTO SOLICITANTE:","1",0,'L');
	$pdf->Cell(0,10, "VISTO DIRETORIA:","1",0,'L');
	
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(0,5, "OBSERVAÇÕES:","LTR",0,'L');
	
	//Recupera os valores para filtragem
	$OrdemId = $_GET["OrdemId"];
	
	//Recupera dos dados do evento
	$sql_oc = "SELECT oc.obs FROM ordem_compra oc WHERE oc.id = '$OrdemId'"; 							
	
	//Executa a query de consulta
	$query_oc = mysql_query($sql_oc);
	
	//Monta a matriz com os dados
	$dados = mysql_fetch_array($query_oc);
	
	$pdf->ln();
	$pdf->SetFont('Arial', '', 7);
	$pdf->Multicell(0,3, $dados["obs"],"LBR",'L');
	
	
//Fecha o If
}


$pdf->ln();	

//Gera o PDF
$pdf->Output();

?>                                                      