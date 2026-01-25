<x-layouts.admin title="Manajemen Tipe Tiket">
    @if (session('success'))
        <div class="toast toast-bottom toast-center">
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        </div>

        <script>
            setTimeout(() => {
                document.querySelector('.toast')?.remove()
            }, 3000)
        </script>
    @endif

    <div class="container mx-auto p-10">
        <div class="flex">
            <h1 class="text-3xl font-semibold mb-4">Manajemen Tipe Tiket</h1>
            <button class="btn btn-primary ml-auto" onclick="add_modal.showModal()">Tambah Tipe Tiket</button>
        </div>
        <div class="overflow-x-auto rounded-box bg-white p-5 shadow-xs">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="w-3/4">Nama Tipe Tiket</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ticketTypes as $index => $ticketType)
                        <tr>
                            <th>{{ $index + 1 }}</th>
                            <td>{{ $ticketType->nama }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary mr-2" onclick="openEditModal(this)" data-id="{{ $ticketType->id }}" data-nama="{{ $ticketType->nama }}">Edit</button>
                                <button class="btn btn-sm bg-red-500 text-white" onclick="openDeleteModal(this)" data-id="{{ $ticketType->id }}">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada tipe tiket tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Tipe Tiket -->
    <dialog id="add_modal" class="modal">
        <form method="POST" action="{{ route('admin.ticket-types.store') }}" class="modal-box">
            @csrf
            <h3 class="text-lg font-bold mb-4">Tambah Tipe Tiket</h3>
            <div class="form-control w-full mb-4">
                <label class="label mb-2">
                    <span class="label-text">Nama Tipe Tiket</span>
                </label>
                <input type="text" placeholder="Masukkan nama tipe tiket" class="input input-bordered w-full" name="nama" required />
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <button class="btn" onclick="add_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Modal Edit Tipe Tiket -->
    <dialog id="edit_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('PUT')

            <input type="hidden" name="ticket_type_id" id="edit_ticket_type_id">

            <h3 class="text-lg font-bold mb-4">Edit Tipe Tiket</h3>
            <div class="form-control w-full mb-4">
                <label class="label mb-2">
                    <span class="label-text">Nama Tipe Tiket</span>
                </label>
                <input type="text" placeholder="Masukkan nama tipe tiket" class="input input-bordered w-full" id="edit_ticket_type_name" name="nama" />
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <button class="btn" onclick="edit_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Modal Hapus Tipe Tiket -->
    <dialog id="delete_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('DELETE')

            <input type="hidden" name="ticket_type_id" id="delete_ticket_type_id">

            <h3 class="text-lg font-bold mb-4">Hapus Tipe Tiket</h3>
            <p>Apakah Anda yakin ingin menghapus tipe tiket ini?</p>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Hapus</button>
                <button class="btn" onclick="delete_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <script>
        function openEditModal(button) {
            const name = button.dataset.nama;
            const id = button.dataset.id;
            const form = document.querySelector('#edit_modal form');

            document.getElementById('edit_ticket_type_name').value = name;
            document.getElementById('edit_ticket_type_id').value = id;

            form.action = `/admin/ticket-types/${id}`;

            edit_modal.showModal();
        }

        function openDeleteModal(button) {
            const id = button.dataset.id;
            const form = document.querySelector('#delete_modal form');
            document.getElementById('delete_ticket_type_id').value = id;

            form.action = `/admin/ticket-types/${id}`;

            delete_modal.showModal();
        }
    </script>
</x-layouts.admin>
