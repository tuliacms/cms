<?php

declare(strict_types=1);

namespace Tulia\Cms\ImportExport\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Importer\ExporterInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ExportController extends AbstractController
{
    public function __construct(
        private readonly ExporterInterface $exporter,
    ) {
    }

    public function homepage(): ViewInterface
    {
        return $this->view('@backend/import_export/exporter.tpl', [
            'collection' => $this->exporter->collectObjects(),
        ]);
    }

    /**
     * @CsrfToken(id="import-export-export-file")
     */
    public function exportFile(Request $request): Response
    {
        $objects = $request->request->all('object');

        if (!is_array($objects) || empty($objects)) {
            return $this->redirect('backend.import_export.exporter');
        }

        $filepath = $this->exporter->export($objects);

        return $this->file($filepath);
    }
}
