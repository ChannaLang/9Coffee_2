document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.getElementById('btnAddMaterial');
    if (!addBtn) return;

    addBtn.addEventListener('click', () => {
        Swal.fire({
            title: 'Add Raw Material',
            html: `
                <input type="text" id="rm_name" class="swal2-input" placeholder="Material Name">
                <input type="number" id="rm_qty" class="swal2-input" placeholder="Quantity" min="0">
                <select id="rm_unit" class="swal2-input">
                    <option value="g">Gram (g)</option>
                    <option value="kg">Kilogram (kg)</option>
                    <option value="ml">Milliliter (ml)</option>
                    <option value="l">Liter (L)</option>
                    <option value="pcs">Pieces (pcs)</option>
                </select>
            `,
            confirmButtonText: 'Save',
            showCancelButton: true,
            preConfirm: () => {
                const name = document.getElementById('rm_name').value;
                let qty = parseFloat(document.getElementById('rm_qty').value);
                let unit = document.getElementById('rm_unit').value;

                if (!name || !qty) {
                    Swal.showValidationMessage('Please fill all fields');
                    return false;
                }

                // Convert KG → g / L → ml
                if (unit === 'kg') { qty *= 1000; unit = 'g'; }
                if (unit === 'l')  { qty *= 1000; unit = 'ml'; }

                // Create temporary form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = addBtn.dataset.url; // gets the correct route
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                    <input type="hidden" name="name" value="${name}">
                    <input type="hidden" name="quantity" value="${qty}">
                    <input type="hidden" name="unit" value="${unit}">
                `;
                document.body.appendChild(form);
                form.submit(); // ← Laravel handles standard validation/errors
            }
        });
    });
});
