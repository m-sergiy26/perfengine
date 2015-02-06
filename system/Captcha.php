<?php
class Captcha
{
    protected static $src;

    public function __construct()
    {
        include('data/captcha_config.php');

        # Create of picture
        self::$src = imagecreatetruecolor($width, $height);
        $fon = imagecolorallocate(self::$src, 255, 255, 255);

        imagefill(self::$src, 0, 0, $fon);

        # Fonts
        $fonts =[];

        $dir = opendir($path_fonts);

        while ($fontName = readdir($dir))
        {
            if ($fontName != '.' && $fontName != '..') $fonts[] = $fontName;
        }

        closedir($dir);

        # Adding symbols in background
        for ($i=0;$i<$fon_let_amount;$i++)
        {
            $color = imagecolorallocatealpha(self::$src, rand(0, 255), rand(0, 255), rand(0, 255), 100);
            $font = $path_fonts . $fonts[rand(0, sizeof($fonts) - 1)];
            $letter = $letters[rand(0, sizeof($letters) - 1)];
            $size = rand($font_size - 2, $font_size + 2);
            imagettftext(self::$src, $size, rand(0, 45), rand($width * 0.1, $width - $width * 0.1), rand($height * 0.2, $height), $color, $font, $letter);
        }

        # Adding symbols
        for ($i=0;$i<$let_amount;$i++)
        {
            $color = imagecolorallocatealpha(self::$src, $colors[rand(0, sizeof($colors) - 1)], $colors[rand(0, sizeof($colors) - 1)], $colors[rand(0, sizeof($colors) - 1)], rand(20, 40));
            $font = $path_fonts . $fonts[rand(0, sizeof($fonts) - 1)];
            $letter = $letters[rand(0, sizeof($letters) - 1)];
            $size = rand($font_size * 2.1 - 2, $font_size * 2.1 + 2);
            $x = ($i + 1) * $font_size + rand(4, 7);
            $y = (($height * 2) / 3) + rand(0, 5);
            $_captcha[] = $letter;
            imagettftext(self::$src, $size, rand(0, 15), $x, $y, $color, $font, $letter);
        }
        # Code to session
        $_SESSION['captcha'] = implode('', $_captcha);
    }

    public static function captcha()
    {
        # Show code
        header('Content-type: image/gif');
        imagegif(self::$src);
    }
}