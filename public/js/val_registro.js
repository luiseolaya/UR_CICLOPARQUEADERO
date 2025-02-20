    const form = document.getElementById("registro-form");

    form.addEventListener("submit", (event) => {
        event.preventDefault();  // Detener el envío del formulario por defecto

        let errorMessage = "";

        const nombre = document.getElementById("nombre").value.trim();
        const apellido = document.getElementById("apellido").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const celular = document.getElementById("celular").value.trim();
        const clave = document.getElementById("clave").value.trim();
        const conf_clave = document.getElementById("conf_clave").value.trim();
        const autorizo = document.getElementById("autorizo").checked;

        if (!nombre || !apellido || !correo || !celular || !clave) {
            errorMessage = "No puede haber campos vacíos.";
        } else if (!autorizo) {
            errorMessage = "Debes aceptar los términos y condiciones.";
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
            errorMessage = "Formato de correo no válido.";
        } else if (clave !== conf_clave) {
            errorMessage = "Las contraseñas no coinciden.";
        } else if (/\d/.test(nombre)) {  
            errorMessage = "El nombre no puede tener números.";
        } else if (/\d/.test(apellido)) {  
            errorMessage = "El apellido no puede tener números.";
        }

        if (errorMessage) {
            Swal.fire({
                title: '¡Error!',
                text: errorMessage,
                icon: 'error',
                timer: 3000,
                timerProgressBar: true
            });
            return false; // Detener el envío del formulario
        }

        form.submit();  // Si todo está bien, enviamos el formulario
    });
