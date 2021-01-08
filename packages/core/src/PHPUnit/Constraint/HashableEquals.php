<?php

declare(strict_types=1);

namespace Par\Core\PHPUnit\Constraint;

use Par\Core\Hashable;
use Par\Core\Values;
use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class HashableEquals extends Constraint
{

    private Hashable $object;

    public function __construct(Hashable $object)
    {
        $this->object = $object;
    }

    /**
     * @inheritDoc
     */
    protected function matches($other): bool
    {
        return $this->object->equals($other);
    }

    /**
     * @inheritDoc
     */
    protected function failureDescription($other): string
    {
        if ($other instanceof Hashable) {
            $otherExport = Values::toString($other);
        } else {
            $otherExport = $this->exporter()->export($other);
        }

        return sprintf('%s %s', $otherExport, $this->toString());
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return sprintf('equals %s', Values::toString($this->object));
    }

    /**
     * @inheritDoc
     */
    protected function additionalFailureDescription($other): string
    {
        if ($other instanceof Hashable) {
            $to = Values::toString($other);
        } else {
            $to = $this->exporter()->export($other);
        }

        $outputBuilder = new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n");

        return (new Differ($outputBuilder))->diff(Values::toString($this->object), $to);
    }
}
