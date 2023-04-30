<?php

namespace Light2\Services;

class RendererService
{
    protected array $data;
    protected string $viewPath;
    protected string $file;

    public function setup(string $viewPath, string $file, array $data = []): void
    {
        $this->viewPath = $viewPath;
        $this->file = $file;
        $this->data = $data;
    }

    public function render(): void
    {
        extract($this->data);
        require_once $this->viewPath . $this->file . '.php';
    }
}