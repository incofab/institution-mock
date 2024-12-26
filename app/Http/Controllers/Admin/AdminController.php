<?php
namespace App\Http\Controllers\Admin;

class AdminController extends BaseAdminController
{
    function index()
    {
        return view('admin.index');
    }
}
