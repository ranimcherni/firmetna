<?php
require __DIR__ . '/vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;

try {
    $builder = new Builder(writer: new SvgWriter());
    $result = $builder->build(data: 'Test QR FIRMETNA', size: 300, margin: 10);
    echo "SUCCESS!\n";
    echo "MimeType: " . $result->getMimeType() . "\n";
    echo "Size: " . strlen($result->getString()) . " bytes\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
