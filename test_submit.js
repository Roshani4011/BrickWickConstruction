document.addEventListener('DOMContentLoaded', function() {
    console.log("Script loaded");
    
    var form = document.getElementById('cost-estimator-form');
    if (!form) {
        console.error("Form not found!");
        return;
    }
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var xhr = new XMLHttpRequest();
        
        // Explicitly use absolute URL to avoid any path manipulation
        xhr.open('POST', 'http://localhost/interior/store_estimation.php');
        
        xhr.onload = function() {
            console.log("Response received:", xhr.responseText);
            alert("Response: " + xhr.responseText);
        };
        
        xhr.onerror = function(error) {
            console.error("XHR Error:", error);
            alert("Error: " + error);
        };
        
        var formData = new FormData(form);
        xhr.send(formData);
        
        return false;
    });
});