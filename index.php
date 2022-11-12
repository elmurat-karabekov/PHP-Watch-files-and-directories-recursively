<?php

/*
    This project was creating following this tutorial:
    https://www.youtube.com/watch?v=5f4PjQJI-Fc4
*/

// This script whatches files and directories to detect changes

//directory name, argument passed to the script
$path = $argv[1];

//An associative array of $path => filemtime($path), for all files and directories within $path
$currentStatus = [];

readPath($path, $currentStatus);

while (true){
    clearCache($path);
    checkPath($path);
    sleep(1);
}

/*
    This function creates a mapping between all files and directories inside $path to their modification times:
        $path  =>  12651656,

        $path/dir1 => 1651616,
        $path/dir1/file1 => 5165655,
        $path/dir1/file2 => 9595848,

        $path/dir2 => 51619,
        $path/dir2/file1 => 651949,
        ....


    Everything is stored in $current status as an associative array keys - path, value - filemtime(path)
*/
function readPath($path, &$filesMap){
    $filesMap[$path] = filemtime($path);
    if (is_dir($path)){

        //list files and directories inside $path
        $files = scandir($path);

        foreach ($files as $file){
            if ($file == '.' || $file ==='..'){
                continue;
            }
            $fileName = $path.'/'.$file;
            $filesMap[$fileName] = filemtime($fileName);
            if (is_dir($fileName)){
                readPath($fileName, $filesMap);
            }
        }
    }
}

function clearCache($path){
    //clears file status cache
    clearstatcache(false, $path);
}

function checkPath($path){
    global $currentStatus;
    $newStatus = [];
    readPath($path, $newStatus);

    //Now that we have currentStatus and newStatus we can compare them:
    foreach($currentStatus as $file => $time){
        if (!isset($newStatus[$file])){
            echo "File \"$file\" was deleted...".PHP_EOL;
        } elseif ($newStatus[$file] !== $time){
            echo "File \"$file\" was modified...".PHP_EOL;
        }
    }
    foreach($newStatus as $file => $time){
        if (!isset($currentStatus[$file])){
            echo "File \"$file\" was added...".PHP_EOL;
        }
    }
    $currentStatus = $newStatus;
}