<?php

namespace Light2\Services;

class RendererService
{
    protected array $data;
    protected string $extension;
    protected string $file;

    public function setup(string $file, array $data = [], string $extension = '.php'): void
    {
        $this->file = $file;
        $this->data = $data;
        $this->extension = $extension;
    }

    public function render(): void
    {
        extract($this->data);
        require_once APPPATH . '/views/' . $this->file . $this->extension;
    }
}