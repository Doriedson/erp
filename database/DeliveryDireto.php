<?php

namespace database;
use Exception;

class DeliveryDireto extends Connection {

	const GET = "GET";
	const POST = "POST";
	const PUT = "PUT";
	const DEL = "DEL";

	public function Read() {

		$this->data = [];

		$this->query = "SELECT * FROM tab_integracaodeliverydireto";

		parent::Execute();
	}

    public function Update(array $data) {

		$field = $data['field'];
		
		$this->data = [
			"value" => $data['value'],
		];

		$this->query = "UPDATE tab_integracaodeliverydireto set $field = :value";

		parent::Execute();
	}

	public function ToggleAtivo() {

		$this->data = [];

		$this->query = "UPDATE tab_integracaodeliverydireto 
						SET ativo = not ativo";

		parent::Execute();
	}

	public function setToken($token) {

		$this->data = [
			"token" => $token
		];

		$this->query = "UPDATE tab_integracaodeliverydireto 
						SET token = :token";

		parent::Execute();
	}

	public static function Authenticate() {

		$ret = false;

		$dd = new DeliveryDireto();

		$dd->Read();

		if ($row = $dd->getResult()) {

			$username = $row['usuario'];
			$password = $row['senha'];

			$client_id = $row['client_id'];
			$client_secret = $row['client_secret'];

			$store_id = $row['store_id'];

			$ch = curl_init();

			$url = "https://deliverydireto.com.br/admin-api/token";

			$headr = array();
			// $headr[] = 'Content-length: 0';
			$headr[] = 'Content-type: application/json';
			$headr[] = 'X-DeliveryDireto-ID: ' . $store_id;
			$headr[] = 'X-DeliveryDireto-Client-Id: ' . $client_id;
			
			// if($logged) {
				$post = "{
					\"grant_type\": \"password\",
					\"client_id\": \"$client_id\",
					\"client_secret\": \"$client_secret\",
					\"username\": \"$username\",
					\"password\": \"$password\"
				}";
				
			// } else {

			// 	$post = "{
			// 		\"grant_type\": \"client_credentials\",
			// 		\"client_id\": \"$client_id\",
			// 		\"client_secret\": \"$client_secret\"
			// 	}";       
			// }

			// Send($post); exit();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::POST);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

			$response = curl_exec($ch);

			try {

				$resp = json_decode($response);

			} catch (Exception $e) {

				Notifier::Add("Exception: " . $e->getMessage());
				return false;
			}
			// Notifier::Add("resp: " . $resp);

			if($resp != null && property_exists($resp, "access_token")) {

				$dd->setToken($resp->access_token);
Notifier::Add("Autenticado com sucesso.");
				$ret = true;

			} else {
			
				Notifier::Add("Erro: " . $response);
			}
		}
		
		return $ret;
	}

	public function getAddressFee() {

		$url = "https://deliverydireto.com.br/admin-api/v1/delivery-areas/address/check";

		$post = "{
			\"street\": \"Rua Manoel Cláudio de Oliveira\",
			\"number\": \"484\",
			\"zipcode\": \"07911-278\",
			\"neighborhood\": \"Vila Capua\",
			\"city\": \"Francisco Morato\",
			\"state\": \"SP\",
			\"complement\": \"\",
			\"reference_point\": \"\"
		}";

		$ret = self::Send($url, $post, self::PUT);

		return $ret;
	}

	public function CalculateFee($id_address) {

		// $url = "https://deliverydireto.com.br/store-api/v1/stores/delivery-fees/{addressId}/calculate";
		$url = "https://deliverydireto.com.br/store-api/v1/stores/delivery-fees/$id_address/calculate";

		$post = "{
			\"subtotal\": {
			\"value\": 40,
			\"currency\": \"BRL\"
			},
			\"scheduling\": 
		}";

		$ret = self::Send($url, $post, self::POST);

Notifier::Add("CalculateFee");
Notifier::Add($ret);

		return $ret;
	}

	public function getOrders() {

		$url = "https://deliverydireto.com.br/store-api/v1/customers/me/orders";

		$post = "";

		$ret = self::Send($url, $post, self::GET);

Notifier::Add("getOrders");
Notifier::Add($ret);

		return $ret;
	}

	public static function Send($url, $post_fields, $request, $recursive = false) {

		$dd = new DeliveryDireto();

		$dd->Read();

		if ($row = $dd->getResult()) {

			$ch = curl_init();

			$headr = array();

			$headr[] = 'Content-type: application/json';
			$headr[] = 'X-DeliveryDireto-ID: ' . $row["store_id"];
			$headr[] = 'X-DeliveryDireto-Client-Id: ' . $row["client_id"];
			$headr[] = "Authorization: Bearer " . $row["token"];

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

			$response = curl_exec($ch);

			$err = curl_error($ch);

			curl_close($ch);

			if ($err) {
				
				Notifier::Add("Error: " . $err);
				return null;
			}

Notifier::Add($response);
			try {

				if ($resp = json_decode($response)) {

					if (property_exists($resp, 'error')) {

						if ($resp->error == "access_denied" && $recursive == false) {

							if (self::Authenticate() == true) {
Notifier::Add("access denied, authenticating...");
								return self::Send($url, $post_fields, $request, true);
							}
						}

					} else if (property_exists($resp, 'status')) {

						if ($resp->status == "success") {

							return $resp;

						} else if ($resp->status == "error") {

							Notifier::Add($resp);
							return null;
						}
					}
				
Notifier::Add($resp);
				}

			} catch (Exception $e) {

				Notifier::Add("Exception: " . $e->getMessage());
			}

		} else {

			Notifier::Add("Erro ao carregar dados de autenticação do Delivery Direto!");
		}

		return null;
	}

	public static function FormatFields($row) {

		$row['senha'] = str_repeat('*', mb_strlen($row['senha']));

		if ($row['ativo'] == 1) {

			$row['ativo'] = "checked";

		} else {

			$row['ativo'] = "";
		}

		return $row;
	}
}


// | store_id                             | client_id                            | client_secret                        | usuario            | senha        | ativo | token |

// | -7YCfomzFceXcjA6kA4hCkcLpWl_EwtdENLe | 7f1dcd0e-85f8-48b7-b762-5e54a01822f8 | OWmZxQ982yZr7VvP0RmBvKelsTBcPPUYA2h6 | 4ptaQOi1@apidd.com | B3KXpa4o_KD9 |     0 |       |

