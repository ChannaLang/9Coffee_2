document.addEventListener('DOMContentLoaded', function() {
    let cart = {};

        // Toast helper
    function showToast(message, icon='success'){
        Swal.fire({
            toast: icon === 'success',       // only success uses small toast style
            position: 'top-end',
            icon: icon,
            title: message,
            showConfirmButton: icon !== 'success',  // errors need button
            timer: icon === 'success' ? 1500 : undefined, // only auto-close success
            timerProgressBar: icon === 'success' ? true : false
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

        let total = Object.values(cart).reduce((sum, item)=> sum + item.price*item.quantity, 0);

        Swal.fire({
            title:'Confirm Checkout',
            html:`<p>Total: <strong>$${total.toFixed(2)}</strong></p>`,
            icon:'question',
            showCancelButton:true,
            confirmButtonText:'Confirm'
        }).then(result=>{
            if(result.isConfirmed){
                const csrfToken = document.querySelector('input[name="_token"]').value;
                const formData = new FormData();
                formData.append('cart_data', JSON.stringify(cart));
                formData.append('payment_method', document.querySelector('#payment_method').value);
                formData.append('_token', csrfToken);

                fetch(document.querySelector('#checkout-form').action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async res => {
                    const text = await res.text();
                    try {
                        const data = JSON.parse(text);
                        if(data.success){
                            showToast(data.message, 'success');
                            cart = {};
                            renderCart();
                        } else {
                            showToast(data.message || 'Error occurred', 'error');
                        }
                    } catch(e) {
                        console.error('Server Response:', text);
                        showToast('Server error!', 'error');
                    }
                })
                .catch(err=>{
                    console.error('Checkout failed:', err);
                    showToast('Server error!', 'error');
                });
            }
        });
    });
});
