<?php
// coverage-checker.php
$inputFile  = $argv[1];
$percentage = min(100, max(0, (int) $argv[2]));

if (!file_exists($inputFile)) {
    throw new InvalidArgumentException('Invalid input file provided');
}

if (!$percentage) {
    throw new InvalidArgumentException('An integer checked percentage must be given as second parameter');
}

$xml             = new SimpleXMLElement(file_get_contents($inputFile));
$metrics         = $xml->xpath('//metrics');
$packages        = $xml->xpath('//package');

foreach ($packages as $package) {
    foreach ($package->file as $file) {
        $fileElements = (int) $file->metrics['elements'];
        $fileCoveredElements = (int) $file->metrics['coveredelements'];
        echo $file['name'] . ' ' . coverage_percent($fileElements, $fileCoveredElements) . '%' . PHP_EOL;
    }
}

$totalElements   = 0;
$coveredElements = 0;
foreach ($metrics as $metric) {
    $totalElements   += (int) $metric['elements'];
    $coveredElements += (int) $metric['coveredelements'];
}

$coverage = coverage_percent($totalElements, $coveredElements);

if ($coverage < $percentage) {
    echo 'Code coverage is ' . $coverage . '%, which is below the accepted ' . $percentage . '%' . PHP_EOL;
    exit(1);
}

echo 'Code coverage is ' . $coverage . '% - OK!' . PHP_EOL;

function coverage_percent($elements, $coveredElements){
    $coverage_percent =  ($elements === 0) ? 0 :  ($coveredElements/$elements) * 100;
    return round($coverage_percent, 2);
}
