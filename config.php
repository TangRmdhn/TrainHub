<?php

/**
 * TrainHub Configuration
 * 
 * Automatically detects environment and sets the correct base path
 * - Production (trainhub.web.id): BASE_PATH = ''
 * - Development (localhost): BASE_PATH = '/trainhub'
 */

// Detect environment based on hostname
$hostname = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isProduction = ($hostname === 'trainhub.web.id' || $hostname === 'www.trainhub.web.id');

// Set base path
if ($isProduction) {
    define('BASE_PATH', '');
} else {
    define('BASE_PATH', '/trainhub');
}

// API URL Configuration
if ($isProduction) {
    // Production API (Hugging Face Space)
    define('API_URL', 'https://indraprhmbd-trainhub-ai.hf.space/generate-plan');
} else {
    // Local Development API
    define('API_URL', 'http://127.0.0.1:8000/generate-plan');
}

/**
 * Helper function to generate URLs with correct base path
 * 
 * @param string $path The path (e.g., '/app', '/login')
 * @return string Full path with base
 */
function url($path)
{
    // Ensure path starts with /
    if (substr($path, 0, 1) !== '/') {
        $path = '/' . $path;
    }
    return BASE_PATH . $path;
}

/**
 * Helper function for asset URLs (CSS, JS, images)
 * 
 * @param string $path The asset path
 * @return string Full asset path
 */
function asset($path)
{
    if (substr($path, 0, 1) !== '/') {
        $path = '/' . $path;
    }
    return BASE_PATH . $path;
}
