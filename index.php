<?php
    
    /*
     *
     * Precisamos retornar um array listando os indicadores com 3 campos variáveis:
     * 1) Instituição ou Nome da capacitação
     * 2) Latitude
     * 3) Longitude
     *
     * No dump temos algumas tabelas:
     * "indicadores" => lista de indicadores, esses indicadores tem campos variáveis
     * "indicadores_secoes_itens" => lista dos campos variáveis dos indicadores
     * "indicadores_respostas" => valores cadastrados referentes aos campos variados dos indicadores
     * "cidades" => Lista de cidades com coordenadas geográficas
     *
     * Resultado deve ser um array como abaixo:
     * array (
     *      ['Capacitação TESTE',	'4.60314',	'-60.1815'],
     *      ['Instituição TESTE',	'-27.5861254',	'-48.5209025']
     * );
     * 
     * Observação: Hoje a tabela "indicadores" tem 40.000 registros
     * e "indicadores_respostas" 650.000 registros
     * 
     */

     function retorno_maps() {

        // Instituicao:
        // - Nome: secao id 40
        // - Lat: secao id 94
        // - Long: secao id 95

        // Capacitacao:
        // - Nome: secoes id 62
        // - Lat e Long: secao id 67 <- busca na tabela cidades pelo id da cidade

        $conn = new PDO("mysql:host=localhost;port=3306;dbname=duo-teste", "leonardo", "root");

        $array = array();

        array_push($array, BuscaCapacitacoes($conn));
        array_push($array, buscaInstituicoes($conn));

        return $array;
     }

     function buscaInstituicoes($conn)
     {
        $array = array();

        $query = " SELECT ir.resposta_text AS Instituicao, (SELECT ir.resposta_text
                                            FROM indicadores_respostas ir
                                            WHERE ir.id_secao_item = 94) AS latitude,
                                            (SELECT ir.resposta_text
                                            FROM indicadores_respostas ir
                                            WHERE ir.id_secao_item = 95) AS longitude
                    FROM indicadores_respostas ir
                    WHERE ir.id_secao_item = 40 ";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);
            array_push($array, $row["Instituicao"], $row["latitude"], $row["longitude"]);
        }

        return $array;
     }

     function BuscaCapacitacoes($conn)
     {
        $array = array();

        $query = " SELECT c.latitude, c.longitude, (SELECT ir.resposta_text
                                                    FROM indicadores_respostas ir
                                                    WHERE ir.id_secao_item = 62) AS Capacitacao
                    FROM cidades c 
                    INNER JOIN indicadores_respostas
                    WHERE indicadores_respostas.id_secao_item = 67 AND
                    c.cidades_id = indicadores_respostas.resposta_text  ";

        $stmt = $conn->prepare($query);

        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            extract($row);
            array_push($array, $row["Capacitacao"], $row["latitude"], $row["longitude"]);
        }

        return $array;
     }

     print_r( retorno_maps() );