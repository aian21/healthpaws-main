<?php
// HealthPaws - Version Configuration
// Update this version number whenever you make CSS/JS changes to force cache refresh

define('ASSETS_VERSION', '1.0');

// Helper function to append version to asset URLs
function asset_url($path) {
    return $path . '?v=' . ASSETS_VERSION;
}
?>
