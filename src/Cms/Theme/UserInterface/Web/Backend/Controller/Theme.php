<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Platform\Infrastructure\DefaultTheme\DefaultTheme;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Theme\Application\Exception\ThemeNotFoundException;
use Tulia\Cms\Theme\Application\Service\ThemeActivator;
use Tulia\Cms\Theme\UserInterface\Web\Backend\Form\ThemeInstallatorForm;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Theme\Configuration\ConfigurationRegistry;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Theme extends AbstractController
{
    public function __construct(
        private readonly ManagerInterface $manager,
        private readonly ThemeActivator $themeActivator,
        private readonly StorageInterface $storage,
        private readonly ConfigurationRegistry $configurationRegistry,
    ) {
    }

    public function index(): ViewInterface
    {
        $themes = $this->manager->getThemes();
        $theme  = $this->manager->getTheme();

        if (\in_array($theme, $themes, true) === true) {
            unset($themes[$theme->getName()]);
            $themes = array_merge([ $theme ], $themes);
        }

        $form = $this->createForm(ThemeInstallatorForm::class, [], [
            'action' => $this->generateUrl('backend.theme.installator.install')
        ]);

        return $this->view('@backend/theme/theme/index.tpl', [
            'themes' => $themes,
            'theme'  => $theme,
            'usesDefaultTheme' => $theme instanceof DefaultTheme,
            'installatorForm' => $form->createView(),
            'development' => $this->getParameter('kernel.environment') === 'dev',
            'configRegistry' => $this->configurationRegistry,
        ]);
    }

    /**
     * @CsrfToken(id="theme.activate")
     */
    public function activate(Request $request, WebsiteInterface $website): RedirectResponse
    {
        $this->denyIfNotDevelopmentEnvironment();

        try {
            $this->themeActivator->activateTheme($request->request->get('theme'), $website->getId());
        } catch (ThemeNotFoundException $e) {
            return $this->redirectToRoute('backend.theme');
        }

        $this->setFlash('success', $this->trans('themeActivated', [], 'themes'));
        return $this->redirectToRoute('backend.theme');
    }

    public function preview(string $theme, string $page): Response
    {
        $themeObject = $this->storage->get($theme);
        $pageFilepath = $themeObject->getPreviewDirectory().'/'.$page;

        if (!is_file($pageFilepath)) {
            throw new NotFoundHttpException();
        }

        $headers = [];

        $headers['Content-Type'] = match (pathinfo($page, PATHINFO_EXTENSION)) {
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'text/plain'
        };

        return new Response(file_get_contents($pageFilepath), Response::HTTP_OK, $headers);
    }

    public function internalImage(string $theme, string $filepath): Response
    {
        $themeObject = $this->storage->get($theme);
        $imageFilepath = $themeObject->getDirectory().'/'.$filepath;

        if (!is_file($imageFilepath)) {
            throw new NotFoundHttpException();
        }

        return new Response(file_get_contents($imageFilepath), Response::HTTP_OK, [
            'Content-Type' => 'image/jpeg',
        ]);
    }
}
