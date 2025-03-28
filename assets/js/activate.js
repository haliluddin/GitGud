document.addEventListener("DOMContentLoaded", () => {
    const table = document.querySelector("table");
    let currentRow = null; // Variable to track the current row being acted upon

    // Deactivate User
    table.addEventListener("click", (event) => {
        if (event.target.closest("[data-bs-target='#deactivateuser']")) {
            currentRow = event.target.closest("tr"); // Save the current row
        }
    });

    const deactivateModal = document.getElementById("deactivateuser");
    deactivateModal.querySelector(".btn-primary").addEventListener("click", () => {
        if (currentRow) {
            const statusCell = currentRow.querySelector("td:nth-child(7)");
            const actionCell = currentRow.querySelector("td:nth-child(10)");

            // Determine deactivation duration
            const duration = deactivateModal.querySelector("input[name='flexRadioDefault']:checked");
            if (duration) {
                const deactivationText = `Deactivated for ${duration.nextElementSibling.textContent}`;
                statusCell.textContent = deactivationText;

                // Update Action to Activate
                actionCell.innerHTML = `
                    <div class="dropdown position-relative">
                        <i class="fa-solid fa-ellipsis small rename py-1 px-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>
                        <ul class="dropdown-menu dropdown-menu-center p-0" style="box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#edituser">Edit</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteuser">Delete</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#activateuser">Activate</a></li>
                            <li><a class="dropdown-item" href="#">Activity</a></li>
                        </ul>
                    </div>`;
            }
            // Close the modal
            bootstrap.Modal.getInstance(deactivateModal).hide();
        }
    });

    // Activate User
    const activateModal = document.getElementById("activateuser");
    table.addEventListener("click", (event) => {
        if (event.target.closest("[data-bs-target='#activateuser']")) {
            currentRow = event.target.closest("tr"); // Save the current row
        }
    });

    activateModal.querySelector(".btn-primary").addEventListener("click", () => {
        if (currentRow) {
            const userId = currentRow.querySelector("[data-user-id]").getAttribute("data-user-id");

            fetch("classes/activate.user.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=activateUser&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'User Activated',
                        text: 'The user has been activated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to activate user.',
                        icon: 'failed',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                }
            })
            .catch(error => console.error("Error:", error));
        }
    });
});