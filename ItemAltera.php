<?php 
###########
## Módulo para alteraçao de Itens do Evento
## Criado: 20/05/2007 - Maycon Edinger
## Alterado: 26/11/2007 - Maycon Edinger 
## Alterações: 
## 17/06/2007 - Implementado os novos campos para o cadastro de produtos 
## 26/06/2007 - Implementado o cadastro de opções do item
## 30/07/2007 - Implementado campo para categoria dos itens
## 26/11/2007 - Implementado campo para valor de locação do item
###########

if ($_GET["headers"] == 1) 
{
	
  //Seta o header do retorno para efetuar a acentuação correta usando o AJAX
  header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Monta o lookup da tabela de categorias de item
//Monta o SQL
$lista_categoria = "SELECT * FROM categoria_item WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_categoria = mysql_query($lista_categoria);

//Monta o lookup da tabela de departamentos de item
//Monta o SQL
$lista_departamento = "SELECT * FROM departamentos WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";

//Executa a query
$dados_departamento = mysql_query($lista_departamento);

?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  
  ID = document.getElementById(id);
  ID.style.display = "none";

}

function wdSubmitItemAltera() 
{
  
  var Form;
  Form = document.frmItemAltera;

  if (Form.edtNome.value.length == 0) 
  {

    alert("É necessário informar a descrição do Item !");
    Form.edtNome.focus();
    return false;

  }

  if (Form.cmbCategoriaId.value == 0) 
  {

    alert("É necessário selecionar um Centro de Custo !");
    Form.cmbCategoriaId.focus();
    return false;

  }   

  //Verifica se o checkbox de ativo está marcado
  if (Form.chkAtivo.checked) 
  {

    var chkAtivoValor = 1;

  } 

  else 

  {

    var chkAtivoValor = 0;

  }

   return true;
}
</script>

<form name="frmItemAltera" action="sistema.php?ModuloNome=ItemAltera" method="post" onsubmit="return wdSubmitItemAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração do Produto</span></td>
        </tr>
        <tr>
          <td colspan="5">
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>

      <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">
            <?php
            
              //Verifica se a página está abrindo vindo de uma postagem
              if ($_POST["Alterar"])
              {

                //Recupera os valores vindo do formulário e atribui as variáveis
                $id = $_POST["Id"];
                $edtNome = $_POST["edtNome"];
                $edtClassificacao = $_POST["edtClassificacao"];
                $cmbCategoriaId = $_POST["cmbCategoriaId"];
                $cmbDepartamentoId = $_POST["cmbDepartamentoId"];
                $chkAtivo = $_POST["chkAtivo"];
                $chkMaterial = $_POST["chkMaterial"];
                $chkEvento = $_POST["chkEvento"];
                $chkOrcamento = $_POST["chkOrcamento"];
                $edtUnidade = $_POST["edtUnidade"];
                $edtEstoqueAtual = $_POST["edtEstoqueAtual"];
                $edtEstoqueMinimo = $_POST["edtEstoqueMinimo"];
                $edtValorCusto = MoneyMySQLInserir($_POST["edtValorCusto"]);
                //$edtValorVenda = MoneyMySQLInserir($_POST["edtValorVenda"]);
                //$edtValorLocacao = MoneyMySQLInserir($_POST["edtValorLocacao"]);
                $edtLocalizacao1 = $_POST["edtLocalizacao1"];
                $edtLocalizacao2 = $_POST["edtLocalizacao2"];
                $edtLocalizacao3 = $_POST["edtLocalizacao3"];

                //Monta e executa a query
                $sql = mysql_query("UPDATE item_evento SET 
                                    nome = '$edtNome',
                                    categoria_id = '$cmbCategoriaId',
                                    departamento_id = '$cmbDepartamentoId',
                                    classificacao = '$edtClassificacao',
                                    ativo = '$chkAtivo',																
                                    unidade = '$edtUnidade',
                                    estoque_atual = '$edtEstoqueAtual',
                                    estoque_minimo = '$edtEstoqueMinimo',
                                    tipo_material = '$chkMaterial',
                                    valor_custo = '$edtValorCusto',
                                    valor_venda = '$edtValorVenda',
                                    valor_locacao = '$edtValorLocacao',
                                    exibir_evento = '$chkEvento',
                                    exibir_orcamento = '$chkOrcamento',
                                    localizacao_1 = '$edtLocalizacao1',
                                    localizacao_2 = '$edtLocalizacao2',
                                    localizacao_3 = '$edtLocalizacao3'
                                    WHERE id = '$id' ");

              //Exibe a mensagem de inclusão com sucesso
              echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Produto alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
             
            }

            //Recebe os valores passados do form anterior para edição do registro
            if ($_POST)
            {

              $ItemId = $_POST["Id"];
              
            }
            
            else
            {

              $ItemId = $_GET["Id"];
              
            }

            //Monta o sql
            $sql = "SELECT * FROM item_evento WHERE id = $ItemId";

            //Executa a query
            $resultado = mysql_query($sql);

            //Monta o array dos dados
            $campos = mysql_fetch_array($resultado);

            //Efetua o switch para a figura de status ativo
            switch ($campos[ativo])
            {

              case 0: $ativo_status = "value='1'"; break;
              case 1: $ativo_status = "value='1' checked"; break;
            }

            switch ($campos["classificacao"])
            {

              case 1: $classifica_1 = "checked"; break;
              case 2: $classifica_2 = "checked"; break;
            }

            //Efetua o switch para o campo de material
            switch ($campos[tipo_material])
            {

              case 0: $material_status = "value='1'"; break;
              case 1: $material_status = "value='1' checked"; break;
            }

            //Efetua o switch para o campo de exibir no evento
            switch ($campos[exibir_evento])
            {

              case 0: $evento_status = "value='1'"; break;
              case 1: $evento_status = "value='1' checked"; break;
            }

            //Efetua o switch para o campo de exibir no orçamento
            switch ($campos[exibir_orcamento])
            {

              case 0: $orcamento_status = "value='1'"; break;
              case 1: $orcamento_status = "value='1' checked"; break;
              
            }
            
            ?>
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td style="PADDING-BOTTOM: 2px">
                  <input name="Id" type="hidden" value="<?php echo $ItemId ?>" />
                  <input name="Alterar" type="submit" class="button" id="Alterar" title="Salva o registro atual]" value="Salvar Registro" >
                  <input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações">
                </td>
                <td align="right">
                  <input name="btnVoltar" type="button" class="button" title="Voltar" value="Voltar" style="width:80px" onclick="wdCarregarFormulario('ItemExibe.php?Id=<?php echo $ItemId ?>&headers=1','conteudo')">					 
                </td>
              </tr>
            </table>

            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="4">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os novos dados do registro e clique em [Salvar Registro] </td>
                    </tr>
                  </table>             
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="130">
                  <span class="dataLabel">Descri&ccedil;&atilde;o:</span>
                </td>
                <td colspan="3" width="600" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td height="20">
                        <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 350px" size="60" maxlength="70" value="<?php echo $campos[nome] ?>">
                      </td>
                      <td>
                        <div align="right">Cadastro Ativo
                          <input name="chkAtivo" type="checkbox" id="chkAtivo" <?php echo $ativo_status ?>>
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Classificação:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="130" height="22">
                        <input name="edtClassificacao" type="radio" value="1" <?php echo $classifica_1 ?>>&nbsp;Produto
                      </td>
                      <td height="22">
                        <input name="edtClassificacao" type="radio" value="2" <?php echo $classifica_2 ?>>&nbsp;Serviço
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Categoria:</td>
                <td colspan="3" valign="middle" class=tabDetailViewDF>
                  <select name="cmbCategoriaId" id="cmbCategoriaId" style="width:350px">
                    <option value="0">--- Selecione uma Categoria ---</option>
                    <?php 
                    
                      while ($lookup_categoria = mysql_fetch_object($dados_categoria))
                      { 
                        
                        ?>
                        <option value="<?php echo $lookup_categoria->id ?>"><?php echo $lookup_categoria->nome ?></option>
                        <?php 
                        
                      }
                      
                    ?>
                  </select>
                  <script>
                    
                    document.getElementById('cmbCategoriaId').value = '<?php echo $campos[categoria_id] ?>';
                    
                  </script>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Responsável:</td>
                <td colspan="3" valign="middle" class=tabDetailViewDF>
                  <select name="cmbDepartamentoId" id="cmbDepartamentoId" style="width:350px">
                    <option value="0">--- Selecione um Departamento ---</option>
                    <?php 
                    
                      while ($lookup_departamento = mysql_fetch_object($dados_departamento))
                      { 
                        
                        ?>
                        <option value="<?php echo $lookup_departamento->id ?>"><?php echo $lookup_departamento->nome ?></option>
                        <?php 
                        
                      }
                      
                    ?>
                  </select>
                  <script>
                    
                    document.getElementById('cmbDepartamentoId').value = '<?php echo $campos[departamento_id] ?>';
                    
                  </script>
                </td>
              </tr>
              <tr>
                <td width="140" class="dataLabel">Estoque Atual:</td>
                <td valign="middle" class=tabDetailViewDF>
                  <?php
                  
                    $bloqueia_estoque = 'readonly="readonly"';

                    if ($usuarioNome == "Janaina" OR $usuarioNome == "Gerri" OR $usuarioNome == "Maycon")
                    {

                      $bloqueia_estoque = '';

                    }
                  
                  ?>
                  <input name="edtEstoqueAtual" type="text" class="campo" id="edtEstoqueAtual" style="width: 50px" <?php echo $bloqueia_estoque ?> onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos["estoque_atual"] ?>" >								 						 
                </td>
                <td width="140" class="dataLabel">Estoque Mínimo:</td>
                <td valign="middle" class=tabDetailViewDF>
                  <input name="edtEstoqueMinimo" type="text" class="campo" id="edtEstoqueMinimo" style="width: 50px" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos["estoque_minimo"] ?>" >								 						 
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Unidade:</td>
                <td colspan="3" class=tabDetailViewDF>
                  <select class="datafield"name="edtUnidade" id="edtUnidade">
                    <option selected value="<?php echo $campos[unidade] ?>"><?php echo $campos[unidade] ?></option>
                    <option value="PC">PC - Peça</option>
                    <option value="UN">UN - Unidade</option>
                    <option value="GR">GR - Grama</option>
                    <option value="KG">KG - Kilo</option>
                    <option value="LT">LT - Litro</option>
                    <option value="PT">PT - Pacote</option>
                    <option value="VD">VD - Vidro</option>
                    <option value="LT">LT - Lata</option>
                    <option value="BD">BD - Balde</option>
                    <option value="CX">CX - Caixa</option>
                    <option value="GL">GL - Galão</option>
                    <option value="MT">MT - Metro</option>
                    <option value="M2">M2 - Metro Quadrado</option>
                    <option value="M3">M3 - Metro Cúbico</option>	
                  </select>							 						 
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Pre&ccedil;o de Custo:</td>
                <td colspan="3" width="120" class=tabDetailViewDF>
                  <?php
                  
                    //Acerta a variável com o valor a alterar
                    $valor_alterar = str_replace(".", ",", $campos[valor_custo]);

                    //Cria um objeto do tipo WDEdit 
                    $objWDComponente = new WDEditReal();

                    //Define nome do componente
                    $objWDComponente->strNome = "edtValorCusto";
                    //Define o tamanho do componente
                    $objWDComponente->intSize = 16;
                    //Busca valor definido no XML para o componente
                    $objWDComponente->strValor = "$valor_alterar";
                    //Busca a descrição do XML para o componente
                    $objWDComponente->strLabel = "";
                    //Determina um ou mais eventos para o componente
                    $objWDComponente->strEvento = "";
                    //Define numero de caracteres no componente
                    $objWDComponente->intMaxLength = 14;

                    //Cria o componente edit
                    $objWDComponente->Criar();
                  
                  ?>								
                </td>
              </tr> 
              <tr>
                <td class="dataLabel">Localização:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <input name="edtLocalizacao1" type="text" class="campo" id="edtLocalizacao1" style="width: 40px" maxlength="5" value="<?php echo $campos["localizacao_1"] ?>">&nbsp;<b>/</b>&nbsp;<input name="edtLocalizacao2" type="text" class="campo" id="edtLocalizacao2" style="width: 40px" maxlength="5" value="<?php echo $campos["localizacao_2"] ?>">&nbsp;<b>/</b>&nbsp;<input name="edtLocalizacao3" type="text" class="campo" id="edtLocalizacao3" style="width: 40px" maxlength="5" value="<?php echo $campos["localizacao_3"] ?>">							
                </td>						 
              </tr>
              <tr>
                <td class="dataLabel" valign="top">Opções:</td>
                <td colspan="3" class=tabDetailViewDF>
                  <input name="chkMaterial" type="checkbox" value="1" style="border: 0px" <?php echo $material_status ?>>
                  <span style="font-size: 11px">Este produto também pode ser usado como um material.</span>
                  <br>
                  <input name="chkEvento" type="checkbox" value="1" style="border: 0px" <?php echo $evento_status ?>>
                  <span style="font-size: 11px">Exibir o produto nos itens disponíveis para um evento.</span>
                  <br>
                  <input name="chkOrcamento" type="checkbox" value="1" style="border: 0px" <?php echo $orcamento_status ?>>
                  <span style="font-size: 11px">Exibir o produto nos itens disponíveis para um orçamento.</span>
                </td>
              </tr>
            </table>
          </td>
        </tr>		
      </table>  	 
    </td>
  </tr>
</table>
</form>