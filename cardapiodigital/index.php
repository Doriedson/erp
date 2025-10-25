<?php


use App\View\View;
use App\Legacy\Clean;
use App\Legacy\Company;
use App\Legacy\Product;
use App\Legacy\ProductSector;
use App\Support\Version;

require __DIR__ . "/../inc/config.inc.php";

$tplDigitalMenu = new View("digital_menu");

$company = new Company();

$company->Read();

$empresa = "Nome da Empresa";

if ($row = $company->getResult()) {

    $empresa = $row['empresa'];
}

$productSector = new ProductSector();

$productSector->getDigitalMenu();

$product = new Product();

$extra_block_product_sector = "";

while ($row = $productSector->getResult()) {

    $product->getDigitalMenuSector($row['id_produtosetor']);

    $extra_block_product = "";

    if ($rowProduct = $product->getResult()) {

        do {
            $rowProduct = Product::FormatFields($rowProduct);

            $extra_block_product .= $tplDigitalMenu->getContent($rowProduct, "EXTRA_BLOCK_PRODUCT");

        } while ($rowProduct = $product->getResult());

        $extra_block_product_sector .= $tplDigitalMenu->getContent(["produtosetor" => $row['produtosetor'], "extra_block_product" => $extra_block_product], "EXTRA_BLOCK_PRODUCT_SECTOR");
    }
}

$date = new DateTimeImmutable();

$content = [
    "version" => Version::get(),
    "date" => date('Y-m-d'),
    "date_search" => date("Y-m"),
    "title" => 'Cardápio Digital',
    "extra_block_product_sector" => $extra_block_product_sector,
    // 'manifest' => 'backend_manifest.json',
    "empresa" => $empresa,
    "timestamp" => $date->getTimestamp()

];

$tplDigitalMenu->Show($content, "BLOCK_PAGE");