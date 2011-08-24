<?php
exit;

ini_set('display_errors', 'on');
error_reporting(E_ALL|E_NOTICE);
header('Content-Type: text/html; charset=utf-8');


function ext($p)
{
    $p = array_pop(explode('/', str_replace('\\', '/', $p)));
    return strtolower(array_pop(explode('.', $p)));
}


function xml_escape($string)
{
   return str_replace(
       array('&', '<', '>', '"', "'", '--'),
       array('&#38;', '&#60;', '&#62;', '&#34;', '&#39;', '-&#45;'),
       $string);
}


function dir_walk($path='', $depth=0)
{
    if ($path == '') {
        $apath = './';
    }
    else {
        if (substr($path, -1, 1) != '/')
            $path .= '/';

        $apath = $path;
    }

    $out = array();
    $ds = utf8_encode(str_repeat("\xA0 \xA0 ", $depth));

    foreach (scandir($apath) as $v) {
        if (substr($v, 0, 1) == '.')
            continue;

        if (is_dir($apath . $v)) {
            printf("%s+ <b>%s</b><br/>\n",
                $ds, xml_escape($path . $v));
            dir_walk($path . $v, $depth + 1);
            print "<br/>\n";
        }
        else {
            if (ext($v) == 'pyc' || ($path == '' && $v == 'index.php'))
                continue;

            $out[] = $v;
        }
    }

    foreach ($out as $file) {
        printf("%s<a href=\"?sauce=%s\">%s</a><br/>\n", $ds,
            urlencode($path . $file),
            xml_escape($file));
    }
}


function valid_path($path)
{
    $p = explode('/', str_replace('\\', '/', $path));

    foreach ($p as $pn) {
        if ($pn == '' || substr($pn, 0, 1) == '.')
            return false;
    }

    return true;
}

header('Content-Type: text/html; charset=utf-8');
print '<!DOCTYPE html><html><head>'
    . '<title>Source</title>' . "\n"
    . '<style type="text/css">/*<![CDATA[*/' . "
html { background-color: #333; }
body { font-family: 'Trebuchet MS', serif;  font-size: 0.9em;  margin: 2em auto;
width: 50em; padding: 2em; border: 3px solid #000; background-color: #EEE; }
a { color: blue; }
a:hover { color: #000033; }
/*]]>*/</style></head>\n<body>\n";

if (isset($_GET['sauce'])) {
    if (!valid_path($_GET['sauce']) || !is_file($_GET['sauce'])) {
        print '<h1>YOR ISP MAC ADDRESS HAS BEEN REPORTING TO THE FBI!!!</h1>';
        exit;
    }
    else {
        require_once '/home/ben/geshi/geshi.php';

        $lang = ext($_GET['sauce']);

        switch ($lang) {
            case 'py': $lang = 'python'; break;
        }

        $geshi = new GeSHi(file_get_contents($_GET['sauce']), $lang);
        $geshi->set_header_type(GESHI_HEADER_PRE_VALID);
        $geshi->enable_classes();
        //$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
        $geshi->enable_keyword_links(false);
        $geshi->set_keyword_group_style(1, 'color: #DD0000;');
        $geshi->set_keyword_group_style(2, 'color: #666;');
        $geshi->set_keyword_group_style(3, 'color: #0033CC;');
        $geshi->set_keyword_group_style(4, 'color: #00CC33;');
        $geshi->set_comments_style(1, 'color: #00CC00;');
        $geshi->set_comments_style(2, 'color: #00CC00;');
        $geshi->set_comments_style('MULTI', 'color: #009900;');

        print '<style type="text/css">/*<![CDATA[*/'
             . $geshi->get_stylesheet()
             . "/*]]>*/</style>\n"
             . $geshi->parse_code();
    }
}
else {
    dir_walk();
}

print "\n</body></html>\n";

