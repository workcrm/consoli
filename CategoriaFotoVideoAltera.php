<?php 
###########
## Módulo para alteraçao de Categorias de Foto e Vídeo
## Criado: 14/10/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

if ($_GET["headers"] == 1) 
{
  //Seta o header do retorno para efetuar a acentuação correta usando o AJAX
  header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

?>

<script language="JavaScript">

document.frmCategoriaAltera.edtNome.focus();

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitCategoriaAltera() 
{
  
  var Form;
  Form = document.frmCategoriaAltera;
  
  if (Form.edtNome.value.length == 0) 
  {
     alert("É necessário informar a descrição do Produto do Foto e Vídeo !");
     Form.edtNome.focus();
     return false;
  }

  return true;
  
}

</script>

<form name="frmCategoriaAltera" action="sistema.php?ModuloNome=CategoriaFotoVideoAltera" method="post" onsubmit="return wdSubmitCategoriaAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Produto do Foto e Vídeo </span></td>
        </tr>
        <tr>
          <td colspan="5">
            <img src="image/bt_espacohoriz.gif" width="100%" height="12" />
          </td>
        </tr>
      </table>

      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">
            <?php
            
              //Verifica se a página está abrindo vindo de uma postagem
              if($_POST["Alterar"]) 
              {
                
                //Recupera os valores vindo do formulário e atribui as variáveis
                $id = $_POST["Id"];			
                $chkAtivo = $_POST["chkAtivo"];
                $edtNome = $_POST["edtNome"];
                $edtValorVenda = MoneyMySQLInserir($_POST["edtValorVenda"]);
                $edtValorCompra = MoneyMySQLInserir($_POST["edtValorCompra"]);
                $edtFornecedorId = $_POST["edtFornecedorId"];

                //Monta e executa a query
                $sql = mysql_query("UPDATE categoria_fotovideo SET 
                                    ativo = '$chkAtivo',
                                    nome = '$edtNome',
                                    valor_venda = '$edtValorVenda',
                                    valor_compra = '$edtValorCompra',
                                    fornecedor_id = '$edtFornecedorId'
                                    WHERE id = '$id' ");			 

                //Exibe a mensagem de inclusão com sucesso
                echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Produto do Foto e Vídeo alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
                
              }

              //Recebe os valores passados do form anterior para edição do registro
              if($_POST) 
              {
                $CategoriaId = $_POST["Id"]; 
              } 
              
              else 
              {
                $CategoriaId = $_GET["Id"]; 
              }
              
              //Monta o sql
              $sql = "SELECT * FROM categoria_fotovideo WHERE id = $CategoriaId";
              //Executa a query
              $resultado = mysql_query($sql);
              //Monta o array dos dados
              $campos = mysql_fetch_array($resultado);
              
              //Efetua o switch para a figura de status ativo
              switch ($campos[ativo]) 
              {
                case 0: $ativo_status = "value='1'";	  break;
                case 1: $ativo_status = "value='1' checked";  break;
              }
	
            ?>
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td style="padding-bottom: 2px">
                  <input name="Id" type="hidden" value="<?php echo $CategoriaId ?>" />
                  <input name="Alterar" type="submit" class="button" id="Alterar" title="Salva o registro atual" value="Salvar Registro" />
                  <input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações" />
                </td>
                <td align="right">
                  <input class="button" title="Retorna ao formulário de cadastro" name="btnVoltar" type="button" id="btnVoltar" value="Voltar" style="width:70px" onclick="window.location='sistema.php?ModuloNome=CategoriaFotoVideoCadastra';" />						 
                 </td>
               </tr>
            </table>
           
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os novos dados do registro e clique em [Salvar Registro] </td>
                    </tr>
                  </table>             
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="15%">
                  <span class="dataLabel">Descri&ccedil;&atilde;o:</span>
                </td>
                <td width="85%" colspan="3" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="333" height="20">                    
                        <label></label>
                        <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 300" size="60" maxlength="50" value="<?php echo $campos[nome] ?>">
                      </td>
                      <td width="110">
                        <div align="right">Cadastro Ativo
                          <input name="chkAtivo" type="checkbox" id="chkAtivo" <?php echo $ativo_status ?>>
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr> 
              <tr>  
                <td class="dataLabel">Pre&ccedil;o de Venda:</td>
                <td class="tabDetailViewDF">
                  <?php
                  
                    //Acerta a variável com o valor a alterar
                    $valor_alterar = str_replace(".",",",$campos[valor_venda]);								

                    //Cria um objeto do tipo WDEdit 
                    $objWDComponente = new WDEditReal();

                    //Define nome do componente
                    $objWDComponente->strNome = "edtValorVenda";
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
                <td class="dataLabel">Pre&ccedil;o de Compra:</td>
                <td class="tabDetailViewDF">
                  <?php
                  
                    //Acerta a variável com o valor a alterar
                    $valor_alterar = str_replace(".",",",$campos[valor_compra]);								

                    //Cria um objeto do tipo WDEdit 
                    $objWDComponente = new WDEditReal();

                    //Define nome do componente
                    $objWDComponente->strNome = "edtValorCompra";
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
                <td class="dataLabel">Fornecedor:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <select id="edtFornecedorId" name="edtFornecedorId" style="width:350px">
                    <option value="0">Selecione uma Opção</option>
                    <?php 
                    
                      //Monta o while para gerar o combo de escolha
                      while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor)) 
                      {
                        
                        ?>
                        <option <?php if ($lookup_fornecedor->id == $campos[fornecedor_id]) { echo " selected "; } 
                        ?>
                   
                        value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . " - " . $lookup_fornecedor->nome ?> </option>
                        <?php 
                    
                      } 
                        
                    ?>
                  </select>
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