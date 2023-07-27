# Padrões comportamentais (Gang of Four)
- [ ] Chain of Responsibility
- [ ] Command
- [ ] Interpreter
- [ ] Iterator
- [ ] Mediator
- [ ] Memento
- [ ] Observer
- [ ] State
- [ ] Strategy
- [ ] Template Method
- [ ] Visitor

Fonte: https://pt.wikipedia.org/wiki/Padr%C3%A3o_de_projeto_de_software

# Strategy

## Iniciando o sistema
Para iniciar o projeto, simplesmente criamos o arquivo `composer.json` com as informações de autoload da PSR-4. Se houver dúvidas, use o comando `composer init` para criar o `composer.json` e depois adapte no que for necessário:

```php
// composer.json
{
    "autoload": {
        "psr-4": {
            "Alura\\DesignPatterns\\": "src/"
        }
    }
}
```
Depois de criar o arquivo `composer.json`, execute o comando `composer dump-autoload` para gerar os arquivos PHP da pasta `vendor`.

## Aplicando impostos
Os problemas: 
1. o método `calcula` na classe `CalculadoraDeImpostos` pode crescer muito se mais tipos de imposto forem criados.
2. O método `calcula` precisa funcionar, mesmo que seja fornecido um tipo de imposto inexistente.

```php
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

```
