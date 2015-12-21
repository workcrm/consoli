<?php 
###########
## Módulo para Listagem dos itens do pedido para alocacao de fornecedor
## Criado: 03/09/2013 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

$PedidoId = $_GET[PedidoId];

//verifica os formandos já cadastrados para este evento e exibe na tela
$sql_consulta = mysql_query("SELECT
                            ped.id,
                            ped.data,
                            ped.hora,
                            ped.formando_id,
                            ped.observacoes,
                            form.situacao,
                            form.nome AS formando_nome,
                            eve.id AS evento_id,
                            eve.nome AS evento_nome,
                            curso.nome AS curso_nome,
                            curso.id AS curso_id 
                            FROM fotovideo_pedido ped
                            LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
                            LEFT OUTER JOIN eventos eve ON eve.id = form.evento_id
                            LEFT OUTER JOIN cursos curso ON curso.id = form.curso_id
                            WHERE ped.id = $PedidoId");

$registros = mysql_num_rows($sql_consulta);

//Monta o lookup da tabela de fornecedores
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//Adiciona o acesso a entidade de criação do componente data
include("CalendarioPopUp.php");

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">
</head>
<body>

<form id="form" name="cadastro" method="post" onsubmit="return valida_form();">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>  
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Detalhamento do Pedido <span style="color: #990000"><?php echo $PedidoId ?></span>:</span></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <?php

        //Caso não houverem registros
        if ($registros == 0)
        { 

          //Exibe uma linha dizendo que nao registros
          echo "
                <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class='listView'>
                  <tr height='24'>
                    <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                      <font color='#33485C'><strong>Pedido não encontrado !</strong></font>
                    </td>
                  </tr>
                </table>";	  

        }

        else 

        {

          //Cria o array e o percorre para montar a listagem dinamicamente
          $dados_pedido = mysql_fetch_array($sql_consulta);

          ?>    
          <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" style="margin-top: 6px">
            <tr>
              <td width="130" class="dataLabel">Data:</td>
              <td valign="middle" class="tabDetailViewDF">
                <?php echo DataMySQLRetornar($dados_pedido['data']) . ' - ' . substr($dados_pedido['hora'],0,5) ?>
              </td>
            </tr>
            <tr>
              <td class="dataLabel">Formando:</td>
              <td valign="middle" class="tabDetailViewDF">
                <b><?php echo '[' . $dados_pedido['formando_id'] . '] - ' . $dados_pedido['formando_nome'] ?></b>
              </td>
            </tr>
            <tr>
              <td class="dataLabel">Evento:</td>
              <td valign="middle" class="tabDetailViewDF">
                <b><?php echo '[' . $dados_pedido['evento_id'] . '] - ' . $dados_pedido['evento_nome'] ?></b>
              </td>
            </tr>
            <tr>
              <td class="dataLabel">Observações:</td>
              <td valign="middle" class="tabDetailViewDF">
                <i><?php echo $dados_pedido['observacoes'] ?></i>
              </td>
            </tr>
          </table>
          <?php

         //Fecha o if de se tem registros
         }
      
      ?>
    </td>
  </tr>
  <tr>  
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440"><br/><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Itens do Pedido:</span></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <?php
      
        //Busca os dados dos produtos                                           
        $sql_produto = mysql_query("SELECT 
                                    prod.id,
                                    prod.pedido_id,
                                    prod.produto_id,
                                    prod.quantidade_venda,
                                    prod.chk_brinde,
                                    prod.valor_unitario,
                                    prod.obs_cadastro,
                                    produto.nome as produto_nome                          
                                    FROM fotovideo_pedido_produto prod
                                    LEFT OUTER JOIN categoria_fotovideo produto ON produto.id = prod.produto_id                          
                                    WHERE prod.pedido_id = $PedidoId
                                    ORDER BY produto_nome
                                    ");

        //Conta os produtos do pedido
        $registros_produto = mysql_num_rows($sql_produto);

        //Caso tenha registros
        if ($registros_produto > 0)
        {
          
          $posicao = 1;
          
          //Percorre o array
          while ($dados_produto = mysql_fetch_array($sql_produto))
          {
         
            ?>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" style="margin-top: 6px">
              <tr>
                <td width="130" class="dataLabel">Produto:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <b><?php echo $dados_produto['produto_nome'] ?></b>
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Quantidade:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <b><?php echo $dados_produto['quantidade_venda'] ?></b>
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Observações:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <select name="cmbFornecedorId_<?php echo $posicao ?>" id="cmbFornecedorId_<?php echo $posicao ?>" style="width:450px">
                    <option value="0">Selecione um Fornecedor</option>
                    <?php 
                    
                      //Monta o while para gerar o combo de escolha
                      while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) 
                      {
                        
                        ?>
                        <option value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->nome ?></option>
                        <?php 
                       
                      }
                      
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Data Entrega:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php
                  
                    //Define a data do formulário
                    $objData->strFormulario = "cadastro";  
                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtData_$posicao";
                    $objData->strRequerido = true;
                    //Valor a constar dentro do campo (p/ alteração)
                    $objData->strValor = '';
                    //Define o tamanho do campo 
                    //$objData->intTamanho = 15;
                    //Define o número maximo de caracteres
                    //$objData->intMaximoCaracter = 20;
                    //define o tamanho da tela do calendario
                    //$objData->intTamanhoCalendario = 200;
                    //Cria o componente com seu calendario para escolha da data
                    $objData->CriarData();

                  ?>
                </td>
              </tr>
            </table>
            <?php
            
            $posicao++;
            
          }
         
        }
        
        else
          
        {
          
          //Exibe uma linha dizendo que nao registros
          echo "
                <table width='100%' align='left' border='0' cellspacing='0' cellpadding='0' class='listView'>
                  <tr height='24'>
                    <td valign='middle' bgcolor='#fdfdfd' class='oddListRowS1'>
                      <font color='#33485C'><strong>Não há produtos neste pedido !</strong></font>
                    </td>
                  </tr>
                </table>";
          
        }
      
      ?>
    </td>
  </tr>
</table>
<br/>
<br/>
</form>
