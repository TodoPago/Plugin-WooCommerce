<?php
namespace TodoPago\Core\ControlFraude;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once dirname(__FILE__).'/Retail.php';

class ControlFraudeFactory {

	const RETAIL = "Retail";
	const SERVICE = "Service";
	const DIGITAL_GOODS = "Digital Goods";
	const TICKETING = "Ticketing";

	public static function get_ControlFraude_extractor($vertical, $order, $customer){
		switch ($vertical) {
			case ControlFraudeFactory::RETAIL:
				$instance = new ControlFraude_Retail($order, $customer);
			break;

			case ControlFraudeFactory::SERVICE:
				$instance = new ControlFraude_Service($order, $customer);
			break;

			case ControlFraudeFactory::DIGITAL_GOODS:
				$instance = new ControlFraude_DigitalGoods($order, $customer);
			break;

			default:
				$instance = new ControlFraude_Retail($order, $customer);
			break;
		}
		return $instance;
	}
}
