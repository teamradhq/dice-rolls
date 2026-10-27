<?php

declare(strict_types=1);

namespace Teamradhq\DiceRoll\Tests\Fixture;

use Mockery\MockInterface;
use Throwable;

final class Mockery extends \Mockery
{
    /**
     * @throws TestSetupException
     */
    public static function mock(...$args): MockInterface
    {
        try {
            return parent::mock(...$args);
        } catch (Throwable $throwable) {
            throw new TestSetupException(
                sprintf('Mockery error: %s', $throwable->getMessage()),
                (int) $throwable->getCode(),
                previous: $throwable
            );
        }
    }
}
