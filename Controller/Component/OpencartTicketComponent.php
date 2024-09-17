<?php
class OpencartTicketComponent extends Component
{

	public $components = array('OpencartExtension', 'Email', 'Session');

	public function initialize(Controller $controller)
	{
		$this->User = ClassRegistry::init('User');
		$this->Ticket = ClassRegistry::init('Tickets.Ticket');
		$this->Extension = ClassRegistry::init('Extensions.Extension');
		$this->TrialLicense = ClassRegistry::init('TrialLicense');
	}

	public function open_ticket($ticket_data, $resend = false, $assign_employ = false)
	{
		$system = array_key_exists('system', $ticket_data) ? $ticket_data['system'] : 'opencart';

		$fields_ticket = array('name' => 'Your name', 'email' => 'Email', 'subject' => 'Subject', 'text' => 'Problem', 'conections' => 'Conections');

		foreach ($fields_ticket as $key => $value) {
			${$key} = '';

			foreach ($ticket_data as $input_name => $val) {
				if (strpos($input_name, $key) !== false)
					${$key} = $val;
			}
		}

		foreach ($fields_ticket as $key => $fiel_name) {
			if ($key != 'conections' && empty(${$key}))
				throw new Exception('Fill "<b>' . $fiel_name . '</b>"');
		}

		if ($system == 'prestashop' && empty($ticket_data['license_id'])) {
			throw new Exception('Fill "<b>License ID</b>"');
		}

		//Only from website page "open_ticket"
		$extension_id = array_key_exists('extension_id', $ticket_data) ? $ticket_data['extension_id'] : '';
		$files_attached = array_key_exists('attach', $ticket_data) ? $ticket_data['attach'] : '';
		//END

		$license_id = array_key_exists('license_id', $ticket_data) ? $ticket_data['license_id'] : '';
		$domain = array_key_exists('domain', $ticket_data) ? $ticket_data['domain'] : '';
		$type = array_key_exists('type', $ticket_data) ? $ticket_data['type'] : '';

		$is_trial = strpos($license_id, 'trial-') !== false;

		if (!$is_trial) {
			$license_info = !empty($license_id) ? $this->OpencartExtension->license_get_license($license_id) : '';

			//Devman Extensions - info@devmanextensions.com - 2017-09-02 15:45:25 - Check expired license
			if (!empty($license_info)) {
				$this->OpencartExtension->license_check_expired($license_info);
				$extension_id = $license_info['Extension']['id'];
			}
			//END
		} else {
			$license_info = $this->TrialLicense->findByLicenseId($license_id);
			if (empty($license_info))
				throw new Exception('Trial not found.');
			else
				$extension_id = $license_info['TrialLicense']['extension_id'];
		}

		//Devman Extensions - info@devmanextensions.com - 2017-09-02 15:01:54 - Save ticket in database
		if (!$resend) {
			$ticket_temp_data = array(
				'Ticket' => array(
					'id_license' => $license_id,
					'domain' => $domain,
					'type' => $type,
					'email' => $email,
					'name' => $name,
					'subject' => $subject,
					'extension_id' => $extension_id,
					'text' => $text,
					'conections' => $conections,
				)
			);

			if (!$this->Ticket->saveAll($ticket_temp_data))
				throw new Exception('Error saving ticket data, try it later.');

			$ticket_id = $this->Ticket->getLastInsertId();

			$subject_email = 'New ticket pending [' . ucfirst($system) . '][' . $ticket_id . '][' . $type . ']';
		} else {
			$this->User->recursive = -1;
			$user = $this->User->find("first", array("conditions" => array("User.id" => $ticket_data['answered_user_id'])));
			$user = $user['User'];

			//$user = $this->Session->read('Auth.User');
			$user_name = $user['realname'];
			$ticket_id = $ticket_data['id'];
			$subject_email = 'Ticket - [' . $user_name . '][' . ucfirst($system) . '][' . $ticket_id . '][' . $type . ']';
		}
		//END

		//Devman Extensions - info@devmanextensions.com - 2017-09-02 15:21:55 - Send email to me about this ticket
		$content_email = '';

		//Devman Extensions - info@devmanextensions.com - 2017-09-02 15:36:57 - Ticket for support license
		if (in_array($type, array('Support')) && !empty($license_info)) {
			if ($resend)
				$subject_email .= '[' . $license_id . ']' . '[' . $license_info['Extension']['name'] . ']';

			$content_email .= "<b>Order id:</b> " . $license_id . "<br>";
			$content_email .= "<b>Domain:</b> " . $domain . "<br>";
			$content_email .= "<b>License domains registered:</b> " . $license_info['Sale']['domain'] . "<br>";
		}
		//END

		//Devman Extensions - info@devmanextensions.com - 2017-09-02 15:37:15 - Ticket for pre-sale question TODO
		if (in_array($type, array('Pre-sale question', 'Personal develop'))) {
			$extension = $this->Extension->findById($extension_id);
			if (!empty($extension['Extension']['name']))
				$subject_email .= '[' . $extension['Extension']['name'] . ']';
		}
		$subject_email .= ' - ' . $subject;
		//END

		$content_email .= "<b>Customer Name:</b> " . $name . "<br>";
		$content_email .= "<b>Customer Email:</b> <a href='mailto:" . $email . "'>" . $email . "</a><br>";

		$content_email .= "<br><b>Subject:</b> " . $subject . "<br>";
		$content_email .= "<b>Message:</b> " . nl2br($text) . "<br>";

		if (!empty($conections))
			$content_email .= "<b>Conections:</b><br>" . nl2br($conections) . "<br>";

		// $this->Email->send_email_support('support@devmanextensions.com', 'support@devmanextensions.com', 'DevmanExtensions support team', $subject_email, $content_email, $files_attached, $email);
		// if ($assign_employ) {
		// 	$this->Email->send_email_support($user['email'], $user['email'], 'DevmanExtensions support team', $subject_email, $content_email, $files_attached, $email);
		// } else {
		// 	$this->Email->send_email_support('support@devmanextensions.com', 'support@devmanextensions.com', 'DevmanExtensions support team', $subject_email, $content_email, $files_attached, $email);
		// 	if ($ticket_data['type'] == 'Support')
		// 		$this->Email->send_email_support('andres@devmanextensions.com', 'andres@devmanextensions.com', 'DevmanExtensions support team', $subject_email, $content_email, $files_attached, $email);
		// }
		// //END

		// //Devman Extensions - info@devmanextensions.com - 2017-09-02 15:51:29 - Send email to customer
		// if (!$resend) {
		// 	$subject_email = 'We received your ticket';

		// 	$content_email = "Hi " . $name . "!
		// 				<br>Thanks you to contact with <b><a href='https://www.devmanextensions.com'>Devman Extensions</a></b>.<br> We received your ticket, please be patient, we will attend asap in support hours.<br>If you have more question please <b>DON'T OPEN MORE TICKETS, YOU CAN RESPOND TO THIS EMAIL</b><br><br>" . $content_email;

		// 	$this->Email->send_email_support($email, 'support@devmanextensions.com', 'DevmanExtensions support team', $subject_email, $content_email, $files_attached);
		// }
		// //END
	}
}
