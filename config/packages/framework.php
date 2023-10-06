<?php

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {
    $framework->router()->defaultUri('https://bottin.marche.be');
};
