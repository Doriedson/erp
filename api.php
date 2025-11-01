<?php

use App\Legacy\CashChange;
use App\Legacy\ControlAccess;
use App\Legacy\CashAdd;
use App\Legacy\CashDrain;
use App\Legacy\CashDrawer;
use App\Legacy\Cashier;
use App\Legacy\Clean;
use App\Legacy\Collaborator;
use App\Legacy\Company;
use App\Legacy\Config;
use App\Legacy\Entity;
use App\Legacy\Freight;
use App\Legacy\Product;
use App\Legacy\SaleOrder;
use App\Legacy\SaleOrderItem;
use App\Legacy\SaleOrderAddress;
use App\Legacy\EntityAddress;
use App\Legacy\Printing;
use App\Legacy\Notifier;
use App\Legacy\SaleOrderStatusChange;
use App\Legacy\Table;
use App\Legacy\Pdv;
use App\Legacy\Log;
use App\Legacy\SaleOrderPay;
use App\View\View;
use App\Legacy\Calc;


require "inc/config.inc.php";

function Unauthorized($message) {

    $data = [
        "authorized" => false,
        "message" => $message,
    ];

    Send($data);
}

switch($_POST['action']) {

    case "login":

        $id_entidade = Clean::HtmlChar($_POST['user']);
        $pass = Clean::HtmlChar($_POST['pass']);
        $acesso = intval($_POST['acesso']); //Always ControlAccess::CA_PDV

        if ($acesso != ControlAccess::CA_PDV) {

            Notifier::Add("Erro na solicitação de login (PDV)!", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        // if (ControlAccess::Login($id_entidade, $pass, ControlAccess::CA_PDV, false)) {

            $data = [
                "security" => true,
                "authorized" => true,
                "id_entidade" => $GLOBALS['authorized_id_entidade'],
                "nome" => $GLOBALS['authorized_nome'],
                "acesso" => true,
            ];

            Send ($data);
        // }

        break;

    case "authenticate":

        $id_entidade = Clean::HtmlChar($_POST['user']);
        $pass = Clean::HtmlChar($_POST['pass']);
        $acesso = intval($_POST['acesso']);

        if ( trim($id_entidade)=='') {// || trim($pass=='') ) {

            Unauthorized("Usuário não localizado!");

        } else {

            $data = [$id_entidade];

            $collaborator = new Collaborator();

            $collaborator->Read($id_entidade);

            if($row = $collaborator->getResult()) {

                $access = json_decode($row['acesso']);

                $data = [
                    "security" => password_verify($pass, $row['hash']),
                    "authorized" => ($access[$acesso] == 1)? true: false,
                    "id_entidade" => $row['id_entidade'],
                    "nome" => $row['nome'],
                    "acesso" => $acesso,
                ];

                Send ($data);

            } else {

                Unauthorized("Usuário não localizado!");
            }
        }

        break;
}

$GLOBALS["api_access"] = true;

require "inc/authorization.php";

function PrintTrocoFim($id_caixa) {

    $cashier = new Cashier();

    $cashier->Read($id_caixa);

    if (!$rowCashier = $cashier->getResult()) {

        Notifier::Add("Erro ao carregar informaçãoes do caixa!", Notifier::NOTIFIER_ERROR);

        Send(null);
    }

    $pdv = new Pdv();

    $pdv->Read($rowCashier['id_pdv']);

    if ($rowPDV = $pdv->getResult()) {

        if (!is_null($rowPDV['id_impressora'])) {

            $printing = new Printing($rowPDV['id_impressora']);

            $printing->initialize();

            $company = new Company();

            $company->Read();

            if ($rowCompany = $company->getResult()) {

                $printing->textCenter($rowCompany['empresa']);
            }

            $printing->line(1);

            $field1 = "PDV: " . $rowPDV['descricao'];
            $field2 = "Data: " . date("d/m/Y H:i");

            $printing->textSpaceBetween($field1, $field2);

            $entity = new Entity();

            $entity->Read($rowCashier['id_entidade']);

            if($rowEntity = $entity->getResult()) {

                $printing->textTruncate("Op.: " . $rowEntity['nome']);
            }

            $printing->linedashspaced();
            $printing->textCenter("FUNDO DE CAIXA");
            $printing->linedashspaced();

            $cashChange = new CashChange();

            $cashChange->Read($id_caixa);

            if ($rowCashChange = $cashChange->getResult()) {

                $troco = number_format($rowCashChange['moeda_1'], 2, ',', '.');
                $printing->text("0,01: R$ " . $troco);
                $troco = number_format($rowCashChange['moeda_5'], 2, ',', '.');
                $printing->text("0,05: R$ " . $troco);
                $troco = number_format($rowCashChange['moeda_10'], 2, ',', '.');
                $printing->text("0,10: R$ " . $troco);
                $troco = number_format($rowCashChange['moeda_25'], 2, ',', '.');
                $printing->text("0,25: R$ " . $troco);
                $troco = number_format($rowCashChange['moeda_50'], 2, ',', '.');
                $printing->text("0,50: R$ " . $troco);
                $troco = number_format($rowCashChange['cedula_1'], 2, ',', '.');
                $printing->text("1,00: R$ " . $troco);
                $troco = number_format($rowCashChange['cedula_2'], 2, ',', '.');
                $printing->text("2,00: R$ " . $troco);
                $troco = number_format($rowCashChange['cedula_5'], 2, ',', '.');
                $printing->text("5,00: R$ " . $troco);
                $troco = number_format($rowCashChange['cedula_10'], 2, ',', '.');
                $printing->text("10,00: R$ " . $troco);
                $troco = number_format($rowCashChange['cedula_20'], 2, ',', '.');
                $printing->text("20,00: R$ " . $troco);
                $troco = number_format($rowCashChange['cedula_50'], 2, ',', '.');
                $printing->text("50,00: R$ " . $troco);
                $troco = number_format($rowCashChange['cedula_100'], 2, ',', '.');
                $printing->text("100,00: R$ " . $troco);
                $troco = number_format($rowCashChange['cedula_200'], 2, ',', '.');
                $printing->text("200,00: R$ " . $troco);
            }

            $printing->line(1);

            $printing->text("Total: R$ " . number_format($rowCashier['trocofim'], 2, ",", "."));

            $printing->linedashspaced();

            $printing->close();

            // Send([], "Imprimindo fundo de caixa...", Notifier::NOTIFIER_INFO);

        } else {

            // Send([], "Sem impressora configurada!", Notifier::NOTIFIER_INFO);
        }

    } else {

        Notifier::Add("Erro ao carregar informaçãoes do caixa!", Notifier::NOTIFIER_ERROR);
        Send(null);
    }
}

function PrintPdvClosing($id_caixa, $return = false) {

    $cashier = new Cashier();

    $cashier->Read($id_caixa);

    if (!$rowCashier = $cashier->getResult()) {

        Notifier::Add("Erro ao carregar informaçãoes do caixa!", Notifier::NOTIFIER_ERROR);

        Send(null);
    }

    if (is_null($rowCashier["datafim"])) {

        Notifier::Add("Não é possivel ver dados de fechamento com o caixa aberto!", Notifier::NOTIFIER_ERROR);

        Send(null);
    }

    $id_impressora = null;

    $pdv = new Pdv();

    $pdv->Read($rowCashier['id_pdv']);

    if ($row = $pdv->getResult()) {

        $id_impressora = $row['id_impressora'];

    } else {

        Notifier::Add("Erro ao carregar informaçãoes do caixa!", Notifier::NOTIFIER_ERROR);

        Send(null);
    }

    if ($return == true) {

        $id_impressora = -1;
    }

    if (!is_null($id_impressora)) {

        $printing = new Printing($id_impressora);

        $printing->initialize();

        $company = new Company();

        $company->Read();

        if ($rowCompany = $company->getResult()) {

            $printing->textCenter($rowCompany['empresa']);
        }

        $printing->line(1);

        $field1 = "PDV: " . $row['descricao'];
        $field2 = " " . date("d/m/Y H:i");

        $printing->textSpaceBetween($field1, $field2);

        $entity = new Entity();

        $entity->Read($rowCashier['id_entidade']);

        if($rowEntity = $entity->getResult()) {

            $printing->textTruncate("Op.: " . $rowEntity['nome']);
        }

        $printing->linedashspaced();
        $printing->textCenter("FECHAMENTO DE CAIXA");
        $printing->line(1);

        $printing->text("Abertura " . date_format(date_create($rowCashier['dataini']), "d/m/Y H:i"));
        $printing->text("Fechamento " . date_format(date_create($rowCashier['datafim']), "d/m/Y H:i"));

        $printing->line(1);

        $saleOrder = new SaleOrder();

        $saleOrder->getPaymentsByCashier($id_caixa);

        $total = 0;
        $entity_credit = 0;

        while ($rowSale = $saleOrder->getResult()) {

            $total += $rowSale['total'];

            $cash_type = $rowSale['especie'];
            $cash_value = "R$ " . number_format($rowSale['total'], 2, ',', '.');

            if ($rowSale['id_especie'] == 2) {

                $entity_credit += $rowSale['total'];
            }

            $printing->textSpaceBetween($cash_type, $cash_value);
        }

        $printing->line(1);

        $field1 = "Total Bruto ";
        $field2 = "R$ " . number_format($total, 2, ",", ".");
        $printing->textSpaceBetween($field1, $field2);

        $printing->line(1);

        $field1 = "Fundo de Caixa Abertura ";
        $field2 = "R$ " . number_format($rowCashier['trocoini'], 2, ",", ".");
        $printing->textSpaceBetween($field1, $field2);

        $total += $rowCashier['trocoini'];

        $field1 = "Fundo de Caixa Fechamento ";
        $field2 = "R$ " . number_format($rowCashier['trocofim'], 2, ",", ".");
        $printing->textSpaceBetween($field1, $field2);

        $total -= $rowCashier['trocofim'];

        $total -= $entity_credit;

        $cashierDrain = new CashDrain();

        $cashierDrain->getTotalByCashier($id_caixa);

        if ($rowCashierDrain = $cashierDrain->getResult()) {

            $field1 = "Total Sangrias ";
            $field2 = "R$ " . number_format($rowCashierDrain['total'], 2, ",", ".");
            $printing->textSpaceBetween($field1, $field2);

            $total -= $rowCashierDrain['total'];
        }

        $cashAdd = new CashAdd();

        $cashAdd->getTotalByCashier($id_caixa);

        if ($rowCashAdd = $cashAdd->getResult()) {

            $field1 = "Total Reforços ";
            $field2 = "R$ " . number_format($rowCashAdd['total'], 2, ",", ".");
            $printing->textSpaceBetween($field1, $field2);

            $total += $rowCashAdd['total'];
        }

        $printing->line(1);

        $field1 = "Saldo do Caixa ";

        if ($total == 0) {

            $field2 = "R$ " . number_format($total, 2, ",", ".");

        } elseif ($total > 0) {

            $field2 = "Faltou R$ " . number_format($total, 2, ",", ".");

        } else {

            $field2 = "Sobrou R$ " . number_format(-$total, 2, ",", ".");
        }

        $printing->textSpaceBetween($field1, $field2);

        $printing->line(1);

        $saleOrder->getTotalCouponsByCashier($id_caixa);

        $coupons = 0;

        if ($rowSale = $saleOrder->getResult()) {

            $coupons = $rowSale['total'];
        }

        $printing->text("Cupons Pagos: " . $coupons);

        $config = new Config();

        $config->Read();

        $rowConfig = $config->getResult();

        // '-----------TAXAS DE SERVIÇO / GARÇOM--------------

        if ($rowConfig['fc_waitertip_print'] == 1) {

            $printing->linedashspaced();
            $printing->textCenter("TAXA DE SERVIÇO / GARÇOM");
            $printing->line(1);

            $saleOrder->getTotalServiceByCashier($id_caixa);

            $total_service = 0;

            while ($rowSale = $saleOrder->getResult()) {

                $total_service = Calc::Sum([
                    $total_service,
                    $rowSale['total']
                ]);

                if ($rowSale['total'] > 0) {

                    $printing->textSpaceBetween(mb_substr($rowSale["nome"], 0, 25), "R$ " . number_format($rowSale['total'], 2, ",", "."));
                }
            }

            $printing->line(1);
            $field1 = "R$ " . number_format($total_service, 2, ",", ".");
            $printing->textSpaceBetween("Total", $field1);
        }

        // '-----------Estorno de vendas--------------

        if ($rowConfig['fc_reversesale_print'] == 1) {

            $saleOrderReversed = new SaleOrder();
            $log = new Log();

            $saleOrderReversed->getReversedSales($id_caixa);

            if ($rowSale = $saleOrderReversed->getResult()) {

                $printing->linedashspaced();
                $printing->textCenter("ESTORNO DE VENDAS");

                do {

                    $log->getEstornoVenda($rowSale['id_venda']);

                    if ($rowLog = $log->getResult()) {

                        $logObject = json_decode($rowLog['log']);

                        $entity->Read($logObject->{'id_entidade'});

                        if ($rowLogEntity = $entity->getResult()) {

                            $saleOrder->Read($rowSale['id_venda']);

                            if ($rowLogSale = $saleOrder->getResult()) {

                                $printing->line(1);

                                $total = Calc::Sum([
                                    $rowLogSale['subtotal'],
                                    $rowLogSale['frete'],
                                    $rowLogSale['valor_servico'],
                                    -$rowLogSale['desconto']
                                ]);

                                $printing->textSpaceBetween("Cupom: " . $rowLogSale['id_venda'], "Total: R$ " . number_format($total, 2, ",", "."));

                                if (!empty($rowLogSale['mesa'])) {

                                    $printing->text("Mesa: " . $rowLogSale['mesa']);
                                }

                                $printing->textTruncate("Op.: " . $rowLogEntity['nome']);

                                if (property_exists($logObject, "obs")) {

                                    $printing->text($logObject->{'obs'});
                                }
                            }
                        }
                    }

                } while ($rowSale = $saleOrderReversed->getResult());
            }
        }

        // '-----------Estorno de itens--------------

        if ($rowConfig['fc_reverseitem_print'] == 1) {

            $saleItemReversed = new SaleOrder();
            $log = new Log();

            $saleItemReversed->getReversedItems($id_caixa);

            if ($rowSaleItem = $saleItemReversed->getResult()) {

                $printing->linedashspaced();
                $printing->textCenter("ESTORNO DE ITENS");

                do {

                    $log->getEstornoItem($rowSaleItem['id_venda'], $rowSaleItem['id_vendaitem']);

                    if ($rowLog = $log->getResult()) {

                        $logObject = json_decode($rowLog['log']);

                        $entity->Read($logObject->{'id_entidade'});

                        if ($rowLogEntity = $entity->getResult()) {

                            // $saleItem->Read($logObject->{'id_venda'}, $logObject->{'id_vendaitem'});

                            // if ($rowLogItem = $saleItem->getResult()) {

                                $printing->line(1);

                                $total = Calc::Mult(
                                    $rowSaleItem['qtd'],
                                    $rowSaleItem['preco']
                                );

                                $printing->textTruncate("Op.: " . $rowLogEntity['nome'] . " Cupom: " . $rowSaleItem['id_venda']);
                                $printing->textTruncate("[" . $rowSaleItem["id_produto"] . "] " . $rowSaleItem['produto']);
                                $printing->textSpaceBetween(number_format($rowSaleItem['qtd'], 3, ",", ".") . " " . $rowSaleItem['produtounidade'] . " x R$ " . number_format($rowSaleItem['preco'], 2, ",", "."), "R$ " . number_format($total, 2, ",", "."));

                                // if (empty($rowLogSale['mesa'])) {

                                    // $printing->text("Cupom: " . $rowSaleItem['id_venda']);
                                    // $printing->text("Cupom: " . $rowLogSale['id_venda']);
                                // } else {

                                //     $printing->textSpaceBetween("Cupom: " . $rowLogSale['id_venda'], "Mesa: " . $rowLogSale['mesa']);
                                // }

                                // $printing->textTruncate("Op.: " . $rowLogEntity['nome']);
                            // }
                        }
                    }

                } while ($rowSaleItem = $saleItemReversed->getResult());
            }
        }

        // '-----------REFORCOS--------------
        $cashAdd->ListByCashier($id_caixa);

        if ($rowCashAdd = $cashAdd->getResult()) {

            $printing->linedashspaced();
            $printing->textCenter("REFORÇOS");
            $first_line = true;

            do {

                if (!$first_line) {

                    $printing->line(1);

                } else {

                    $first_line = false;
                }

                $field1 = $rowCashAdd['especie'];
                $field2 = "R$ " . number_format($rowCashAdd['valor'], 2, ",", ".");
                $printing->textSpaceBetween($field1, $field2);

                $field1 = "Oper.: " . $rowCashAdd['nome'];
                $printing->textTruncate($field1);

            } while ($rowCashAdd = $cashAdd->getResult());

        // } else {

        //     $printing->line(1);
        //     $printing->text("Não há reforços");
        }

        // '-----------SANGRIAS--------------
        $cashierDrain->ListByCashier($id_caixa);

        if ($rowCashierDrain = $cashierDrain->getResult()) {

            $printing->linedashspaced();
            $printing->textCenter("SANGRIAS");
            $first_line = true;

            do {
                if (!$first_line) {

                    $printing->line(1);

                } else {

                    $first_line = false;
                }

                $field1 = $rowCashierDrain['especie'];
                $field2 = "R$ " . number_format($rowCashierDrain['valor'], 2, ",", ".");
                $printing->textSpaceBetween($field1, $field2);

                $field1 = "Oper.: " . $rowCashierDrain['nome'];
                $printing->textTruncate($field1);

                $printing->text("Motivo: " . $rowCashierDrain['obs']);

            } while ($rowCashierDrain = $cashierDrain->getResult());

        // } else {

        //     $printing->line(1);
        //     $printing->text("Não há sangrias");
        }

        // '-----------Venda a Prazo--------------

        if ($rowConfig['fc_forwardsale_print'] == 1) {

            $saleOrder->getSalesOnCreditByCashier($id_caixa); //->getCouponsByDateInterval($rowCashier['dataini'], $rowCashier['datafim'], SaleOrder::STATUS_VENDA_PRAZO);

            if ($rowCoupons = $saleOrder->getResult()) {

                $printing->linedashspaced();
                $printing->textCenter("VENDAS A PRAZO");

                $total = 0;

                do {

                    $printing->line(1);

                    $printing->textTruncate($rowCoupons['id_entidade'] . " - " . $rowCoupons['nome']);

                    $total_coupon = Calc::Sum([
                        $rowCoupons['subtotal'],
                        -$rowCoupons['desconto'],
                        $rowCoupons['frete'],
                        $rowCoupons['valor_servico']
                    ]);

                    $total = Calc::Sum([
                        $total,
                        $total_coupon
                    ]);

                    $printing->textRight("R$ " . number_format($total_coupon, 2, ",", "."));

                } while ($rowCoupons = $saleOrder->getResult());

                $printing->line(1);
                $field1 = "R$ " . number_format($total, 2, ",", ".");
                $printing->textSpaceBetween("Total", $field1);
            }
        }

        // '-----------Venda a Prazo Pagos--------------

        if ($rowConfig['fc_forwardsalepaid_print'] == 1) {

            $saleOrder->getCouponsByCasher($rowCashier['id_caixa'], SaleOrder::STATUS_VENDA_PRAZO_PAGA);

            if ($rowCoupons = $saleOrder->getResult()) {

                $printing->linedashspaced();
                $printing->textCenter("VENDAS A PRAZO PAGAS");

                $total = 0;

                do {

                    $printing->line(1);

                    $printing->textTruncate($rowCoupons['id_entidade'] . " - " . $rowCoupons['nome']);

                    $total_coupon = Calc::Sum([
                        $rowCoupons['subtotal'],
                        -$rowCoupons['desconto'],
                        $rowCoupons['frete'],
                        $rowCoupons['valor_servico']
                    ]);

                    $total = Calc::Sum([
                        $total,
                        $total_coupon
                    ]);

                    $printing->textRight("R$ " . number_format($total_coupon, 2, ",", "."));

                } while ($rowCoupons = $saleOrder->getResult());

                $printing->line(1);
                $field1 = "R$ " . number_format($total, 2, ",", ".");
                $printing->textSpaceBetween("Total", $field1);
            }
        }

        // '-----------Pedidos Pagos (Delivery)--------------

        if ($rowConfig['fc_orderpaid_print'] == 1) {

            $saleOrder->getCouponsByCasher($rowCashier['id_caixa'], SaleOrder::STATUS_PEDIDO_PAGO);

            if ($rowCoupons = $saleOrder->getResult()) {

                $printing->linedashspaced();
                $printing->textCenter("PEDIDOS / DELIVERY PAGOS");

                $printing->line(1);

                $total_pay = 0;

                do {

                    $total = Calc::Sum([
                        $rowCoupons['subtotal'],
                        -$rowCoupons['desconto'],
                        $rowCoupons['frete'],
                        $rowCoupons['valor_servico']
                    ]);

                    $total_pay = Calc::Sum([
                        $total_pay,
                        $total
                    ]);

                    $printing->textSpaceBetween("Cupom: " . $rowCoupons['id_venda'], "R$ " . number_format($total, 2, ",", "."));

                } while ($rowCoupons = $saleOrder->getResult());

                $printing->line(1);
                $printing->textSpaceBetween("Total: ", "R$ " . number_format($total_pay, 2, ",", "."));
            }
        }

        // '-----------Controle de reimpressão de pedidos--------------

        if ($rowConfig['fc_reprint_print'] == 1) {

            $saleOrder->getReprints($rowCashier['id_caixa']);

            if ($rowPrint = $saleOrder->getResult()) {

                $printing->linedashspaced();
                $printing->textCenter("CONTROLE DE REIMPRESSÃO DE PEDIDOS");

                $printing->line(1);

                $salePrint = new SaleOrder();

                do {

                    $salePrint->Read($rowPrint["id_venda"]);

                    if ($rowSalePrint = $salePrint->getResult()) {

                        $total = Calc::Sum([
                            $rowSalePrint['subtotal'],
                            -$rowSalePrint['desconto'],
                            $rowSalePrint['frete'],
                            $rowSalePrint['valor_servico']
                        ]);

                        $printing->textSpaceBetween("Cupom: " . $rowSalePrint['id_venda'], "R$ " . number_format($total, 2, ",", "."));

                        $saleStatus = new SaleOrderStatusChange();

                        $saleStatus->getStatus($rowPrint["id_venda"], SaleOrder::STATUS_PEDIDO_IMPRESSO);

                        while ($rowSaleStatus = $saleStatus->getResult()) {

                            $printing->textTruncate(date_format(date_create($rowSaleStatus['data']), 'd/m/Y H:i') . " " . $rowSaleStatus["nome"]);
                        }
                    }

                    if (($rowPrint = $saleOrder->getResult())) {

                        $printing->line(1);
                    }

                } while ($rowPrint);
            }
        }

        // '-----------Produtos Vendidos--------------

        if ($rowConfig['fc_productssold_print'] == 1) {

            if ($rowConfig['fc_productssoldoption_print'] == 0) {

                $saleOrder->getSaleProductByDateInterval(date_format(date_create($rowCashier['dataini']), "Y-m-d 00:00"), $rowCashier['datafim']);

            } else {

                $saleOrder->getSaleProductByDateInterval($rowCashier['dataini'], $rowCashier['datafim']);
            }

            if ($rowSaleProduct = $saleOrder->getResult()) {

                $printing->linedashspaced();
                $printing->textCenter("PRODUTOS VENDIDOS");

                if ($rowConfig['fc_productssoldoption_print'] == 0) {

                    $printing->textCenter(date_format(date_create($rowCashier['dataini']), "d/m/Y 00:00") . " a " . date_format(date_create($rowCashier['datafim']), "d/m/Y H:i"));

                } else {

                    $printing->textCenter(date_format(date_create($rowCashier['dataini']), "d/m/Y H:i") . " a " . date_format(date_create($rowCashier['datafim']), "d/m/Y H:i"));
                }

                $productssold_sector = "";

                do {

                    if ($productssold_sector != $rowSaleProduct['produtosetor']) {

                        $printing->line(1);
                        $printing->textTruncate(":: " . $rowSaleProduct['produtosetor']);
                        $productssold_sector = $rowSaleProduct['produtosetor'];
                    }

                    $printing->textTruncate(number_format($rowSaleProduct['qtd'], 3, ",", ".") . " " . $rowSaleProduct['produtounidade'] . " - " . $rowSaleProduct['produto']);

                } while ($rowSaleProduct = $saleOrder->getResult());
            }
        }

        // '-----------Controle de Mesas (Pagas e em aberto)--------------

        if ($rowConfig['fc_table'] == 1) {

            $saleOrder->getCouponsByCasher($rowCashier['id_caixa'], SaleOrder::STATUS_MESA_PAGA);

            if ($rowCoupons = $saleOrder->getResult()) {

                $printing->linedashspaced();
                $printing->textCenter("MESAS PAGAS");

                $printing->line(1);

                $total_pay = 0;

                do {

                    $total = Calc::Sum([
                        $rowCoupons['subtotal'],
                        -$rowCoupons['desconto'],
                        $rowCoupons['frete'],
                        $rowCoupons['valor_servico']
                    ]);

                    $total_pay = Calc::Sum([
                        $total_pay,
                        $total
                    ]);

                    $printing->textSpaceBetween("Mesa: " . $rowCoupons['mesa'] . " #" . $rowCoupons['id_venda'], "R$ " . number_format($total, 2, ",", "."));

                } while ($rowCoupons = $saleOrder->getResult());

                $printing->line(1);
                $printing->textSpaceBetween("Total: ", "R$ " . number_format($total_pay, 2, ",", "."));
            }

            $saleOrder->getCouponsTableOpenedUntil($rowCashier['datafim']);

            if ($rowCoupons = $saleOrder->getResult()) {

                $printing->linedashspaced();
                $printing->textCenter("MESAS EM ABERTO");

                $printing->line(1);

                do {

                    $total = Calc::Sum([
                        $rowCoupons['subtotal'],
                        -$rowCoupons['desconto'],
                        $rowCoupons['frete'],
                        $rowCoupons['valor_servico']
                    ]);

                    $printing->textSpaceBetween("Mesa: " . $rowCoupons['mesa'] . " #" . $rowCoupons['id_venda'], "R$ " . number_format($total, 2, ",", "."));

                } while ($rowCoupons = $saleOrder->getResult());
            }
        }

        // ------------------------------------------

        $printing->linedashspaced();
        $printing->textCenter("FECHAMENTO CONCLUIDO");
        $printing->textCenter("CAIXA ZERADO");
        $printing->textRepeat("X");
        $printing->textRepeat("X");
        $printing->textRepeat("X");

        if ($return == true) {

            return $printing->getData();

        } else {

            $printing->close();
        }

    } else {

        Notifier::Add("Não há impressora configurada para impressão de fechamento de caixa!", Notifier::NOTIFIER_ERROR);

        Send(null);
    }
}

switch($_POST['action']) {

    case "get_saleoff":

        $id_venda = $_POST['id_venda'];

        $sale = new SaleOrder();

        $discount_percent = $sale->getSaleOff($id_venda);

        $data = [
            "job" => "done",
            "percent" => $discount_percent,
        ];

        Send($data);

        break;

    case "venda_set_entity":

        $id_venda = $_POST['id_venda'];
        $id_entidade = $_POST['id_entidade'];

        $saleOrder = new SaleOrder();

        $saleOrder->Update($id_venda, "id_entidade", $id_entidade);

        $saleOrder->applyFidelityProgram($id_venda);

        Send([]);

        break;

    case "venda_set_servicevalue":

        $id_venda = $_POST['id_venda'];
        $service = $_POST['service'];

        $saleOrder = new SaleOrder();

        $saleOrder->Update($id_venda, "valor_servico", $service);

        Send([]);

        break;

    case "venda_get_venda_prazo":

        $id_entidade = $_POST['id_entidade'];

        $saleOrder = new SaleOrder();

        $saleOrder->getSalesOnCreditByEntity($id_entidade);

        $sales = [];

        while ($row = $saleOrder->getResult()) {

            $row = SaleOrder::FormatFields($row);

            $sales[] = $row;

        }

        Send($sales);

        break;

    case "venda_set_endereco_entrega":

        $id_venda = $_POST['id_venda'];
		$id_endereco = $_POST['id_endereco'];

		$sale = new SaleOrder();
		$saleAddress = new SaleOrderAddress();
		$entityAddress = new EntityAddress();

		$entityAddress->Read($id_endereco);

		if ($row = $entityAddress->getResult()) {

			$saleAddress->Delete($id_venda);

			$saleAddress->Create([
				"id_venda" => $id_venda,
				"nickname" => $row['nickname'],
				"logradouro" => $row['logradouro'],
                "numero" => $row['numero'],
                "complemento" => $row['complemento'],
				"bairro" => $row['bairro'],
				"cidade" => $row['cidade'],
				"uf" => $row['uf'],
				"cep" => $row['cep'],
				"obs" => $row['obs'],
			]);

			$frete = $sale->applyFreight($id_venda);

			Send([]);

		} else {

			Notifier::Add("Erro ao definir endereço para a venda.", Notifier::NOTIFIER_ERROR);

            Send(null);
		}

        break;

    case "venda_remove_endereco_entrega":

        $id_venda = $_POST['id_venda'];

        $sale = new SaleOrder();
        $saleAddress = new SaleOrderAddress();

        $saleAddress->Delete($id_venda);

        $sale->applyFreight($id_venda);

        Send([]);

        break;

    case "saleorder_payment_register":

		// ControlAccess::Check(ControlAccess::CA_PDV);

		$id_venda = $_POST['id_venda'];
		$id_especie = $_POST['id_especie'];
		$valor = floatval($_POST['valor']);
		$valor_recebido = floatval($_POST['valor_recebido']);

		if ($valor_recebido == 0) {

			Notifier::Add("Valor não pode ser zero!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		$saleOrder = new SaleOrder();

		$saleOrder->Read($id_venda);

		if ($rowSale = $saleOrder->getResult()) {

			$totalSale = Calc::Sum([
				$rowSale['subtotal'],
				$rowSale['valor_servico'],
				$rowSale['frete'],
				- $rowSale['desconto']
			]);

			$salePayment = new SaleOrderPay();

			$salePayment->getTotal($id_venda);

			$row = $salePayment->getResult();

			$totalPayment = $row['total'];

			if ($totalPayment >= $totalSale) {

				Notifier::Add("Pedido já pago!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

			// $valor = $valor_recebido;

			// $resta = Calc::Sum([
			// 	$totalSale,
			// 	-$totalPayment
			// ]);

			// if ($valor_recebido > $resta) {

				// Money
				// if ($id_especie == 1) {

				// 	$valor = $resta;

				// } else {

				// 	Notifier::Add("Valor de pagamento não pode ser maior que valor do pedido!", Notifier::NOTIFIER_INFO);
				// 	Send(null);
				// }
			// }

			$credito = null;

			// Entity Credit
			if ($id_especie == 2) {

				$entity = new Entity();

				$entity->Read($rowSale['id_entidade']);

				if ($rowEntity = $entity->getResult()) {

					if ($valor_recebido > $rowEntity['credito']) {

						Notifier::Add("Crédito insuficiente!<br> Crédito atual R$ " . number_format($rowEntity['credito'], 2, ",", "."), Notifier::NOTIFIER_ERROR);
						Send(null);
					}

					$entity->setCredito($rowSale['id_entidade'], -$valor_recebido, "Uso de crédito no pedido " . $rowSale['id_venda'], $GLOBALS["authorized_id_entidade"]);

					$credito = Calc::Sum([
						$rowEntity['credito'],
						-$valor_recebido
					]);

				} else {

					Notifier::Add("Erro ao carregador informação do cliente!", Notifier::NOTIFIER_ERROR);
					Send(null);
				}
			}

			if ($salePayment->Create($id_venda, $id_especie, $valor, $valor_recebido)) {

				Send([]);

			} else {

				Notifier::Add("Erro ao registrar pagamento!", Notifier::NOTIFIER_ERROR);
				Send(null);
			}

		} else {

			Notifier::Add("Erro ao ler dados do pedido!", Notifier::NOTIFIER_ERROR);
			Send(null);
		}

		break;

    case "venda_read":

        $id_venda = $_POST['id_venda'];

        $sale = new SaleOrder();

        $sale->Read($id_venda);

        if ($row = $sale->getResult()) {

            $row = SaleOrder::FormatFields($row);

            Send($row);

        } else {

            Notifier::Add("Venda não encontrada!", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        break;

    case "produto_read":

        $id_produto = $_POST['id_produto'];

        $product = new Product();

        $product->Read($id_produto);

        if ($row = $product->getResult()) {

            // $row = Product::FormatFields($row);

            Send($row);

        } else {

            Notifier::Add("Produto não encontrado!", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        break;

    case "venda_delete":

        $id_venda = $_POST['id_venda'];
        $user = $_POST['user'];
        $obs = $_POST["obs"];

		$sale = new SaleOrder();

		if ($sale->Delete($id_venda, $obs, $user)) {

            Send([]);

        } else {

            Notifier::Add("Erro ao estonar venda!", Notifier::NOTIFIER_ERROR);

            Send(null);
        }
        break;

    case "vendaitem_create":

        $id_venda = $_POST['id_venda'];
        $id_produto = $_POST['id_produto'];
        $qtd = $_POST['qtd'];
        $preco = $_POST['preco'];

        $product = new Product();

        $product->Read($id_produto);

        if ($row = $product->getResult()) {

            $saleItem = new SaleOrderItem();

            if ($id_vendaitem = $saleItem->Create($id_venda, $id_produto, $row['id_produtotipo'], $qtd, $preco, "")) {

                // $saleOrder = new SaleOrder();

                // $saleOrder->ReadOnly($id_venda);

                // if ($rowSale = $saleOrder->getResult()) {

                    // switch ($rowSale['id_vendastatus']) {

                    //     case SaleOrder::STATUS_MESA_EM_ANDAMENTO:
                    //     case SaleOrder::STATUS_MESA_EM_PAGAMENTO:

                            if ($row['id_impressora']) {

                                $table = new Table();

                                $table->ReadFromSale($id_venda);

                                if ($rowTable = $table->getResult()) {

                                    $printing = new Printing($row['id_impressora']);

                                    $printing->initialize();

                                    // Header comanda
                                    $printing->text("Mesa: " . $rowTable['mesa']);
                                    $printing->textTruncate("Garçom: " . $GLOBALS['authorized_nome']);
                                    $printing->text("Data/Hora: " . date("d/m/Y H:i"));
                                    $printing->line(1);
                                    $printing->text("Produtos");
                                    $printing->linedashspaced();

                                    //Body Comanda
                                    $printing->text("$qtd - ". $row['produto']);
                                    $printing->line(1);

                                    // Footer Comanda
                                    $printing->linedashspaced();

                                    $printing->close();
                                }
                            }

                    //         break;
                    // }
                // }

                $data = [
                    "id_vendaitem" => $id_vendaitem,
                ];

                Send($data);

            } else {

                Notifier::Add("Erro ao registrar item de venda.", Notifier::NOTIFIER_ERROR);

                Send(null);
            }

        } else {

            Notifier::Add("Erro ao localizar produto.", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        break;

    case "vendaitem_delete":

        $id_venda = $_POST['id_venda'];
        $id_vendaitem = $_POST['id_vendaitem'];
        $id_entidade = $_POST['user'];

        // $saleOrder = new SaleOrder();

        // $saleOrder->ReadOnly($id_venda);

        // if ($row = $saleOrder->getResult()) {

        //     switch($row["id_vendastatus"]) {

        //         case SaleOrder::STATUS_MESA_EM_ANDAMENTO:

        //             ControlAccess::CheckAuth()
        //         break;

        //         case SaleOrder::STATUS_VENDA_EM_ANDAMENTO:

        //         break;

        //         default:

        //             Notifier::Add("Erro ao estornar item de venda.", Notifier::NOTIFIER_ERROR);

        //             Send(null);
        //     }

        // } else {

        //     Notifier::Add("Erro ao estornar item de venda.", Notifier::NOTIFIER_ERROR);

        //     Send(null);
        // }

        $saleItem = new SaleOrderItem();

        if ($saleItem->Delete($id_venda, $id_vendaitem, $id_entidade)) {

            Send ([]);

        } else {

            Notifier::Add("Erro ao estornar item de venda.", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        break;

    case "venda_prazo":

        $id_venda = $_POST['id_venda'];
        $id_entidade = $_POST['id_entidade'];

        $sale = new SaleOrder();

        if ($sale->VendaPrazo($id_venda, $id_entidade)) {

            Notifier::Add("Venda a prazo registrada com sucesso!", Notifier::NOTIFIER_INFO);

            Send([]);

        } else {

            Notifier::Add("Erro ao colocar venda em pendência!", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        break;

    case "venda_discount_clear":

        $id_venda = $_POST['id_venda'];

        $vendaitem = new SaleOrderItem();

        $vendaitem->DiscountClear($id_venda);

        $data = [
            "job" => "done",
        ];

        Send ($data);
        break;

    case "pdv_print":

        $id_pdv = $_POST['id_pdv'];
        $print = $_POST['print'];

        $print = json_decode($print);

        $pdv = new Pdv();

        $pdv->Read($id_pdv);

        if ($row = $pdv->getResult()) {

            if (!is_null($row['id_impressora'])) {

                $printing = new Printing($row['id_impressora']);

                $printing->initialize();

                foreach ($print as $line) {

                    $printing->text($line);
                }

                $printing->close();

                Send([]);
            }
        }

        Notifier::Add("Ocorreu um erro ao imprimir!", Notifier::NOTIFIER_ERROR);

        Send(null);

        break;

    case "pdv_casher_open":

        $id_pdv = $_POST['id_pdv'];

        $pdv = new Pdv();

        $pdv->Read($id_pdv);

        if ($row = $pdv->getResult()) {

            if ($row['id_impressora'] && $row['gaveteiro'] == 1) {

                $printing = new Printing($row['id_impressora']);

                $printing->initialize();

                $cashDrawer = new CashDrawer();

                $cashDrawer->Read($row['id_gaveteiro']);

                if ($rowCashDrawer = $cashDrawer->getResult()) {

                    $printing->Command($rowCashDrawer['comando']);
                    $printing->finalize();
                }
            }
        }

        Send([]);

        break;

    case "pdv_close":

        $id_caixa = $_POST['id_caixa'];

        $sale = new SaleOrder();

        $sale->setCashierSalesOnCredit($id_caixa);

        $sale->setCashierReversedSales($id_caixa);

            // Notifier::Add("Ocorreu um erro no controle de vendas canceladas!");

        $pdv = new Cashier();

        $pdv->Close($id_caixa);

        PrintTrocoFim($id_caixa);

        PrintPdvClosing($id_caixa);

        Notifier::Add("Caixa Fechado!", Notifier::NOTIFIER_INFO);

        Send([]);

        break;

    case "table_search":

        $desc = $_POST['desc'];

        $table = new Table();

		$table->Search($desc, true);

        $tables = [];

		if ($row = $table->getResult()) {

            do {

                $row = Table::FormatFields($row);

                $tables[] = $row;

            } while ($row = $table->getResult());

            Send($tables);

        } else {

            Notifier::Add("Mesa não encontrada!", Notifier::NOTIFIER_INFO);

            Send([]);
        }

        break;

    case "table_getlist":

        $table = new Table();

        $table->getList(true);

		$tables = [];

		if ($row = $table->getResult()) {

			do {

                $row = Table::FormatFields($row);

				$tables[] = $row;

			} while ($row = $table->getResult());

            Send($tables);

		} else {

            Notifier::Add("Não há mesas cadastradas!", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        break;

    case "saleorder_getlist":

        $sale = new SaleOrder();

        $status_selection = [
            SaleOrder::STATUS_PEDIDO_EFETUADO,
            SaleOrder::STATUS_PEDIDO_IMPRESSO, //DEPRECATED
            SaleOrder::STATUS_PEDIDO_PRODUCAO,
            SaleOrder::STATUS_PEDIDO_ENTREGA
        ];

        $sales = [];

        foreach($status_selection as $status) {

            $sale->getOrderList($status);

            if ($row = $sale->getResult()) {

                do {

                    $row = SaleOrder::FormatFields($row);

                    $sales[] = $row;

                } while ($row = $sale->getResult());
            }
        }

        if (count($sales) > 0) {

            Send($sales);

        } else {

            Notifier::Add("Não há pedidos em aberto!", Notifier::NOTIFIER_INFO);

            Send(null);
        }

        break;

    case "saleorder_search":

        $id_venda = $_POST['id_venda'];

        $sale = new SaleOrder();

        $sale->Read($id_venda);

        $sales = [];

        if ($row = $sale->getResult()) {

            switch($row['id_vendastatus']) {

                case SaleOrder::STATUS_PEDIDO_EFETUADO:
                case SaleOrder::STATUS_PEDIDO_IMPRESSO: //DEPRECATED
                case SaleOrder::STATUS_PEDIDO_PRODUCAO:
                case SaleOrder::STATUS_PEDIDO_ENTREGA:

                    $row = SaleOrder::FormatFields($row);

                    $sales[] = $row;

                    break;
            }

        }

        Send($sales);

        break;

    case "entity_search":

        $desc = Clean::HtmlChar(trim($_POST['desc']));
		$desc = Clean::DuplicateSpace($desc);

		$entity = new Entity();

		if( is_numeric($desc) ) {

			$entity->SearchByCode($desc);

		} else {

			// if (mb_strlen($desc) < 3) {

			// 	Send($tplEntity->getContent($row, "BLOCK_ITEM_SEARCH_NONE"));
			// 	exit();
			// }

			$entity->Search($desc);
		}

		$entity_row = [];

		if ($row = $entity->getResult()) {

			do {

				$row = Entity::FormatFields($row);

				$entity_row[] = $row;

			} while ($row = $entity->getResult());

            Send($entity_row);

		} else {

            Notifier::Add("Nenhum cliente encontrado!", Notifier::NOTIFIER_INFO);

            Send([]);
        }

        break;

    case "print_trocofim":

        $id_caixa = $_POST['id_caixa'];

        PrintTrocoFim($id_caixa);

        break;


    case "pdvreport_closeview":

        $id_caixa = $_POST['id_caixa'];

        $data = PrintPdvClosing($id_caixa, true);

        $cupom = implode("", $data);

        $cupom = str_replace("\n", "<br>", $cupom);
        $cupom = str_replace(" ", "&nbsp;", $cupom);
        // foreach ($data as $line) {

        //     $cupom .= $line;
        // }
        $tplReport = new View("report_sale_total");

        $data = [
            "cupom" => $cupom
        ];

        Send($tplReport->getContent($data, "EXTRA_BLOCK_REPORTSALE_CUPOM"));

        break;

    case "vendapay_load":

        $id_venda = $_POST['id_venda'];
        $id_entidade = $_POST['id_entidade'];

        $salePayment = new SaleOrderPay();

        $salePayment->getList($id_venda);

        $rows = [];

        if ($row = $salePayment->getResult()) {

            do  {

                $row = SaleOrderPay::FormatFields($row);

                $rows[] = $row;

            } while ($row = $salePayment->getResult());

            Send($rows);

        } else {

            Notifier::Add("Error ao carregar pagamento de pedido!", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        break;

    case "vendapay_clear":

        $id_venda = $_POST['id_venda'];

        $salePayment = new SaleOrderPay();

        $salePayment->DeletePaymentSale($id_venda);

        Send([]);

    case "saleorder_print":

        $id_venda = $_POST['id_venda'];
        $id_pdv = $_POST['id_pdv'];

        $pdv = new Pdv();

        $pdv->Read($id_pdv);

        if ($row = $pdv->getResult()) {

            if (!is_null($row['id_impressora'])) {

                SaleOrder::DoPrint($id_venda, $row['id_impressora'], false);
            }

        } else {

            Notifier::Add("Configuração de impressora de PDV não encontrada!", Notifier::NOTIFIER_ERROR);
        }

        Send([]);

        break;

    case "get_empresa":

        $company = new Company();

        $company->Read();

        if ($row = $company->getResult()) {

            $row = Company::FormatFields($row);

            Send($row);

        } else {

            Notifier::Add("Erro ao carregar dados da empresa!", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        break;

    case "saleorder_changestatus":

        $id_venda = $_POST['id_venda'];
        $id_vendastatus = $_POST['id_vendastatus'];

        $sale = new SaleOrder();

        $sale->ChangeStatus($id_venda, $id_vendastatus);

        Send([]);

        break;

    case "saleorder_create":

        $id_vendastatus = $_POST['id_vendastatus'];

        $sale = new SaleOrder();

        $data = [
            "id_entidade" => null,
            "frete" => 0,
            "id_vendastatus" => $id_vendastatus
        ];

        $id_venda = $sale->Create($data);

        Send(["id_venda" => $id_venda]);

        break;

    case "saleorder_mesa_create":

        $id_mesa = $_POST['id_mesa'];
        $mesa = $_POST['mesa'];

        $saleorder = new SaleOrder();
        $table = new Table();

        $id_venda = $saleorder->Create([
            "frete" => 0,
            "id_entidade" => null,
            "id_vendastatus" => SaleOrder::STATUS_MESA_EM_ANDAMENTO,
            "mesa" => $mesa,
        ]);

        $table->Book($id_mesa, $id_venda, $GLOBALS['authorized_id_entidade']);

        Send(["id_venda" => $id_venda]);

        break;

    case "table_transf":

        $id_mesa_from = $_POST['id_mesa_from'];
		$versao_from = $_POST['versao_from'];
		$id_mesa_to = $_POST['id_mesa_to'];
		$versao_to = $_POST['versao_to'];
        $id_entidade = $_POST['user'];

        $collaborator = new Collaborator();

		$collaborator->Read($id_entidade);

		if ($row = $collaborator->getResult()) {

            $access = json_decode($row['acesso']);

            if ($access[ControlAccess::CA_TRANSFERENCIA_MESA] == 0) {

                ControlAccess::Unauthorized();
            }

        } else {

            ControlAccess::Unauthorized();
        }

        $table = new Table();

        $table->Transfer($id_mesa_from, $versao_from, $id_mesa_to, $versao_to, $id_entidade);

        break;

    case "image_upload":

        $target = $_POST['target'];

        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 2048000) {

            Notifier::Add("Arquivo muito grande. máximo 2MB", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);

        if($check === false) {

            Notifier::Add("Arquivo não é uma imagem.", Notifier::NOTIFIER_ERROR);

            Send(null);
        }

        switch ($check[2]) {

            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
            case IMAGETYPE_PNG:
            case IMAGETYPE_BMP:

                break;

            default:

                Notifier::Add("Somente imagens no formato JPG, JPEG, BMP & PNG são permitidos.", Notifier::NOTIFIER_ERROR);

                Send(null);
                break;
        }

        switch($target) {

            case "digitalmenu-header":

                ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

                $target_dir = "assets/";
                $target_file = "assets/digitalmenu_header.png";

                // Checks image size
                if ($check[0] > 480 || $check[1] > 180) {

                    Notifier::Add("Dimensão máxima permitida: 480x180px<br>Imagem: " . $check[0] . "x" . $check[1] . "px", Notifier::NOTIFIER_ERROR);

                    Send(null);
                }

                // Upload image
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

                    Notifier::Add("Imagem alterada com sucesso!", Notifier::NOTIFIER_DONE);

                    Send([]);

                } else {

                    Notifier::Add("Problema ao carregar arquivo " . $_FILES["fileToUpload"]["name"] . "<br> possível problema de permissão.", Notifier::NOTIFIER_ERROR);

                    Send(null);
                }

                break;

            case "digitalmenu-logo":

                ControlAccess::Check(ControlAccess::CA_SERVIDOR_CONFIG);

                $target_dir = "assets/";
                $target_file = "assets/digitalmenu_logo.png";

                // Checks image size
                if ($check[0] != 128 || $check[1] != 128) {

                    Notifier::Add("Imagem deve ter tamanho: 128x128px<br>Imagem: " . $check[0] . "x" . $check[1] . "px", Notifier::NOTIFIER_ERROR);
                    Send(null);
                }

                // Upload image
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

                    Notifier::Add("Imagem alterada com sucesso!", Notifier::NOTIFIER_DONE);
                    Send([]);

                } else {

                    Notifier::Add("Problema ao carregar arquivo " . $_FILES["fileToUpload"]["name"] . "<br> possível problema de permissão.", Notifier::NOTIFIER_ERROR);
                    Send(null);
                }

                break;

            case "produto":

                ControlAccess::Check(ControlAccess::CA_SERVIDOR_PRODUTO);

		        $target_dir = "pic/";
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

                // Checks image size
                if ($check[0] > 320 || $check[1] > 240) {

                    Notifier::Add("Dimensão máxima permitida: 320x240px<br>Imagem: " . $check[0] . "x" . $check[1] . "px", Notifier::NOTIFIER_ERROR);
                    Send(null);
                }

                if (file_exists($target_file)) {

                    Notifier::Add("Já existe um arquivo com o mesmo nome: " . $_FILES["fileToUpload"]["name"], Notifier::NOTIFIER_ERROR);
                    Send(null);
                }

                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

                    Notifier::Add("Imagem " . $_FILES["fileToUpload"]["name"] . " carregada com sucesso!", Notifier::NOTIFIER_DONE);
                    Send(basename($_FILES["fileToUpload"]["name"]));

                } else {

                    Notifier::Add("Problema ao carregar arquivo " . $_FILES["fileToUpload"]["name"] . "<br> possível problema de permissão.", Notifier::NOTIFIER_ERROR);
                    Send(null);
                }

                break;

            default:

                Notifier::Add("Destino da imagem não informado!", Notifier::NOTIFIER_ERROR);
                Send(null);

                break;
        }

    break;

    case "check_deliveryminimo":

        $id_venda = $_POST["id_venda"];

        $freight = new Freight();

        $freight->Read();

        if (!$rowFreight = $freight->getResult()) {

            Notifier::Add("Erro ao carregar dados de frete.", Notifier::NOTIFIER_ERROR);
            Send(null);
        }

        $saleOrderAddress = new SaleOrderAddress();

        $saleOrderAddress->Read($id_venda);

        $ret = true;
        $message = "";

        if ($row = $saleOrderAddress->getResult()) {

            $sale = new SaleOrder();

            $sale->Read($id_venda);

            if ($row = $sale->getResult()) {

                if ($rowFreight["deliveryminimo"] == 1) {

                    $subtotal = Calc::Sum([
                        $row['subtotal'],
                        - $row['desconto'],
                    ]);

                    if ($subtotal < $rowFreight["deliveryminimo_valor"]) {

                        $ret = false;
                        $rowFreight = Freight::FormatFields($rowFreight);
                        $message = "Pedido não atingiu valor mínimo de R$ " . $rowFreight["deliveryminimo_valor_formatted"] . " para Delivery!";
                    }
                }
            }
        }

        $data = [
            "delivery" => $ret,
            "message" => $message
        ];

        Send($data);

    break;

    case "entity_address_new":

        $id_entidade = $_POST["id_entidade"];
        $nickname = $_POST["nickname"];
        $logradouro = $_POST["logradouro"];
        $numero = $_POST["numero"];
        $complemento = $_POST["complemento"];
        $bairro = $_POST["bairro"];
        $cidade = $_POST["cidade"];
        $uf = $_POST["uf"];
        $cep = $_POST["cep"];
        $obs = $_POST["obs"];

        if ($numero == "") {

            $numero = null;
        }

        $entityAddress = new EntityAddress();

        $id_endereco = $entityAddress->Create($id_entidade, $nickname, $logradouro, $numero, $complemento, $bairro, $cidade, $uf, $cep, $obs);

        if ($id_endereco) {

            Send(["id_endereco" => $id_endereco]);

        } else {

            Notifier::Add("Ocorreu um erro ao cadastrar endereço.", Notifier::NOTIFIER_ERROR);
            Send(null);
        }
    break;

    default:

        Notifier::Add("Comando desconhecido!", Notifier::NOTIFIER_ERROR);
        Send(null);

        break;
}