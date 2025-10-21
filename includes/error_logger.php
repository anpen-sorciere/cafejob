<?php
// 独自エラーログシステム
function custom_error_log($message, $context = []) {
    $log_file = __DIR__ . '/logs/custom_error.log';
    $log_dir = dirname($log_file);
    
    // ログディレクトリが存在しない場合は作成
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $context_str = !empty($context) ? ' | Context: ' . json_encode($context) : '';
    $log_entry = "[$timestamp] $message$context_str" . PHP_EOL;
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// エラーハンドラーを設定
set_error_handler(function($severity, $message, $file, $line) {
    custom_error_log("PHP Error: $message in $file on line $line", [
        'severity' => $severity,
        'file' => $file,
        'line' => $line
    ]);
});

// 例外ハンドラーを設定
set_exception_handler(function($exception) {
    custom_error_log("Uncaught Exception: " . $exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
});

// 致命的エラーハンドラーを設定
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        custom_error_log("Fatal Error: " . $error['message'], [
            'file' => $error['file'],
            'line' => $error['line'],
            'type' => $error['type']
        ]);
    }
});
?>
