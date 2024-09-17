<?php
	class GoogleConsentController extends AppController
    {


        public function beforeFilter()
        {
            $this->Auth->allow('get_cookie_code');
        }

        function get_cookie_code()
        {
            /*
                Variables POST que recibe este método desde el .php que el cliente sube en su raíz:

                - domr: Dominio desde donde se hace la petición
                - doml: El "identificador" interno que usaremos nostors por cada dominio contratado
            */

            $array_return = array(
                'error' => false,
                'error_message' => '',
                'js_code' => ''
            );

            //TODO - Antes de nada, aquí se hará la comprobación de que domr y doml están relacionados y la suscripción está en regla
            //TODO - si hay algún error, ponemos "error" a true y rellenamos el "error_message".
                /*
                $array_return['error'] = true;
                $array_return['error_message'] = 'Google Consent For Dummies: Error - subscription expired or invalid ID or domain.';

                header('Content-Type: application/json');
                echo json_encode($array_return);
                die;*/



            //Si está correcto, continuamos generando el código JS, obtenemos los settings de ese dominio (los dejo aquí a pelo para hacer pruebas)
                $settings = array(
                    'GCFD_BUTTON_CONFIGURATION' => 'true',
                    'GCFD_CONSENT_POPUP_POSITION' => 'middle',
                    'GCFD_CONSENT_POPUP_BUTTON_CONFIG_POSITION' => 'bottom_left',
                    'GCFD_MAIN_COLOR' => '229ac8',
                    'GCFD_CONFIGURATION_BUTTON_COLOR' => 'daebf3',
                    'GCFD_POPUP_BACKGROUND' => 'fff',
                    'GCFD_POPUP_FONT_COLOR' => '000',
                    'GCFD_BUTTON_ACCEPT_FONT_COLOR' => 'fff',
                    'GCFD_CONFIGURATION_BUTTON_SVG_COLOR' => 'fff',
                    'GCFD_MORE_INFORMATION_PAGE' => 'https://www.redosar.com/politica-de-cookies-ue',
                    'GCFD_LANGUAGES' => array(
                        /*'en' => array(
                            'popup_title' => 'Cookies law policy',
                            'popup_explain' => 'Some of these cookies are essential, while others help us to improve your experience by providing insights into how the site is being used.',
                            'link_title' => 'More information',
                            'link_href' => 'mylink_href',
                            'button_accept' => 'ACCEPT',
                            'button_configure_title' => 'Configure cookies',
                            'button_configure' => 'CONFIGURATION',
                            'statistics_label' => 'Statistics',
                            'statistics_explain' => 'Enables storage (such as cookies) related to analytics e.g. visit duration',
                            'marketing_label' => 'Marketing',
                            'marketing_explain' => 'Enables storage (such as cookies) related to advertising',
                            'functionality_label' => 'Functionality',
                            'functionality_explain' => 'Enables storage that supports the functionality of the website or app e.g. language settings',
                            'personalization_label' => 'Personalization',
                            'personalization_explain' => 'Enables storage related to personalization e.g. video recommendations',
                            'secutiry_label' => 'Security',
                            'secutiry_explain' => 'Enables storage related to security such as authentication functionality, fraud prevention, and other user protection',
                        ),*/
                        'es' => array(
                            'popup_title' => 'Ley de cookies',
                            'popup_explain' => 'Algunas de estas cookies son esenciales, mientras que otras nos ayudan a mejorar su experiencia al proporcionar información sobre cómo se utiliza el sitio.',
                            'link_title' => 'Más información',
                            'link_href' => 'https://www.redosar.com/politica-de-cookies-ue',
                            'button_accept' => 'ACEPTO',
                            'button_configure_title' => 'Confirgurar cookies',
                            'button_configure' => 'Configuración',
                            'statistics_label' => 'Estadísticas',
                            'statistics_explain' => 'Habilita el almacenamiento (como cookies) relacionado con análisis, p. duración de la visita',
                            'marketing_label' => 'Marketing',
                            'marketing_explain' => 'Habilita el almacenamiento (como las cookies) relacionado con la publicidad',
                            'functionality_label' => 'Funcionalidad',
                            'functionality_explain' => 'Habilita el almacenamiento que admite la funcionalidad del sitio web o la aplicación, p. Opciones de lenguaje',
                            'personalization_label' => 'Personalización',
                            'personalization_explain' => 'Habilita el almacenamiento relacionado con la personalización, p. recomendaciones de videos',
                            'secutiry_label' => 'Seguridad',
                            'secutiry_explain' => 'Permite el almacenamiento relacionado con la seguridad, como la funcionalidad de autenticación, la prevención del fraude y otra protección del usuario.',
                        )
                    )
                                    );
                $array_return['js_code'] = $this->get_js_code($settings);

            header('Content-Type: application/json');
            echo json_encode($array_return);
            die;
        }

        public function get_js_code($settings)
        {
            /*
                Aquí tendríamos que reemplazar las variables de configuración del código JS por la configuración que el usuario puso en este dominio.

                BUTTON_CONFIGURATION (true/false)
                GCFD_CONSENT_POPUP_POSITION (middle/bottom_left/bottom_right/top_left/top_right)
                GCFD_CONSENT_POPUP_BUTTON_CONFIG_POSITION (bottom_left/bottom_right/top_left/top_right)
                GCFD_MAIN_COLOR (RGB, ejemplo: daebf3)
                GCFD_CONFIGURATION_BUTTON_COLOR (RGB, ejemplo: daebf3)
                GCFD_POPUP_BACKGROUND (RGB, ejemplo: daebf3)
                GCFD_POPUP_FONT_COLOR (RGB, ejemplo: daebf3)
                GCFD_BUTTON_ACCEPT_FONT_COLOR (RGB, ejemplo: daebf3)
                GCFD_CONFIGURATION_BUTTON_SVG_COLOR (RGB, ejemplo: daebf3)
                GCFD_LANGUAGES (json_encode de los idiomas, dejo un ejemplo abajo):
                {
                    "en": {
                        "popup_title": "Cookies law policy",
                        "popup_explain": "Some of these cookies are essential, while others help us to improve your experience by providing insights into how the site is being used.",
                        "link_title": "More information",
                        "link_href": "mylink_href",
                        "button_accept": "ACCEPT ALL",
                        "button_configure_title": "Configure cookies",
                        "button_configure": "CONFIGURATION",
                        "statistics_label": "Statistics",
                        "statistics_explain": "Enables storage (such as cookies) related to analytics e.g. visit duration",
                        "marketing_label": "Marketing",
                        "marketing_explain": "Enables storage (such as cookies) related to advertising",
                        "functionality_label": "Functionality",
                        "functionality_explain": "Enables storage that supports the functionality of the website or app e.g. language settings",
                        "personalization_label": "Personalization",
                        "personalization_explain": "Enables storage related to personalization e.g. video recommendations",
                        "secutiry_label": "Security",
                        "secutiry_explain": "Enables storage related to security such as authentication functionality, fraud prevention, and other user protection",
                    }
                }
             */

            $js_code = "
                !function(e,t){\"object\"==typeof exports&&\"undefined\"!=typeof module?module.exports=t():\"function\"==typeof define&&define.amd?define(t):(e=e||self,function(){var n=e.GCFDCookies,o=e.GCFDCookies=t();o.noConflict=function(){return e.GCFDCookies=n,o}}())}(this,(function(){\"use strict\";function e(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var o in n)e[o]=n[o]}return e}return function t(n,o){function r(t,r,i){if(\"undefined\"!=typeof document){\"number\"==typeof(i=e({},o,i)).expires&&(i.expires=new Date(Date.now()+864e5*i.expires)),i.expires&&(i.expires=i.expires.toUTCString()),t=encodeURIComponent(t).replace(/%(2[346B]|5E|60|7C)/g,decodeURIComponent).replace(/[()]/g,escape);var c=\"\";for(var u in i)i[u]&&(c+=\"; \"+u,!0!==i[u]&&(c+=\"=\"+i[u].split(\";\")[0]));return document.cookie=t+\"=\"+n.write(r,t)+c}}return Object.create({set:r,get:function(e){if(\"undefined\"!=typeof document&&(!arguments.length||e)){for(var t=document.cookie?document.cookie.split(\"; \"):[],o={},r=0;r<t.length;r++){var i=t[r].split(\"=\"),c=i.slice(1).join(\"=\");try{var u=decodeURIComponent(i[0]);if(o[u]=n.read(c,u),e===u)break}catch(e){}}return e?o[e]:o}},remove:function(t,n){r(t,\"\",e({},n,{expires:-1}))},withAttributes:function(n){return t(this.converter,e({},this.attributes,n))},withConverter:function(n){return t(e({},this.converter,n),this.attributes)}},{attributes:{value:Object.freeze(o)},converter:{value:Object.freeze(n)}})}({read:function(e){return'\"'===e[0]&&(e=e.slice(1,-1)),e.replace(/(%[\dA-F]{2})+/gi,decodeURIComponent)},write:function(e){return encodeURIComponent(e).replace(/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,decodeURIComponent)}},{path:\"/\"})}));
            
                var cookie_names = [
                    'analytics_storage',
                    'ad_storage',
                    'functionality_storage',
                    'personalization_storage',
                    'security_storage'
                ];
            
                /*Setting variables*/
                    var gcfd_button_configuration = GCFD_BUTTON_CONFIGURATION;
                    var gcfd_consent_popup_position = 'GCFD_CONSENT_POPUP_POSITION';
                    var gcfd_consent_popup_button_config_position = 'GCFD_CONSENT_POPUP_BUTTON_CONFIG_POSITION';
                    var gcfd_main_color = 'GCFD_MAIN_COLOR';
                    var gcfd_configuration_button_svg_color = 'GCFD_CONFIGURATION_BUTTON_SVG_COLOR';
                    var gcfd_configuration_button_color = 'GCFD_CONFIGURATION_BUTTON_COLOR';
                    var gcfd_popup_background = 'GCFD_POPUP_BACKGROUND';
                    var gcfd_popup_font_color = 'GCFD_POPUP_FONT_COLOR';
                    var gcfd_button_accept_font_color = 'GCFD_BUTTON_ACCEPT_FONT_COLOR';
                    var gcfd_languages = GCFD_LANGUAGES;
                    var gcfd_more_information_page = 'GCFD_MORE_INFORMATION_PAGE';
                   
                /*END Setting variables*/
                document.addEventListener(\"DOMContentLoaded\", function(event) {
                    if(window.location.href != gcfd_more_information_page) {
                        gcfd_setup_styles();
                        gcfd_setup_popup();
                        gcfd_check_consent();
                    }
                });
                
                window.addEventListener('resize', check_consent_popup_height);
           
                function gcfd_check_consent() {
                    if (GCFDCookies.get(\"acceptedGoogleConsent\") != 'Yes') {
                        open_consent_popup();
                    } else {
                        show_configure_button();
                        document.querySelector('div.gcfd_block_screen').style.display = \"none\";
                        $.each(cookie_names, function( index, cookie_name ) {
                            var input = document.querySelector('div.gcfd_consent_popup input[name=\"'+cookie_name+'\"]');
                            input.checked = false;
            
                            if(typeof GCFDCookies.get(cookie_name) !== 'undefined' && GCFDCookies.get(cookie_name) == 'granted')
                                input.checked = true;
                        });
                    }
                }
            
                function accept_current_setting() {
                    $.each(cookie_names, function( index, cookie_name ) {
                        GCFDCookies.remove(cookie_name);
                        var checkbox_cookie = document.querySelector('div.gcfd_configure_panel input[name=\"'+cookie_name+'\"]');
                        if(checkbox_cookie.checked) {
                            GCFDCookies.set(cookie_name, 'granted');
                        }else {
                            GCFDCookies.set(cookie_name, 'denied');
                        }
                    });
            
                    event_update_google_consent();
                    close_consent_popup();
                }
            
                function close_consent_popup() {
                    document.querySelector('div.gcfd_block_screen').style.display = \"none\";
                    document.querySelector('div.gcfd_consent_popup').style.display = \"none\";
                    show_configure_button();
                }
                function open_consent_popup(open_settings) {
                    open_settings = typeof open_settings == 'undefined' ? false : true;
                    var popup = document.querySelector('div.gcfd_consent_popup');
                    popup.classList.add(\"gcfd_\"+gcfd_consent_popup_position);
                    
                    if(gcfd_consent_popup_position == 'middle')
                        document.querySelector('div.gcfd_block_screen').style.display = \"block\";
                        
                    document.querySelector('div.gcfd_consent_popup').style.display = \"block\";
                    if(open_settings)
                        document.querySelector('div.gcfd_configure_panel').style.display = \"block\";
                    close_gdpr_button();
                    check_consent_popup_height();
                }
                function check_consent_popup_height() {
                    var consent_popup = document.querySelector('div.gcfd_consent_popup');
                    var popup_height = consent_popup.offsetHeight;
                    var window_height = window.innerHeight;
                    consent_popup.style.height = \"auto\";
                    if(popup_height > window_height) {
                        var new_height = window_height-40;
                        consent_popup.style.height = new_height+\"px\";
                        consent_popup.style[\"overflow-y\"] = \"scroll\";
                    } else {
                        consent_popup.style.height = \"auto\";
                        consent_popup.style[\"overflow-y\"] = \"visible\";
                    }
                }
                function close_gdpr_button() {
                    document.querySelector('div.gcfd_consent_popup_button_config').style.display = \"none\";
                }
                function show_configure_button() {
                    if(gcfd_button_configuration) {
                        document.querySelector('div.gcfd_consent_popup_button_config').classList.add(\"gcfd_\"+gcfd_consent_popup_button_config_position);
                        document.querySelector('div.gcfd_consent_popup_button_config').style.display = \"block\";
                    }
                }
                function configure_gdpr() {
                    var x = document.querySelector('div.gcfd_configure_panel');
                    if (x.style.display === \"none\") x.style.display = \"block\";
                    else x.style.display = \"none\";
                    check_consent_popup_height();
                }
                function event_update_google_consent() {
                    GCFDCookies.set('acceptedGoogleConsent', 'Yes');
            
                    if(typeof dataLayer == 'undefined')
                        console.error(\"Google Consent For Dummies: Error - Google tag manager tag not found.\");
            
                    dataLayer.push({
                        \"event\" : \"googleConsentUpdate\",
                        \"googleConsentUpdate\" : {
                          ad_storage: GCFDCookies.get(\"ad_storage\"),
                          analytics_storage: GCFDCookies.get(\"analytics_storage\"),
                          functionality_storage: GCFDCookies.get(\"functionality_storage\"),
                          personalization_storage: GCFDCookies.get(\"personalization_storage\"),
                          security_storage: GCFDCookies.get(\"security_storage\")
                        }
                    });
                }
            
                function gcfd_setup_popup() {
                    var popup_code = `<div class=\"gcfd_block_screen\"></div><div class=\"gcfd_consent_popup_button_config\" onclick=\"open_consent_popup(true);\" title=\"gcfd_lang_button_configure_title\" style=\"display: none;\"><svg aria-hidden=\"true\" focusable=\"false\" data-prefix=\"fas\" data-icon=\"cookie-bite\" class=\"svg-inline--fa fa-cookie-bite fa-w-16\" role=\"img\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path fill=\"currentColor\" d=\"M510.52 255.82c-69.97-.85-126.47-57.69-126.47-127.86-70.17 0-127-56.49-127.86-126.45-27.26-4.14-55.13.3-79.72 12.82l-69.13 35.22a132.221 132.221 0 0 0-57.79 57.81l-35.1 68.88a132.645 132.645 0 0 0-12.82 80.95l12.08 76.27a132.521 132.521 0 0 0 37.16 72.96l54.77 54.76a132.036 132.036 0 0 0 72.71 37.06l76.71 12.15c27.51 4.36 55.7-.11 80.53-12.76l69.13-35.21a132.273 132.273 0 0 0 57.79-57.81l35.1-68.88c12.56-24.64 17.01-52.58 12.91-79.91zM176 368c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm32-160c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm160 128c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32z\"></path></svg></div>
                                <div class=\"gcfd_consent_popup\" style=\"display: none;\">
                                    <span class=\"gcfd_title\">gcfd_lang_popup_title</span>
                                    <span class=\"gcfd_description\">gcfd_lang_popup_explain</span>
                                    <a class=\"gcfd_link\" href=\"gcfd_lang_link_href\">gcfd_lang_link_title</a>
                                    <div style=\"clear: both;\"></div>
                                    <span class=\"gcfd_button gcfd_accept\" onclick=\"accept_current_setting();\">gcfd_lang_button_accept</span>
                                    <span class=\"gcfd_button gcfd_configuration\" onclick=\"configure_gdpr();\">gcfd_lang_button_configure</span>
            
                                    <div class=\"gcfd_configure_panel\" style=\"display: none;\">
                                        <label class=\"gcfd_checkbox_container\">
                                            <div class=\"gcfd_api_text\">
                                                <label>gcfd_lang_statistics_label</label>
                                                <p>gcfd_lang_statistics_explain</p>
                                            </div>
                                            <div class=\"gdpr_checkbox\">
                                                <input name=\"analytics_storage\" type=\"checkbox\" class=\"gcfd_checkbox\" checked=\"checked\">
                                                <div><div></div></div>
                                            </div>
                                        </label>
            
                                        <label class=\"gcfd_checkbox_container\">
                                            <div class=\"gcfd_api_text\">
                                                <label>gcfd_lang_marketing_label</label>
                                                <p>gcfd_lang_marketing_explain</p>
                                            </div>
                                            <div class=\"gdpr_checkbox\">
                                                <input name=\"ad_storage\" type=\"checkbox\" class=\"gcfd_checkbox\" checked=\"checked\">
                                                <div><div></div></div>
                                            </div>
                                        </label>
            
                                        <label class=\"gcfd_checkbox_container\">
                                            <div class=\"gcfd_api_text\">
                                                <label>gcfd_lang_functionality_label</label>
                                                <p>gcfd_lang_functionality_explain</p>
                                            </div>
                                            <div class=\"gdpr_checkbox\">
                                                <input name=\"functionality_storage\" type=\"checkbox\" class=\"gcfd_checkbox\" checked=\"checked\">
                                                <div><div></div></div>
                                            </div>
                                        </label>
            
                                        <label class=\"gcfd_checkbox_container\">
                                            <div class=\"gcfd_api_text\">
                                                <label>gcfd_lang_personalization_label</label>
                                                <p>gcfd_lang_personalization_explain</p>
                                            </div>
                                            <div class=\"gdpr_checkbox\">
                                                <input name=\"personalization_storage\" type=\"checkbox\" class=\"gcfd_checkbox\" checked=\"checked\">
                                                <div><div></div></div>
                                            </div>
                                        </label>
            
                                        <label class=\"gcfd_checkbox_container\">
                                            <div class=\"gcfd_api_text\">
                                                <label>gcfd_lang_secutiry_label</label>
                                                <p>gcfd_lang_secutiry_explain</p>
                                            </div>
                                            <div class=\"gdpr_checkbox\">
                                                <input name=\"security_storage\" type=\"checkbox\" class=\"gcfd_checkbox\" checked=\"checked\">
                                                <div><div></div></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>`;
            
                    var language_code = document.documentElement.lang;
            
                    if(typeof language_code == 'undefined' || typeof gcfd_languages[language_code] == \"undefined\")
                        language_code = Object.keys(gcfd_languages)[0];
                   
                    lang_variables = gcfd_languages[language_code];
            
                    for (var [key, value] of Object.entries(lang_variables)) {
                        key = 'gcfd_lang_'+key;
                        popup_code = popup_code.replace(key, value);
                    }
                    document.getElementsByTagName(\"body\")[0].innerHTML += popup_code;
                }
                function gcfd_setup_styles() {
                    var styles_code = `<style>
                            :root {
                                --gcfd_main_color: #`+gcfd_main_color+`;
                                --gcfd_configuration_button_color: #`+gcfd_configuration_button_color+`;
                                --gcfd_popup_background: #`+gcfd_popup_background+`;
                                --gcfd_popup_font_color: #`+gcfd_popup_font_color+`;
                                --gcfd_button_accept_font_color: #`+gcfd_button_accept_font_color+`;
                                --gcfd_configuration_button_svg_color: #`+gcfd_configuration_button_svg_color+`;
                            }
                            div.gcfd_block_screen {
                                position: fixed;
                                left: 0px;
                                top: 0px;
                                height: 100%;
                                width: 100%;
                                z-index: 9999999998;
                                background: rgba(0, 0, 0, 0.30)
                            }
                            div.gcfd_consent_popup {
                                padding: 25px;
                                border-radius: 4px;
                                max-width: 450px;
                                background: var(--gcfd_popup_background);
                                font-size: 12px;
                                position: fixed;
                                z-index: 9999999999;
                                box-shadow: 0px 0px 7px rgb(0 0 0 / 30%);
                                color: var(--gcfd_popup_font_color);
                                border-top: 8px solid var(--gcfd_main_color);
                            }
                            
                            div.gcfd_consent_popup.gcfd_middle {
                                top: 50%;
                                left: 50%;
                                margin-right: -50%;
                                transform: translate(-50%, -50%);
                            }
            
                            div.gcfd_consent_popup p {
                                margin: 0px;
                            }
            
                            div.gcfd_consent_popup.gcfd_top_left {
                                top: 10px;
                                left: 10px;
                            }
                            div.gcfd_consent_popup.gcfd_top_right {
                                top: 10px;
                                right: 10px;
                            }
                            div.gcfd_consent_popup.gcfd_bottom_left {
                                bottom: 10px;
                                left: 10px;
                            }
                            div.gcfd_consent_popup.gcfd_bottom_right {
                                bottom: 10px;
                                right: 10px;
                            }
            
                            span.gcfd_title {
                                font-size: 16px;
                                font-weight: bold;
                                width: 100%;
                                position: relative;
                                float: left;
                                margin-bottom: 6px;
                            }
                            span.gcfd_description {
                                line-height: 14px;
                                display: block;
                                margin: 10px 0px;
                            }
                            a.gcfd_link {
                                border-bottom: 2px solid;
                                color: var(--gcfd_main_color);
                                font-weight: bold;
                                display: block;
                                float: left;
                            }
            
                            span.gcfd_button {
                                display: block;
                                border-radius: 4px;
                                text-align: center;
                                width: 100%;
                                padding-top: 14px;
                                padding-bottom: 14px;
                                font-weight: bold;
                                font-size: 13px;
                            }
                            span.gcfd_button:hover {
                                cursor: pointer;
                            }
                            span.gcfd_button.gcfd_accept {
                                background: var(--gcfd_main_color);
                                color: var(--gcfd_button_accept_font_color);
                                margin: 15px 0px 8px 0px;
                            }
            
                            span.gcfd_button.gcfd_configuration {
                                background: var(--gcfd_configuration_button_color);
                            }
            
                            div.gcfd_api_text,div.gdpr_checkbox{
                                position: relative;
                                float: left;
                            }
                            div.gcfd_api_text {
                                padding-right: 5%;
                                width: 90%;
                                text-align: left;
                            }
            
                            div.gdpr_checkbox {
                                width: 10%;
                            }
            
                            /* Custom checkboxes*/
                                label.gcfd_checkbox_container {
                                    position: relative;
                                    float: left;
                                    width: 100%;
                                    margin-bottom: 0px;
                                    padding-top: 7px;
                                    border-bottom: 1px solid var(--gcfd_main_color);
                                    padding-bottom: 12px;
                                }
            
                                label.gcfd_checkbox_container:first-child {
                                    border-top: 1px solid var(--gcfd_main_color);
                                    margin-top: 15px;
                                }
            
                                label.gcfd_checkbox_container:hover
                                {
                                    cursor: pointer;
                                }
            
                                label.gcfd_checkbox_container label{
                                    padding: 0px;
                                    margin: 0px;
                                    line-height: 10px;
                                    font-size: 13px;
                                    font-weight: bold;
                                    margin-bottom: 5px;
                                }
                                label.gcfd_checkbox_container div.text_info{
                                    font-size: 12px;
                                }
                                label.gcfd_checkbox_container input[type=\"checkbox\"] {
                                    position: absolute;
                                    opacity: 0;
                                    left: -28px;
                                }
            
                                input[type=\"checkbox\"].gcfd_checkbox + div {
                                    vertical-align: middle;
                                    width: 40px;
                                    height: 20px;
                                    border: 1px solid rgba(0,0,0,.4);
                                    border-radius: 999px;
                                    background-color: rgba(0, 0, 0, 0.1);
                                    -webkit-transition-duration: .4s;
                                    -webkit-transition-property: background-color, box-shadow;
                                    box-shadow: inset 0 0 0 0px rgba(0,0,0,0.4);
                                    margin-top: 3px;
                                    float: right;
                                }
            
                                input[type=\"checkbox\"].gcfd_checkbox:checked + div {
                                    width: 40px;
                                    background-position: 0 0;
                                    background-color: var(--gcfd_main_color);
                                    border: 1px solid var(--gcfd_main_color);
                                    box-shadow: inset 0 0 0 10px var(--gcfd_main_color);
                                }
            
                                label.gcfd_checkbox_container.disabled {
                                    opacity: 0.3;
                                    cursor: inherit !important;
                                }
            
                                input[type=\"checkbox\"].gcfd_checkbox + div > div {
                                    float: left;
                                    width: 18px;
                                    height: 18px;
                                    border-radius: inherit;
                                    background: #fff;
                                    -webkit-transition-timing-function: cubic-bezier(.54,1.85,.5,1);
                                    -webkit-transition-duration: 0.4s;
                                    -webkit-transition-property: transform, background-color, box-shadow;
                                    -moz-transition-timing-function: cubic-bezier(.54,1.85,.5,1);
                                    -moz-transition-duration: 0.4s;
                                    -moz-transition-property: transform, background-color;
                                    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3), 0px 0px 0 1px rgba(0, 0, 0, 0.4);
                                    pointer-events: none;
                                    margin-top: 0px;
                                    margin-left: 1px;
                                }
            
                                input[type=\"checkbox\"].gcfd_checkbox:checked + div > div {
                                    -webkit-transform: translate3d(20px, 0, 0);
                                    -moz-transform: translate3d(20px, 0, 0);
                                    background-color: #fff;
                                    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3), 0px 0px 0 1px rgba(8, 80, 172,1);
                                }
                            /* Custom checkboxes*/
            
                            /* Config button*/
                                div.gcfd_consent_popup_button_config {
                                    width: 0;
                                    height: 0;
                                    position: fixed;
                                    z-index: 9999999;
                                }
                                div.gcfd_consent_popup_button_config:hover {
                                    cursor: pointer;
                                }
            
                                div.gcfd_consent_popup_button_config svg {
                                    width: 26px;
                                    height: 26px;
                                    position: relative;
                                    color: var(--gcfd_configuration_button_svg_color);
                                }
            
                                div.gcfd_consent_popup_button_config.gcfd_bottom_left svg, div.gcfd_consent_popup_button_config.gcfd_bottom_right svg {
                                    top: 39px;
                                }
                                div.gcfd_consent_popup_button_config.gcfd_bottom_left svg {
                                    left: 11px;
                                }
                                div.gcfd_consent_popup_button_config.gcfd_bottom_right svg {
                                    right: 30px;
                                }
                                div.gcfd_consent_popup_button_config.gcfd_top_left svg, div.gcfd_consent_popup_button_config.gcfd_top_right svg {
                                    bottom: 68px;
                                }
                                div.gcfd_consent_popup_button_config.gcfd_top_left svg {
                                    left: 7px;
                                }
                                div.gcfd_consent_popup_button_config.gcfd_top_right svg {
                                    right: 31px;
                                }
            
                                div.gcfd_consent_popup_button_config.gcfd_top_left,
                                div.gcfd_consent_popup_button_config.gcfd_top_right {
                                    border-top: 75px solid var(--gcfd_main_color);
                                    top: 0px;
                                }
            
                                div.gcfd_consent_popup_button_config.gcfd_top_left{
                                    border-right: 75px solid transparent;
                                    left: 0px;
                                }
            
                                div.gcfd_consent_popup_button_config.gcfd_top_right{
                                    border-left: 75px solid transparent;
                                    right: 0px;
                                }
            
                                div.gcfd_consent_popup_button_config.gcfd_bottom_left,
                                div.gcfd_consent_popup_button_config.gcfd_bottom_right {
                                    border-bottom: 75px solid var(--gcfd_main_color);
                                    bottom: 0px;
                                }
            
                                div.gcfd_consent_popup_button_config.gcfd_bottom_left{
                                    border-right: 75px solid transparent;
                                    left: 0px;
                                }
            
            
                                div.gcfd_consent_popup_button_config.gcfd_bottom_right{
                                    border-left: 75px solid transparent;
                                    right: 0px;
                                }
                            /* Config button*/
                        </style>`;
                    document.getElementsByTagName(\"head\")[0].innerHTML += styles_code;
                }";

            foreach ($settings as $key_setting => $value) {
                $js_code = str_replace($key_setting, is_array($value) ? json_encode($value) : $value, $js_code);
            }
            $js_code = str_replace(array("\r", "\n", "\t"), '', $js_code);

            return $js_code;
        }
    }
?>