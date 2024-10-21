<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;

class TeamController extends BaseController
{
    protected $fillable = [
        "name",
        "competition_id"
    ];
}
