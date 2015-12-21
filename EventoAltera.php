<?php 
###########
## Módulo para alteração de evento
## Criado: 25/05/2007 - Maycon Edinger
## Alterado: 22/08/2007 - Maycon Edinger
## Alterações: 
## 22/08/2007 - Implementado o cadastro de grupos
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET['headers'] == 1) 
{
	
  header('Content-Type: text/html;  charset=ISO-8859-1',true);

}

//Desativar o CSS redundante
//<link rel='stylesheet' type='text/css' href='include/workStyle.css'>

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Adiciona o acesso a entidade de criação do componente data
include('CalendarioPopUp.php');

//Cria um objeto do componente data
$objData = new tipData();

//Define que não deve exibir a hora no calendario
$objData->bolExibirHora = false;

//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript();

//pesquisa as diretivas do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";													  													  
							  
//Executa a query
$resultado_usuario = mysql_query($sql_usuario);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_usuario);

//Monta o lookup aa tabela de clientes
//Monta o SQL
$lista_cliente = "SELECT id, nome FROM clientes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_cliente = mysql_query($lista_cliente);

//Monta o lookup aa tabela de regioes
//Monta o SQL
$lista_regiao = "SELECT id, nome FROM regioes WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY id";

//Executa a query
$dados_regiao = mysql_query($lista_regiao);

//Inclui o arquivo para manipulação de valor monetário
include './include/ManipulaMoney.php';
 
?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<script language="JavaScript">
//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  ID = document.getElementById(id);
  ID.style.display = "none";
}

function wdSubmitEventoAltera() 
{
  var Form;
  Form = document.frmEventoAltera;

  if (Form.edtNome.value.length == 0) 
  {
    alert("É necessário Informar o Nome do Evento !");
    Form.edtNome.focus();
    return false;
  }
  
  if (Form.edtClienteId.value == 0) 
  {
    alert("É necessário selecionar um Cliente !");
    Form.edtClienteId.focus();
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

<form name="frmEventoAltera" action="sistema.php?ModuloNome=EventoAltera" method="post" onsubmit="return wdSubmitEventoAltera()">

  <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
    <tr>
      <td class="text" valign="top">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Alteração de Evento</span></td>
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
              
                //Verifica se a flag está vindo de uma postagem para liberar a alteração
                if ($_POST['Submit'])
                {

                  //Recupera os valores do formulario e alimenta as variáveis
                  $id = $_POST['Id'];
                  $chkAtivo = $_POST['chkAtivo'];
                  $edtEmpresaId = $empresaId;
                  $edtRegiaoId = $_POST['edtRegiaoId'];
                  $edtNome = $_POST['edtNome'];
                  $edtDescricao = $_POST['edtDescricao'];
                  $edtTipo = $_POST['edtTipo'];
                  $edtStatus = $_POST['edtStatus'];
                  $edtClienteId = $_POST['edtClienteId'];
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

                  $edtContato7 = $_POST['edtContato7'];
                  $edtContatoObs7 = $_POST['edtContatoObs7'];
                  $edtContatoFone7 = $_POST['edtContatoFone7'];

                  $edtContato8 = $_POST['edtContato8'];
                  $edtContatoObs8 = $_POST['edtContatoObs8'];
                  $edtContatoFone8 = $_POST['edtContatoFone8'];

                  $edtContato9 = $_POST['edtContato9'];
                  $edtContatoObs9 = $_POST['edtContatoObs9'];
                  $edtContatoFone9 = $_POST['edtContatoFone9'];

                  $edtContato10 = $_POST['edtContato10'];
                  $edtContatoObs10 = $_POST['edtContatoObs10'];
                  $edtContatoFone10 = $_POST['edtContatoFone10'];

                  $edtContato11 = $_POST['edtContato11'];
                  $edtContatoObs11 = $_POST['edtContatoObs11'];
                  $edtContatoFone11 = $_POST['edtContatoFone11'];

                  $edtContato12 = $_POST['edtContato12'];
                  $edtContatoObs12 = $_POST['edtContatoObs12'];
                  $edtContatoFone12 = $_POST['edtContatoFone12'];

                  $edtData = DataMySQLInserir($_POST['edtData']);
                  $DataRealizacaoOriginal = $_POST['DataRealizacaoOriginal'];

                  $edtDataTermino = DataMySQLInserir($_POST['edtDataTermino']);
                  $edtHora = $_POST['edtHora'];
                  $edtHoraTermino = $_POST['edtHoraTermino'];
                  $edtDuracao = $_POST['edtDuracao'];

                  $edtConfirmados = $_POST['edtConfirmados'];
                  $edtLugaresOcupados = $_POST['edtLugaresOcupados'];

                  $edtAlunosColacao = $_POST['edtAlunosColacao'];
                  $edtAlunosBaile = $_POST['edtAlunosBaile'];
                  $edtParticipantesBaile = $_POST['edtParticipantesBaile'];

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

                  $edtObsEnsaio = $_POST['edtObsEnsaio'];
                  $edtObsCulto = $_POST['edtObsCulto'];
                  $edtObsColacao = $_POST['edtObsColacao'];
                  $edtObsBaile = $_POST['edtObsBaile'];

                  $edtHoraJantar = $_POST['edtHoraJantar'];
                  $edtHoraCertame = $_POST['edtHoraCertame'];
                  $edtHoraFotoConvite = $_POST['edtHoraFotoConvite'];
                  $edtLocalFotoConvite = $_POST['edtLocalFotoConvite'];

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

                  $edtValorGeralEvento = MoneyMySQLInserir($_POST['edtValorGeralEvento']);
                  $edtValorGeralEventoOrig = $_POST['edtValorGeralEvento'];
                  
                  $edtValorDescontoEvento = MoneyMySQLInserir($_POST['edtValorDescontoEvento']);

                  $edtRoteiro = $_POST['edtRoteiro'];

                  //Executa a query de alteração da conta
                  $sql = mysql_query("UPDATE 
                  											eventos 
                											SET 
	                                      ativo = '$chkAtivo', 
	                                      nome = '$edtNome',
	                                      regiao_id = '$edtRegiaoId',												
	                                      descricao = '$edtDescricao',
	                                      tipo = '$edtTipo', 
	                                      status = '$edtStatus', 
	                                      cliente_id = '$edtClienteId',
	                                      grupo_id = '$edtGrupo', 
	                                      responsavel_orcamento = '$edtResponsavelOrca',
	                                      responsavel = '$edtResponsavel',
	                                      contato1 = '$edtContato1',
	                                      contato_obs1 = '$edtContatoObs1',
	                                      contato_fone1 = '$edtContatoFone1',
	                                      contato2 = '$edtContato2',
	                                      contato_obs2 = '$edtContatoObs2',
	                                      contato_fone2 = '$edtContatoFone2',
	                                      contato3 = '$edtContato3',
	                                      contato_obs3 = '$edtContatoObs3',
	                                      contato_fone3 = '$edtContatoFone3',
	                                      contato4 = '$edtContato4',
	                                      contato_obs4 = '$edtContatoObs4',
	                                      contato_fone4 = '$edtContatoFone4',
	                                      contato5 = '$edtContato5',
	                                      contato_obs5 = '$edtContatoObs5',
	                                      contato_fone5 = '$edtContatoFone5',
	                                      contato6 = '$edtContato6',
	                                      contato_obs6 = '$edtContatoObs6',
	                                      contato_fone6 = '$edtContatoFone6',
	                                      contato7 = '$edtContato7',
	                                      contato_obs7 = '$edtContatoObs7',
	                                      contato_fone7 = '$edtContatoFone7',
	                                      contato8 = '$edtContato8',
	                                      contato_obs8 = '$edtContatoObs8',
	                                      contato_fone8 = '$edtContatoFone8',
	                                      contato9 = '$edtContato9',
	                                      contato_obs9 = '$edtContatoObs9',
	                                      contato_fone9 = '$edtContatoFone9',
	                                      contato10 = '$edtContato10',
	                                      contato_obs10 = '$edtContatoObs10',
	                                      contato_fone10 = '$edtContatoFone10',
	                                      contato11 = '$edtContato11',
	                                      contato_obs11 = '$edtContatoObs11',
	                                      contato_fone11 = '$edtContatoFone11',
	                                      contato12 = '$edtContato12',
	                                      contato_obs12 = '$edtContatoObs12',
	                                      contato_fone12 = '$edtContatoFone12',
	                                      data_realizacao = '$edtData', 
	                                      hora_realizacao = '$edtHora',
	                                      duracao = '$edtDuracao',
	                                      numero_confirmado = '$edtConfirmados',
	                                      lugares_ocupados = '$edtLugaresOcupados',
	                                      alunos_colacao = '$edtAlunosColacao',
	                                      alunos_baile = '$edtAlunosBaile',
	                                      participantes_baile = '$edtParticipantesBaile',
	                                      observacoes = '$edtObservacoes',
	                                      observacoes_financeiro = '$edtObservacoesFinanceiro',
	                                      exibir_observacoes = '$edtExibirObservacao',
	                                      alteracao_timestamp = now(),
	                                      alteracao_operador_id = '$edtOperadorId',
	                                      data_jantar = '$edtDataJantar', 
	                                      hora_jantar = '$edtHoraJantar',
	                                      data_certame = '$edtDataCertame', 
	                                      hora_certame = '$edtHoraCertame', 
	                                      data_foto_convite = '$edtDataFotoConvite', 
	                                      hora_foto_convite = '$edtHoraFotoConvite', 
	                                      local_foto_convite = '$edtLocalFotoConvite', 
	                                      data_ensaio = '$edtDataEnsaio',  
	                                      obs_ensaio = '$edtObsEnsaio',
	                                      data_culto = '$edtDataCulto',  
	                                      obs_culto = '$edtObsCulto',
	                                      data_colacao = '$edtDataColacao',  
	                                      obs_colacao = '$edtObsColacao',
	                                      data_baile = '$edtDataBaile',  
	                                      obs_baile = '$edtObsBaile',
	                                      valor_foto = '$edtValorFoto',
	                                      valor_dvd = '$edtValorDVD',
	                                      obs_fotovideo = '$edtObsFotoVideo',
	                                      foto_video_liberado = '$chkFotoVideoLiberado',
	                                      quebras = '$edtQuebras',
	                                      numero_nf = '$edtNotaFiscal',
	                                      posicao_financeira = '$edtPosicaoFinanceira',
	                                      valor_culto = '$edtValorCulto',
	                                      valor_colacao = '$edtValorColacao',
	                                      valor_baile = '$edtValorBaile',
	                                      valor_evento = '$edtValorEvento',
	                                      valor_desconto_evento = '$edtValorDescontoEvento',
	                                      valor_geral_evento = '$edtValorGeralEvento',
	                                      roteiro = '$edtRoteiro'
                                      WHERE 
                                      	id = $id");
                  

                  //Verifica se o status do evento é Realizado										
                  if ($edtStatus == 2)
                  {

                    //Verifica e marca os formandos do evento como realizado
                    $sql = mysql_query("UPDATE 
                    											eventos_formando 
                  											SET 
                  												status = 2 
                												WHERE 
                													evento_id = $id 
              													AND 
              														status < 3");

                    $mensagem_formando = 'Evento marcado como realizado. Os formandos foram marcados como FORMADOS.';
                    
                  }

                  //Verifica se alterou a data do evento
                  if ($DataRealizacaoOriginal != $edtData)
                  {

                    //Caso a data for alterada, verifica se ha atividades vinculadas
                    //Monta a query para pegar as atividades do evento
                    $sql_atividade = "SELECT * FROM eventos_atividade WHERE evento_id = $id";

                    //Executa a query
                    $query_atividade = mysql_query($sql_atividade);

                    //Conta o numero de registros da query
                    $registros_atividade = mysql_num_rows($query_atividade);

                    //Caso não houver registros
                    if ($registros_atividade > 0)
                    {

                      $data_evento = $edtData;

                      //efetua o loop na pesquisa
                      while ($dados_atividade = mysql_fetch_array($query_atividade))
                      {

                        $atividade_id = $dados_atividade[id];
                        $atividade_busca_id = $dados_atividade[atividade_id];

                        //Busca o numero de dias desta atividade
                        $sql_dias_atividade = "SELECT dias FROM atividades WHERE id = $atividade_busca_id";

                        //Executa a query
                        $query_dias_atividade = mysql_query($sql_dias_atividade);

                        $dados_dias_atividade = mysql_fetch_array($query_dias_atividade);

                        $dias_prazo = $dados_dias_atividade[dias];

                        $data_prazo = subDias("$data_evento", "$dias_prazo");

                        //Insere a atividade ao evento
                        $insere_atividade = mysql_query("UPDATE eventos_atividade SET data_prazo = '$data_prazo' WHERE id = $atividade_id;");
                        
                      }

                    }

                    else
                    {

                      //Caso nao haja atividades,entao insere as pendencias
                      //Monta a query para pegar as atividades do evento
                      $sql_atividade = "SELECT * FROM atividades WHERE tipo_evento = 2";

                      //Executa a query
                      $query_atividade = mysql_query($sql_atividade);

                      //Conta o numero de registros da query
                      $registros_atividade = mysql_num_rows($query_atividade);

                      $data_evento = $edtData;

                      //Caso não houver registros
                      if ($registros_atividade > 0)
                      {

                        //efetua o loop na pesquisa
                        while ($dados_atividade = mysql_fetch_array($query_atividade))
                        {

                          $atividade_id = $dados_atividade[id];
                          $dias_prazo = $dados_atividade[dias];

                          $data_prazo = subDias("$data_evento", "$dias_prazo");

                          //Insere a atividade ao evento
                          $insere_atividade = mysql_query("INSERT INTO eventos_atividade (evento_id, atividade_id, data_prazo) VALUES ($id, $atividade_id, '$data_prazo');");
                        }
                        
                      }

                    }

                  }

                  //Exibe a mensagem de alteração com sucesso
                  echo "<div id='99'><table width='100%' border='0' align='left' cellpadding='0' cellspacing='0' class='text'><tr><td height='22' width='20' valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px'><img src='./image/bt_informacao.gif' border='0' /></td><td valign='middle' bgcolor='#FFFFCD' style='border: solid 1px; padding-left: 4px; border-left: 0px'><strong>Evento alterado com sucesso ! $mensagem_formando</strong></td></tr><tr><td>&nbsp</td></tr></table></td></tr><tr><td></div><script language='javascript'>setTimeout('oculta(99)', 3500);</script>";
               
                }

                //RECEBENDO OS VALORES PARA A ALTERAÇAO DO REGISTRO
                //Captura o id da cleinte a alterar
                if ($_GET['Id'])
                {
                  $EventoId = $_GET['Id'];
                }
                
                else
                {
                  $EventoId = $_POST['Id'];
                }

                //Monta o sql para busca da conta
                $sql = "SELECT * FROM eventos WHERE id = $EventoId";

                //Executa a query
                $resultado = mysql_query($sql);

                //Monta o array dos dados
                $campos = mysql_fetch_array($resultado);

                switch ($campos[tipo])
                {

                  //Caso for evento social
                  case 1:
                    $tipo_eve = 'checked';
                    $tipo_for = '';
                    $tipo_edital = '';
                    $tipo_casan = '';
                    break;
                  //Formatura
                  case 2:
                    $tipo_eve = '';
                    $tipo_for = 'checked';
                    $tipo_edital = '';
                    $tipo_casan = '';
                    break;
                  //Edital
                  case 3:
                    $tipo_eve = '';
                    $tipo_for = '';
                    $tipo_edital = 'checked';
                    $tipo_casan = '';
                    break;
                  //Formatura
                  case 4:
                    $tipo_eve = '';
                    $tipo_for = '';
                    $tipo_edital = '';
                    $tipo_casan = 'checked';
                    break;
                }

                switch ($campos[ativo])
                {
                  case 0: $ativo_status = "value='1'"; break;
                  case 1: $ativo_status = "value='1' checked"; break;
                }

                switch ($campos[foto_video_liberado])
                {
                  case 0: $foto_video_liberado_status = ""; break;
                  case 1: $foto_video_liberado_status = "checked"; break;
                }

                //Efetua o switch para o campo de situacao
                switch ($campos[status])
                {
                  case 0:
                    $sit_0 = 'checked';
                    $sit_1 = '';
                    $sit_2 = '';
                    $sit_3 = '';
                    break;
                  case 1:
                    $sit_0 = '';
                    $sit_1 = 'checked';
                    $sit_2 = '';
                    $sit_3 = '';
                    break;
                  case 2:
                    $sit_0 = '';
                    $sit_1 = '';
                    $sit_2 = 'checked';
                    $sit_3 = '';
                    break;
                  case 3:
                    $sit_0 = '';
                    $sit_1 = '';
                    $sit_2 = '';
                    $sit_3 = 'checked';
                    break;
                }

                //Efetua o switch para o campo de grupo
                switch ($campos[grupo_id])
                {
                  case 1:
                    $grupo_1 = 'checked';
                    $grupo_2 = '';
                    $grupo_3 = '';
                  break;
                  case 2:
                    $grupo_1 = '';
                    $grupo_2 = 'checked';
                    $grupo_3 = '';
                  break;
                  case 3:
                    $grupo_1 = '';
                    $grupo_2 = '';
                    $grupo_3 = 'checked';
                  break;
                }

                //Efetua o switch para o campo de exxibir informações financeiras
                switch ($campos[exibir_observacoes])
                {

                  case 0:
                    $obs_fin_1 = 'checked';
                    $obs_fin_2 = '';
                    break;
                  case 1:
                    $obs_fin_1 = '';
                    $obs_fin_2 = 'checked';
                    break;
                }

                //Efetua o switch para o campo de posicao financeira
                switch ($campos[posicao_financeira])
                {
                  case 1:
                    $financeiro_1 = 'checked';
                    $financeiro_2 = '';
                    $financeiro_3 = '';
                    break;
                  case 2:
                    $financeiro_1 = '';
                    $financeiro_2 = 'checked';
                    $financeiro_3 = '';
                    break;
                  case 3:
                    $financeiro_1 = '';
                    $financeiro_2 = '';
                    $financeiro_3 = 'checked';
                    break;
                }
                
              ?>
              <table cellspacing="0" cellpadding="0" width="100%" border="0">
                <tr>
                  <td style="PADDING-BOTTOM: 2px">
                    <input name="Id" type="hidden" value="<?php echo $EventoId ?>" />
                    <input name="DataRealizacaoOriginal" type="hidden" value="<?php echo $campos[data_realizacao] ?>" />
                    <input name="Submit" type="submit" class="button" accesskey="S" title="Salva o evento atual" value="Salvar Evento" />
                    <input class="button" title="Cancela as alterações efetuadas no registro" name="Reset" type="reset" id="Reset" value="Cancela Alterações" />
                  </td>
                  <td width="36" align="right">
                    <input class="button" title="Retorna a exibição do registro" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Evento" onclick="wdCarregarFormulario('EventoExibe.php?EventoId=<?php echo $EventoId ?>&headers=1','conteudo')" />						
                  </td>
                </tr>
              </table>

              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
                <tr>
                  <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="6">
                    <table cellspacing="0" cellpadding="0" width="100%" border="0">
                      <tr>
                        <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Informe os dados do evento e clique em [Salvar Evento]<br />
                          <br />
                          <span class="style1">Aten&ccedil;&atilde;o:</span> Esta transa&ccedil;&atilde;o ser&aacute; monitorada pelo sistema e ser&aacute; gerado um log da atividade para fins de auditoria. 
                        </td>
                      </tr>
                    </table>             
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel">
                    <span class="dataLabel">Nome do Evento :</span>             
                  </td>
                  <td colspan="5" class="tabDetailViewDF">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td height="20">										
                          <input name="edtNome" type="text" class="requerido" id="edtNome" style="width: 450px; color: #6666CC; font-weight: bold" maxlength="100" value="<?php echo $campos[nome] ?>">
                        </td>
                        <td width="100">
                          <div align="right">Cadastro Ativo
                            <input name="chkAtivo" type="checkbox" id="chkAtivo" value="1" <?php echo $ativo_status ?>>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Região:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <select name="edtRegiaoId" id="edtRegiaoId" style="width:350px">
                      <?php

                        while ($lookup_regiao = mysql_fetch_object($dados_regiao))
                        {

                          ?>
                        <option <?php
                          if ($lookup_regiao->id == $campos[regiao_id])
                          {
                            echo " selected ";
                          }
                          ?>
                          value="<?php echo $lookup_regiao->id ?>"><?php echo $lookup_regiao->id . " - " . $lookup_regiao->nome ?></option>
                      <?php } ?>
                    </select>		
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Descri&ccedil;&atilde;o:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <textarea name="edtDescricao" wrap="virtual" class="datafield" id="edtDescricao" style="width: 100%; height: 80px"><?php echo $campos[descricao] ?></textarea>
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Tipo de Evento:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td width="130" height="20">
                          <input name="edtTipo" type="radio" value="1" <?php echo $tipo_eve ?>>&nbsp;Evento Social
                        </td>
                        <td width="130" height="20">
                          <input name="edtTipo" type="radio" value="2" <?php echo $tipo_for ?>>&nbsp;Formatura
                        </td>
                        <td height="20">
                          <input name="edtTipo" type="radio" value="3" <?php echo $tipo_edital ?>>&nbsp;Pregão/Edital
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
                          <input name="edtStatus" type="radio" value="0" <?php echo $sit_0 ?>>&nbsp;Em Orçamento
                        </td>
                        <td width="130" height="20">
                          <input name="edtStatus" type="radio" value="1" <?php echo $sit_1 ?>>&nbsp;Em Aberto
                        </td>
                        <td width="130" height="20">
                          <input name="edtStatus" type="radio" value="2" <?php echo $sit_2 ?>>&nbsp;Realizado
                        </td>
                        <td height="20">
                          <input name="edtStatus" type="radio" value="3" <?php echo $sit_3 ?>>&nbsp;Não-Realizado
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td width="180" valign="top" class="dataLabel">Data do Evento:</td>
                  <td width="160" valign="middle" class="tabDetailViewDF">
                    <?php

                      //Define a data do formulário
                      $objData->strFormulario = "frmEventoAltera";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtData";
                      $objData->strRequerido = true;
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = DataMySQLRetornar($campos[data_realizacao]);
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
                  <td width="150" valign="middle" class="dataLabel">Hora:</td>
                  <td width="130" valign="middle" class="tabDetailViewDF">
                    <input name="edtHora" type="text" class="requerido" id="edtHora" size="7" maxlength="5" onkeypress="return FormataCampo(document.frmEventoAltera, 'edtHora', '99:99', event);" value="<?php echo $campos[hora_realizacao] ?>" />						 
                  </td>
                  <td width="150" valign="middle" class="dataLabel">Dura&ccedil;&atilde;o:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <input name="edtDuracao" type="text" class="datafield" id="edtDuracao" size="7" maxlength="5" onkeypress="return FormataCampo(document.frmEventoAltera, 'edtDuracao', '99:99', event);" value="<?php echo $campos[duracao] ?>" />									 
                  </td>
                </tr>           
                <tr>
                  <td valign="top" class="dataLabel">Data do Jantar:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php

                      //Define a data do formulário
                      $objData->strFormulario = "frmEventoAltera";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtDataJantar";
                      $objData->strRequerido = false;
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = DataMySQLRetornar($campos[data_jantar]);
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
                    <input name="edtHoraJantar" type="text" class="datafield" id="edtHoraJantar" size="7" value="<?php echo $campos[hora_jantar] ?>" maxlength="5" onkeypress="return FormataCampo(document.frmEventoAltera, 'edtHoraJantar', '99:99', event);" />						 
                  </td>
                </tr>            
                <tr>
                  <td valign="top" class="dataLabel">Data CERTAME:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Define a data do formulário
                      $objData->strFormulario = "frmEventoAltera";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtDataCertame";
                      $objData->strRequerido = false;
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = DataMySQLRetornar($campos[data_certame]);
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
                    <input name="edtHoraCertame" type="text" class="datafield" id="edtHoraCertame" size="7" value="<?php echo $campos[hora_certame] ?>" maxlength="5" onkeypress="return FormataCampo(document.frmEventoAltera, 'edtHoraCertame', '99:99', event);" />						 
                  </td>
                </tr>            
                <tr>
                  <td valign="top" class="dataLabel">Data Foto Convite:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Define a data do formulário
                      $objData->strFormulario = "frmEventoAltera";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtDataFotoConvite";
                      $objData->strRequerido = false;
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = DataMySQLRetornar($campos[data_foto_convite]);
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
                    <input name="edtHoraFotoConvite" type="text" class="datafield" id="edtHoraFotoConvite" value="<?php echo $campos[hora_foto_convite] ?>" size="7" maxlength="5" onkeypress="return FormataCampo(document.frmEventoAltera, 'edtHoraFotoConvite', '99:99', event);" />						 
                  </td>
                  <td valign="top" class="dataLabel">Local Foto Convite:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <textarea name="edtLocalFotoConvite" wrap="virtual" class="datafield" id="edtLocalFotoConvite" style="font-size: 9px; width: 180px; height: 40px"><?php echo $campos[local_foto_convite] ?></textarea>									 
                  </td>
                </tr>                         
                <tr>
                  <td valign="top" class="dataLabel">Data do Ensaio:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Define a data do formulário
                      $objData->strFormulario = "frmEventoAltera";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtDataEnsaio";
                      $objData->strRequerido = false;
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = DataMySQLRetornar($campos[data_ensaio]);
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
                    <textarea name="edtObsEnsaio" wrap="virtual" class="datafield" id="edtObsEnsaio" style="font-size: 9px; width: 380px; height: 40px"><?php echo $campos[obs_ensaio] ?></textarea>
                  </td>             
                </tr>					            
                <tr>
                  <td valign="top" class="dataLabel">Data do Culto:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Define a data do formulário
                      $objData->strFormulario = "frmEventoAltera";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtDataCulto";
                      $objData->strRequerido = false;
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = DataMySQLRetornar($campos[data_culto]);
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
                    <textarea name="edtObsCulto" wrap="virtual" class="datafield" id="edtObsCulto" style="font-size: 9px; width: 380px; height: 40px"><?php echo $campos[obs_culto] ?></textarea>				 
                  </td>             
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Data da Colação:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Define a data do formulário
                      $objData->strFormulario = "frmEventoAltera";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtDataColacao";
                      $objData->strRequerido = false;
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = DataMySQLRetornar($campos[data_colacao]);
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
                    <textarea name="edtObsColacao" wrap="virtual" class="datafield" id="edtObsColacao" style="font-size: 9px; width: 380px; height: 40px"><?php echo $campos[obs_colacao] ?></textarea>	 
                  </td>             
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Data do Baile:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Define a data do formulário
                      $objData->strFormulario = "frmEventoAltera";
                      //Nome do campo que deve ser criado
                      $objData->strNome = "edtDataBaile";
                      $objData->strRequerido = false;
                      //Valor a constar dentro do campo (p/ alteração)
                      $objData->strValor = DataMySQLRetornar($campos[data_baile]);
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
                    <textarea name="edtObsBaile" wrap="virtual" class="datafield" id="edtObsBaile" style="font-size: 9px; width: 380px; height: 40px"><?php echo $campos[obs_baile] ?></textarea>				 
                  </td>             
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Cliente:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <select name="edtClienteId" id="edtClienteId" style="width:350px">
                    <?php while ($lookup_cliente = mysql_fetch_object($dados_cliente))
                    { ?>
                        <option <?php
                      if ($lookup_cliente->id == $campos[cliente_id])
                      {
                        echo " selected ";
                      }
                      ?>
                          value="<?php echo $lookup_cliente->id ?>"><?php echo $lookup_cliente->id . " - " . $lookup_cliente->nome ?></option>
                      <?php } ?>
                    </select>		
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel">Grupo:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <table width="500" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td width="150" height="20">
                          <input name="edtGrupo" type="radio" value="1" <?php echo $grupo_1 ?>>&nbsp;Consoli Rio do Sul
                        </td>
                        <td width="150" height="20">
                          <input name="edtGrupo" type="radio" value="2" <?php echo $grupo_2 ?>>&nbsp;Consoli Joinville
                        </td>
                        <td height="20">
                          <input name="edtGrupo" type="radio" value="3" <?php echo $grupo_3 ?>>&nbsp;Gerri Adriane Consoli ME
                        </td>                   
                      </tr>
                    </table>								 						 
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel">Respons. Orçamento:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <input name="edtResponsavelOrca" type="text" class="datafield" id="edtResponsavelOrca" style="width: 300" size="84" maxlength="80" value="<?php echo $campos[responsavel_orcamento] ?>" />
                  </td>
                </tr>
                <tr>
                  <td class="dataLabel">Respons. Evento:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <input name="edtResponsavel" type="text" class="datafield" id="edtResponsavel" style="width: 300" size="84" maxlength="80" value="<?php echo $campos[responsavel] ?>" />
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Contatos:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <table width="100%" cellpadding="0" cellspacing="0">
                      <tr valign="middle">
                        <td width="100" height="20">
                          Nome:
                        </td>
                        <td width="126" height="20">
                          E-Mail:
                        </td>
                        <td height="20">
                          Telefone:
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato1" type="text" class="datafield" id="edtContato1" value="<?php echo $campos[contato1] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs1" type="text" class="datafield" id="edtContatoObs1" value="<?php echo $campos[contato_obs1] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone1" type="text" class="datafield" id="edtContatoFone1" value="<?php echo $campos[contato_fone1] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone1', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato2" type="text" class="datafield" id="edtContato2" value="<?php echo $campos[contato2] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs2" type="text" class="datafield" id="edtContatoObs2" value="<?php echo $campos[contato_obs2] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone2" type="text" class="datafield" id="edtContatoFone2" value="<?php echo $campos[contato_fone2] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone2', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato3" type="text" class="datafield" id="edtContato3" value="<?php echo $campos[contato3] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs3" type="text" class="datafield" id="edtContatoObs3" value="<?php echo $campos[contato_obs3] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone3" type="text" class="datafield" id="edtContatoFone3" value="<?php echo $campos[contato_fone3] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone3', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato4" type="text" class="datafield" id="edtContato4" value="<?php echo $campos[contato4] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs4" type="text" class="datafield" id="edtContatoObs4" value="<?php echo $campos[contato_obs4] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone4" type="text" class="datafield" id="edtContatoFone4" value="<?php echo $campos[contato_fone4] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone4', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato5" type="text" class="datafield" id="edtContato5" value="<?php echo $campos[contato5] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs5" type="text" class="datafield" id="edtContatoObs5" value="<?php echo $campos[contato_obs5] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone5" type="text" class="datafield" id="edtContatoFone5" value="<?php echo $campos[contato_fone5] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone5', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato6" type="text" class="datafield" id="edtContato6" value="<?php echo $campos[contato6] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs6" type="text" class="datafield" id="edtContatoObs6" value="<?php echo $campos[contato_obs6] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone6" type="text" class="datafield" id="edtContatoFone6" value="<?php echo $campos[contato_fone6] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone6', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato7" type="text" class="datafield" id="edtContato7" value="<?php echo $campos[contato7] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs7" type="text" class="datafield" id="edtContatoObs7" value="<?php echo $campos[contato_obs7] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone7" type="text" class="datafield" id="edtContatoFone7" value="<?php echo $campos[contato_fone7] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone7', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato8" type="text" class="datafield" id="edtContato8" value="<?php echo $campos[contato8] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs8" type="text" class="datafield" id="edtContatoObs8" value="<?php echo $campos[contato_obs8] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone8" type="text" class="datafield" id="edtContatoFone8" value="<?php echo $campos[contato_fone8] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone8', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato9" type="text" class="datafield" id="edtContato9" value="<?php echo $campos[contato9] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs9" type="text" class="datafield" id="edtContatoObs9" value="<?php echo $campos[contato_obs9] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone9" type="text" class="datafield" id="edtContatoFone9" value="<?php echo $campos[contato_fone9] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone9', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato10" type="text" class="datafield" id="edtContato10" value="<?php echo $campos[contato10] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs10" type="text" class="datafield" id="edtContatoObs10" value="<?php echo $campos[contato_obs10] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone10" type="text" class="datafield" id="edtContatoFone10" value="<?php echo $campos[contato_fone10] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone10', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato11" type="text" class="datafield" id="edtContato11" value="<?php echo $campos[contato11] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs11" type="text" class="datafield" id="edtContatoObs11" value="<?php echo $campos[contato_obs11] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone11" type="text" class="datafield" id="edtContatoFone11" value="<?php echo $campos[contato_fone11] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone11', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                      <tr valign="middle">
                        <td height="20">
                          <input name="edtContato12" type="text" class="datafield" id="edtContato12" value="<?php echo $campos[contato12] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoObs12" type="text" class="datafield" id="edtContatoObs12" value="<?php echo $campos[contato_obs12] ?>" style="width: 240px" maxlength="50">
                        </td>
                        <td height="20">
                          <input name="edtContatoFone12" type="text" class="datafield" id="edtContatoFone12" value="<?php echo $campos[contato_fone12] ?>" size="16" maxlength="14" onkeypress="return FormataCampo(document.cadastro, 'edtContatoFone12', '(99) 9999-9999', event);" />
                        </td>
                      </tr>
                    </table>               							 
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Pessoas Confirmadas:</td>
                  <td colspan="2" class="tabDetailViewDF" valign="middle">
                    <input name="edtConfirmados" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de pessoas confirmadas para o evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos[numero_confirmado] ?>"/>
                  </td>
                  <td valign="top" class="dataLabel">Lugares Montados:</td>
                  <td colspan="3" class="tabDetailViewDF" valign="middle">
                    <input name="edtLugaresOcupados" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de lugares ocupados para o evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos[lugares_ocupados] ?>" />
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Alunos na Colação:</td>
                  <td class="tabDetailViewDF" valign="middle">
                    <input name="edtAlunosColacao" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de alunos na colação do evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos[alunos_colacao] ?>" />
                  </td>
                  <td valign="top" class="dataLabel">Alunos no Baile:</td>
                  <td class="tabDetailViewDF" valign="middle">
                    <input name="edtAlunosBaile" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de alunos no baile do evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos[alunos_baile] ?>" />
                  </td>
                  <td valign="middle" class="dataLabel">Participantes no Baile:</td>
                  <td class="tabDetailViewDF" valign="middle">
                    <input name="edtParticipantesBaile" type="text" class="datafield" style="width: 36px" maxlength="5" title="Informe a quantidade de participantes no baile do evento" onkeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $campos[participantes_baile] ?>" />
                  </td>
                </tr>					                   
                <tr>
                  <td width="130" valign="top" class="dataLabel">Valor do Culto/Formando:</td>
                  <td width="180" valign="middle" class="tabDetailViewDF">
                    <?php

                      //Acerta a variável com o valor a alterar
                      $valor_alterar = number_format($campos[valor_culto],2,',','.');
                      
                      //Caso tenha valor
                      if ($campos[valor_culto] > 0 && $usuarioNome != 'Zulaine')
                      {
                        
                        echo "$valor_alterar<br/>
                              <input type='hidden' id='edtValorCulto' name='edtValorCulto' value='$valor_alterar' />";
                        
                      }
                      
                      else
                      {

                        //Cria um objeto do tipo WDEdit 
                        $objWDComponente = new WDEditReal();

                        //Define nome do componente
                        $objWDComponente->strNome = "edtValorCulto";
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
                        
                      }

                    ?>						 
                  </td>
                  <td valign="middle" width="110" class="dataLabel">Valor da Colação/Formando:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Acerta a variável com o valor a alterar
                      $valor_alterar = number_format($campos[valor_colacao],2,',','.');
                      
                      //Caso tenha valor
                      if ($campos[valor_colacao] > 0 && $usuarioNome != 'Zulaine')
                      {
                        
                        echo "$valor_alterar<br/>
                              <input type='hidden' id='edtValorColacao' name='edtValorColacao' value='$valor_alterar' />";
                        
                      }
                      
                      else
                      {

                        //Cria um objeto do tipo WDEdit 
                        $objWDComponente = new WDEditReal();

                        //Define nome do componente
                        $objWDComponente->strNome = "edtValorColacao";
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
                      
                      }
                    
                    ?>						 
                  </td>
                </tr>
                <tr> 
                  <td valign="middle" class="dataLabel">Total do Baile/Formando:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Acerta a variável com o valor a alterar
                      $valor_alterar = number_format($campos[valor_baile],2,',','.');
                      
                      //Caso tenha valor
                      if ($campos[valor_baile] > 0 && $usuarioNome != 'Zulaine')
                      {
                        
                        echo "$valor_alterar<br/>
                              <input type='hidden' id='edtValorBaile' name='edtValorBaile' value='$valor_alterar' />";
                        
                      }
                      
                      else
                      {

                        //Cria um objeto do tipo WDEdit 
                        $objWDComponente = new WDEditReal();

                        //Define nome do componente
                        $objWDComponente->strNome = "edtValorBaile";
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
                      
                      }
                    
                    ?>						 
                  </td>
                  <td valign="middle" class="dataLabel">Total do Evento/Formando:</td>
                  <td colspan="3" valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Acerta a variável com o valor a alterar
                      $valor_alterar = number_format($campos[valor_evento],2,',','.');
                      
                      //Caso tenha valor
                      if ($campos[valor_evento] > 0 && $usuarioNome != 'Zulaine')
                      {
                        
                        echo "$valor_alterar<br/>
                              <input type='hidden' id='edtValorEvento' name='edtValorEvento' value='$valor_alterar' />";
                        
                      }
                      
                      else
                      {

                        //Cria um objeto do tipo WDEdit 
                        $objWDComponente = new WDEditReal();

                        //Define nome do componente
                        $objWDComponente->strNome = "edtValorEvento";
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
                      
                      }
                    
                    ?>             
                  </td> 								
                </tr>
                <tr>
                  <td width="130" valign="top" class="dataLabel">Total de Desconto:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Acerta a variável com o valor a alterar
                      $valor_alterar = number_format($campos[valor_desconto_evento],2,',','.');
                      
                      //Caso tenha valor
                      if ($campos[valor_desconto_evento] > 0 && $usuarioNome != 'Zulaine')
                      {
                        
                        echo "$valor_alterar<br/>
                              <input type='hidden' id='edtValorDescontoEvento' name='edtValorDescontoEvento' value='$valor_alterar' />";
                        
                      }
                      
                      else
                      {
                        
                        //Cria um objeto do tipo WDEdit 
                        $objWDComponente = new WDEditReal();

                        //Define nome do componente
                        $objWDComponente->strNome = "edtValorDescontoEvento";
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
                      
                      }
                    
                    ?>						 
                  </td>
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Total <b>GERAL</b> Evento:</td>
                  <td colspan="5" valign="middle" class="tabDetailViewDF">
                    <?php
                    
                      //Acerta a variável com o valor a alterar
                      $valor_alterar = number_format($campos[valor_geral_evento],2,',','.');
                      
                      //Caso tenha valor
                      if ($campos[valor_geral_evento] > 0 && $usuarioNome != 'Zulaine')
                      {
                        
                        echo "$valor_alterar<br/>
                              <input type='hidden' id='edtValorGeralEvento' name='edtValorGeralEvento' value='$valor_alterar' />";
                        
                      }
                      
                      else
                      {
                      
                        //Cria um objeto do tipo WDEdit 
                        $objWDComponente = new WDEditReal();

                        //Define nome do componente
                        $objWDComponente->strNome = "edtValorGeralEvento";
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
                        
                      }
                      
                    ?>						 
                  </td>							
                </tr>
                <tr>
                  <td valign="top" class="dataLabel">Informações Complementares :</td>
                  <td colspan="5" class="tabDetailViewDF">
                    <textarea name="edtObservacoes" wrap="virtual" class="datafield" id="edtObservacoes" style="width: 100%; height: 300px"><?php echo $campos[observacoes] ?></textarea>
                  </td>
                </tr>
              </table>
              <br/>
              <span class="TituloModulo">Roteiro do Evento:</span>
              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
                <tr>
                  <td width="130" valign="top" class="dataLabel">Descrição do Roteiro:</td>
                  <td colspan="5" class="tabDetailViewDF">
                    <textarea name="edtRoteiro" wrap="virtual" class="datafield" id="edtRoteiro" style="width: 100%; height: 140px"><?php echo $campos[roteiro] ?></textarea>
                  </td>
                </tr>
              </table>
              <br/>
              <?php

                //verifica a exibição
                if ($dados_usuario["evento_financeiro"] == 1 || $usuarioNome == 'Zulaine')
                {

                  ?>
                  <span class="TituloModulo">Informações Financeiras:</span>
                  <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0"> 	
                    <tr>
                      <td valign="top" width="130" class="dataLabel">Posição Financeira:</td>
                      <td colspan="5" class="tabDetailViewDF">						   
                        <table width="500" cellpadding="0" cellspacing="0">
                          <tr valign="middle">
                            <td width="150" height="20">
                              <input name="edtPosicaoFinanceira" type="radio" value="1" <?php echo $financeiro_1 ?> />&nbsp;A Receber
                            </td>
                            <td width="150" height="20">
                              <input name="edtPosicaoFinanceira" type="radio" value="2" <?php echo $financeiro_2 ?> />&nbsp;Recebido
                            </td>
                            <td width="200" height="20">
                              <input name="edtPosicaoFinanceira" type="radio" value="3" <?php echo $financeiro_3 ?> />&nbsp;Cortesia
                            </td>
                          </tr>
                        </table>	
                      </td>
                    </tr>           
                    <tr>
                      <td valign="top" class="dataLabel">Número da NF:</td>
                      <td colspan="5" class="tabDetailViewDF">
                        <input name="edtNotaFiscal" type="text" class="datafield" id="edtNotaFiscal" style="width: 110px" maxlength="20" value="<?php echo $campos[numero_nf] ?>" />
                      </td>
                    </tr>
                    <tr>
                      <td width="130" valign="top" class="dataLabel">Obs. Financeiras:</td>
                      <td colspan="5" class="tabDetailViewDF">
                        <textarea name="edtObservacoesFinanceiro" wrap="virtual" class="datafield" id="edtObservacoesFinanceiro" style="width: 100%; height: 100px"><?php echo $campos[observacoes_financeiro] ?></textarea>
                      </td>
                    </tr>
                  </table>			
                  <?php

                }

              ?>
              <br/>
              <span class="TituloModulo">Informações de Foto e Vídeo:</span>
              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
                <tr>
                  <td valign="top" class="dataLabel">Foto e Vídeo Liberado?:</td>
                  <td colspan="5" class="tabDetailViewDF">
                    <input name="chkFotoVideoLiberado" type="checkbox" id="chkFotoVideoLiberado" value="1" <?php echo $foto_video_liberado_status ?> >&nbsp;SIM
                  </td>
                </tr>
                <tr>
                  <td width="130" valign="top" class="dataLabel">Valor da Foto:</td>
                  <td valign="middle" class="tabDetailViewDF">
                    <?php

                      //Acerta a variável com o valor a alterar
                      $valor_alterar = str_replace(".", ",", $campos[valor_foto]);

                      //Cria um objeto do tipo WDEdit 
                      $objWDComponente = new WDEditReal();

                      //Define nome do componente
                      $objWDComponente->strNome = "edtValorFoto";
                      //Define o tamanho do componente
                      $objWDComponente->intSize = 16;
                      //Busca valor definido no XML para o componente
                      $objWDComponente->strValor = $valor_alterar;
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
                    
                      //Acerta a variável com o valor a alterar
                      $valor_alterar = str_replace(".", ",", $campos[valor_dvd]);

                      //Cria um objeto do tipo WDEdit 
                      $objWDComponente = new WDEditReal();

                      //Define nome do componente
                      $objWDComponente->strNome = "edtValorDVD";
                      //Define o tamanho do componente
                      $objWDComponente->intSize = 16;
                      //Busca valor definido no XML para o componente
                      $objWDComponente->strValor = $valor_alterar;
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
                    <textarea name="edtObsFotoVideo" wrap="virtual" class="datafield" id="edtObsFotoVideo" style="width: 100%; height: 80px"><?php echo $campos["obs_fotovideo"] ?></textarea>
                  </td>
                </tr>
              </table>
              <br/>
              <span class="TituloModulo">Quebras de Produtos:</span>
              <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">                      
                <tr>
                  <td width="130" valign="top" class="dataLabel">Quebras:</td>
                  <td class="tabDetailViewDF">
                    <textarea name="edtQuebras" wrap="virtual" class="datafield" id="edtQuebras" style="width: 100%; height: 80px"><?php echo $campos["quebras"] ?></textarea>
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