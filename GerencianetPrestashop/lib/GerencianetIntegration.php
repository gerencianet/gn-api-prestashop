<?php

/**
 * Gerencianet Integration Class.
 */

include_once('gerencianet/autoload.php');

use Contrib\Bundle\CoverallsV1Bundle\Config\Configuration;
use Gerencianet\Exception\GerencianetException;
use Gerencianet\Gerencianet;



class GerencianetIntegration
{

	public $client_id_production;
	public $client_secret_production;
	public $client_id_development;
	public $client_secret_development;
	public $sandbox;
	public $payee_code;



	public function __construct($clientIdProduction, $clientSecretProduction, $clientIdDevelopment, $clientSecretDevelopment, $sandbox, $payeeCode)
	{
		$this->client_id_production = $clientIdProduction;
		$this->client_secret_production = $clientSecretProduction;
		$this->client_id_development = $clientIdDevelopment;
		$this->client_secret_development = $clientSecretDevelopment;
		$this->sandbox = $sandbox;
		$this->payee_code = $payeeCode;
	}

	public function validate_credentials($client_id, $client_secret, $mode)
	{

		if ($mode == "production") {
			$sandbox = false;
		} else {
			$sandbox = true;
		}

		$options = array(
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'sandbox'       => $sandbox
		);

		$params = array('total' => 1000, 'brand' => 'visa');

		try {
			$api = new Gerencianet($options);
			$installments = $api->getInstallments($params, array());

			return 'true';
		} catch (GerencianetException $e) {
			return 'false';
		} catch (Exception $e) {
			return 'false';
		}
	}

	public function get_gn_api_credentials()
	{

		$isSandbox = ($this->sandbox == "yes");
		$gn_credentials_options = array(
			'client_id' => $isSandbox ? $this->client_id_development : $this->client_id_production,
			'client_secret' => $isSandbox ? $this->client_secret_development : $this->client_secret_production,
			'sandbox' => $isSandbox
		);

		return $gn_credentials_options;
	}

	public function get_gn_api_credentialsPix($options)
	{

		$isSandbox = ($options['sandbox']);
		$gn_credentials_options = array(
			'client_id' => $isSandbox ? $options['client_id_development'] : $options['client_id_production'],
			'client_secret' => $isSandbox ? $options['client_secret_development'] : $options['client_secret_production'],
			'sandbox' => $isSandbox,
			'pix_cert' => $options['pix_cert']
		);

		return $gn_credentials_options;
	}


	public function get_gn_script()
	{

		if ($this->sandbox == "yes") {
			return html_entity_decode("<script type='text/javascript'>var s=document.createElement('script');s.type='text/javascript';var v=parseInt(Math.random()*1000000);s.src='https://sandbox.gerencianet.com.br/v1/cdn/" . $this->payee_code . "/'+v;s.async=false;s.id='" . $this->payee_code . "';if(!document.getElementById('" . $this->payee_code . "')){document.getElementsByTagName('head')[0].appendChild(s);};&#36;gn={validForm:true,processed:false,done:{},ready:function(fn){&#36;gn.done=fn;}};</script>");
		} else {
			return html_entity_decode("<script type='text/javascript'>var s=document.createElement('script');s.type='text/javascript';var v=parseInt(Math.random()*1000000);s.src='https://api.gerencianet.com.br/v1/cdn/" . $this->payee_code . "/'+v;s.async=false;s.id='" . $this->payee_code . "';if(!document.getElementById('" . $this->payee_code . "')){document.getElementsByTagName('head')[0].appendChild(s);};&#36;gn={validForm:true,processed:false,done:{},ready:function(fn){&#36;gn.done=fn;}};</script>");
		}
	}

	public function max_installments($total)
	{

		$options = GerencianetIntegration::get_gn_api_credentials();
		$params  = array('total' => $total, 'brand' => 'visa');

		try {
			$api              = new Gerencianet($options);
			$installments     = array();
			$installments     = $api->getInstallments($params, array());
			$max_installments = end($installments['data']['installments'])['installment'] . "x de " . GerencianetIntegration::formatCurrencyBRL(end($installments['data']['installments'])['value']);

			return $max_installments;
		} catch (GerencianetException $e) {
			return null;
		} catch (Exception $e) {
			return null;
		}
	}

	public function get_installments($total, $brand)
	{

		$options = GerencianetIntegration::get_gn_api_credentials();

		$params = array('total' => $total, 'brand' => $brand);

		try {
			$api          = new Gerencianet($options);
			$installments = $api->getInstallments($params, array());

			return GerencianetIntegration::result_api($installments, true);
		} catch (GerencianetException $e) {
			$errorResponse = array(
				"code"    => $e->getCode(),
				"error"   => $e->error,
				"message" => $e->errorDescription,
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		} catch (Exception $e) {
			$errorResponse = array(
				"code"    => 0,
				"message" => $e->getMessage(),
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		}
	}

	public function create_charge($order_id, $items, $shipping, $notification_url)
	{

		$options = GerencianetIntegration::get_gn_api_credentials();

		$metadata = array(
			'custom_id' => strval($order_id),
			'notification_url' => $notification_url
		);

		if ($shipping) {
			$body = array(
				'items'     => $items,
				'shippings' => $shipping,
				'metadata'  => $metadata
			);
		} else {
			$body = array(
				'items'    => $items,
				'metadata' => $metadata
			);
		}

		try {
			$api    = new Gerencianet($options);
			$charge = $api->createCharge(array(), $body);

			return GerencianetIntegration::result_api($charge, true);
		} catch (GerencianetException $e) {
			$errorResponse = array(
				"code"    => $e->getCode(),
				"error"   => $e->error,
				"message" => $e->errorDescription,
			);


			return GerencianetIntegration::result_api($errorResponse, false);
		} catch (Exception $e) {
			$errorResponse = array(
				"message" => $e->getMessage(),
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		}
	}

	public function pay_billet($charge_id, $expirationDate, $customer, $discount = false, $configurations  = false)
	{

		$options = GerencianetIntegration::get_gn_api_credentials();
		$params  = array('id' => $charge_id);

		if ($discount) {
			$banking_billet = array(
				'expire_at' => $expirationDate,
				'customer'  => $customer,
				'discount'  => $discount
			);
		} else {
			$banking_billet = array(
				'expire_at' => $expirationDate,
				'customer'  => $customer
			);
		}

		if ($configurations) {
			$banking_billet['configurations'] = $configurations;
		}

		$body = array(
			'payment' => array(
				'banking_billet' => $banking_billet
			)
		);

		try {
			$api    = new Gerencianet($options);
			$charge = $api->payCharge($params, $body);

			return GerencianetIntegration::result_api($charge, true);
		} catch (GerencianetException $e) {
			$errorResponse = array(
				"code"    => $e->getCode(),
				"error"   => $e->error,
				"message" => $e->errorDescription,
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		} catch (Exception $e) {
			$errorResponse = array(
				"message" => $e->getMessage(),
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		}
	}

	public function cancel_charge($charge_id)
	{

		$options = GerencianetIntegration::get_gn_api_credentials();
		$params  = array('id' => $charge_id);

		try {
			$api    = new Gerencianet($options);
			$charge = $api->cancelCharge($params, []);

			return GerencianetIntegration::result_api($charge, true);
		} catch (GerencianetException $e) {
			$errorResponse = array(
				"code"    => $e->getCode(),
				"error"   => $e->error,
				"message" => $e->errorDescription,
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		} catch (Exception $e) {
			$errorResponse = array(
				"message" => $e->getMessage(),
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		}
	}

	public function pay_pix($options, $body)
	{

		$clients =  GerencianetIntegration::get_gn_api_credentialsPix($options);
		$response = false;
		try {
			$api = new Gerencianet($clients);
			$data = $api->pixCreateImmediateCharge([], $body);
			$response = true;
			return GerencianetIntegration::result_api($data, true);
		} catch (GerencianetException $e) {
			$data = array(
				"code" => $e->getCode(),
				"error" => $e->error,
				"message" => $e->errorDescription,
			);
			return GerencianetIntegration::result_api($data, false);
		} catch (Exception $e) {
			$data = array("message" => $e->getMessage());
			return GerencianetIntegration::result_api($data, false);
		}

		return GerencianetIntegration::result_api($data, $response);
	}

	public function generate_qrcode_txid($options, $txid)
	{



		$clients =  GerencianetIntegration::get_gn_api_credentialsPix($options);
		$response = false;
		$params = ['txid' => $txid];
		try {
			$api = new Gerencianet($clients);
			$data = $api->pixDetailCharge($params);
			$response = true;




			if ($response) {
				$paramsQr = ['id' => $data['loc']['id']];


				try {
					$data = $api->pixGenerateQRCode($paramsQr, []);

					$response = true;
				} catch (GerencianetException $e) {
					$data = array(
						"code" => $e->getCode(),
						"error" => $e->error,
						"message" => $e->errorDescription,
					);
				} catch (Exception $e) {
					$data = array("message" => $e->getMessage());
				}
			}
		} catch (GerencianetException $e) {
			$data = array(
				"code" => $e->getCode(),
				"error" => $e->error,
				"message" => $e->errorDescription,
			);
		} catch (Exception $e) {
			$data = array("message" => $e->getMessage());
		}
		return GerencianetIntegration::result_api($data, $response);
	}

	public function generate_qrcode($options, $locationId)
	{

		$clients =  GerencianetIntegration::get_gn_api_credentialsPix($options);
		$response = false;
		$params = ['id' => $locationId];

		try {
			$api = new Gerencianet($clients);
			$data = $api->pixGenerateQRCode($params, []);
			$response = true;
		} catch (GerencianetException $e) {
			$data = array(
				"code" => $e->getCode(),
				"error" => $e->error,
				"message" => $e->errorDescription,
			);
		} catch (Exception $e) {
			$data = array("message" => $e->getMessage());
		}

		return GerencianetIntegration::result_api($data, $response);
	}

	public function update_meta($charge_id, $reference, $notification_url)
	{

		$options = GerencianetIntegration::get_gn_api_credentials();
		$params = array('id' => $charge_id);

		$body = array(
			'custom_id' => (string)$reference,
			'notification_url' => (string)$notification_url,
		);

		try {
			$api = new Gerencianet($options);
			$subscription = $api->updateChargeMetadata($params, $body);

			return GerencianetIntegration::result_api($subscription, true);
		} catch (GerencianetException $e) {
			$errorResponse = array(
				"code" => $e->code,
				"error" => $e->error,
				"message" => $e->errorDescription,
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		} catch (Exception $e) {
			$errorResponse = array(
				"message" => $e->getMessage(),
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		}
	}

	public function update_webhook($options, $pix_key, $skip_mtls, $url)
	{
		$response = false;
		$params = ['chave' => $pix_key];
		$options['headers'] = ['x-skip-mtls-checking' => $skip_mtls];
		$options['debug'] = true;
		try {
			$api = new Gerencianet($options);
			$data = $api->pixConfigWebhook($params, ['webhookUrl' => $url]);

			$response = true;
		} catch (GerencianetException $e) {
			$data = array(
				"code" => $e->getCode(),
				"error" => $e->error,
				"message" => $e->errorDescription,
			);
		} catch (Exception $e) {
			$data = array("message" => $e->getMessage());
		}

		return GerencianetIntegration::result_api($data, $response);
	}

	public function pay_card($charge_id, $paymentTokenCard, $installments, $billingAddress, $customer, $discount)
	{

		$options = GerencianetIntegration::get_gn_api_credentials();
		$params  = array('id' => $charge_id);

		$paymentToken = $paymentTokenCard;

		if ($discount > 0) {
			$body = array(
				'payment' => array(
					'credit_card' => array(
						'installments'    => $installments,
						'billing_address' => $billingAddress,
						'payment_token'   => $paymentToken,
						'customer'        => $customer,
						'discount'        => $discount
					)
				)
			);
		} else {
			$body = array(
				'payment' => array(
					'credit_card' => array(
						'installments'    => $installments,
						'billing_address' => $billingAddress,
						'payment_token'   => $paymentToken,
						'customer'        => $customer
					)
				)
			);
		}

		try {
			$api    = new Gerencianet($options);
			$charge = $api->payCharge($params, $body);

			return GerencianetIntegration::result_api($charge, true);
		} catch (GerencianetException $e) {
			$errorResponse = array(
				"code"    => $e->getCode(),
				"error"   => $e->error,
				"message" => $e->errorDescription,
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		} catch (Exception $e) {
			$errorResponse = array(
				"message" => $e->getMessage(),
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		}
	}

	public function notificationCheck($notificationToken)
	{

		$options = GerencianetIntegration::get_gn_api_credentials();

		$params = array(
			'token' => $notificationToken
		);

		try {
			$api          = new Gerencianet($options);
			$notification = $api->getNotification($params, array());

			return GerencianetIntegration::result_api($notification, true);
		} catch (GerencianetException $e) {
			$errorResponse = array(
				"message" => "Error retrieving notification: " . $notificationToken,
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		} catch (Exception $e) {
			$errorResponse = array(
				"message" => "Error retrieving notification: " . $notificationToken,
			);

			return GerencianetIntegration::result_api($errorResponse, false);
		}
	}

	public function result_api($result, $success)
	{

		if ($success) {
			return json_encode($result);
		} else {
			if (isset($result['message']['property'])) {
				$property = explode("/", $result['message']['property']);
				$propertyName = end($property);
			} else {
				$propertyName = "";
			}

			if (isset($result['code'])) {
				if (isset($result['message']) && $propertyName == "") {
					$messageShow = $this->getErrorMessage(intval($result['code']), $result['message']);
				} else {
					$messageShow = $this->getErrorMessage(intval($result['code']), $propertyName);
				}
			} else {
				if (isset($result['message'])) {
					$messageShow = $result['message'];
				} else {
					$messageShow = $this->getErrorMessage(1, $propertyName);
				}
			}

			$errorResponse = array(
				"code" => $result['code'],
				"message" => $messageShow
			);
			return json_encode($errorResponse);
		}
	}

	public function getErrorMessage($error_code, $property)
	{
		$messageErrorDefault = 'Ocorreu um erro ao tentar realizar a sua requisição. Entre em contato com o proprietário da loja.';
		switch ($error_code) {
			case 3500000:
				$message = 'Erro interno do servidor.';
				break;
			case 3500001:
				$message = $messageErrorDefault;
				break;
			case 3500002:
				$message = $messageErrorDefault;
				break;
			case 3500007:
				$message = 'O tipo de pagamento informado não está disponível.';
				break;
			case 3500008:
				$message = 'Requisição não autorizada.';
				break;
			case 3500010:
				$message = $messageErrorDefault;
				break;
			case 3500021:
				$message = 'Não é permitido parcelamento para assinaturas.';
				break;
			case 3500030:
				$message = 'Esta transação já possui uma forma de pagamento definida.';
				break;
			case 3500034:
				if ($property == "payment_token") {
					$message = 'Os dados do cartão não foram validados. Por favor, digite os dados do cartão novamente.';
				} else {
					$message = 'O campo ' . $this->getFieldName($property) . ' não está preenchido corretamente.';
				}
				break;
			case 3500042:
				$message = $messageErrorDefault;
				break;
			case 3500044:
				$message = 'A transação não pode ser paga. Entre em contato com o vendedor.';
				break;
			case 4600002:
				$message = $messageErrorDefault;
				break;
			case 4600012:
				$message = 'Ocorreu um erro ao tentar realizar o pagamento: ' . $property;
				break;
			case 4600022:
				$message = $messageErrorDefault;
				break;
			case 4600026:
				$message = 'cpf inválido';
				break;
			case 4600029:
				$message = 'pedido já existe';
				break;
			case 4600032:
				$message = $messageErrorDefault;
				break;
			case 4600035:
				$message = 'Serviço indisponível para a conta. Por favor, solicite que o recebedor entre em contato com o suporte Gerencianet.';
				break;
			case 4600037:
				$message = 'O valor da emissão é superior ao limite operacional da conta. Por favor, solicite que o recebedor entre em contato com o suporte Gerencianet.';
				break;
			case 4600073:
				$message = 'O telefone informado não é válido.';
				break;
			case 4600111:
				$message = 'valor de cada parcela deve ser igual ou maior que R$5,00';
				break;
			case 4600142:
				$message = 'Transação não processada por conter incoerência nos dados cadastrais.';
				break;
			case 4600148:
				$message = 'já existe um pagamento cadastrado para este identificador.';
				break;
			case 4600196:
				$message = $messageErrorDefault;
				break;
			case 4600204:
				$message = 'cpf deve ter 11 números';
				break;
			case 4600209:
				$message = 'Limite de emissões diárias excedido. Por favor, solicite que o recebedor entre em contato com o suporte Gerencianet.';
				break;
			case 4600210:
				$message = 'não é possível emitir três emissões idênticas. Por favor, entre em contato com nosso suporte para orientações sobre o uso correto dos serviços Gerencianet.';
				break;
			case 4600212:
				$message = 'Número de telefone já associado a outro CPF. Não é possível cadastrar o mesmo telefone para mais de um CPF.';
				break;
			case 4600222:
				$message = 'Recebedor e cliente não podem ser a mesma pessoa.';
				break;
			case 4600219:
				$message = 'Ocorreu um erro ao validar seus dados: ' . $property;
				break;
			case 4600224:
				$message = $messageErrorDefault;
				break;
			case 4600254:
				$message = 'identificador da recorrência não foi encontrado';
				break;
			case 4600257:
				$message = 'pagamento recorrente já executado';
				break;
			case 4600329:
				$message = 'código de segurança deve ter três digitos';
				break;
			case 4699999:
				$message = 'falha inesperada';
				break;
			default:
				$message = $messageErrorDefault;
				break;
		}

		return $message;
	}

	public function getFieldName($name)
	{
		switch ($name) {
			case "neighborhood":
				return 'Bairro';
				break;
			case "street":
				return 'Endereço';
				break;
			case "complement":
				return 'Complemento';
				break;
			case "number":
				return 'Número';
				break;
			case "city":
				return 'Cidade';
				break;
			case "zipcode":
				return 'CEP';
				break;
			case "name":
				return 'Nome';
				break;
			case "cpf":
				return 'CPF';
				break;
			case "phone_number":
				return 'Telefone de contato';
				break;
			case "email":
				return 'Email';
				break;
			case "cnpj":
				return 'CNPJ';
				break;
			case "corporate_name":
				return 'Razão Social';
				break;
			case "installments":
				return 'Quantidade de Parcelas';
				break;
			case "birth":
				return 'Data de nascimento';
				break;
			default:
				return '';
				break;
		}
	}

	public function formatMoney($value, $gnFormat)
	{
		$cleanString       = preg_replace('/([^0-9\.,])/i', '', $value);
		$onlyNumbersString = preg_replace('/([^0-9])/i', '', $value);

		$separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

		$stringWithCommaOrDot     = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
		$removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '', $stringWithCommaOrDot);

		if ($gnFormat) {
			return (int) (((float) str_replace(',', '.', $removedThousendSeparator)) * 100);
		} else {
			return ((float) str_replace(',', '.', $removedThousendSeparator));
		}
	}

	public static function formatCurrencyBRL($value)
	{
		$formated = "R$" . number_format($value  / 100, 2, ',', '.');

		return $formated;
	}


	public static function priceFormat($value)
	{
		$value = number_format($value, 2, "", "");
		return $value;
	}
}
