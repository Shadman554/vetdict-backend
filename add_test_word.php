<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illwarem\Console\Kernel::class);

use Illuminate\Support\Str;
use App\Models\Word;

$word = new Word();
$word->id = (string) Str::uuid();
$word->name = 'Test';
$word->kurdish = 'تست';
$word->arabic = 'اختبار';
$word->is_saved = true;
$word->is_favorite = false;
$word->save();

echo "Word created with ID: " . $word->id . "\n";
