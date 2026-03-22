<?php
// =====================================================
// STEVEN GAMES — DATABASE CONFIGURATION FILE
// db_connect.php
// =====================================================
// This is the required separate config file for the
// data-driven milestone. It safely delegates to the
// existing includes/db.php so constants are never
// defined twice (fixes the "already defined" warning).
// =====================================================

// Load the core DB file only once
require_once __DIR__ . '/includes/db.php';

/**
 * Alias so milestone code can call connectDB()
 * while existing code keeps using getDB().
 * Both return the same mysqli connection.
 */
if (!function_exists('connectDB')) {
    function connectDB(): mysqli {
        return getDB();
    }
}
?>
