<?php 
###########
## Módulo para cadastro de eventos
## Criado: 17/05/2007 - Maycon Edinger
## Alterado: 30/09/2008 - Maycon Edinger
## Alterações: 
## 22/08/2007 - Implementado o grupo de eventos ao cadastro
## 30/09/2008 - Implementado o cadastro de até 3 contatos para o evento
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	
  header("Content-Type: text/html;  charset=ISO-8859-1",true);

}

//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";

?>

<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function valida_form() 
{
	
  var Form;
  Form = document.cadastro;

  if (Form.edtNome.value.length == 0) 
  {
    alert("É necessário Informar o Nome do Evento !");
    Form.edtNome.focus();
    return false;
  }

  if (Form.cmbClienteId.value == 0) 
  {
          alert("É necessário selecionar um Cliente !");
          Form.cmbClienteId.focus();
          return false;
  }

  if (Form.edtData.value.length == 0) 
  {
    alert("É necessário Informar a Data !");
    Form.edtData.focus();
    return false;
  }

  if (Form.edtHora.value.length == 0) 
  {
    alert("É necessário Informar a Hora !");
    Form.edtHora.focus();
    return false;
  }

  return true;
  
}
</script>

<?php 
//Monta o lookup da tabela de clientes
//Monta o SQL
$lista_cliente = "SELECT id, nome FROM clientes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_cliente = mysql_query($lista_cliente);

//Monta o lookup da tabela de regiões
//Monta o SQL
$lista_regiao = "SELECT id, nome FROM regioes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY id";

//Executa a query
$dados_regiao = mysql_query($lista_regiao);

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');

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

<form id="form" name="cadastro" action="sistema.php?ModuloNome=EventoCadastra" method="post" onsubmit="return valida_form()">

  <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
    <tr>
      <td class="text" valign="top">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Cadastramento de Eventos</span></td>
          </tr>
          <tr>
            <td>
              <img src="image/bt_espacohoriz.gif" width="100%" height="12">
            </td>
          </tr>
          <tr>
            <td>
              <table style="display: none" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td valign="midle"><img src="image/bt_ajuda.gif" width="13" height="16" /></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <table id="2" width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100%" class="text">
              <?php
              
                //Recupera os valores vindos do formulário e armazena nas variaveis
                if ($_POST['Submit'])
                {

                  $chkAtivo = $_POST['chkAtivo'];
                  $edtEmpresaId = $empresaId;
                  $edtNome = $_POST['edtNome'];
                  $cmbRegiaoId = $_POST['cmbRegiaoId'];
                  $edtTipo = $_POST['edtTipo'];
                  $edtDescricao = $_POST['edtDescricao'];
                  $edtStatus = $_POST['edtStatus'];
                  $cmbClienteId = $_POST['cmbClienteId'];
                  $edtGrupo = $_POST['edtGrupo'];
                  $edtResponsavelOrca = $_POST['edtResponsavelOrca'];
                  $edtResponsavel = $_POST['edtResponsavel'];

                  $edtContato1 = $_POST['edtContato1'];
                  $edtContatoObs1 = $_POST['edtContatoObs1'];
                  $edtContatoFone1 = $_POST['edtContatoFone1'];

                  $edtContato2 = $_POST['edtContato2'];
                  $edtContatoObs2 = $_POST['edtContatoObs2'];
                  $edtContatoFone2 = $_POST['edtContatoFone2'];

                  $edtContato3 = $_POST['edtContato3'];
                  $edtContatoObs3 = $_POST['edtContatoObs3'];
                  $edtContatoFone3 = $_POST['edtContatoFone3'];

                  $edtContato4 = $_POST['edtContato4'];
                  $edtContatoObs4 = $_POST['edtContatoObs4'];
                  $edtContatoFone4 = $_POST['edtContatoFone4'];

                  $edtContato5 = $_POST['edtContato5'];
                  $edtContatoObs5 = $_POST['edtContatoObs5'];
                  $edtContatoFone5 = $_POST['edtContatoFone5'];

                  $edtContato6 = $_POST['edtContato6'];
                  $edtContatoObs6 = $_POST['edtContatoObs6'];
                  $edtContatoFone6 = $_POST['edtContatoFone6'];

                  $edtData = DataMySQLInserir($_POST['edtData']);
                  $edtDataTermino = DataMySQLInserir($_POST['edtDataTermino']);
                  $edtHora = $_POST['edtHora'];
                  $edtHoraTermino = $_POST['edtHoraTermino'];
                  $edtDuracao = $_POST['edtDuracao'];

                  $edtConfirmados = $_POST['edtConfirmados'];
                  $edtLugaresOcupados = $_POST['edtLugaresOcupados'];

                  $edtAlunosColacao = $_POST['edtAlunosColacao'];
                  $edtAlunosBaile = $_POST['edtAlunosBaile'];
                  $edtConvidadosBaile = $_POST['edtConvidadosBaile'];

                  $edtObservacoes = $_POST['edtObservacoes'];
                  $edtObservacoesFinanceiro = $_POST['edtObservacoesFinanceiro'];
                  $edtExibirObservacao = $_POST['edtExibirObservacao'];
                  $edtOperadorId = $usuarioId;

                  $edtDataJantar = DataMySQLInserir($_POST['edtDataJantar']);
                  $edtDataCertame = DataMySQLInserir($_POST['edtDataCertame']);
                  $edtDataFotoConvite = DataMySQLInserir($_POST['edtDataFotoConvite']);
                  $edtDataEnsaio = DataMySQLInserir($_POST['edtDataEnsaio']);
                  $edtDataCulto = DataMySQLInserir($_POST['edtDataCulto']);
                  $edtDataColacao = DataMySQLInserir($_POST['edtDataColacao']);
                  $edtDataBaile = DataMySQLInserir($_POST['edtDataBaile']);

                  $edtHoraJantar = $_POST['edtHoraJantar'];
                  $edtHoraCertame = $_POST['edtHoraCertame'];
                  $edtHoraFotoConvite = $_POST['edtHoraFotoConvite'];
                  $edtLocalFotoConvite = $_POST['edtLocalFotoConvite'];

                  $edtObsEnsaio = $_POST['edtObsEnsaio'];
                  $edtObsCulto = $_POST['edtObsCulto'];
                  $edtObsColacao = $_POST['edtObsColacao'];
                  $edtObsBaile = $_POST['edtObsBaile'];

                  $chkFotoVideoLiberado = $_POST['chkFotoVideoLiberado'];
                  $edtValorFoto = MoneyMySQLInserir($_POST['edtValorFoto']);
                  $edtValorDVD = MoneyMySQLInserir($_POST['edtValorDVD']);
                  $edtObsFotoVideo = $_POST['edtObsFotoVideo'];

                  $edtQuebras = $_POST['edtQuebras'];

                  $edtNotaFiscal = $_POST['edtNotaFiscal'];
                  $edtPosicaoFinanceira = $_POST['edtPosicaoFinanceira'];
                  $edtValorCulto = MoneyMySQLInserir($_POST['edtValorCulto']);
                  $edtValorColacao = MoneyMySQLInserir($_POST['edtValorColacao']);
                  $edtValorBaile = MoneyMySQLInserir($_POST['edtValorBaile']);
                  $edtValorEvento = MoneyMySQLInserir($_POST['edtValorEvento']);

                  $edtValorDescontoEvento = MoneyMySQLInserir($_POST['edtValorDescontoEvento']);
                  $edtValorGeralEvento = MoneyMySQLInserir($_POST['edtValorGeralEvento']);

                  $edtRoteiro = $_POST['edtRoteiro'];

                  //Monta o sql e executa a query de inserção dos clientes
                  $sql = mysql_query("INSERT INTO eventos (ativo,
                                                          empresa_id, 
                                                          nome, 
                                                          regiao_id,
                                                          descricao,
                                                          tipo,
                                                          status,
                                                          cliente_id,
                                                          grupo_id,
                                                          responsavel_orcamento,
                                                          responsavel,
                                                          contato1,
                                                          contato_obs1,
                                                          contato_fone1,
                                                          contato2,
                                                          contato_obs2,
                                                          contato_fone2,
                                                          contato3,
                                                          contato_obs3,
                                                          contato_fone3,
                                                          contato4,
                                                          contato_obs4,
                                                          contato_fone4,
                                                          contato5,
                                                          contato_obs5,
                                                          contato_fone5,
                                                          contato6,
                                                          contato_obs6,
                                                          contato_fone6,
                                                          contato7,
                                                          contato_obs7,
                                                          contato_fone7,
                                                          contato8,
                                                          contato_obs8,
                                                          contato_fone8,
                                                          contato9,
                                                          contato_obs9,
                                                          contato_fone9,
                                                          contato10,
                                                          contato_obs10,
                                                          contato_fone10,
                                                          contato11,
                                                          contato_obs11,
                                                          contato_fone11,
                                                          contato12,
                                                          contato_obs12,
                                                          contato_fone12,
                                                          data_realizacao,
                                                          hora_realizacao,
                                                          duracao,
                                                          numero_confirmado,
                                                          lugares_ocupados,
                                                          alunos_colacao,
                                                          alunos_baile,
                                                          participantes_baile,
                                                          observacoes,
                                                          observacoes_financeiro,
                                                          exibir_observacoes,
                                                          cadastro_timestamp,
                                                          cadastro_operador_id,
                                                          data_jantar,
                                                          hora_jantar,
                                                          data_certame,
                                                          hora_certame,
                                                          data_foto_convite,
                                                          hora_foto_convite,
                                                          local_foto_convite,
                                                          data_ensaio,
                                                          obs_ensaio,
                                                          data_culto,
                                                          obs_culto,
                                                          data_colacao,
                                                          obs_colacao,
                                                          data_baile,
                                                          obs_baile,
                                                          valor_foto,
                                                          valor_dvd,
                                                          obs_fotovideo,
                                                          foto_video_liberado,
                                                          quebras,
                                                          numero_nf,
                                                          posicao_financeira,
                                                          valor_culto,
                                                          valor_colacao,
                                                          valor_baile,
                                                          valor_evento,
                                                          valor_desconto_evento,
                                                          valor_geral_evento,
                                                          roteiro

                                                          ) VALUES (

                                                          '$chkAtivo',
                                                          '$edtEmpresaId',
                                                          '$edtNome',
                                                          '$cmbRegiaoId',
                                                          '$edtDescricao',
                                                          '$edtTipo',
                                                          '$edtStatus',
                                                          '$cmbClienteId',
                                                          '$edtGrupo',
                                                          '$edtResponsavelOrca',
                                                          '$edtResponsavel',
                                                          '$edtContato1',
                                                          '$edtContatoObs1',
                                                          '$edtContatoFone1',
                                                          '$edtContato2',
                                                          '$edtContatoObs2',
                                                          '$edtContatoFone2',
                                                          '$edtContato3',
                                                          '$edtContatoObs3',
                                                          '$edtContatoFone3',
                                                          '$edtContato4',
                                                          '$edtContatoObs4',
                                                          '$edtContatoFone4',
                                                          '$edtContato5',
                                                          '$edtContatoObs5',
                                                          '$edtContatoFone5',
                                                          '$edtContato6',
                                                          '$edtContatoObs6',
                                                          '$edtContatoFone6',
                                                          '$edtContato7',
                                                          '$edtContatoObs7',
                                                          '$edtContatoFone7',
                                                          '$edtContato8',
                                                          '$edtContatoObs8',
                                                          '$edtContatoFone8',
                                                          '$edtContato9',
                                                          '$edtContatoObs9',
                                                          '$edtContatoFone9',
                                                          '$edtContato10',
                                                          '$edtContatoObs10',
                                                          '$edtContatoFone10',
                                                          '$edtContato11',
                                                          '$edtContatoObs11',
                                                          '$edtContatoFone11',
                                                          '$edtContato12',
                                                          '$edtContatoObs12',
                                                          '$edtContatoFone12',
                                                          '$edtData',
                                                          '$edtHora',
                                                          '$edtDuracao',
                                                          '$edtConfirmados',
                                                          '$edtLugaresOcupados',
                                                          '$edtAlunosColacao',
                                                          '$edtAlunosBaile',
                                                          '$edtConvidadosBaile',
                                                          '$edtObservacoes',
                                                          '$edtObservacoesFinanceiro',
                                                          '$edtExibirObservacao',
                                                          now(),
                                                          '$edtOperadorId',
                                                          '$edtDataJantar',
                                                          '$edtHoraJantar',
                                                          '$edtDataCertame',
                                                          '$edtHoraCertame',
                                                          '$edtDataFotoConvite',
                                                          '$edtHoraFotoConvite',
                                                          '$edtLocalFotoConvite',
                                                          '$edtDataEnsaio',
                                                          '$edtObsEnsaio',				
                                                          '$edtDataCulto',
                                                          '$edtObsCulto',
                                                          '$edtDataColacao',
                                                          '$edtObsColacao',
                                                          '$edtDataBaile',
                                                          '$edtObsBaile',
                                                          '$edtValorFoto',
                                                          '$edtValorDVD',
                                                          '$edtObsFotoVideo',
                                                          '$chkFotoVideoLiberado',
                                                          '$edtQuebras',
                                                          '$edtNotaFiscal',
                                                          '$edtPosicaoFinanceira',
                                                          '$edtValorCulto',
                                                          '$edtValorColacao',
                                                          '$edtValorBaile',
                                                          '$edtValorEvento',
                                                          '$edtValorDescontoEvento',
                                                          '$edtValorGeralEvento',
                                                          '$edtRoteiro'
                                                          );");

                $evento_id = mysql_insert_id();

                //Monta a query para pegar as atividades do evento
                $sql_atividade = "SELECT * FROM atividades WHERE tipo_evento = $edtTipo";

                //Executa a query
                $query_atividade = mysql_query($sql_atividade);

                //Conta o numero de registros da query
                $registros_atividade = mysql_num_rows($query_atividade);

                //Caso não houver registros
                if ($registros_atividade > 0)
                {

                  //efetua o loop na pesquisa
                  while ($dados_atividade = mysql_fetch_array($query_atividade))
                  {

                    $data_evento = $edtData;

                    $atividade_id = $dados_atividade[id];
                    $dias_prazo = $dados_atividade[dias];

                    $data_prazo = subDias("$data_evento", "$dias_prazo");

                    //echo "<br/>Data Evento: $data_evento, - Dias: $dias_prazo, - Prazo: $data_prazo";
                    //echo "<br/>INSERT INTO eventos_atividade (evento_id, atividade_id) VALUES ($evento_id, $atividade_id);";
                    //Insere a atividade ao evento
                    $insere_atividade = mysql_query("INSERT INTO eventos_atividade (evento_id, atividade_id, data_prazo) VALUES ($evento_id, $atividade_id, '$data_prazo');");
                    
                  }
                  
                }

                //Exibe a mensagem de inclusão com sucesso
                echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Evento cadastrado com sucesso !</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 2500)</script>";
                
              }
              
              ?>

              <table cellspacing="0" cellpadding="0" width="520" border="0">
                <tr>
                  <td width="484">

                  </td>
                </tr>
                <tr>
                  <td style="PADDING-BOTTOM: 2px">
                    <input name="Submit" type="submit" class="button" id="Submit" accesskey="S" title="Salva o evento atual" value="Salvar Evento" />
                    <input class="button" title="Limpa o conteúdo dos campos digitados" name="Reset" type="reset" id="Reset" value="Limpar Campos" />
                  </td>
                  <td width="36" align="right">	  </td>
                </tr>
              </table>

              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
                <tr>
                  <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="6">
                    <table cellspacing="0" cellpadding="0" width="100%" border="0">
                      <tr>
                        <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Informe os dados do evento e clique em [Salvar Evento] </td>
                      </tr>
                    </table>             
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel"><span class="dataLabel">Nome do Evento :</span></td>
                  <td colspan="5" class="tabDetailViewDF">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 520px; color: #6666CC; font-weight: bold" maxlength="100">
                        </td>
                        <td width="100">
                          <div align="right">Cadastro Ativo
                            <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" checked>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Região:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <select name="cmbRegiaoId" id="cmbRegiaoId" style="width:350px">
                      <?php
                      
                      //Monta o while para gerar o combo de escolha de regiao
                      while ($lookup_regiao = mysql_fetch_object($dados_regiao))
                      {
                        
                        ?>
                        <option value="<?php echo $lookup_regiao->id ?>"><?php echo $lookup_regiao->id . " - " . $lookup_regiao->nome ?></option>
                        <?php
                        
                      }
                      
                      ?>
                    </select>	
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <textarea name="edtDescricao" wrap="virtual" class="datafield" id="edtDescricao" style="width: 100%; height: 80px"></textarea>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Tipo do Evento:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td width="130" height="20">
                          <input name="edtTipo" type="radio" value="1" checked>&nbsp;Evento Social
                        </td>
                        <td  width="130" height="20">
                          <input name="edtTipo" type="radio" value="2">&nbsp;Formatura
                        </td>
                        <td height="20">
                          <input name="edtTipo" type="radio" value="3">&nbsp;Pregão/Edital
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Status:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <table width="500" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td width="130" height="20">
                          <input name="edtStatus" type="radio" value="0" checked>&nbsp;Em Orçamento
                        </td>
                        <td width="130" height="20">
                          <input name="edtStatus" type="radio" value="1">&nbsp;Em Aberto
                        </td>
                        <td width="130" height="20">
                          <input name="edtStatus" type="radio" value="2">&nbsp;Realizado
                        </td>
                        <td height="20">
                          <input name="edtStatus" type="radio" value="3">&nbsp;Não-Realizado
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td width="180" valign="top" class="dataLabel">Data do Evento:</td>
                  <td width="180" valign="middle" class="tabDetailViewDF">
<?php
//Define a data do formulário
$objData->strFormulario = "cadastro";
//Nome do campo que deve ser criado
$objData->strNome = "edtData";
$objData->strRequerido = true;
//Valor a constar dentro do campo (p/ alteração)
$objData->strValor = "";
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
                  <td width="180" valign="middle" class="dataLabel">Hora Início:</td>
                  <td width="160" valign="middle" class="tabDetailViewDF">
                    <input name="edtHora" type="text" class="requerido" id="edtHora" size="7" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtHora', '99:99', event);" />						 
                  </td>
                  <td width="150" valign="middle" class="dataLabel">Dura&ccedil;&atilde;o:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <input name="edtDuracao" type="text" class="datafield" id="edtDuracao" size="7" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtDuracao', '99:99', event);" />									 
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Data do Jantar:</td>
                  <td valign="middle" class="tabDetailViewDF">
<?php
//Define a data do formulário
$objData->strFormulario = "cadastro";
//Nome do campo que deve ser criado
$objData->strNome = "edtDataJantar";
$objData->strRequerido = false;
//Valor a constar dentro do campo (p/ alteração)
$objData->strValor = "";
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
                  <td valign="middle" class="dataLabel">Hora do Jantar:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <input name="edtHoraJantar" type="text" class="datafield" id="edtHoraJantar" size="7" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtHoraJantar', '99:99', event);" />						 
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Data CERTAME:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    //Define a data do formulário
                    $objData->strFormulario = "cadastro";
                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataCertame";
                    $objData->strRequerido = false;
                    //Valor a constar dentro do campo (p/ alteração)
                    $objData->strValor = "";
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
                  <td valign="middle" class="dataLabel">Hora CERTAME:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <input name="edtHoraCertame" type="text" class="datafield" id="edtHoraCertame" size="7" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtHoraCertame', '99:99', event);" />						 
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Data Foto Convite:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    //Define a data do formulário
                    $objData->strFormulario = "cadastro";
                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataFotoConvite";
                    $objData->strRequerido = false;
                    //Valor a constar dentro do campo (p/ alteração)
                    $objData->strValor = "";
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
                  <td valign="top" class="dataLabel">Hora Foto Convite:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <input name="edtHoraFotoConvite" type="text" class="datafield" id="edtHoraFotoConvite" size="7" maxlength="5" onkeypress="return FormataCampo(document.cadastro, 'edtHoraFotoConvite', '99:99', event);" />						 
                  </td>
                  <td valign="top" class="dataLabel">Local Foto Convite:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <textarea name="edtLocalFotoConvite" wrap="virtual" class="datafield" id="edtLocalFotoConvite" style="font-size: 9px; width: 180px; height: 40px"></textarea>									 
                  </td>
                </tr>                      
                <tr>
                  <td valign="top" class="dataLabel">Data do Ensaio:</td>
                  <td valign="middle" class="tabDetailViewDF">
<?php
//Define a data do formulário
$objData->strFormulario = "cadastro";
//Nome do campo que deve ser criado
$objData->strNome = "edtDataEnsaio";
$objData->strRequerido = false;
//Valor a constar dentro do campo (p/ alteração)
$objData->strValor = "";
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
                  <td valign="top" class="dataLabel">Obs:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <textarea name="edtObsEnsaio" wrap="virtual" class="datafield" id="edtObsEnsaio" style="font-size: 9px; width: 430px; height: 40px"></textarea>
                  </td>             
                </tr>					            
                <tr>
                  <td valign="top" class="dataLabel">Data do Culto:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    //Define a data do formulário
                    $objData->strFormulario = "cadastro";
                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataCulto";
                    $objData->strRequerido = false;
                    //Valor a constar dentro do campo (p/ alteração)
                    $objData->strValor = "";
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
                  <td valign="top" class="dataLabel">Obs:</td>
                  <td colspan="3" valign="top" class="tabDetailViewDF">
                    <textarea name="edtObsCulto" wrap="virtual" class="datafield" id="edtObsCulto" style="font-size: 9px; width: 430px; height: 40px"></textarea>				 
                  </td>             
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Data da Colação:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    //Define a data do formulário
                    $objData->strFormulario = "cadastro";
                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataColacao";
                    $objData->strRequerido = false;
                    //Valor a constar dentro do campo (p/ alteração)
                    $objData->strValor = "";
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
                  <td valign="top" class="dataLabel">Obs:</td>
                  <td colspan="3" valign="top" class="tabDetailViewDF">
                    <textarea name="edtObsColacao" wrap="virtual" class="datafield" id="edtObsColacao" style="font-size: 9px; width: 430px; height: 40px"></textarea>	 
                  </td>             
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Data do Baile:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    //Define a data do formulário
                    $objData->strFormulario = "cadastro";
                    //Nome do campo que deve ser criado
                    $objData->strNome = "edtDataBaile";
                    //Valor a constar dentro do campo (p/ alteração)
                    $objData->strValor = "";
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
                  <td valign="top" class="dataLabel">Obs:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <textarea name="edtObsBaile" wrap="virtual" class="datafield" id="edtObsBaile" style="font-size: 9px; width: 430px; height: 40px"></textarea>				 
                  </td>             
                </tr>					            
                <tr>
                  <td valign="top" class="dataLabel">Cliente:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <select name="cmbClienteId" id="cmbClienteId" style="width:350px">
                      <option value="0">Selecione uma Opção</option>
<?php
//Monta o while para gerar o combo de escolha de funcao
while ($lookup_cliente = mysql_fetch_object($dados_cliente))
{
  ?>
                        <option value="<?php echo $lookup_cliente->id ?>"><?php echo $lookup_cliente->id . " - " . $lookup_cliente->nome ?></option>
  <?php
}
?>
                    </select>	
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel">Grupo:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <table width="500" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td width="150" height="20">
                          <input name="edtGrupo" type="radio" value="1" checked>&nbsp;Consoli Rio do Sul
                        </td>
                        <td width="150" height="20">
                          <input name="edtGrupo" type="radio" value="2">&nbsp;Consoli Joinville
                        </td>
                        <td height="20">
                          <input name="edtGrupo" type="radio" value="3">&nbsp;Gerri Adriane Consoli ME
                        </td>                   
                      </tr>
                    </table>						 						 
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel">Responsável Orçamento:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <input name="edtResponsavelOrca" type="text" class="datafield" id="edtResponsavelOrca" style="width: 300" size="84" maxlength="80" />
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel">Respons. Evento:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <input name="edtResponsavel" type="text" class="datafield" id="edtResponsavel" style="width: 300" size="84" maxlength="80" />
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Contatos:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td width="100" height="20">Nome:</td>
                        <td width="126" height="20">E-Mail:</td>
                        <td height="20">Telefone:</td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato1" type="text" class="datafield" id="edtContato1" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs1" type="text" class="datafield" id="edtContatoObs1" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone1" type="text" class="datafield" id="edtContatoFone1" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone1', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato2" type="text" class="datafield" id="edtContato2" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs2" type="text" class="datafield" id="edtContatoObs2" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone2" type="text" class="datafield" id="edtContatoFone2" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone2', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato3" type="text" class="datafield" id="edtContato3" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs3" type="text" class="datafield" id="edtContatoObs3" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone3" type="text" class="datafield" id="edtContatoFone3" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone3', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato4" type="text" class="datafield" id="edtContato4" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs4" type="text" class="datafield" id="edtContatoObs4" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone4" type="text" class="datafield" id="edtContatoFone4" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone4', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato5" type="text" class="datafield" id="edtContato5" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs5" type="text" class="datafield" id="edtContatoObs5" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone5" type="text" class="datafield" id="edtContatoFone5" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone5', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato6" type="text" class="datafield" id="edtContato6" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs6" type="text" class="datafield" id="edtContatoObs6" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone6" type="text" class="datafield" id="edtContatoFone6" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone6', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato7" type="text" class="datafield" id="edtContato7" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs7" type="text" class="datafield" id="edtContatoObs7" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone7" type="text" class="datafield" id="edtContatoFone7" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone7', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato8" type="text" class="datafield" id="edtContato8" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs8" type="text" class="datafield" id="edtContatoObs8" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone8" type="text" class="datafield" id="edtContatoFone8" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone8', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato9" type="text" class="datafield" id="edtContato9" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs9" type="text" class="datafield" id="edtContatoObs9" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone9" type="text" class="datafield" id="edtContatoFone9" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone9', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato10" type="text" class="datafield" id="edtContato10" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs10" type="text" class="datafield" id="edtContatoObs10" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone10" type="text" class="datafield" id="edtContatoFone10" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone10', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato11" type="text" class="datafield" id="edtContato11" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs11" type="text" class="datafield" id="edtContatoObs11" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone11" type="text" class="datafield" id="edtContatoFone11" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone11', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato12" type="text" class="datafield" id="edtContato12" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs12" type="text" class="datafield" id="edtContatoObs12" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone12" type="text" class="datafield" id="edtContatoFone12" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone12', '(99) 9999-9999', event);">
                        </td>
                      </tr>
                    </table>               							 
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Pessoas Confirmadas:</td>
                  <td colspan="2" class="tabDetailViewDF" valign="middle">
                    <input name="edtConfirmados" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de pessoas confirmadas para o evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" />
                  </td>
                  <td valign="top" class="dataLabel">Lugares Montados:</td>
                  <td colspan="3" class="tabDetailViewDF" valign="middle">
                    <input name="edtLugaresOcupados" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de lugares ocupados para o evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" />
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Alunos na Colação:</td>
                  <td class="tabDetailViewDF" valign="middle">
                    <input name="edtAlunosColacao" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de alunos na colação do evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57)
                          event.returnValue = false;" />
                  </td>
                  <td valign="top" class="dataLabel">Alunos no Baile:</td>
                  <td class="tabDetailViewDF" valign="middle">
                    <input name="edtAlunosBaile" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de alunos no baile do evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57)
                          event.returnValue = false;" />
                  </td>
                  <td valign="top" class="dataLabel">Participantes no Baile:</td>
                  <td class="tabDetailViewDF" valign="middle">
                    <input name="edtConvidadosBaile" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de convidados no baile do evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57)
                          event.returnValue = false;" />
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Informa&ccedil;&otilde;es Complementares:</td>
                  <td colspan="5" class="tabDetailViewDF">
                    <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 300px"></textarea>
                  </td>
                </tr>           
              </table>
              <br/>
              <span class="TituloModulo">Roteiro do Evento:</span>
              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
                <tr>
                  <td width="130" valign="top" class="dataLabel">Descrição do Roteiro:</td>
                  <td colspan="5" class="tabDetailViewDF">
                    <textarea name="edtRoteiro" wrap="virtual" class="datafield" id="edtRoteiro" style="width: 100%; height: 80px"></textarea>
                  </td>
                </tr>
              </table>

              <br/>

              <span class="TituloModulo">Informações Financeiras:</span>

              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0" style="margin-top: 10px;">                      
                <tr>
                  <td valign="top" class="dataLabel">Posição Financeira:</td>
                  <td colspan="3" class="tabDetailViewDF">						   
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td width="150" height="20">
                          <input name="edtPosicaoFinanceira" type="radio" value="1" checked="checked" />&nbsp;A Receber
                        </td>
                        <td width="150" height="20">
                          <input name="edtPosicaoFinanceira" type="radio" value="2" />&nbsp;Recebido
                        </td>
                        <td height="20">
                          <input name="edtPosicaoFinanceira" type="radio" value="3" />&nbsp;Cortesia
                        </td>
                      </tr>
                    </table>	
                  </td>
                </tr>           
                <tr>
                  <td valign="top" class="dataLabel">Número da NF:</td>
                  <td colspan="3" class="tabDetailViewDF">
                    <input name="edtNotaFiscal" type="text" class="datafield" id="edtNotaFiscal" style="width: 110px" maxlength="20">
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Obs. Financeiras:</td>
                  <td colspan="3" class="tabDetailViewDF">
                    <textarea name="edtObservacoesFinanceiro" wrap="virtual" class="datafield" id="edtObservacoesFinanceiro" style="width: 100%; height: 80px"></textarea>
                  </td>
                </tr>
                <tr>
                  <td width="130" valign="top" class="dataLabel">Valor do Culto/Formando:</td>
                  <td width="200" valign="middle" class="tabDetailViewDF">
                    <?php

                      //Cria um objeto do tipo WDEdit 
                      $objWDComponente = new WDEditReal();

                      //Define nome do componente
                      $objWDComponente->strNome = "edtValorCulto";
                      //Define o tamanho do componente
                      $objWDComponente->intSize = 16;
                      //Busca valor definido no XML para o componente
                      $objWDComponente->strValor = "";
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
                  <td valign="middle" width="140" class="dataLabel">Valor da Colação/Formando:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Cria um objeto do tipo WDEdit 
                      $objWDComponente = new WDEditReal();

                      //Define nome do componente
                      $objWDComponente->strNome = "edtValorColacao";
                      //Define o tamanho do componente
                      $objWDComponente->intSize = 16;
                      //Busca valor definido no XML para o componente
                      $objWDComponente->strValor = "";
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
                  <td valign="top" class="dataLabel">Valor do Baile/Formando:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php

                      //Cria um objeto do tipo WDEdit 
                      $objWDComponente = new WDEditReal();

                      //Define nome do componente
                      $objWDComponente->strNome = "edtValorBaile";
                      //Define o tamanho do componente
                      $objWDComponente->intSize = 16;
                      //Busca valor definido no XML para o componente
                      $objWDComponente->strValor = "";
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
                  <td valign="middle" class="dataLabel">Valor do Evento/Formando:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Cria um objeto do tipo WDEdit 
                      $objWDComponente = new WDEditReal();

                      //Define nome do componente
                      $objWDComponente->strNome = "edtValorEvento";
                      //Define o tamanho do componente
                      $objWDComponente->intSize = 16;
                      //Busca valor definido no XML para o componente
                      $objWDComponente->strValor = "";
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
                  <td valign="top" class="dataLabel">Total <b>Desconto</b> no Evento:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Cria um objeto do tipo WDEdit 
                      $objWDComponente = new WDEditReal();

                      //Define nome do componente
                      $objWDComponente->strNome = "edtValorDescontoEvento";
                      //Define o tamanho do componente
                      $objWDComponente->intSize = 16;
                      //Busca valor definido no XML para o componente
                      $objWDComponente->strValor = "";
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
                  <td valign="top" class="dataLabel">Total <b>GERAL</b> Evento:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <?php
                    //Cria um objeto do tipo WDEdit 
                    $objWDComponente = new WDEditReal();

                    //Define nome do componente
                    $objWDComponente->strNome = "edtValorGeralEvento";
                    //Define o tamanho do componente
                    $objWDComponente->intSize = 16;
                    //Busca valor definido no XML para o componente
                    $objWDComponente->strValor = "";
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
              </table>   	  

              <br/>

              <span class="TituloModulo">Informações de Foto e Vídeo:</span>
              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
                <tr>
                  <td valign="top" class="dataLabel">Foto e Vídeo Liberado?:</td>
                  <td colspan="5" class="tabDetailViewDF">
                    <input name="chkFotoVideoLiberado" type="checkbox" id="chkFotoVideoLiberado" value="1">&nbsp;SIM
                  </td>
                </tr>
                <tr>
                  <td width="130" valign="top" class="dataLabel">Valor da Foto:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    //Cria um objeto do tipo WDEdit 
                    $objWDComponente = new WDEditReal();

                    //Define nome do componente
                    $objWDComponente->strNome = "edtValorFoto";
                    //Define o tamanho do componente
                    $objWDComponente->intSize = 16;
                    //Busca valor definido no XML para o componente
                    $objWDComponente->strValor = "";
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
                  <td valign="middle" width="110" class="dataLabel">Valor do DVD:</td>
                  <td colspan="2" width="200" valign="middle" class="tabDetailViewDF">
                    <?php
                    //Cria um objeto do tipo WDEdit 
                    $objWDComponente = new WDEditReal();

                    //Define nome do componente
                    $objWDComponente->strNome = "edtValorDVD";
                    //Define o tamanho do componente
                    $objWDComponente->intSize = 16;
                    //Busca valor definido no XML para o componente
                    $objWDComponente->strValor = "";
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
                  <td valign="top" class="dataLabel">Obs Foto e Vídeo:</td>
                  <td colspan="5" class="tabDetailViewDF">
                    <textarea name="edtObsFotoVideo" wrap="virtual" class="datafield" id="edtObsFotoVideo" style="width: 100%; height: 80px"></textarea>
                  </td>
                </tr>
              </table>

              <br/>

              <span class="TituloModulo">Quebras de Produtos:</span>
              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
                <tr>
                  <td width="130" valign="top" class="dataLabel">Quebras:</td>
                  <td class="tabDetailViewDF">
                    <textarea name="edtQuebras" wrap="virtual" class="datafield" id="edtQuebras" style="width: 100%; height: 80px"></textarea>
                  </td>
                </tr>
              </table>	   	  
            </td>
          </tr>
        </table>  	 
        </form>

    </tr>
  </table>
