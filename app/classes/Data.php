<?php

namespace App\classes;

class Data
{

    public function Data()
    {

        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");
        $data = [
            'mes' => $mes,
            'ano' => $ano,
            'dia' => $dia
        ];

        return $data;
    }

    public function Meses()
    {
        $meses = ([
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
        ]);
        return $meses;
    }
}
