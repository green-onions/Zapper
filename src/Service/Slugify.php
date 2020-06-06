<?php


namespace App\Service;


class Slugify
{
    public function generate(string $input): string
    {
        $slug = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $input);
        $slug = strtolower($slug);
        $slug = str_replace(' ', '-', $slug);
        $slug = preg_replace('/[^a-z\d-]+/i', '', $slug);
        $slug = str_replace('--', '-', $slug);
        $slug = trim($slug);
        $slug = rtrim($slug, "-");

        return $slug;
    }
}
