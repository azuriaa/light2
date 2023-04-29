<?php

namespace Light2\Services;

class RendererService
{
    protected array $data;
    protected string $extension;
    protected string $file;

    public function setParams(string $file, array $data = [], string $extension = '.php')
    {
        $this->file = $file;
        $this->data = $data;
        $this->extension = $extension;
    }

    public function render()
    {
        if (file_exists(APPPATH . '/views/' . $this->file . $this->extension)) {
            extract($this->data);
            require_once APPPATH . '/views/' . $this->file . $this->extension;
        } else {
            throw new \ErrorException($this->file . $this->extension . ' not found');
        }
    }
}