<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Controller class.
 *
 * This class extends the BaseController and provides additional functionality
 * for handling HTTP requests and dispatching jobs.
 *
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
    use DispatchesJobs;
}
