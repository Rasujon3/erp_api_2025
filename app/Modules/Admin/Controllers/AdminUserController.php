<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\AppBaseController;
use App\Modules\Admin\Repositories\AdminUserRepository;
use App\Modules\Admin\Requests\AdminUserRequest;

class AdminUserController extends AppBaseController
{
    protected AdminUserRepository $adminUserRepository;
    public function __construct(AdminUserRepository $adminUserRepo)
    {
        $this->adminUserRepository = $adminUserRepo;
    }

    // Store data
    public function store(AdminUserRequest $request)
    {
        $store = $this->adminUserRepository->store($request->all());
        if (!$store) {
            return $this->sendError('Something went wrong!!! [AUS-01]', 500);
        }
        return $this->sendResponse($store, 'Admin User created successfully!');
    }
}
