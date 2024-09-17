<?php
	 require_once VENDORS . 'Redsys/RedsysAPI.php';
	class TpvController extends InvoicesAppController
	{
		public  $uses = array(
			'Invoices.Invoice'
		);

		public $components = array('Invoices.InvoicesOpencart', 'OpencartExtension', 'Email');

		public function beforeFilter() {
	        $this->Auth->allow('callback','error');

	        //Devman Extensions - info@devmanextensions.com - 2017-10-10 09:52:42 - TPV
	        	$this->tpv_test = true;
	        //END
	    }

		function callback()
		{
			//Check signature
	       if (!class_exists("RedsysAPI")) {
	           require_once(REDSYS_FILE_PATH);
	       }

	       // Se crea Objeto
	       $redsys = new RedsysAPI;

	       /** Se decodifican los datos enviados y se carga el array de datos **/
	       $decoded = $redsys->decodeMerchantParameters($_REQUEST["Ds_MerchantParameters"]);
	       $redsys->stringToArray($decoded);

	       /** Clave **/
	       $kc = $this->tpv_test ? 'sq7HjrUOBfKmC576ILgskD5srU870gJ7' : 'swlc3cD4nVupK7M6OcgOl5BfYVM2xesj';

	       /** Se calcula la firma **/
	       $firma_local = $redsys->createMerchantSignatureNotif($kc, $_REQUEST["Ds_MerchantParameters"]);

	       /** Extraer datos de la notificación **/
	        $ds_date=$_REQUEST['Ds_Date'];
	        $ds_hour=$_REQUEST['Ds_Hour'];
	        $ds_amount=$_REQUEST['Ds_Amount'];
	        $ds_currency=$_REQUEST['Ds_Currency'];
	        $ds_order=$_REQUEST['Ds_Order'];
	        $ds_merchantcode=$_REQUEST['Ds_MerchantCode'];
	        $ds_terminal=$_REQUEST['Ds_Terminal'];
	        $ds_signature=$_REQUEST['Ds_Signature'];
	        $ds_response=$_REQUEST['Ds_Response'];
	        $ds_transactiontype=$_REQUEST['Ds_TransactionType'];
	        $ds_securepayment=$_REQUEST['Ds_SecurePayment'];
	        $ds_merchantdata=$_REQUEST['Ds_MerchantData'];
	        $ds_authorisationcode=$_REQUEST['Ds_AuthorisationCode'];
	        $ds_card_country=$_REQUEST['Ds_Card_Country'];
	        $ds_card_type=$_REQUEST['Ds_Card_Country'];

	        $invoice_id = $ds_merchantdata;
	        $invoice = $this->Invoice->findById($invoice_id);

			if(!empty($invoice)){
	            /*if ($firma_local === $_POST["Ds_Signature"]
	               && RedsysHelper::checkRespuesta($ds_response)
	               && RedsysHelper::checkMoneda($ds_currency)
	               && RedsysHelper::checkFuc($ds_merchantcode)
	               && RedsysHelper::checkPedidoNum($ds_order)
	               && RedsysHelper::checkImporte($ds_amount)
	           ) {
	                $this->send_email_error("Firma no válida, procedencia del mensaje (".$_SERVER['REMOTE_ADDR'].") no verificada: ".$ds_signature."!=".$firma_local);
	                die("Firma no válida, procedencia del mensaje no verificada");
	            } else*/
	            $ds_response = (int)ltrim($ds_response, 0);

	            if ($ds_response >= 0 && $ds_response <= 99){
					if(empty($ds_authorisationcode)) {
						$this->send_email_error('Error paying invoice', array('Error message' => 'ds_authorisationcode empty', 'Datas received' => json_encode($_REQUEST)));
						die("finish");
					}
					if(in_array($invoice['Invoice']['system'], array('Opencart', 'cs-cart', 'woo')))
					{
						try {
							$ds = ConnectionManager::getDataSource($this->Invoice->useDbConfig);
							$ds->begin($this);
							$invoice['Invoice']['tpv_id_transaction'] = $ds_authorisationcode;
					    	$this->InvoicesOpencart->process_invoice($invoice);
					    	$ds->commit();
						} catch (Exception $e) {
							$this->send_email_error('Error during update invoice', array('Error message' => $e->getMessage()));
							die();
							$ds->rollback();
						}
						die("finish");
					}
					else
					{
						$this->send_email_error('System not found: '.$invoice['Invoice']['system']);
						die("finish");
					}
	            } else {
	                $errors=array();
	                $errors[101]='Tarjeta caducada';
	                $errors[102]='Tarjeta bloqueada por el banco emisor';
	                $errors[107]='Orden de contactar con el banco emisor de la tarjeta';
	                $errors[180]='Tarjeta no soportada por el sistema';
	                $errors[184]='Autenticación del titular de la tarjeta fallida';
	                $errors[190]='Denegada por el banco emisor de la tarjeta por diversos motivos';
	                $errors[201]='Tarjeta caducada. Orden de retirar la tarjeta';
	                $errors[202]='Tarjeta bloqueada por el banco emisor. Orden de retirar la tarjeta';
	                $errors[290]='Denegada por diversos motivos. Orden de retirar la tarjeta';
	                $errors[909]='Error de sistema';
	                $errors[912]='Centro resolutor no disponible';
	                $errors[913]='Recibido mensaje duplicado';
	                $errors[949]='Fecha de caducidad de la tarjeta errónea';
	                $errors[9111]='Banco emisor de la tarjeta no responde';
	                $errors[9093]='Número de tarjeta inexistente';
	                $errors[9112]='Número de tarjeta inexistente';
	                //Transacción denegada
	                $result = (int)$ds_response;
	                $deserror = $errors[$result];

	                $this->send_email_error('Error paying invoice', array('Error message' => $deserror, 'Datas received' => json_encode($_REQUEST)));
	                die("finish");
	            }
	        }
	        else
			{
				$this->send_email_error('Invoice not found: '.$invoice_id);
				die("finish");
			}
			die("finish");
		}

		public function error()
		{
			$this->send_email_error('Payment error from TPV', array('Datas returned' => json_encode($_POST)));

			$message = 'Error during payment. Devman was alert, don\'t worry! =)';
			$this->Session->setFlash($message, 'default', array('class' => 'error'));
			$this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));

			die("finish");
		}

		public function send_email_error($subject, $extra_variables = array())
		{
			$subject = '[TpvController][ERROR] - '.$subject;

			$message = '';
			if(!empty($extra_variables))
			{
				foreach ($extra_variables as $label => $value) {
					$message .= '<b>'.$label.'</b>: '.$value.'<br>';
				}
			}

			$this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Invoices System', $subject, $message);
		}
	}
?>
