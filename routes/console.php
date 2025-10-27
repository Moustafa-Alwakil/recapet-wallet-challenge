<?php

declare(strict_types=1);

Schedule::command('wallets:snapshot')
    ->twiceDaily()
    ->runInBackground()
    ->withoutOverlapping();
