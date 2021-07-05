<?php

declare(strict_types=1);

namespace Par\TimeTest\Fixtures;

use DateTimeImmutable;
use DateTimeInterface;
use Par\Time\Temporal\Temporal;
use Par\Time\Temporal\TemporalAdjuster;
use Par\Time\Temporal\TemporalAmount;
use Par\Time\Temporal\TemporalField;
use Par\Time\Temporal\TemporalUnit;

/**
 * @internal
 */
final class DecoratedTemporal implements Temporal
{
    public static ?Temporal $decorated = null;

    /**
     * @inheritDoc
     * @psalm-suppress PossiblyNullArgument
     */
    public static function fromNative(DateTimeInterface $dateTime): static
    {
        return new static(self::$decorated);
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function toNative(): DateTimeImmutable
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function minus(int $amountToSubtract, TemporalUnit $unit): static
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function minusAmount(TemporalAmount $amount): static
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function plus(int $amountToAdd, TemporalUnit $unit): static
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function plusAmount(TemporalAmount $amount): static
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function supportsUnit(TemporalUnit $unit): bool
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function with(TemporalAdjuster $adjuster): static
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function withField(TemporalField $field, int $newValue): static
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function supportsField(TemporalField $field): bool
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    /**
     * @inheritDoc
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function get(TemporalField $field): int
    {
        return call_user_func_array([static::$decorated, __FUNCTION__], func_get_args());
    }

    public function __construct(Temporal $decorated)
    {
        self::$decorated = $decorated;
    }
}