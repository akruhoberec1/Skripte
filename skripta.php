<?php 

$saveFolderName = 'Image folder';
$scriptPath = __DIR__;
$saveFolderPath = $scriptPath . DIRECTORY_SEPARATOR . $saveFolderName;

if (!file_exists($saveFolderPath)) {
    mkdir($saveFolderPath, 0777, true);
}

$csvFile = '/path/to/csv/file'; //zamjenimo putanju gdje stoji file.csv
$handle = fopen($csvFile, 'r');
$header = fgetcsv($handle, 0, ';'); // preskačemo header

$errorLogPath = $saveFolderPath . DIRECTORY_SEPARATOR . 'error.log';
$errorLogHandle = fopen($errorLogPath, 'w');

$index = array(); // asocijativni niz za praćenje broja slike po artiklu

// glavna petlja
while (($data = fgetcsv($handle, 0, ';')) !== false || !feof($handle)) {

    $articleNumber = $data[2];
    $imageUrlsString = $data[22];

    $imageUrls = explode('|', $imageUrlsString);

    //petlja koja hvata slike iz jednog reda
    foreach ($imageUrls as $imageUrl) {
        // u nekim redovima ima više ||||||, moramo preskočiti prazne URLove
        if (empty($imageUrl)) {
            continue;
        }

        $imageData = file_get_contents($imageUrl);
        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);

        if (!isset($index[$articleNumber])) {
            $index[$articleNumber] = 1; 
        }

        $savePath = $saveFolderPath . DIRECTORY_SEPARATOR . $articleNumber . '_' . $index[$articleNumber] . '.' . $extension;
        file_put_contents($savePath, $imageData);

        if ($imageData === false) {
            // error log poruka
            $errorMessage = 'Failed to download image from URL: ' . $imageUrl . ' for article ' . $articleNumber . '_' . $index[$articleNumber] . '.' . $extension . PHP_EOL;
            fwrite($errorLogHandle, $errorMessage);
        }

        $index[$articleNumber]++;
    }
}

// closeAll
fclose($handle);
fclose($errorLogHandle);