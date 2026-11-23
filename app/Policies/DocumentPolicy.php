<?php
namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    // only the owner may view; everyone else gets 403
    public function view(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }
}
