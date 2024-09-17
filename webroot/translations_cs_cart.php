<?php
header('X-XSS-Protection:0');

error_reporting(E_ALL);
ini_set('display_errors', 'On');

$index = array_key_exists('file', $_GET) ? $_GET['file'] : '';

$files_to_translate = array(
    array('/var/www/vhosts/devmanextensions.com/cs-cart.devmanextensions.com/gmt/var/langs/en/addons/google_marketing_tools.po' => '/var/www/vhosts/devmanextensions.com/cs-cart.devmanextensions.com/gmt/var/langs/ru/addons/google_marketing_tools.po'),
);

$file_to_translate = array_keys($files_to_translate[$index]);
$file_to_translate = $file_to_translate[0];
$file_translated = $files_to_translate[$index][$file_to_translate];

$translated = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $final_string = '';

    foreach ($_POST as $key => $translations_post) {
        foreach ($translations_post as $key2 => $translations) {
            foreach ($translations as $key_translation => $translation) {
                $translation = str_replace('"', '\"', $translation);
                $final_string .= $key_translation.' "'.$translation.'"'."\n";
            }
            $final_string .= "\n";
        }
    }

    file_put_contents($file_translated, str_replace('&#39;', "'", $final_string));
    $translated = true;
}

if(empty($file_to_translate))
    die("File not found");

$languages = transform_to_array($file_to_translate, true);


$file_to_translate = $files_to_translate[$index][$file_to_translate];

if(!is_file($file_translated)) {
    fopen($file_translated, "w");
}

$translations = transform_to_array($file_translated);

function transform_to_array($path, $original = false) {
    $file_content = file_get_contents($path);
    
    $translations_lines = explode(PHP_EOL, $file_content);

    if($original)
        $translations_lines = array_slice($translations_lines, 6);
    
    foreach ($translations_lines as $key => $line) {
        if (empty($line))
            unset($translations_lines[$key]);
    }
    
    $translations_lines = array_values($translations_lines);

    $translations_vars = array();
    $temp = array();
    foreach ($translations_lines as $key => $value) {
        $exploded = explode(" ", $value, 2);
        $temp[$exploded[0]] = substr(trim($exploded[1]), 1, -1);

        if($exploded[0] == 'msgstr') {
            $translations_vars[] = $temp;
            $temp = array();
        }
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
        <form method="post" action="translations_cs_cart.php?file=<?= $_GET['file'] ?>">
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
                        <?php $count = 1;
                            foreach ($languages as $key => $value) {

                            $text_translated = $value['msgstr'];
                            $translated = array_key_exists($key, $translations);
                            $some_difference = array_key_exists($key, $translations) && sanize_string($text_translated) != sanize_string($translations[$key]['msgstr']);

                            $text = $translated ? $translations[$key]['msgstr'] : $text_translated;
                            if($translated) unset($translations[$key]);
                                echo '<tr>';
                                    echo '<td style="background: #'.(!$translated ? 'F00' : ($some_difference ? 'b43fea' : '00cf27')).'">'.$count.'</td>';
                                    echo '<td>
                                                       <input type="hidden" name="translation['.$key.'][msgctxt]" value="'.$value['msgctxt'].'">
                                                       <input type="hidden" name="translation['.$key.'][msgid]" value="'.$value['msgid'].'">
                                                       <textarea style="height:80px; width: 100%;" name="translation['.$key.'][msgstr]">'.$text.'</textarea>
                                    </td>';
                                    echo '<td>'.$text.'</td>';
                                    echo '<td>'.$text_translated.'</td>';
                                echo '<tr>';
                                    $count++;
                            }
                            if(!empty($translations)) {
                                foreach ($translations as $key => $value) {

                                    $text = $value['msgstr'];

                                    echo '<tr>';
                                    echo '<td style="background: #fa8500">' . $count . '</td>';
                                    echo '<td>
                                            <input type="hidden" name="translation['.$key.'][msgctxt]" value="'.$text['msgctxt'].'">
                                                       <input type="hidden" name="translation['.$key.'][msgid]" value="'.$text['msgid'].'">
                                                       <textarea style="height:80px; width: 100%;" name="translation['.$key.'][msgstr]">' . $text['msgstr'] . '</textarea>
                                        </td>';
                                    echo '<td>' . $text['msgstr'] . '</td>';
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
