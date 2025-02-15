<?php

namespace App\Http\Controllers\API;

use App\Services\CustomerService;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

class CustomerController extends BaseController
{
    protected $customerService;
    
    public function __construct(CustomerService $customerService) {
        $this->customerService = $customerService;
    }

    public function index() {
        return response()->json($this->customerService->getAllCustomers());
    }

    public function create(Request $request) {
        // $validated = $request->validate([
        //     'first_name' => 'required|string|max:255',
        //     'last_name' => 'required|string|max:255',
        //     'email' => 'required|email|unique:customers,email',
        //     'phone' => 'nullable|string|max:20',
        //     'address' => 'nullable|string|max:255',
        // ]);

        $customer = $this->customerService->createCustomer($request->all());
        return response()->json($customer, 201);
    }

    public function show($id) {
        return response()->json($this->customerService->getCustomerById($id));
    }
    
    public function update(Request $request, $id) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        return response()->json($this->customerService->updateCustomerById($id, $validated));
    }

    public function destroy($id) {
        $this->customerService->deleteCustomerById($id);
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
