<?php

use Illuminate\Support\Facades\Schedule;

// Günlük nümizmatik bakım ve istatistik optimizasyonları
Schedule::command('model:prune')->daily();
Schedule::command('cache:prune-stale-tags')->hourly();

