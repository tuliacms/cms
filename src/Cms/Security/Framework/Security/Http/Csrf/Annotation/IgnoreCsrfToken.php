<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 * @author Adam Banaszkiewicz
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class IgnoreCsrfToken
{
}
