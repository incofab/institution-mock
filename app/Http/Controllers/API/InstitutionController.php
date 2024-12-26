<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Institution;

class InstitutionController extends Controller
{
    function showInstitution(Institution $institution)
    {
        return $this->apiSuccessRes($institution);
    }
}
