<?php

declare(strict_types=1);

namespace Par\TimeTest\Unit\LocalDate;

use Par\Time\LocalDate;
use PHPUnit\Framework\TestCase;

final class ObjectTest extends TestCase
{

    /**
     * @dataProvider provideForEquality
     *
     *
     * @param LocalDate $localDate
     * @param bool      $isEqual
     * @param mixed     $other
     *
     * @return void
     */
    public function testItCanDetermineEquality(LocalDate $localDate, bool $isEqual, mixed $other): void
    {
        self::assertSame($isEqual, $localDate->equals($other));
    }

    /**
     * @return array<string, array{LocalDate, bool, mixed}>
     */
    public function provideForEquality(): array
    {
        $year = 2000;
        $month = 1;
        $dayOfMonth = 1;
        $source = LocalDate::of($year, $month, $dayOfMonth);
        return [
            'equals-self' => [$source, true, $source],
            'equals-same' => [$source, true, LocalDate::of($year, $month, $dayOfMonth)],
            'diff-year' => [$source, false, LocalDate::of($year + 1, $month, $dayOfMonth)],
            'diff-month' => [$source, false, LocalDate::of($year, $month + 1, $dayOfMonth)],
            'diff-dayOfMonth' => [$source, false, LocalDate::of($year, $month, $dayOfMonth + 1)],
        ];
    }

    public function testHashing(): void
    {
        $source = LocalDate::of(2010, 1, 1);

        self::assertSame(20100101, $source->hash());
    }

    public function testItCanBeNaturallySorted(): void
    {
        $list = [
            LocalDate::of(2001, 3, 12),
            LocalDate::of(2010, 1, 1),
            LocalDate::of(2001, 3, 1),
            LocalDate::of(2001, 2, 1),
        ];

        uasort(
            $list,
            static function (LocalDate $a, LocalDate $b): int {
                return $a->compareTo($b);
            }
        );

        $orderedList = [];
        foreach ($list as $item) {
            $orderedList[] = $item->toString();
        }

        self::assertSame(
            ['2001-02-01', '2001-03-01', '2001-03-12', '2010-01-01'],
            $orderedList
        );
    }
}