<?php
namespace App\Services;

use App\Models\Customer;

class CustomerService {
    public function getAllCustomers(){
        return Customer::all();
    }

    public function createCustomer(array $data) {
        return Customer::create($data);
    }

    public function getCustomerById($id) {
        return Customer::findOrFail($id);
    }

    public function updateCustomerById($id, array $data) {
        $customer = Customer::findOrFail($id);
        $customer->update($data);
        return $customer;
    }

    public function deleteCustomerById($id) {
        $customer = Customer::findOrFail($id);
        $customer->delete();
    }
}
