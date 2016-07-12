<?php

namespace Wame\Core\Registers;

use Wame\Core\Repositories\BaseRepository;

class RepositoryRegister extends BaseRegister
{

    public function __construct()
    {
        parent::__construct(BaseRepository::class);
    }
}
