<?php
###########
## Módulo para impressão da música com letra
## Criado: 08/10/2007 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Acesso as rotinas do PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conexão com o servidor
include "../conexao/ConexaoMySQL.php";

//Recupera os valores para filtragem
$MusicaId = $_GET["MusicaId"];

//Recupera dos dados da musica
$sql_musica = "SELECT * FROM musicas WHERE id = '$MusicaId'";

//Executa a query de consulta
$query_musica = mysql_query($sql_musica);

//Monta a matriz com os dados
$dados_musica = mysql_fetch_array($query_musica);

//Chama a classe para gerar o PDF
class PDF extends FPDF
{

}

//Instancia a classe gerador de pdf
$pdf=new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos - CopyRight(c) - Desenvolvido por Maycon Edinger - workeventos@gmail.com');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Impressão da Música');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();

//Nova linha
//$pdf->ln();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0,7, $dados_musica['nome'],'T',0,'L');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 11);
$pdf->MultiCell(0,4, $dados_musica['interprete'],'B');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'I', 12);
$pdf->MultiCell(0,7, $dados_musica['letra']);

//Gera o PDF
$pdf->Output();

?>                                                      