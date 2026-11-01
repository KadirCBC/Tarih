<?php

namespace App\Observers;

use App\Models\UserCollection;
use App\Services\LegalContentFilterService;
use Illuminate\Validation\ValidationException;

class UserCollectionObserver
{
    protected LegalContentFilterService $filter;

    public function __construct(LegalContentFilterService $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Koleksiyon kaydı kaydedilmeden önce yasaklı kelimeleri denetler.
     */
    public function saving(UserCollection $userCollection): void
    {
        if (!empty($userCollection->notes)) {
            if ($this->filter->containsProhibitedContent($userCollection->notes)) {
                $violations = $this->filter->detectViolations($userCollection->notes);
                $words = implode(', ', array_map(fn ($w) => "\"$w\"", $violations));

                throw ValidationException::withMessages([
                    'notes' => "2863 sayılı kanun uyarınca not alanında tespit edilen ticari/define ibareleri ({$words}) yasaktır. Platformumuz yalnızca akademik referans amaçlıdır.",
                ]);
            }
        }
    }
}

