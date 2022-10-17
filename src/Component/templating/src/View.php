<?php

declare(strict_types=1);

namespace Tulia\Component\Templating;

/**
 * @author Adam Banaszkiewicz
 */
class View implements ViewInterface
{
    protected $views = [];
    protected $data  = [];

    /**
     * @param       $views
     * @param array $data
     */
    public function __construct($views, array $data = [])
    {
        if (\is_array($views) === false) {
            $views = [ $views ];
        }

        $this->views = $views;
        $this->data  = $data;
    }

    public function getViews(): array
    {
        return $this->views;
    }

    public function setViews(array $views): void
    {
        $this->views = $views;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }
}
