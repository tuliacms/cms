<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Theme\Application\UseCase\ImportThemeCollection;
use Tulia\Cms\Theme\Application\UseCase\ImportThemeCollectionRequest;
use Tulia\Cms\Theme\Application\UseCase\InstallTheme;
use Tulia\Cms\Theme\Application\UseCase\InstallThemeRequest;
use Tulia\Cms\Theme\Application\UseCase\UninstallTheme;
use Tulia\Cms\Theme\Application\UseCase\UninstallThemeRequest;
use Tulia\Cms\Theme\Domain\ThemeImportCollectionRegistry;
use Tulia\Cms\Theme\UserInterface\Web\Backend\Form\ThemeInstallatorForm;
use Tulia\Component\Importer\ImporterInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class Installator extends AbstractController
{
    public function __construct(
        private readonly ManagerInterface $themesManager
    ) {
    }

    /**
     * @CsrfToken(id="theme_installator_form")
     */
    public function install(Request $request, InstallTheme $installTheme): RedirectResponse
    {
        $form = $this->createForm(ThemeInstallatorForm::class);
        $form->handleRequest($request);

        /** @var UploadedFile $data */
        $themeFile = $form->getData()['theme'];

        /** @var IdResult $result */
        $result = ($installTheme)(new InstallThemeRequest($themeFile->getRealPath()));

        $this->addFlash('success', $this->trans('themeHasBeenInstalled', [], 'themes'));

        return $this->redirectToRoute('backend.theme.installator.publish_assets', [
            'theme' => $result->id
        ]);
    }

    public function publishAssetsAfterInstallation(
        Request $request,
        AssetsPublisher $assetsPublisher,
        ThemeImportCollectionRegistry $registry
    ): RedirectResponse {
        $assetsPublisher->publishRegisteredAssets();

        if ($registry->hasAny($request->query->get('theme'))) {
            return $this->redirectToRoute('backend.theme.installator.importer', [
                'theme' => $request->query->get('theme')
            ]);
        }

        return $this->redirectToRoute('backend.theme');
    }

    /**
     * @return RedirectResponse|ViewInterface
     */
    public function importer(Request $request, ThemeImportCollectionRegistry $registry)
    {
        $theme = $request->query->get('theme');

        if (false === $registry->hasAny($theme)) {
            $this->addFlash('success', $this->trans('themeHasntGotAnyCollectionsToImport', [], 'themes'));
            return $this->redirectToRoute('backend.theme');
        }

        return $this->view('@backend/theme/installator/importer.tpl', [
            'imports' => $registry->getFor($theme),
            'theme' => $theme,
        ]);
    }

    /**
     * @CsrfToken(id="theme.importer.import")
     */
    public function import(Request $request, ImportThemeCollection $importThemeCollection): RedirectResponse
    {
        ($importThemeCollection)(new ImportThemeCollectionRequest(
            $request->request->get('theme'),
            $request->request->get('collection')
        ));

        $this->addFlash('success', $this->trans('collectionHasBeenImported', [], 'themes'));
        return $this->redirectToRoute('backend.theme.installator.importer', [
            'theme' => $request->request->get('theme'),
        ]);
    }

    /**
     * @CsrfToken(id="theme.uninstall")
     */
    public function uninstall(Request $request, UninstallTheme $uninstallTheme): RedirectResponse
    {
        ($uninstallTheme)(new UninstallThemeRequest($request->request->get('theme')));

        $this->addFlash('success', $this->trans('themeHasBeenUninstalled', [], 'themes'));
        return $this->redirectToRoute('backend.theme');
    }
}
