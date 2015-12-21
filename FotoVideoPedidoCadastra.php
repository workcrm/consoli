<?php 
###########
## Módulo para Criacao de novo pedido do foto e video
## Criado: 01/07/2013 - Maycon Edinger
## Alterado: 
## 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

//Converte uma data timestamp de mysql para normal
function TimestampMySQLRetornar($DATA)
{
  $ANO = 0000;
  $MES = 00;
  $DIA = 00;
  $HORA = '00:00:00';
  $data_array = split('[- ]',$DATA);
  if ($DATA <> '')
  {
    $ANO = $data_array[0];
    $MES = $data_array[1];
    $DIA = $data_array[2];
    $HORA = $data_array[3];
    return $DIA.'/'.$MES.'/'.$ANO. ' - ' . $HORA;
  }

  else 

  {
    $ANO = 0000;
    $MES = 00;
    $DIA = 00;
    return $DIA.'/'.$MES.'/'.$ANO;
  }

}

//Pega o valor do formando do pedido
$FormandoId = $_GET['FormandoId'];
$EventoId = $_GET['EventoId'];

//pesquisa as diretivas do usuário
$sql_formando = "SELECT 
                form.id AS formando_id,
                form.nome AS formando_nome,
                form.evento_id AS evento_id,
                eve.nome AS evento_nome 
                FROM eventos_formando form
                LEFT OUTER JOIN eventos eve ON eve.id = form.evento_id
                WHERE form.id = $FormandoId";													  													  
							  
//Executa a query
$resultado_formando = mysql_query($sql_formando);

//Monta o array dos campos
$dados_formando = mysql_fetch_array($resultado_formando);

$edtFormandoId = $dados_formando['formando_id'];
$edtFormandoNome = $dados_formando['formando_nome'];
$edtEventoId = $dados_formando['evento_id'];
$edtEventoNome = $dados_formando['evento_nome'];

?>
<script>
  
  //Função para selecionar todos os elementos de checkbox do formulário
  function aloca_checkbox(formulario, campo_pesquisa, campo_retorno)
  {

    var Form = formulario;
    var total = 0;

    //Percorre o array dos checkboxes de usuários e verifica se ele está marcado
    for (i = 0; i < Form.elements.length; i++) 
    {

      if (Form.elements[i].type == "checkbox" && isNaN(Form.elements[i].value) == false) 
      {

        nome_campo = Form.elements[i].id;

        if (nome_campo.substr(0, 4) == campo_pesquisa)
        {

          if (Form.elements[i].checked == true) 
          {
            total = total + 1;
          }

        }

      }

    }

    document.getElementById(campo_retorno).value = total;

  }

  function workPedidoProcessa()
  {
    
    alert('pedidoo');
    return false;
    
  }
  
  function workItemPedidoProcessa()
  {
    
    var Form = document.frmItemPedidoFV;
    
    if (Form.edtQuantidade.value.length == 0)
    {
      
      alert('É necessário informar a quantidade do produto !');
      return false;
      
    }
    
    Form.submit();
    return true;
    
  }
  
</script>

<form name="frmPedidoFV" action="FotoVideoPedidoProcessa.php" method="post" target="iframe_pedido">
<input id="edtFormandoId" name="edtFormandoId" type="hidden" value="<?php echo $edtFormandoId ?>" />
<input id="edtEventoId" name="edtEventoId" type="hidden" value="<?php echo $edtEventoId ?>" />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Novo Pedido do Foto e Vídeo</span>
          </td>
        </tr>
        <tr>
          <td>
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>
      <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="300" style="PADDING-BOTTOM: 2px">
                  <input name="btnVoltar" type="button" class="button" title="Retorna para o Gerenciamento de Pedidos do Foto e Vídeo" value="Voltar" onclick="wdCarregarFormulario('ModuloFotoVideo.php?EventoId=<?php echo $EventoId ?>','conteudo')" style="width: 80px" />
                  <input name="btnSalvar" type="submit" class="button" title="Salva o Pedido" value="Salvar Pedido" style="width: 100px" />
		            </td>
                <td align="right" style="PADDING-BOTTOM: 2px">
                  <div id="imprime_pedido" style="display: none">
                    <input class="button" title="Emite o relatório dos detalhes do pedido" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="abreJanela('./relatorios/FotoVideoPedidoRelatorioPDF.php?PedidoId=' + document.getElementById('PedidoId').value)" />
                  </div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>         
<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" style="margin-top: 6px">
  <tr>
    <td valign="top" class="dataLabel">Nro Pedido:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <span style="color: #990000;"><b><div id="numero_pedido">[AUTO NUMERADO]</div></b></span>
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Evento:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <span style="color: #990000;"><b><?php echo '[' . $edtEventoId . '] - ' . $edtEventoNome ?></b></span>
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Formando:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <span style="font-size: 14px; color: cornflowerblue;"><b><?php echo '[' . $edtFormandoId . '] - ' . $edtFormandoNome ?></b></span>
    </td>
  </tr>
  <tr>
    <td class="dataLabel" width="120">
      <span class="dataLabel">Data do Pedido:</span>             
    </td>
    <td width="200" class="tabDetailViewDF">
      <?php echo date('d/m/Y', mktime()) ?>
    </td>
    <td class="dataLabel" width="80">
      <span class="dataLabel">Hora:</span>             
    </td>
    <td class="tabDetailViewDF">
      <?php echo date('H:i', mktime()) ?>
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Data da Venda:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <?php
      //Define a data do formulário
      $objData->strFormulario = "frmPedidoFV";
      //Nome do campo que deve ser criado
      $objData->strNome = "edtDataVenda";
      $objData->strRequerido = false;
      //Valor a constar dentro do campo (p/ alteração)
      $objData->strValor = date('d/m/Y', mktime());
      //Cria o componente com seu calendario para escolha da data
      $objData->CriarData();
      ?>             
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Prazo Entrega:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <?php
      //Define a data do formulário
      $objData->strFormulario = "frmPedidoFV";
      //Nome do campo que deve ser criado
      $objData->strNome = "edtDataPrazoEnvio";
      $objData->strRequerido = false;
      //Valor a constar dentro do campo (p/ alteração)
      $objData->strValor = date('d/m/Y', mktime());
      //Cria o componente com seu calendario para escolha da data
      $objData->CriarData();
      ?>             
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Responsável:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <b><?php echo $usuarioNome . ' ' . $usuarioSobrenome ?></b>
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Vendedores:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <table width="400" align="left" border="0" cellspacing="0" cellpadding="0" class="listView">
        <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">
           <td width="30" align="center">&nbsp;&nbsp;S</td>
           <td width="250">Descrição do Vendedor</td>
           <td align="center">Comissão</td>
        </tr>
        <?php 

          //Busca os vendedores para alocacao
          $sql_vendedor = "SELECT * FROM fotovideo_vendedores WHERE ativo = 1 ORDER BY nome";													  													  

          //Executa a query
          $resultado_vendedor = mysql_query($sql_vendedor);
          
          //Inicia o contador
          $edtPosicao = 1;

          //Monta o array dos campos
          while ($dados_vendedor = mysql_fetch_array($resultado_vendedor))
          {
           
            $edtVendedorId = $dados_vendedor['id'];
            $edtVendedorNome = $dados_vendedor['nome'];
            $edtVendedorDescricao = $dados_vendedor['descricao'];
            $edtVendedorComissao = $dados_vendedor['comissao'];
            
            echo "<tr valign='middle' height='26'>
                    <td align='center' style='border-bottom: 1px #aaa dotted'>
                      <input name='VEN_$edtPosicao' id='VEN_$edtPosicao' type='checkbox' value='$edtVendedorId' onclick='aloca_checkbox(document.frmPedidoFV, \"VEN_\", \"edtRateio\");' />
                    </td>
                    <td style='border-bottom: 1px #aaa dotted'>$edtVendedorNome<br/><span style='font-size: 10px; color: cornflowerblue;'>$edtVendedorDescricao</span></td>
                    <td align='center' style='border-bottom: 1px #aaa dotted'><input name='edtComissao_$edtPosicao' id='edtComissao_$edtPosicao' type='text' class='datafield' style='width: 25px' value='$edtVendedorComissao' maxlength='2' onkeypress='if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;' /></td>
                  </tr>";
            
            $edtPosicao++;

          }

        ?>
      </table>
      &nbsp;&nbsp;Vendedores para Raterio:&nbsp;&nbsp;<input type="text" id="edtRateio" name="edtRateio" value="0" style="width: 30px; background-color: #ddd;" readonly="readonly" /> 
      <input type="hidden" id="edtPosicao" name="edtPosicao" value="<?php echo $edtPosicao ?>" />
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Observações:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <textarea id="edtPedidoObs" name="edtPedidoObs" style="width: 90%; height: 80px"></textarea>
    </td>
  </tr>
</table>
</form>
<br/>
<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Itens do Pedido:</span>
<br/>
<br/>
<form name="frmItemPedidoFV" action="FotoVideoItemPedidoProcessa.php" method="post" target="iframe_itens">
<div id="aviso_produto">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="26" width="18" valign="middle" bgcolor="#FFFFCD" style="border: #D3BE96 solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px; -webkit-border-top-left-radius: 5px; -webkit-border-bottom-left-radius: 5px; border-top-left-radius: 5px; border-bottom-left-radius: 5px;">
        <img src="./include/include_imagens/bt_informacao.png" border="0" align="absmiddle" />
      </td>
      <td width="780" valign="middle" bgcolor="#FFFFCD" style="border: #D3BE96 solid 1px; padding-left: 4px; border-left: 0px; -webkit-border-top-right-radius: 5px; -webkit-border-bottom-right-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
        <span class="TextoAzul; color: #000000">É necessário primeiro salvar o pedido para adicionar itens !</span>
      </td>
    </tr>							
  </table>          
</div>
<div id="botao_produto" style="display:none">
  <input name="btnAdicionar" type="button" class="button" title="Adiciona o Produto ao Pedido" value="Adicionar Produto" onclick="return workItemPedidoProcessa();" style="width: 110px" />
</div>
<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" style="margin-top: 6px">
  <tr>
    <td width="120" valign="top" class="dataLabel">Produto:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <select name="cmbProdutoId" id="cmbProdutoId" style="width:350px">
        <?php

          //Monta o lookup da tabela de produtos do FV
          $lista_produtos = "SELECT id, nome FROM categoria_fotovideo WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
          
          //Executa a query
          $dados_produtos = mysql_query($lista_produtos);
          
          //Monta o while para gerar o combo de escolha
          while ($lookup_produtos = mysql_fetch_object($dados_produtos)) 
          {

            ?>
            <option value="<?php echo $lookup_produtos->id ?>"><?php echo $lookup_produtos->nome ?></option>
            <?php

          }

        ?>
      </select>
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Quantidade:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <input name="edtQuantidade" id="edtQuantidade" type="text" class="datafield" style="width: 50px" maxlength="5" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" />
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Preço Unitário:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <table cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
          <td width="120">
            <?php
      
              //Acerta a variável com o valor a alterar
              //$valor_alterar = str_replace(".",",",$campos[valor]);

              //Cria um objeto do tipo WDEdit 
              $objWDComponente = new WDEditReal();

              //Define nome do componente
              $objWDComponente->strNome = "edtValorUnitario";
              //Define o tamanho do componente
              $objWDComponente->intSize = 16;
              //Busca valor definido no XML para o componente
              $objWDComponente->strValor = '';
              //Busca a descrição do XML para o componente
              $objWDComponente->strLabel = '';
              //Determina um ou mais eventos para o componente
              $objWDComponente->strEvento = '';
              //Define numero de caracteres no componente
              $objWDComponente->intMaxLength = 14;

              //Cria o componente edit
              $objWDComponente->Criar();  

            ?>
          </td>
          <td>
            <input id="chkBrinde" name="chkBrinde" type="checkbox" onclick="vlr = document.getElementById('edtValorUnitario'); if (this.checked) {vlr.value = ''; vlr.disabled = true} else {vlr.disabled = false}">&nbsp;Brinde
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td valign="top" class="dataLabel">Observações:</td>
    <td colspan="3" valign="middle" class="tabDetailViewDF">
      <textarea id="edtObs" name="edtObs" style="width: 90%; height: 80px"></textarea>
    </td>
  </tr>
  <tr>
    <td colspan="4">
      <div id="consulta_itens" name="consulta_itens"></div>
    </td>
  </tr>
</table>
<input id="PedidoId" name="PedidoId" type="hidden" value="0" />
</form>
<iframe id="iframe_itens" name="iframe_itens" frameborder="0" scrolling="no" width="100%" height="1000"></iframe>
<br/>
<iframe id="iframe_pedido" name="iframe_pedido" frameborder="0"></iframe>

							