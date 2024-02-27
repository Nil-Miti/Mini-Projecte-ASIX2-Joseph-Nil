// Funciones para mostrar/ocultar la caja de creacion de cuenta
function openCreateAccountModal() {
    document.getElementById("create-account-modal").classList.add("show-modal");
}

function closeCreateAccountModal() {
    document.getElementById("create-account-modal").classList.remove("show-modal");
}

// Función para manejar la eliminación del texto predeterminado en los campos de entrada
document.addEventListener("DOMContentLoaded", function() {
    const inputFields = document.querySelectorAll("input");
    inputFields.forEach(function(input) {
        input.addEventListener("focus", function() {
            this.placeholder = "";
        });
        input.addEventListener("blur", function() {
            if (this.value === "") {
                this.placeholder = "Ingrese su " + this.id;
            }
        });
    });
});

// Función para manejar la creación de la cuenta (aquí puedes agregar lógica adicional según tus necesidades)
function createAccount() {
    alert("Cuenta creada con éxito. ¡Puedes personalizar esta función según tus necesidades!");
}

//Funcion para mostrar/ocultar la caja de iniciar session
function AbrirCajaIniciarSesion() {
    document.getElementById("login-modal").classList.add("show-modal");
}

function closeCreateAccountModal() {
    document.getElementById("login-modal").classList.remove("show-modal");
}
