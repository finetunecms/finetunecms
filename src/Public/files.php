<?php

if (!empty($_SERVER['REQUEST_URI'])) {
    $uri = $_SERVER['REQUEST_URI'];
    $uriParts = explode('/', strtok($uri, '?'));


    if ($uriParts[1] == 'image') {

        if (!empty($uriParts[1])) {
            if (!empty($uriParts[2])) {

                $filename = '/'.$uriParts[2] .'/resized/';
                $fileParts = explode('.', $uriParts[3]);
                $filename = $filename.$fileParts[0];
                if (isset($fileParts[1])) {
                    $fileExt = $fileParts[1];
                } else {
                    $fileExt = '';
                }
                $fileSize = null;
                $fileSizeArray = array();
                $vars = array();
                $fileUrl = null;
                $fileMime = null;
                if (!empty($uriParts[4])) {

                    $parts = explode('x', $uriParts[4]);

                    if(isset($parts[1])){

                        $filename = $filename.'-'.$parts[0];
                        $filename = $filename.'x'.$parts[1];

                        if(isset($_GET['fit'])){

                            $filename .= '-fit';
                            if(isset($_GET['bg'])){
                                $filename .= '-'.$_GET['bg'];
                            }else{
                                $filename .= '-000000';
                            }
                        }

                    }else{
                        $filename = $filename.'-'.$uriParts[4];
                    }
                }
                $filenameArray = explode('?', $filename);
                $filename = $filenameArray[0];

                if(!empty($_GET)){
                    if(isset($_GET['c'])){
                        $cropSettings = explode('-', $_GET['c']);
                        $filename .= $cropSettings[0].'-'. $cropSettings[1];
                    }
                    if(isset($_GET['f'])){
                        $filename .= '-'.$_GET['f'];
                    }
                    if(isset($_GET['r'])){
                        $filename .= '-'.$_GET['rot'];
                    }
                    if(isset($_GET['greyscale'])){
                        $filename .= '-grey';
                    }
                    if(isset($_GET['q'])){
                        $filename .= '-'.$_GET['q'];
                    }
                }
                $imageUrl = __DIR__ . '/../storage/uploads';
                $mimes = array(
                    'gif' => 'image/gif',
                    'jpg' => 'image/jpg',
                    'png' => 'image/png',
                    'psd' => 'image/psd',
                    'bmp' => 'image/bmp',
                    'tiff' => 'image/tiff'
                );
                $extensionRaw = str_replace('.', '', $fileExt);
                $extensionPart = explode('?', $extensionRaw);
                $extension = $extensionPart[0];

                foreach ($mimes as $ext => $mime) {
                    if ($ext == $extension) {
                        $fileMime = $mime;
                    }
                }

                $imageUrl .= $filename.'.'.$fileExt;

                if (file_exists($imageUrl)) {
                    header("Content-Type: $fileMime");
                    header("Content-Length: " . filesize($imageUrl));
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($imageUrl)));
                    $fp = fopen($imageUrl, 'rb');
                    fpassthru($fp);
                    unset($fp);
                    unset($imageUrl);
                    exit;
                }

                $imageUrl = str_replace('/resized/', '', $imageUrl);
                if (file_exists($imageUrl)) {
                    header("Content-Type: $fileMime");
                    header("Content-Length: " . filesize($imageUrl));
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($imageUrl)));
                    $fp = fopen($imageUrl, 'rb');
                    fpassthru($fp);
                    unset($fp);
                    unset($imageUrl);
                    exit;
                }

            }
        }
    }
}

