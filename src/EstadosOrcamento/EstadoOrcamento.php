<?php

namespace Alura\DesignPattern\EstadosOrcamento;

use Alura\DesignPattern\Orcamento;

abstract class EstadoOrcamento
{
    /**
     * @throws \DomainException
     */
    abstract public function calculaDescontoExtra(Orcamento $orcamento) : float;

    /**
     * @throws \DomainException
     */
    public function aprova(Orcamento $orcamento) 
    {
        throw new \DomainException('Este orçamento não pode ser aprovado.');
    }
    /**
     * @throws \DomainException
     */
    public function reprova(Orcamento $orcamento) 
    {
        throw new \DomainException('Este orçamento não pode ser reprovado.');
    }
    /**
     * @throws \DomainException
     */
    public function finaliza(Orcamento $orcamento) 
    {
        throw new \DomainException('Este orçamento não pode ser finalizado.');
    }
}
