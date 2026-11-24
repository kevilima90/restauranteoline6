document.addEventListener('DOMContentLoaded', () => {
    // Confirmación previa para acciones que modifican datos.
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm || '¿Desea continuar?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // Vista previa del subtotal en el formulario de pedidos.
    const dishSelect = document.getElementById('dish_id');
    const quantityInput = document.getElementById('quantity');
    const preview = document.getElementById('orderPreview');

    const updatePreview = () => {
        if (!dishSelect || !quantityInput || !preview) return;

        const selected = dishSelect.options[dishSelect.selectedIndex];
        const price = Number(selected?.dataset.price || 0);
        const quantity = Math.max(1, Number(quantityInput.value || 1));

        if (!price) {
            preview.textContent = 'Seleccione un plato para visualizar el subtotal.';
            return;
        }

        preview.textContent = `Subtotal estimado: $${(price * quantity).toFixed(2)}`;
    };

    dishSelect?.addEventListener('change', updatePreview);
    quantityInput?.addEventListener('input', updatePreview);
    updatePreview();

    // Validación visual de formularios HTML5.
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
            }
        });
    });
});
