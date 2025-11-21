<?php
/**
 * Database Connection Handler
 * Establishes PDO connection with error handling
 */

try {
    // Build DSN
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    // PDO options for better security and performance
    $options = [
        // Error mode: throw exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        // Default fetch mode: associative array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // Disable emulated prepared statements for better security
        PDO::ATTR_EMULATE_PREPARES => false,
        
        // Persistent connection for better performance
        PDO::ATTR_PERSISTENT => false,
        
        // Enable MySQL buffered queries
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        
        // Set connection timeout
        PDO::ATTR_TIMEOUT => 5,
        
        // Convert numeric values to strings (safer)
        PDO::ATTR_STRINGIFY_FETCHES => false
    ];
    
    // Create PDO instance
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Set SQL mode for better compatibility
    $pdo->exec("SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    
    // Set timezone to match PHP timezone
    $pdo->exec("SET time_zone = '+07:00'");
    
    // Success message for development
    if (DEBUG_MODE) {
        error_log("Database connected successfully");
    }
    
} catch (PDOException $e) {
    // Log the error
    error_log("Database connection failed: " . $e->getMessage());
    error_log("Error Code: " . $e->getCode());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // User-friendly error message
    if (DEBUG_MODE) {
        $errorMessage = "<div style='padding:20px; background:#fee; border:2px solid #c33; border-radius:5px; margin:20px;'>";
        $errorMessage .= "<h2 style='color:#c33; margin:0 0 10px 0;'>Database Connection Error</h2>";
        $errorMessage .= "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        $errorMessage .= "<p><strong>Code:</strong> " . $e->getCode() . "</p>";
        $errorMessage .= "<p><strong>Host:</strong> " . DB_HOST . "</p>";
        $errorMessage .= "<p><strong>Database:</strong> " . DB_NAME . "</p>";
        $errorMessage .= "<p><strong>User:</strong> " . DB_USER . "</p>";
        $errorMessage .= "<hr>";
        $errorMessage .= "<h3>Troubleshooting:</h3>";
        $errorMessage .= "<ul>";
        $errorMessage .= "<li>Check if MySQL/MariaDB service is running</li>";
        $errorMessage .= "<li>Verify database credentials in config.php</li>";
        $errorMessage .= "<li>Ensure database '" . DB_NAME . "' exists</li>";
        $errorMessage .= "<li>Check user permissions</li>";
        $errorMessage .= "</ul>";
        $errorMessage .= "</div>";
        die($errorMessage);
    } else {
        // Production error page
        http_response_code(503);
        die("
        <!DOCTYPE html>
        <html>
        <head>
            <title>Service Unavailable</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
                .error-container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }
                .error-icon { font-size: 64px; color: #e74c3c; margin-bottom: 20px; }
                h1 { color: #333; margin: 0 0 10px 0; }
                p { color: #666; margin: 0 0 20px 0; }
                .retry-btn { background: #3498db; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
                .retry-btn:hover { background: #2980b9; }
            </style>
        </head>
        <body>
            <div class='error-container'>
                <div class='error-icon'>⚠️</div>
                <h1>Service Temporarily Unavailable</h1>
                <p>We're experiencing technical difficulties. Please try again in a few moments.</p>
                <a href='javascript:location.reload()' class='retry-btn'>Retry</a>
            </div>
        </body>
        </html>
        ");
    }
}

// ==================== HELPER FUNCTIONS ====================

/**
 * Execute a query and return results
 */
function dbQuery($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        error_log("SQL: " . $sql);
        throw $e;
    }
}

/**
 * Get single row
 */
function dbGetRow($sql, $params = []) {
    $stmt = dbQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Get all rows
 */
function dbGetAll($sql, $params = []) {
    $stmt = dbQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Get single value
 */
function dbGetValue($sql, $params = []) {
    $stmt = dbQuery($sql, $params);
    return $stmt->fetchColumn();
}

/**
 * Insert record and return last insert ID
 */
function dbInsert($table, $data) {
    global $pdo;
    
    $columns = array_keys($data);
    $placeholders = array_fill(0, count($columns), '?');
    
    $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") 
            VALUES (" . implode(', ', $placeholders) . ")";
    
    $stmt = dbQuery($sql, array_values($data));
    return $pdo->lastInsertId();
}

/**
 * Update record
 */
function dbUpdate($table, $data, $where, $whereParams = []) {
    $setPairs = [];
    foreach (array_keys($data) as $column) {
        $setPairs[] = "{$column} = ?";
    }
    
    $sql = "UPDATE {$table} SET " . implode(', ', $setPairs) . " WHERE {$where}";
    $params = array_merge(array_values($data), $whereParams);
    
    $stmt = dbQuery($sql, $params);
    return $stmt->rowCount();
}

/**
 * Delete record
 */
function dbDelete($table, $where, $whereParams = []) {
    $sql = "DELETE FROM {$table} WHERE {$where}";
    $stmt = dbQuery($sql, $whereParams);
    return $stmt->rowCount();
}

/**
 * Check if table exists
 */
function dbTableExists($tableName) {
    global $pdo;
    try {
        $result = $pdo->query("SHOW TABLES LIKE '{$tableName}'");
        return $result->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Transaction helpers
 */
function dbBeginTransaction() {
    global $pdo;
    return $pdo->beginTransaction();
}

function dbCommit() {
    global $pdo;
    return $pdo->commit();
}

function dbRollback() {
    global $pdo;
    return $pdo->rollBack();
}