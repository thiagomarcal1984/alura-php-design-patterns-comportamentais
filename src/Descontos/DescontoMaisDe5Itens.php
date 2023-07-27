<?php

namespace Alura\DesignPattern\Descontos;

use Alura\DesignPattern\Orcamento;

class DescontoMaisDe5Itens extends Desconto
{
    public function calculaDesconto(Orcamento $orcamento) : float
    {
        $desconto = 0;
        if ($orcamento->quantidadeItens > 5) {
            $desconto = $orcamento->valor * 0.1;
        }
        return $desconto + $this->proximoDesconto->calculaDesconto($orcamento);
    }
}
