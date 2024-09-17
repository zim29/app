$(function(){
    /*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
    (function (factory) {
        if (typeof define === 'function' && define.amd) {
            // AMD (Register as an anonymous module)
            define(['jquery'], factory);
        } else if (typeof exports === 'object') {
            // Node/CommonJS
            module.exports = factory(require('jquery'));
        } else {
            // Browser globals
            factory(jQuery);
        }
    }(function ($) {

        var pluses = /\+/g;

        function encode(s) {
            return config.raw ? s : encodeURIComponent(s);
        }

        function decode(s) {
            return config.raw ? s : decodeURIComponent(s);
        }

        function stringifyCookieValue(value) {
            return encode(config.json ? JSON.stringify(value) : String(value));
        }

        function parseCookieValue(s) {
            if (s.indexOf('"') === 0) {
                // This is a quoted cookie as according to RFC2068, unescape...
                s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
            }

            try {
                // Replace server-side written pluses with spaces.
                // If we can't decode the cookie, ignore it, it's unusable.
                // If we can't parse the cookie, ignore it, it's unusable.
                s = decodeURIComponent(s.replace(pluses, ' '));
                return config.json ? JSON.parse(s) : s;
            } catch(e) {}
        }

        function read(s, converter) {
            var value = config.raw ? s : parseCookieValue(s);
            return $.isFunction(converter) ? converter(value) : value;
        }

        var config = $.cookie = function (key, value, options) {

            // Write

            if (arguments.length > 1 && !$.isFunction(value)) {
                options = $.extend({}, config.defaults, options);

                if (typeof options.expires === 'number') {
                    var days = options.expires, t = options.expires = new Date();
                    t.setMilliseconds(t.getMilliseconds() + days * 864e+5);
                }

                return (document.cookie = [
                    encode(key), '=', stringifyCookieValue(value),
                    options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                    options.path    ? '; path=' + options.path : '',
                    options.domain  ? '; domain=' + options.domain : '',
                    options.secure  ? '; secure' : ''
                ].join(''));
            }

            // Read

            var result = key ? undefined : {},
                // To prevent the for loop in the first place assign an empty array
                // in case there are no cookies at all. Also prevents odd result when
                // calling $.cookie().
                cookies = document.cookie ? document.cookie.split('; ') : [],
                i = 0,
                l = cookies.length;

            for (; i < l; i++) {
                var parts = cookies[i].split('='),
                    name = decode(parts.shift()),
                    cookie = parts.join('=');

                if (key === name) {
                    // If second argument (value) is a function it's a converter...
                    result = read(cookie, value);
                    break;
                }

                // Prevent storing a cookie that we couldn't decode.
                if (!key && (cookie = read(cookie)) !== undefined) {
                    result[name] = cookie;
                }
            }

            return result;
        };

        config.defaults = {};

        $.removeCookie = function (key, options) {
            // Must not alter options, thus extending a fresh object...
            $.cookie(key, '', $.extend({}, options, { expires: -1 }));
            return !$.cookie(key);
        };

    }));
    if($('div.gdpr').length) {
        if (typeof $.cookie('gdpr_cookie_marketing') === 'undefined' && typeof $.cookie('gdpr_cookie_statistics') === 'undefined') {
            $('div.gdpr').show();
        } else {
            var input_statistics = $('div.gdpr').find('input[name="statistics"]');
            var input_marketing = $('div.gdpr').find('input[name="marketing"]');

            input_statistics.prop( "checked", false );
            input_marketing.prop( "checked", false );

            if(typeof $.cookie('gdpr_cookie_statistics') !== 'undefined' && $.cookie('gdpr_cookie_statistics') == 'accepted')
                input_statistics.prop( "checked", true );

            if(typeof $.cookie('gdpr_cookie_marketing') !== 'undefined' && $.cookie('gdpr_cookie_marketing') == 'accepted')
                input_marketing.prop( "checked", true );

        }
    }
});

function gdpr_accept_current_setting() {
    var checkbox_statistics = $('div.gdpr').find('input[name="statistics"]');
    var checkbox_marketing = $('div.gdpr').find('input[name="marketing"]');

    if(checkbox_statistics.is(':checked')) {
        document.cookie = "gdpr_cookie_statistics=accepted";
        insert_event('statistics');
    }else {
        document.cookie = "gdpr_cookie_statistics=no-accepted";
    }

    if(checkbox_marketing.is(':checked')) {
        document.cookie = "gdpr_cookie_marketing=accepted";
        insert_event('marketing');
    }else {
        document.cookie = "gdpr_cookie_marketing=no-accepted";
    }

    close_gdpr();
}

function gdpr_configure() {
    $('div.gdpr div.configure').toggle();
}

function close_gdpr() {
    $('div.gdpr').fadeOut('slow', function () {
        $('div.gdpr_button_config').fadeIn('slow');
    });
}

function open_gdpr_configuration() {
    $('div.gdpr_button_config').fadeOut('slow', function () {
        $('div.gdpr').fadeIn('slow', function () {
           if(!$('div.gdpr div.configure').is(":visible"))
               $('div.gdpr div.configure').show();
        });
    });
}

function insert_event(cookie_type) {
    var event_name = cookie_type == 'statistics' ? 'GDPRStatisticsAccepted' : 'GDPRMarketingAccepted';

    if(cookie_type == 'statistics')
        dataLayer.push({
            "gdpr_statistics_status": 'accepted'
        });
    else
        dataLayer.push({
            "gdpr_marketing_status": 'accepted'
        });

    dataLayer.push({
        "event": event_name
    });
}