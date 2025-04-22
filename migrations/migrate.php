<?php
// Load Composer autoloader (adjust path if needed)
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
} else {
    die("Error: Composer dependencies not found. Run 'composer install' first.\n");
}

use Api\Migrations\MigrationManager;

// Only allow CLI execution unless explicitly enabled for web
if (php_sapi_name() !== 'cli') {
    die("This script is meant to be run from the command line.\n");
}

// CLI Argument Handling
$action = $argv[1] ?? 'run';
$validActions = ['run', 'migrate', 'rollback'];

if (!in_array($action, $validActions)) {
    echo "Usage: php migrate.php [run|rollback]\n";
    echo "  run/migrate (default): Run pending migrations\n";
    echo "  rollback: Revert the last batch of migrations\n";
    exit(1);
}

// Execute Migration
try {
    $manager = new MigrationManager();

    switch ($action) {
        case 'run':
        case 'migrate':
            echo "Running migrations...\n";
            $manager->runMigrations();
            break;
            
        case 'rollback':
            echo "Rolling back last migration batch...\n";
            $manager->rollback();
            break;
    }

    echo "Operation completed successfully.\n";
    exit(0);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}