<?php

namespace database;

class ControlAccess extends Connection {

	const CA_SERVIDOR = 0;
	const CA_SERVIDOR_PRODUTO = 1;
	const CA_SERVIDOR_PRODUTO_SETOR = 2;
	const CA_SERVIDOR_CLIENTE = 3;
	const CA_SERVIDOR_COLABORADOR = 4;
	const CA_SERVIDOR_FORNECEDOR = 5;
	const CA_SERVIDOR_ORDEM_COMPRA = 6;
	const CA_PDV = 7;
	const CA_PDV_SANGRIA = 8;
	const CA_PDV_CANCELA_ITEM = 9;
	const CA_PDV_CANCELA_VENDA = 10;
	const CA_SERVIDOR_PRODUTO_PRECO = 11;
	const CA_SERVIDOR_ORDEM_COMPRA_LISTA = 12;
	const CA_SERVIDOR_CONTAS_A_PAGAR = 13;
	const CA_SERVIDOR_EMISSAO_RECIBO = 14;
	const CA_SERVIDOR_ORDEM_VENDA = 15;
	const CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO = 16;
	const CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO = 17;
	const CA_CLIENTE_LIMITE = 18;
	const CA_SERVIDOR_RELATORIO = 19;
	const CA_PDV_DESCONTO = 20;
	const CA_SERVIDOR_CONFIG = 21;
	const CA_SERVIDOR_ORDEM_VENDA_FRETE = 22;
	const CA_SERVIDOR_CONTAS_A_RECEBER = 23;
	const CA_CLIENTE_CREDITO = 24;
	const CA_PDV_REFORCO = 25;
	const CA_WAITER = 26;
	const CA_PRODUTO_ESTOQUE_ADD = 27;
	const CA_PRODUTO_ESTOQUE_DEL = 28;
	const CA_VENDA_PRAZO_SEM_LIMITE = 29;
	const CA_TRANSFERENCIA_MESA = 30;
	const CA_VENDA_PRAZO_EDITAR = 31;
	const CA_ESTOQUE_SECUNDARIO_ADD = 32;
	const CA_ESTOQUE_SECUNDARIO_DEL = 33;
	const CA_ORDEM_VENDA_EDITAR = 34;
	const CA_MESA_ITEM_ESTORNO = 35;
	const CA_MAX = 35;

	static function Unauthorized() {

		// header('WWW-Authenticate: Basic realm="My Realm"');
		header('HTTP/1.0 401 Unauthorized');
		exit();
	}

	static function Check($access_type, $ret = false) {

		if ($GLOBALS['authorized']) {

			$collaborator = new Collaborator();

			$collaborator->Read($GLOBALS['authorized_payload']->id);

			if ($row = $collaborator->getResult()) {

				$access = json_decode($row['acesso']);

				if ($access[$access_type] == 1) {

					return true;
				}
			}
		}

		if ($ret == true) {

			return false;

		} else {

			self::Unauthorized();
		}
	}

	static function CheckAuth($id_entidade, $pass, $access_type) {

		if (trim($id_entidade)=='' || trim($pass=='') ) {

			self::Unauthorized();
		}

		$collaborator = new Collaborator();

		$collaborator->Read($id_entidade);

		if ($row = $collaborator->getResult()) {

			if (password_verify($pass, $row['hash'])) {

				$access = json_decode($row['acesso']);

				if ($access[$access_type] == 0) {

					self::Unauthorized();
				}

			} else {

				self::Unauthorized();
			}

		} else {

			self::Unauthorized();
		}
	}

	static function Login($id_entidade, $pass, $access_type, $register_session = true) {

		if (trim($id_entidade)=='' || trim($pass=='')) {

			self::Unauthorized();

		} else {

			$data = [$id_entidade]; //, $pass];

			$collaborator = new Collaborator();

			$collaborator->Read($id_entidade);

			if ($row = $collaborator->getResult()) {

				if (password_verify($pass, $row['hash'])) {

					$access = json_decode($row['acesso']);

					if ($access[$access_type] == 0) {

						self::Unauthorized();
					}

					$session = password_hash(rand(), PASSWORD_BCRYPT);

					array_unshift($data, $session);

					// For compatibility with PDV VB6
					if ($register_session == true) {

						$collaborator->RegistrySession($data);
					}

					//Creates JWT token and send to client.
					//JWT
					$header = [
						'alg' => 'HS256',
						'typ' => 'JWT'
					];

					$header = json_encode($header);
					$header = base64_encode($header);

					$payload = [
						'id' => $id_entidade,
						'session' => $session,
					];

					$payload = json_encode($payload);
					$payload = base64_encode($payload);

					$signature = hash_hmac('sha256',"$header.$payload",'minha-senha',true);
					$signature = base64_encode($signature);

					$GLOBALS['authorized_id_entidade'] = $row['id_entidade'];
                    $GLOBALS['authorized_nome'] = $row['nome'];
                    $GLOBALS['authorized'] = true;

					header("Authorization: x-auth-token $header.$payload.$signature");

				} else {

					self::Unauthorized(); //invalid password
				}
			} else {

				self::Unauthorized(); //user not found
			}
		}

		return true;
	}
}