<?php
include_once "resource/view/header.php";
include_once "resource/view/nav.php";
include_once "functions.php";
$employees = getEmployees();
?>
<div class="row">
    <div class="col-12">
        <h1 class="text-center">Employees</h1>
    </div>
    <div class="col-12">
        <a href="employee_add.php" class="btn btn-info mb-2">Add new employee <i class="fa fa-plus"></i></a>
    </div>
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee) { ?>
                        <tr>
                            <td>
                                <?php echo $employee->id ?>
                            </td>
                            <td>
                                <?php echo $employee->name ?>
                            </td>
                            <td>
                                <a class="btn btn-warning" href="employee_edit.php?id=<?php echo $employee->id ?>">
                                Edit <i class="fa fa-edit"></i>
                            </a>
                            </td>
                            <td>
                                <a class="btn btn-danger" href="employee_delete.php?id=<?php echo $employee->id ?>">
                                Delete <i class="fa fa-trash"></i>
                            </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!DOCTYPE html>
<html>
<head>
    <title>Control de LED</title>
</head>
<body>
    <h1>Control de LED</h1>
    
    <button id="turnOnButton">Encender LED</button>
    <button id="turnOffButton">Apagar LED</button>
    
    <script>
        document.getElementById("turnOnButton").addEventListener("click", function() {
            controlLED("turn_on_led");
        });

        document.getElementById("turnOffButton").addEventListener("click", function() {
            controlLED("turn_off_led");
        });

        function controlLED(action) {
            fetch("/control_led.php", {  // Cambia la URL a "/control_led.php"
                method: "POST",
                body: JSON.stringify({ action }),
                headers: {
                    "Content-Type": "application/json"
                }
            })
            .then(response => {
                // Verifica si la respuesta tiene contenido antes de intentar analizarla como JSON
                if (response.status !== 204) {
                    return response.json();
                }
                return { message: "Operación exitosa" };
            })
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                console.error("Error:", error);
            });
        }
    </script>
</body>
</html>

<?php
include_once "resource/view/footer.php";
?>
