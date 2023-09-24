<?php
include_once("../includes/auth.php");

$acao = $_GET["acao"];
$retorno = array();

if($acao == "select"){
    $queries = array(
        "cidade" => "SELECT C.ID_CIDADE AS value, C.NOME_CIDADE AS label FROM CIDADE C ORDER BY ID_CIDADE",
        "comorbidade" => "SELECT C.ID_COMORBIDADE AS value, C.NOME_COMORBIDADE AS label FROM COMORBIDADE C ORDER BY ID_COMORBIDADE",
        "planoSaude" => "SELECT P.ID_PLANO AS value, P.NOME_PLANO AS label FROM PLANO_SAUDE P ORDER BY ID_PLANO"
    );

    foreach ($queries as $key => $query) {
        $rs = $con->fetchAll($query);
        $retorno[$key] = $rs;
    }

} else if($acao == "formulario"){
    $dados = json_decode(file_get_contents('php://input'));

    $id_contato = !empty($dados->id_contato) ? $dados->id_contato : "";

    $id_usuario = $decoded_array['id_usuario'];
    $verifca_contato = !empty($id_contato) ? " AND ID_CONTATO = ?" : "";

    $sql = "SELECT * FROM FORMULARIO WHERE ID_USUARIO = ?" . $verifca_contato;
    $values = array($id_usuario);

    if(!empty($id_contato))
        array_push($values, $id_contato);

    $rs = $con->fetch($sql, $values);

    $dataFormatada = date('d/m/Y', strtotime($rs["DT_NASCIMENTO"]));
    $telefoneFormatado = !empty($rs["TELEFONE"]) ? sprintf('(%s) %s-%s', substr($rs["TELEFONE"], 0, 2), substr($rs["TELEFONE"], 2, 4), substr($rs["TELEFONE"], 6))
                            : "";
    $celularFormatado = sprintf('(%s) %s-%s', substr($rs["CELULAR"], 0, 2), substr($rs["CELULAR"], 2, 5), substr($rs["CELULAR"], 7));
    $cepFormatado = substr($rs["CEP"], 0, 5) . '-' . substr($rs["CEP"], 5);

    $retorno = array(
        "id_formulario" => $rs["ID_FORMULARIO"],
        "id_usuario" => $rs["ID_USUARIO"],
        "id_contato" => $rs["ID_CONTATO"],
        "dt_nascimento" => $dataFormatada,
        "tp_sexo" => $rs["TP_SEXO"],
        "cep" => $cepFormatado,
        "endereco" => $rs["ENDERECO"],
        "bairro" => $rs["BAIRRO"],
        "complemento" => $rs["COMPLEMENTO"],
        "id_cidade" => $rs["ID_CIDADE"],
        "telefone" => $telefoneFormatado,
        "celular" => $celularFormatado,
        "id_plano" => $rs["ID_PLANO"],
        "ds_plano" => $rs["DS_PLANO"],
        "alergia" => $rs["ALERGIA"],
        "ds_alergia" => $rs["DS_ALERGIA"],
        "id_comorbidade" => $rs["ID_COMORBIDADE"],
        "ds_comorbidade" => $rs["DS_COMORBIDADE"],
        "medicamento_continuo" => $rs["MEDICAMENTO_CONTINUO"],
        "ds_medicamento_continuo" => $rs["DS_MEDICAMENTO_CONTINUO"],
        "cirurgia" => $rs["CIRURGIA"],
        "ds_cirurgia" => $rs["DS_CIRURGIA"]
    );
} else if($acao == "contatos"){
    $id_usuario = $decoded_array['id_usuario'];

    $sql = "SELECT * FROM CONTATOS WHERE ID_USUARIO = ?";
    $retorno = $con->fetchAll($sql, array($id_usuario));
}

die(json_encode($retorno));
?>
