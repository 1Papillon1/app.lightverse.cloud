{{-- Bootstrap modal za potvrdu brisanja, koristi se na files/index i admin/files --}}
{{-- Gumb za brisanje u tablici šalje data-action (URL za brisanje) i data-name (ime datoteke) --}}
<div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="delete-modal-title">Delete file</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="delete-modal-name"></strong>? This cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="delete-modal-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Prije otvaranja modala upiši URL i ime datoteke iz kliknutog gumba
    document.getElementById('delete-modal').addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        document.getElementById('delete-modal-form').action = button.dataset.action;
        document.getElementById('delete-modal-name').textContent = button.dataset.name;
    });
</script>
