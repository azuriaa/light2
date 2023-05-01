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
        if (file_exists($this->viewPath . $this->file . '.php')) {
            extract($this->data);
            require_once $this->viewPath . $this->file . '.php';
        } elseif (file_exists($this->viewPath . $this->file . '.html')) {
            $view = file_get_contents($this->viewPath . $this->file . '.html');
            $keys = array_keys($this->data);
            foreach ($keys as $key) {
                $view = str_replace("{{ $key }}", htmlspecialchars($this->data[$key]), $view);
            }
            echo $view;
        } else {
            throw new \Exception("File $this->file.php or $this->file.html does not exist.");
        }
    }
}