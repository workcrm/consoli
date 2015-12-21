<?php
###########
## Módulo para montagem do relatório de Planilha de Foto e Vídeo em PDF
## Criado: 22/10/2008 - Maycon Edinger
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
$TipoRelatorio = $_GET['TipoRelatorio'];

//Recupera dos dados do evento
$sql_evento = "SELECT 
			eve.id,
			eve.nome,
			eve.descricao,
			eve.status,
			eve.cliente_id,
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
			eve.data_realizacao,
			eve.hora_realizacao,
			eve.duracao,
			eve.observacoes,
			eve.desconto_comissao,
			eve.comissao_avista,
			eve.comissao_30,
			eve.comissao_60,
			eve.vendedor_id,
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
			cid.nome as cliente_cidade,
			col.nome as colaborador_nome
			FROM eventos eve 
			INNER JOIN clientes cli ON cli.id = eve.cliente_id
			LEFT OUTER JOIN cidades cid ON cid.id = cli.cidade_id
			LEFT OUTER JOIN colaboradores col ON col.id = eve.vendedor_id
			WHERE eve.id = $EventoId";

//Executa a query de consulta
$query_evento = mysql_query($sql_evento);

//Monta a matriz com os dados
$dados_evento = mysql_fetch_array($query_evento);

//Efetua o switch para o campo de status
switch ($dados_evento[status]) 
{
	case 0: $desc_status = 'Em orçamento'; break;
	case 1: $desc_status = 'Em aberto'; break;
	case 2: $desc_status = 'Realizado'; break;
	case 3: $desc_status = 'Não-Realizado'; break;
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
		$this->Cell(0,6,'Planilha Controle de Foto e Vídeo do Evento',0,0,'R');
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
$pdf->SetTitle('Planilha Controle de Foto e Vídeo do Evento');
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
$pdf->Cell(0,6, 'Responsável:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(35);
$pdf->Cell(0,6, $dados_evento['responsavel'],0,0,'L');

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0,16, 'Contatos:',1,0,'L');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX(35);
$pdf->Cell(0,4, 'Nome',0,0,'L');
$pdf->SetX(95);
$pdf->Cell(0,4, 'Observações',0,0,'L');
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

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60,6, 'Data:',1,0,'L');
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetX(35);
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

//Nova linha
$pdf->ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, 'Informações Complementares',1,0,'C',1);
$pdf->ln();
$pdf->SetFont('Arial', 'I', 10);
$pdf->MultiCell(0,4, $dados_evento['observacoes'],1);

//Verifica se deve imprimir completo 
if ($TipoRelatorio == 1)
{

	$pdf->ln();
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetFillColor(178,178,178);
	$pdf->Cell(0,6, 'Formandos do Evento X Consumo de Produtos de Foto e Vídeo',1,0,'C',1);
	$pdf->ln();
	
	//verifica os formandos já cadastrados para este evento
	$sql_formando = mysql_query("SELECT 
								id, 
								nome, 
								obs_compra, 
								data_venda, 
								data_envio_lab, 
								data_retorno_lab, 
								data_entrega_cliente, 
								data_envio_cliente 
								FROM eventos_formando 
								WHERE evento_id = $EventoId 
								AND status < 3
								ORDER by nome");
	
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
		
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(57,6, 'Nome do Formando/Produto:',1,0,'L',1);
		$pdf->SetFont('Arial', 'B', 7);	
		$pdf->Cell(11,6, 'Qt Disp',1,0,'R',1);		
		$pdf->Cell(16,6, 'Vl. Venda',1,0,'R',1);
		$pdf->Cell(14,6, 'Qt Venda',1,0,'R',1);
		$pdf->Cell(13,6, 'Qt Brinde',1,0,'R',1);
		$pdf->Cell(14,6, 'Desconto',1,0,'R',1);
		$pdf->Cell(16,6, 'Valor Total',1,0,'R',1);
		$pdf->Cell(13,6, 'Estoque',1,0,'R',1);
		$pdf->Cell(10,6, '% Com',1,0,'R',1);
		$pdf->Cell(13,6, 'Bonus C.',1,0,'R',1);
		$pdf->Cell(13,6, 'Vl. Com.',1,0,'R',1);
		
		$total_geral_comissao_formando = 0;
		$formandos_atendidos = 0;
		
		//zera a variável de marcar o formando
		$marca_formando = 0;
		
		//Percorre o array 
		while ($dados_formando = mysql_fetch_array($sql_formando))
		{
	
			$total_formando = 0;
			$total_comissao_formando = 0;
			
			//Imprime os dados do formando
			$pdf->ln(8);
			$pdf->SetFont('Arial','B',9);
			$pdf->SetFillColor(178,178,178);
			$pdf->Cell(0,4, $dados_formando['nome'],1,0,'L',1);
			
			//Pesquisa os itens que o formando adquiriu
			$sql_produto = mysql_query("SELECT 
										item.quantidade_disponivel,
										item.valor_venda,
										item.comissao,
										item.bonus_comissao,
										item.quantidade_venda,
										item.quantidade_brinde,
										item.valor_desconto,
										centro.nome as item_nome																
										FROM eventos_fotovideo item
										LEFT OUTER JOIN categoria_fotovideo centro ON centro.id = item.item_id
										WHERE item.evento_id = $EventoId
										AND item.formando_id = '$dados_formando[id]'
										ORDER by centro.nome");
																	
			
			//Conta se retornou algum registro
			$conta_retorno = mysql_num_rows($sql_produto);
			
			if ($conta_retorno >0) {					
			
				$formandos_atendidos++;
			
			}
					
																	
			//Percorre o array 
			while ($dados_item = mysql_fetch_array($sql_produto))
			{
			
				//Imprime os dados do item
				$pdf->ln();
				$pdf->SetFont('Arial','B',9);	
				$pdf->Cell(57,4, '     ' . $dados_item['item_nome'],1);	
		
				$pdf->SetFont('Arial','',8);	
				$pdf->Cell(11,4, $dados_item['quantidade_disponivel'],1,0,'R');
				$pdf->Cell(16,4, number_format($dados_item['valor_venda'], 2, ',', '.'),1,0,'R');
				$pdf->Cell(14,4, $dados_item['quantidade_venda'],1,0,'R');
				$pdf->Cell(13,4, $dados_item['quantidade_brinde'],1,0,'R');
				$pdf->Cell(14,4, $dados_item['valor_desconto'],1,0,'R');
				
				//Define a variável com o valor da venda
				$valor_venda = number_format(($dados_item['valor_venda'] * $dados_item['quantidade_venda']) - $dados_item['valor_desconto'], 2, ',', '.');
				
				$pdf->Cell(16,4, $valor_venda,1,0,'R');
				
				//Define a variável de quantidade de saldo do item
				$saldo_item = $dados_item['quantidade_disponivel'] - $dados_item['quantidade_venda'] - $dados_item['quantidade_brinde'];
				
				$pdf->Cell(13,4, $saldo_item,1,0,'R');
				
				//Alimenta a variável de total do formando
				$total_formando = $total_formando + (($dados_item['valor_venda'] * $dados_item['quantidade_venda']) - $dados_item['valor_desconto']);
				
				//Imprime o percentual de comissão
				$pdf->Cell(10,4, $dados_item['comissao'],1,0,'R');
				
				//Imprime o percentual de comissão
				$pdf->Cell(13,4, number_format($dados_item['bonus_comissao'], 2, ',', '.'),1,0,'R');
				
				$total_comissao_formando = (((($dados_item['valor_venda'] * $dados_item['quantidade_venda']) - $dados_item['valor_desconto']) * ($dados_item['comissao'] / 100)) + $dados_item['bonus_comissao']);
			
				$formata_comissao_formando = number_format($total_comissao_formando, 2, ',', '.');
			
				//Imprime o valor da comissão	
				$pdf->Cell(13,4, $formata_comissao_formando,1,0,'R');
				
				$total_geral_comissao_formando = $total_geral_comissao_formando + $total_comissao_formando;
				
				$total_master_comissao_formando = $total_master_comissao_formando + $total_comissao_formando;
			
			}
			
			
	
			$pdf->ln();
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(42,4, 'Observações do Formando:',0,0,'L');
			$pdf->SetX(104);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(31,4, 'Total do Formando:',1,0,'R',1);
			$pdf->Cell(16,4, number_format($total_formando, 2, ',', '.') ,1,0,'R');
			$pdf->Cell(36,4, 'Total Comissão:',1,0,'R',1);
			$pdf->Cell(00,4, number_format($total_geral_comissao_formando, 2, ',', '.') ,1,0,'R');

			$total_geral_comissao_formando = 0;			
			
			$pdf->ln();
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->MultiCell(0,4, $dados_formando['obs_compra'],1);
    
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(20,4, 'Data Venda:',1,0,'L',1);
			$pdf->SetFont('Arial', '', 8);
       
			if ($dados_formando['data_venda'] != '0000-00-00')
			{
				
				$pdf->Cell(18,4, DataMySQLRetornar($dados_formando['data_venda']),1,0,'L');
			
			}
			
			else
			
			{
				
				$pdf->Cell(18,4, ' ',1,0,'L');
			}
   
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(20,4, 'Envio Lab:',1,0,'L',1);
			$pdf->SetFont('Arial', '', 8);
   
			if ($dados_formando['data_envio_lab'] != '0000-00-00')
			{
				$pdf->Cell(18,4, DataMySQLRetornar($dados_formando['data_envio_lab']),1,0,'L');
			}
			else
			{
				$pdf->Cell(18,4, ' ',1,0,'L');
			}
    
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(20,4, 'Retorno Lab:',1,0,'L',1);
			$pdf->SetFont('Arial', '', 8);
    
			if ($dados_formando['data_retorno_lab'] != '0000-00-00')
			{
				$pdf->Cell(18,4, DataMySQLRetornar($dados_formando['data_retorno_lab']),1,0,'L');
			}
			else
			{
				$pdf->Cell(18,4, ' ',1,0,'L');
			}
    
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(20,4, 'Entrega Cli:',1,0,'L',1);
			$pdf->SetFont('Arial', '', 8);
    
			if ($dados_formando['data_entrega_cliente'] != '0000-00-00')
			{
				$pdf->Cell(18,4, DataMySQLRetornar($dados_formando['data_entrega_cliente']),1,0,'L');
			}
			else
			{
				$pdf->Cell(18,4, ' ',1,0,'L');
			}
    
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(20,4, 'Envio Cli:',1,0,'L',1);
			$pdf->SetFont('Arial', '', 8);
    
			if ($dados_formando['data_envio_cliente'] != '0000-00-00')
			{
				$pdf->Cell(18,4, DataMySQLRetornar($dados_formando['data_envio_cliente']),1,0,'L');
			}
			else
			{
				$pdf->Cell(18,4, ' ',1,0,'L');
			}
    
		//Fecha o while
		}	
		
		//Imprime o total de formandos do evento
		$pdf->ln(6);
		$pdf->SetFont('Arial','BI',9);
		
		$formando_pendente = $registros - $formandos_atendidos;
		
		$pdf->Cell(0,5, 'Total de Formandos no Evento: ' . $registros . '  Atendidos: ' . $formandos_atendidos . '  Pendentes: ' . $formando_pendente,'1');	
		
		$pdf->ln(8);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->SetFillColor(178,178,178);
		$pdf->Cell(0,6, 'Gerenciamento de Estoques de Foto e Vídeo do Evento',1,0,'C',1);
		$pdf->ln();
	
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->Cell(55,6, 'Produto:',1,0,'L',1);
		$pdf->Cell(22,6, 'Qtd Total Disp.:',1,0,'R',1);		
		$pdf->Cell(22,6, 'Qtd Total Venda:',1,0,'R',1);
		$pdf->Cell(22,6, 'Qtd Total Brinde:',1,0,'R',1);
		$pdf->Cell(20,6, 'Total Desc.:',1,0,'R',1);
		$pdf->Cell(20,6, 'Total Venda:',1,0,'R',1);
		$pdf->Cell(15,6, 'Estoque:',1,0,'R',1);
		$pdf->Cell(14,6, '% Venda:',1,0,'R',1);
		$pdf->ln(2);
			
		//Pesquisa os itens que o formando adquiriu
		$sql_total = mysql_query("SELECT 
								sum( ite.quantidade_disponivel ) AS total_disponivel, 
								sum( ite.quantidade_venda ) AS total_venda,
								sum( (ite.quantidade_venda * ite.valor_venda) - ite.valor_desconto) AS total_item, 
								sum( ite.quantidade_brinde ) AS total_brinde,
								sum( ite.valor_desconto ) AS total_desconto,  
								prod.nome AS item_nome
								FROM eventos_fotovideo ite
								LEFT OUTER JOIN categoria_fotovideo prod ON prod.id = ite.item_id
								WHERE ite.evento_id = $EventoId
								GROUP BY ite.item_id
								ORDER by prod.nome");
		
		//Cria a variável de total geral
		$total_geral = 0;
																	
		//Percorre o array 
		while ($dados_total = mysql_fetch_array($sql_total))
		{
	
			//Imprime os dados do item
			$pdf->ln();
			$pdf->SetFont('Arial','B',9);	
			$pdf->Cell(55,4, $dados_total['item_nome'],1);	
			$pdf->Cell(22,4, $dados_total['total_disponivel'],1,0,'R');
			$pdf->Cell(22,4, $dados_total['total_venda'],1,0,'R');
			$pdf->Cell(22,4, $dados_total['total_brinde'],1,0,'R');
			$pdf->Cell(20,4, $dados_total['total_desconto'],1,0,'R');
			$pdf->Cell(20,4, $dados_total['total_item'],1,0,'R');
			
			
			$saldo_total = $dados_total['total_disponivel'] - $dados_total['total_venda'] - $dados_total['total_brinde'];
			
			$pdf->Cell(15,4, $saldo_total,1,0,'R');
			
			//Gambi para não gerar o erro de divisão por zero
			if ($dados_total['total_venda'] == 0)
			{
       
				$dados_total_venda = 1;
        
			}
      
			else
      
			{
        
				$dados_total_venda = $dados_total['total_venda'];
        
			}
      
			if ($dados_total['total_disponivel'] == 0)
			{
       
				$dados_total_disponivel = 1;
        
			}
      
			else
      
			{
        
				$dados_total_disponivel = $dados_total['total_disponivel'];
        
			}
      
			$pdf->Cell(14,4, round(($dados_total_venda / $dados_total_disponivel) * 100) . ' %',1,0,'R');
		
			//Alimenta a variável de total geral do evento
			$total_geral = $total_geral + $dados_total[total_item];	
		}
		
		$pdf->ln(6);
		$pdf->ln();
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(19,4, 'Vendedor:',1,0,'L',1);
		$pdf->Cell(63,4, $dados_evento[colaborador_nome],1,0,'L');		
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Total Geral do Evento:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($total_geral, 2, ',', '.'),1,0,'R');	
		$pdf->ln(8);
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Total de Comissões:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($total_master_comissao_formando, 2, ',', '.'),1,0,'R');
		$pdf->ln();
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Desconto de Comissões:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($dados_evento[desconto_comissao], 2, ',', '.'),1,0,'R');
		$pdf->ln();
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Total Geral de Comissões:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($total_master_comissao_formando - $dados_evento[desconto_comissao], 2, ',', '.'),1,0,'R');
		
		$pdf->ln(8);
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Valor pago a vista:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($dados_evento[comissao_avista], 2, ',', '.'),1,0,'R');
		$pdf->ln();
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Valor pago 30 dias:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($dados_evento[comissao_30], 2, ',', '.'),1,0,'R');
		$pdf->ln();
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Valor pago 60 dias:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($dados_evento[comissao_60], 2, ',', '.'),1,0,'R');
	
	
	//Fecha o If
	}

//Caso for tipo 2
} 

else if ($TipoRelatorio == 2)

{
	
	//verifica os formandos já cadastrados para este evento
	$sql_formando = mysql_query("SELECT id, nome, obs_compra FROM eventos_formando WHERE evento_id = $EventoId ORDER by nome");
	
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
	
		$pdf->ln(5);
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->SetFillColor(178,178,178);
		$pdf->Cell(0,6, 'Gerenciamento de Estoques de Foto e Vídeo do Evento',1,0,'C',1);
		$pdf->ln();
	
		//Define a cor RGB do fundo da celula
		$pdf->SetFillColor(178,178,178);
		//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->Cell(55,6, 'Produto:',1,0,'L',1);
		$pdf->Cell(22,6, 'Qtd Total Disp.:',1,0,'R',1);		
		$pdf->Cell(22,6, 'Qtd Total Venda:',1,0,'R',1);
		$pdf->Cell(22,6, 'Qtd Total Brinde:',1,0,'R',1);
		$pdf->Cell(20,6, 'Total Desc.:',1,0,'R',1);
		$pdf->Cell(20,6, 'Total Venda:',1,0,'R',1);
		$pdf->Cell(15,6, 'Estoque:',1,0,'R',1);
		$pdf->Cell(14,6, '% Venda:',1,0,'R',1);
		$pdf->ln(2);
			
		//Pesquisa os itens que o formando adquiriu
		$sql_total = mysql_query("SELECT 
								sum( ite.quantidade_disponivel ) AS total_disponivel, 
								sum( ite.quantidade_venda ) AS total_venda,
								sum( (ite.quantidade_venda * ite.valor_venda) - ite.valor_desconto) AS total_item, 
								sum( ite.quantidade_brinde ) AS total_brinde,
								sum( ite.valor_desconto ) AS total_desconto,  
								prod.nome AS item_nome
								FROM eventos_fotovideo ite
								LEFT OUTER JOIN categoria_fotovideo prod ON prod.id = ite.item_id
								WHERE ite.evento_id = $EventoId
								GROUP BY ite.item_id
								ORDER by prod.nome");
		
		//Cria a variável de total geral
		$total_geral = 0;
																	
		//Percorre o array 
		while ($dados_total = mysql_fetch_array($sql_total))
		{
	
			//Imprime os dados do item
			$pdf->ln();
			$pdf->SetFont('Arial','B',9);	
			$pdf->Cell(55,4, $dados_total['item_nome'],1);	
			$pdf->Cell(22,4, $dados_total['total_disponivel'],1,0,'R');
			$pdf->Cell(22,4, $dados_total['total_venda'],1,0,'R');
			$pdf->Cell(22,4, $dados_total['total_brinde'],1,0,'R');
			$pdf->Cell(20,4, $dados_total['total_desconto'],1,0,'R');
			$pdf->Cell(20,4, $dados_total['total_item'],1,0,'R');
			
			
			$saldo_total = $dados_total['total_disponivel'] - $dados_total['total_venda'] - $dados_total['total_brinde'];
			
			$pdf->Cell(15,4, $saldo_total,1,0,'R');
      
			//Gambi para não gerar o erro de divisão por zero
			if ($dados_total['total_venda'] == 0)
			{
       
				$dados_total_venda = 1;
        
			}
      
			else
      
			{
        
				$dados_total_venda = $dados_total['total_venda'];
        
			}
      
			if ($dados_total['total_disponivel'] == 0)
			{
       
				$dados_total_disponivel = 1;
        
			}
      
			else
      
			{
        
				$dados_total_disponivel = $dados_total['total_disponivel'];
        
			}
      
			$pdf->Cell(14,4, round(($dados_total_venda / $dados_total_disponivel) * 100) . ' %',1,0,'R');
			
		
			//Alimenta a variável de total geral do evento
			$total_geral = $total_geral + $dados_total[total_item];	
		}
		
		$pdf->ln(6);
		$pdf->ln();
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(19,4, 'Vendedor:',1,0,'L',1);
		$pdf->Cell(63,4, $dados_evento[colaborador_nome],1,0,'L');		
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Total Geral do Evento:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($total_geral, 2, ',', '.'),1,0,'R');			
		
		$pdf->ln(8);
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Valor pago a vista:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($dados_evento[comissao_avista], 2, ',', '.'),1,0,'R');
		$pdf->ln();
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Valor pago 30 dias:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($dados_evento[comissao_30], 2, ',', '.'),1,0,'R');
		$pdf->ln();
		$pdf->SetX(101);
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(50,4, 'Valor pago 60 dias:',1,0,'R',1);
		$pdf->Cell(20,4, number_format($dados_evento[comissao_60], 2, ',', '.'),1,0,'R');
	
	
	//Fecha o If
	}


}

$pdf->ln();	

//Gera o PDF
$pdf->Output();

?>                                                      