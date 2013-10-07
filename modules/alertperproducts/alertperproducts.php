<?php

class AlertPerProducts extends Module
{
	private $_html = '';
	private $_postErrors = array();

	private $_merchant_mails;
	private $_merchant_order;
	private $_merchant_oos;
	private $_customer_qty;

	const __MAP_MAIL_DELIMITOR__ = ',';

	public function __construct()
	{
		$this->name = 'alertperproducts';
		$this->tab = 'Tools';
		$this->version = '1.0';

		$this->_refreshProperties();

		parent::__construct();

		$this->displayName = $this->l('Alerts per product');
		$this->description = $this->l('Sends e-mails notifications to customers and merchants per product');
		$this->confirmUninstall = $this->l('Are you sure you want to delete all customers notifications ?');
	}

	public function install()
	{
		if (!parent::install() OR
			!$this->registerHook('newOrder') OR
			!$this->registerHook('updateQuantity') OR
			!$this->registerHook('productOutOfStock') OR
			!$this->registerHook('customerAccount') OR
			!$this->registerHook('updateProduct') OR
			!$this->registerHook('updateProductAttribute')
		)
			return false;

		Configuration::updateValue('MAP_MERCHANT_ORDER', 1);
		Configuration::updateValue('MAP_MERCHANT_OOS', 1);
		Configuration::updateValue('MAP_CUSTOMER_QTY', 1);
		Configuration::updateValue('MAP_MERCHANT_MAILS', Configuration::get('PS_SHOP_EMAIL'));
		Configuration::updateValue('MAP_LAST_QTIES', Configuration::get('PS_LAST_QTIES'));

		if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mailalert_customer_oos2` (
				`id_customer` int(10) unsigned NOT NULL,
				`customer_email` varchar(128) NOT NULL,
				`id_product` int(10) unsigned NOT NULL,
				`id_product_attribute` int(10) unsigned NOT NULL,
				PRIMARY KEY  (`id_customer`,`customer_email`,`id_product`,`id_product_attribute`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci')
		)
	 		return false;
		if (!Db::getInstance()->Execute('
			ALTER TABLE`'._DB_PREFIX_.'product` ADD COLUMN `qty_alert_mail` int(10) unsigned NOT NULL')
		)
	 		return false;

		/* This hook is optional */
		//$this->registerHook('myAccountBlock');
		return true;
	}

	public function uninstall()
	{
		Configuration::deleteByName('MAP_MERCHANT_ORDER');
		Configuration::deleteByName('MAP_MERCHANT_OOS');
		Configuration::deleteByName('MAP_CUSTOMER_QTY');
		Configuration::deleteByName('MAP_MERCHANT_MAILS');
		Configuration::deleteByName('MAP_LAST_QTIES');
	 	if (!Db::getInstance()->Execute('ALTER TABLE`'._DB_PREFIX_.'product` DROP COLUMN `qty_alert_mail`'))
	 		return false;
	 	if (!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'mailalert_customer_oos2'))
	 		return false;
		return parent::uninstall();
		return parent::uninstall();
	}
	
	private function _refreshProperties()
	{
		$this->_merchant_mails = Configuration::get('MAP_MERCHANT_MAILS');
		$this->_merchant_order = intval(Configuration::get('MAP_MERCHANT_ORDER'));
		$this->_merchant_oos = intval(Configuration::get('MAP_MERCHANT_OOS'));
		$this->_customer_qty = intval(Configuration::get('MAP_CUSTOMER_QTY'));
	}

	
	public function customerHasNotification($id_customer, $id_product, $id_product_attribute)
	{
		 
		$result = Db::getInstance()->ExecuteS('
			SELECT * 
			FROM `'._DB_PREFIX_.'mailalert_customer_oos2` 
			WHERE `id_customer` = '.intval($id_customer).' 
			AND `id_product` = '.intval($id_product).' 
			AND `id_product_attribute` = '.intval($id_product_attribute));
		return sizeof($result); 
	}

	public function hookUpdateQuantity($params)
	{
		global $cookie;
		
		$qty = intval($params['product']['quantity_attribute'] ? $params['product']['quantity_attribute'] : $params['product']['stock_quantity']);
		if ($qty <= intval(Configuration::get('MAP_last_qties')) AND !(!$this->_merchant_oos OR empty($this->_merchant_mails)) AND Configuration::get('PS_STOCK_MANAGEMENT'))
		{
			$templateVars = array(
				'{qty}' => $qty - $params['product']['cart_quantity'],
				'{last_qty}' => intval(Configuration::get('MAP_last_qties')),
				'{product}' => strval($params['product']['name']));
			$iso = Language::getIsoById(intval($cookie->id_lang));
			if (file_exists(dirname(__FILE__).'/mails/'.$iso.'/productoutofstock.txt') AND file_exists(dirname(__FILE__).'/mails/'.$iso.'/productoutofstock.html'))
				Mail::Send(intval(Configuration::get('PS_LANG_DEFAULT')), 'productoutofstock', $this->l('Product out of stock'), $templateVars, explode(self::__MAP_MAIL_DELIMITOR__, $this->_merchant_mails), NULL, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/');
		}
		
		if ($this->_customer_qty AND $params['product']->quantity > 0)
			$this->sendCustomerAlert(intval($params['product']->id), 0);
	}

	public function hookUpdateProduct($params)
	{
		if ($this->_customer_qty AND $params['product']->quantity > 0)
			$this->sendCustomerAlert(intval($params['product']->id), 0);
	}

	public function hookUpdateProductAttribute($params)
	{
		$result = Db::getInstance()->GetRow('
			SELECT `id_product`, `quantity` 
			FROM `'._DB_PREFIX_.'product_attribute` 
			WHERE `id_product_attribute` = '.intval($params['id_product_attribute']));
		$qty = intval($result['quantity']);
		if ($this->_customer_qty AND $qty > 0)
			$this->sendCustomerAlert(intval($result['id_product']), intval($params['id_product_attribute']));
	}
	
	
	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_postProcess();
		$this->_displayForm();
		return $this->_html;
	}

	private function _displayForm()
	{
		$tab = Tools::getValue('tab');
		$currentIndex = __PS_BASE_URI__.substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__)).($tab ? '?tab='.$tab : '');
		$token = Tools::getValue('token');

		$this->_html .= '
		<!--<form action="'.$currentIndex.'&token='.$token.'&configure=mailalerts" method="post">
			<fieldset class="width3"><legend><img src="'.$this->_path.'logo.gif" />'.$this->l('Customer notification').'</legend>
				<label>'.$this->l('Product availability:').' </label>
				<div class="margin-form">
					<input type="checkbox" value="1" id="MAP_customer_qty" name="MAP_customer_qty" '.(Tools::getValue('MAP_customer_qty', $this->_customer_qty) == 1 ? 'checked' : '').'>
					&nbsp;<label for="MAP_customer_qty" class="t">'.$this->l('Gives the customer the possibility to receive a notification for an available product if this one is out of stock ').'</label>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitMACustomer" class="button" />
				</div>
			</fieldset>
		</form>-->
		<br />
		<form action="'.$currentIndex.'&token='.$token.'&configure=mailalerts" method="post">
			<fieldset class="width3"><!--<legend><img src="'.$this->_path.'logo.gif" />'.$this->l('Merchant notification').'</legend>-->
				<label>'.$this->l('New order:').' </label>
				<div class="margin-form">
					<input type="checkbox" value="1" id="MAP_merchand_order" name="MAP_merchand_order" '.(Tools::getValue('MAP_merchand_order', $this->_merchant_order) == 1 ? 'checked' : '').'>
					&nbsp;<label for="MAP_merchand_order" class="t">'.$this->l('Receive a notification if a new order is made').'</label>
				</div>
				<label>'.$this->l('Out of stock:').' </label>
				<div class="margin-form">
					<input type="checkbox" value="1" id="MAP_merchand_oos" name="MAP_merchand_oos" '.(Tools::getValue('MAP_merchand_oos', $this->_merchant_oos) == 1 ? 'checked' : '').'>
					&nbsp;<label for="MAP_merchand_oos" class="t">'.$this->l('Receive a notification if the quantity of a product is below the alert threshold').'</label>
				</div>
				<label>'.$this->l('Alert threshold:').'</label>
				<div class="margin-form">
					<input type="text" name="MAP_LAST_QTIES" value="'.(Tools::getValue('MAP_LAST_QTIES') != NULL ? intval(Tools::getValue('MAP_LAST_QTIES')) : Configuration::get('MAP_LAST_QTIES')).'" size="3" />
					<p>'.$this->l('Quantity for which a product is regarded as out of stock').'</p>
				</div>
				<label>'.$this->l('Send to these emails:').' </label>
				<div class="margin-form">
					<div style="float:left; margin-right:10px;">
						<textarea name="MAP_merchant_mails" rows="10" cols="30">'.Tools::getValue('MAP_merchant_mails', str_replace(self::__MAP_MAIL_DELIMITOR__, "\n", $this->_merchant_mails)).'</textarea>
					</div>
					<div style="float:left;">
						'.$this->l('One email address per line').'<br />
						'.$this->l('e.g.,').' bob@example.com
					</div>
				</div>
				<div style="clear:both;">&nbsp;</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitMAMerchant" class="button action_submit_form" />
				</div>
			</fieldset>
		</form>';
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('submitMACustomer'))
		{
			if (!Configuration::updateValue('MAP_CUSTOMER_QTY', intval(Tools::getValue('MAP_customer_qty'))))
				$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
			else
				$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
		elseif (Tools::isSubmit('submitMAMerchant'))
		{
			$emails = strval(Tools::getValue('MAP_merchant_mails'));
			if (!$emails OR empty($emails))
				$this->_html .= '<div class="alert error">'.$this->l('Please type one (or more) email address').'</div>';
			else
			{
				$emails = explode("\n", $emails);
				foreach ($emails AS $k => $email)
				{
					$email = trim($email);
					if (!empty($email) AND !Validate::isEmail($email))
						return ($this->_html .= '<div class="alert error">'.$this->l('Invalid e-mail:').' '.$email.'</div>');
					if (!empty($email) AND sizeof($email))
						$emails[$k] = $email;
					else
						unset($emails[$k]);
				}
				$emails = implode(self::__MAP_MAIL_DELIMITOR__, $emails);
				if (!Configuration::updateValue('MAP_MERCHANT_MAILS', strval($emails)))
					$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
				elseif (!Configuration::updateValue('MAP_MERCHANT_ORDER', intval(Tools::getValue('MAP_merchand_order'))))
					$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
				elseif (!Configuration::updateValue('MAP_MERCHANT_OOS', intval(Tools::getValue('MAP_merchand_oos'))))
					$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
				elseif (!Configuration::updateValue('MAP_LAST_QTIES', intval(Tools::getValue('MAP_LAST_QTIES'))))
					$this->_html .= '<div class="alert error">'.$this->l('Cannot update settings').'</div>';
				else
					$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
			}
		}
		$this->_refreshProperties();
	}

	static public function getProductsAlerts($id_customer, $id_lang)
	{
		if (!Validate::isUnsignedId($id_customer) OR
			!Validate::isUnsignedId($id_lang)
		)
			die (Tools::displayError());

		$products = Db::getInstance()->ExecuteS('
			SELECT ma.`id_product`, p.`quantity` AS product_quantity, pl.`name`, ma.`id_product_attribute`
			FROM `'._DB_PREFIX_.'mailalert_customer_oos2` ma
			JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = ma.`id_product`
			JOIN `'._DB_PREFIX_.'product_lang` pl ON pl.`id_product` = ma.`id_product`
			WHERE ma.`id_customer` = '.intval($id_customer).'
			AND pl.`id_lang` = '.intval($id_lang));
		if (empty($products) === true OR !sizeof($products))
			return array();
		for ($i = 0; $i < sizeof($products); ++$i)
		{
			$obj = new Product(intval($products[$i]['id_product']), false, intval($id_lang));
			if (!Validate::isLoadedObject($obj))
				continue;

			if (isset($products[$i]['id_product_attribute']) AND
				Validate::isUnsignedInt($products[$i]['id_product_attribute']))
			{
				$result = Db::getInstance()->ExecuteS('
					SELECT al.`name` AS attribute_name
					FROM `'._DB_PREFIX_.'product_attribute_combination` pac
					LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
					LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
					LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.intval($id_lang).')
					LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.intval($id_lang).')
					LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
					WHERE pac.`id_product_attribute` = '.intval($products[$i]['id_product_attribute']));
				$products[$i]['attributes_small'] = '';
				if ($result)
					foreach ($result AS $k => $row)
						$products[$i]['attributes_small'] .= $row['attribute_name'].', ';
				$products[$i]['attributes_small'] = rtrim($products[$i]['attributes_small'], ', ');
				
				// cover
				$attrgrps = $obj->getAttributesGroups(intval($id_lang));
				foreach ($attrgrps AS $attrgrp)
					if ($attrgrp['id_product_attribute'] == intval($products[$i]['id_product_attribute']) AND $images = Product::_getAttributeImageAssociations(intval($attrgrp['id_product_attribute'])))
					{
						$products[$i]['cover'] = $obj->id.'-'.array_pop($images);
						break;
					}
			}
			if (!isset($products[$i]['cover']) OR !$products[$i]['cover'])
			{
				$images = $obj->getImages(intval($id_lang));
				foreach ($images AS $k => $image)
					if ($image['cover'])
					{
						$products[$i]['cover'] = $obj->id.'-'.$image['id_image'];
						break;
					}
			}
			if (!isset($products[$i]['cover']))
				$products[$i]['cover'] = Language::getIsoById($id_lang).'-default';
			$products[$i]['link'] = $obj->getLink();
		}
		return ($products);
	}

	static public function deleteAlert($id_customer, $customer_email, $id_product, $id_product_attribute)
	{
		return Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'mailalert_customer_oos2` 
			WHERE `id_customer` = '.intval($id_customer).'
			AND `customer_email` = \''.pSQL($customer_email).'\'
			AND `id_product` = '.intval($id_product).'
			AND `id_product_attribute` = '.intval($id_product_attribute));
	}
}

?>
