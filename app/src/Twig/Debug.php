<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Debug extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('dd', [$this, 'debug'], ['is_safe' => ['html']]),
        ];
    }

    public function debug($mix)
    {
        ob_start();
        var_dump($mix);
        $result = ob_get_clean();
        return '<pre>' . $result . '</pre>';
    }
}