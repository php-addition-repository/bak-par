<?php

declare(strict_types=1);

namespace Par\CoreTest\Unit\PHPUnit\Constraint;

use Par\Core\Hashable;
use Par\Core\PHPUnit\Constraint\HashableEquals;
use Par\CoreTest\Fixtures\GenericHashable;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class HashableEqualsTest extends TestCase
{
    /**
     * @return array<string, array{GenericHashable, mixed, bool}>
     */
    public function provideDataForEvaluate(): array
    {
        $expectedHash = 'hash';
        $expected = new GenericHashable($expectedHash);
        $other = new GenericHashable('other');

        return [
            'same-hashable' => [$expected, $expected, true],
            'other-hashable' => [$expected, $other, false],
            'other-value' => [$expected, 'foo', false],
        ];
    }

    /**
     * @test
     * @dataProvider provideDataForEvaluate
     *
     * @param Hashable $object
     * @param mixed    $other
     * @param bool     $expectedResult
     */
    public function itCanEvaluateOther(Hashable $object, mixed $other, bool $expectedResult): void
    {
        $constraint = new HashableEquals($object);
        self::assertSame($expectedResult, $constraint->evaluate($other, '', true));
    }

    /**
     * @test
     */
    public function itCanCreateDescriptionForFailureWhenOtherIsHashable(): void
    {
        $expected = new GenericHashable('foo');
        $constraint = new HashableEquals($expected);

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage(
            'Failed asserting that Par\CoreTest\Fixtures\GenericHashable@bar equals Par\CoreTest\Fixtures\GenericHashable@foo.
--- Expected
+++ Actual
@@ @@
-Par\CoreTest\Fixtures\GenericHashable@foo
+Par\CoreTest\Fixtures\GenericHashable@bar'
        );
        $constraint->evaluate(new GenericHashable('bar'));
    }

    /**
     * @test
     */
    public function itCanCreateDescriptionForFailureWhenOtherIsNotHashable(): void
    {
        $expected = new GenericHashable('foo');
        $constraint = new HashableEquals($expected);

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage(
            'Failed asserting that \'foo\' equals Par\CoreTest\Fixtures\GenericHashable@foo.
--- Expected
+++ Actual
@@ @@
-Par\CoreTest\Fixtures\GenericHashable@foo
+\'foo\''
        );
        $constraint->evaluate('foo');
    }
}
