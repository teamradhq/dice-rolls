# Dice Rolls

A simple package for rolling dice and making roll tables.

## Quick Start

Roll some dice:

```php
use Teamradhq\DiceRoll\Dice;

$d6 = Dice::make();
$d20 = Dice::make(20);
```


Roll the dice:

```php
$d6->roll()->result();          // 3
$d6->advantage()->result();     // 6
$d6->disadvantage()->result();  // 1

$d20->roll()->result();          // 10
$d20->advantage()->result();     // 20
$d20->disadvantage()->result();  // 1

$rolls = $d20->times(3);
foreach ($rolls as $roll) {
    $roll->result(); // 1 - 20
}
```

Create a roll table:

```php
use Teamradhq\DiceRoll\RollTable;
use Teamradhq\DiceRoll\PrimitiveType;

$table = new RollTable(6, PrimitiveType::String);
```

Add some items:

```php
$table->add('One');
$table->add('Two', 2);
$table->add('Three', 3);
```

Roll for an item:

```php
$table->disadvantage(); // 'One'
$table->roll();         // 'Two'
$table->advantage();    // 'Three'
```

Get a specific item:

```php
$table->get(1)          // 'One';
$table->get(3)          // 'Two';
$table->get(6)          // 'Three';
```

List all items:

```php
$table->list();         // ['One', 'Two', 'Three']
```