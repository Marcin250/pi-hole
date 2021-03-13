<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Str;

$listsFile = 'lists.txt';
$allHostsFile = 'hosts/allHosts.txt';
$tmpHostsFile = 'hosts/tmpHosts.txt';

$ignoredEntries = [
    '0.0.0.0',
    '0.0.0.0 ',
    '127.0.0.1 ',
    '127.0.0.1	',
];

$lists = fopen($listsFile, 'ab+');
$hosts = fopen($allHostsFile, 'ab+');

while (($url = fgets($lists)) !== false) {
    if (empty(trim($url))) {
        continue;
    }

    echo $url;

    file_put_contents($tmpHostsFile, file_get_contents(trim($url)));
    $tmpHosts = fopen($tmpHostsFile, 'ab+');

    while (($line = fgets($tmpHosts)) !== false) {
        $parsedLine = trim(str_replace($ignoredEntries, array_fill(0, count($ignoredEntries), ''), $line));

        if (empty($parsedLine)
            || Str::startsWith($parsedLine, '#')
            || !Str::contains($parsedLine, '.')
            || Str::contains($parsedLine, 'localhost')) {
            continue;
        }

        fputs($hosts, $parsedLine . PHP_EOL);
    }

    fclose($tmpHosts);
}

fclose($lists);
fclose($hosts);

@unlink($tmpHostsFile);
exec("split -l 1000000 --numeric-suffixes --additional-suffix=.txt {$allHostsFile} hosts/hosts");
@unlink($allHostsFile);

echo "Done.\n";