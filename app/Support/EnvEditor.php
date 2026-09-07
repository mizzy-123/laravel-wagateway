<?php

namespace App\Support;

class EnvEditor
{
    /**
     * @param  array<string, string|int|null>  $values
     */
    public function update(array $values): void
    {
        $path = base_path('.env');

        if (! is_file($path) || ! is_writable($path)) {
            throw new \RuntimeException('File .env tidak dapat ditulis.');
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new \RuntimeException('Gagal membaca file .env.');
        }

        foreach ($values as $key => $value) {
            $content = $this->set($content, (string) $key, $value);
        }

        if (file_put_contents($path, $content) === false) {
            throw new \RuntimeException('Gagal menyimpan file .env.');
        }
    }

    private function set(string $content, string $key, string|int|null $value): string
    {
        $normalized = $this->normalizeValue($value);
        $pattern = "/^{$key}=.*$/m";
        $line = "{$key}={$normalized}";

        if (preg_match($pattern, $content) === 1) {
            return (string) preg_replace($pattern, $line, $content, 1);
        }

        return rtrim($content).PHP_EOL.$line.PHP_EOL;
    }

    private function normalizeValue(string|int|null $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = (string) $value;

        if ($value === '') {
            return '';
        }

        if (preg_match('/\s|#|"|\'/', $value) === 1) {
            return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
        }

        return $value;
    }
}
