<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator;

/**
 * @author Adam Banaszkiewicz
 */
interface PasswordValidatorInterface
{
    public const OK = 0;
    public const ERR_MIN_LENGTH        = 101;
    public const ERR_MIN_DIGITS        = 102;
    public const ERR_MIN_SPECIALS      = 103;
    public const ERR_MIN_BIG_LETTERS   = 104;
    public const ERR_MIN_SMALL_LETTERS = 105;

    public function validate(string $password): int;

    public function getMinLength(): int;

    public function setMinLength(int $minLength): void;

    public function getMinDigits(): int;

    public function setMinDigits(int $minDigits): void;

    public function getMinSpecialChars(): int;

    public function setMinSpecialChars(int $minSpecialChars): void;

    public function getMinBigLetters(): int;

    public function setMinBigLetters(int $minBigLetters): void;

    public function getMinSmallLetters(): int;

    public function setMinSmallLetters(int $minSmallLetters): void;
}
