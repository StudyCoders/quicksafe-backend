<?php
include_once("../includes/conexao.php");

$retorno = file_get_contents('php://input');
$dados = json_decode($retorno);
$acao = $_GET['acao'];
$retorno = array();

if ($acao == 'select') {
    $campo = $dados->campo;
    $contato = $dados->contato;
    $numero = is_numeric($campo);
    $verifica = "";

    if ($contato) {
        if ($numero) {
            $verifica = "(
                C.ID_CONTATO LIKE '%" . $campo . "%'
             OR F.TELEFONE LIKE '%" . $campo . "%'
             OR C.CPF LIKE '%" . $campo . "%'
             OR F.CELULAR LIKE '%" . $campo . "%'
            )";
        } else {
            $campo = str_replace(' ', '%', strtoupper($campo));
            $verifica = "C.TIPO_CONTATO LIKE '%" . $campo . "%'";
        }

        $sql = "SELECT
                    C.*,
                    F.CELULAR
                FROM CONTATOS C
                INNER JOIN FORMULARIO F ON
                    C.ID_CONTATO = F.ID_CONTATO
                WHERE
                    " . $verifica . "
                    AND F.ID_CONTATO IS NOT NULL";
    } else {
        if ($numero) {
            $verifica = "(
                U.ID_USUARIO LIKE '%" . $campo . "%'
             OR F.TELEFONE LIKE '%" . $campo . "%'
             OR U.CPF LIKE '%" . $campo . "%'
             OR F.CELULAR LIKE '%" . $campo . "%'
            )";
        } else {
            $campo = str_replace(' ', '%', strtoupper($campo));
            $verifica = "upper(U.NOME_COMPLETO) LIKE '%" . $campo . "%'";
        }

        $sql = "SELECT
                    U.*,
                    F.CELULAR
                FROM USUARIO U
                INNER JOIN FORMULARIO F ON
                    U.ID_USUARIO = F.ID_USUARIO
                WHERE
                    " . $verifica . "
                    AND F.ID_CONTATO IS NULL";
    }
    $rs = $con->fetchAll($sql);

    foreach ($rs as $key => $value) {
        $retorno[] = array(
            "tipo"   => $contato ? "C" : "U",
            "codigo" => $contato ? $value['ID_CONTATO'] : $value['ID_USUARIO'],
            "nome"   => $contato ? $value['TIPO_CONTATO'] : $value['NOME_COMPLETO'],
            "cpf"    => $value['CPF']
        );
    }
} else if ($acao == "formulario") {
    $tipo = $dados->tipo;
    $codigo = $dados->codigo;

    function concatTxt($tipo = "", $descricao = "")
    {
        return $tipo == "S" ? "Sim, " . $descricao : "Não.";
    }

    if ($tipo == "C") {
        $sql = "SELECT
                    F.*,
                    CID.NOME_CIDADE,
                    E.NOME_ESTADO,
                    COM.NOME_COMORBIDADE,
                    PL.NOME_PLANO,
                    CON.TIPO_CONTATO,
                    CON.CPF AS CPF_CONTATO,
                    U.NOME_COMPLETO,
                    U.EMAIL,
                    U.CPF AS CPF_USUARIO
                FROM FORMULARIO F
                INNER JOIN CIDADE CID ON
                    F.ID_CIDADE = CID.ID_CIDADE
                INNER JOIN ESTADO E ON
                    CID.ID_ESTADO = E.ID_ESTADO
                INNER JOIN COMORBIDADE COM ON
                    F.ID_COMORBIDADE = COM.ID_COMORBIDADE
                INNER JOIN PLANO_SAUDE PL ON
                    F.ID_PLANO = PL.ID_PLANO
                INNER JOIN CONTATOS CON ON
                    F.ID_CONTATO = CON.ID_CONTATO
                INNER JOIN USUARIO U ON
                    F.ID_USUARIO = U.ID_USUARIO AND U.ATIVO = 'S'
                WHERE F.ID_CONTATO = ?
                  AND F.ID_CONTATO IS NOT NULL";
    } else {
        $sql = "SELECT
                    F.*,
                    CID.NOME_CIDADE,
                    E.NOME_ESTADO,
                    COM.NOME_COMORBIDADE,
                    PL.NOME_PLANO,
                    U.NOME_COMPLETO,
                    U.EMAIL,
                    U.CPF AS CPF_USUARIO
                FROM FORMULARIO F
                INNER JOIN CIDADE CID ON
                    F.ID_CIDADE = CID.ID_CIDADE
                INNER JOIN ESTADO E ON
                    CID.ID_ESTADO = E.ID_ESTADO
                INNER JOIN COMORBIDADE COM ON
                    F.ID_COMORBIDADE = COM.ID_COMORBIDADE
                INNER JOIN PLANO_SAUDE PL ON
                    F.ID_PLANO = PL.ID_PLANO
                INNER JOIN USUARIO U ON
                    F.ID_USUARIO = U.ID_USUARIO AND U.ATIVO = 'S'
                WHERE F.ID_USUARIO = ?
                  AND F.ID_CONTATO IS NULL";
    }

    $rs = $con->fetch($sql, array($codigo));

    $sql_localidade = "SELECT DATE_FORMAT(DTHR_LOCALIDADE, '%d/%m/%Y %H:%i:%S') AS DTHR_LOCALIDADE,
                              LATITUDE,
                              LONGITUDE
                        FROM LOCALIDADE
                           WHERE ID_USUARIO = ?
                        ORDER BY DTHR_LOCALIDADE DESC LIMIT 1";
    $rs_localidade = $con->fetch($sql_localidade, array($rs['ID_USUARIO']));

    $dataFormatada = date('d/m/Y', strtotime($rs["DT_NASCIMENTO"]));
    $telefoneFormatado = !empty($rs["TELEFONE"]) ? sprintf('(%s) %s-%s', substr($rs["TELEFONE"], 0, 2), substr($rs["TELEFONE"], 2, 4), substr($rs["TELEFONE"], 6))
        : "-";
    $celularFormatado = sprintf('(%s) %s-%s', substr($rs["CELULAR"], 0, 2), substr($rs["CELULAR"], 2, 5), substr($rs["CELULAR"], 7));
    $cepFormatado = substr($rs["CEP"], 0, 5) . '-' . substr($rs["CEP"], 5);

    $retorno = array(
        "id_formulario" => $rs["ID_FORMULARIO"],
        "id_usuario"    => $rs["ID_USUARIO"],
        "nome_usuario"  => $rs["NOME_COMPLETO"],
        "email_usuario" => $rs["EMAIL"],
        "cpf_usuario"   => maskCpf($rs["CPF_USUARIO"]),
        "id_contato"    => $rs["ID_CONTATO"],
        "nome_contato"  => $tipo == "C" ? $rs["TIPO_CONTATO"] : "",
        "cpf_contato"   => $tipo == "C" ? maskCpf($rs["CPF_CONTATO"]) : "",
        "dt_nascimento" => $dataFormatada,
        "tp_sexo"       => $rs["TP_SEXO"] == "M" ? "Masculino" : "Feminino",
        "cep"           => $cepFormatado,
        "endereco"      => $rs["ENDERECO"],
        "bairro"        => $rs["BAIRRO"],
        "complemento"   => $rs["COMPLEMENTO"] ? $rs["COMPLEMENTO"] : "-",
        "estado"        => $rs["NOME_ESTADO"],
        "cidade"        => $rs["NOME_CIDADE"],
        "telefone"      => $telefoneFormatado,
        "celular"       => $celularFormatado,
        "plano_saude"   => $rs["ID_PLANO"] == 8 ? $rs["DS_PLANO"] : $rs["ID_PLANO"],
        "alergia"       => concatTxt($rs["ALERGIA"], $rs["DS_ALERGIA"]),
        "comorbidade"   => $rs["ID_COMORBIDADE"] == 21 ? $rs["DS_COMORBIDADE"] : $rs["NOME_COMORBIDADE"],
        "med_continuo"  => concatTxt($rs["MEDICAMENTO_CONTINUO"], $rs["DS_MEDICAMENTO_CONTINUO"]),
        "cirurgia"      => concatTxt($rs["CIRURGIA"], $rs["DS_CIRURGIA"]),
        "latitude"      => $rs_localidade['LATITUDE'],
        "longitude"     => $rs_localidade['LONGITUDE'],
        "dthr_local"    => $rs_localidade['DTHR_LOCALIDADE']
    );
}

die(json_encode($retorno));
