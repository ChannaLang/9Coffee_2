document.addEventListener('DOMContentLoaded', function() {
    let cart = {};

        // Toast helper
    function showToast(message, icon='success'){
        Swal.fire({
            position: 'center',
            icon,
            title: message,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    }


    // Select size/sugar
    document.querySelectorAll('.size-buttons, .sugar-buttons').forEach(group=>{
        group.addEventListener('click', e=>{
            if(e.target.classList.contains('size-btn') || e.target.classList.contains('sugar-btn')){
                group.querySelectorAll('button').forEach(btn=>btn.classList.remove('active'));
                e.target.classList.add('active');
            }
        });
    });

    // Add to cart
    document.querySelectorAll('.btn-add-to-cart').forEach(btn=>{
        btn.addEventListener('click', function(){
            const card = this.closest('.product-card');
            const id = card.dataset.id;
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);

            const sizeBtn = card.querySelector('.size-btn.active');
            const sugarBtn = card.querySelector('.sugar-btn.active');
            if(!sizeBtn || !sugarBtn){
                showToast('Please select size and sugar level!', 'error');
                return;
            }

            const size = sizeBtn.dataset.size;
            const sugar = sugarBtn.dataset.sugar;
            const key = `${id}_${size}_${sugar}`;

            if(cart[key]) cart[key].quantity++;
            else cart[key] = {id, name, size, sugar, price, quantity:1};

            renderCart();
            showToast(`${name} (${size}, ${sugar}%) added!`);
        });
    });

    // Remove from cart
    document.querySelectorAll('.btn-remove-from-cart').forEach(btn=>{
        btn.addEventListener('click', function(){
            const card = this.closest('.product-card');
            const id = card.dataset.id;
            const size = card.querySelector('.size-btn.active').dataset.size;
            const sugar = card.querySelector('.sugar-btn.active').dataset.sugar;
            const key = `${id}_${size}_${sugar}`;

            if(cart[key]){
                cart[key].quantity--;
                if(cart[key].quantity<=0) delete cart[key];
            }

            renderCart();
            showToast('Item removed', 'info');
        });
    });

    // Render cart
    function renderCart(){
        const tbody = document.querySelector('#cart-table tbody');
        tbody.innerHTML = '';
        Object.values(cart).forEach(item=>{
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>${item.size}</td>
                <td>${item.sugar}%</td>
                <td>${item.quantity}</td>
                <td>$${(item.price*item.quantity).toFixed(2)}</td>`;
            tbody.appendChild(row);
        });
    }

    // Checkout button
document.querySelector('#checkout').addEventListener('click', function(e){
    e.preventDefault();
    if(Object.keys(cart).length===0){
        showToast('Cart is empty!', 'error');
        return;
    }

    const csrfToken = document.querySelector('input[name="_token"]').value;
    const formData = new FormData();
    formData.append('cart_data', JSON.stringify(cart));
    formData.append('payment_method', 'cash'); // focus only on cash
    formData.append('_token', csrfToken);

    fetch(document.querySelector('#checkout-form').action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            // ✅ Backend confirmed, stock deducted
            showToast(data.message, 'success');

            // ✅ Show invoice
            showInvoice(cart, 'cash');

            cart = {};
            renderCart();
        } else {
            showToast(data.message || 'Error occurred', 'error');
        }
    })
    .catch(err=>{
        console.error(err);
        showToast('Server error!', 'error');
    });
});

        // --- Invoice Popup ---
    function showInvoice(cartData, paymentMethod){
        let invoiceHTML = `
            <div id="invoice" style="text-align:left;font-family:Arial, sans-serif;">
                <h3 style="text-align:center;">☕ Coffee POS Invoice</h3>
                <p style="text-align:center;font-size:13px;">Payment Method: <b>${paymentMethod.toUpperCase()}</b></p>
                <hr>
                <table style="width:100%;font-size:14px;">
                    <thead>
                        <tr>
                            <th align="left">Item</th>
                            <th>Size</th>
                            <th>Sugar</th>
                            <th>Qty</th>
                            <th align="right">Total</th>
                        </tr>
                    </thead>
                    <tbody>`;

        let total = 0;
        Object.values(cartData).forEach(item=>{
            const lineTotal = item.price * item.quantity;
            total += lineTotal;
            invoiceHTML += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.size}</td>
                    <td>${item.sugar}%</td>
                    <td>${item.quantity}</td>
                    <td align="right">$${lineTotal.toFixed(2)}</td>
                </tr>`;
        });

        invoiceHTML += `
                    </tbody>
                </table>
                <hr>
                <p style="text-align:right;font-weight:bold;">Total: $${total.toFixed(2)}</p>
                <p style="text-align:center;font-size:12px;">Thank you for your purchase! ☕</p>
                <p style="text-align:center;font-size:11px;color:gray;">Printed on ${new Date().toLocaleString()}</p>
            </div>`;

        Swal.fire({
            title: 'Payment Successful!',
            html: invoiceHTML,
            icon: 'success',
            width: 600,
            confirmButtonText: 'Print Invoice',
            didOpen: () => {
                const popup = Swal.getPopup();
                popup.style.textAlign = 'left';
            }
        }).then(result=>{
            if(result.isConfirmed){
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html><head><title>Invoice</title></head><body>
                    ${invoiceHTML}
                    <script>window.onload = function(){ window.print(); }</script>
                    </body></html>
                `);
                printWindow.document.close();
            }
        });
    }

});
