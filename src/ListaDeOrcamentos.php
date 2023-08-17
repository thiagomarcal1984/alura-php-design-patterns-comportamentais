<?php

namespace Alura\DesignPattern;

class ListaDeOrcamentos
{
    /** @var Orcamento[] */
    private array $orcamentos;

    public function __construct()
    {
        $this->orcamentos = [];
    }

    public function addOrcamento(Orcamento $orcamento)
    {
        $this->orcamentos[] = $orcamento;
    }

    public function orcamentos() : array
    {
        return $this->orcamentos;
    }
}
