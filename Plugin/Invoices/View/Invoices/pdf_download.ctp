<?php
//////////////////////////////////////////////
?>
<htmlpageheader name="myheader">
    <p style="text-align: right;">
        <img src="https://devmanextensions.com/img/layouts/administration/invoices/invoices_devman_logo.jpg" style="height: 150px;">
    <?php //echo $this->Html->image("layouts/administration/invoices/invoices_devman_logo.jpg", array("alt" => "alt-tag", 'style' => 'height: 150px;')); ?>
    </p>
</htmlpageheader>
<sethtmlpageheader name="myheader" value="on"  show-this-page="1" />
<htmlpagefooter name="myfooter">
    <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center;   padding-top: 3mm; ">
        Page {PAGENO} of {nb}
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="myfooter" value="on" />
<?php
//////////////////////////////////////////////
?>
<table border="0" cellpadding="1" cellspacing="1" summary="" width="100%">
    <tbody>
        <tr>
            <td style="width: 55%;">
                <p><span style="font-weight: bold;">David Nieves Coronado</span></p>
                <p>
	                ES74521153A<br />
	                C/ Primera Literatura 11, 3ºA<br />
	                02008 Albacete, Albacete<br />
	                España<br />
					<a href="mailto:info@devmanextensions.com">info@devmanextensions.com</a>
                </p>
            </td>
            <td style="text-align: left; vertical-align: top; width: 45%;">
                <p><span style="font-weight: bold;"><?= $invoice["Invoice"]["customer_name"]; ?></span></p>
                <p>
                	<?= !empty($invoice["Invoice"]["customer_vat"]) ? $invoice["Invoice"]["customer_vat"] : '-'; ?><br />
	                <?= $invoice["Invoice"]["customer_address"]; ?><br />
	                <?= $invoice["Invoice"]["customer_post_code"]; ?> <?= $invoice["Invoice"]["customer_city"]; ?>, <?= $invoice["Invoice"]["customer_zone"]; ?><br />
	                <?= $invoice["Invoice"]["customer_country"]; ?><br />
	                <a href="mailto:<?= $invoice['Invoice']['customer_email']; ?>"><?= $invoice["Invoice"]["customer_email"]; ?></a>
                </p>
            </td>
        </tr>
    </tbody>
</table>
<p>
    <br><br>
<font size="5" style="font-weight: bold;">INVOICE</font>
</p>
<table border="1" cellpadding="3" cellspacing="0" style="font-size:10px;" width="100%">
    <tbody>
        <tr>
            <td style="TEXT-ALIGN: center" width="25%">
                <b>Number</b><br><?= $invoice["Invoice"]["number"]; ?>&nbsp;
            </td>
            <td style="TEXT-ALIGN: center" width="25%">
                <b>Date</b><br />
                <?= date("d-m-Y", strtotime($invoice["Invoice"]["payed_date"])); ?>&nbsp;
            </td>
            <td style="TEXT-ALIGN: center" width="25%">
                <b>Client</b><br />
                <?= $invoice["Invoice"]["customer_name"]; ?>&nbsp;
            </td>
			<?php if(!empty($invoice["Invoice"]["customer_vat"])) { ?>
	            <td style="TEXT-ALIGN: center" width="25%">
	                <b>VAT</b><br />
	                <?= $invoice["Invoice"]["customer_vat"]; ?>&nbsp;
	            </td>
	        <?php } ?>
        </tr>
    </tbody>
</table>
<br />
<br />
<table border="1" cellpadding="3" cellspacing="0" style="font-size:10px;" width="100%">
    <tbody>
        <tr bgcolor="#EEEEEE">
            <td style="TEXT-ALIGN: center; border-left:0; border-right:0;">*</td>
            <td style="TEXT-ALIGN: left; border-left:0; border-right:0;">
                <strong>Payment method</strong>
            </td>
            <td style="TEXT-ALIGN: center; border-left:0; border-right:0;">
                <strong>Description</strong>
            </td>
            <td style="TEXT-ALIGN: center; border-left:0; border-right:0;">
                <strong>Quantity</strong>
            </td>
            <td style="TEXT-ALIGN: center; border-left:0; border-right:0;">
                <strong>Unit price</strong>
            </td>
            <td style="TEXT-ALIGN: center; border-left:0; border-right:0;">
                <strong>Discount (%)</strong>
            </td>
            <td style="text-align: center; border-left:0; border-right:0;">
                <strong><span>TAX (%)</span></strong>
            </td>
            <td style="TEXT-ALIGN: center; border-left:0; border-right:0;">
                <strong><span>Total</span></strong>
            </td>
        </tr>

        <tr></tr>

        <tr>
            <td style="border-left:0; border-right:0;" >1</td>
            <td style="border-left:0; border-right:0;" valign="middle">
                <center><?= $invoice["Invoice"]["payment_method"] ?><?= $invoice["Invoice"]["payment_method"] == 'Paypal' ? ' (3.7% fee)' : '' ; ?></center>
            </td>
            <td style="border-left:0; border-right:0;" valign="middle">
                <center><?= $invoice['Invoice']['description'] ?></center>
            </td>
            <td style="border-left:0; border-right:0;" valign="middle">
                <center><?= $invoice['Invoice']['quantity'] ?></center>
            </td>
            <td style="border-left:0; border-right:0;" valign="middle">
                <center><?= $invoice['Invoice']['price'] ?></center>
            </td>
            <td style="border-left:0; border-right:0;" valign="middle">
                <center><?= $invoice['Invoice']['discount'] ? $invoice['Invoice']['discount'].'%' : '0%' ?></center>
            </td>
            <td style="border-left:0; border-right:0;" valign="middle">
                <center><?= $invoice['Invoice']['tax'] ? $invoice['Invoice']['tax'].'%' : '0%' ?></center>
            </td>
            <td style="TEXT-ALIGN: right; border-left:0; border-right:0;" valign="middle">
                <?= $invoice['Invoice']['total'] ?>
            </td>
        </tr>

        <tr></tr>

        <tr>
            <td colspan="8" style="border-left:0; border-right:0;">
                <br />
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: right">
                Total (without tax)
            </td>
            <td colspan="2" style="TEXT-ALIGN: right">
                <?= $invoice["Invoice"]["total_without_tax"]; ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: right">
                Discount
            </td>
            <td colspan="2" style="TEXT-ALIGN: right">
                <?= $invoice["Invoice"]["discount"].'%'; ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: right;">
                TAX
            </td>
            <td colspan="2" style="text-align: right;">
            	<?= $invoice["Invoice"]["tax_price"]; ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" style="TEXT-ALIGN: right">
                <span style="font-weight: bold;">Total</span>
            </td>
            <td colspan="2" nowrap="nowrap" style="text-align: right">
                <strong>
                	<?= $invoice["Invoice"]["total"]; ?>
                </strong>
            </td>
        </tr>
    </tbody>
</table>
    <br><br>
<?php if ($invoice['Invoice']['payment_method'] == 'Bank Transfer') { ?>
	<p>
	Bank transfer data:
	</p>
	<table border="1" cellpadding="3" cellspacing="0" style="font-size:10px;" width="100%">
	    <tbody>
            <tr>
	            <td width="100%">
	                <b>Beneficiary</b>: David Nieves Coronado
	            </td>
	        </tr>
	        <tr>
	            <td width="100%">
	                <b>IBAN</b>: ES4201822857160201673993
	            </td>
	        </tr>
            <tr>
	            <td width="100%">
	                <b>Bank name</b>: BANCO BILBAO VIZCAYA ARGENTARIA S.A.
	            </td>
	        </tr>
            <tr>
	            <td width="100%">
	                <b>SWIFT code</b>: BBVAESMMXXX
	            </td>
	        </tr>
	    </tbody>
	</table>
	<p>
	&nbsp;
	</p>
<?php } ?>

<?php //////////////////////////////////////////////
?>