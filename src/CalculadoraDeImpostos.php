<?php

namespace Alura\DesignPattern;

class CalculadoraDeImpostos
{
    public function calcula(Orcamento $orcamento, string $nomeImposto) : float
    {
        switch ($nomeImposto) {
            case 'ICMS':
                // Retorna 10% do valor do orçamento.
                return $orcamento->valor * 0.1;
                break;
            
            case 'ISS':
                return $orcamento->valor * 0.06;
                break;
        }
    }
}
