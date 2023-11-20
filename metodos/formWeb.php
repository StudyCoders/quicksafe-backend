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

    $rs = $con->fetchAll($sql, array($codigo));

    function concatTxt($tipo = "", $descricao = "")
    {
        return $tipo == "S" ? "Sim, " . $descricao : "Não.";
    }

    foreach ($rs as $key => $v) {
        $dataFormatada = date('d/m/Y', strtotime($v["DT_NASCIMENTO"]));
        $telefoneFormatado = !empty($v["TELEFONE"]) ? sprintf('(%s) %s-%s', substr($v["TELEFONE"], 0, 2), substr($v["TELEFONE"], 2, 4), substr($v["TELEFONE"], 6))
            : "-";
        $celularFormatado = sprintf('(%s) %s-%s', substr($v["CELULAR"], 0, 2), substr($v["CELULAR"], 2, 5), substr($v["CELULAR"], 7));
        $cepFormatado = substr($v["CEP"], 0, 5) . '-' . substr($v["CEP"], 5);

        $retorno[] = array(
            "id_formulario" => $v["ID_FORMULARIO"],
            "id_usuario"    => $v["ID_USUARIO"],
            "nome_usuario"  => $v["NOME_COMPLETO"],
            "email_usuario" => $v["EMAIL"],
            "cpf_usuario"   => maskCpf($v["CPF_USUARIO"]),
            "id_contato"    => $v["ID_CONTATO"],
            "nome_contato"  => $tipo == "C" ? $v["TIPO_CONTATO"] : "",
            "cpf_contato"   => $tipo == "C" ? maskCpf($v["CPF_CONTATO"]) : "",
            "dt_nascimento" => $dataFormatada,
            "tp_sexo"       => $v["TP_SEXO"] == "M" ? "Masculino" : "Feminino",
            "cep"           => $cepFormatado,
            "endereco"      => $v["ENDERECO"],
            "bairro"        => $v["BAIRRO"],
            "complemento"   => $v["COMPLEMENTO"] ? $v["COMPLEMENTO"] : "-",
            "nm_cidade"     => $v["NOME_CIDADE"],
            "telefone"      => $telefoneFormatado,
            "celular"       => $celularFormatado,
            "plano_saude"   => $v["ID_PLANO"] == 8 ? $v["DS_PLANO"] : $v["ID_PLANO"],
            "alergia"       => concatTxt($v["ALERGIA"], $v["DS_ALERGIA"]),
            "comorbidade"   => $v["ID_COMORBIDADE"] == 21 ? $v["DS_COMORBIDADE"] : $v["NOME_COMORBIDADE"],
            "med_continuo"  => concatTxt($v["MEDICAMENTO_CONTINUO"], $v["DS_MEDICAMENTO_CONTINUO"]),
            "cirurgia"      => concatTxt($v["CIRURGIA"], $v["DS_CIRURGIA"]),
        );
    }
}

die(json_encode($retorno));
