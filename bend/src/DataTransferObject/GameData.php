<?php

namespace App\DataTransferObject;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints;

class GameData
{
    public function __construct(
        #[Constraints\NotBlank(message: 'You must indicate the league identifier!')]
        public int $leagueId,
        #[Constraints\NotBlank(message: 'You must indicate the date!')]
        #[Constraints\DateTime(DateTimeInterface::ATOM, message: 'The format of the date must comply with the ISO 8601 standard!')]
        public string $date,
        #[Constraints\NotBlank(message: 'You must indicate the winner identifier!')]
        public int $winnerId,
        #[Constraints\NotBlank(message: 'You must indicate the winner name!')]
        public string $winnerName,
        #[Constraints\NotBlank(message: 'You must indicate the result!')]
        public string $result,
    )
    {
    }
}