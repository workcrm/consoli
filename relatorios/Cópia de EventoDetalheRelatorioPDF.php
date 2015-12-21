<?php
###########
## Módulo para montagem do relatório de Detalhamento do Evento em PDF
## Criado: 24/05/2007 - Maycon Edinger
## Alterado: 30/09/2008 - Maycon Edinger
## Alterações: 
## 05/06/2007 - Implementado rotina para impressão dos participantes do evento (opcional)
## 21/06/2007 - Implementado para exibir os detalhes financeiros (opcional)
## 14/07/2007 - Implementado exibição da função do colador no evento, telefone e celular
## 05/08/2007 - Implementado exibição dos itens do evento agrupados por categoria
## 15/10/2007 - Implementado exibição do repertório do evento e possibilidade de não exibir categorias pelas opçoes
## 05/11/2007 - Implementado exibição dos materiais de composição dos itens do evento
## 25/11/2007 - Implementado exibição dos serviços do evento
## 30/09/2007 - Implementado exibição dos terceiros do evento
###########
//http://localhost/consoli/relatorios/EventoDetalheRelatorioPDF.php?EventoId=1&UsuarioNome=Maycon%20Edinger&EmpresaNome=Nome%20da%20empresa&EmpresaId=1

//Acesso as rotinas do PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conexão com o servidor
include '../conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include '../include/ManipulaDatas.php';

//Recupera os valores para filtragem
$EventoId = $_GET['EventoId'];
$EmpresaId = $_GET['EmpresaId'];
$ImprimeDatas = $_GET['Id'];
$ImprimeParticipantes = $_GET['Ip'];
$ImprimeEnderecos = $_GET['Ie'];
$ImprimeRepertorio = $_GET['Ir'];
$ImprimeItens = $_GET['Ii'];
$ImprimeMateriais = $_GET['Im'];
$ImprimeValores = $_GET['Iv'];
$ImprimeServicos = $_GET['Is'];
$ImprimeTerceiros = $_GET['It'];
$ImprimeBrindes = $_GET['Ib'];
$ImprimeFormandos = $_GET['If'];
$ImprimeValoresServicos = $_GET['Ivs'];

//Recupera dos dados do evento
$sql_evento = "SELECT 
				eve.id,
				eve.nome,
				eve.descricao,
				eve.tipo,
				eve.status,
				eve.cliente_id,
				eve.responsavel_orcamento,
				eve.responsavel,
				eve.contato1,
				eve.contato_obs1,
				eve.contato_fone1,
				eve.contato2,
				eve.contato_obs2,
				eve.contato_fone2,
				eve.contato3,
				eve.contato_obs3,
				eve.contato_fone3,
				eve.contato4,
				eve.contato_obs4,
				eve.contato_fone4,
				eve.contato5,
				eve.contato_obs5,
				eve.contato_fone5,
				eve.contato6,
				eve.contato_obs6,
				eve.contato_fone6,				
				eve.data_realizacao,
				eve.hora_realizacao,
				eve.duracao,
				eve.numero_confirmado,
				eve.lugares_ocupados,
				eve.alunos_colacao,
				eve.alunos_baile,
				eve.observacoes,
				eve.data_jantar,
				eve.hora_jantar,
				eve.data_certame,
				eve.hora_certame,
				eve.data_foto_convite,
				eve.hora_foto_convite,
				eve.local_foto_convite,
				eve.data_ensaio,
				eve.obs_ensaio,
				eve.data_culto,
				eve.obs_culto,
				eve.data_colacao,
				eve.obs_colacao,
				eve.data_baile,
				eve.obs_baile,
				eve.quebras,
				eve.valor_foto,
				eve.valor_dvd,
				eve.obs_fotovideo,
				eve.numero_nf,
				eve.posicao_financeira,
				eve.roteiro,
				cli.id as cliente_id,
				cli.nome as cliente_nome,
				cli.endereco as cliente_endereco,
				cli.complemento as cliente_complemento,
				cli.bairro as cliente_bairro,
				cli.cidade_id,
				cli.cep as cliente_cep,
				cli.uf as cliente_uf,
				cli.telefone as cliente_telefone,
				cli.fax as cliente_fax,
				cli.celular as cliente_celular,
				cli.email as cliente_email,
				cid.nome as cliente_cidade
				FROM eventos eve 
				LEFT OUTER JOIN clientes cli ON cli.id = eve.cliente_id
				LEFT OUTER JOIN cidades cid ON cid.id = cli.cidade_id
				WHERE eve.id = $EventoId";

//Executa a query de consulta
$query_evento = mysql_query($sql_evento);

//Monta a matriz com os dados
$dados_evento = mysql_fetch_array($query_evento);

//Efetua o switch para o campo de tipo
switch ($dados_evento[tipo]) 
{
  
	case 1: $desc_tipo = 'Evento Social'; break;
	case 2: $desc_tipo = 'Formatura'; break;

}

//Efetua o switch para o campo de status
switch ($dados_evento[status]) 
{
	
	case 0: $desc_status = 'Em orçamento'; break;
	case 1: $desc_status = 'Em aberto'; break;
	case 2: $desc_status = 'Realizado'; break;
	case 3: $desc_status = 'Não-Realizado'; break;

} 

//Efetua o switch para o campo de posição financeira
switch ($dados_evento[posicao_financeira]) 
{
	
	case 1: $desc_financeiro = 'A Receber'; break;
	case 2: $desc_financeiro = 'Recebido'; break;
	case 3: $desc_financeiro = 'Cortesia'; break;	

} 

//Chama a classe para gerar o PDF
class PDF extends FPDF
{

	//Cabeçalho do relatório
	function Header()
	{    
	
		
		//Recupera o nome da empresa
		$empresaNome = $_GET['EmpresaNome'];
		
		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');
		$this->Ln();	    
		$this->SetFont('Arial','B',15);
		$this->Cell(0,6,'Detalhamento do Evento',0,0,'R');
		$this->SetFont('Arial','',9);

		//Imprime o logotipo da empresa
		$this->Image('../image/logo_consoli2.jpg',10,10,40);
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
$pdf->SetCreator('Work Eventos - CopyRight(c) - Work Labs Tecnologia - www.worklabs.com.br');
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle('Detalhamento do Evento');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();

//Nova linha
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0,7, $dados_evento['nome'],1,0,'C');
$pdf->ln(4);

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, 'Detalhamento do Evento',1,0,'C',1);
$pdf->SetFont('Arial', 'I', 9);
$pdf->ln();
$pdf->MultiCell(0,4, $dados_evento['descricao'],1);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,4, 'Tipo:',1,0,'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $desc_tipo,0,0,'L');
$pdf->ln();

//Nova linha
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,16, 'Cliente:',1,0,'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $dados_evento['cliente_nome'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial', 'I', 7);
$pdf->SetX(35);
$pdf->Cell(0,3, $dados_evento['cliente_endereco'] . ' - ' . $dados_evento['cliente_complemento'],0,0,'L');
$pdf->ln();
$pdf->SetX(35);
$pdf->Cell(0,3, $dados_evento['cliente_bairro'] . ' - ' . $dados_evento['cliente_cep'] . ' - ' . $dados_evento['cliente_cidade'] . '/' . $dados_evento['cliente_uf'],0,0,'L');
$pdf->ln();
$pdf->SetX(35);
$pdf->Cell(0,3, 'Fone: ' . $dados_evento[cliente_telefone] . ' - Fax: ' . $dados_evento[cliente_fax] . ' - Celular: ' . $dados_evento[cliente_celular],0,0,'L');
$pdf->ln();
$pdf->SetX(35);
$pdf->Cell(0,3, 'email: ' . $dados_evento[cliente_email],0,0,'L');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Respons. Orçamento:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(45);
$pdf->Cell(0,6, $dados_evento['responsavel_orcamento'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Respons. Evento:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(45);
$pdf->Cell(0,6, $dados_evento['responsavel'],0,0,'L');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,16, 'Contatos:',1,0,'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, 'Nome',0,0,'L');
$pdf->SetX(95);
$pdf->Cell(0,4, 'E-Mail',0,0,'L');
$pdf->SetX(172);
$pdf->Cell(0,4, 'Telefone',0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $dados_evento['contato1'],0,0,'L');
$pdf->SetX(95);
$pdf->Cell(0,4, $dados_evento['contato_obs1'],0,0,'L');
$pdf->SetX(172);
$pdf->Cell(0,4, $dados_evento['contato_fone1'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $dados_evento['contato2'],0,0,'L');
$pdf->SetX(95);
$pdf->Cell(0,4, $dados_evento['contato_obs2'],0,0,'L');
$pdf->SetX(172);
$pdf->Cell(0,4, $dados_evento['contato_fone2'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $dados_evento['contato3'],0,0,'L');
$pdf->SetX(95);
$pdf->Cell(0,4, $dados_evento['contato_obs3'],0,0,'L');
$pdf->SetX(172);
$pdf->Cell(0,4, $dados_evento['contato_fone3'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $dados_evento['contato4'],0,0,'L');
$pdf->SetX(95);
$pdf->Cell(0,4, $dados_evento['contato_obs4'],0,0,'L');
$pdf->SetX(172);
$pdf->Cell(0,4, $dados_evento['contato_fone4'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $dados_evento['contato5'],0,0,'L');
$pdf->SetX(95);
$pdf->Cell(0,4, $dados_evento['contato_obs5'],0,0,'L');
$pdf->SetX(172);
$pdf->Cell(0,4, $dados_evento['contato_fone5'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, $dados_evento['contato6'],0,0,'L');
$pdf->SetX(95);
$pdf->Cell(0,4, $dados_evento['contato_obs6'],0,0,'L');
$pdf->SetX(172);
$pdf->Cell(0,4, $dados_evento['contato_fone6'],0,0,'L');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Data Evento:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(40);
$pdf->Cell(0,6, DataMySQLRetornar($dados_evento['data_realizacao']),0,0,'L');
$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Hora:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(80);
$pdf->Cell(0,6, $dados_evento['hora_realizacao'],0,0,'L');
$pdf->SetX(130);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(70,6, 'Duração:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(146);
$pdf->Cell(0,6, $dados_evento['duracao'],0,0,'L');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Data do Jantar:',1,0,'L');

if ($dados_evento['data_jantar'] != '0000-00-00')
{
  
	$pdf->SetFont('Arial', 'I', 9);
	$pdf->SetX(40);
	$pdf->Cell(0,6, DataMySQLRetornar($dados_evento['data_jantar']),0,0,'L');

}

$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Hora do Jantar:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(101);
$pdf->Cell(0,6, $dados_evento['hora_jantar'],0,0,'L');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Data CERTAME:',1,0,'L');

if ($dados_evento['data_certame'] != '0000-00-00')
{

  $pdf->SetFont('Arial', 'I', 9);
  $pdf->SetX(40);
  $pdf->Cell(0,6, DataMySQLRetornar($dados_evento['data_certame']),0,0,'L');

}

$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Hora Certame:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(101);
$pdf->Cell(0,6, $dados_evento['hora_certame'],0,0,'L');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Data Foto Convite:',1,0,'L');

if ($dados_evento['data_foto_convite'] != '0000-00-00')
{
  
	$pdf->SetFont('Arial', 'I', 9);
	$pdf->SetX(40);
	$pdf->Cell(0,6, DataMySQLRetornar($dados_evento['data_foto_convite']),0,0,'L');

}

$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Hora Foto Convite:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(101);
$pdf->Cell(0,6, $dados_evento['hora_foto_convite'],0,0,'L');
$pdf->SetX(130);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(70,6, 'Local:',1,0,'L');
$pdf->SetFont('Arial', 'I', 7);
$pdf->SetX(142);
$pdf->Cell(0,6, $dados_evento['local_foto_convite'],0,0,'L');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Data Ensaio:',1,0,'L');

if ($dados_evento['data_ensaio'] != '0000-00-00')
{

	$pdf->SetFont('Arial', 'I', 9);
	$pdf->SetX(40);
	$pdf->Cell(0,6, DataMySQLRetornar($dados_evento['data_ensaio']),0,0,'L');

}

$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Obs:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(80);
$pdf->Cell(0,6, $dados_evento['obs_ensaio'],0,0,'L');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Data Culto:',1,0,'L');

if ($dados_evento['data_culto'] != '0000-00-00')
{
  
	$pdf->SetFont('Arial', 'I', 9);
	$pdf->SetX(40);
	$pdf->Cell(0,6, DataMySQLRetornar($dados_evento['data_culto']),0,0,'L');

}

$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Obs:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(80);
$pdf->Cell(0,6, $dados_evento['obs_culto'],0,0,'L');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Data Colação:',1,0,'L');

if ($dados_evento['data_colacao'] != '0000-00-00')
{
  
	$pdf->SetFont('Arial', 'I', 9);
	$pdf->SetX(40);
	$pdf->Cell(0,6, DataMySQLRetornar($dados_evento['data_colacao']),0,0,'L');

}

$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Obs:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(80);
$pdf->Cell(0,6, $dados_evento['obs_colacao'],0,0,'L');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Data Baile:',1,0,'L');

if ($dados_evento['data_baile'] != '0000-00-00')
{
  
  $pdf->SetFont('Arial', 'I', 9);
  $pdf->SetX(40);
  $pdf->Cell(0,6, DataMySQLRetornar($dados_evento['data_baile']),0,0,'L');

}

$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Obs:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(80);
$pdf->Cell(0,6, $dados_evento['obs_baile'],0,0,'L');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Pessoas Confirmadas:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(55);
$pdf->Cell(0,6, $dados_evento['numero_confirmado'],0,0,'L');
$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Lugares Montados:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(110);
$pdf->Cell(0,6, $dados_evento['lugares_ocupados'],0,0,'L');

$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Alunos na Colação:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(55);
$pdf->Cell(0,6, $dados_evento['alunos_colacao'],0,0,'L');
$pdf->SetX(70);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,6, 'Alunos no Baile:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(110);
$pdf->Cell(0,6, $dados_evento['alunos_baile'],0,0,'L');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, 'Informações Complementares',1,0,'C',1);
$pdf->ln();
$pdf->SetFont('Arial', 'I', 10);
$pdf->MultiCell(0,4, $dados_evento['observacoes'],1);

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, 'Roteiro do Evento',1,0,'C',1);
$pdf->ln();
$pdf->SetFont('Arial', 'I', 10);
$pdf->MultiCell(0,4, $dados_evento['roteiro'],1);

//Verifica se deve imprimir as datas do evento
if ($ImprimeDatas == 1) 
{

	//*** Exibe as datas do evento
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, 'Datas do Evento',1,0,'C',1);
	$pdf->ln();
	
	//verifica os participantes já cadastrados para este evento e exibe na tela
	$sql_datas = mysql_query("SELECT * FROM eventos_data WHERE evento_id = $EventoId ORDER BY data");
	
	//Verifica o numero de registros retornados
	$registros_datas = mysql_num_rows($sql_datas);
	
	//Verifica a quantidade de registros
	if ($registros_datas == 0 ) 
	{
		
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Não há datas cadastradas para este evento !',1,0,'L');
	
	//Caso tiver
	} 
	
	else 
	
	{
		
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Data/Hora:',1,0,'L');
		$pdf->SetX(40);
		$pdf->Cell(0,6, 'Descrição:');	
		$pdf->SetX(116);
		$pdf->Cell(0,6, 'Observações:');	
	
		//Percorre o array 
		while ($dados_datas = mysql_fetch_array($sql_datas))
		{
	
			//Imprime os dados dos contatos
			$pdf->ln();
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(6,4, DataMySQLRetornar($dados_datas['data']) . ' - ' . substr($dados_datas['hora'], 0, 5));
			$pdf->SetX(40);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(100,4, $dados_datas['descricao']);		  		  
			$pdf->SetX(116);
			$pdf->SetFont('Arial','',8);
			$pdf->Multicell(0,3, $dados_datas['observacoes']);
	
		}	
	
	}
	
	$pdf->ln();

//Fecha o if de imprimir as datas
}	


//Verifica se deve imprimir os participantes do evento
if ($ImprimeParticipantes == 1) 
{

	//*** Exibe os participantes do evento
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, 'Participantes do Evento',1,0,'C',1);
	$pdf->ln();
	
	//verifica os participantes já cadastrados para este evento e exibe na tela
	$sql_participante = mysql_query("SELECT
									 par.id,
									 par.colaborador_id,
									 par.funcao_id,
									 col.nome as colaborador_nome,
									 col.telefone,
									 col.celular,
									 fun.nome as funcao_nome
									 FROM eventos_participante par
									 INNER JOIN colaboradores col ON col.id = par.colaborador_id
									 LEFT OUTER JOIN funcoes fun ON fun.id = par.funcao_id
									 WHERE par.evento_id = '$EventoId'
									 ORDER by col.nome");
	
	//Verifica o numero de registros retornados
	$registros_part = mysql_num_rows($sql_participante);
	
	//Verifica a quantidade de registros
	if ($registros_part == 0 ) 
	{
		
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Não há participantes cadastrados para este evento !',1,0,'L');
	
	} 
	
	else 
	{
		
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Participante/Colaborador:',1,0,'L');
		$pdf->SetX(100);
		$pdf->Cell(0,6, 'Função no Evento:');
		$pdf->SetX(154);
		$pdf->Cell(0,6, 'Telefone:');
		$pdf->SetX(177);
		$pdf->Cell(0,6, 'Celular:');		
	
		//Percorre o array 
		while ($dados_participante = mysql_fetch_array($sql_participante))
		{
	
			//Imprime os dados dos contatos
			$pdf->ln();
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(6,4, $dados_participante['colaborador_nome']);
			$pdf->SetX(100);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(100,4, $dados_participante['funcao_nome']);
			$pdf->SetX(154);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(70,4, $dados_participante['telefone']);
			$pdf->SetX(177);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(70,4, $dados_participante['celular']);		  
	
		}	
	
	}
	
	$pdf->ln();

//Fecha o if de imprimir os participantes
}	

//Verifica se deve imprimir os endereços do evento
if ($ImprimeEnderecos == 1) 
{
	
	//*** Exibe os endereços da conta
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, 'Endereços do Evento',1,0,'C',1);
	$pdf->ln();
	
	//Efetua a pesquisa dos endereços
	//verifica os endereços já cadastrados para este evento e exibe na tela
	$sql_consulta = mysql_query("SELECT
								 end.id,
								 end.local_id,
								 end.fornecedor_id,
								 end.nome,
								 end.endereco,
								 end.complemento,
								 end.bairro,
								 end.cep,
								 end.uf,
								 end.hora_inicio,
								 end.hora_termino,
								 end.telefone,
								 end.fax,
								 end.celular,
								 end.email,
								 loc.nome as local_nome,
								 cid.nome as cidade_nome,
								 cid2.nome as fornecedor_cidade_nome,
								 forn.nome as fornecedor_nome,
								 forn.endereco as fornecedor_endereco,
								 forn.complemento as fornecedor_complemento,
								 forn.bairro as fornecedor_bairro,
								 forn.cep as fornecedor_cep,
								 forn.uf as fornecedor_uf,
								 forn.telefone as fornecedor_telefone,
								 forn.fax as fornecedor_fax,
								 forn.celular as fornecedor_celular,
								 forn.email as fornecedor_email							 																	 
								 FROM eventos_endereco end
								 INNER JOIN local_evento loc ON loc.id = end.local_id
								 LEFT OUTER JOIN cidades cid ON cid.id = end.cidade_id
								 LEFT OUTER JOIN fornecedores forn ON forn.id = end.fornecedor_id
								 LEFT OUTER JOIN cidades cid2 ON cid2.id = forn.cidade_id														 
								 WHERE end.evento_id = '$EventoId'
								 ORDER BY loc.nome");
	
	//Verifica o numero de registros retornados
	$registros = mysql_num_rows($sql_consulta);
	
	//Verifica a quantidade de registros
	if ($registros == 0 ) 
	{
		
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Não há endereços cadastrados para este evento !',1,0,'L');
		$pdf->ln();
		
	} 
	
	else 
	{
		
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Local:',1,0,'L');
		$pdf->SetX(75);
		$pdf->Cell(0,6, 'Nome/Endereço:');
		$pdf->SetX(172);
		$pdf->Cell(0,6, 'Início:');
		$pdf->SetX(184);
		$pdf->Cell(0,6, 'Término:');
			
		//Percorre o array 
		while ($dados_endereco = mysql_fetch_array($sql_consulta))
		{
	
			//Imprime os dados dos contatos
			$pdf->ln();
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(6,4, $dados_endereco['local_nome']);
			$pdf->SetX(75);
			$pdf->SetFont('Arial','B',9);

			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco['fornecedor_id'] == 0)
			{

				$pdf->Cell(100,4, $dados_endereco['nome']);
			
			} 
			
			else 
			
			{
				
				$pdf->Cell(100,4, $dados_endereco['fornecedor_nome']);
			
			}

			$pdf->SetX(172);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(48,4, $dados_endereco['hora_inicio']);
			$pdf->SetX(184);
			$pdf->Cell(0,4, $dados_endereco['hora_termino']);

			$pdf->ln();
			$pdf->SetFont('Arial', 'I', 7);
			$pdf->SetX(75);

			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco['fornecedor_id'] == 0)
			{
			
				$pdf->Cell(0,3, $dados_endereco['endereco'] . ' - ' . $dados_endereco['complemento'],0,0,'L');
			
			} 
			
			else 
			
			{
				
				$pdf->Cell(0,3, $dados_endereco['fornecedor_endereco'] . ' - ' . $dados_endereco['fornecedor_complemento'],0,0,'L');
			
			}

			$pdf->ln();
			$pdf->SetX(75);

			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco['fornecedor_id'] == 0)
			{
			
				$pdf->Cell(0,3, $dados_endereco['bairro'] . ' - ' . $dados_endereco['cep'] . ' - ' . $dados_endereco['cidade_nome'] . '/' . $dados_endereco['uf'],0,0,'L');
			
			} 
			
			else 
			
			{
				
				$pdf->Cell(0,3, $dados_endereco['fornecedor_bairro'] . ' - ' . $dados_endereco['fornecedor_cep'] . ' - ' . $dados_endereco['fornecedor_cidade_nome'] . '/' . $dados_endereco['fornecedor_uf'],0,0,'L');
			
			}

			$pdf->ln();
			$pdf->SetX(75);

			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco['fornecedor_id'] == 0)
			{
			
				$pdf->Cell(0,3, 'Fone: ' . $dados_endereco[telefone] . ' - Fax: ' . $dados_endereco[fax] . ' - Celular: ' . $dados_endereco[celular],0,0,'L');
			
			} 
			
			else 
			
			{
				
				$pdf->Cell(0,3, 'Fone: ' . $dados_endereco[fornecedor_telefone] . ' - Fax: ' . $dados_endereco[fornecedor_fax] . ' - Celular: ' . $dados_endereco[fornecedor_celular],0,0,'L');
			
			}

			$pdf->ln();
			$pdf->SetX(75);


			//Caso seja antigo e não tem fornecedor ID cadastrado
			if ($dados_endereco['fornecedor_id'] == 0)
			{
			
				$pdf->Cell(0,3, 'email: ' . $dados_endereco[email],0,0,'L');
			
			} 
			
			else 
			
			{
				
				$pdf->Cell(0,3, 'email: ' . $dados_endereco[fornecedor_email],0,0,'L');
			
			}

			$pdf->ln();
			$pdf->Cell(0,3, '',0,0,'L');
	
		}
		
	}

}


//Verifica se deve imprimir os dados dos itens do evento
if ($ImprimeItens == 1) 
{

	//Verifica se deve imprimir os dados financeiros do evento
	if ($ImprimeValores == 0) 
	{
	
		//*** Exibe os itens do evento
		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->SetFillColor(178,178,178);
		$pdf->Cell(0,6, 'Produtos do Evento',1,0,'C',1);
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
		if ($registros == 0 ) 
		{
			
			//Exibe a mensagem que não foram encontrados registros
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,6, 'Não há produtos cadastrados para este evento !',1,0,'L');
		
		//Caso tiver itens no banco
		} 
		
		else 
		
		{
			
			//Define a cor RGB do fundo da celula
			$pdf->SetFillColor(178,178,178);
			//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
			$pdf->SetFont('Arial', 'B', 9);		
	
			$pdf->Cell(0,6, '    Quant',1,0,'L');
			$pdf->SetX(28);
			$pdf->Cell(0,6, 'Un');
			$pdf->SetX(34);
			$pdf->Cell(0,6, '     Descrição do Item:');
			$pdf->SetX(106);
			$pdf->Cell(0,6, 'Observações:');
	
	
			
			//Monta a query para capturar as categorias que existem cadastrados itens
			$sql_categoria = mysql_query("SELECT 
										ite.id,
										ite.categoria_id,											
										cat.nome as categoria_nome,
										eve.valor_venda
										FROM item_evento ite
										LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
										INNER JOIN eventos_item eve ON eve.item_id = ite.id
										WHERE eve.evento_id = '$EventoId'											
										GROUP BY cat.nome
										ORDER BY cat.nome");
		
			
			//Percorre o array das funcoes
			while ($dados_categoria = mysql_fetch_array($sql_categoria))
			{
		
				//Imprime o nome da categoria
				$pdf->ln(8);
				$pdf->SetFont('Arial','B',10);
				
				//Verifica se a categoria não tem o id 0 (devido as versões antigas do sistema)
				if ($dados_categoria['categoria_id'] == 0) 
				{
					
					$nome_categoria = 'Produto sem Centro de Custo definido';
				
				} 
				
				else 
				
				{
					
					$nome_categoria = $dados_categoria['categoria_nome'];
				
				}
				
				$pdf->Cell(6,4, $nome_categoria);		
		
				//Monta a query de filtragem dos itens
				$filtra_item = "SELECT 
								ite.id,
								ite.nome,
								ite.unidade,											
								cat.nome as categoria_nome,
								eve.quantidade,
								eve.valor_venda,
								eve.observacoes
								FROM item_evento ite
								LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
								INNER JOIN eventos_item eve ON eve.item_id = ite.id
								WHERE eve.evento_id = '$EventoId'
								AND ite.categoria_id = '$dados_categoria[categoria_id]'
								ORDER BY cat.nome, ite.nome";
				
				//Executa a query
				$lista_item = mysql_query($filtra_item);
				 
				//Cria um contador com o número de contar que a query retornou
				$nro_item = mysql_num_rows($lista_item);	
				
				//Zera a variável de total do custo
				$valor_categoria = 0;
				
				//Percorre o array 
				while ($dados_item = mysql_fetch_array($lista_item))
				{
				
					//Define a variável do valor total do item
					$total_item = $dados_item[quantidade] * $dados_item[valor_venda];
		
					//Imprime os dados do item
					$pdf->ln();	  
					$pdf->SetFont('Arial','B',9);
					$pdf->SetX(15);
					$pdf->Cell(13,4, $dados_item['quantidade'],'0','0','R');
					$pdf->SetX(28);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(70,4, $dados_item['unidade']);		  
					$pdf->SetX(34);
					$pdf->Cell(100,4, $dados_item['nome']);
					$pdf->SetX(106);
					$pdf->SetFont('Arial','',8);
					$pdf->MultiCell(0,3, $dados_item['observacoes'],0);
		
					//Incrementa o contador do valor
					$valor_categoria = $valor_categoria + $total_item;
					
					//Verifica se deve imprimir os materiais do item
					if ($ImprimeMateriais == 1) 
					{
					
						//Verifica se existe arvore de material cadastrada para o item
						$sql_material_arvore = "SELECT 
												mat.quantidade,
												item.nome as material_nome
												item.uniade
												FROM item_evento_composicao mat
												INNER JOIN item_evento item ON item.id = mat.material_id
												WHERE item_id = '$dados_item[id]'";

						//Executa a query																		
						$query_material_arvore = mysql_query($sql_material_arvore);
						
						$registros_arvore = mysql_num_rows($query_material_arvore);
						
						if ($registros_arvore > 0)
						{
					
							$dados_arvore = mysql_fetch_array($query_material_arvore);
							
							//Imprime os dados dos material
							$pdf->ln();	  
							$pdf->SetX(15);
							$pdf->SetFont('Arial','',9);
							$pdf->Cell(100,4, $dados_arvore[material_nome]);
							$pdf->SetX(106);
							$pdf->SetFont('Arial','',8);
							$pdf->Cell(0,4, $dados_arvore[unidade]);					
					
						}
					
					}

				}
			
			}
			
			$pdf->ln();
			
		}
	
	//Fecha o if se NÃO deve imprimir os detalhes financeiros
	} 
	
	else 
	
	{
		
		//*** Exibe os itens do evento
		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->SetFillColor(178,178,178);
		$pdf->Cell(0,6, 'Produtos do Evento',1,0,'C',1);
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
		if ($registros == 0 ) 
		{
			
			//Exibe a mensagem que não foram encontrados registros
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,6, 'Não há produtos cadastrados para este evento !',1,0,'L');
		
		//Caso tiver itens no banco
		} 
		
		else 
		
		{
		
			//Define a cor RGB do fundo da celula
			$pdf->SetFillColor(178,178,178);
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,6, '    Quant',1,0,'L');
			$pdf->SetX(28);
			$pdf->Cell(0,6, 'Un');
			$pdf->SetX(34);
			$pdf->Cell(0,6, 'Descrição dos Produtos');
			$pdf->SetX(110);
			$pdf->Cell(15,6, 'Custo','0','0','R');		
			$pdf->SetX(126);
			$pdf->Cell(0,6, 'Observações');
			
			//Monta a query para capturar as categorias que existem cadastrados itens
			$sql_categoria = mysql_query("SELECT 
										ite.id,
										ite.categoria_id,											
										cat.nome as categoria_nome,
										eve.valor_venda
										FROM item_evento ite
										LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
										INNER JOIN eventos_item eve ON eve.item_id = ite.id
										WHERE eve.evento_id = '$EventoId'											
										GROUP BY cat.nome
										ORDER BY cat.nome");
		
			
			//Percorre o array das funcoes
			while ($dados_categoria = mysql_fetch_array($sql_categoria))
			{
				
				//Imprime o nome da categoria
				$pdf->ln(8);
				$pdf->SetFont('Arial','B',10);
				
				//Verifica se a categoria não tem o id 0 (devido as versões antigas do sistema)
				if ($dados_categoria['categoria_id'] == 0) 
				{
					
					$nome_categoria = 'Sem categoria definida';
				
				} 
				
				else 
				
				{
					
					$nome_categoria = $dados_categoria['categoria_nome'];
				
				}
				
				$pdf->Cell(6,4, $nome_categoria);		
		
				//Monta a query de filtragem dos itens
				$filtra_item = "SELECT 
								ite.id,
								ite.nome,
								ite.unidade,											
								cat.nome as categoria_nome,
								eve.quantidade,
								eve.valor_venda,
								eve.observacoes
								FROM item_evento ite
								LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
								INNER JOIN eventos_item eve ON eve.item_id = ite.id
								WHERE eve.evento_id = '$EventoId'
								AND ite.categoria_id = '$dados_categoria[categoria_id]'
								ORDER BY cat.nome, ite.nome";
				
				//Executa a query
				$lista_item = mysql_query($filtra_item);
				 
				//Cria um contador com o número de contar que a query retornou
				$nro_item = mysql_num_rows($lista_item);	
				
				//Zera a variável de total do custo
				$valor_categoria = 0;			
				
				//Percorre o array 
				while ($dados_item = mysql_fetch_array($lista_item))
				{
									
					//Define a variável do valor total do item
					$total_item = $dados_item[quantidade] * $dados_item[valor_venda];
				
					//Imprime os dados dos itens
					$pdf->ln();
					$pdf->SetFont('Arial','B',9);
					$pdf->SetX(15);
					$pdf->Cell(13,4, $dados_item['quantidade'],'0','0','R');
					$pdf->SetX(28);
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(70,4, $dados_item['unidade']);		  
					$pdf->SetX(34);
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(100,4, $dados_item['nome']);
					$pdf->SetX(110);
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(16,4, number_format($total_item, 2, ',', '.'),'0','0','R');		  
					$pdf->SetX(126);
					$pdf->SetFont('Arial','',8);
					$pdf->MultiCell(0,3, $dados_item['observacoes'],0);
			
					//Incrementa o contador do valor
					$valor_categoria = $valor_categoria + $total_item;
					
					//Verifica se deve imprimir os materiais do item
					if ($ImprimeMateriais == 1) 
					{
					
						//Verifica se existe arvore de material cadastrada para o item
						$sql_material_arvore = "SELECT 
												mat.quantidade,
												mat.material_id,
												item.nome as material_nome,
												item.unidade
												FROM item_evento_composicao mat
												INNER JOIN item_evento item ON item.id = mat.material_id
												WHERE item_id = '$dados_item[id]'";

						//Executa a query																		
						$query_material_arvore = mysql_query($sql_material_arvore);
						
						$registros_arvore = mysql_num_rows($query_material_arvore);
						
						if ($registros_arvore > 0)
						{
					
							while ($dados_arvore = mysql_fetch_array($query_material_arvore)) 
							{
							
								//Pesquisa se não existe quantidade diferente para o item do evento em especial
								$procura_material_evento = "SELECT quantidade FROM eventos_item_composicao 
															WHERE evento_id = '$EventoId'
															AND item_id = $dados_item[id]
															AND material_id = $dados_arvore[material_id]";
								
								$query_procura_material_evento = mysql_query($procura_material_evento);
								
								$registros_procura_material_evento = mysql_num_rows($query_procura_material_evento);
							
								if ($registros_procura_material_evento > 0) 
								{
									
									$dados_material = mysql_fetch_array($query_procura_material_evento);
									$valor_material = $dados_material[quantidade];
								
								} 
								
								else 
								
								{
									
									$valor_material = $dados_arvore[quantidade];	
								
								}
								
								//Imprime os dados dos material
								$pdf->ln();	  
								$pdf->SetX(28);
								$pdf->SetFont('Arial','',8);
								$pdf->Cell(20,4, $valor_material,'0','0','R');
								$pdf->SetX(48);
								$pdf->Cell(0,4, $dados_arvore[unidade]);					
								$pdf->SetX(55);
								$pdf->Cell(100,4, $dados_arvore[material_nome]);
					
							}
						
						}
					
					}
					
				}	
	
				//Imprime o total de itens da categoria
				$pdf->ln();
				$pdf->SetFont('Arial','I',9);
				$pdf->SetX(66);
				$pdf->Cell(60,4, 'Subtotal da categoria: R$ ' . number_format($valor_categoria, 2, ',', '.'),'T','0','R');		

				//Incrmenta o valor do total dos itens ao total geral
				$valor_geral = $valor_geral + $valor_categoria;
		
			}
		
			//Imprime o total de itens do evento
			$pdf->ln(8);
			$pdf->SetFont('Arial','BI',11);
			$pdf->Cell(0,6, 'Total dos Produtos do evento: R$ ' . number_format($valor_geral, 2, ',', '.'),'1');
			$pdf->ln(4);
		
		}		
		
	//Fecha o if se deve imprimir os detalhes financeiros	
	}

//Fecha o if se deve imprimir os itens do evento
}


//Verifica se deve imprimir os dados dos serviços do evento
if ($ImprimeServicos == 1) 
{

	//Verifica se deve imprimir os dados financeiros dos serviços evento
	if ($ImprimeValoresServicos == 0) 
	{
	
		//*** Exibe os serviços do evento
		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->SetFillColor(178,178,178);
		$pdf->Cell(0,6, 'Serviços do Evento',1,0,'C',1);
		$pdf->ln();
		
		//Efetua a pesquisa dos itens da base de dados geral
		//Monta a query de filtragem dos servicos
		$filtra_servico = "SELECT 
							id,
							nome
							FROM servico_evento 
							WHERE ativo = '1' 
							AND empresa_id = $EmpresaId
							ORDER BY nome";
		
		//Executa a query
		$lista_servico = mysql_query($filtra_servico);
		
		//Cria um contador com o número de contar que a query retornou
		$registros = mysql_num_rows($lista_servico);
		
		//Verifica a quantidade de registros
		if ($registros == 0 ) 
		{
			
			//Exibe a mensagem que não foram encontrados registros
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,6, 'Não há serviços cadastrados para este evento !',1,0,'L');
		
		//Caso tiver serviços no banco
		} 
		
		else 
		
		{
			
			//Define a cor RGB do fundo da celula
			$pdf->SetFillColor(178,178,178);
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,6, '    Quant',1,0,'L');
			$pdf->SetX(34);
			$pdf->Cell(0,6, '     Descrição do Serviço:','1');
			$pdf->SetX(106);
			$pdf->Cell(0,6, 'Observações:');
	
	
			
			//Monta a query para capturar as categorias que existem cadastrados itens
			$sql_categoria = mysql_query("SELECT 
											serv.id,
											serv.categoria_id,											
											cat.nome as categoria_nome,
											eve.valor_venda
											FROM servico_evento serv
											LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
											INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
											WHERE eve.evento_id = '$EventoId'											
											GROUP BY cat.nome
											ORDER BY cat.nome");
		
			
			//Percorre o array das funcoes
			while ($dados_categoria = mysql_fetch_array($sql_categoria))
			{
		
				//Imprime o nome da categoria
				$pdf->ln(8);
				$pdf->SetFont('Arial','B',10);
				
				//Verifica se a categoria não tem o id 0 (devido as versões antigas do sistema)
				if ($dados_categoria['categoria_id'] == 0) 
				{
					
					$nome_categoria = 'Sem categoria definida';
				
				}
				
				else 
				{
					
					$nome_categoria = $dados_categoria['categoria_nome'];
				
				}
				
				$pdf->Cell(6,4, $nome_categoria);		
		
				//Monta a query de filtragem dos serviços
				$filtra_servico = "SELECT 
									serv.id,
									serv.nome,											
									cat.nome as categoria_nome,
									eve.quantidade,
									eve.valor_venda,
									eve.observacoes
									FROM servico_evento serv
									LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
									INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
									WHERE eve.evento_id = '$EventoId'
									AND serv.categoria_id = '$dados_categoria[categoria_id]'
									ORDER BY cat.nome, serv.nome";
				
				//Executa a query
				$lista_servico = mysql_query($filtra_servico);
				 
				//Cria um contador com o número de contar que a query retornou
				$nro_servico = mysql_num_rows($lista_servico);	
				
				//Zera a variável de total do custo
				$valor_categoria = 0;
				
				//Percorre o array 
				while ($dados_servico = mysql_fetch_array($lista_servico))
				{
				
					//Define a variável do valor total do serviço
					$total_servico = $dados_servico[quantidade] * $dados_servico[valor_venda];
		
					//Imprime os dados do item
					$pdf->ln();	  
					$pdf->SetFont('Arial','B',9);
					$pdf->SetX(15);
					$pdf->Cell(13,4, $dados_servico['quantidade'],'0','0','R');
					$pdf->SetX(34);
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(100,4, $dados_servico['nome']);
					$pdf->SetX(106);
					$pdf->SetFont('Arial','',8);
					$pdf->MultiCell(20,3, $dados_servico['observacoes'],0);

					//Incrementa o contador do valor
					$valor_categoria = $valor_categoria + $total_servico;					
				
				}
			
			//Fecha o while das categorias
			}
			
			$pdf->ln();
			
		}
	
	//Fecha o if se NÃO deve imprimir os detalhes financeiros
	} 
	
	else 
	
	{
		
		//*** Exibe os serviços do evento
		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->SetFillColor(178,178,178);
		$pdf->Cell(0,6, 'Serviços do Evento',1,0,'C',1);
		$pdf->ln();
		
		//Efetua a pesquisa dos serviços da base de dados geral
		//Monta a query de filtragem dos serviços
		$filtra_servico = "SELECT 
							id,
							nome
							FROM servico_evento 
							WHERE ativo = '1' 
							AND empresa_id = $EmpresaId
							ORDER BY nome";
		
		//Executa a query
		$lista_servico = mysql_query($filtra_servico);
		
		//Cria um contador com o número de contar que a query retornou
		$registros = mysql_num_rows($lista_servico);
		
		//Verifica a quantidade de registros
		if ($registros == 0 ) 
		{
			
			//Exibe a mensagem que não foram encontrados registros
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,6, 'Não há serviços cadastrados para este evento !',1,0,'L');
		
		//Caso tiver servicos no banco
		} 
		
		else 
		
		{
			
			//Define a cor RGB do fundo da celula
			$pdf->SetFillColor(178,178,178);
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,6, '    Quant',1,0,'L');
			$pdf->SetX(34);
			$pdf->Cell(0,6, 'Descrição dos Serviços');
			$pdf->SetX(110);
			$pdf->Cell(15,6, 'Custo','0','0','R');		
			$pdf->SetX(126);
			$pdf->Cell(0,6, 'Observações');
			
			//Monta a query para capturar as categorias que existem cadastrados itens
			$sql_categoria = mysql_query("SELECT 
											serv.id,
											serv.categoria_id,											
											cat.nome as categoria_nome,
											eve.valor_venda
											FROM servico_evento serv
											LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
											INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
											WHERE eve.evento_id = '$EventoId'											
											GROUP BY cat.nome
											ORDER BY cat.nome");
		
			
			//Percorre o array das funcoes
			while ($dados_categoria = mysql_fetch_array($sql_categoria))
			{
				
				//Imprime o nome da categoria
				$pdf->ln(8);
				$pdf->SetFont('Arial','B',10);
				
				//Verifica se a categoria não tem o id 0 (devido as versões antigas do sistema)
				if ($dados_categoria['categoria_id'] == 0) 
				{
						
						$nome_categoria = 'Sem categoria definida';
				
				} 
				
				else 
				
				{
					
					$nome_categoria = $dados_categoria['categoria_nome'];
				
				}
				
				$pdf->Cell(6,4, $nome_categoria);		
		
				//Monta a query de filtragem dos servicos
				$filtra_servico = "SELECT 
									serv.id,
									serv.nome,
									cat.nome as categoria_nome,
									eve.quantidade,
									eve.valor_venda,
									eve.observacoes
									FROM servico_evento serv
									LEFT OUTER JOIN categoria_servico cat ON cat.id = serv.categoria_id
									INNER JOIN eventos_servico eve ON eve.servico_id = serv.id
									WHERE eve.evento_id = '$EventoId'
									AND serv.categoria_id = '$dados_categoria[categoria_id]'
									ORDER BY cat.nome, serv.nome";
				
				//Executa a query
				$lista_servico = mysql_query($filtra_servico);
				 
				//Cria um contador com o número de contar que a query retornou
				$nro_servico = mysql_num_rows($lista_servico);	
				
				//Zera a variável de total do custo
				$valor_categoria = 0;			
				
				//Percorre o array 
				while ($dados_servico = mysql_fetch_array($lista_servico))
				{
									
					//Define a variável do valor total do servico
					$total_servico = $dados_servico[quantidade] * $dados_servico[valor_venda];
				
					//Imprime os dados dos servicos
					$pdf->ln();
					$pdf->SetFont('Arial','B',9);
					$pdf->SetX(15);
					$pdf->Cell(13,4, $dados_servico['quantidade'],'0','0','R');
					$pdf->SetX(34);
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(100,4, $dados_servico['nome']);
					$pdf->SetX(110);
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(16,4, number_format($total_servico, 2, ',', '.'),'0','0','R');		  
					$pdf->SetX(126);
					$pdf->SetFont('Arial','',8);
					$pdf->MultiCell(20,3, $dados_servico['observacoes'],0);

					//Incrementa o contador do valor
					$valor_categoria = $valor_categoria + $total_servico;										
							
				//Fecha o while dos itens da categoria
				}	
	
				//Imprime o total de servicos da categoria
				$pdf->ln();
				$pdf->SetFont('Arial','I',9);
				$pdf->SetX(66);
				$pdf->Cell(60,4, 'Subtotal da categoria: R$ ' . number_format($valor_categoria, 2, ',', '.'),'T','0','R');		

				//Incrmenta o valor do total dos serviços ao total geral
				$valor_geral = $valor_geral + $valor_categoria;
		
			//Fecha o while das categorias
			}
		
			//Imprime o total de serviços do evento
			$pdf->ln(8);
			$pdf->SetFont('Arial','BI',11);
			$pdf->Cell(0,6, 'Total dos Serviços do Evento: R$ ' . number_format($valor_geral, 2, ',', '.'),'1');
			$pdf->ln(4);
		
		//Fecha o if de se tiver serviços
		}		
		
	//Fecha o if se deve imprimir os detalhes financeiros	
	}

//Fecha o if se deve imprimir os servicos do evento
}


//Verifica se deve imprimir os terceiros do evento
if ($ImprimeTerceiros == 1) {

	//*** Exibe os terceiros do evento
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, 'Terceiros do Evento',1,0,'C',1);
	$pdf->ln();
	
	//verifica os participantes já cadastrados para este evento e exibe na tela
	$sql_terceiro = mysql_query("SELECT
								 ter.id,
								 ter.fornecedor_id,
								 ter.servico_contratado,
								 ter.custo,
								 ter.valor_venda,
								 ter.observacoes,
								 ter.status_contrato,
								 forn.nome as fornecedor_nome
								 FROM eventos_terceiro ter
								 LEFT OUTER JOIN fornecedores forn ON forn.id = ter.fornecedor_id
								 WHERE ter.evento_id = '$EventoId'
								 ORDER by ter.status_contrato, forn.nome");
	
	//Verifica o numero de registros retornados
	$registros_terc = mysql_num_rows($sql_terceiro);
	
	//Verifica a quantidade de registros
	if ($registros_terc == 0 ) 
	{
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Não há terceiros cadastrados para este evento !',1,0,'L');
	
	//Caso tiver
	} 
	
	else 
	
	{
		
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Terceiro/Fornecedor:',1,0,'L');
		$pdf->SetX(100);
		$pdf->Cell(0,6, 'Serviço Prestado:');
		$pdf->SetX(177);
		$pdf->Cell(0,6, 'Valor Venda:');		
	
		$valor_geral = 0;
		
		$pdf->ln(7);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(6,4, 'A Contratar');
		
		$valor_quebra = 1;
		
	
		//Percorre o array 
		while ($dados_terceiro = mysql_fetch_array($sql_terceiro))
		{
	
			if($dados_terceiro[status_contrato] != $valor_quebra)
			{
				
				if ($dados_terceiro[status_contrato] == 2)
				{
        
					$pdf->ln(7);
					$pdf->SetFont('Arial','B',11);
					$pdf->Cell(6,4, 'Contratado');

					$valor_quebra = 2;
		  
				} 
				
				else if ($dados_terceiro[status_contrato] == 3)
				
				{
                
					$pdf->ln(7);
					$pdf->SetFont('Arial','B',11);
					$pdf->Cell(6,4, 'Cancelado');
								
					$valor_quebra = 3;
                
				}
      	
			}
	
			//Imprime os dados dos contatos
			$pdf->ln();
			$pdf->SetFont('Arial','B',9);
			$pdf->SetX(15);
			$pdf->Cell(6,4, $dados_terceiro['fornecedor_nome']);
			$pdf->SetX(100);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(100,4, $dados_terceiro['servico_contratado']);
			$pdf->SetX(177);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(21,4, 'R$: ' . number_format($dados_terceiro['valor_venda'], 2, ',', '.'),'0','0','R');	  

			//Incrmenta o valor do total dos terceiros ao total geral
			$valor_geral = $valor_geral + $dados_terceiro['valor_venda'];
	
		//Fecha o while
		}	

		//Imprime o total de serviços do evento
		$pdf->ln();
		$pdf->SetFont('Arial','BI',11);
		$pdf->Cell(0,6, 'Total dos Terceiros do Evento: R$ ' . number_format($valor_geral, 2, ',', '.'),'1');	
	
	//Fecha o If
	}
	
	$pdf->ln(10);

//Fecha o if de imprimir os terceiros
}	

//Verifica se deve imprimir os dados dos itens do evento
if ($ImprimeBrindes == 1) 
{
	
	//*** Exibe os itens do evento
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, 'Brindes do Evento',1,0,'C',1);
	$pdf->ln();
		
	//Monta a query de filtragem dos brindes
	$filtra_brinde = "SELECT 
						bri.brinde_id,
						bri.quantidade,
						bri.observacoes,
						brinde.nome as brinde_nome
						FROM eventos_brinde bri
						LEFT OUTER JOIN brindes brinde ON brinde.id = bri.brinde_id
						WHERE bri.evento_id = '$EventoId'
						ORDER BY brinde.nome";
		
	//Executa a query
	$lista_brinde = mysql_query($filtra_brinde);
		
	//Cria um contador com o número de contar que a query retornou
	$registros = mysql_num_rows($lista_brinde);
		
	//Verifica a quantidade de registros
	if ($registros == 0 ) 
	{
		
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Não há brindes cadastrados para este evento !',1,0,'L');
		$pdf->ln();

	//Caso tiver itens no banco
	} 
	
	else 
	
	{

			//Define a cor RGB do fundo da celula
			$pdf->SetFillColor(178,178,178);
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(0,6, '    Quant',1,0,'L');
			$pdf->SetX(28);
			$pdf->Cell(0,6, 'Descrição dos Brindes');
			$pdf->SetX(110);
			$pdf->Cell(0,6, 'Observações');												
				
			//Percorre o array 
			while ($dados_brinde = mysql_fetch_array($lista_brinde))
			{									
				
			  	//Imprime os dados dos itens
				$pdf->ln();
				$pdf->SetFont('Arial','B',9);
				$pdf->SetX(15);
				$pdf->Cell(13,4, $dados_brinde['quantidade'],'0','0','R');
				$pdf->SetX(28);
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(100,4, $dados_brinde['brinde_nome']);
				$pdf->SetX(110);
				$pdf->SetFont('Arial','',8);
				$pdf->MultiCell(0,3, $dados_brinde['observacoes'],0);			
					
			}											
		
		//Fecha o if de se tiver itens
		}				

		$pdf->ln();
		
//Fecha o if se deve imprimir os brindes do evento
}

//Verifica se deve imprimir o repertório musical do evento
if ($ImprimeRepertorio == 1) 
{
	
	//*** Exibe o repertório do evento
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, 'Repertório Musical do Evento',1,0,'C',1);

	//Monta um sql para pesquisar se há repertório para este evento
	$sql_conta_rep = mysql_query("SELECT id
								 FROM eventos_repertorio
								 WHERE evento_id = '$EventoId' ");

	//Verifica o numero de registros retornados																	 
	$registros = mysql_num_rows($sql_conta_rep);

	//Verifica as categorias cadastradas para o evento
	$sql_conta_categorias = mysql_query("SELECT 
										rep.id, 
										cat.id as categoria_id,
										cat.nome as categoria_nome
										FROM eventos_repertorio rep
										INNER JOIN categoria_repertorio cat ON cat.id = rep.categoria_repertorio_id
										WHERE rep.evento_id = '$EventoId'
										GROUP BY rep.categoria_repertorio_id");																		
	
	
	//Verifica a quantidade de registros
	if ($registros == 0 ) 
	{
		
		//Exibe a mensagem que não foram encontrados registros
		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Não há repertório musical cadastrado para este evento !',1,0,'L');
		$pdf->ln();
		
	//Caso tiver
	} 
	
	else 
	
	{

		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->ln();
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, '     Música:',1,0,'L');
		$pdf->SetX(110);
		$pdf->Cell(0,6, 'Intérprete:');
		$pdf->ln();
		
		//Cria o array e o percorre para montar a listagem das categorias
		while ($dados_conta_categoria = mysql_fetch_array($sql_conta_categorias))
		{
    
			//Imprime o nome da categoria

			$pdf->SetFont('Arial','B',10);
			
			//Verifica se a categoria não tem o id 0 (devido as versões antigas do sistema)
			if ($dados_conta_categoria['categoria_id'] == 0) 
			{
				
				$nome_conta_categoria = 'Sem categoria definida';
			
			} 
			
			else 
			
			{
				
				$nome_conta_categoria = $dados_conta_categoria['categoria_nome'];
			
			}
			
			$pdf->Cell(6,4, $nome_conta_categoria);	
			
			//Monta a pesquisa das musicas listadas na categoria
			$sql_musica = mysql_query("SELECT
										 rep.id,
										 mus.nome as musica_nome,
										 mus.interprete as musica_interprete
										 FROM eventos_repertorio rep
										 LEFT OUTER JOIN musicas mus ON mus.id = rep.musica_id
										 WHERE rep.evento_id = '$EventoId' AND rep.categoria_repertorio_id = $dados_conta_categoria[categoria_id]
										 ");		
			
	
			//Cria o array e o percorre para montar a listagem dinamicamente
			while ($dados_musica = mysql_fetch_array($sql_musica))
			{
	    	
				//Imprime as musicas da categoria
				$pdf->ln();
				$pdf->SetX(14);
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(70,4, $dados_musica['musica_nome']);		  
				$pdf->SetX(110);
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(100,4, $dados_musica['musica_interprete']);
			
			//Fecha o while das musicas das categorias	    	
			}

			$pdf->ln(4);
		
		//Fecha o while das categorias
		}		

		//Salta uma linha pra não grudar na outra parte dos itens
		//$pdf->ln();	

	//Fecha o if
	}
	
//Fecha o if de se deve imprimir o repertório
}

//Verifica se deve imprimir os formandos do evento
if ($ImprimeFormandos == 1) 
{

	//*** Exibe os formandos do evento
	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, 'Formandos do Evento',1,0,'C',1);
	$pdf->ln();
	
	//verifica os formandos já cadastrados para este evento e exibe na tela
	$sql_formando = mysql_query("SELECT * FROM eventos_formando 
								 WHERE evento_id = '$EventoId'
								 ORDER by nome");
	
	//Verifica o numero de registros retornados
	$registros_part = mysql_num_rows($sql_formando);
	
	//Verifica a quantidade de registros
	if ($registros_part == 0 ) 
	{
		
		//Exibe a mensagem que não foram encontrados registros
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Não há formandos cadastrados para este evento !',1,0,'L');
	
	//Caso tiver
	} 
	
	else 
	{
		
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(0,6, 'Nome do Formand:',1,0,'L');
		$pdf->SetX(72);
		$pdf->Cell(0,6, 'Part');
		$pdf->SetX(80);
		$pdf->Cell(0,6, 'Telefone');
		$pdf->SetX(100);
		$pdf->Cell(0,6, 'Email');
		$pdf->SetX(146);
		$pdf->Cell(0,6, 'Observações');		
	
		$numero_formandos = 0;
		//Percorre o array 
		while ($dados_formando = mysql_fetch_array($sql_formando))
		{
      
			//Efetua o switch para o campo de participante
			switch ($dados_formando[participante]) 
			{
			  
				case 0: $desc_participante = ' '; break;
				case 1: $desc_participante = 'C/B'; break;
				case 2: $desc_participante = 'C'; break;
				case 3: $desc_participante = 'B'; break;
			}
	
			//Imprime os dados dos contatos
			$pdf->ln();
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(6,4, $dados_formando['nome']);		  
			$pdf->SetFont('Arial','',8);
			$pdf->SetX(72);
			$pdf->Cell(100,4, $desc_participante);
			$pdf->SetX(80);
			$pdf->Cell(100,4, $dados_formando['contato']);
			$pdf->SetX(100);
			$pdf->Cell(100,4, $dados_formando['email']);
			$pdf->SetX(146);
			$pdf->MultiCell(0,3, $dados_formando['observacoes'],0);					  

			$numero_formandos++;
		
		//Fecha o while
		}	

		//Imprime o total de serviços do evento
		$pdf->ln();
		$pdf->SetFont('Arial','BI',9);
		$pdf->Cell(0,6, 'Total de Formandos no Evento: ' . $numero_formandos,'1');
	
	//Fecha o If
	}
	
	$pdf->ln();

//Fecha o if de imprimir os formandos
}	


//Gera o PDF
$pdf->Output();

?>                                                      