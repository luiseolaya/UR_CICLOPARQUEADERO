document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM cargado para validación de inicio de sesión");

    const form = document.getElementById("inicio-form");

    if (form) {
        form.addEventListener("submit", (event) => {
            event.preventDefault();  

            let errorMessage = "";

            const correo = document.getElementById("floatingInput").value.trim();
            const clave = document.getElementById("floatingPassword").value.trim();

            if (!correo || !clave) {
                errorMessage = "No puede haber campos vacíos.";
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
                errorMessage = "Formato de correo no válido.";
            }

            if (errorMessage) {
                Swal.fire({
                    title: '¡Error!',
                    text: errorMessage,
                    icon: 'error',
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                form.submit(); // Si pasa la validación, enviar el formulario
            }
        });
    } else {
        console.error("Formulario no encontrado");
    }
});
