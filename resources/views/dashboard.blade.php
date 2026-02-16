<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h1 class="text-3xl font-bold mb-6 text-gray-800">
                Dashboard
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white p-6 rounded-xl shadow">
                    <h2 class="text-lg font-semibold text-gray-700">
                        Total Customers
                    </h2>
                    <p class="text-3xl text-blue-600 mt-2">
                        {{ \App\Models\Customer::count() }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    <h2 class="text-lg font-semibold text-gray-700">
                        Total Accounts
                    </h2>
                    <p class="text-3xl text-green-600 mt-2">
                        {{ \App\Models\Account::count() }}
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow">
                    <h2 class="text-lg font-semibold text-gray-700">
                        Total Loans
                    </h2>
                    <p class="text-3xl text-red-600 mt-2">
                        {{ \App\Models\Loan::count() }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
