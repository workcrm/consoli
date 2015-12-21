<?php
###########
## Módulo para exibiçào de Foto e Vídeo de Formandos sem compra
## Criado: 22/08/2013 - Maycon Edinger
## Alterado: 
## Alterações: 
##
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Inclui o arquivo de conexão com o servidor
include 'conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include 'include/ManipulaDatas.php';

//Recupera os valores para filtragem
$CidadeId = $_GET['CidadeId'];
$UFId = $_GET['UFId'];
$DataIni = DataMySQLInserir($_GET["DataIni"]);
$DataFim = DataMySQLInserir($_GET["DataFim"]);


//Caso tenha Informado uma cidade
if ($CidadeId != 0)
{

  //Recupera dos dados do evento
  $sql_cidade = "SELECT nome FROM cidades WHERE id = $CidadeId";

  //Executa a query de consulta
  $query_cidade = mysql_query($sql_cidade);

  //Monta a matriz com os dados
  $dados_cidade = mysql_fetch_array($query_cidade);

  //Caria a descricao da cidade
  $desc_cidade = "Cidade: " . $dados_cidade['nome'];

  //Cria a filtragem
  $where_cidade = "AND form.cidade_id = $CidadeId";

}

//Caso tenha Informado uma UF
if (!empty($UFId))
{
	
  //Caria a descricao da uf
  $desc_uf = "UF: " . $UFId;

  //Cria a filtragem
  $where_uf = "AND cid.uf = '$UFId'";

}

//Caso tenha uma data
if ($DataFim != '0-0-0')
{
	
  //Caria a descricao da uf
  $desc_datas = "Data de Realização do Evento: " . DataMySQLRetornar($DataIni) . ' a ' . DataMySQLRetornar($DataFim);

  //Cria a filtragem
  $where_datas = "AND eve.data_realizacao BETWEEN '$DataIni' AND '$DataFim'";

}

echo "<span style='color: #990000'><b>Relação de formandos sem compra por cidade/uf</b></span>";
echo '<br/>' . $desc_cidade;
echo '<br/>' . $desc_uf;
echo '<br/>' . $desc_datas . '<br/>';

//verifica os formandos já cadastrados para este evento
$sql_formando = mysql_query("SELECT 
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
                            cid.nome AS cidade_nome,
                            cid.uf AS cidade_uf,
                            eve.nome AS evento_nome
                            FROM eventos_formando form
                            LEFT OUTER JOIN eventos eve ON eve.id = form.evento_id
                            LEFT OUTER JOIN cidades cid ON cid.id = form.cidade_id
                            WHERE form.status = 2 
                            $where_cidade
                            $where_uf
                            $where_datas
                            AND form.status_fotovideo = 1
                            ORDER BY form.nome");

//Verifica o numero de registros retornados
$registros = mysql_num_rows($sql_formando);
	
//Verifica a quantidade de registros
if ($registros == 0 ) 
{
  //Exibe a mensagem que não foram encontrados registros
  echo '<br/>Não há Formandos Cadastrados para esta Cidade/UF !';

//Caso tiver
} 

else 

{
	
  //Percorre o array 
  while ($dados_formando = mysql_fetch_array($sql_formando))
  {

    $FormandoId = $dados_formando["id"];

    //verifica se o formando comprou algo de foto e video
    $sql_compra = mysql_query("SELECT 
                              item_id 
                              FROM eventos_fotovideo 
                              WHERE formando_id = $FormandoId");

    //Verifica o numero de registros retornados
    $registros = mysql_num_rows($sql_compra);

    //Verifica a quantidade de registros
    if ($registros == 0 ) 
    {

      if ($dados_formando['email'] != '')
      {
        
        $emails .= $dados_formando['email'] . ";\r\n";
        
      }

      $pendente++;

    }

  }

  //Imprime os dados do formando	  
  echo "<br/><br/>Total de Formandos a Atender: <b>" . $pendente . '</b>';
  
}

?>
<br/>
<textarea style="width: 740px; height: 800px; font-family: courier; color: navy"><?php echo $emails ?></textarea>