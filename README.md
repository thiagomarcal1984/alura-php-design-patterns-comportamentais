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
## Aplicando mais de um desconto
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

        if ($orcamento->valor > 500) {
            return $orcamento->valor * 0.05;
        }

        return 0;
    }
}
```
Problemas encontrados:
1. O código acima não aplica os dois descontos caso o valor seja maior que 500 e a quantidade seja maior que 5.
2. A ordem para a aplicação dos descontos importa. Então, é complexo aplicar vários if/switch/strategy para estabelecer o desconto que será aplicado.

## Strategy resolve
Os percentuais de desconto poderiam ser delegados para uma implementação do padrão Strategy, mas a ordem e sequência dos descontos ainda precisa ser testada. Logo o padrão Strategy não soluciona o problema da calculadora de descontos.

## Criando a Chain of Responsibility
```php
// Descontos\Desconto.php
<?php

namespace Alura\DesignPattern\Descontos;

use Alura\DesignPattern\Orcamento;

abstract class Desconto
{
    protected ?Desconto $proximoDesconto;

    public function __construct(?Desconto $proximoDesconto)
    {
        $this->proximoDesconto = $proximoDesconto;
    }

    abstract function calculaDesconto(Orcamento $orcamento) : float;
}
```
> Note que a propriedade `$proximoDesconto` está protegida (para que as subclasses consigam enxergá-la). Note também que `$proximoDesconto` é nullable (veja as marcas de interrogação).
```php
// Descontos\DescontoMaisDe5Itens.php
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

```
```php
// Descontos\DescontoMaisDe500Reais.php
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

```
```php
// Descontos\SemDesconto.php
<?php

namespace Alura\DesignPattern\Descontos;

use Alura\DesignPattern\Orcamento;

class SemDesconto extends Desconto
{
    public function __construct(){
        parent::__construct(null);
    }

    public function calculaDesconto(Orcamento $orcamento) : float
    {
        return 0;
    }
}
```
> Note que a classe `SemDesconto` finaliza a cadeia, porque ela recebe nulo no seu construtor.
```php
// CalculadoraDeDescontos.php
<?php

namespace Alura\DesignPattern;

use Alura\DesignPattern\Descontos\DescontoMaisDe500Reais;
use Alura\DesignPattern\Descontos\DescontoMaisDe5Itens;
use Alura\DesignPattern\Descontos\SemDesconto;

class CalculadoraDeDescontos
{
    public function calculaDescontos(Orcamento $orcamento) : float
    {
        $desconto5itens = new DescontoMaisDe5Itens(
            new DescontoMaisDe500Reais(
                new SemDesconto()
            )
        );

        $desconto = $desconto5itens->calculaDesconto($orcamento);

        return $desconto;
    }
}
```
> Note que a `CalculadoraDeDescontos` determina qual é a ordem de aplicação do desconto (primeiro os objetos de desconto mais internos retornam valores, depois os descontos mais externos são aplicados).