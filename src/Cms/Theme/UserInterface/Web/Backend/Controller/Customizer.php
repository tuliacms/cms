<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Options\Domain\ReadModel\OptionsFinderInterface;
use Tulia\Cms\Platform\Infrastructure\DefaultTheme\DefaultTheme;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Settings\Domain\SettingsRepositoryInterface;
use Tulia\Cms\Shared\Application\UseCase\IdResult;
use Tulia\Cms\Theme\Application\UseCase\LeftTemporaryChangeset;
use Tulia\Cms\Theme\Application\UseCase\LeftTemporaryChangesetRequest;
use Tulia\Cms\Theme\Application\UseCase\NewCustomization;
use Tulia\Cms\Theme\Application\UseCase\NewCustomizationRequest;
use Tulia\Cms\Theme\Application\UseCase\ResetThemeCustomization;
use Tulia\Cms\Theme\Application\UseCase\ResetThemeCustomizationRequest;
use Tulia\Cms\Theme\Application\UseCase\SaveChangeset;
use Tulia\Cms\Theme\Application\UseCase\SaveChangesetRequest;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Theme\Customizer\Builder\BuilderInterface;
use Tulia\Component\Theme\Customizer\Changeset\PredefinedChangesetRegistry;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface;
use Tulia\Component\Theme\Exception\ChangesetNotFoundException;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Customizer extends AbstractController
{
    public function __construct(
        private readonly ManagerInterface $themeManager,
        private readonly StorageInterface $customizerChangesetStorage,
    ) {
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @throws ChangesetNotFoundException
     */
    public function customize(
        Request $request,
        BuilderInterface $builder,
        PredefinedChangesetRegistry $predefinedChangesetRegistry,
        WebsiteInterface $website,
        NewCustomization $newCustomization,
        OptionsFinderInterface $options,
    ) {
        $theme = $this->themeManager->getTheme();

        if (! $theme) {
            return $this->redirectToRoute('backend.theme');
        }

        if ($theme instanceof DefaultTheme) {
            $this->addFlash('warning', $this->trans('defaultThemeCannotBeCustomized', [], 'themes'));
            return $this->redirectToRoute('backend.theme');
        }

        /** @var IdResult $result */
        $result = $newCustomization(new NewCustomizationRequest(
            $theme->getName(),
            $website->getId(),
            $website->getLocaleCodes(),
        ));

        $changeset = $this->customizerChangesetStorage->get($result->id, $website->getId(), $website->getLocale()->getCode());
        $customizerView = $builder->build($changeset, $theme);

        $parameters = [
            'mode'      => 'customizer',
            '_locale'   => $website->getLocale()->getCode(),
        ];

        if ($request->query->has('open')) {
            /** @var array $parsed */
            $parsed = parse_url($request->query->get('open'));
            $parsed['query'] = array_merge($parsed['query'] ?? [], $parameters);

            $previewUrl = $parsed['path'] . '?' . http_build_query($parsed['query']);
        } else {
            $previewUrl = $this->generateUrl('frontend.homepage', $parameters);
        }

        return $this->view('@backend/theme/customizer/customize.tpl', [
            'theme'      => $theme,
            'changeset'  => $changeset,
            'customizerView' => $customizerView,
            'previewUrl' => $previewUrl,
            'returnUrl'  => $request->query->get('returnUrl'),
            'predefinedChangesets' => $predefinedChangesetRegistry->get($theme),
            'settings' => [
                'website_favicon' => $options->findByName('website_favicon', $website->getId(), $website->getLocale()->getCode()),
            ],
        ]);
    }

    /**
     * @return JsonResponse|RedirectResponse
     * @CsrfToken(id="theme.customizer.save")
     */
    public function save(
        Request $request,
        string $theme,
        WebsiteInterface $website,
        SaveChangeset $saveChangeset,
        NewCustomization $newCustomization,
        SettingsRepositoryInterface $settingsRepository,
    ) {
        if ($this->themeManager->getStorage()->has($theme) === false) {
            return $this->redirectToRoute('backend.theme');
        }

        $data = $request->request->all('data');

        $settings = $settingsRepository->get(
            $website->getId(),
            $website->getLocale()->getCode(),
            $website->getDefaultLocale()->getCode(),
        );
        $settings->update(['website_favicon' => $data['settings.website_favicon']]);
        $settingsRepository->save($settings);

        // Remove settings from changeset data
        unset($data['settings.website_favicon']);

        $isActiveChangesetSaving = $request->request->get('mode') === 'theme';

        $saveChangeset(new SaveChangesetRequest(
            $theme,
            $website->getId(),
            $website->getLocale()->getCode(),
            $request->request->get('changeset'),
            $data,
            $isActiveChangesetSaving,
        ));

        if ($isActiveChangesetSaving) {
            /** @var IdResult $result */
            $result = $newCustomization(
                new NewCustomizationRequest(
                    $theme,
                    $website->getId(),
                    $website->getLocaleCodes(),
                )
            );

            return $this->responseJson(['status' => 'success', 'changeset' => $result->id, 'message' => $this->trans('changsetPublished', [], 'customizer')]);
        }

        return $this->responseJson(['status' => 'success', 'message' => $this->trans('changsetPublished', [], 'customizer')]);
    }

    /**
     * @throws ChangesetNotFoundException
     */
    public function left(
        Request $request,
        WebsiteInterface $website,
        LeftTemporaryChangeset $leftChangeset,
        string $changeset,
        string $theme,
    ): RedirectResponse {
        $leftChangeset(new LeftTemporaryChangesetRequest($theme, $website->getId(), $changeset));

        if (empty($request->query->get('returnUrl')) === false) {
            return $this->redirect($request->getUriForPath($request->query->get('returnUrl')));
        }

        return $this->redirectToRoute('backend.theme');
    }

    /**
     * @param string $theme
     * @return RedirectResponse
     * @CsrfToken(id="theme.customizer.copy_changeset_from_parent")
     */
    public function copyChangesetFromParent(string $theme, WebsiteInterface $website): RedirectResponse
    {
        $themeInstance = $this->themeManager->getStorage()->get($theme);

        if (! $themeInstance) {
            return $this->redirectToRoute('backend.theme.customize');
        }

        if (! $themeInstance->hasParent()) {
            return $this->redirectToRoute('backend.theme.customize');
        }

        $parent  = $themeInstance->getParent();

        $changeset = $this->customizerChangesetStorage->getActiveChangeset($parent->getName(), $website->getId(), $website->getLocale()->getCode());

        if (!$changeset) {
            return $this->redirectToRoute('backend.theme.customize');
        }

        $themeChangeset = $this->customizerChangesetStorage->getActiveChangeset($parent->getName(), $website->getId(), $website->getLocale()->getCode());

        if ($themeChangeset && $changeset->getId() !== $themeChangeset->getId()) {
            $changeset->merge($themeChangeset);
        }

        $changeset->setTheme($themeInstance->getName());
        $this->customizerChangesetStorage->save($changeset, $website->getId(), $website->getLocale()->getCode(), $website->getDefaultLocale()->getCode(), $website->getLocaleCodes());

        return $this->redirectToRoute('backend.theme.customize');
    }

    /**
     * @CsrfToken(id="theme.customizer.reset")
     */
    public function reset(
        string $theme,
        WebsiteInterface $website,
        ResetThemeCustomization $resetThemeCustomization,
    ): RedirectResponse {
        if ($this->themeManager->getStorage()->has($theme) === false) {
            return $this->redirectToRoute('backend.theme');
        }

        $resetThemeCustomization(new ResetThemeCustomizationRequest($theme, $website->getId()));

        return $this->redirectToRoute('backend.theme.customize');
    }
}
