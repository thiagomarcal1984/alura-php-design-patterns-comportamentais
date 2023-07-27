<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Orcamento;

class Icms implements Imposto
{
    public function calcula(Orcamento $orcamento) : float
    {
        // Retorna 10% do valor do orçamento.
        return $orcamento->valor * 0.1;
    }
}
