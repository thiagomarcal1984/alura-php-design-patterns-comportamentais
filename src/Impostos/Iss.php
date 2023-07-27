<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Orcamento;

class Iss implements Imposto
{
    public function calcula(Orcamento $orcamento) : float
    {
        // Retorna 6% do valor do orçamento.
        return $orcamento->valor * 0.06;
    }
}
