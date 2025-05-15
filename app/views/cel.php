<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/sessionController.php';

$error = $_SESSION['error'] ?? '';
$mensaje = $_SESSION['mensaje'] ?? '';
unset($_SESSION['error'], $_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Teléfono</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/cicloparqueaderos/public/css/inicio_style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="/cicloparqueaderos/public/img/icon_U.png" rel="icon" type="image/x-icon" />
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="/cicloparqueaderos/public/img/LOGOU.png" alt="Logo">
            <div class="title">Cicloparqueadero</div>
            <div class="subtitle">Antes de continuar por favor actualiza tu número telefónico y acepta términos y condiciones</div>
        </div>
        <form action="/cicloparqueaderos/app/controllers/UsuarioController.php" method="POST">
            <div class="form-floating mb-3">
                <input type="tel" class="form-control" id="floatingInput" name="celular" placeholder="Celular" required pattern="[0-9]{10}">
                <label for="floatingInput">Celular</label>
                <input type="checkbox" name="terminos" value="1" id="check_terminos" required/>
                 <a href="#" id="ver_terminos">Aceptar términos y condiciones</a>
             </div>
            <button type="submit" name="actualizar_telefono" class="btn btn-primary w-100">Aceptar</button>
        </form>
    </div>

<script>
document.getElementById('ver_terminos').addEventListener('click', function(e) {
    e.preventDefault();

    Swal.fire({
        title: '<img src="/cicloparqueaderos/public/img/LOGOU.png" alt="Logo" style="height:40px;"> <br> Términos y Condiciones',
        html: `<div id="swal-tyc-content" style="height:300px;overflow:auto;text-align:left;">
        Términos de Referencia: Aplicación de Movilidad Sostenible
1. Antecedentes y Justificación
La Universidad del Rosario, en su compromiso con la sostenibilidad y la promoción de la salud física y mental, implementa la Aplicación de Movilidad Sostenible como herramienta de registro y seguimiento de los desplazamientos en bicicleta y scooter por parte de estudiantes, funcionarios y otros miembros de la comunidad universitaria.
2. Objetivo General
Diseñar y operar una aplicación móvil que promueva la movilidad sostenible al:
•	Facilitar el registro de ingresos de bicicletas y scooters.
•	Recopilar datos de modos de viaje y uso de bicicleta para otorgar incentivos al finalizar el año académico.
•	Fomentar hábitos de transporte activos y saludables.
3. Alcance
•	Usuarios: estudiantes y funcionarios.
•	Dispositivos: dispositivos móviles compatibles con GPS.
•	Registros: entradas y salidas de bicicletas y scooters en cicloparqueaderos.
•	Datos: ubicación GPS, hora de ingreso y modo de transporte.
4. Funcionalidades Principales
1.	Registro de ingreso mediante escaneo de QR y/o validación GPS.
2.	Monitoreo en tiempo real (opcional) para verificar congruencia de la ubicación.
3.	Panel de incentivos: acumulación de puntos por uso de bicicleta o scooter.
4.	Alertas: recordatorios de uso de casco y revisión previa del vehículo.
5.	Informe anual: reporte de hábitos de movilidad y recompensas para responsables del sistema.
5. Responsabilidades
•	Universidad del Rosario:
o	Proveer la infraestructura y soporte técnico.
o	Garantizar la confidencialidad y seguridad de los datos.
o	Administrar el programa de incentivos.
o	Cumplir con la política de Empresa Familiarmente Responsable (EFR).
•	Usuario:
o	Permitir el acceso a GPS y datos básicos del dispositivo.
o	Registrar correctamente cada ingreso.
o	Usar casco y elementos de seguridad obligatorios.
o	Realizar la revisión previa de la bicicleta (frenos, luces, llantas).
•	Vigilante:
o	Verificar en la aplicación la correcta validación del registro.
o	Reportar anomalías o usos indebidos.
6. Condiciones de Uso y Exenciones de Responsabilidad
1.	Descargo de responsabilidad extracontractual:
o	El usuario reconoce que la Universidad del Rosario no se hace responsable por daños, pérdidas o lesiones derivadas del uso de la bicicleta o scooter.
o	El uso de la aplicación y la participación en el programa es voluntario y no genera relación contractual ni académica.
2.	Limitación de vínculo:
o	El registro en la aplicación no sustituye licencias académicas ni justificaciones oficiales.
3.	Protección de datos:
o	La App solicitará permisos GPS sólo para fines de validación de ingresos.
o	La información se almacenará de acuerdo con la política de privacidad de la Universidad.
7. Compromiso EFR (Empresa Familiarmente Responsable)
•	La Universidad del Rosario reafirma su compromiso con el bienestar físico y mental de la comunidad:
o	Programas de salud preventiva.
o	Jornadas de formación en movilidad segura.
o	Apoyo psicológico y asesoría en hábitos saludables.
8. Seguridad y Salud
•	Uso obligatorio de casco.
•	Elementos reflectantes (cintas, chalecos) para circulación en horas de baja visibilidad.
•	Inspección diaria de la bicicleta: frenos, sistema de dirección, estado de las llantas
9. Incentivos y Reconocimientos
•	Puntos acumulables por cada registro válido.
•	Recompensas: descuentos en cafeterías, obsequios sostenibles, certificados de reconocimiento a cargo y libre decisión de los responsables
10. Autorización de tratamientos 
En aplicación de la Ley Estatutaria de Colombia 1581 de 2012 y demás normas que las modifiquen, regulen o adicionen, autorizo a LA UNIVERSIDAD DEL ROSARIO, institución privada, de educación superior, con el carácter académico de Universidad, para el tratamiento de información relacionada con mis datos personales que se encuentran asociados al pasaporte virtual, la geolocalización y el uso de la cámara durante uso   de la aplicación MOVILIDAD SOSTENIBLE Y SEGURA base a las siguientes finalidades: promover la movilidad sostenible y segura de la comunidad Rosarista  , única y exclusivamente para realizar el registro de ingreso al cicloparqueadero, trazabilidad de ubicación del vehículo cuando se encuentre en las instalaciones del mismo, identificación del vehículo que ingresa mediante el uso de la cámara del móvil, validación del ingreso y entrega de incentivos por uso de la bicicleta, todo con el fin de habilitar la generación automática de indicadores de uso de los cicloparqueaderos y la entrega de reconocimientos.. Legitimar el uso de la aplicación a través de la geolocalización, exclusivamente para identificar la cercanía al cicloparqueadero, y del uso de la cámara únicamente para registrar visualmente el vehículo y validar su ingreso. Todo esto con el fin de habilitar la generación automatizada de indicadores de uso y facilitar la entrega de reconocimientos .

Manifiesto bajo la gravedad de juramento que soy mayor de dieciocho (18) años y cuento con la capacidad necesaria para dar mi autorización al tratamiento de mis datos personales.  
Reconozco y acepto que la valoración de mi comportamiento dentro de la aplicación será con fines exclusivamente laborales y la información allí capturada será atendida conforme al principio de confidencialidad y protección de datos personales de la legislación colombiana.
En caso de que la custodia y almacenamiento sea realizado por una entidad con la que tenga relación contractual LA UNIVERSIDAD DEL ROSARIO, autorizo la transmisión de mis datos personales a un tercer país, que cuenta con los estándares de seguridad en la protección de datos personales fijados por la Superintendencia de Industria y Comercio.
Me queda entendido que el tratamiento se limitará a los fines previamente establecidos y a los señalados en la política de tratamiento de protección de datos personales de LA UNIVERSIDAD y que se guardará la debida reserva, mediante la adopción de las medidas físicas, técnicas y administrativas adecuadas y suficientes que permitan el cuidado y conservación de mis datos personales y evitar la pérdida, la adulteración, el uso fraudulento o no adecuado de mis datos personales. Declaro que tengo conocimiento de mi derecho a conocer, actualizar, incluir y rectificar sus datos personales, también podrá solicitar la supresión o revocar la autorización otorgada para su tratamiento. En caso de un reclamo o consulta relativa a sus datos personales, puede realizarla remitiendo la solicitud al correo electrónico urpheel@urosario.edu.co.
Si desea mayor información sobre el tratamiento de sus datos personales, consulte nuestra Política de Tratamiento de Datos personales en www.urosario.edu.co  
        </div>`,
        showCancelButton: false,
        showConfirmButton: true,
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#3085d6',
        allowOutsideClick: false,
        didOpen: () => {
            const content = document.getElementById('swal-tyc-content');
            const btn = Swal.getConfirmButton();
            btn.disabled = true;
            content.addEventListener('scroll', function() {
                if (content.scrollTop + content.clientHeight >= content.scrollHeight - 5) {
                    btn.disabled = false;
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('check_terminos').checked = true;
        }
    });
});
</script>


</body>

</html>