<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Breadcrumbs;

/**
 * @author Adam Banaszkiewicz
 */
class Breadcrumbs implements BreadcrumbsInterface
{
    private array $breadcrumbs = [];
    private ?string $classlist = null;

    public function __toString(): string
    {
        return $this->render();
    }

    public function all(): array
    {
        return $this->breadcrumbs;
    }

    public function count(): int
    {
        return count($this->breadcrumbs);
    }

    public function push($href, $label): void
    {
        $this->breadcrumbs[] = [
            'href'  => (string) $href,
            'label' => (string) $label
        ];
    }

    public function pop(): array
    {
        return array_pop($this->breadcrumbs);
    }

    public function replace(array $crumbs): void
    {
        $this->breadcrumbs = [];

        foreach ($crumbs as $crumb) {
            if (isset($crumb['label'], $crumb['href'])) {
                $this->push($crumb['label'], $crumb['href']);
            }
        }
    }

    public function unshift($href, $label): void
    {
        array_unshift($this->breadcrumbs, [
            'href'  => (string) $href,
            'label' => (string) $label
        ]);
    }

    public function shift(): array
    {
        return array_shift($this->breadcrumbs);
    }

    public function render(): string
    {
        $result = '<ol class="breadcrumb '.$this->classlist.'">';
        $total  = count($this->breadcrumbs);

        foreach ($this->breadcrumbs as $key => $crumb) {
            $result .= '<li class="breadcrumb-item '.($total === ($key + 1) ? 'active' : '').'">
                <a href="'.htmlspecialchars($crumb['href']).'" title="'.htmlspecialchars($crumb['label'], ENT_QUOTES).'">
                    '.htmlspecialchars($crumb['label'], ENT_QUOTES).'
                </a>
            </li>';
        }

        return $result.'</ol>';
    }

    public function setClasslist(?string $classlist = null): void
    {
        $this->classlist = $classlist;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->breadcrumbs);
    }
}
