<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteCreated;
use Tulia\Cms\Website\Domain\WriteModel\Event\WebsiteUpdated;
use Tulia\Cms\Website\Domain\WriteModel\Exception;
use Tulia\Cms\Website\Domain\WriteModel\Exception\CannotTurnOffWebsiteException;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite\CanTurnOffWebsiteInterface;
use Tulia\Cms\Website\Domain\WriteModel\Rules\CanTurnOffWebsite\CanTurnOffWebsiteReasonEnum;
use Tulia\Component\Routing\Enum\SslModeEnum;

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
        ?string $localePrefix = null,
        SslModeEnum $sslMode = SslModeEnum::ALLOWED_BOTH
    ): self {
        $self = new self($id, $name, $backendPrefix);
        $self->locales->add(new Locale($self, $localeCode, $domain, $domainDevelopment, $localePrefix, $pathPrefix, $sslMode, true));
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

        foreach ($data['locales'] ?? [] as $locale) {
            $website->addLocale(new Locale(
                $website,
                $locale['code'] ?? 'en_US',
                $locale['domain'] ?? 'localhost',
                $locale['domainDevelopment'] ?? 'localhost',
                $locale['localePrefix'] ?? null,
                $locale['pathPrefix'] ?? null,
                $locale['sslMode'] ?? null,
                $locale['isDefault'] ?? null,
            ));
        }

        return $website;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
            'locales' => $this->localesToArray(),
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function rename(string $name): void
    {
        $this->name = $name;

        $this->recordWebsiteChange();
    }

    public function turnOn(): void
    {
        $this->active = true;

        $this->recordWebsiteChange();
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

    public function replaceLocales(array $locales): void
    {
        $this->locales->clear();

        foreach ($locales as $locale) {
            $this->addLocale(new Locale(
                $this,
                $locale['code'],
                $locale['domain'],
                $locale['domain_development'],
                $locale['locale_prefix'],
                $locale['path_prefix'],
                $locale['ssl_mode'] instanceof SslModeEnum
                    ? $locale['ssl_mode']
                    : SslModeEnum::from($locale['ssl_mode']),
                (bool) $locale['is_default'],
            ));
        }
    }

    /**
     * @param Locale $locale
     * @throws Exception\LocalePrefixInvalidException
     * @throws Exception\PathPrefixInvalidException
     */
    public function addLocale(Locale $locale): void
    {
        $key = null;

        foreach ($this->locales as $duplicatedKey => $el) {
            if ($locale->getCode() === $el->getCode()) {
                $key = $duplicatedKey;
            }
        }

        $this->validateLocale($locale);

        if ($key !== null) {
            $this->locales[$key] = $locale;
        } else {
            $this->locales[] = $locale;
        }

        $this->recordWebsiteChange();
    }

    /**
     * @param Locale $locale
     *
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
}
