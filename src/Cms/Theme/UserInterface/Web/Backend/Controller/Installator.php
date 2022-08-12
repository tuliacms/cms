<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Theme\Application\UseCase\InstallTheme;
use Tulia\Cms\Theme\Application\UseCase\InstallThemeRequest;
use Tulia\Cms\Theme\Application\UseCase\UninstallTheme;
use Tulia\Cms\Theme\Application\UseCase\UninstallThemeRequest;
use Tulia\Cms\Theme\UserInterface\Web\Backend\Form\ThemeInstallatorForm;

/**
 * @author Adam Banaszkiewicz
 */
final class Installator extends AbstractController
{
    /**
     * @CsrfToken(id="theme_installator_form")
     */
    public function install(Request $request, InstallTheme $installTheme): RedirectResponse
    {
        $form = $this->createForm(ThemeInstallatorForm::class);
        $form->handleRequest($request);

        /** @var UploadedFile $data */
        $themeFile = $form->getData()['theme'];

        ($installTheme)(new InstallThemeRequest($themeFile->getRealPath()));

        $this->addFlash('success', $this->trans('themeHasBeenInstalled', [], 'themes'));
        return $this->redirectToRoute('backend.theme');
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
