<?php
header('X-XSS-Protection:0');

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$index = array_key_exists('file', $_GET) ? $_GET['file'] : '';

$files_to_translate = array(
    array('/var/www/vhosts/devmanextensions.com/httpdocs/global-devman-api/translations/gmt+intl-icu.en.yml' => '/var/www/vhosts/devmanextensions.com/httpdocs/api_translations/gmt+intl-icu.ru.yml'),
);

$file_to_translate = array_keys($files_to_translate[$index]);
$file_to_translate = $file_to_translate[0];
$file_translated = $files_to_translate[$index][$file_to_translate];

$translated = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $final_string = '';
    foreach ($_POST as $key_translation => $translation) {
        $translation = str_replace('"', '\"', $translation);
        $final_string .= $key_translation.': "'.$translation.'"'."\n";
    }

    file_put_contents($file_translated, str_replace('&#39;', "'", $final_string));
    $translated = true;
}

if(empty($file_to_translate))
    die("File not found");

$languages = transform_to_array($file_to_translate);


$file_to_translate = $files_to_translate[$index][$file_to_translate];

if(!is_file($file_translated)) {
    fopen($file_translated, "w");
}

$translations = transform_to_array($file_translated);

function transform_to_array($path) {
    $file_content = file_get_contents($path);

    $translations_lines = explode(PHP_EOL, $file_content);
    
    foreach ($translations_lines as $key => $line) {
        if (!strpos($line, ':') !== false)
            unset($translations_lines[$key]);
    }
    
    $translations_lines = array_values($translations_lines);

    $translations_vars = array();
    foreach ($translations_lines as $key => $value) {
        $exploded = explode(":", $value, 2);
        $translations_vars[$exploded[0]] = substr(trim($exploded[1]), 1, -1);
    }
    return $translations_vars;
}

?><html>
    <head>
        <title>CS-Cart translations</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <style type="text/css">
            div.container {
                display: block;
                width: 100%;
            }
            table {
              table-layout:fixed;
                width: 100%;
            }
            table img {
                display: none;
            }
        </style>
        <script type="text/javascript">

            $(document).on("keyup", 'textarea', function() {
                var value = $(this).val();
                $(this).closest('td').next().html(value);
            });

            $(function(){
                $('table').find('textarea').each(function(){
                    var height = $(this).closest('td').height();
                    $(this).height(height);
                });
            });
        </script>
    </head>
    <body>
        <?= $translated ? '<h1>Translated saved sucessfully</h1>' : ''; ?>
        <form method="post" action="translations_api.php?file=<?= $_GET['file'] ?>">
            <input type="submit" style="background: #406dff; color: #fff; font-weight: bold; padding: 10px 15px;" value="SAVE TRANSLATION">
            <div class="container">
                <table cellpadding="1" cellspacing="1" border="1">
                    <thead>
                        <tr>
                            <td style="width: 25px;">*</td>
                            <td>Translate</td>
                            <td>Previsualization</td>
                            <td>Original text</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1; foreach ($languages as $key => $value) {
                            $translated = array_key_exists($key, $translations);
                            $some_difference = array_key_exists($key, $translations) && sanize_string($value) != sanize_string($translations[$key]);

                            $text = $translated ? $translations[$key] : $value;
                            if($translated) unset($translations[$key]);
                            echo '<tr>';
                                echo '<td style="background: #'.(!$translated ? 'F00' : ($some_difference ? 'b43fea' : '00cf27')).'">'.$count.'</td>';
                                echo '<td><textarea style="height:80px; width: 100%;" name="'.$key.'">'.$text.'</textarea></td>';
                                echo '<td>'.$text.'</td>';
                                echo '<td>'.$value.'</td>';
                            echo '<tr>';
                                $count++;
                        }
                        if(!empty($translations)) {
                            foreach ($translations as $key => $value) {

                                $text = $value;

                                echo '<tr>';
                                echo '<td style="background: #fa8500">' . $count . '</td>';
                                echo '<td><textarea style="height:80px; width: 100%;" name="' . $key . '">' . $text . '</textarea></td>';
                                echo '<td>' . $text . '</td>';
                                echo '<td>NEW TRANSLATION.</td>';
                                echo '<tr>';
                                $count++;
                            }
                        }?>
                    </tbody>
                </table>
            </div>
            <input type="submit" style="background: #406dff; color: #fff; font-weight: bold; padding: 10px 15px;" value="SAVE TRANSLATION">
        </form>
    </body>
</html>

<?php function sanize_string($string) {
    return trim(preg_replace('/\s\s+/', ' ', $string));
} ?>
