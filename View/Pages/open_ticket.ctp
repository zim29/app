<?php echo $this->Html->script(
    array(
        'pages/open_ticket.js?' . date('YmdHis')
    )
);
$text_lng = ['en' => 'Open a ticket', 'es' => 'Abrir un ticket'];
App::import('Model', 'Extensions.Extension');
$this->Extension = new Extension();

$extensions = array('' => __('- Select a extension -'));
$extensions_temp = $this->Extension->find('list', array('order' => array('system', 'name'), 'fields' => array('Extension.id', 'Extension.system_extension_name'), 'conditions' => array('Extension.in_support' => true)));
foreach ($extensions_temp as $key => $value) {
    $extensions[$key] = $value;
}

print_r($selected);

$types = [
    'Support' => __('Support'),
    'Pre-sale question' => __('Pre-sale question'),
    'Personal develop' => __('Personal develop'),
    'Others' => __('Others')
];
?>

<?php
$this->request->data = $this->Session->read('ticket_data') ? $this->Session->read('ticket_data') : array();
$type = array_key_exists('Ticket', $this->request->data) && array_key_exists('type', $this->request->data['Ticket']) ? $this->request->data['Ticket']['type'] : '';
$id_extension = array_key_exists('Ticket', $this->request->data) && array_key_exists('id_extension', $this->request->data['Ticket']) ? $this->request->data['Ticket']['id_extension'] : '';
$id_order = array_key_exists('Ticket', $this->request->data) && array_key_exists('id_order', $this->request->data['Ticket']) ? $this->request->data['Ticket']['id_order'] : '';
$web = array_key_exists('Ticket', $this->request->data) && array_key_exists('web', $this->request->data['Ticket']) ? $this->request->data['Ticket']['web'] : '';
$email = array_key_exists('Ticket', $this->request->data) && array_key_exists('email', $this->request->data['Ticket']) ? $this->request->data['Ticket']['email'] : '';
$name = array_key_exists('Ticket', $this->request->data) && array_key_exists('name', $this->request->data['Ticket']) ? $this->request->data['Ticket']['name'] : '';
$subject = array_key_exists('Ticket', $this->request->data) && array_key_exists('subject', $this->request->data['Ticket']) ? $this->request->data['Ticket']['subject'] : '';
$text = array_key_exists('Ticket', $this->request->data) && array_key_exists('text', $this->request->data['Ticket']) ? $this->request->data['Ticket']['text'] : '';
$conections = array_key_exists('Ticket', $this->request->data) && array_key_exists('conections', $this->request->data['Ticket']) ? $this->request->data['Ticket']['conections'] : '';
?>

<?= $this->element('header', [
    'title' => 'Open a ticket',
    'text' => 'Tell us what you need and how we can reach you',
]); ?>
<section class="mt-5">

    <?php if ($this->Flash->render()): ?>
        <div class="ticket_success card">
            <div class="card-body">
                <?= $this->Html->image('ticket_sent.gif', ['id' => 'ticket_sent_gif']) ?>
                <a href="https://www.flaticon.com/free-animated-icons/send" title="send animated icons">Send animated icons created by Freepik - Flaticon</a>
                <br>
                <h1>Ticket created successfully</h1>

            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <form action="<?php echo Router::url("/", false); ?>tickets/tickets/create" role="form" id="createTicket" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                    <div class="mb-3">
                        <input type="text" name="data[Ticket][name]" class="form-control" placeholder="Name *" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="data[Ticket][email]" class="form-control" placeholder="Email *" required>
                    </div>
                    <div class="mb-3">
                        <select id="type" name="data[Ticket][type]" class="form-select" required>
                            <option value="" selected>What do you need? *</option>

                            <?php foreach ($types as $key => $val) { ?>
                                <option value="<?= $key ?>" <?= $key == $type ? ' selected="selected"' : '' ?>><?= $val ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="data[Ticket][subject]" class="form-control" placeholder="Subject *" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="data[Ticket][text]" class="form-control" rows="4" placeholder="Text *" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="file-input-container">
                            <input type="file" id="file-upload" multiple />
                            <label for="file-upload" class="file-input-label d-flex justify-content-between">
                                Additional files<i class="icon-clip"></i>
                            </label>
                            <div id="file-list"></div>
                        </div>

                        <input type="file" name="data[Ticket][attach]" class="form-control">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-danger btn-lg px-4">Send a ticket</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-center">
                <?= $this->Html->image('ticket-support-illustration.svg', ['class' => 'img-fluid']); ?>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        setTimeout(() => {
            const params = new URLSearchParams(window.location.search);
            document.getElementById('type').value = params.get('option') !== null ? params.get('option') : '';
        }, 20);

        const fileInput = document.getElementById('file-upload');
        const fileList = document.getElementById('file-list');

        fileInput.addEventListener('change', function() {
            const files = fileInput.files;
            fileList.innerHTML = '';
            for (let i = 0; i < files.length; i++) {
                const fileItem = document.createElement('p');
                fileItem.textContent = files[i].name;
                fileList.appendChild(fileItem);
            }
        });

        const gif = document.getElementById('ticket_sent_gif').parentElement;

        // Define la duración en segundos del GIF manualmente
        const gifDuration = 8; // Por ejemplo, 5 segundos


        // Usa setTimeout para ocultar el GIF después de la duración especificada
        setTimeout(() => {
            console.log(gif);
            gif.style.display = 'none';
        }, gifDuration * 1000); // Elimina el gif después de que termina la animación

    });
</script>



<!-- <article role="main">
    <section>
        <header class="jumbotron">
            <h1><?= $text_lng[$lang_code]; ?></h1>
        </header>

        <div class="container">
            <form action="<?php echo Router::url("/", false); ?>tickets/tickets/create" role="form" id="createTicket" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?= __('What do you need?') ?> *</label>
                            <select name="data[Ticket][type]" class="form-control selectpicker bs-select-hidden" data-live-search="true" id="TicketType">
                                <?php foreach ($types as $key => $val) { ?>
                                    <option value="<?= $key ?>"<?= $key == $type ? ' selected="selected"' : '' ?>><?= $val ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?= __('Extension') ?> *</label>
                            <select name="data[Ticket][id_extension]" class="form-control selectpicker" data-live-search="true" id="TicketIdExtension">
                                <?php foreach ($extensions as $key => $val) { ?>
                                    <option value="<?= $key ?>"<?= $key == $id_extension ? ' selected="selected"' : '' ?>><?= $val ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?= __('License ID') ?> *</label>
                            <input name="data[Ticket][id_order]" class="form-control"
                                onkeypress="get_license($(this).val(), '<?= Router::url('/', true); ?>opencart/get_license_from_ticket_system');"
                                onkeyup="get_license($(this).val(), '<?= Router::url('/', true); ?>opencart/get_license_from_ticket_system');"
                                onchange="get_license($(this).val(), '<?= Router::url('/', true); ?>opencart/get_license_from_ticket_system');"
                                placeholder="Order ID"
                                value="<?= $id_order ?>"
                                type="text"
                                id="TicketIdOrder"
                            >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?= __('Ecommerce url') ?> *</label>
                            <input name="data[Ticket][web]" class="form-control" placeholder="<?= __('Shop url where you installed the product') ?>" value="<?= $web ?>" type="text" id="TicketWeb">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?= __('Email') ?> *</label>
                            <input name="data[Ticket][email]" class="form-control" value="<?= $email ?>" type="text" id="TicketEmail">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?= __('Name') ?> *</label>
                            <input name="data[Ticket][name]" class="form-control" value="<?= $name ?>" type="text" id="TicketName">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?= __('Subject') ?> *</label>
                            <input name="data[Ticket][subject]" class="form-control" value="<?= $subject ?>" type="text" id="TicketSubject">
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?= __('Text') ?> *</label>
                            <textarea name="data[Ticket][text]" class="form-control" id="TicketText"><?= $text ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?= __('Connections') ?></label>
                            <textarea name="data[Ticket][conections]" class="form-control" id="TicketConections" placeholder="<?= __('Sometimes we need to connect to your shop to investigate and fix errors, if you think that I have to connect to your shop please create us a temporal FTP account and OpenCart admin account and put here. (This data won\'t be saved)') ?>"><?= $conections ?></textarea>
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><?= __('Additional files') ?></label>
                            <div style="clear:both;"></div>
                            <a class="btn btn-lg btn-default attach btn-emphasis-fe" href="javascript:{}">Click to attach files<span></span></a>
                            <input type="file" name="data[Ticket][attach][]" class="form-control" multiple="multiple"style="display:none;" id="TicketAttach">
                        </div>
                    </div>

                    <div class="col-md-8 send_ticket">
                        <div class="form-group text-right">
                            <div class="g-recaptcha" style="position: relative; float: left;" data-sitekey="6LeNxKAUAAAAAGHlDeqliG7-9wDsvvQhv8a6i3Cw"></div>
                            <a href="javascript:{}" onclick="$(this).closest('form').submit();" class="btn btn-lg btn-primary ticket"><?= __('Send ticket') ?></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="mds2">
        <div class="container">
            <center>

                <?php if ($is_holidays) { ?>
                    <H2 style="color:#ff0000;">VACATION TIME FROM <?= $holidays_from ?> TO <?= $holidays_to ?><br>Your emails/tickets are being putting in queue, will be attend soon as possible, when coming back!</H2>
                <?php } else { ?>
                    <div class="we-are-online-box">
                        <img class="img-fluid" src="<?= Router::url("/", false) ?>images/extensions/support_status.jpg?v=<?= date('YmdHms') ?>" alt="" class="img-responsive">
                    </div>
                <?php } ?>
            </center>
        </div>
    </section>

</article> -->