<?php if(!empty($link)) { ?>
var css_code = `<style>
    div.devman_purchase_bar {
        position: fixed;
        height: 75px;
        background: #040059;
        z-index: 9999;
        width: 100%;
        font-size: 14px;
        color: #fff;
    }
    nav#top{
        padding-top: 75px;
    }
    <?php if (!empty($admin)) { ?>
        header#header {
            padding-top: 75px;
        }
        nav#column-left {
            padding-top: 135px;
        }
    <?php } ?>
    img.logo_devman {
        position: relative;
        float: left;
        width: 80px;
        margin-top: 8px;
    }
    span.purchase_message {
        font-size: 18px;
        line-height: 75px;
        padding-left: 40px;
        position: relative;
        float: left;
    }
    span.purchase_message b {
        color: #ff0d4d;
    }
    a.buton_purchase {
        display: block;
        background: #ff0d4d;
        text-transform: uppercase;
        font-weight: bold;
        color: #fff;
        float: right;
        position: relative;
        padding: 17px 20px;
        margin-top: 10px;
    }

    @media (max-width: 1200px) {
        span.purchase_message {
            font-size: 14px;
        }
        a.buton_purchase {
            padding: 10px 10px;
            margin-top: 17px;
            font-size: 12px;
        }
    }

    @media (max-width: 991px) {
        span.purchase_message {
            font-size: 14px;
            width: 285px;
            line-height: 23px;
            margin-top: 14px;
        }
        a.buton_purchase {
            padding: 10px 10px;
            margin-top: 17px;
            font-size: 12px;
        }
    }

    @media (max-width: 627px) {
        span.purchase_message {
            font-size: 13px;
            padding-left: 20px;
        }
        a.buton_purchase span {
            display: none;
        }
    }

    @media (max-width: 503px) {
        span.purchase_message {
            width: 228px;
        }
        a.buton_purchase span {
            display: none;
        }
    }

    @media (max-width: 455px) {
        img.logo_devman {
            width: 50px;
            margin-top: 20px;
        }
    }

    @media (max-width: 417px) {
        img.logo_devman {
            display: none;
        }
        span.purchase_message {
            padding-left: 0px;
        }
    }

</style>`;

var html_bar_purchase_code = `
    <div class="devman_purchase_bar">
        <div class="container">
            <div class="col-md-12">
                <a href="<?php echo Router::url("/", false); ?>" target="_blank">
                    <?= $this->Html->image('https://devmanextensions.com/img/logo_devman_white.svg', [
                        'alt' => 'Devman logo',
                        'class' => 'logo_devman'
                    ]) ?>
                </a>
                <span class="purchase_message">Buy it now and get <b>EXCLUSIVE DISCOUNTS</b> for future purchases!</span>
                <a class="buton_purchase" target="_blank" href="<?= $link ?>">Buy now <span>in Devman's Store</span></a>
            </div>
        </div>
    </div>
`;
$(function(){
   $('head').append(css_code);
   $('body').prepend(html_bar_purchase_code);
});
<?php } ?>