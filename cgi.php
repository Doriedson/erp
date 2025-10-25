<?php

use App\Legacy\PrinterConfig;
use App\Legacy\Printing;
use App\Legacy\Company;
use App\Legacy\SaleOrder;
use App\Legacy\SaleOrderAddress;
use App\Legacy\SaleOrderItem;
use App\Legacy\PurchaseOrder;
use App\Legacy\PurchaseOrderItem;
use App\Legacy\Entity;
use App\Legacy\EntityAddress;
use App\Legacy\Product;
use App\Legacy\Notifier;
use App\Legacy\Calc;
use App\Legacy\OS;

require "inc/config.inc.php";
require "inc/authorization.php";

// $GLOBALS['authorized_id_entidade'] = 9;

function PrintOrdemDeCompra($id_compra) {

	$printing = null;

	$printer = new PrinterConfig();

	$printer->PrintingRead(PrinterConfig::PRINTING_PURCHASEORDER);

	if ($row = $printer->getResult()) {

		if ($row['id_impressora'] == null) {

			Notifier::Add("Impressora não definida!", Notifier::NOTIFIER_INFO);
			Send(null);
		}

	} else {

		Notifier::Add("Erro ao carregar dados da impressora!", Notifier::NOTIFIER_ERROR);
		Send(null);
	}

	$printing = new Printing($row['id_impressora']);

	$printing->initialize();

	$purchase = new PurchaseOrder();

	$purchase->Read($id_compra);

	$printing->textCenter("Lista para Conferência");

	$printing->line(1);

	$printing->text("Data: " . date_format(date_create(), "d/m/Y H:i"));

	$printing->line(1);

	if ($row = $purchase->getResult()) {

		$printing->textTruncate("Fornecedor: " . $row['razaosocial']);

		$printing->linedashspaced();

		$purchaseItem = new PurchaseOrderItem();

		$purchaseItem->getItems($id_compra);

		while ($row = $purchaseItem->getResult()) {

			$printing->textTruncate($row['produto']);

			if ($row['qtdvol'] == 1) {

				$printing->text(number_format($row['vol'], 3, ",", ".") . " " . $row['produtounidade']);

			} else {

				$printing->text(number_format($row['vol'], 3, ",", ".") . " volume [" . $row['qtdvol'] . " " . $row['produtounidade'] . "]");
			}

			$printing->line(1);
		}
	}

	$printing->linedashspaced();

	$printing->close();

	Notifier::Add("Ordem de Compra impressa!", Notifier::NOTIFIER_DONE);
	Send([]);
}

function Change_Status($pedido_id) {

	$ch = curl_init();

	$headr = array();
	// $headr[] = 'Content-length: 0';
	$headr[] = 'Content-type: application/json';
	$headr[] = 'Authorization: chave_api 3ed724736df65ae32b36 aplicacao 21a25d5f-2070-4d2e-9de3-f17445c8a678';

	curl_setopt($ch, CURLOPT_URL, "https://api.awsli.com.br/v1/situacao/pedido/" . $pedido_id);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"codigo\": \"pedido_em_separacao\"}");

	$response = curl_exec($ch);

	// if ($response === false)
	// {
	//     // throw new Exception('Curl error: ' . curl_error($crl));
	//     print_r('Curl error: ' . curl_error($crl));
	// }

	curl_close($ch);

	// var_dump($response);
	// return json_decode($response);
}

function navigate($url) {

	$ch = curl_init();

	$headr = array();
	$headr[] = 'Content-length: 0';
	$headr[] = 'Content-type: application/json';
	$headr[] = 'Authorization: chave_api 3ed724736df65ae32b36 aplicacao 21a25d5f-2070-4d2e-9de3-f17445c8a678';

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	$response = curl_exec($ch);

	if ($response === false)
	{
	    // throw new Exception('Curl error: ' . curl_error($crl));
	    print_r('Curl error: ' . curl_error($ch));
	    return null;
	}

	curl_close($ch);

	return json_decode($response);
}

if (!isset($_GET['action'])) {

	Notifier::Add("No action!", Notifier::NOTIFIER_ERROR);
	Send(null);
}

switch ($_GET['action']) {

	case "GetPedidoEcommerce":

		$response = navigate("https://api.awsli.com.br/v1/pedido/search/?situacao_id=9");
		// $response = navigate("https://api.awsli.com.br/v1/pedido/search/?since_numero=319");

		if ($response->meta->total_count == 0) {

			Send([]);
		}

		$sale = new SaleOrder();
		$saleItem = new SaleOrderItem();
		$entidade = new Entity();
		$product = new Product();

		for ($cupom_id = 0; $cupom_id < $response->meta->total_count; $cupom_id++) {

			$pedido = navigate("https://api.awsli.com.br" . $response->objects[$cupom_id]->resource_uri);

			$entidade->SearchByCode($pedido->cliente->cpf);

			if ($row = $entidade->getResult()) {

				$id_entidade = $row['id_entidade'];

			} else {

				$data_entidade = [
					// 'id_entidade' => 0,
					'cpfcnpj' => $pedido->cliente->cpf,
					'nome' => $pedido->cliente->nome,
					'telcelular' => $pedido->cliente->telefone_celular,
					'telresidencial' => '',
					'telcomercial' => '',
					'credito' => 0,
					'limite' => 0,
					'email' => '',
					'obs' => "Cadastro automatico. " . $pedido->cliente->email,
					'ativo' => 1];

				$id_entidade = $entidade->Create($data_entidade);
			}

			$entidadeEndereco = new EntityAddress();

			$entidadeEndereco->SearchCEP($id_entidade, $pedido->endereco_entrega->cep);

			if ($row = $entidadeEndereco->getResult()) {

				$id_endereco = $row['id_endereco'];

			} else {

				$id_endereco = $entidadeEndereco->CreateFrom([
					'id_entidade' => $id_entidade,
					'nickname' => "Endereço do site",
					'endereco' => $pedido->endereco_entrega->endereco . ", " . $pedido->endereco_entrega->numero. " " . $pedido->endereco_entrega->complemento,
					'bairro' => $pedido->endereco_entrega->bairro,
					'cidade' => $pedido->endereco_entrega->cidade,
					'uf' => $pedido->endereco_entrega->estado,
					'cep' => $pedido->endereco_entrega->cep,
					'obs' => ($pedido->endereco_entrega->referencia)?$pedido->endereco_entrega->referencia:""]);
			}

			// $GLOBALS['authorized_id_entidade'] = 9; //Para criar venda com colaborador responsavel

			$data_venda = [
				'id_entidade' => $id_entidade,
				'id_vendastatus' => SaleOrder::STATUS_PEDIDO_EM_ANDAMENTO,
				'frete' => $pedido->envios[0]->valor,
				'obs' => "*SITE* "  . $pedido->cliente_obs,
			];

			$id_venda = $sale->Create($data_venda);

			$enderecoVenda = new SaleOrderAddress();

			$enderecoVenda->Create([
				"id_venda" => $id_venda,
				'nickname' => "",
				'endereco' => $pedido->endereco_entrega->endereco . ", " . $pedido->endereco_entrega->numero. " " . $pedido->endereco_entrega->complemento,
				'bairro' => $pedido->endereco_entrega->bairro,
				'cidade' => $pedido->endereco_entrega->cidade,
				'uf' => $pedido->endereco_entrega->estado,
				'cep' => $pedido->endereco_entrega->cep,
				'obs' => ($pedido->endereco_entrega->referencia)? $pedido->endereco_entrega->referencia : "",
			]);

			for ($item_id=0; $item_id < count($pedido->itens); $item_id++) {

				$qtd = floatval($pedido->itens[$item_id]->quantidade);
				$peso_str = strpos($pedido->itens[$item_id]->sku, 'G');

				$obs = "";

				if ($peso_str === false) {

					$id_produto = $pedido->itens[$item_id]->sku;

					$peso_un = 1;

				} else {

					$id_produto = substr($pedido->itens[$item_id]->sku, 0, $peso_str );

					$peso_un = intval(substr($pedido->itens[$item_id]->sku,$peso_str+1,mb_strlen($pedido->itens[$item_id]->sku)-$peso_str)) / 1000;

					$peso_total = $qtd * $peso_un;

					// $obs = number_format($qtd, 0) . " UN";
				}

				//Verificar se produto é kit, composição ou normal
				$product->Read($id_produto);

				if ($row = $product->getResult()) {

					$preco = $row['preco'];
					$obs = "";

					$saleItem->Create($id_venda, $id_produto, $row['id_produtotipo'], $qtd * $peso_un, $pedido->itens[$item_id]->preco_venda / $peso_un, $obs);
				}
			}

			Change_Status($response->objects[$cupom_id]->numero);

			$printer = new PrinterConfig();

			$printer->getPrinting(PrinterConfig::PRINTING_SALEORDER);

			if ($row = $printer->getResult()) {

				SaleOrder::DoPrint($id_venda, $row['id_impressora']);
			}
		}

	break;

	case "PrintPedido":

		$id_venda = $_GET['id_venda'];

		$printer = new PrinterConfig();

		$printer->getPrinting(PrinterConfig::PRINTING_SALEORDER);

		if ($row = $printer->getResult()) {

			if (is_null($row['id_impressora'])) {

				Notifier::Add("Não há impressora configurada para impressão de pedidos!", Notifier::NOTIFIER_INFO);
				Send(null);
			}

			if (SaleOrder::DoPrint($id_venda, $row['id_impressora'])) {

				Notifier::Add("Pedido impresso!", Notifier::NOTIFIER_DONE);
				Send([]);

			} else {

				Notifier::Add("Erro ao imprimir pedido!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao imprimir pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

	break;

	case "purchase_order_print":

		$id_compra = $_GET['id_compra'];

		PrintOrdemDeCompra($id_compra);
	break;
}