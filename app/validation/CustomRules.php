<?php

namespace App\Validation;

class CustomRules
{
    public function is_linkedin_url(string $url): bool
    {
        if (empty($url)) {
            return true; 
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return str_ends_with(strtolower($host), 'linkedin.com');
    }

    public function is_instagram_url(string $url): bool
    {
        if (empty($url)) {
            return true; 
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return str_ends_with(strtolower($host), 'instagram.com');
    }
}