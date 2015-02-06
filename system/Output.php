<?php
class Output
{
    public function bbtags($string)
    {
        $string = preg_replace('/\[b\](.+?)\[\/b\]/isU', '<span style="font-weight: bold">$1</span>', str_replace("]\n", "]", $string));
        $string = preg_replace('/\[u\](.+?)\[\/u\]/isU', '<span style="text-decoration:underline;">$1</span>', $string);
        $string = preg_replace('/\[s\](.+?)\[\/s\]/isU', '<span style="text-decoration:line-through;">$1</span>', $string);
        $string = preg_replace('/\[i\](.+?)\[\/i\]/isU', '<span style="font-style:italic;">$1</span>', $string);
        $string = preg_replace('/\[big\](.+?)\[\/big\]/isU', '<span style="font-size:large;">$1</span>', $string);
        $string = preg_replace('/\[small\](.+?)\[\/small\]/isU', '<span style="font-size:small;">$1</span>', $string);
        $string = preg_replace('/\[red\](.+?)\[\/red\]/isU', '<span style="color:#ff0000;">$1</span>', $string);
        $string = preg_replace('/\[yellow\](.+?)\[\/yellow\]/isU', '<span style="color:#ffff22;">$1</span>', $string);
        $string = preg_replace('/\[green\](.+?)\[\/green\]/isU', '<span style="color:#00bb00;">$1</span>', $string);
        $string = preg_replace('/\[blue\](.+?)\[\/blue\]/isU', '<span style="color:#0000bb;">$1</span>', $string);
        $string = preg_replace('/\[quote\](.+?)\[\/quote\]/isU', '<div class="quote">$1</div>', $string);
//        $string = preg_replace_callback('/\[file\]([0-9]+)\[\/file\]/isU', 'files_parser', $string);
        $string = preg_replace_callback('/\[code\](.+?)\[\/code\]/isU', array($this, 'highlight_php'), $string);
        $string = preg_replace_callback("/\[url=(https?:\/\/.+?)\](.+?)\[\/url\]|(https?:\/\/([a-zA-Zа-яА-Я0-9іїёґ\.\/\[\]\#\;\&\_\-\)\(\:\?\=\+]*))/iu", array($this, 'link_parser'), $string);
        return $string;
    }

    protected function highlight_php($source)
    {
    // print_r($source);
        $php = strtr($source[1], array
        (
            '<br />' => '',
            '\\' => 'slash'
        ));

        $php = html_entity_decode($php, ENT_QUOTES, 'UTF-8');
        $php = stripslashes($php);
        $php = str_replace("\n", '', $php);
        if(!strpos($php, "<?") && substr($php, 0, 2) != "<?")
        {
            $php = "<?php\n".trim($php)."\n?>";
        }
        $code = trim($php);
        $code = highlight_string($code, true);
        $code = strtr($code, array (
            'slash' => '&#92;',
            '[' => '&#91;',
            '&nbsp;' => ' ',
        ));


        if(substr($php, 0, 2) == "<?")
        {
            $code = preg_replace('/&lt;\?php<br \/>|\?&gt;/i', '<!--CODE-->', $code);
        }
        return '<div class="code">'.$code.'</div>';
    }

    protected function link_parser($linkInfo)
    {
        if(!$linkInfo[1])
        {
            return '<a target="_blank" href="'.$linkInfo[0].'">'.$linkInfo[0].'</a>';
        }
        else
        {
            return '<a target="_blank" href="'.$linkInfo[1].'">'.$linkInfo[2].'</a>';
        }
    }

    public function smiles($string)
    {
        $smiles = App::config('system/data/smiles');
        $string = str_replace(array_keys($smiles), array_values($smiles), $string);
        return $string;
    }
}