<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\DefaultTheme\DefaultTheme;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Theme\Application\Exception\ThemeNotFoundException;
use Tulia\Cms\Theme\Application\Service\ThemeActivator;
use Tulia\Cms\Theme\UserInterface\Web\Backend\Form\ThemeInstallatorForm;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Theme extends AbstractController
{
    public function __construct(
        private readonly ManagerInterface $manager,
        private readonly ThemeActivator $themeActivator
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
}
