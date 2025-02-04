<?php

namespace App\Constants;

class DraftProductStatus
{
    const DRAFT = 'draft';
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';

    const STATUSES = [
        self::DRAFT,
        self::PENDING,
        self::APPROVED,
        self::REJECTED,
    ];
}