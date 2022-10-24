<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleActivityChanged;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleAdded;
use Tulia\Cms\Website\Domain\WriteModel\Event\LocaleDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteActivityChanged;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteDeleted;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteUpdated;
use Tulia\Cms\Website\Domain\WriteModel\Exception;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotAddLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotDeleteLocaleException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotDeleteWebsiteException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotTurnOffWebsiteException;
use Tulia\Cms\Website\Domain\WriteModel\Exception\LocaleNotExistsException;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteLocale\CanDeleteLocaleInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteLocale\CanDeleteLocaleReasonEnum;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteWebsite\CanDeleteWebsiteInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanDeleteWebsite\CanDeleteWebsiteReasonEnum;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CannAddLocale\CanAddLocale;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CannAddLocale\CanAddLocaleInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CannAddLocale\CanAddLocaleReasonEnum;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite\CanTurnOffWebsiteInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite\CanTurnOffWebsiteReasonEnum;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\Website\SslModeEnum;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class Website extends AbstractAggregateRoot
{
    /** @var ArrayCollection<int, Locale> */
    private Collection $locales;
    private bool $changedInThisSession = false;

    private function __construct(
        private string $id,
        private string $name,
        private string $backendPrefix = '/administrator',
        private bool $active = true
    ) {
        $this->locales = new ArrayCollection();
    }

    public static function create(
        string $id,
        string $name,
        string $localeCode,
        string $domain,
        string $backendPrefix = '/administrator',
        ?string $domainDevelopment = null,
        ?string $pathPrefix = null,
        SslModeEnum $sslMode = SslModeEnum::ALLOWED_BOTH,
        bool $active = true,
    ): self {
        $self = new self($id, $name, $backendPrefix, $active);
        $self->locales->add(new Locale($self, $localeCode, $domain, $domainDevelopment, '', $pathPrefix, $sslMode, true, true));
        $self->recordThat(new WebsiteCreated($self->id));

        return $self;
    }

    public static function buildFromArray(array $data): self
    {
        $website = new self(
            $data['id'],
            $data['name'] ?? '',
            $data['backend_prefix'] ?? '/administrator'
        );
        $website->active = $data['active'] ?? true;

        foreach ($data['locales'] ?? [] as $locale) {
            $website->locales[] = new Locale(
                $website,
                $locale['code'] ?? 'en_US',
                $locale['domain'] ?? 'localhost',
                $locale['domain_development'] ?? 'localhost',
                $locale['locale_prefix'] ?? null,
                $locale['path_prefix'] ?? null,
                SslModeEnum::tryFrom($locale['ssl_mode'] ?? SslModeEnum::ALLOWED_BOTH),
                true,
                (bool) ($locale['is_default'] ?? false),
            );
        }

        return $website;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
            'backend_prefix' => $this->backendPrefix,
            'locales' => $this->localesToArray(),
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function turnOff(CanTurnOffWebsiteInterface $rules): void
    {
        if (CanTurnOffWebsiteReasonEnum::OK !== ($reason = $rules->decide($this->id))) {
            throw CannotTurnOffWebsiteException::fromReason($reason, $this->id);
        }

        $this->active = false;

        $this->recordWebsiteChange();
    }

    /**
     * @return Locale[]
     */
    public function getLocales(): array
    {
        return $this->locales->toArray();
    }

    public function addLocale(
        CanAddLocaleInterface $rules,
        string $code,
        string $domain = null,
        ?string $domainDevelopment = null,
        ?string $localePrefix = null,
        ?string $pathPrefix = null,
        SslModeEnum $sslMode = SslModeEnum::ALLOWED_BOTH,
        bool $active = true,
    ): void {
        $reason = $rules->decide($code, $this->collectLocaleCodes());

        if (CanAddLocaleReasonEnum::OK !== $reason) {
            throw CannotAddLocaleException::fromReason($reason, $code, $this->id);
        }

        $defaultLocale = $this->getDefaultLocale();

        $this->locales[] = new Locale(
            $this,
            $code,
            $domain ?? $defaultLocale->domain,
            $domainDevelopment ?? $defaultLocale->domainDevelopment,
            $localePrefix,
            $pathPrefix,
            $sslMode,
            $active,
        );

        $this->recordThat(new LocaleAdded($this->id, $code, $defaultLocale->code));
        $this->recordWebsiteChange();
    }

    public function deactivate(): void
    {
        if ($this->active) {
            $this->active = false;
            $this->recordThat(new WebsiteActivityChanged($this->id, $this->active));
            $this->recordWebsiteChange();
        }
    }

    public function activate(): void
    {
        if (false === $this->active) {
            $this->active = true;
            $this->recordThat(new WebsiteActivityChanged($this->id, $this->active));
            $this->recordWebsiteChange();
        }
    }

    public function delete(CanDeleteWebsiteInterface $rules): void
    {
        if (CanDeleteWebsiteReasonEnum::OK !== ($reason = $rules->decide($this->id))) {
            throw CannotDeleteWebsiteException::fromReason($reason, $this->id);
        }

        $this->recordThat(new WebsiteDeleted($this->id));
    }

    public function deleteLocale(
        CanDeleteLocaleInterface $rules,
        string $code,
    ): void {
        $defaultLocale = $this->getDefaultLocale();
        $reason = $rules->decide($code, $this->id, $this->collectLocaleCodes(), $defaultLocale->code);

        if (CanDeleteLocaleReasonEnum::OK !== $reason) {
            throw CannotDeleteLocaleException::fromReason($reason, $code, $this->id);
        }

        foreach ($this->locales as $locale) {
            if ($locale->isA($code)) {
                $this->locales->removeElement($locale);
                $this->recordThat(new LocaleDeleted($this->id, $code));
                $this->recordWebsiteChange();
            }
        }
    }

    public function activateLocale(string $code): void
    {
        foreach ($this->locales as $locale) {
            if ($locale->isA($code) && $locale->activate()) {
                $this->recordThat(new LocaleActivityChanged($this->id, $code, $locale->active));
                $this->recordWebsiteChange();
            }
        }
    }

    public function deactivateLocale(string $code): void
    {
        foreach ($this->locales as $locale) {
            if ($locale->isA($code) && $locale->deactivate()) {
                $this->recordThat(new LocaleActivityChanged($this->id, $code, $locale->active));
                $this->recordWebsiteChange();
            }
        }
    }

    public function collectDomainEvents(): array
    {
        $this->changedInThisSession = false;
        return parent::collectDomainEvents();
    }

    /**
     * @throws Exception\LocalePrefixInvalidException
     * @throws Exception\PathPrefixInvalidException
     */
    private function validateLocale(Locale $locale): void
    {
        if ($locale->getPathPrefix() !== null) {
            if (! preg_match('/^\/{1}[a-z0-9-_]+$/', $locale->getPathPrefix())) {
                throw new Exception\PathPrefixInvalidException('PathPrefix must contain slash at the beggining and only those signs after slash: [a-z, 0-9, -, _].');
            }
        }

        if ($locale->getLocalePrefix() !== null) {
            if (! preg_match('/^\/{1}[a-z0-9-_]+$/', $locale->getLocalePrefix())) {
                throw new Exception\LocalePrefixInvalidException('LocalePrefix must contain slash at the beggining and only those signs after slash: [a-z, 0-9, -, _].');
            }
        }
    }

    private function localesToArray(): array
    {
        return array_map(
            static fn($v) => $v->toArray(),
            $this->locales->toArray()
        );
    }

    private function recordWebsiteChange()
    {
        if ($this->changedInThisSession) {
            return;
        }

        $this->recordThat(new WebsiteUpdated($this->id));
        $this->changedInThisSession = true;
    }

    /**
     * @return string[]
     */
    private function collectLocaleCodes(): array
    {
        return array_map(
            static fn(Locale $v) => $v->code,
            $this->locales->toArray()
        );
    }

    private function getDefaultLocale(): Locale
    {
        foreach ($this->locales as $locale) {
            if ($locale->isDefault) {
                return $locale;
            }
        }

        throw new LocaleNotExistsException(sprintf('Default locale of Website %s does not exists, but it should.', $this->id));
    }
}
