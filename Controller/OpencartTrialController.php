<?php
	class OpencartTrialController extends AppController
    {

        public $uses = array(
            'Trial',
            'Extensions.Extension',
            'Faq'
        );

        public $components = array(
            'Mailchimp',
            'OpencartFormGenerator',
            'Email'
        );

        public function beforeFilter()
        {
            $this->Auth->allow('generate_form', 'form_import_export_xls', 'validate_extension', 'get_validate_extension_link');
            $this->api_url =  $_SERVER['SERVER_NAME'] == 'localhost' ? 'http://localhost/devmanextensions_intranet/' : 'https://devmanextensions.com/';
        }

        public function generate_form() {

            echo json_encode(array('form' => '', 'error' => 'You are using old version, download last version <a target="_blank" href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=33123">here</a>'));
            die;


            if (empty($this->request->data)) {
                die("No params");
            }

            $form = json_decode($this->request->data['form'], true);
            $form_basic_datas = json_decode($this->request->data['form_basic_datas'], true);
            $form_basic_datas['domain'] = $this->request->data['domain'];
            $form_basic_datas['ts6EpBx2'] = true;
            $form_basic_datas['license_id'] = 'TRIAL';
            $oc_version = $form['oc_version'];
            $form_basic_datas['oc_version'] = $oc_version;
            $extension_id = $form_basic_datas['extension_id'];
            $domain = $form_basic_datas['domain'];

            $error = false;
            $form_view = false;
            try {
                $days_to_expired = $this->Trial->check($extension_id, $domain, $oc_version);
                $form_view = $this->form_import_export_xls($form_basic_datas, $form, $days_to_expired);
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                if(in_array($error_message, array('not_found'))) {
                    if($error_message == 'not_found')
                        $form_view = $this->get_form_not_found($extension_id, $domain, $oc_version, $form_basic_datas);
                } else {
                    $error = $error_message;
                }
            }

            echo json_encode(array('form' => $form_view, 'error' => $error));
            die;
        }
        function form_import_export_xls($form_basic_datas, $form, $days_to_expired)
        {
            $conditions = array('Faq.extension_id' => '542068d4-ed24-47e4-8165-0994fa641b0a', 'Faq.system' => 'opencart');
            $faqs = $this->Faq->find('all', array('conditions' => $conditions, 'order' => array('Faq.order ASC', 'Faq.created ASC')));

            if(!empty($faqs))
                $tab_help = $this->OpencartFormGenerator->get_tab_faq($faqs);

            $expired = $days_to_expired <= 0;
            $action = $form['action'];
            $import_xls_innodb_converted = $form['import_xls_innodb_converted'];
            $href_download_xls = $form['href_download_xls'];
            $url_backup_tool = $form['url_backup_tool'];
            $products_in_shop = $form['products_in_shop'];
            $oc_version = $form['oc_version'];
            $action_js = "$(this).find('i').first().toggleClass('fa-angle-down fa-angle-up')";
            $form_view = array(
                'action' => $action,
                'id' => 'import_xls',
                'extension_name' => 'import_xls',
                'columns' => 1,
                'tabs' => array(
                    'Daily product managing' => array(
                        'icon' => '<i class="fa fa-archive"></i>',
                        'fields' => array(
                            array(
                                'type' => 'legend',
                                'text' => '<b>Recommended </b> change your tables to InnoDB motor if hasn\'t.',
                                'remove_border_button' => true
                            ),

                            array(
                                'type' => 'html_code',
                                'html_code' => 'If your database tables haven\'t InnoDB motor if while importing appear some unexpected error the previous datas that was inserted won\'t be deleted, however if you convert your tables to InnoDB motor the Rollback query will be compatible and if appear some unexpected error your datas inserted while import process will be deleted.<br><br><b> His shop won\'t be affected by this change motor</b>',
                                'remove_border_button' => true
                            ),

                            array(
                                'type' => 'button',
                                'text' => '<i class="fa fa-database"></i> Convert to InnoDB',
                                'onclick' => 'convert_to_innodb();'
                            ),

                            array(
                                'type' => 'legend',
                                'text' => '<a onclick="$(\'.import_export_process_configuration\').toggle();' . $action_js . '" class="button"><i class="fa fa-angle-down" aria-hidden="true"></i><i class="fa fa-cog"></i></a> Configuration - Import/Export process',
                                'class' => 'with_button',
                            ),
                            array(
                                'type' => 'html_hard',
                                'html_code' => $oc_version == 1 ? '<tr class="inner_section import_export_process_configuration" style="display: none;"><td colspan="2"><table class="form">' : '<div class="inner_section import_export_process_configuration" style="display: none;">'
                            ),
                            array(
                                'label' => 'Category tree',
                                'help' => 'Category parent > Category children 1 > Category children 2...',
                                'type' => 'boolean',
                                'name' => 'import_xls_categories_tree',
                                'value' => $form['config']['import_xls_categories_tree'],
                                'after' => '<input type="hidden" name="ajax_function">'
                            ),
                            array(
                                'label' => 'Last tree child assign',
                                'help' => 'If checked product will only be assigned to the last child of trees, else it will be assigned to all trees categories.',
                                'type' => 'boolean',
                                'name' => 'import_xls_categories_last_tree',
                                'value' => $form['config']['import_xls_categories_last_tree'],
                            ),
                            array(
                                'type' => 'button',
                                'label' => 'Save configuration',
                                'text' => '<i class="fa fa-floppy-o"></i> Save configuration',
                                'onclick' => 'save_configuration();'
                            ),
                            array(
                                'type' => 'html_hard',
                                'html_code' => $oc_version == 1 ? '</table></td></tr>' : '</div>'
                            ),
                        )
                    ),
                    'Discover PRO version features' => array(
                        'icon' => '<i class="fa fa-trophy"></i>',
                        'fields' => array(
                            array(
                                'type' => 'html_code',
                                'html_code' => '
                                    <center><h1>Get PRO version</h1></center>
                                    <ul style="font-size: 16px;">
                                        <li><b>More import/export formats</b>: xlsx, csv, xml</li>
                                        <li><b>Set item numbers</b>: Attributes, filters, images, categories, specials, discounts, downloads.</li>
                                        <li><b>Make your excel</b>: Delete unnecesary columns to make excel esier to manage!</li>
                                        <li><b>Multiples filters in product exports</b>: By store, by product status, by categories, by manufacturers, only specials, by tax classes...</li>
                                        <li><b>Extra import/export with multiple filters</b>: Export another elements like categories, attributes, options, options values, manufactures, filter groups, filters, customer groups, customers, addresses, orders...</li>
                                        <li><b>Connect with Google Spreadsheet</b>: Send your product export to google drive and get data from google drive to do import products!</li>
                                        <li><b>Full backups or migrations</b>: Manage full website backups or full migrations for Opencart versions!</li>
                                        <li><b>AND MUCH MORE! -> <a href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=16803">Discover PRO version</a></b></li>
                                    </ul>
                                    <center>
                                    <h2><a target="_blank" href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=16803">Click here for more information</a></h2>
                                    <a target="_blank" href="https://goo.gl/7msUjB" rel="nofollow"><img style="max-width: 700px;" src="https://devmanextensions.com/images/extensions/big_banners/import_export_xls_explain.jpg" alt="" class="img-responsive"></a>
                                    </center>',
                                'remove_border_button' => true
                            ),
                        )
                    ),
                    'FAQ' => $tab_help
                )
            );

            if ($import_xls_innodb_converted) {
                unset($form_view['tabs']['Daily product managing']['fields'][0]);
                unset($form_view['tabs']['Daily product managing']['fields'][1]);
                unset($form_view['tabs']['Daily product managing']['fields'][2]);
            }

            //Devman Extensions - info@devmanextensions.com - 13/12/17 16:05 - Add import process to tab product
            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'legend',
                'class' => 'with_button',
                'text' => '<a onclick="$(\'.products_import_process\').toggle();' . $action_js . '" class="button"><i class="fa fa-angle-down" aria-hidden="true"></i><i class="fa fa-upload"></i></a>Import products',
            );
            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'html_hard',
                'html_code' => $oc_version == 1 ? '<tr class="inner_section products_import_process" style="display: none;"><td colspan="2"><table class="form">' : '<div class="inner_section products_import_process" style="display: none;">'
            );
            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'legend',
                'text' => '1.- Download empty XLS file and fill it or use exported file to bulk changes!',
                'remove_border_button' => true
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'button',
                'label' => 'Download xls file',
                'text' => '<i class="fa fa-file-excel-o"></i> Download xls file',
                'href' => $href_download_xls
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'legend',
                'text' => '<b>2.-</b> Modify your XLS file. (Optional)',
                'remove_border_button' => true
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'html_code',
                'html_code' => 'You can change order columns. <b>Never change column name or create new columns</b> the system will detect this and give you error.',
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'legend',
                'text' => '<b>3.-</b> Upload your filled XLS file or get it from Google SpreadSheets',
                'remove_border_button' => true
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'button',
                'class' => 'button_upload_xls',
                'label' => '2.- Upload file',
                'text' => '<i class="fa fa-upload"></i> Upload xls file <span></span>',
                'onclick' => "$(this).next('input').click();",
                'after' => '<input onchange="readURL($(this));" name="upload" type="file" style="display:none;">'
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'legend',
                'text' => '<b>4.-</b> Upload your images. (Optional)',
                'remove_border_button' => true
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => version_compare($oc_version, '2', '>=') ? 'button' : 'button_upload_images',
                'label' => 'Upload images',
                'help' => 'You can be upload all files once in <b>.zip</b> file.',
                'text' => '<i class="fa fa-file-image-o"></i> Upload images',
                'data' => version_compare($oc_version, '2', '>=') ? array('name' => 'toggle', 'value' => 'image') : array()
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'legend',
                'text' => '<b>5.-</b> All ready? <b>Go to import!</b>',
                'remove_border_button' => true
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'button',
                'label' => 'Press button to start to import',
                'text' => '<i class="fa fa-download"></i> <b>START IMPORT</b>',
                'help' => sprintf('<b>IMPORTANT</b>: For your safety, make a backup before import.', $url_backup_tool),
                'onclick' => 'ajax_start_import(\'products\')'
            );
            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'html_hard',
                'html_code' => $oc_version == 1 ? '</table></td></tr>' : '</div>'
            );
            //END

            //Devman Extensions - info@devmanextensions.com - 13/12/17 16:26 - Add export products process
            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'legend',
                'class' => 'with_button',
                'text' => '<a onclick="$(\'.products_export_process\').toggle();' . $action_js . '" class="button"><i class="fa fa-angle-down" aria-hidden="true"></i><i class="fa fa-download"></i></a>Export products',
            );
            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'html_hard',
                'html_code' => $oc_version == 1 ? '<tr class="inner_section products_export_process" style="display: none;"><td colspan="2"><table class="form">' : '<div class="inner_section products_export_process" style="display: none;">'
            );
            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'legend',
                'text' => '<b>Export your product datas!</b> You can use this excel to bulk changes or Opencart migrations!',
                'remove_border_button' => true
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'button',
                'label' => 'Export xls file',
                'text' => '<i class="fa fa-file-excel-o"></i> Export xls file',
                'onclick' => 'export_start();'
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'legend',
                'text' => 'Filters to apply to products'
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'label' => 'Range from',
                'help' => sprintf('Export products by ranges, you can put from <b>1</b> to <b>%s</b>.', $products_in_shop),
                'type' => 'text',
                'value' => '',
                'name' => 'export_range_from'
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'label' => 'Range to',
                'help' => sprintf('Export products by ranges, you can put from <b>1</b> to <b>%s</b>.', $products_in_shop),
                'type' => 'text',
                'value' => '',
                'name' => 'export_range_to'
            );

            $form_view['tabs']['Daily product managing']['fields'][] = array(
                'type' => 'html_hard',
                'html_code' => $oc_version == 1 ? '</table></td></tr>' : '</div>'
            );

            if($expired) {
                unset($form_view['tabs']['Daily product managing']);
                $form_view['tabs']['Discover PRO version features']['fields'][] = array(
                    'type' => 'html_hard',
                    'html_code' => '<script>$(document).on("ready", function(){ open_manual_notification("Your trial period is finished. <a href=\"https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=16803\" target=\"_blank\">Get PRO version</a>", "danger", "exclamation"); });</script>'
                );
            } else {
                $form_view['tabs']['Daily product managing']['fields'][] = array(
                    'type' => 'html_hard',
                    'html_code' => '<script>$(document).on("ready", function(){ open_manual_notification("<b>Your trial period will finish in '.$days_to_expired.' days</b>. This is a basic version, if you want know the PRO version features click here! -> <a href=\"https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=16803\" target=\"_blank\">Get PRO version</a>.", "success"); });</script>'
                );
            }


            $form_view = $this->OpencartFormGenerator->generateForm($form_view, $form_basic_datas);


            $form_view .= $this->get_common_js_css($oc_version);

            if(!$expired) {
                $form_view .= '<script src=' . $this->api_url . 'trials/ie_xls/import_xls.js?' . date('Ymdhis') . '"></script>';
                $form_view .= '<link href=' . $this->api_url . 'trials/ie_xls/import_xls.css?' . date('Ymdhis') . '" type="text/css" rel="stylesheet" media="screen" />';
                $form_view .= '<link href=' . $this->api_url . 'trials/ie_xls/import_xls.css?' . date('Ymdhis') . '" type="text/css" rel="stylesheet" media="screen" />';
            }
            return $form_view;
        }

        public function get_form_not_found($extension_id, $domain, $oc_version, $form_basic_datas) {
            $extension = $this->Extension->findById($extension_id);
            $form_basic_datas['extension_group_config'] = '';
            $form_view = array(
                'action' => $this->api_url.'opencart_trial/get_validate_extension_link',
                'id' => 'trial_form',
                'extension_name' => 'trial_form',
                'license_id' => 'trial_form',
                'columns' => 1,
                'tabs' => array(
                    'Validate extension' => array(
                        'icon' => '<i class="fa fa-user"></i>',
                        'fields' => array(
                            array(
                                'type' => 'html_code',
                                'html_code' => 'Fill the next form to get link to validate your extension. <input type="hidden" name="domain" value="'.$domain.'"><input type="hidden" name="extension_id" value="'.$extension_id.'">',
                                'remove_border_button' => true
                            ),
                            array(
                                'type' => 'text',
                                'label' => 'Your name',
                                'name' => 'customer_name'
                            ),
                            array(
                                'type' => 'text',
                                'label' => 'Your email',
                                'name' => 'customer_email'
                            ),
                            array(
                                'type' => 'button',
                                'label' => 'Get your validation link',
                                'text' => '<i class="fa fa-floppy-o"></i> Validate extension',
                                'onclick' => 'get_validate_extension_link();'
                            ),
                        )
                    ),
                )
            );

            $form_view = $this->OpencartFormGenerator->generateForm($form_view, $form_basic_datas);

            $form_view .= $this->get_common_js_css($oc_version);

            return $form_view;
        }

        public function get_form_expired() {

        }

        public function get_common_js_css($oc_version) {
            $form_view = '';
            if (version_compare($oc_version, '2', '<')) {
                $form_view .= '<script src='.$this->api_url.'trials/bootstrap.min.js?' . date('Ymdhis') . '"></script>';
                $form_view .= '<link href='.$this->api_url.'trials/bootstrap.min.css?' . date('Ymdhis') . '" type="text/css" rel="stylesheet" media="screen" />';
                $form_view .= '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" type="text/css" rel="stylesheet" media="screen" />';
                $form_view .= '<link href='.$this->api_url.'trials/oc15x.css?' . date('Ymdhis') . '" type="text/css" rel="stylesheet" media="screen" />';
                $form_view .= '<script src='.$this->api_url.'trials/oc15x.js?' . date('Ymdhis') . '"></script>';
            } else {
                $form_view .= '<link href='.$this->api_url.'trials/oc2x.css?' . date('Ymdhis') . '" type="text/css" rel="stylesheet" media="screen" />';
                $form_view .= '<script src='.$this->api_url.'trials/oc2x.js?' . date('Ymdhis') . '"></script>';
            }

            $form_view .= '<script src='.$this->api_url.'trials/tools.js?' . date('Ymdhis') . '"></script>';

            return $form_view;
        }

        public function validate_extension($id_trial) {
            if(empty($id_trial)) {
                die("empty param");
            }
            try {
                $trial = $this->Trial->findById($id_trial);

                $temp = array(
                    'Trial' => array(
                        'id' => $id_trial,
                        'activated' => 1
                    )
                );

                $this->Trial->save($temp);

                $mailchimp_list_id = 'ddb182e8b6';
                $mailchimp_data = array(
                    'email' => $trial['Trial']['customer_email'],
                    'merge_fields' => array(
                        'FNAME' => $trial['Trial']['customer_name'],
                        'ENAME' => $trial['Extension']['name'],
                        'EID' => $trial['Extension']['oc_extension_id']
                    )
                );

                $this->Mailchimp->subscribe($mailchimp_list_id, $mailchimp_data);

                $message = '<b>Name</b>: '.$trial['Trial']['customer_name'].'<br>
                    <b>Email</b>: '.$trial['Trial']['customer_email'].'<br>
                    <b>Extension</b>: '.$trial['Extension']['name'];
                $this->Email->send_email('info@devmanextensions.com', 'info@devmanextensions.com', 'Devmanextensions Trials system', 'Trial validated - '.$trial['Extension']['name'], $message);

                $this->Session->setFlash('Extension validate successful, now you can refresh your extension page in admin zone.', 'default', array('class' => 'success'));
                $this->redirect(array('plugin' => false, 'controller' => 'pages', 'action' => 'display', 'shop'));

            } catch (Exception $e) {
                $error_message = $e->getMessage();
                $this->Session->setFlash($error_message, 'default', array('class' => 'error'));
			    $this->redirect(array('plugin' => false, 'controller' => 'errors', 'action' => 'general'));
            }
        }

        public function get_validate_extension_link() {
            $array_return = array('error' => false, 'message' => '');

            if(empty($this->request->data['customer_name'])) {
                $array_return['error'] = true;
                $array_return['message'] = 'Fill your name';
                echo json_encode($array_return); die;
            }
            if(empty($this->request->data['customer_email'])) {
                $array_return['error'] = true;
                $array_return['message'] = 'Fill your email';
                echo json_encode($array_return); die;
            }

            $trials = $this->Trial->find('all', array('conditions' => array('Trial.customer_email' => $this->request->data['customer_email'])));
            if(!empty($trials)) {
                $array_return['error'] = true;
                $array_return['message'] = 'This email was used for another validate.';
                echo json_encode($array_return); die;
            }

            try {
                $data_to_save = $this->request->data;
                $extension_id = $this->request->data['extension_id'];
                $extension = $this->Extension->findById($extension_id);

                if(empty($extension))
                    throw new Exception('Extension not found.');

                $data_to_save['extension_name'] = $extension['Extension']['name'];
                $this->Trial->save($data_to_save);

                $trial_id = $this->Trial->getLastInsertID();

                $subject = 'Validate - '.$extension['Extension']['name'];
                $message = sprintf('<p>Hi %s!', $data_to_save['customer_name']).'</p>';
                $message .= sprintf('<p>To validate your extension <a href="%s">click in this link</a>. After, go to your admin shop and refresh extension page.</p>', $this->api_url.'opencart_trial/validate_extension/'.$trial_id);
                $this->Email->send_email($this->request->data['customer_email'], 'info@devmanextensions.com', 'Validate extension', $subject, $message);

                $array_return['message'] = sprintf('Validate link sent successful to email <b>%s</b>, check INBOX and SPAM folder.', $data_to_save['customer_email']);
                echo json_encode($array_return); die;
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                $array_return['error'] = true;
                $array_return['message'] = $error_message;
                echo json_encode($array_return); die;
            }
        }
    }