<?php

$excludeDirs = [
    'vendor',
    '.git',
];

$excludeFiles = [
    'scan.txt',
    'scan.php',
    'composer.lock',
    '.env',
    'README.md',
    'php_errors.log',
];

// Убираем ограничения времени выполнения и памяти
set_time_limit(0);
ini_set('memory_limit', '-1');

/**
 * Проверяет, нужно ли исключить директорию
 */
function shouldExcludeDir($root, $dirName) {
    global $excludeDirs;
    
    $fullPath = $root . DIRECTORY_SEPARATOR . $dirName;
    $normalizedPath = realpath($fullPath);
    
    if ($normalizedPath === false) {
        return false;
    }
    
    foreach ($excludeDirs as $excludeDir) {
        $excludePath = '';
        
        if (strpos($excludeDir, '/') === 0) {
            $excludePath = realpath('.' . $excludeDir);
        } else {
            $excludePath = realpath($excludeDir);
        }
        
        if ($excludePath === false) {
            continue;
        }
        
        if ($normalizedPath === $excludePath || strpos($normalizedPath, $excludePath) === 0) {
            return true;
        }
    }
    
    return false;
}

/**
 * Рекурсивно строит дерево проекта (без ограничений)
 */
function makeTree($startPath) {
    global $excludeFiles;
    
    $tree = [];
    $directories = new RecursiveDirectoryIterator($startPath, RecursiveDirectoryIterator::SKIP_DOTS);
    $filter = new RecursiveCallbackFilterIterator($directories, function($current, $key, $iterator) {
        global $excludeDirs, $excludeFiles;
        
        // Проверяем, нужно ли исключить директорию
        if ($iterator->hasChildren()) {
            return !shouldExcludeDir(dirname($current->getPathname()), $current->getFilename());
        }
        
        // Проверяем, нужно ли исключить файл
        return !in_array($current->getFilename(), $GLOBALS['excludeFiles']);
    });
    
    $iterator = new RecursiveIteratorIterator($filter, RecursiveIteratorIterator::SELF_FIRST);
    
    // Отключаем лимит рекурсии PHP
    $iterator->setMaxDepth(-1);
    
    foreach ($iterator as $name => $object) {
        $depth = $iterator->getDepth();
        $indent = '';
        
        // Строим отступы
        for ($i = 0; $i < $depth; $i++) {
            $indent .= '│   ';
        }
        
        if ($object->isDir()) {
            $tree[] = $indent . '├── ' . $object->getFilename() . '/' . "\n";
        } else {
            $tree[] = $indent . '├── ' . $object->getFilename() . "\n";
        }
    }
    
    return implode('', $tree);
}

/**
 * Рекурсивно собирает все файлы для обработки (без ограничений)
 */
function getAllFiles($startPath) {
    global $excludeFiles;
    
    $files = [];
    $directories = new RecursiveDirectoryIterator($startPath, RecursiveDirectoryIterator::SKIP_DOTS);
    $filter = new RecursiveCallbackFilterIterator($directories, function($current, $key, $iterator) {
        global $excludeDirs;
        
        if ($iterator->hasChildren()) {
            return !shouldExcludeDir(dirname($current->getPathname()), $current->getFilename());
        }
        return true;
    });
    
    $iterator = new RecursiveIteratorIterator($filter, RecursiveIteratorIterator::SELF_FIRST);
    $iterator->setMaxDepth(-1);
    
    foreach ($iterator as $name => $object) {
        if ($object->isFile() && !in_array($object->getFilename(), $GLOBALS['excludeFiles'])) {
            $files[] = $object->getPathname();
        }
    }
    
    return $files;
}

/**
 * Определяет, является ли файл текстовым
 */
function isTextFile($filepath) {
    $textExtensions = ['txt', 'php', 'html', 'htm', 'css', 'js', 'json', 'xml', 'md', 
                       'ini', 'conf', 'cfg', 'sql', 'log', 'csv', 'yml', 'yaml', 
                       'twig', 'tpl', 'phtml', 'ctp', 'py', 'rb', 'pl', 'sh', 'bat'];
    
    $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    
    if (in_array($extension, $textExtensions)) {
        return true;
    }
    
    // Если расширение не в списке, пробуем прочитать первые байты
    $handle = fopen($filepath, 'rb');
    if ($handle) {
        $bytes = fread($handle, 1024);
        fclose($handle);
        
        // Проверяем наличие нулевых байтов (признак бинарного файла)
        return strpos($bytes, "\0") === false;
    }
    
    return false;
}

/**
 * Читает содержимое файла (без ограничений)
 */
function readFileContent($filepath) {
    $content = '';
    $handle = fopen($filepath, 'rb');
    
    if ($handle) {
        while (!feof($handle)) {
            $content .= fread($handle, 8192); // Читаем блоками по 8KB
        }
        fclose($handle);
    }
    
    return $content;
}

// Основной код
echo "Начинаем сканирование проекта...\n";

$outputFile = 'scan.txt';
$handle = fopen($outputFile, 'w', false);

if (!$handle) {
    die("Не удалось создать файл scan.txt\n");
}

echo "Генерируем дерево проекта...\n";
fwrite($handle, "=== PROJECT TREE ===\n");
fwrite($handle, makeTree('.'));
fwrite($handle, "\n=== FILE CONTENTS ===\n\n");

echo "Собираем список файлов...\n";
$files = getAllFiles('.');
$totalFiles = count($files);
$currentFile = 0;

foreach ($files as $file) {
    $currentFile++;
    $relativePath = str_replace('.' . DIRECTORY_SEPARATOR, '', $file);
    
    // Прогресс
    echo "\rОбрабатываем файл {$currentFile} из {$totalFiles}: " . basename($file);
    
    fwrite($handle, "» " . $relativePath . "\n");
    
    try {
        if (isTextFile($file)) {
            $content = readFileContent($file);
            fwrite($handle, $content);
            
            // Добавляем пустые строки между файлами
            fwrite($handle, "\n\n");
        } else {
            fwrite($handle, "[binary file: " . 
                   number_format(filesize($file)) . " bytes]\n\n");
        }
    } catch (Exception $e) {
        fwrite($handle, "[error: " . $e->getMessage() . "]\n\n");
    }
    
    // Принудительно сбрасываем буфер для освобождения памяти
    if ($currentFile % 100 == 0) {
        fflush($handle);
    }
}

fclose($handle);
// echo "\n\nГотово! Результат сохранен в scan.txt\n";
// echo "Всего обработано файлов: " . $totalFiles . "\n";
// echo "Размер выходного файла: " . number_format(filesize($outputFile)) . " байт\n";