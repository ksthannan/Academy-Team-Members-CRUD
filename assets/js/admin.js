/**
 * 
 * Confirm before delete 
 */
function confirmDelete(e) {
    // Display a confirmation dialog
    let confirmDelete = confirm("Are you sure you want to delete?");

    // If the user confirms, proceed with the deletion
    if (confirmDelete) {
        window.location.href = e.target.href;
    }

    // Prevent the default action (e.g., following the link)
    return false;
}