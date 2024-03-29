<?php

declare(strict_types=1);

namespace Tulia\Cms\Content\Block\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Content\Type\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\ContentTypeFormDescriptor;
use Tulia\Cms\Content\Type\Infrastructure\Framework\Form\Service\SymfonyFormBuilderCreator;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\IgnoreCsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class BlockPanel extends AbstractController
{
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private SymfonyFormBuilderCreator $formBuilder;

    public function __construct(
        ContentTypeRegistryInterface $contentTypeRegistry,
        SymfonyFormBuilderCreator $formBuilder
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->formBuilder = $formBuilder;
    }

    /**
     * @IgnoreCsrfToken()
     */
    public function index(string $type, Request $request): Response
    {
        $blockType = $this->contentTypeRegistry->get($type);

        // @todo Info when block type not exists.

        $data = (array) $request->request->get("content_builder_form_{$type}", []);

        $form = $this->formBuilder->createBuilder($blockType, $data, false);

        $validatedAndReadyToSave = false;
        $formDescriptor = new ContentTypeFormDescriptor($blockType, $form);
        $formDescriptor->handleRequest($request);

        if ($formDescriptor->isFormValid()) {
            $validatedAndReadyToSave = true;
        }

        $newData = [];

        // @todo Temporary transformation to multiple values.
        foreach ($data as $key => $value) {
            $newData[$key][] = $value;
        }

        return $this->render('@backend/content_block/block-panel/index.tpl', [
            'type' => $type,
            'formDescriptor' => $formDescriptor,
            'data' => $newData,
            'validatedAndReadyToSave' => $validatedAndReadyToSave,
        ]);
    }
}
