<?php

namespace Alura\DesignPattern;

use Alura\DesignPattern\AcoesAoGerarPedido\AcaoAposGerarPedido;
use SplObserver;

class GerarPedidoHandler implements \SplSubject
{
    /** @var \SplObserver[] $acoesAposGerarPedido */
    private array $acoesAposGerarPedido = [];

    public Pedido $pedido;

    public function __construct(/* PedidoRepository, MailService */)
    {
        // Repare que os parâmetros contém os objetos injetados por DI.
    }

    public function attach(SplObserver $observer): void
    {
        $this->acoesAposGerarPedido[] = $observer;
    }
    
    public function detach(SplObserver $observer): void
    {
        // Remover um observer da lista de observers.
    }

    public function notify(): void
    {
        foreach($this->acoesAposGerarPedido as $acao) {
            $acao->update($this);
        }
    }
    
    public function execute(GerarPedido $gerarPedido)
    {
        $orcamento = new Orcamento();
        $orcamento->quantidadeItens = $gerarPedido->getNumeroItens();
        $orcamento->valor = $gerarPedido->getValorOrcamento();
        
        $this->pedido = new Pedido();
        $this->pedido->nomeCliente = "Teste";
        $this->notify();
    }
}
