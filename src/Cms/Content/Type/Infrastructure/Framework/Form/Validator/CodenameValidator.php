<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CodenameValidator
{
    protected const PATTERN = '/^[0-9a-z_]+$/';

    public function isCodenameValid(?string $code): bool
    {
        return !($code && !preg_match(self::PATTERN, $code));
    }

    public function validateNodeType(?string $code, ExecutionContextInterface $context): void
    {
        if ($code && ! preg_match(self::PATTERN, $code)) {
            $context->buildViolation('contentTypeCodeMustContainOnlyAlphanumsAndUnderline')
                ->setTranslationDomain('content_builder')
                ->addViolation();
        }
    }

    public function validateSectionCode(?string $code, ExecutionContextInterface $context): void
    {
        if ($code && ! preg_match(self::PATTERN, $code)) {
            $context->buildViolation('sectionCodeCodeMustContainOnlyAlphanumsAndUnderline')
                ->setTranslationDomain('content_builder')
                ->addViolation();
        }
    }

    public function validateFieldCode(?string $code, ExecutionContextInterface $context): void
    {
        if ($code && ! preg_match(self::PATTERN, $code)) {
            $context->buildViolation('fieldCodeMustContainOnlyAlphanumsAndUnderline')
                ->setTranslationDomain('content_builder')
                ->addViolation();
        }
    }
}
