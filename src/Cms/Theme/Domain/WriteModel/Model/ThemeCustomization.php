<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Domain\WriteModel\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AbstractAggregateRoot;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Theme\Domain\WriteModel\Exception\ChangesetDoesNotExists;
use Tulia\Cms\Theme\Domain\WriteModel\Service\DefaultThemeConfigurationProviderInterface;
use Tulia\Cms\Theme\Domain\WriteModel\Service\IdGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 * @final
 */
class ThemeCustomization extends AbstractAggregateRoot
{
    private const OLD_CHANGESETS = '-2 days';

    private string $id;
    /** @var ArrayCollection<int, Changeset> $changesets */
    private Collection $changesets;

    private function __construct(
        private readonly string $theme,
        private readonly string $websiteId,
    ) {
        $this->changesets = new ArrayCollection();
    }

    public static function create(string $theme, string $websiteId): self
    {
        return new self($theme, $websiteId);
    }

    public function modify(
        IdGeneratorInterface $idGenerator,
        DefaultThemeConfigurationProviderInterface $provider,
        array $availableLocales,
    ): string {
        $this->removeOldTemporaryChangesets();

        if ($this->isSomeActiveOne()) {
            return $this->modifyUsingActiveOne($idGenerator);
        }

        return $this->modifyFromScratch($idGenerator, $provider, $availableLocales);
    }

    public function updateChangeset(
        string $id,
        string $locale,
        DefaultThemeConfigurationProviderInterface $provider,
        array $payload,
    ): void {
        $changeset = $this->getChangeset($id);

        if (!$changeset) {
            throw new \DomainException(sprintf('Changeset %s of theme %s does not exists.', $id, $this->theme));
        }

        $changeset->updatePayload(
            $locale,
            $payload,
            $provider->provideMultilingualControls($this->theme)
        );
    }

    public function activateChangeset(string $id): void
    {
        $this->removeOldActiveChangesets();
        $this->getChangeset($id)->activate();
    }

    public function deleteTemporaryChangeset(string $changesetId): void
    {
        foreach ($this->changesets as $changeset) {
            if ($changeset->getId() === $changesetId && !$changeset->isActive()) {
                $changeset->detach();
                $this->changesets->removeElement($changeset);
            }
        }
    }

    public function reset(): void
    {
        $this->changesets->clear();
    }

    private function modifyUsingActiveOne(
        IdGeneratorInterface $idGenerator,
    ): string {
        $activeOne = $this->getActiveOne();
        $temporary = $this->copyChangesetToNewOne($idGenerator->getNextId(), $activeOne);

        $this->changesets->add($temporary);

        dump($activeOne, $temporary);

        return $temporary->getId();
    }

    private function modifyFromScratch(
        IdGeneratorInterface $idGenerator,
        DefaultThemeConfigurationProviderInterface $provider,
        array $availableLocales,
    ): string {
        $temporary = Changeset::create(
            $this,
            $idGenerator->getNextId(),
            $this->theme,
            $this->websiteId,
            $availableLocales,
            $provider->provideDefaultValues($this->theme),
            $provider->provideMultilingualControls($this->theme),
        );

        $this->changesets->add($temporary);

        return $temporary->getId();
    }

    private function isSomeActiveOne(): bool
    {
        foreach ($this->changesets as $changeset) {
            if ($changeset->isActive()) {
                return true;
            }
        }

        return false;
    }

    private function getChangeset(string $id): Changeset
    {
        foreach ($this->changesets as $changeset) {
            if ($changeset->getId() === $id) {
                return $changeset;
            }
        }

        throw ChangesetDoesNotExists::fromId($id, $this->theme);
    }

    private function getActiveOne(): Changeset
    {
        foreach ($this->changesets as $changeset) {
            if ($changeset->isActive()) {
                return $changeset;
            }
        }

        throw new \LogicException('Cannot find active changeset. Try to call isSomeActiveOne() method first.');
    }

    private function copyChangesetToNewOne(string $id, Changeset $activeOne): Changeset
    {
        return $activeOne->copy($id);
    }

    private function removeOldTemporaryChangesets(): void
    {
        foreach ($this->changesets as $changeset) {
            if (
                $changeset->isActive() === false
                && $changeset->isUpdatedBefore(ImmutableDateTime::now()->modify(self::OLD_CHANGESETS))
            ) {
                $changeset->detach();
                $this->changesets->removeElement($changeset);
            }
        }
    }

    private function removeOldActiveChangesets(): void
    {
        foreach ($this->changesets as $changeset) {
            if ($changeset->isActive()) {
                $changeset->detach();
                $this->changesets->removeElement($changeset);
            }
        }
    }
}
