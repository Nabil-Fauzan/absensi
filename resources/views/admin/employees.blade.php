<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Akun Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Notification -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-900 dark:text-green-400 border border-green-200 dark:border-green-850 shadow-sm transition duration-150 ease-in-out" role="alert">
                    <span class="font-medium">Sukses!</span> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-900 dark:text-red-400 border border-red-200 dark:border-red-850 shadow-sm transition duration-150 ease-in-out" role="alert">
                    <span class="font-medium">Gagal!</span> {{ session('error') }}
                </div>
            @endif

            <div class="space-y-6">
                @include('admin.partials.employees-crud')
            </div>

        </div>
    </div>

    @include('admin.partials.modals')

    <script>
        // --- CUSTOM CONFIRMATION MODAL HELPER FUNCTIONS ---
        let activeConfirmFormId = null;

        function showConfirmModal(formId, title, message) {
            activeConfirmFormId = formId;
            document.getElementById('confirmTitle').innerText = title;
            document.getElementById('confirmMessage').innerText = message;
            
            const submitBtn = document.getElementById('confirmSubmitBtn');
            submitBtn.onclick = function(e) {
                e.preventDefault();
                const form = document.getElementById(activeConfirmFormId);
                if (form) form.submit();
            };
            
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            activeConfirmFormId = null;
        }

        // --- EMPLOYEE MODAL CRUD ---
        function openAddEmployeeModal() {
            document.getElementById('addEmployeeModal').classList.remove('hidden');
        }
        function closeAddEmployeeModal() {
            document.getElementById('addEmployeeModal').classList.add('hidden');
        }
        
        function openEditEmployeeModal(id, name, email) {
            document.getElementById('editEmpName').value = name;
            document.getElementById('editEmpEmail').value = email;
            document.getElementById('editEmployeeForm').action = `/admin/employees/${id}`;
            document.getElementById('editEmployeeModal').classList.remove('hidden');
        }
        function closeEditEmployeeModal() {
            document.getElementById('editEmployeeModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
