<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\CancelType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\SubmitType;

/**
 * @author Adam Banaszkiewicz
 */
final class ThemeInstallatorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('theme', FileType::class, [
            'label' => 'themePackage',
            'help' => 'installThemeShortInfo',
            'translation_domain' => 'themes',
            'constraints' => [
                new NotBlank(),
                new File(
                    mimeTypes: ['application/zip', 'application/octet-stream', 'application/x-zip-compressed', 'multipart/x-zip'],
                ),
            ],
        ]);
        $builder->add('install', SubmitType::class, [
            'label' => 'install',
            'translation_domain' => 'themes',
        ]);
        $builder->add('cancel', CancelType::class);
    }
}
