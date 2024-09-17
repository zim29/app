<?php
class TicketsController extends TicketsAppController
{
	public  $uses = array(
		'User',
		'Tickets.Ticket',
		'Licenses.License',
		'Extensions.Extension'
	);

	public $components = array(
		'OpencartExtension',
		'Email',
		'OpencartTicket',
		'RequestHandler',
	);

	// only allow the login controllers only
	public function beforeFilter()
	{
		$this->Auth->allow('create');
	}

	/**
	 *
	 * Función Index
	 *
	 */
	function index()
	{
		if (empty($this->request->data)) {
			$this->request->data = $this->Session->read('filter_tickets');
		} else {
			$this->Session->write('filter_tickets', $this->request->data);
		}


		//$tickets = $this->Ticket->find('all');

		//2019-04-28 23:59:59 - Start Luis working in ticket support
		$conditions = array('Ticket.created > ' => date("Y-m-d", strtotime("-15 days", strtotime(date('Y-m-d')))));

		$user = $this->Session->read('Auth.User');
		if ($user['role'] == 'employ-russia') {
			$conditions['Ticket.id_license LIKE'] = 'of-%';
		}

		$this->paginate = array(
			'limit' => 25,
			'conditions' => $conditions,
			'order' => array(
				'Ticket.id' => 'desc'
			)
		);

		//$this->Ticket->unbindModel(array('belongsTo' => array('Sale')));
		$data = $this->paginate('Ticket');

		$this->set("tickets", $data);
	}

	public function create()
	{
		$this->data = $this->request->data;
		$this->Session->write('ticket_data', $this->request->data);
		try {
			if (empty($this->request->data))
				throw new Exception('Not allow direct access.');

			//Devman Extensions - info@devmanextensions.com - 2016-10-12 19:42:38 - Captcha

			$userIP = $_SERVER["REMOTE_ADDR"];
			// $recaptchaResponse = $this->request->data['g-recaptcha-response'];
			// $secretKey = "6LeNxKAUAAAAAPNyzGddMVFpZLZUefwO4E3HXTh7";

			// $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}&remoteip={$userIP}");

			// if(!strstr($request, "true"))
			// 	throw new Exception('And the captcha??');
			//END
			$ticket_data = array(
				'type' => array_key_exists('type', $this->request->data['Ticket']) ? $this->request->data['Ticket']['type'] : '',
				'email' => array_key_exists('email', $this->request->data['Ticket']) ? $this->request->data['Ticket']['email'] : '',
				'name' => array_key_exists('name', $this->request->data['Ticket']) ? $this->request->data['Ticket']['name'] : '',
				'subject' => array_key_exists('subject', $this->request->data['Ticket']) ? $this->request->data['Ticket']['subject'] : '',
				'text' => array_key_exists('text', $this->request->data['Ticket']) ? $this->request->data['Ticket']['text'] : '',
				'conections' => array_key_exists('conections', $this->request->data['Ticket']) ? $this->request->data['Ticket']['conections'] : '',
				'extension_id' => array_key_exists('id_extension', $this->request->data['Ticket']) ? $this->request->data['Ticket']['id_extension'] : '',
				'attach' => array_key_exists('attach', $this->request->data['Ticket']) ? $this->request->data['Ticket']['attach'] : '',
				'license_id' => array_key_exists('id_order', $this->request->data['Ticket']) ? $this->request->data['Ticket']['id_order'] : '',
				'domain' => array_key_exists('web', $this->request->data['Ticket']) ? $this->request->data['Ticket']['web'] : '',
			);

			$this->OpencartTicket->open_ticket($ticket_data);


			$this->Session->setFlash(
				'<i class="fa fa-paper-plane"></i> <b>Ticket sent successfully</b>, check your email inbox or SPAM folder, you will get an auto response with ticket details.'
			);

			$this->data = $this->request->data = array();
		} catch (Exception $e) {
			die($e->getMessage());
			$this->Session->setFlash($e->getMessage(), 'default', array('class' => 'error'));
		}


		//$this->redirect(array('plugin' => false, 'controller' => 'Pages', 'action'=>'display', 'open_ticket'));
		$this->redirect($this->referer());
	}

	function view($id = null)
	{
		if ($id)
			$this->data = $this->Ticket->findById($id);
		else
			$this->data = array(
				'Ticket' => array(
					'id' => '',
					'name' => ''
				)
			);
	}

	public function assign($id)
	{
		$arra_return = array('error' => false, 'message' => '', 'data' => array());
		$this->Ticket->id = $id;
		$ticket = $this->Ticket->read();
		$data_to_save = array(
			'Ticket' => array(
				'id' => $id
			)
		);

		$user = $this->Session->read('Auth.User');
		$data_to_save['Ticket']['answered_user_id'] = $user['id'];

		$users = $this->User->find('all');
		$current_user_id = $user['id'];

		if ($this->Ticket->saveAll($data_to_save)) {
			$arra_return['data']['class'] = 'fa fa-user yes';
			$result = $this->resend($id, true, true);
			if ($result['error'])
				$arra_return = $result;
		} else {
			$arra_return['error'] = true;
			$arra_return['message'] = 'Error saving Ticket';
		}
		echo json_encode($arra_return);
		die;
	}
	public function resend($id = null, $return = false, $assign_employ = false)
	{
		$arra_return = array('error' => false, 'message' => 'Sent again success!', 'data' => array());
		$this->Ticket->id = $id;
		$ticket = $this->Ticket->read();

		try {
			$ticket_data = array(
				'id' => $ticket['Ticket']['id'],
				'type' => $ticket['Ticket']['type'],
				'email' => $ticket['Ticket']['email'],
				'name' => $ticket['Ticket']['name'],
				'subject' => $ticket['Ticket']['subject'],
				'text' => $ticket['Ticket']['text'],
				'conections' => $ticket['Ticket']['conections'],
				'extension_id' => $ticket['Ticket']['extension_id'],
				'license_id' => $ticket['Ticket']['id_license'],
				'domain' => $ticket['Ticket']['domain'],
				'answered_user_id' => $ticket['Ticket']['answered_user_id'],
			);

			$this->OpencartTicket->open_ticket($ticket_data, true, $assign_employ);
		} catch (Exception $e) {
			$arra_return['error'] = true;
			$arra_return['message'] = $e->getMessage();
		}

		if (!$return) {
			echo json_encode($arra_return);
			die;
		}

		return $arra_return;
	}

	function answered($id = null)
	{
		$arra_return = array('error' => false, 'message' => '', 'data' => array());
		$this->Ticket->id = $id;
		$ticket = $this->Ticket->read();

		$data_to_save = array(
			'Ticket' => array(
				'id' => $id,
				'answered' => 0
			)
		);

		if ($this->Ticket->saveAll($data_to_save)) {
			$arra_return['data']['class'] = 'fa fa-check-square no';
			$arra_return['data']['href'] = Router::url('/') . 'tickets/tickets/noanswered/' . $this->request->data['id'];
		} else {
			$arra_return['error'] = true;
			$arra_return['message'] = 'Error saving Ticket';
		}
		echo json_encode($arra_return);
		die;
	}
	function noanswered($id = null)
	{
		$arra_return = array('error' => false, 'message' => '', 'data' => array());
		$this->Ticket->id = $id;
		$ticket = $this->Ticket->read();

		if (empty($ticket['Ticket']['answered_user_id'])) {
			$arra_return['error'] = true;
			$arra_return['message'] = 'Ticket hasn\'t employ assigned';
			echo json_encode($arra_return);
			die;
		}

		$data_to_save = array(
			'Ticket' => array(
				'id' => $id,
				'answered' => 1
			)
		);
		if ($ticket['Ticket']['answered_date'] == '0000-00-00 00:00:00') {
			$user = $this->Session->read('Auth.User');
			$data_to_save['Ticket']['answered_date'] = date('Y-m-d H:i:s');
		}
		if ($this->Ticket->saveAll($data_to_save)) {
			$arra_return['data']['class'] = 'fa fa-check-square yes';
			$arra_return['data']['href'] = Router::url('/') . 'tickets/tickets/answered/' . $this->request->data['id'];
		} else {
			$arra_return['error'] = true;
			$arra_return['message'] = 'Error saving Ticket';
		}
		echo json_encode($arra_return);
		die;
	}

	function nosolve($id = null)
	{
		$arra_return = array('error' => false, 'message' => '', 'data' => array());
		$this->Ticket->id = $id;
		$ticket = $this->Ticket->read();

		if (empty($ticket['Ticket']) || !isset($ticket['Ticket'])) {
			$arra_return['error'] = true;
			$arra_return['message'] = 'Ticket not found';
		} else {

			if ($this->Ticket->saveField('solved', 1)) {
				$arra_return['data']['class'] = 'fa fa-check-square yes';
				$arra_return['data']['href'] = Router::url('/') . 'tickets/tickets/solve/' . $this->request->data['id'];
				/*
					//Email customer
						//Construct subject
							$subject = 'Your ticket has been closed. SOLVED!';
						//END Construct subject

						//Construct content
							$content="Hi ".$ticket['Ticket']['name'].", <br> Your ticket has been closed and marked like Solved. Thanks you!<br>If you are satisfied with my support and my extension please rate it and comment, it's a minute!<br>";
							$content.="<h2>SOLVED Ticket details:</h2>";
              				$content.="<b>You can still continue the conversation of this ticket if you still have doubts. Please, don't open news tickets. Thanks</b><br><br>";

							$content.="<b>Ticket Number:</b> ".$ticket['Ticket']['id']."<br>";
							$content .= "<b>Type:</b> ".$ticket['Ticket']['type']."<br>";

							if(in_array($ticket['Ticket']['type'], array('Pre-sale question', 'Personal develop')))
							{
								$extension_id = $ticket['Ticket']['extension_id'];
								$extension = $this->Extension->findById($extension_id);
								if(!empty($extension['Extension']['name']))
									$content .= "<b>Extension:</b> ".$extension['Extension']['name']."<br>";
							}

              //License
                if (!empty($ticket['Sale']))
                {
                  $content .= "<b>Order id:</b> ".$ticket['Sale']['order_id']."<br>";
                  $content .= "<b>Opencart store:</b> ".$ticket['Ticket']['domain']."<br>";
                  $content .= "<b>License domains registered:</b> ".$ticket['Sale']['domain']."<br>";
                }
              //END License

							$content .= "<br><b>Subject:</b> ".$ticket['Ticket']['subject']."<br>";
							$content .= "<b>Message:</b> ".nl2br($ticket['Ticket']['text'])."<br>";

							if ($ticket['Ticket']['conections'])
								$content .= "<b>Conections:</b> ".nl2br($ticket['Ticket']['conections'])."<br>";
						//END Construct content

						App::uses('CakeEmail', 'Network/Email');

						$Email = new CakeEmail();
						$Email->config('gmail');
						$Email->from(array('info@devmanextensions.com' => 'Devman Extensions'));
						$Email->to($ticket['Ticket']['email']);
						$Email->emailFormat('html');
						$Email->template('ticket_email');
						$Email->subject($subject);
						$Email->send($content);
					//END Email customer*/
			} else {
				$arra_return['error'] = true;
				$arra_return['message'] = 'Error solving Ticket';
			}
		}

		echo json_encode($arra_return);
		die;
	}

	function solve($id = null)
	{
		$arra_return = array('error' => false, 'message' => '');

		$this->Ticket->id = $id;
		$ticket = $this->Ticket->read();

		if (empty($ticket['Ticket']) || !isset($ticket['Ticket'])) {
			$arra_return['error'] = true;
			$arra_return['message'] = 'Ticket not found';
		} else {
			if ($this->Ticket->saveField('solved', 0)) {
				$arra_return['data']['class'] = 'fa fa-minus-square no';
				$arra_return['data']['href'] = Router::url('/') . 'tickets/tickets/nosolve/' . $this->request->data['id'];

				/*//Email customer
						//Construct subject
							$subject = 'Your ticket has been reopened.';
						//END Construct subject

						//Construct content
							$content="Hi ".$ticket['Ticket']['name'].", <br> Your ticket has been reopened and marked like No Solved. I will attend assap.<br>";
							$content.="<h2>REOPENED Ticket details:</h2>";
							$content.="<b>Ticket Number:</b> ".$ticket['Ticket']['id']."<br>";
							$content .= "<b>Type:</b> ".$ticket['Ticket']['type']."<br>";

							if(in_array($ticket['Ticket']['type'], array('Pre-sale question', 'Personal develop')))
							{
								$extension_id = $ticket['Ticket']['extension_id'];
								$extension = $this->Extension->findById($extension_id);
								if(!empty($extension['Extension']['name']))
									$content .= "<b>Extension:</b> ".$extension['Extension']['name']."<br>";
							}

							$content .= "<br><b>Subject:</b> ".$ticket['Ticket']['subject']."<br>";
							$content .= "<b>Message:</b> ".nl2br($ticket['Ticket']['text'])."<br>";

							if ($ticket['Ticket']['conections'])
								$content .= "<b>Conections:</b> ".nl2br($ticket['Ticket']['conections'])."<br>";
						//END Construct content

						App::uses('CakeEmail', 'Network/Email');

						$Email = new CakeEmail();
						$Email->config('gmail');
						$Email->from(array('info@devmanextensions.com' => 'Devman Extensions'));
						$Email->to($ticket['Ticket']['email']);
						$Email->emailFormat('html');
						$Email->template('ticket_email');
						$Email->subject($subject);
						$Email->send($content);
					//END Email customer*/
			} else {
				$arra_return['error'] = true;
				$arra_return['message'] = 'Error unsolving Ticket';
			}
		}
		echo json_encode($arra_return);
		die;
	}
}
