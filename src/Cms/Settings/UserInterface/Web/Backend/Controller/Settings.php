<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\UserInterface\Web\Backend\Controller;

use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_Loggers_ArrayLogger;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Settings\Domain\Group\SettingsStorage;
use Tulia\Cms\Settings\Domain\Group\SettingsGroupRegistryInterface;
use Tulia\Cms\Settings\Domain\SettingsRepositoryInterface;
use Tulia\Cms\Shared\Infrastructure\Mail\MailerInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\WebsiteInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Settings extends AbstractController
{
    public function __construct(
        private readonly SettingsGroupRegistryInterface $settings,
        private readonly FormFactoryInterface $formFactory,
        private readonly SettingsRepositoryInterface $settingsRepository,
    ) {
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="settings_form")
     */
    public function show(Request $request, WebsiteInterface $website, ?string $group = null)
    {
        if (! $group) {
            return $this->redirectToRoute('backend.settings', ['group' => 'cms']);
        }

        if ($this->settings->hasGroup($group) === false) {
            throw $this->createNotFoundException($this->trans('settingsGroupNotFound', [], 'settings'));
        }

        $settings = $this->settingsRepository->get(
            $website->getId(),
            $website->getLocale()->getCode(),
            $website->getDefaultLocale()->getCode(),
        );
        $storage = new SettingsStorage($settings->export());

        $groupObj = $this->settings->getGroup($group);
        $groupObj->setFormFactory($this->formFactory);
        $form = $groupObj->buildForm($storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settings->update($groupObj->export($form));
            $this->settingsRepository->save($settings);

            $this->addFlash('success', $this->trans('settingsSaved', [], 'settings'));
            return $this->redirectToRoute('backend.settings', [ 'group' => $groupObj->getId() ]);
        }

        $view = $groupObj->buildView();
        $view['data']['form'] = $form->createView();

        return $this->view('@backend/settings/index.tpl', [
            'form'   => $view['data']['form'],
            'group'  => $groupObj,
            'groups' => $this->settings->getGroups(),
            'view'   => [
                'name' => $view['view'],
                'data' => $view['data'],
            ],
        ]);
    }

    /**
     * @CsrfToken(id="cms_settings_test_mail")
     */
    public function sendTestEmail(Request $request, MailerInterface $mailer): JsonResponse
    {
        if (filter_var($request->request->get('recipient'), FILTER_VALIDATE_EMAIL) === false) {
            return $this->responseJson([
                'message' => $this->trans('pleaseTypeValidEmailAddress', [], 'settings'),
                'status'  => 'error',
                'log'     => '',
            ]);
        }

        try {
            $message = $mailer->createMessage($this->trans('testMessageSubject', [], 'settings'));
            $message->setTo($request->request->get('recipient'));
            $message->setBody('<p>' . $this->trans('testMessageBody', [], 'settings') . '</p>', 'text/html');

            $logger = new Swift_Plugins_Loggers_ArrayLogger;
            $mailer->getMailer()->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

            $sent = $mailer->send($message);
            $log  = $logger->dump();

            if ($sent) {
                $message = $this->trans('testMessageSentSuccessfull', [], 'settings');
                $status  = 'success';
            } else {
                $message = $this->trans('testMessageNotSendCheckLog', [], 'settings');
                $status  = 'error';
            }
        } catch(\Exception $e) {
            $message = $this->trans('testMessageNotSendCheckLog', [], 'settings');
            $status  = 'error';
            $log     = 'Exception message: '.$e->getMessage();
        }

        return $this->responseJson([
            'message' => $message,
            'status'  => $status,
            'log'     => $log,
        ]);
    }
}
