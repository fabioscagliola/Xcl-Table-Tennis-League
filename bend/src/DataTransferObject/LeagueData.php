<?php

namespace App\DataTransferObject;

use Symfony\Component\Validator\Constraints;

class LeagueData
{
    public function __construct(
        #[Constraints\NotBlank(message: 'You must indicate the name!')]
        public string $name,
    )
    {
    }
}