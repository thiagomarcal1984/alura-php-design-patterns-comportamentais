<?php

namespace Alura\DesignPattern\Descontos;

use Alura\DesignPattern\Orcamento;

class DescontoMaisDe500Reais extends Desconto
{
    public function calculaDesconto(Orcamento $orcamento) : float
    {
        $desconto = 0;
        if ($orcamento->valor > 500) {
            $desconto = $orcamento->valor * 0.05;
        }
        return $desconto + $this->proximoDesconto->calculaDesconto($orcamento);
    }
}
