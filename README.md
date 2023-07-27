# Padrões comportamentais (Gang of Four)
- [ ] Chain of Responsibility
- [ ] Command
- [ ] Interpreter
- [ ] Iterator
- [ ] Mediator
- [ ] Memento
- [ ] Observer
- [ ] State
- [x] Strategy
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
## Removendo switch-case com Strategy
A solução é criar uma interface que é implementada por cada tipo de imposto:

```php
// Impostos\Imposto.php
<?php

namespace Alura\DesignPattern\Impostos;

use Alura\DesignPattern\Orcamento;

interface Imposto
{
    public function calcula(Orcamento $orcamento) : float;
}
```
```php
// Impostos\Icms.php
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
```
```php
// Impostos\Iss.php
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
```
```php
// CalculadoraDeImpostos.php
<?php

namespace Alura\DesignPattern;

use Alura\DesignPattern\Impostos\Imposto;

class CalculadoraDeImpostos
{
    public function calcula(Orcamento $orcamento, Imposto $imposto) : float
    {
        return $imposto->calcula($orcamento);
    }
}
```
> Perceba que o método `calcula` ficou muito mais enxuto.

O padrão Strategy usa 3 tipos de classe: 
1. O Contexto (que usa uma interface de Estratégia);
2. A interface de Estratégia; e
3. A(s) implementação(ões) concreta(s) da interface.

O Contexto não precisa conhecer os vários tipos de implementações possíveis, nem testar com `switch` ou `if` qual o comportamento a ser usado. O Contexto usa a interface de Estratégia, que é implementada por classes concretas.

Leitura complementar sobre o padrão Strategy: https://refactoring.guru/design-patterns/strategy 

# Chain of Responsibility

## Criando a calculadora de descontos
```php
<?php

namespace Alura\DesignPattern;

class CalculadoraDeDescontos
{
    public function calculaDescontos(Orcamento $orcamento) : float
    {
        if ($orcamento->quantidadeItens > 5) {
            return $orcamento->valor * 0.1;
        }

        return 0;
    }
}
```