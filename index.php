<?php

$download_file = false; //true = data save in downloads folder else false = direct zip download

header('Content-Type: application/json');

if (isset($_GET['url'])) {
    $url = $_GET['url'];

    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $host = parse_url($url, PHP_URL_HOST);
        $unique = uniqid();
        $baseName = $host . "_" . $unique;
        $zipName = $baseName . ".zip";
        $zipTempPath = sys_get_temp_dir() . "/" . $zipName;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $html = curl_exec($ch);
        if (curl_errno($ch)) {
            echo json_encode(["status" => "error", "message" => "cURL error"]);
            exit;
        }
        curl_close($ch);
        if ($html === false) {
            echo json_encode(["status" => "error", "message" => "Failed to fetch HTML"]);
            exit;
        }

        preg_match_all('/<img[^>]+src=["\'](.*?)["\']/i', $html, $images);
        preg_match_all('/<link[^>]+href=["\'](.*?)["\']/i', $html, $links);
        preg_match_all('/<script[^>]+src=["\'](.*?)["\']/i', $html, $scripts);
        $resources = array_merge($images[1], $links[1], $scripts[1]);

        $zip = new ZipArchive();
        if ($zip->open($zipTempPath, ZipArchive::CREATE) !== TRUE) {
            echo json_encode(["status" => "error", "message" => "Cannot create ZIP"]);
            exit;
        }

        $zip->addFromString("index.html", $html);

        foreach ($resources as $res) {
            $resUrl = parse_url($res, PHP_URL_SCHEME) ? $res : rtrim($url, '/') . '/' . ltrim($res, '/');
            $resData = @file_get_contents($resUrl);
            if ($resData !== false) {
                $resPath = parse_url($resUrl, PHP_URL_PATH);
                $ext = pathinfo($resPath, PATHINFO_EXTENSION);
                $fileName = basename($resPath);
                if ($ext == "css") {
                    $zip->addFromString("css/" . $fileName, $resData);
                } elseif ($ext == "js") {
                    $zip->addFromString("js/" . $fileName, $resData);
                } elseif (in_array($ext, ["jpg", "jpeg", "png", "gif", "webp", "svg"])) {
                    $zip->addFromString("images/" . $fileName, $resData);
                } else {
                    $zip->addFromString("assets/" . $fileName, $resData);
                }
            }
        }

        $zip->close();

        if ($download_file) {
            $savePath = "downloads/";
            if (!file_exists($savePath)) mkdir($savePath, 0777, true);
            $finalPath = $savePath . $zipName;

            if (copy($zipTempPath, $finalPath)) {
                unlink($zipTempPath);
                $urlPath = "https://" . $_SERVER['HTTP_HOST'] . "/" . $finalPath;
                echo json_encode(["status" => "success", "url" => $urlPath]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to move ZIP file"]);
            }
        } else {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $host . '.zip"');
            header('Content-Length: ' . filesize($zipTempPath));
            flush();
            readfile($zipTempPath);
            unlink($zipTempPath);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid URL"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No URL provided"]);
}
?>
