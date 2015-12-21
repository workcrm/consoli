<?php 
###########
## Módulo para alteração de formando do evento
## Criado: 14/10/2008 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET['headers'] == 1) 
{
	
  header('Content-Type: text/html;  charset=ISO-8859-1',true);

}

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

//Monta o lookup da tabela de cidades
//Monta o SQL
$lista_cidade = "SELECT * FROM cidades WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_cidade = mysql_query($lista_cidade);

//Monta o lookup da tabela de cursos
//Monta o SQL
$lista_curso = "SELECT * FROM cursos WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_curso = mysql_query($lista_curso);

//Monta o lookup da tabela de fornecedores (para a pessoa_id)
//Monta o SQL
$lista_fornecedor = "SELECT id, nome FROM fornecedores WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";
//Executa a query
$dados_fornecedor = mysql_query($lista_fornecedor);

//pesquisa as diretivas do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";													  													  
							  
//Executa a query
$resultado_usuario = mysql_query($sql_usuario);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_usuario);

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="JavaScript">

function adicionarDias(data, dias)
{
     
  return new Date(data.getTime() + (dias * 24 * 60 * 60 * 1000));
  
}

function VerificaCPF ()
{
 
  cpf = document.frmFormandoEventoAltera.edtCpf.value;
  cpf = remove(cpf, ".");
  cpf = remove(cpf, "-");

  if (cpf.length > 0)
  {
    
    if (vercpf(cpf)) 
    {

      return true;

    } 

    else 

    {

      errors="1";

      if (errors) alert('O CPF informado é Inválido');

      document.retorno = (errors == '');
    }

  }
  
}

function vercpf (cpf) 
{
  
  if (cpf.length != 11 || cpf == "00000000000" || cpf == "11111111111" || cpf == "22222222222" || cpf == "33333333333" || cpf == "44444444444" || cpf == "55555555555" || cpf == "66666666666" || cpf == "77777777777" || cpf == "88888888888" || cpf == "99999999999")
  return false;
  add = 0;

  for (i=0; i < 9; i ++)
  add += parseInt(cpf.charAt(i)) * (10 - i);
  rev = 11 - (add % 11);

  if (rev == 10 || rev == 11)
  rev = 0;

  if (rev != parseInt(cpf.charAt(9)))
  return false;
  add = 0;
  for (i = 0; i < 10; i ++)
  add += parseInt(cpf.charAt(i)) * (11 - i);
  rev = 11 - (add % 11);
  if (rev == 10 || rev == 11)
  rev = 0;
  if (rev != parseInt(cpf.charAt(10)))
  return false;
  return true;
  
}

function remove(str, sub) 
{
   i = str.indexOf(sub);
   r = "";
   if (i == -1) return str;
   r += str.substring(0,i) + remove(str.substring(i + sub.length), sub);
   return r;
}
 
//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitFormandoEventoAltera() 
{
  var Form;
  Form = document.frmFormandoEventoAltera;

  if (Form.edtCpf.value.length == 0) 
  {
    alert("É necessário informar o cpf do formando !");
    Form.edtCpf.focus();
    return false;
  }

  if (Form.edtNome.value.length == 0) 
  {
    alert("É necessário informar o nome do formando !");
    Form.edtNome.focus();
    return false;
  }

  if (Form.cmbCidadeId.value == 0) 
  {
    alert("É necessário selecionar a cidade do formando !");
    return false;
  }

  return true;

}
</script>

<form name="frmFormandoEventoAltera" action="sistema.php?ModuloNome=FormandoEventoAltera" method="post" onsubmit="return wdSubmitFormandoEventoAltera()">

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
  <tr>
    <td class="text" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Formando do Evento</span></td>
        </tr>
        <tr>
          <td colspan="5">
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">
          </td>
        </tr>
      </table>
      <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="100%" class="text">
            <?php
            
              //Verifica se a flag está vindo de uma postagem para liberar a alteração
              if ($_POST['Submit'])
              {

                //Recupera os valores do formulario e alimenta as variáveis
                $id = $_POST['FormandoId'];
                $edtEventoId = $_POST['EventoId'];
                $edtNome = $_POST['edtNome'];
                $cmbCursoId = $_POST['cmbCursoId'];
                $edtCpf = $_POST['edtCpf'];
                $edtDataNascimento = DataMySQLInserir($_POST["edtDataNascimento"]);
                $edtEndereco = $_POST['edtEndereco'];
                $edtComplemento = $_POST['edtComplemento'];
                $edtBairro = $_POST['edtBairro'];
                $cmbCidadeId = $_POST['cmbCidadeId'];
                $edtUf = $_POST['edtUf'];
                $edtCep = $_POST['edtCep'];
                $edtContato = $_POST['edtTelefone'];
                $edtOperadora = $_POST['edtOperadora'];
                $edtTelefoneComercial = $_POST['edtTelefoneComercial'];
                $edtTelefoneResidencial = $_POST['edtTelefoneResidencial'];
                $edtEmail = $_POST['edtEmail'];
                $edtObservacoes = $_POST['edtObservacoes'];
                $edtStatus = $_POST['edtStatus'];
                $edtStatusFotoVideo = $_POST['edtStatusFotoVideo'];

                //Parte financeira
                $edtSituacao = $_POST['edtSituacao'];
                $edtObsFinanceiro = $_POST['edtObsFinanceiro'];

                $edtSenhaFormando = $_POST['edtSenhaFormando'];

                //Captura os 5 ultimos numeros do CPF
                $PegaDigitos = substr($edtCpf, (strlen($edtCpf) - 5), strlen($edtCpf));

                $senha_formando = str_replace('-', '', $PegaDigitos);

                //Participacoes
                $chkCulto = $_POST["chkCulto"];
                $chkColacao = $_POST["chkColacao"];
                $chkJantar = $_POST["chkJantar"];
                $chkBaile = $_POST["chkBaile"];

                $chkSpc = $_POST["chkSpc"];
                $chkAcaoCobranca = $_POST["chkAcaoCobranca"];
                $chkAssessoriaCobranca = $_POST["chkAssessoriaCobranca"];

                //Dados do foto e vídeo
                $edtDataVenda = DataMySQLInserir($_POST["edtDataVenda"]);
                $edtDataEnvioLab = DataMySQLInserir($_POST["edtDataEnvioLab"]);
                $edtDataPrevLab = DataMySQLInserir($_POST["edtDataPrevLab"]);
                $edtDataRetornoLab = DataMySQLInserir($_POST["edtDataRetornoLab"]);
                $edtDataEntregaCliente = DataMySQLInserir($_POST["edtDataEntregaCliente"]);
                $edtDataEnvioCliente = DataMySQLInserir($_POST["edtDataEnvioCliente"]);
                $cmbFornecedorId = $_POST["cmbFornecedorId"];
                
                //Dados do Avalista
                $edtAvalistaNome = $_POST["edtAvalistaNome"];
                $edtAvalistaCpf = $_POST["edtAvalistaCpf"];
                $edtAvalistaTelefone = $_POST["edtAvalistaTelefone"];
                $edtAvalistaEndereco = $_POST["edtAvalistaEndereco"];

                if ($dados_usuario["evento_fotovideo"] == 1)
                {

                  //Executa a query de alteração
                  $sql = mysql_query("UPDATE eventos_formando SET 
                                      nome = '$edtNome',
                                      curso_id = '$cmbCursoId',
                                      senha = '$senha_formando',
                                      cpf = '$edtCpf', 
                                      endereco = '$edtEndereco', 
                                      complemento = '$edtComplemento', 
                                      bairro = '$edtBairro', 
                                      cidade_id = '$cmbCidadeId', 
                                      uf = '$edtUf', 
                                      cep = '$edtCep', 
                                      contato = '$edtContato',
                                      operadora = '$edtOperadora',
                                      telefone_comercial = '$edtTelefoneComercial',
                                      telefone_residencial = '$edtTelefoneResidencial',
                                      email = '$edtEmail',  
                                      observacoes = '$edtObservacoes',
                                      status = '$edtStatus',
                                      alteracao_timestamp = now(),
                                      alteracao_operador_id = '$edtOperadorId',
                                      situacao = '$edtSituacao',
                                      obs_financeiro = '$edtObsFinanceiro',
                                      data_nascimento = '$edtDataNascimento',
                                      chk_culto = '$chkCulto',
                                      chk_colacao = '$chkColacao',
                                      chk_jantar = '$chkJantar',
                                      chk_baile = '$chkBaile',
                                      data_venda = '$edtDataVenda',
                                      data_envio_lab = '$edtDataEnvioLab',
                                      data_prev_lab = '$edtDataPrevLab',
                                      data_retorno_lab = '$edtDataRetornoLab',
                                      data_entrega_cliente = '$edtDataEntregaCliente',
                                      data_envio_cliente = '$edtDataEnvioCliente',
                                      lab_fornecedor_id = '$cmbFornecedorId',
                                      chk_spc = '$chkSpc',
                                      chk_acao_cobranca = '$chkAcaoCobranca',
                                      chk_assessoria_cobranca = '$chkAssessoriaCobranca',
                                      status_fotovideo = '$edtStatusFotoVideo',
                                      avalista_nome = '$edtAvalistaNome',
                                      avalista_cpf = '$edtAvalistaCpf',
                                      avalista_telefone = '$edtAvalistaTelefone',
                                      avalista_endereco = '$edtAvalistaEndereco'
                                      WHERE id = '$id' ");
                }

                else
                {

                  //Executa a query de alteração
                  $sql = mysql_query("UPDATE eventos_formando SET 
                                      nome = '$edtNome',
                                      curso_id = '$cmbCursoId',
                                      senha = '$senha_formando',
                                      cpf = '$edtCpf', 
                                      endereco = '$edtEndereco', 
                                      complemento = '$edtComplemento', 
                                      bairro = '$edtBairro', 
                                      cidade_id = '$cmbCidadeId', 
                                      uf = '$edtUf', 
                                      cep = '$edtCep', 
                                      contato = '$edtContato',
                                      operadora = '$edtOperadora',
                                      telefone_comercial = '$edtTelefoneComercial',
                                      telefone_residencial = '$edtTelefoneResidencial',
                                      email = '$edtEmail',  
                                      observacoes = '$edtObservacoes',
                                      status = '$edtStatus',
                                      alteracao_timestamp = now(),
                                      alteracao_operador_id = '$edtOperadorId',
                                      situacao = '$edtSituacao',
                                      obs_financeiro = '$edtObsFinanceiro',
                                      data_nascimento = '$edtDataNascimento',
                                      chk_culto = '$chkCulto',
                                      chk_colacao = '$chkColacao',
                                      chk_jantar = '$chkJantar',
                                      chk_baile = '$chkBaile',
                                      chk_spc = '$chkSpc',
                                      chk_acao_cobranca = '$chkAcaoCobranca',
                                      chk_assessoria_cobranca = '$chkAssessoriaCobranca',
                                      status_fotovideo = '$edtStatusFotoVideo',
                                      avalista_nome = '$edtAvalistaNome',
                                      avalista_cpf = '$edtAvalistaCpf',
                                      avalista_telefone = '$edtAvalistaTelefone',
                                      avalista_endereco = '$edtAvalistaEndereco'
                                      WHERE id = '$id' ");
                }

                //Configura a assinatura digital
                $sql = mysql_query("UPDATE eventos SET formandos_timestamp = now(), formandos_operador_id = $usuarioId WHERE id = $edtEventoId");

                //Atualiza os dados do formando no boleto
                $boleto_sacado = $edtNome . " - CPF: " . $edtCpf;
                $boleto_endereco1 = $edtEndereco . " - " . $edtComplemento . " - " . $edtBairro;

                $sql = mysql_query("UPDATE boleto SET sacado = '$boleto_sacado', endereco1 = '$boleto_endereco1' WHERE formando_id = '$id'");

                //Exibe a mensagem de alteração com sucesso
                echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Formando alterado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div>";
            
              }

              //RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
              //Captura o id 
              if ($_GET['FormandoId'])
              {

                $FormandoId = $_GET['FormandoId'];
                $EventoId = $_GET['EventoId'];
                
              }
              
              else
              {

                $FormandoId = $_POST['FormandoId'];
                $EventoId = $_POST['EventoId'];
                
              }

              //Monta o sql para busca
              $sql = "SELECT * FROM eventos_formando WHERE id = $FormandoId";

              //Executa a query
              $resultado = mysql_query($sql);

              //Monta o array dos dados
              $campos = mysql_fetch_array($resultado);

              //Efetua o switch para o check de ativo
              switch ($campos[status])
              {
                case 1: $aformar_status = "checked='checked'"; break;
                case 2: $formado_status = "checked='checked'"; break;
                case 3: $desistente_status = "checked='checked'"; break;
                case 4: $aguardando_status = "checked='checked'"; break;
              }

              //Efetua o switch para o campo de situação financeira
              switch ($campos["situacao"])
              {
                case 0:
                  $situacao_1 = "checked='checked'";
                  $situacao_2 = "";
                  break;
                case 1:
                  $situacao_1 = "checked='checked'";
                  $situacao_2 = "";
                  break;
                case 2:
                  $situacao_1 = "";
                  $situacao_2 = "checked='checked'";
                  break;
              }

              //Efetua o switch para o check de participante do culto
              switch ($campos["chk_culto"])
              {
                case 0: $chk_culto = ""; break;
                case 1: $chk_culto = "checked='checked'"; break;
              }

              //Efetua o switch para o check de participante da colacao 
              switch ($campos["chk_colacao"])
              {
                case 0: $chk_colacao = ""; break;
                case 1: $chk_colacao = "checked='checked'"; break;
              }

              //Efetua o switch para o check de participante do jantar
              switch ($campos["chk_jantar"])
              {
                case 0: $chk_jantar = ""; break;
                case 1: $chk_jantar = "checked='checked'"; break;
              }

              //Efetua o switch para o check de participante do baile
              switch ($campos["chk_baile"])
              {
                case 0: $chk_baile = ""; break;
                case 1: $chk_baile = "checked='checked'"; break;
              }

              $chk_desconhecido = "";
              $chk_vivo = "";
              $chk_tim = "";
              $chk_claro = "";
              $chk_oi = "";

              //Efetua o switch para a operadora
              switch ($campos["operadora"])
              {
                case 1: $chk_desconhecido = "checked='checked'"; break;
                case 1: $chk_vivo = "checked='checked'"; break;
                case 2: $chk_tim = "checked='checked'"; break;
                case 3: $chk_claro = "checked='checked'"; break;
                case 4: $chk_oi = "checked='checked'"; break;
              }

              //Efetua o switch para o check de spc
              switch ($campos["chk_spc"])
              {
                case 0: $status_spc = ""; break;
                case 1: $status_spc = "checked='checked'"; break;
              }

              //Efetua o switch para o check de acao de cobranca
              switch ($campos["chk_acao_cobranca"])
              {
                case 0: $status_acao_cobranca = ""; break;
                case 1: $status_acao_cobranca = "checked='checked'"; break;
              }

              //Efetua o switch para o check de assessoria de cobranca
              switch ($campos["chk_assessoria_cobranca"])
              {
                case 0: $status_assessoria_cobranca = ""; break;
                case 1: $status_assessoria_cobranca = "checked='checked'"; break;
              }

              //Efetua o switch para o campo de status do foto e video
              switch ($campos["status_fotovideo"])
              {
                case 0:
                  $statusfv_0 = "checked='checked'";
                  $statusfv_1 = "";
                  $statusfv_2 = "";
                  break;
                case 1:
                  $statusfv_0 = "";
                  $statusfv_1 = "checked='checked'";
                  $statusfv_2 = "";
                  break;
                case 2:
                  $statusfv_0 = "";
                  $statusfv_1 = "";
                  $statusfv_2 = "checked='checked'";
                  break;
              }
              
            ?>
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="100%"> </td>
              </tr>
              <tr>
                <td style="PADDING-BOTTOM: 2px">
                  <input name="FormandoId" type="hidden" value="<?php echo $FormandoId ?>" />
                  <input name="EventoId" type="hidden" value="<?php echo $EventoId ?>" />
									<?php

										//Verifica se existe uma senha cadastrada para o formando
										if ($campos["senha"] == "")
										{

											?>
	                    <input name="edtSenhaFormando" type="hidden" value="1" />
											<?php

										}

									?>
                  <input name="Submit" type="submit" class="button" title="Salva o registro atual" value="Salvar Formando">
                  <input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações">
                </td>
                <td width="36" align="right">
                  <input class="button" title="Retorna ao cadastro de formandos do evento" name="btnVoltar" type="button" id="btnVoltar" value="Retornar aos Formandos do Evento" onclick="wdCarregarFormulario('FormandoEventoCadastra.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" />						
                </td>
              </tr>
            </table>

            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="20">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15"> Informe os dados do formando clique em [Salvar Formando] </td>
                    </tr>
                  </table>             
                </td>
              </tr>
              <tr>
                <td class="dataLabel" width="20%">
                  <span class="dataLabel">Nome do Formando:</span>             
                </td>
                <td colspan="3" class="tabDetailViewDF">           
                  <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 350px ;color: #6666CC; font-weight: bold" maxlength="50" value="<?php echo $campos[nome] ?>" />
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Status:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="130" height="20">
                        <input name="edtStatus" type="radio" value="1" <?php echo $aformar_status ?> />&nbsp;&nbsp;<img src="image/bt_a_formar.png" alt="A se formar" />&nbsp;A se formar
                      </td>
                      <td width="130" height="20">
                        <input name="edtStatus" type="radio" value="2" <?php echo $formado_status ?> />&nbsp;&nbsp;<img src="image/bt_formado.png" alt="Formado" />&nbsp;Formado
                      </td>
                      <td width="250" height="20">
                        <input name="edtStatus" type="radio" value="4" <?php echo $aguardando_status ?> />&nbsp;&nbsp;<img src="image/bt_pendencia.gif" alt="Aguardando Declaração da Rescisão" />&nbsp;Aguardando Declaração da Rescisão
                      </td>
                      <td height="20">
                        <input name="edtStatus" type="radio" value="3" <?php echo $desistente_status ?> />&nbsp;&nbsp;<img src="image/bt_desistente.png" alt="Desistente" />&nbsp;Desistente
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Comercial Foto e Vídeo:</td>
                <td colspan="5" valign="middle" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="200" height="20">
                        <input name="edtStatusFotoVideo" type="radio" value="1" <?php echo $statusfv_1 ?> />&nbsp;&nbsp;Comercializada pela <b>CONSOLI</b>
                      </td>
                      <td width="220" height="20">
                        <input name="edtStatusFotoVideo" type="radio" value="0" <?php echo $statusfv_0 ?> />&nbsp;&nbsp;Comercializada por Outra Empresa
                      </td>
                      <td height="20">
                        <input name="edtStatusFotoVideo" type="radio" value="2" <?php echo $statusfv_2 ?> />&nbsp;&nbsp;Sem Interesse em FV
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Participante:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <input name="chkCulto" type="checkbox" id="chkCulto" value="1" <?php echo $chk_culto ?> > Culto<br/>
                  <input name="chkColacao" type="checkbox" id="chkColacao" value="1" <?php echo $chk_colacao ?> > Colação<br/>
                  <input name="chkJantar" type="checkbox" id="chkJantar" value="1" <?php echo $chk_jantar ?> > Jantar<br/>
                  <input name="chkBaile" type="checkbox" id="chkBaile" value="1" <?php echo $chk_baile ?> > Baile<br/>
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Curso:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <select name="cmbCursoId" id="cmbCursoId" style="width:350px">
                    <option value="0">Selecione uma Opção</option>
<?php while ($lookup_curso = mysql_fetch_object($dados_curso))
{ ?>
                      <option <?php
if ($lookup_curso->id == $campos[curso_id])
{
  echo " selected ";
}
?>
                        value="<?php echo $lookup_curso->id ?>"><?php echo $lookup_curso->nome ?>				 
                      </option>
                  <?php } ?>
                  </select>
                </td>
              </tr>   
              <tr>
                <td class="dataLabel">CPF:</td>
                <td class="tabDetailViewDF">
                  <input name="edtCpf" type="text" class="requerido" id="edtCpf" size="20" maxlength="14" onkeypress="return FormataCampo(document.frmFormandoEventoAltera, 'edtCpf', '999.999.999-99', event);" value="<?php echo $campos[cpf] ?>" onblur="VerificaCPF();"/>
                </td>
                <td class="dataLabel">Data Nascimento:</td>
                <td class="tabDetailViewDF">
<?php
//Define a data do formulário
$objData->strFormulario = "frmFormandoEventoAltera";
//Nome do campo que deve ser criado
$objData->strNome = "edtDataNascimento";
//Valor a constar dentro do campo (p/ alteração)
$objData->strValor = DataMySQLRetornar($campos["data_nascimento"]);

$objData->CriarData();
?>
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Endere&ccedil;o:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <input name="edtEndereco" type="text" class="datafield" id="edtEndereco" style="width: 470px" maxlength="80" value="<?php echo $campos[endereco] ?>">						 
                </td>
              </tr>
              <tr>
                <td class="dataLabel">Complemento:</td>
                <td colspan="3" class="tabDetailViewDF"><input name="edtComplemento" type="text" class="datafield" id="edtComplemento" style="width: 300" size="52" maxlength="50" value="<?php echo $campos[complemento] ?>" /></TD>
              </tr>
              <tr>
                <td class="dataLabel"><span class="dataLabel">Bairro:</span></td>
                <td colspan="3" class="tabDetailViewDF">
                  <input name="edtBairro" type="text" class="datafield" id="edtBairro" style="width: 300" size="52" maxlength="50" value="<?php echo $campos[bairro] ?>">		    		</TD>
              </tr>
              <tr>
                <td class="dataLabel">Cidade:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <select name="cmbCidadeId" id="cmbCidadeId" style="width:350px">
<?php while ($lookup_cidade = mysql_fetch_object($dados_cidade))
{ ?>
                      <option <?php
if ($lookup_cidade->id == $campos[cidade_id])
{
  echo " selected ";
}
?>
                        value="<?php echo $lookup_cidade->id ?>"><?php echo $lookup_cidade->nome ?>				 
                      </option>
<?php } ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="dataLabel">UF:</td>
                <td width="136" class="tabDetailViewDF">
                  <select class="datafield"name="edtUf" id="edtUf">
                    <option selected value="<?php echo $campos[uf] ?>"><?php echo $campos[uf] ?></option>
                    <option value="AC">AC</option>
                    <option value="AL">AL</option>
                    <option value="AM">AM</option>
                    <option value="BA">BA</option>
                    <option value="CE">CE</option>
                    <option value="DF">DF</option>
                    <option value="ES">ES</option>
                    <option value="GO">GO</option>
                    <option value="MA">MA</option>
                    <option value="MG">MG</option>
                    <option value="MS">MS</option>
                    <option value="MT">MT</option>
                    <option value="PA">PA</option>
                    <option value="PB">PB</option>
                    <option value="PE">PE</option>
                    <option value="PI">PI</option>
                    <option value="PR">PR</option>
                    <option value="RJ">RJ</option>
                    <option value="RN">RN</option>
                    <option value="RO">RO</option>
                    <option value="RR">RR</option>
                    <option value="RS">RS</option>
                    <option value="SC">SC</option>
                    <option value="SE">SE</option>
                    <option value="SP">SP</option>
                    <option value="TO">TO</option>
                  </select>						
                </td>
                <td width="130" class="dataLabel">Cep:</td>
                <td width="300" valign="top" class="tabDetailViewDF">
                  <input name="edtCep" type="text" class="datafield" id="edtCep" size="11" maxlength="9" onkeypress="return FormataCampo(document.frmFormandoEventoAltera, 'edtCep', '99999-999', event);" value="<?php echo $campos[cep] ?>">						</TD>
              </tr>       
              <tr>
                <td valign="top" class="dataLabel">Celular:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="120">
                        <input name="edtTelefone" type="text" class="datafield" id="edtTelefone" size="16" maxlength="14" onkeypress="return FormataCampo(document.frmFormandoEventoAltera, 'edtTelefone', '(99) 9999-9999', event);" value="<?php echo $campos[contato] ?>" />
                      </td>
                      <td width="80" valign="middle" height="20">
                        <b>Operadora:</b>
                      </td>
                      <td width="120" height="20">
                        <input name="edtOperadora" type="radio" value="0" style="border: 0px" <?php echo $chk_desconhecido ?> />&nbsp;Não Informado
                      </td>
                      <td height="20">
                        <input name="edtOperadora" type="radio" value="1" style="border: 0px" <?php echo $chk_vivo ?> />&nbsp;Vivo
                      </td>
                      <td height="20">
                        <input name="edtOperadora" type="radio" style="border: 0px" value="2" <?php echo $chk_tim ?> />&nbsp;Tim
                      </td>
                      <td height="20">
                        <input name="edtOperadora" type="radio" style="border: 0px" value="3" <?php echo $chk_claro ?> />&nbsp;Claro
                      </td>
                      <td height="20">
                        <input name="edtOperadora" type="radio" style="border: 0px" value="4" <?php echo $chk_oi ?> />&nbsp;Oi
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td valign="top" width="60" class="dataLabel">Telefone Comercial:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <input name="edtTelefoneComercial" type="text" class="datafield" id="edtTelefoneComercial" size="16" maxlength="14" onkeypress="return FormataCampo(document.frmFormandoEventoAltera, 'edtTelefoneComercial', '(99) 9999-9999', event);" value="<?php echo $campos["telefone_comercial"] ?>" />            
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Telefone Residencial:</td>
                <td class="tabDetailViewDF">
                  <input name="edtTelefoneResidencial" type="text" class="datafield" id="edtTelefoneResidencial" size="16" maxlength="14" onkeypress="return FormataCampo(document.frmFormandoEventoAltera, 'edtTelefoneResidencial', '(99) 9999-9999', event);" value="<?php echo $campos["telefone_residencial"] ?>" />            
                </td>
                <td valign="top" width="60" class="dataLabel">Email:</td>
                <td class="tabDetailViewDF">
                  <input name="edtEmail" type="text" class="datafield" id="edtEmail" style="width: 300; text-transform:lowercase" maxlength="50" value="<?php echo $campos[email] ?>" />            
                </td>
              </tr>        
              <tr>
                <td valign="top" class="dataLabel">Observações:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 60px"><?php echo $campos[observacoes] ?></textarea>
                </td>
              </tr> 
            </table>
            <br/>
            <span class="TituloModulo">Informações Financeiras:</span>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="140" valign="top" class="dataLabel">Situação:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <table width="500" cellpadding="0" cellspacing="0">
                    <tr valign="middle">
                      <td width="130" height="20">
                        <input name="edtSituacao" type="radio" value="1" <?php echo $situacao_1 ?> />&nbsp;&nbsp;<img src="image/bt_receber.gif" alt="Formado em Dia" />&nbsp;Em Dia
                      </td>
                      <td height="20">
                        <input name="edtSituacao" type="radio" value="2" <?php echo $situacao_2 ?> />&nbsp;&nbsp;<img src="image/bt_pendente.gif" alt="Formando com Restrições Financeiras" />&nbsp;Restrições Financeiras
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Status Jurídico:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <input name="chkSpc" type="checkbox" id="chkSpc" value="1" <?php echo $status_spc ?> > SPC<br/>
                  <input name="chkAcaoCobranca" type="checkbox" id="chkAcaoCobranca" value="1" <?php echo $status_acao_cobranca ?> > Ação de Cobrança<br/>
                  <input name="chkAssessoriaCobranca" type="checkbox" id="chkAssessoriaCobranca" value="1" <?php echo $status_assessoria_cobranca ?> > Assessoria de Cobrança
                </td>
              </tr>
              <?php
              /*

                <tr>
                <td class="dataLabel">Valor Contratado:</td>
                <td colspan="3" class="tabDetailViewDF">
                <?php

                //Acerta a variável com o valor a alterar
                $valor_alterar = str_replace(".",",",$campos["valor_contratado"]);

                //Cria um objeto do tipo WDEdit
                $objWDComponente = new WDEditReal();

                //Define nome do componente
                $objWDComponente->strNome = "edtValorContratado";
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
                <td class="dataLabel">Valor Pago:</td>
                <td width="200" class="tabDetailViewDF">
                <?php

                //Acerta a variável com o valor a alterar
                $valor_alterar = str_replace(".",",",$campos["valor_pago"]);

                //Cria um objeto do tipo WDEdit
                $objWDComponente = new WDEditReal();

                //Define nome do componente
                $objWDComponente->strNome = "edtValorPago";
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
                <td width="140" class="dataLabel">Valor Pendente:</td>
                <td class="tabDetailViewDF">
                <?php
                //Acerta a variável com o valor a alterar
                $valor_alterar = str_replace(".",",",$campos["valor_pendente"]);

                //Cria um objeto do tipo WDEdit
                $objWDComponente = new WDEditReal();

                //Define nome do componente
                $objWDComponente->strNome = "edtValorPendente";
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

               */
              ?>

              <tr>
                <td class="dataLabel">
                  <span class="dataLabel">Observações:</span>						
                </td>
                <td colspan="3" class="tabDetailViewDF">
                  <input name="edtObsFinanceiro" type="text" class="datafield" id="edtObsFinanceiro" style="width: 600px" maxlength="150" value="<?php echo $campos[obs_financeiro] ?>"/>
                </td>
              </tr>
            </table>
            
            <br/>
            <span class="TituloModulo">Dados do Avalista:</span>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="140" valign="top" class="dataLabel">Nome:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <input id="edtAvalistaNome" name="edtAvalistaNome" type="text" class="datafield" style="width: 300px" maxlength="50" value="<?php echo $campos['avalista_nome'] ?>" />
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">CPF:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <input id="edtAvalistaCpf" name="edtAvalistaCpf" type="text" class="datafield" style="width: 130px" maxlength="15" value="<?php echo $campos['avalista_cpf'] ?>" />
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Telefone:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <input id="edtAvalistaTelefone" name="edtAvalistaTelefone" type="text" class="datafield" style="width: 130px" maxlength="20" value="<?php echo $campos['avalista_telefone'] ?>" />
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Endereço:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <input id="edtAvalistaEndereco" name="edtAvalistaEndereco" type="text" class="datafield" style="width: 600px" maxlength="150"  value="<?php echo $campos['avalista_endereco'] ?>" />
                </td>
              </tr>
            </table>

            <br/>
            <span class="TituloModulo">Informações de Foto e Vídeo:</span>
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td width="140" valign="top" class="dataLabel">Data Venda:</td>
                <td width="140" valign="middle" class="tabDetailViewDF">
                  <?php
                  //verifica a exibição
                  if ($dados_usuario["evento_fotovideo"] == 1)
                  {

                    $valor = DataMySQLRetornar($campos["data_venda"]);

                    //Define a data do formulário
                    $objData->strFormulario = "frmFormandoEventoAltera";

                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataVenda";

                    $objData->strRequerido = false;
                    //Valor a constar dentro do campo (p/ alteração)

                    if ($valor != "00/00/0000")
                    {

                      $objData->strValor = $valor;
                    }
                    else
                    {

                      $objData->strValor = '';
                    }

                    $objData->CriarData();
                  }
                  else
                  {

                    if ($campos["data_venda"] != "0000-00-00")
                    {

                      echo DataMySQLRetornar($campos["data_venda"]);
                    }
                  }
                  ?>
                </td>
                <td colspan='2' class="tabDetailViewDF">
                  <input name="EmiteAr" type="button" class="button" title="Emite o formulário de AR para este formando" value="Emitir AR" style="width: 100px" onclick="abreJanela('./relatorios/FormularioAR.php?TipoPessoa=1&PessoaId=<?php echo $campos['id'] ?>')" />
                </td>
              </tr>
              <tr>
                <td valign="top" class="dataLabel">Envio Laboratório:</td>
                <td colspan="3" valign="middle" class="tabDetailViewDF">
                  <?php
                  //verifica a exibição
                  if ($dados_usuario["evento_fotovideo"] == 1)
                  {

                    $valor = DataMySQLRetornar($campos["data_envio_lab"]);

                    //Define a data do formulário
                    $objData->strFormulario = "frmFormandoEventoAltera";

                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataEnvioLab";

                    $objData->strRequerido = false;
                    //Valor a constar dentro do campo (p/ alteração)

                    if ($valor != "00/00/0000")
                    {

                      $objData->strValor = $valor;
                    }
                    else
                    {

                      $objData->strValor = '';
                    }

                    $objData->CriarData();
                  }
                  else
                  {

                    if ($campos["data_envio_lab"] != "0000-00-00")
                    {

                      echo DataMySQLRetornar($campos["data_envio_lab"]);
                    }
                  }
                  ?>
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Prev. Laboratório:</td>
                <td width="120" valign="middle" class="tabDetailViewDF">
<?php
//verifica a exibição
if ($dados_usuario["evento_fotovideo"] == 1)
{

$valor = DataMySQLRetornar($campos["data_prev_lab"]);

//Define a data do formulário
$objData->strFormulario = "frmFormandoEventoAltera";

//Nome do campo que deve ser criado
$objData->strNome = "edtDataPrevLab";

$objData->strRequerido = false;

//$objData->strOnfocus = "var data_base = document.frmFormandoEventoAltera.edtDataEnvioLab.value; this.value = adicionarDias(data_base, 40)";
//Valor a constar dentro do campo (p/ alteração)

if ($valor != "00/00/0000")
{

  $objData->strValor = $valor;
}
else
{

  $objData->strValor = '';
}

$objData->CriarData();
}
else
{

if ($campos["data_prev_lab"] != "0000-00-00")
{

  echo DataMySQLRetornar($campos["data_prev_lab"]);
}
}
?>
                </td>
                <td width="140" valign="top" class="dataLabel">Retorno Laboratório:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php
                  //verifica a exibição
                  if ($dados_usuario["evento_fotovideo"] == 1)
                  {

                    $valor = DataMySQLRetornar($campos["data_retorno_lab"]);

                    //Define a data do formulário
                    $objData->strFormulario = "frmFormandoEventoAltera";

                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataRetornoLab";

                    $objData->strRequerido = false;
                    //Valor a constar dentro do campo (p/ alteração)

                    if ($valor != "00/00/0000")
                    {

                      $objData->strValor = $valor;
                    }
                    else
                    {

                      $objData->strValor = '';
                    }

                    $objData->CriarData();
                  }
                  else
                  {

                    if ($campos["data_retorno_lab"] != "0000-00-00")
                    {

                      echo DataMySQLRetornar($campos["data_retorno_lab"]);
                    }
                  }
                  ?>
                </td>
              </tr>
              <tr>
                <td width="140" valign="top" class="dataLabel">Entrega Cliente:</td>
                <td width="120" valign="middle" class="tabDetailViewDF">
                  <?php
                  //verifica a exibição
                  if ($dados_usuario["evento_fotovideo"] == 1)
                  {

                    $valor = DataMySQLRetornar($campos["data_entrega_cliente"]);

                    //Define a data do formulário
                    $objData->strFormulario = "frmFormandoEventoAltera";

                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataEntregaCliente";

                    $objData->strRequerido = false;
                    //Valor a constar dentro do campo (p/ alteração)

                    if ($valor != "00/00/0000")
                    {

                      $objData->strValor = $valor;
                    }
                    else
                    {

                      $objData->strValor = '';
                    }

                    $objData->CriarData();
                  }
                  else
                  {

                    if ($campos["data_entrega_cliente"] != "0000-00-00")
                    {

                      echo DataMySQLRetornar($campos["data_entrega_cliente"]);
                    }
                  }
                  ?>
                </td>
                <td width="140" valign="top" class="dataLabel">Envio Cliente:</td>
                <td valign="middle" class="tabDetailViewDF">
                  <?php
                  //verifica a exibição
                  if ($dados_usuario["evento_fotovideo"] == 1)
                  {

                    $valor = DataMySQLRetornar($campos["data_envio_cliente"]);

                    //Define a data do formulário
                    $objData->strFormulario = "frmFormandoEventoAltera";

                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataEnvioCliente";

                    $objData->strRequerido = false;
                    //Valor a constar dentro do campo (p/ alteração)

                    if ($valor != "00/00/0000")
                    {

                      $objData->strValor = $valor;
                    }
                    else
                    {

                      $objData->strValor = '';
                    }

                    $objData->CriarData();
                  }
                  else
                  {

                    if ($campos["data_envio_cliente"] != "0000-00-00")
                    {

                      echo DataMySQLRetornar($campos["data_envio_cliente"]);
                    }
                  }
                  ?>
                </td>
              </tr>
              <tr valign="middle">
                <td class="dataLabel">Fornecedor:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <select name="cmbFornecedorId" id="cmbFornecedorId" style="width:350px">
                    <option value="0">Selecione uma Opção</option>
                  <?php
                  $seleciona = '';

                  //Monta o while para gerar o combo de escolha
                  while ($lookup_fornecedor = mysql_fetch_object($dados_fornecedor))
                  {
                    ?>
                      <option <?php
                    if ($lookup_fornecedor->id == $campos[lab_fornecedor_id])
                    {
                      echo " selected ";
                    }
                    ?>
                        value="<?php echo $lookup_fornecedor->id ?>"><?php echo $lookup_fornecedor->id . " - " . $lookup_fornecedor->nome ?> </option>
                  <?php } ?>
                  </select>
                </td>
              </tr>		
              <tr>
                <td class="dataLabel">
                  <span class="dataLabel">Obs Foto e Vídeo:</span>						
                </td>
                <td colspan="3" class="tabDetailViewDF">
                  <?php
                  echo nl2br($campos[obs_compra]);
                  ?>
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