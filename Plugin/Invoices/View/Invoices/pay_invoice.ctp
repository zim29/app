<article role="main">
	<header class="jumbotron text-center">
	  <h1 class="main"><?= $invoice['Invoice']['payment_method'] == 'Stripe' ? __('Pay invoice') : __('Redirecting to payment page...'); ?></h1>
	 </header>

<?php if ($invoice['Invoice']['payment_method'] == 'Paypal') { ?>
    <form id="paypal_payment" action="<?= $paypal_url ?>" method="post">
        <input type="hidden" name="cmd" value="_cart" />
        <input type="hidden" name="upload" value="1" />
        <input type="hidden" name="business" value="<?= $business_email ?>" />

        <input type="hidden" name="item_name_1" value="<?= $invoice['Invoice']['description']; ?>" />
        <input type="hidden" name="item_number_1" value="" />
        <input type="hidden" name="amount_1" value="<?= $invoice['Invoice']['total']*$invoice['Invoice']['currency_euro_value']; ?>" />
        <input type="hidden" name="quantity_1" value="1" />

        <input type="hidden" name="currency_code" value="EUR" />
        <input type="hidden" name="first_name" value="<?= $invoice['Invoice']['customer_name'] ?>" />
        <input type="hidden" name="address1" value="<?= $invoice['Invoice']['customer_address'] ?>" />
        <input type="hidden" name="city" value="<?= $invoice['Invoice']['customer_city'] ?>" />
        <input type="hidden" name="zip" value="<?= $invoice['Invoice']['customer_post_code'] ?>" />
        <input type="hidden" name="country" value="<?= $invoice['Invoice']['customer_country'] ?>" />
        <input type="hidden" name="address_override" value="0" />
        <input type="hidden" name="email" value="<?= $invoice['Invoice']['customer_email'] ?>" />
        <input type="hidden" name="rm" value="2" />
        <input type="hidden" name="no_note" value="1" />
        <input type="hidden" name="no_shipping" value="1" />
        <input type="hidden" name="charset" value="utf-8" />
        <input type="hidden" name="return" value="https://devmanextensions.com/invoices/invoices/pay_success" />
        <input type="hidden" name="notify_url" value="https://devmanextensions.com/invoices/paypal/callback" />
        <input type="hidden" name="cancel_return" value="https://devmanextensions.com/invoices/paypal/error_in_payment" />
        <input type="hidden" name="paymentaction" value="sale" />
        <input type="hidden" name="custom" value="<?= $invoice['Invoice']['id'] ?>" />

        <?= $sandbox_mode ? '<input type="submit" value="send">' : ''; ?>
    </form>

    <?php if(!$sandbox_mode){ ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $('form#paypal_payment').submit();
            });
        </script>
    <?php } ?>

<?php } elseif ($invoice['Invoice']['payment_method'] == 'Credit Card') { ?>
    <form class="form-horizontal" id="tpv_payment" action="<?= $action; ?>" method="post">
        <input type="hidden" name="Ds_SignatureVersion" value="<?= $version; ?>" />
        <input type="hidden" name="Ds_MerchantParameters" value="<?= $paramsBase64; ?>" />
        <input type="hidden" name="Ds_Signature" value="<?= $signatureMac; ?>" />
    </form>

    <script type="text/javascript">
        $(document).ready(function(){
            $('form#tpv_payment').submit();
        });
    </script>
<?php } elseif ($invoice['Invoice']['payment_method'] == 'Stripe') { ?>
    <?php /*
    <script type="text/javascript">
        $(document).ready(function(){
            $('.stripe-button-el').click();
        });
    </script>
    <center><form id="#stripe" action="<?= $stripe_callback; ?>" method="post">
        <input type="hidden" name="invoice_id" value="<?= $invoice['Invoice']['id']; ?>">
        <input type="hidden" name="invoice_description" value="<?= $invoice['Invoice']['description']; ?>">

        <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
              data-key="<?= $publishable_key; ?>"
              data-name="Devman Extensions"
              data-description="<?= $invoice['Invoice']['description']; ?>"
              data-amount="<?= ($invoice['Invoice']['total']*$invoice['Invoice']['currency_euro_value'])*100; ?>"
              data-panel-label="<?= __('Click to pay'); ?>"
              data-locale="auto"
              data-currency="eur"></script>
    </form></center>
 */ ?>
    <?php

    echo $this->Html->css(
        array(
        'Invoices.Invoice/stripe'
        )
    );

    ?>
    <div class="payment-form-wrapper">
        <form id="payment-form">
            <div id="payment-request-button"></div>

            <fieldset>
                <legend class="card-only"><?= $text_enter_card_detail ?><br>Total <?= $invoice['Invoice']['total'].'$ ('.number_format($invoice['Invoice']['total']*$invoice['Invoice']['currency_euro_value'], 2).'€)' ?></legend>
                <legend class="payment-request-available"><?= $text_or_enter_card_detail ?></legend>
                <div class="container-stripe">
                    <div id="card-element"></div>
                    <button type="button" id="button-confirm" data-secret="<?= $intent['client_secret'] ?>" class="buttons"><?= $text_submit_payment ?></button>
                </div>
            </fieldset>

            <div class="error-stripe" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
                    <path class="base" fill="#000" d="M8.5,17 C3.80557963,17 0,13.1944204 0,8.5 C0,3.80557963 3.80557963,0 8.5,0 C13.1944204,0 17,3.80557963 17,8.5 C17,13.1944204 13.1944204,17 8.5,17 Z"></path>
                    <path class="glyph" fill="#FFF" d="M8.5,7.29791847 L6.12604076,4.92395924 C5.79409512,4.59201359 5.25590488,4.59201359 4.92395924,4.92395924 C4.59201359,5.25590488 4.59201359,5.79409512 4.92395924,6.12604076 L7.29791847,8.5 L4.92395924,10.8739592 C4.59201359,11.2059049 4.59201359,11.7440951 4.92395924,12.0760408 C5.25590488,12.4079864 5.79409512,12.4079864 6.12604076,12.0760408 L8.5,9.70208153 L10.8739592,12.0760408 C11.2059049,12.4079864 11.7440951,12.4079864 12.0760408,12.0760408 C12.4079864,11.7440951 12.4079864,11.2059049 12.0760408,10.8739592 L9.70208153,8.5 L12.0760408,6.12604076 C12.4079864,5.79409512 12.4079864,5.25590488 12.0760408,4.92395924 C11.7440951,4.59201359 11.2059049,4.59201359 10.8739592,4.92395924 L8.5,7.29791847 L8.5,7.29791847 Z"></path>
                </svg>
                <span id="card-errors" class="message"></span>
            </div>
        </form>

        <div class="success-stripe">
            <div class="icon">
                <svg width="84px" height="84px" viewBox="0 0 84 84" version="1.1" xmlns="http://www.w3.org/2000/svg" xlink="http://www.w3.org/1999/xlink">
                    <circle class="border" cx="42" cy="42" r="40" stroke-linecap="round" stroke-width="4" stroke="#000" fill="none"></circle>
                </svg>
            </div>
        </div>
    </div>

    <?php /*<div class="payment-form-wrapper type-checkout">
        <form id="payment-form">
            <button type="button" id="button-confirm" class="buttons">Submit payment</button>
        </form>
    </div> */ ?>

    <script type="text/javascript" src="https://js.stripe.com/v3/"></script>

    <script type="text/javascript">
        var stripe = null;

        function finishPayment(data) {
            $.ajax({
                url: '<?= $callback_url ?>',
                type: 'post',
                data: data,
                dataType: 'json',
                complete: function() {
                },
                success: function(json) {
                    var error = json.error;
                    console.log(json);
                    if(!error) {
                        window.location.href = json.redirect;
                    }
                    else {
                        errorElement.textContent = errorElement;
                        errorElement.addClass('visible');
                        $('.payment-form-wrapper').removeClass('submitting');
                    }

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(result.error);
                    // Inform the user if there was an error
                    show_error(xhr.responseText);
                }
            });
        }

        function show_error(message) {
            var errorElement = $('#card-errors');
            errorElement.html(message);
            errorElement.closest('div.error-stripe').addClass('visible');
            $('.payment-form-wrapper').removeClass('submitting');

        }


        function initTypePaymentForm() {
            var elements = stripe.elements();
            var style = {
                base: {
                    color: "#32325D",
                    fontWeight: 500,
                    fontFamily: "Inter UI, Open Sans, Segoe UI, sans-serif",
                    fontSize: "15px",
                    fontSmoothing: "antialiased",
                    "::placeholder": {
                        color: "#CFD7DF"
                    }
                },
                invalid: {
                    color: "#E25950"
                }
            };
            var card = elements.create('card', {style: style, hidePostalCode: true});
            card.mount('#card-element');

            // Handle real-time validation errors from the card Element.
            card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                    $('.payment-form-wrapper .error-stripe').addClass('visible');
                } else {
                    $('.payment-form-wrapper .error-stripe').removeClass('visible');
                    displayError.textContent = '';
                }
            });

            // Handle form submission
            var buttonConfirm = document.getElementById('button-confirm');
            buttonConfirm.addEventListener('click', function(event) {
                $('.payment-form-wrapper').addClass('submitting');

                var clientSecret = buttonConfirm.getAttribute('data-secret');
                var billingInfo = '<?= str_replace("'", "\'", json_encode($billing)) ?>';
                stripe.handleCardPayment(
                    clientSecret, card, {
                        payment_method_data: {
                            billing_details: billingInfo,
                        },
                    }
                ).then(function(result) {
                    if (result.error) {
                        console.log(result.error);
                        // Inform the user if there was an error
                        show_error(result.error.message);
                    } else {
                        // The payment has succeeded. Display a success message.
                        finishPayment(result);
                    }
                });
            });



            // Apple Pay
            if (!$('html').hasClass('quick-checkout-page')) {
                var paymentRequest = stripe.paymentRequest({
                    country: 'ES',
                    requestPayerName: true,
                    requestPayerEmail: true,
                    currency: 'EUR',
                    total: {
                        label: 'Total',
                        amount: parseInt('<?= $amount ?>'),
                    },
                });
                paymentRequest.canMakePayment().then(function (result) {
                    if (result) {
                        $(".card-only").hide();
                        $(".payment-request-available").show();
                        var prButton = elements.create('paymentRequestButton', {
                            paymentRequest: paymentRequest,
                        });
                        prButton.mount('#payment-request-button');
                    } else {    // apple pay is not available
                        document.getElementById('payment-request-button').style.display = 'none';
                    }
                });

                paymentRequest.on('source', function(ev) {
                    var clientSecret = $('#button-confirm').data('secret');
                    stripe.confirmPaymentIntent(clientSecret, {
                        source: ev.source.id,
                        use_stripe_sdk: true
                    }).then(function(confirmResult) {
                        if (confirmResult.error) {
                            ev.complete('fail');
                            $('.payment-form-wrapper').removeClass('submitting');
                        } else {
                            ev.complete('success');
                            finishPayment(confirmResult);
                        }
                    });
                });
            }
        }



        function initStripe() {
            if (window.Stripe) {
                stripe = Stripe('<?= $payment_stripepro_public_key ?>');
                //initTypeCheckoutPage();
                initTypePaymentForm();
            } else {
                setTimeout(function() { initStripe() }, 50);
            }
        }
        initStripe();
    </script>

<?php } ?>
</article>
