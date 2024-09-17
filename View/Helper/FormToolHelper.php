<?php
/**
 * Class and Function List:
 * Function list:
 * - fieldset()
 * - toolbar()
 * - button()
 * - file()
 * - gallery()
 * Classes list:
 * - FormToolHelper extends AppHelper
 */

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Helper', 'View/Helper/Form');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class FormToolHelper extends AppHelper {
       
        var $helpers = array(
                'Html',
                "Form"
        );
       
        /**
         *
         * Generación de tabla de edición
         * @param $contenido
         */
       
        public function fieldset($contenido, $opciones = null, $inputs_ocultos = null)
        {
                $columns = isset($contenido['columns']) ? $contenido['columns'] : 2;
                $style = isset($opciones['style']) ? $opciones['style'] : "";
                $span = 'col-md-' . floor(12 / $columns) . ' col-sm-' . floor(12 / $columns);
                $title = isset($contenido['title']) ? $contenido['title'] : false;
               
                // Attributes
                $attributes = "";
                if (isset($contenido['attributes'])) {
                        foreach ($contenido['attributes'] as $key => $attr) {
                                $attributes.= $key . '="' . $attr . '" ';
                        }
                } ?>
                <div class="row <?php
                if (!empty($opciones['class'])) echo $opciones['class']; ?>" style="<?php
                echo $style; ?> ">
                <fieldset data-columns="<?php
                echo $columns
?>" <?php
                echo $attributes; ?>>
                        <?php
                if ($title) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <legend><?= $title; ?></legend>
                        </div>
                    </div>                                
                <?php }
                $column = 0; ?>
                <div class="row">
                <?php
                foreach ($contenido['inputs'] as $key => $item) {
                        if (is_array($item)) { ?>
                                <div class="row">
                                        <div class="col-md-12">
                                                <?php
                                echo $this->Html->tag($item['tag'], $item['text']); ?>
                                        </div>
                                </div>
                                <?php
                        } else {
                                if (false) { ?>
                                <div class="row">
                                        <?php
                                } ?>
                                <div class="<?php
                                    echo $span; ?>">
                                
                                <?php
                                echo $item; ?>
                        </div>
                        <?php
                                if (false) {
                                        $column = - 1; ?>
                        </div>
                        <?php
                                }
                                $column++;
                        }
                } ?>
                </div>
                </fieldset>
                </div>
                <?php
        }

        public function button($label, $type="save", $aling = 'right',  $params = null)
        {
            //Config align
            if ($aling == "right") $aling = 'text-right';
            else $aling = 'text-left';

            //Config type
                $class = '';
                $icon = '';
                if ($type == 'save')
                {
                    $class = "btn btn-lg btn-success save";
                    $icon = '<i class="fa fa-floppy-o"></i>';
                }
                elseif ($type == 'login')
                {
                    $class = "btn btn-lg btn-primary login";
                    $icon = '<i class="fa fa-user"></i>';
                }
                elseif ($type == 'cancel')
                {
                    $class = "btn btn-lg btn-danger cancel";
                    $icon = '<i class="fa fa-arrow-circle-left"></i>';
                }
                elseif ($type == 'logout')
                {
                    $class = "btn btn-lg btn-danger logout";
                    $icon = '<i class="fa fa-power-off"></i>';
                }
                elseif ($type == 'ticket')
                {
                    $class = "btn btn-lg btn-primary ticket";
                    $icon = '';
                }
                elseif ($type == 'globe')
                {
                    $class = "btn btn-lg btn-primary globe";
                    $icon = '<i class="fa fa-globe"></i>';
                }
                elseif ($type == 'attach')
                {
                    $class = "btn btn-lg btn-primary attach";
                    $icon = '<i class="fa fa-paperclip"></i>';
                }
                elseif ($type == 'credit_card')
                {
                    $class = "btn btn-lg btn-success save";
                    $icon = '<i class="fa fa-credit-card"></i>';
                }

                

            //Config action button
                $action = 'href="javascript:{}" onclick="jQuery(this).closest(\'form\').submit();"';
                if (isset($params['no_action']))
                    $action = 'href="javascript:{}"';
                if (isset($params['action']))
                    $action = $params['action'];

                if ($type == 'cancel')
                    $action = 'href="javascript:{}" onclick="history.back(1);"';
            
            if (!isset($params['simple_button']))
            {
                echo '<div class="row '.$aling.'">';
                    echo '<div class="col-md-12 col-sm-12">';
                        echo '<a '.$action.' class="'.$class.'">'.$icon.$label.'</a>';
                    echo '</div>';
                echo '</div>';
            }
            else
            {
                echo '<a '.$action.' class="'.$class.'">'.$icon.$label.'</a>';
            }
        }

        public function buttonIcon($type, $id = "", $params = null, $options = null)
        {
            $plugin = $this->params['plugin'];
            $controller = $this->params['controller'];
            $action = $type;

            if ($id) $action .= '/'.$id;

            if ($params)
            {
                if (isset($params['plugin']))
                    $plugin = $params['plugin'];
                if (isset($params['controller']))
                    $controller = $params['controller'];
                if (isset($params['action']))
                    $action = $params['action'];
            }  

            if ($type == "clone_invoice")
                $icon = 'fa fa-files-o clone';
            if ($type == "edit")
                $icon = 'fa fa-pencil-square edit';
            if ($type == "view")
                $icon = 'fa fa-eye view';
            if ($type == "delete")
                $icon = 'fa fa-times-circle delete';
            if ($type == "done")
                $icon = 'fa fa-check-square yes';
            if ($type == "nodone")
                $icon = 'fa fa-minus-square no';
            if (in_array($type, array('solve', 'nopayed')))
                $icon = 'fa fa-check-square yes';
            if (in_array($type, array('nosolve', 'payed', 'complete')))
                $icon = 'fa fa-minus-square no';
            if ($type == "paid")
                $icon = 'fa fa-check-square yes';
            if ($type == "nopaid")
                $icon = 'fa fa-minus-square no';
            if ($type == "answered")
                $icon = 'fa fa-share-square yes';
            if ($type == "noanswered")
                $icon = 'fa fa-share-square no';
            if (in_array($type, array('resend')))
                $icon = 'fa fa-paper-plane';
            if (in_array($type, array('assign')))
                $icon = 'fa fa-user no';
            if (in_array($type, array('pdf_send')))
                $icon = 'fa fa-paper-plane no';
            if (in_array($type, array('pdf_resend')))
                $icon = 'fa fa-paper-plane yes';
            if (in_array($type, array('pdf_download')))
                $icon = 'fa fa-file-pdf-o';

            return $this->Html->link(
                '<i class="'.$icon.'"></i>',
                array(
                    'plugin' => $plugin,
                    'controller' => $controller,
                    'action' => $action
                ),
                array(
                    'escape' => false, 
                    'class' => 'button_action'
                )
            );



        }
        public function viewTable($data)
        {  
            foreach ($data as $label => $val) {
                echo '<div class="row view">';
                    echo '<div class="form-group">';
                        echo '<label class="col-sm-2 control-label" for="">'.$label.'</label>';
                        echo '<div class="col-sm-10">'.nl2br($val).'</div>';
                    echo '</div>'; 
                echo '</div>';        
            }
        }
        public function toolbar($buttons, $params = null)
        {

            echo '<div class="toolbar text-right">';

                foreach ($buttons as $key => $button) {

                    if ($button['type'] == 'create')
                    {
                        $icon = 'fa fa-plus-square';
                        $action = 'edit';
                    }

                    $plugin = $this->params['plugin'];
                    $controller = $this->params['controller'];

                    if (isset($button['params']))
                    {
                        if (isset($button['params']['plugin']))
                            $plugin = $button['params']['plugin'];
                        if (isset($button['params']['controller']))
                            $controller = $button['params']['controller'];
                        if (isset($button['params']['action']))
                            $action = $button['params']['action'];
                    }   

                    echo $this->Html->link(
                        '<i class="'.$icon.'"></i>',
                        array(
                            'plugin' => $plugin,
                            'controller' => $controller,
                            'action' => $action
                        ),
                        array(
                            'escape' => false, 
                        )
                    );

                    
                }
            echo '</div>';

        }
       
        public function file($files = array(), $model = 'Fichero')      {
                echo '<fieldset>';
                echo '<legend>Ficheros adjuntos</legend>';
                echo $this->Form->button($this->Html->tag('span', '', array(
                        'class' => 'glyphicon glyphicon-plus'
                )) . ' Nuevo fichero', array(
                        'type' => 'button',
                        'class' => 'btn btn-primary pull-right file',
                        'onClick' => 'newFile(this, \'' . $model . '\')'
                ));
                echo '<table class="table table-striped files col-md-12">';
                echo '<thead>';
                echo $this->Html->tableHeaders(array(
                        array(
                                '' => array(
                                        'class' => 'tools'
                                )
                        ) ,
                        array(
                                'Nombre' => array(
                                        'class' => 'name'
                                )
                        ) ,
                        array(
                                'Tamaño' => array(
                                        'class' => 'size'
                                )
                        ) ,
                        'Fecha de subida',
                        array(
                                '' => array(
                                        'class' => 'actions'
                                )
                        )
                ));
                echo '</thead>';
               
                $rows = array();
                $model = array(
                        $this->Html->tag('span', null, array(
                                'class' => 'icon'
                        )) ,
                        array(
                                null,
                                array(
                                        'class' => 'name'
                                )
                        ) ,
                        array(
                                null,
                                array(
                                        'class' => 'size'
                                )
                        ) ,
                        array(
                                null,
                                array(
                                        'class' => 'created'
                                )
                        ) ,
                        array(
                                $this->Html->link($this->Html->tag('span', '', array(
                                        'class' => 'glyphicon glyphicon-download'
                                )) , '', array(
                                        'escape' => false
                                )) . $this->Html->link($this->Html->tag('span', '', array(
                                        'class' => 'glyphicon glyphicon-remove'
                                )) , '', array(
                                        'escape' => false
                                )) ,
                                array(
                                        'class' => 'tools'
                                )
                        )
                );
               
                echo $this->Html->tableCells($model, array(
                        'class' => 'model hidden'
                ));
                foreach ($files as $key => $file) {
                        $force_blank = false;
                        if(in_array($file['tipo'], array('image/png', 'image/jpeg', 'image/gif', 'image/bmp', 'image/tiff'))) $force_blank = true;
                        $rows[] = array(
                                $this->Html->tag('span', null, array(
                                        'class' => 'icon',
                                        'style' => 'background-image: url(' . Router::url("/", false) . 'img/icons/' . $file['extension'] . '.png)'
                                )) ,
                                array(
                                        $file['nombre'],
                                        array(
                                                'class' => 'name'
                                        )
                                ) ,
                                array(
                                        number_format(($file['tamanio'] / 1024) , 2) . ' KB',
                                        array(
                                                'class' => 'size'
                                        )
                                ) ,
                                array(
                                        $file['created'],
                                        array(
                                                'class' => 'created'
                                        )
                                ) ,
                                array(
                                        $this->Html->link($this->Html->tag('span', '', array(
                                                'class' => 'glyphicon glyphicon-download'
                                        )) , $file['url'], array(
                                                'escape' => false,
                                                'target' => $force_blank ? 'blank' : ""
                                        )) . $this->Html->link($this->Html->tag('span', '', array(
                                                'class' => 'glyphicon glyphicon-remove'
                                        )) , '', array(
                                                'escape' => false
                                        )) ,
                                        array(
                                                'class' => 'actions'
                                        )
                                )
                        );
                }
               
                echo $this->Html->tableCells($rows);
                echo '</table>';
                echo '</fieldset>';
        }
       
        public function gallery($model = 'Imagen', $images = array() , $options = array())
        {
               
                $upload_button = $this->Form->button(!empty($options['button_name']) ? $options['button_name'] : 'Nueva imágen', array(
                        'class' => 'btn btn-primary image-uploader',
                        'type' => 'button',
                        'data-inputs_container' => "#{$model}InputsContainer",
                        'data-images_ontiner' => "#{$model}Container"
                ));
                if (!empty($options['help'])) {
                        echo $this->button('question-sign', '', array(
                                'title' => $options['help'],
                                'after' => $upload_button,
                                'div' => 'clearfix text-right image-control'
                        ));
                } else {
                        echo $this->Html->div('clearfix', $upload_button);
                }
                echo $this->Html->div('hidden', '', array(
                        'id' => "{$model}InputsContainer"
                ));
                $thumbnails = array();
                if (empty($this->data['Imagen']) && $this->params['action'] == 'view') {
                        echo $this->Html->div('alert alert-info', $this->Html->tag('p', 'No existen imágenes asociadas.'));
                } else {
                        foreach ($images as $key => $imagen) {
                                $image = $this->Html->image($imagen['url'], array(
                                        'alt' => $imagen['nombre'],
                                        'data-imagen_id' => $imagen['id']
                                ));
                                $caption = $this->Html->div('caption', $this->Html->para(null, $this->Html->tag('strong', 'Nombre: ') . $imagen['nombre']) . $this->Html->para(null, $this->Html->tag('strong', 'Tamaño: ') . number_format($imagen['tamanio'] / 1024, 2, ',', '.') . ' kb'));
                                $button = $this->Form->button('x', array(
                                        'type' => 'button',
                                        'class' => 'close'
                                ));
                                $thumbnail = $this->Html->div('col-sm-5 col-md-4 col-lg-3', $this->Html->div('thumbnail', $image . $caption . $button));
                                $thumbnails[] = $thumbnail;
                        }
                }
                echo $this->Html->div("images-container row", implode($thumbnails) , array(
                        'id' => "#{$model}Container"
                ));
                echo $this->Html->div('clearfix');
        }
}