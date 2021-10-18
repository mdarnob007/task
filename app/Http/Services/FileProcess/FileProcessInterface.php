<?php

namespace App\Http\Services\FileProcess;

interface FileProcessInterface
{
    /**
     * import
     *
     * @return object
     */
    public function import(): object;
}
