<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer\Changeset\Changeset;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Theme\Customizer\Builder\BuilderInterface;
use Tulia\Component\Theme\Customizer\Changeset\PredefinedChangesetRegistry;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface;
use Tulia\Component\Theme\Enum\ChangesetTypeEnum;
use Tulia\Component\Theme\Exception\ChangesetNotFoundException;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Customizer extends AbstractController
{
    protected ManagerInterface $themeManager;
    protected StorageInterface $customizerChangesetStorage;

    public function __construct(
        ManagerInterface $manager,
        StorageInterface $storage
    ) {
        $this->themeManager = $manager;
        $this->customizerChangesetStorage = $storage;
    }

    public function customizeRedirect(Request $request, WebsiteInterface $website): RedirectResponse
    {
        $theme = $this->themeManager->getTheme();

        if (! $theme) {
            return $this->redirectToRoute('backend.theme');
        }

        $changeset = $this->customizerChangesetStorage->getTemporaryCopyOfActiveChangeset($theme->getName(), $website->getLocale()->getCode());

        $parameters = [
            'theme'     => $theme->getName(),
            'changeset' => $changeset->getId(),
        ];

        if ($request->query->has('open')) {
            $parameters['open'] = $request->query->get('open');
        }
        if ($request->query->has('returnUrl')) {
            $parameters['returnUrl'] = $request->query->get('returnUrl');
        }

        return $this->redirectToRoute('backend.theme.customize', $parameters);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @throws ChangesetNotFoundException
     */
    public function customize(
        Request $request,
        BuilderInterface $builder,
        PredefinedChangesetRegistry $predefinedChangesetRegistry,
        string $theme,
        string $changeset = null
    ) {
        $storage = $this->themeManager->getStorage();

        if ($storage->has($theme) === false) {
            return $this->redirectToRoute('backend.theme');
        }

        if (! $changeset) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        $themeObject = $storage->get($theme);

        $changesetItem = $this->customizerChangesetStorage->has($changeset, $request->getLocale())
            ? $this->customizerChangesetStorage->get($changeset, $request->getLocale())
            : new Changeset($changeset);

        if ($changesetItem->getType() !== ChangesetTypeEnum::TEMPORARY) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        $parameters = [
            'mode'      => 'customizer',
            'changeset' => $changesetItem->getId(),
            '_locale'   => $request->getLocale(),
        ];

        if ($request->query->has('open')) {
            /** @var array $parsed */
            $parsed = parse_url($request->query->get('open'));
            $parsed['query'] = array_merge($parsed['query'] ?? [], $parameters);

            $previewUrl = $parsed['path'] . '?' . http_build_query($parsed['query']);
        } else {
            $previewUrl = $this->generateUrl('homepage', $parameters);
        }

        $customizerView = $builder->build($changesetItem, $themeObject);

        return $this->view('@backend/theme/customizer/customize.tpl', [
            'theme'      => $themeObject,
            'changeset'  => $changesetItem,
            'customizerView' => $customizerView,
            'previewUrl' => $previewUrl,
            'returnUrl'  => $request->query->get('returnUrl'),
            'predefinedChangesets' => $predefinedChangesetRegistry->get($themeObject),
        ]);
    }

    /**
     * @return JsonResponse|RedirectResponse
     * @throws ChangesetNotFoundException
     * @CsrfToken(id="theme.customizer.save")
     */
    public function save(Request $request, string $theme, string $changeset, WebsiteInterface $website)
    {
        $storage = $this->themeManager->getStorage();

        if ($storage->has($theme) === false) {
            return $this->redirectToRoute('backend.theme');
        }

        $themeObject = $storage->get($theme);

        $changesetEntity = $this->customizerChangesetStorage->has($changeset, $request->getLocale())
            ? $this->customizerChangesetStorage->get($changeset, $request->getLocale())
            : new Changeset($changeset);

        if ($changesetEntity->getType() !== ChangesetTypeEnum::TEMPORARY) {
            return $this->responseJson([
                'status' => 'error',
            ]);
        }

        $changesetEntity->setTheme($theme);
        $changesetEntity->setType(ChangesetTypeEnum::TEMPORARY);

        if ($changesetEntity->isEmpty()) {
            $themeChangeset = $this->customizerChangesetStorage->getActiveChangeset($themeObject->getName(), $website->getLocale()->getCode());

            if ($themeChangeset && $changesetEntity->getId() !== $themeChangeset->getId()) {
                $changesetEntity->merge($themeChangeset);
            }
        }

        $data = $request->request->all('data');

        if (\is_array($data) === false) {
            $data = [];
        }

        $changesetEntity->mergeArray($data);
        //changesetEntity->setAuthorId($this->getUser()->getId());

        if ($request->request->get('mode') === 'temporary') {
            $this->customizerChangesetStorage->save($changesetEntity, $request->getLocale(), $website->getDefaultLocale()->getCode(), $website->getLocaleCodes());
        }

        if ($request->request->get('mode') === 'theme') {
            $changesetEntity->setType(ChangesetTypeEnum::ACTIVE);
            $this->customizerChangesetStorage->save($changesetEntity, $request->getLocale(), $website->getDefaultLocale()->getCode(), $website->getLocaleCodes());
        }

        return $this->responseJson([
            'status' => 'success',
        ]);
    }

    /**
     * @param string $changeset
     *
     * @return RedirectResponse
     *
     * @throws ChangesetNotFoundException
     */
    public function left(Request $request, string $changeset): RedirectResponse
    {
        if ($this->customizerChangesetStorage->has($changeset, $request->getLocale())) {
            $changesetItem = $this->customizerChangesetStorage->get($changeset, $request->getLocale());

            if ($changesetItem->getType() !== ChangesetTypeEnum::TEMPORARY) {
                return $this->redirectToRoute('backend.theme');
            }

            $this->customizerChangesetStorage->remove($changesetItem);
        }

        if (empty($request->query->get('returnUrl')) === false) {
            return $this->redirectToUrl($request->getUriForPath($request->query->get('returnUrl')));
        }

        return $this->redirectToRoute('backend.theme');
    }

    /**
     * @param string $theme
     * @return RedirectResponse
     * @CsrfToken(id="theme.customizer.copy_changeset_from_parent")
     */
    public function copyChangesetFromParent(Request $request, string $theme, WebsiteInterface $website): RedirectResponse
    {
        $themeInstance = $this->themeManager->getStorage()->get($theme);

        if (! $themeInstance) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        if (! $themeInstance->getParent()) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        $parent  = $this->themeManager->getStorage()->get($themeInstance->getParent());

        $changeset = $this->customizerChangesetStorage->getActiveChangeset($parent->getName(), $website->getLocale()->getCode());

        if (!$changeset) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        $themeChangeset = $this->customizerChangesetStorage->getActiveChangeset($parent->getName(), $website->getLocale()->getCode());

        if ($themeChangeset && $changeset->getId() !== $themeChangeset->getId()) {
            $changeset->merge($themeChangeset);
        }

        $changeset->setTheme($themeInstance->getName());
        $this->customizerChangesetStorage->save($changeset, $request->getLocale(), $website->getDefaultLocale()->getCode(), $website->getLocaleCodes());

        return $this->redirectToRoute('backend.theme.customize.current');
    }

    /**
     * @param string $theme
     * @return RedirectResponse
     * @CsrfToken(id="theme.customizer.reset")
     */
    public function reset(string $theme, WebsiteInterface $website): RedirectResponse
    {
        $themeInstance = $this->themeManager->getStorage()->get($theme);

        if (! $themeInstance) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        $changeset = $this->customizerChangesetStorage->getActiveChangeset($themeInstance->getName(), $website->getLocale()->getCode());

        if ($changeset) {
            $this->customizerChangesetStorage->remove($changeset);
        }

        return $this->redirectToRoute('backend.theme.customize.current');
    }
}
