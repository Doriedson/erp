<?php


use App\Legacy\Notifier;
use App\Legacy\ProductComposition;
use App\Legacy\ProductKit;
use App\View\View;
use App\Legacy\Product;
use App\Legacy\SaleOrderItem;
use App\Legacy\Calc;

require "inc/config.inc.php";
require "inc/authorization.php";

ControlAccess::Check(ControlAccess::CA_SERVIDOR_RELATORIO);

function SearchByHours($id_produto, $produto, $produtounidade, $dataini, $hours) {

	$datasets = [];
	$total = 0;

	$saleItem = new SaleOrderItem();

	$saleItem->ReportItemByDate($id_produto, $dataini);

	if ($row = $saleItem->getResult()) {

		do {

			$hours[str_pad($row['hora'], 2, "0", STR_PAD_LEFT)] = $row['qtd'];

			$total = Calc::Sum([
				$total,
				$row['qtd']
			]);

		} while ($row = $saleItem->getResult());

		$datasets_data = [];

		foreach($hours as $key => $value) {

			// $labels[] = $key . " h";
			$datasets_data[] = $value;

		}

		$datasets = [
			"label" => $produto . " [ " . number_format($total, 3, ",", ".") . " " . $produtounidade . " ]",
			"data" => $datasets_data,
		];

	} else {

		$datasets = [
			"label" => $produto . " [ 0 " . $produtounidade . " ]",
			"data" => [],
		];
	}

	return [$datasets, $total];
}

function SearchByDateInterval($id_produto, $produto, $produtounidade, $dataini, $datafim, $dates) {

	$datasets = [];

	$total = 0;

	$saleItem = new SaleOrderItem();

	$saleItem->ReportItemByDateInterval($id_produto, $dataini, $datafim);

	if ($row = $saleItem->getResult()) {

		do {

			$dates[date_format(date_create($row['year'] . "-" . $row['month'] . "-" . $row['day']), "d/m/Y")] = $row['qtd'];

			$total = Calc::Sum([
				$total,
				$row['qtd']
			]);

		} while ($row = $saleItem->getResult());

		$datasets_data = [];

		foreach($dates as $key => $value) {

			// $labels[] = $key;
			$datasets_data[] = $value;

		}

		$datasets = [
			"label" => $produto . " [ " . number_format($total, 3, ",", ".") . " " . $produtounidade . " ]",
			"data" => $datasets_data,
		];

	} else {

		$datasets = [
			"label" => $produto . " [ 0 " . $produtounidade . " ]",
			"data" => [],
		];
	}

	return [$datasets, $total];
}

switch ($_POST['action']) {

	case "load":

		$tplSaleProduct = new View('report_sale_one_product');
        $tplProduct = new View('product');

		$data = ['data' => date('Y-m-d')];
        $data["block_product_autocomplete_search"] = $tplProduct->getContent([], "BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH");

		Send($tplSaleProduct->getContent($data, "BLOCK_PAGE"));
	break;

	case "report_sale_one_product_search":

		$dataini = $_POST['dataini'];
		$datafim = $_POST['datafim'];
		$intervalo = ($_POST['intervalo'] == "false")? false : true;
        $produto = $_POST['produto'];

        $product = new Product();

		if( is_numeric($produto) ) {

			$product->SearchByCode($produto);

		} else {

			$product->SearchByString($produto);
		}

		if ($row = $product->getResult()) {

			$id_produto = $row['id_produto'];
			$produto = $row['produto'];
			$produtounidade = $row['produtounidade'];
			$id_produtotipo = $row['id_produtotipo'];

			$filter = "";

            $saleItem = new SaleOrderItem();

			$total = 0;

            if ($intervalo == true) {

				$dateend = new DateTime($datafim);
				$dateend->modify('+1 day');

				$period = new DatePeriod(
					new DateTime($dataini),
					new DateInterval('P1D'),
					$dateend
			   	);

				$dates = [];
				$labels = [];

				foreach ($period as $key => $value) {

					$index_date = date_format($value, "d/m/Y");

					$labels[] = $index_date;

					$dates[$index_date] = 0;
				}

				[$dataset, $total_search] = SearchByDateInterval($id_produto, $produto, $produtounidade, $dataini, $datafim, $dates);

				$total = Calc::Sum([
					$total,
					$total_search
				]);

				$data['chart'][] = [
					"labels" => $labels,
					// "title" => "",
					"datasets" => [$dataset]
				];

				switch ($id_produtotipo) {

					case Product::PRODUTO_TIPO_NORMAL:

						$productComposition = new ProductComposition();

						$productComposition->having($id_produto);

						while ($rowComposition = $productComposition->getResult()) {

							[$dataset, $total_search] = SearchByDateInterval($rowComposition['id_composicao'], $rowComposition["produto"], $rowComposition["produtounidade"], $dataini, $datafim, $dates);

							$data['chart'][] = [
								"labels" => $labels,
								// "title" => "",
								"datasets" => [$dataset]
							];

							$total = Calc::Sum([
								$total,
								Calc::Mult($total_search, $rowComposition["qtd"])
							]);
						}

						$productKit = new ProductKit();

						$productKit->having($id_produto);

						while ($rowKit = $productKit->getResult()) {

							[$dataset, $total_search] = SearchByDateInterval($rowKit['id_kit'], $rowKit["produto"], $rowKit["produtounidade"], $dataini, $datafim, $dates);

							$data['chart'][] = [
								"labels" => $labels,
								// "title" => "",
								"datasets" => [$dataset]
							];

							$total = Calc::Sum([
								$total,
								Calc::Mult($total_search, $rowKit["qtd"])
							]);
						}

						break;
				}

				$filter = date_format(date_create($dataini), "d/m/Y") . " a " . date_format(date_create($datafim), "d/m/Y");

				$tplReport = new View('report_sale_one_product');

				Send([
					// "data" => $tplReport->getContent([], "EXTRA_BLOCK_CONTAINER"),
					"filter" => $filter,
					"chart" => $data['chart'],
					"total" => number_format($total, 3, ",", ".")
					// "set" => $datasets_data
				]);

            } else {

				$dateend = new DateTime($dataini);
				$dateend->modify('+1 day');

				$period = new DatePeriod(
					new DateTime($dataini),
					new DateInterval('PT1H'),
					$dateend
			   	);

				$hours = [];
				$labels = [];

				foreach ($period as $key => $value) {

					$index_hour = $value->format("H");

					$labels[] = $index_hour;

					$hours[$index_hour] = 0;
				}

				[$dataset, $total_search] = SearchByHours($id_produto, $produto, $produtounidade, $dataini, $hours);

				$total = Calc::Sum([
					$total,
					$total_search
				]);

				$data['chart'][] = [
					"labels" => $labels,
					// "title" => "",
					"datasets" => [$dataset]
				];

				switch ($id_produtotipo) {

					case Product::PRODUTO_TIPO_NORMAL:

						$productComposition = new ProductComposition();

						$productComposition->having($id_produto);

						while ($rowComposition = $productComposition->getResult()) {

							[$dataset, $total_search] = SearchByHours($rowComposition['id_composicao'], $rowComposition["produto"], $rowComposition["produtounidade"], $dataini, $hours);

							$data['chart'][] = [
								"labels" => $labels,
								// "title" => "",
								"datasets" => [$dataset]
							];

							$total = Calc::Sum([
								$total,
								Calc::Mult($total_search, $rowComposition["qtd"])
							]);
						}

						$productKit = new ProductKit();

						$productKit->having($id_produto);

						while ($rowKit = $productKit->getResult()) {

							[$dataset, $total_search] = SearchByHours($rowKit['id_kit'], $rowKit["produto"], $rowKit["produtounidade"], $dataini, $hours);

							$data['chart'][] = [
								"labels" => $labels,
								// "title" => "",
								"datasets" => [$dataset]
							];

							$total = Calc::Sum([
								$total,
								Calc::Mult($total_search, $rowKit["qtd"])
							]);
						}

						break;
				}

				$tplReport = new View('report_sale_one_product');

				$filter = date_format(date_create($dataini), "d/m/Y");

				Send([
					// "data" => $tplReport->getContent([], "EXTRA_BLOCK_CONTAINER"),
					"filter" => $filter,
					"chart" => $data['chart'],
					"total" => number_format($total, 3, ",", ".")
					// "set" => $datasets_data
				]);

					// $data['chart'] = [
					// 	"labels" => $labels,
					// 	"title" => "",
					// 	"datasets" => [[
					// 		"label" => $produto . " [" . number_format($total, 3, ",", ".") . " " . $produtounidade . "]",
					// 		"data" => $datasets_data,
					// 	]],
					// ];

					// $tplReport = new View('report_sale_one_product');

					// Send([
					// 	"data" => $tplReport->getContent([], "EXTRA_BLOCK_CONTAINER"),
					// 	"filter" => $filter,
					// 	"chart" => $data['chart'],
					// 	"set" => $datasets_data
					// ]);

				// } else {

				// 	Send(null, "Não há registro de venda do produto no período!", Notifier::NOTIFIER_ERROR);

				// }

            }

        } else {

			Notifier::Add("Produto não localizado!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "reportsaleonproduct_popup":

		$id_produto = $_POST['id_produto'];
		$datestart = $_POST['datestart'];
		$dateend = $_POST['dateend'];
		$dateend_sel = ($_POST['dateend_sel'] == "true")?true:false;
		$datelock = ($_POST['datelock'] == "true")?true:false;
//  Send(null, $dateend_sel, Notifier::NOTIFIER_ERROR); exit();
		$product = new Product();

		$product->Read($id_produto);

		if ($row = $product->getResult()) {

			$tplIndex = new View("index");
			$tplProduct = new View("product");
			$tplReport = new View("report_sale_one_product");

			$row = Product::FormatFields($row);

			$row['datestart'] = $datestart;
			$row['dateend'] = $dateend;
			$row['dateend_sel'] = ($dateend_sel == true)?"checked":"";
			$row['dateend_disabled'] = ($dateend_sel == true)?"":"disabled";

			if ($datelock == true) {

				$row['extra_block_button_datelock'] = $tplReport->getContent($row, "EXTRA_BLOCK_BUTTON_DATELOCK");

			} else {

				$row['extra_block_button_datelock'] = $tplReport->getContent($row, "EXTRA_BLOCK_BUTTON_DATEUNLOCK");
			}

			// $row['block_product_produto'] = $tplProduct->getContent($row, "BLOCK_PRODUCT_PRODUTO");

			Send($tplReport->getContent($row, "EXTRA_BLOCK_POPUP_REPORTSALEONEPRODUCT"));

		} else {

			Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

	case "reportsaleonproduct_popup_data":

		$produto = $_POST['produto'];

		$product = new Product();

		if( is_numeric($produto) ) {

			$product->SearchByCode($produto);

		} else {

			$product->SearchByString($produto);
		}

		if ($row = $product->getResult()) {

			$tplProduct = new View("product");
			$tplReport = new View("report_sale_one_product");

			$row = Product::FormatFields($row);

			// $row['block_product_produto'] = $tplProduct->getContent($row, "BLOCK_PRODUCT_PRODUTO");

			// $data['block_popup_reportsaleoneproduct_data'] = $tplReport->getContent($row, "BLOCK_POPUP_REPORTSALEONEPRODUCT_DATA");

			Send($tplReport->getContent($row, "EXTRA_BLOCK_REPORTSALEONEPRODUCT_CONTAINER"));

		} else {

			Notifier::Add("Erro ao carregar dados do produto!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

    default:

		Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

		break;
}