<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Keep shipping awesome pet experiences 🐾');
})->purpose('Display an inspiring message');
